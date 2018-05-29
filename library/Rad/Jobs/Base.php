<?php
/**
 * Basado en DjJob
 * https://github.com/seatgeek/djjob/blob/master/DJJob.php
 *
 * Se adapto para integrarlo, se mejoro la implementacion y se agregaron funcionalidades
 * @author  Martin A. Santangelo <msantangelo@smartsoftware.com.ar>
 */
/**
 *
 */
class Rad_Jobs_Base {

    // error severity levels
    const CRITICAL = 4;
    const    ERROR = 3;
    const     WARN = 2;
    const     INFO = 1;
    const    DEBUG = 0;

    private static $log_level = self::DEBUG;

    private static $db = null;

    private static $dsn = "";
    protected static $options = array(
      "mysql_user"    => null,
      "mysql_pass"    => null,
      "mysql_retries" => 3
    );

    // use either `configure` or `setConnection`, depending on if
    // you already have a PDO object you can re-use
    public static function configure($dsn, $options = array()) {
        self::$dsn = $dsn;
        self::$options = array_merge(self::$options, $options);
    }

    public static function setLogLevel($const) {
        self::$log_level = $const;
    }

    public static function setConnection(PDO $db) {
        self::$db = $db;
    }

    protected static function getConnection() {
        if (self::$db === null) {
            if (!self::$dsn) {
                throw new Rad_Jobs_Exception("Debe configurar una conexion a la DB para DjJob");
            }
            try {
                // http://stackoverflow.com/questions/237367/why-is-php-pdo-dsn-a-different-format-for-mysql-versus-postgresql
                if (self::$options["mysql_user"] !== null) {
                    self::$db = new PDO(self::$dsn, self::$options["mysql_user"], self::$options["mysql_pass"]);
                } else {
                    self::$db = new PDO(self::$dsn);
                }
                self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                throw new Exception("DJJob No se pudo conectar a la DB. PDO dijo [{$e->getMessage()}]");
            }
        }
        return self::$db;
    }

    public static function runQuery($sql, $params = array()) {
        $retries = self::$options["mysql_retries"];

        for ($attempts = 0; $attempts < $retries; $attempts++) {
            try {
                $stmt = self::getConnection()->prepare($sql);
                $stmt->execute($params);

                $ret = array();
                if ($stmt->rowCount()) {
                    // calling fetchAll on a result set with no rows throws a
                    // "general error" exception
                    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) $ret []= $r;
                }

                $stmt->closeCursor();
                return $ret;
            }
            catch (PDOException $e) {
                // Catch "MySQL server has gone away" error.
                if ($e->errorInfo[1] == 2006) {
                    self::$db = null;
                }
                // Throw all other errors as expected.
                else {
                    throw $e;
                }
            }
        }

        throw new Rad_Jobs_Exception("DJJob no puedo conectar a la DB luego de varios intentos");
    }

    public static function runUpdate($sql, $params = array()) {
        $retries = self::$options["mysql_retries"];

        for ($attempts = 0; $attempts < $retries; $attempts++) {
            try {
                $stmt = self::getConnection()->prepare($sql);
                $stmt->execute($params);
                return $stmt->rowCount();
            }
            catch (PDOException $e) {
                // Catch "MySQL server has gone away" error.
                if ($e->errorInfo[1] == 2006) {
                    self::$db = null;
                }
                // Throw all other errors as expected.
                else {
                    throw $e;
                }
            }
        }

        throw new Rad_Jobs_Exception("DJJob no puedo conectar a la DB luego de varios intentos");
    }

    public static function log($mesg, $severity=self::CRITICAL) {
        if ($severity >= self::$log_level) {
            printf("[%s] %s\n", date('c'), $mesg);
        }
    }
}