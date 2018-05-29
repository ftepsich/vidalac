<?php

namespace Rad\Util;

require '../Exception.php';

class SemaforoException extends \Rad_Exception{}

/**
 * Implementacion de sistema de semaforos para procesos
 * , sacado de Magento Mage_Index_Model_Process
 *
 */
class Semaforo
{
    static public $dir;
     /**
     * Process lock properties
     */
    protected $_isLocked = null;
    protected $_lockFile = null;
    protected $_id       = null;

    public function __construct($proccessId)
    {
        if (!$proccessId) {
            throw new SemaforoException('El argumento proccessId es obligatorio');
        }

        $this->_id = $proccessId;
    }

    public function getId()
    {
        return $this->_id;
    }

    /**
     * Get lock file resource
     *
     * @return resource
     */
    protected function _getLockFile()
    {
        if ($this->_lockFile === null) {
            $varDir = self::$dir;
            $file = $varDir . '/' . 'index_process_'.$this->getId().'.lock';
            if (is_file($file)) {
                $this->_lockFile = fopen($file, 'w');
            } else {
                $this->_lockFile = fopen($file, 'x');
            }
            fwrite($this->_lockFile, date('r'));
        }
        return $this->_lockFile;
    }

    /**
     * Lock process without blocking.
     * This method allow protect multiple process runing and fast lock validation.
     *
     * @return Mage_Index_Model_Process
     */
    public function lock()
    {
        $this->_isLocked = true;
        flock($this->_getLockFile(), LOCK_EX | LOCK_NB);
        return $this;
    }

    /**
     * Lock and block process.
     * If new instance of the process will try validate locking state
     * script will wait until process will be unlocked
     *
     * @return Mage_Index_Model_Process
     */
    public function lockAndBlock()
    {
        $this->_isLocked = true;
        flock($this->_getLockFile(), LOCK_EX);
        return $this;
    }

    /**
     * Unlock process
     *
     * @return Mage_Index_Model_Process
     */
    public function unlock()
    {
        $this->_isLocked = false;
        flock($this->_getLockFile(), LOCK_UN);
        return $this;
    }

    /**
     * Check if process is locked
     *
     * @return bool
     */
    public function isLocked()
    {
        if ($this->_isLocked !== null) {
            return $this->_isLocked;
        } else {
            $fp = $this->_getLockFile();
            if (flock($fp, LOCK_EX | LOCK_NB)) {
                flock($fp, LOCK_UN);
                return false;
            }
            return true;
        }
    }

    /**
     * Close file resource if it was opened
     */
    public function __destruct()
    {
        if ($this->_lockFile) {
            fclose($this->_lockFile);
        }
    }
}

