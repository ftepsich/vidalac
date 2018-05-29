<?php

// class Zend_Test_PHPUnit_Db_Operation_Truncate2 extends Zend_Test_PHPUnit_Db_Operation_Truncate
// {
//     public function execute(PHPUnit_Extensions_Database_DB_IDatabaseConnection $connection, PHPUnit_Extensions_Database_DataSet_IDataSet $dataSet)
//     {
        
//         if(!($connection instanceof Zend_Test_PHPUnit_Db_Connection)) {
//             require_once "Zend/Test/PHPUnit/Db/Exception.php";
//             throw new Zend_Test_PHPUnit_Db_Exception("Not a valid Zend_Test_PHPUnit_Db_Connection instance, ".get_class($connection)." given!");
//         }
//         file_put_contents('/var/www-desarrollo/martin/tests/log.ttt', "------\n", FILE_APPEND);
//         file_put_contents('/var/www-desarrollo/martin/tests/log.ttt', print_r($dataSet, true), FILE_APPEND);
//         foreach ($dataSet->getReverseIterator() AS $table) {
//             try {
                
//                 $tableName = $table->getTableMetaData()->getTableName();
//                 file_put_contents('/var/www-desarrollo/martin/tests/log.ttt', $tableName."\n", FILE_APPEND);
//                 $this->_truncate($connection->getConnection(), $tableName);
//             } catch (Exception $e) {
//                 throw new PHPUnit_Extensions_Database_Operation_Exception('TRUNCATE', 'TRUNCATE '.$tableName.'', array(), $table, $e->getMessage());
//             }
//         }
//     }
// }

/**
 * Rad_Test_PHPUnit_BaseDatabaseTestCases
 *
 * Clase base para el testing de modelos del sistema
 * 
 * @copyright SmartSoftware Argentina
 * @package Rad
 * @subpackage Test
 * @author Martin Alejandro Santangelo
 */
abstract class Rad_Test_PHPUnit_BaseDatabaseTestCase extends Zend_Test_PHPUnit_DatabaseTestCase
{
    /**
     * Database connection
     * @var Zend_Test_PHPUnit_Db_Connection
     */
    protected $_db;

    /**
     * Instancia del modelo a ser testeado, definido por $_modelClass
     * @var object
     */
    protected $_model;

    /** 
     * Nombre del modelo a ser testeado, se supone que se sobreescribe en la clase hija
     * @var string
     */
    protected $_modelClass;

    /** 
     * Path al directorio donde se almacenan los fixtures
     * @var string
     */
    protected $_filesDir;

    protected function initApp()
    {
        $app = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');
        $app->bootstrap();
        $options = $app->getOptions();
        $schema = $options['resources']['db']['params']['dbname'];
        $db = $app->getBootstrap()->getPluginResource('db')->getDbAdapter();
        $this->_db = $this->createZendDbConnection($db, $schema);
    }

    /** 
     * Inicializa el modelo.
     * @return void
     */
    public function setUp()
    {   
        $this->initApp();
        $this->application = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');
        $this->application->bootstrap();

        parent::setUp();

        $this->_filesDir = dirname(__FILE__) . '/_files/' . $this->_modelClass;
        $this->_model = new $this->_modelClass();
    }

    /**
     * Implements PHPUnit_Extensions_Database_TestCase::getConnection().
     * @return Zend_Test_PHPUnit_Db_Connection
     */
    protected function getConnection()
    {
        if (empty($this->_db)) {
            $options = $this->application->getOptions();
            $schema = $options['resources']['db']['params']['dbname'];
            $db = $this->application->getBootstrap()->getPluginResource('db')->getDbAdapter();
            $this->_db = $this->createZendDbConnection($db, $schema);
        }
        return $this->_db;
    }
}