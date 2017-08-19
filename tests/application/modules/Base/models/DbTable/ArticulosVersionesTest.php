<?php

class Base_Model_DbTable_ArticulosVersionesTest extends Rad_Test_PHPUnit_BaseDatabaseTestCase
{
    protected $_modelClass = 'Base_Model_DbTable_ArticulosVersiones';

    /**
     * Implements PHPUnit_Extensions_Database_TestCase::getDataSet().
     * @return PHPUnit_Extensions_Database_DataSet_IDataSet 
     */
    protected function getDataSet()
    {
        // Primero cargo datos referenciales que necesito
        $ds = new PHPUnit_Extensions_Database_DataSet_YamlDataSet(dirname(__FILE__) . '/_files/ArticulosVersionesSeed.yml');
        return $ds;
    }

    /**
     * Test de insert en Facturas Ventas
     */
    public function testInsert()
    {
        $data = array( 
            'Articulo'    => 1,
            'Version'     => 1,
            'Fecha'       => '2012-09-11',
            'Descripcion' => 'Version 1'
        );

        $id = $this->_model->insert($data);

        //$insertado = $this->_model->find($id)->current();
 
        $ds = new Zend_Test_PHPUnit_Db_DataSet_QueryDataSet(
            $this->getConnection()
        );

        $ds->addTable('ArticulosVersiones', 'SELECT * FROM ArticulosVersiones');

        $this->assertDataSetsEqual(
            new PHPUnit_Extensions_Database_DataSet_YamlDataSet(
                dirname(__FILE__) . "/_files/ArticulosVersionesInsertIntoAssertion.yml"
            ),
            $ds
        );
        return $id;
    }

    /**
     * Test de insert en Facturas Ventas
     */
    public function testCreateRow()
    {
        $data = array( 
            'Articulo'    => 1,
            'Version'     => 1,
            'Fecha'       => '2012-09-11',
            'Descripcion' => 'Version 1'
        );

        $row = $this->_model->createRow($data);

        $id = $row->save();

        $ds = new Zend_Test_PHPUnit_Db_DataSet_QueryDataSet(
            $this->getConnection()
        );

        $ds->addTable('ArticulosVersiones', 'SELECT * FROM ArticulosVersiones');

        $this->assertDataSetsEqual(
            new PHPUnit_Extensions_Database_DataSet_YamlDataSet(
                dirname(__FILE__) . "/_files/ArticulosVersionesInsertIntoAssertion.yml"
            ),
            $ds
        );
    }

    /**
     * @depends testInsert
     */
    public function testAgregarDetallePorId()
    {
        $data = array( 
            'Articulo'    => 1,
            'Version'     => 1,
            'Fecha'       => '2012-09-11',
            'Descripcion' => 'Version 1'
        );

        $id = $this->_model->insert($data);

        $this->_model->agregarDetalle(1, 1, 1);

        $ds = new Zend_Test_PHPUnit_Db_DataSet_QueryDataSet(
            $this->getConnection()
        );

        $ds->addTable('ArticulosVersionesDetalles', 'SELECT * FROM ArticulosVersionesDetalles');

        $this->assertDataSetsEqual(
            new PHPUnit_Extensions_Database_DataSet_YamlDataSet(
                dirname(__FILE__) . "/_files/ArticulosVersionesAgregarDetalleAssertion.yml"
            ),
            $ds
        );
    }

    /**
     * @depends testAgregarDetallePorId
     */
    public function testClonarVersion()
    {
        $data = array( 
            'Articulo'    => 1,
            'Version'     => 1,
            'Fecha'       => '2012-09-11',
            'Descripcion' => 'Version 1'
        );

        $id = $this->_model->insert($data);

        $this->_model->agregarDetalle(1, 1, 1);

        // Clonamos
        $this->_model->clonarVersion(1);


        $ds = new Zend_Test_PHPUnit_Db_DataSet_QueryDataSet(
            $this->getConnection()
        );

        $ds1 = new Zend_Test_PHPUnit_Db_DataSet_QueryDataSet(
            $this->getConnection()
        );

        

        $ds->addTable('ArticulosVersionesDetalles', 'SELECT * FROM ArticulosVersionesDetalles');

        $this->assertDataSetsEqual(
            new PHPUnit_Extensions_Database_DataSet_YamlDataSet(
                dirname(__FILE__) . "/_files/ArticulosVersionesClonarDetalleAssertion.yml"
            ),
            $ds
        );

        $ds1->addTable('ArticulosVersiones', 'SELECT * FROM ArticulosVersiones');

        $this->assertDataSetsEqual(
            new PHPUnit_Extensions_Database_DataSet_YamlDataSet(
                dirname(__FILE__) . "/_files/ArticulosVersionesClonarAssertion.yml"
            ),
            $ds1
        );
    }

    /**
     * @depends testInsert
     */
    public function testAgregarDetallePorArticuloVersion()
    {
        $data = array( 
            'Articulo'    => 1,
            'Version'     => 1,
            'Fecha'       => '2012-09-11',
            'Descripcion' => 'Version 1'
        );

        $id = $this->_model->insert($data);

        $articuloVersion = $this->_model->find(1)->current();

        $this->_model->agregarDetalle($articuloVersion, 1, 1);

        $ds = new Zend_Test_PHPUnit_Db_DataSet_QueryDataSet(
            $this->getConnection()
        );

        $ds->addTable('ArticulosVersionesDetalles', 'SELECT * FROM ArticulosVersionesDetalles');

        $this->assertDataSetsEqual(
            new PHPUnit_Extensions_Database_DataSet_YamlDataSet(
                dirname(__FILE__) . "/_files/ArticulosVersionesAgregarDetalleAssertion.yml"
            ),
            $ds
        );
    }
}