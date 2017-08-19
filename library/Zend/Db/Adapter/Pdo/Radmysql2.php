<?php
require_once 'Zend/Db/Adapter/Pdo/Mysql.php';

class Zend_Db_Adapter_Pdo_Radmysql2 extends Zend_Db_Adapter_Pdo_Mysql
{
    protected $_transactionCount = 0;
    
    /**
     * Begin a transaction.
     *
     * @return void
     */
    protected function _beginTransaction()
    {
        $this->_transactionCount++;
	    
		if ($this->_transactionCount === 1)
        {
            $this->_connect();
            $this->_connection->beginTransaction();
			Zend_Wildfire_Plugin_FirePhp::send('RB');
        } 
		
    }

    /**
     * Commit a transaction.
     *
     * @return void
     */
    protected function _commit()
    {
        $this->_transactionCount--;
        
        if ($this->_transactionCount === 0)
        {
            $this->_connect();
            $this->_connection->commit();
			Zend_Wildfire_Plugin_FirePhp::send('RC');
			
        } elseif ($this->_transactionCount < 0) {
			$this->_transactionCount = 0;
			throw new Zend_Exception('No hay transaccion iniciada');
		}
		
    }
	
	public function __destruct() 
	{
		if ($this->_transactionCount !== 0) {
			error_log('##### RAD_DB: Se cerro la coneccion con una transaccion activa! #####');
		}
	}
    
    protected function _rollback()
    {
		if ($this->_transactionCount) {
			$this->_transactionCount = 0;
			$this->_connect();
			$this->_connection->rollBack();
			Zend_Wildfire_Plugin_FirePhp::send('RR');
		}
    }
}

