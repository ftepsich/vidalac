<?php
require_once __DIR__.'/Base.php';

/**
 * Basado en DjJob
 * https://github.com/seatgeek/djjob/blob/master/DJJob.php
 *
 * Se adapto para integrarlo, se mejoro la implementacion y se agregaron funcionalidades
 * @author  Martin A. Santangelo <msantangelo@smartsoftware.com.ar>
 */


class Rad_Jobs_Job extends Rad_Jobs_Base
{
    protected static $_preRun;
    public $user;

    public function __construct($worker_name, $job_id, $options = array()) {
        $options = array_merge(array(
            "max_attempts" => 5
        ), $options);
        $this->worker_name  = $worker_name;
        $this->job_id       = $job_id;
        $this->max_attempts = $options["max_attempts"];
        $this->user         = $options["user"];
    }

    public static function setPreRun($callback)
    {
        self::$_preRun = $callback;
    }

    public function run() {
        # pull the handler from the db
        $handler = $this->getHandler();
        if (!is_object($handler)) {
            $this->log("[JOB] bad handler for job::{$this->job_id}", self::ERROR);
            $this->finishWithError("bad handler for job::{$this->job_id}");
            return false;
        }

        // ejecutar callback de estar seteado
        $cback = self::$_preRun;
        if (is_callable($cback)) {
            //para cuando tengamos php 5.4
            //$cback->bindTo($this);
            $cback($this);
        }

        # run the handler
        try {
            $handler->perform();

            # cleanup
            $this->finish();
            return true;

        } catch (DJRetryException $e) {
            # attempts hasn't been incremented yet.
            $attempts = $this->getAttempts()+1;

            $msg = "\"{$e->getMessage()}\" en intento $attempts/{$this->max_attempts}.";

            if($attempts >= $this->max_attempts) {
                $this->log("[JOB] job::{$this->job_id} $msg Giving up.");
                $this->finishWithError($msg, $handler);
            } else {
                $this->log("[JOB] job::{$this->job_id} $msg Try again in {$e->getDelay()} seconds.", self::WARN);
                $this->retryLater($e->getDelay());
            }
            return false;

        } catch (Exception $e) {
            $this->finishWithError($e->getMessage(), $handler);
            $this->log("[JOB] ERROR job::{$this->job_id} ".$e->getMessage()." in ".$e->getFile()." line ".$e->getLine(), self::CRITICAL);
            $this->log(print_r($e->getTrace(),true), self::DEBUG);
            return false;
        }
    }

    public function acquireLock() {
        $this->log("[JOB] attempting to acquire lock for job::{$this->job_id} on {$this->worker_name}", self::INFO);

        $lock = $this->runUpdate("
            UPDATE Jobs
            SET    locked_at = NOW(), locked_by = ?
            WHERE  id = ? AND (locked_at IS NULL OR locked_by = ?) AND failed_at IS NULL
        ", array($this->worker_name, $this->job_id, $this->worker_name));

        if (!$lock) {
            $this->log("[JOB] failed to acquire lock for job::{$this->job_id}", self::INFO);
            return false;
        }

        return true;
    }

    public function releaseLock() {
        $this->runUpdate("
            UPDATE Jobs
            SET locked_at = NULL, locked_by = NULL
            WHERE id = ?",
            array($this->job_id)
        );
    }

    public function finish() {
        $this->runUpdate(
            "DELETE FROM Jobs WHERE id = ?",
            array($this->job_id)
        );
        $this->log("[JOB] completed job::{$this->job_id}", self::INFO);
    }

    public function finishWithError($error, $handler = null) {
        $this->runUpdate("
            UPDATE Jobs
            SET attempts = attempts + 1,
                failed_at = IF(attempts >= ?, NOW(), NULL),
                error = IF(attempts >= ?, ?, NULL)
            WHERE id = ?",
            array(
                $this->max_attempts,
                $this->max_attempts,
                self::getConnection()->quote($error),
                $this->job_id
            )
        );
        $this->log("[JOB] failure in job::{$this->job_id}", self::ERROR);
        $this->releaseLock();

        if ($handler && ($this->getAttempts() == $this->max_attempts) && method_exists($handler, '_onJobRetryError')) {
          $handler->_onJobRetryError($error);
        }
    }

    public function retryLater($delay) {
        $this->runUpdate("
            UPDATE Jobs
            SET run_at = DATE_ADD(NOW(), INTERVAL ? SECOND),
                attempts = attempts + 1
            WHERE id = ?",
            array(
              $delay,
              $this->job_id
            )
        );
        $this->releaseLock();
    }

    public function getHandler() {
        $rs = $this->runQuery(
            "SELECT handler FROM Jobs WHERE id = ?",
            array($this->job_id)
        );
        foreach ($rs as $r) return unserialize($r["handler"]);
        return false;
    }

    public function getAttempts() {
        $rs = $this->runQuery(
            "SELECT attempts FROM Jobs WHERE id = ?",
            array($this->job_id)
        );
        foreach ($rs as $r) return $r["attempts"];
        return false;
    }

    public static function enqueue($handler, $queue = "default", $run_at = null) {
        $affected = self::runUpdate(
            "INSERT INTO Jobs (handler, queue, run_at, created_at, user) VALUES(?, ?, ?, NOW(),?)",
            array(serialize($handler), (string) $queue, $run_at, self::$options['user'])
        );

        if ($affected < 1) {
            self::log("[JOB] failed to enqueue new job", self::ERROR);
            return false;
        }

        return self::getConnection()->lastInsertId(); // return the job ID, for manipulation later
    }

    public static function bulkEnqueue($handlers, $queue = "default", $run_at = null) {
        $sql = "INSERT INTO Jobs (handler, queue, run_at, created_at) VALUES";
        $sql .= implode(",", array_fill(0, count($handlers), "(?, ?, ?, NOW())"));

        $parameters = array();
        foreach ($handlers as $handler) {
            $parameters []= serialize($handler);
            $parameters []= (string) $queue;
            $parameters []= $run_at;
        }
        $affected = self::runUpdate($sql, $parameters);

        if ($affected < 1) {
            self::log("[JOB] failed to enqueue new Jobs", self::ERROR);
            return false;
        }

        if ($affected != count($handlers))
            self::log("[JOB] failed to enqueue some new Jobs", self::ERROR);

        return true;
    }

    public static function status($queue = "default") {
        $rs = self::runQuery("
            SELECT COUNT(*) as total, COUNT(failed_at) as failed, COUNT(locked_at) as locked
            FROM `Jobs`
            WHERE queue = ?
        ", array($queue));
        $rs = $rs[0];

        $failed = $rs["failed"];
        $locked = $rs["locked"];
        $total  = $rs["total"];
        $outstanding = $total - $locked - $failed;

        return array(
            "outstanding" => $outstanding,
            "locked"      => $locked,
            "failed"      => $failed,
            "total"       => $total
        );
    }
}