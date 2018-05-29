<?php
/**
 * Basado en DjJob
 * https://github.com/seatgeek/djjob/blob/master/DJJob.php
 *
 * Se adapto para integrarlo, se mejoro la implementacion y se agregaron funcionalidades
 * @author  Martin A. Santangelo <msantangelo@smartsoftware.com.ar>
 */

require_once __DIR__.'/Base.php';

class Rad_Jobs_Worker extends Rad_Jobs_Base {
    static public $currentJob;

    # This is a singleton-ish thing. It wouldn't really make sense to
    # instantiate more than one in a single request (or commandline task)
    public function __construct($options = array()) {
        $options = array_merge(array(
            "queue"        => "default",
            "count"        => 0,
            "sleep"        => 5,
            "max_attempts" => 5
        ), $options);

        list($this->queue, $this->count, $this->sleep, $this->max_attempts) =
            array($options["queue"], $options["count"], $options["sleep"], $options["max_attempts"]);

        list($hostname, $pid) = array(trim(`hostname`), getmypid());
        $this->name = "host::$hostname pid::$pid";

        if (function_exists("pcntl_signal")) {
            pcntl_signal(SIGTERM, array($this, "handleSignal"));
            pcntl_signal(SIGINT,  array($this, "handleSignal"));
        }

                // desactivo el manejador de errores fatales para q use solo el del worker
        Rad_ErrorHandler::$ERROR_HANDLER = array(__CLASS__, 'shutdowncheck');
    }

    public static function shutdowncheck($e)
    {
        self::$currentJob->finishWithError($e['message']);
    }

    public function handleSignal($signo) {
        $signals = array(
            SIGTERM => "SIGTERM",
            SIGINT  => "SIGINT"
        );
        $signal = $signals[$signo];

        $this->log("[WORKER] Received received {$signal}... Shutting down", self::INFO);
        $this->releaseLocks();
        die(0);
    }

    public function releaseLocks() {
        $this->runUpdate("
            UPDATE Jobs
            SET locked_at = NULL, locked_by = NULL
            WHERE locked_by = ?",
            array($this->name)
        );
    }

    /**
     * Returns a new job ordered by most recent first
     * why this?
     *     run newest first, some Jobs get left behind
     *     run oldest first, all Jobs get left behind
     * @return Rad_Jobs_Job
     */
    public function getNewJob() {
        # we can grab a locked job if we own the lock
        $rs = $this->runQuery("
            SELECT id, user
            FROM   Jobs
            WHERE  queue = ?
            AND    (run_at IS NULL OR NOW() >= run_at)
            AND    (locked_at IS NULL OR locked_by = ?)
            AND    failed_at IS NULL
            AND    attempts < ?
            ORDER BY created_at DESC
            LIMIT  10
        ", array($this->queue, $this->name, $this->max_attempts));

        // randomly order the 10 to prevent lock contention among workers
        shuffle($rs);

        foreach ($rs as $r) {
            $job = new Rad_Jobs_Job($this->name, $r["id"], array(
                "max_attempts" => $this->max_attempts,
                "user"         => $r["user"]
            ));
            if ($job->acquireLock()) return $job;
        }

        return false;
    }

    public function start() {
        $this->log("[JOB] Starting worker {$this->name} on queue::{$this->queue}", self::INFO);

        $count = 0;
        $job_count = 0;

        try {
            while ($this->count == 0 || $count < $this->count) {
                if (function_exists("pcntl_signal_dispatch")) pcntl_signal_dispatch();

                $count += 1;
                $job = $this->getNewJob($this->queue);

                if (!$job) {
                    $this->log("[JOB] Failed to get a job, queue::{$this->queue} may be empty", self::DEBUG);
                    // si no hay nada y ya termine por que esperar?
                    if ($this->count != 0 && $count >= $this->count) break;

                    sleep($this->sleep);
                    continue;
                }
                self::$currentJob = $job;
                $job_count += 1;
                $job->run();
            }
        } catch (Exception $e) {
            $this->log("[JOB] unhandled exception::\"{$e->getMessage()}\"", self::ERROR);
        }

        $this->log("[JOB] worker shutting down after running {$job_count} Jobs, over {$count} polling iterations", self::INFO);
    }
}
