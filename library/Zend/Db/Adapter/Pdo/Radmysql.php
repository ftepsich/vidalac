<?php
require_once 'Zend/Db/Adapter/Mysqli.php';

// function templog($t) {
//     $d = debug_backtrace();
//     array_shift($d);
//     array_shift($d);
//     $d = array_shift($d);
//     Rad_Jobs_Base::log($t.' '.$d['file'].' '.$d['line']);
// }

class Zend_Db_Adapter_Pdo_Radmysql extends Zend_Db_Adapter_Pdo_Mysql
{

    private $_transactionCount = 0;

    public function getTransactionCount()
    {
        return $this->_transactionCount;
    }

    public function query($sql, $bind = array())
    {
        try {
            return parent::query($sql, $bind);
        } catch (Zend_Db_Statement_Exception $e) {

            if (APPLICATION_ENV != 'production') {

                $e->sql = $sql;
                $e->parametros = $bind;
                throw $e;
            } else {
                $msg = $this->_getStmtError($e->getCode());
//            throw new Exception($sql . $e->getMessage());
                if (!$msg) {
                    $msg = 'Error en la consulta a la base de datos.';
                }

                $ex = new Zend_Db_Statement_Mysqli_Exception($msg, $e->getCode());
                $ex->sql = $sql;
                throw $ex;
            }

        }
    }

    protected function _getStmtError($code)
    {
        switch ($code) {
            case 1451:
                return "No se puede realizar la operación ya que existen datos relacionados";
                break;
        }
    }

    /**
     * Begin a transaction.
     *
     * @return void
     */
    protected function _beginTransaction()
    {
        if ($this->_transactionCount === 0) {
            $this->_connect();
            //$this->_connection->autocommit(false);
            parent::_beginTransaction();
        }

        $this->_transactionCount++;
    }

    /**
     * Commit a transaction.
     *
     * @return void
     */
    protected function _commit()
    {
        $this->_transactionCount--;

        if ($this->_transactionCount === 0) {
            $this->_connect();
            $this->_connection->commit();
            //$this->_connection->autocommit(true);
        }

        if ($this->_transactionCount < 0) {
            $this->_transactionCount = 0;
            throw new Exception('No existe una transaccion abierta para hacer commit');
        }
    }

    protected function _rollback()
    {
        if ($this->_transactionCount > 0) {
            $this->_transactionCount = 0;
            $this->_connect();
            $this->_connection->rollBack();
        }
    }

}

