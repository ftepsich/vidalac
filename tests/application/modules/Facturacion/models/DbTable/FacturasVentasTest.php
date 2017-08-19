<?php

class Facturacion_Model_DbTable_FacturasVentasTest extends Rad_Test_PHPUnit_BaseDatabaseTestCase
{
    protected $_modelClass = 'Facturacion_Model_DbTable_FacturasVentas';

    /**
     * Implements PHPUnit_Extensions_Database_TestCase::getDataSet().
     * @return PHPUnit_Extensions_Database_DataSet_IDataSet 
     */
    protected function getDataSet()
    {
        // Primero cargo datos referenciales que necesito
        $ds = new PHPUnit_Extensions_Database_DataSet_YamlDataSet(dirname(__FILE__) . '/_files/FacturasSeed.yml');
        return $ds;
    }

    /*
    protected function tearDown()
    {
        $db = Zend_Registry::get('db');
        $db->query("TRUNCATE TABLE Comprobantes;");
        parent::tearDown();
    }*/

    /**
     * Test de insert en Facturas Ventas
     */
    public function testInsert()
    {
        $data = array(
            'Id'                    => 10,
            'TipoDeComprobante'     => 24,
            'Punto'                 => 1,
            'Numero'                => 2,
            'Persona'               => 1,
            'LibroIVA'              => 1,
            'FechaEmision'          => '2012-09-01',
            'FechaCierre'           => null,
            'FechaVencimiento'      => null,
            'Divisa'                => 1,
            'ValorDivisa'           => 1,
            'ListaDePrecio'         => null,
            'CondicionDePago'       => 1,
            'Observaciones'         => null,
            'ObservacionesImpresas' => null
        );

        $id = $this->_model->insert($data);

        //$insertado = $this->_model->find($id)->current();
 
        $ds = new Zend_Test_PHPUnit_Db_DataSet_QueryDataSet(
            $this->getConnection()
        );
        $ds->addTable('Comprobantes', 'SELECT * FROM Comprobantes');

        $this->assertDataSetsEqual(
            new PHPUnit_Extensions_Database_DataSet_YamlDataSet(
                dirname(__FILE__) . "/_files/FacturasVentasInsertIntoAssertion.yml"
            ),
            $ds
        );
    }

    /**
     * Test de insert en Facturas Ventas
     */
    public function testCreateRow()
    {
        $data = array(
            'Id'                    => 10,
            'TipoDeComprobante'     => 24,
            'Punto'                 => 1,
            'Numero'                => 2,
            'Persona'               => 1,
            'LibroIVA'              => 1,
            'FechaEmision'          => '2012-09-01',
            'FechaCierre'           => null,
            'Divisa'                => 1,
            'ValorDivisa'           => 1,
            'ListaDePrecio'         => null,
            'CondicionDePago'       => 1,
            'Observaciones'         => null,
            'ObservacionesImpresas' => null
        );

        $row = $this->_model->createRow($data);

        $id = $row->save();

        $ds = new Zend_Test_PHPUnit_Db_DataSet_QueryDataSet(
            $this->getConnection()
        );

        $ds->addTable('Comprobantes', 'SELECT * FROM Comprobantes');

        $this->assertDataSetsEqual(
            new PHPUnit_Extensions_Database_DataSet_YamlDataSet(
                dirname(__FILE__) . "/_files/FacturasVentasInsertIntoAssertion.yml"
            ),
            $ds
        );
    }


    /**
     * Proveedor de datos para test de validadores
     */
    public static function validadoresProvider()
    {
        $data = array(
            'Id'                    => 10,
            'TipoDeComprobante'     => 24,
            'Punto'                 => 1,
            'Numero'                => 0,
            'Persona'               => 1,
            'LibroIVA'              => 1,
            'FechaEmision'          => '2012-09-01',
            'FechaVencimiento'      => null,
            'Divisa'                => 1,
            'ValorDivisa'           => 1,
            'ListaDePrecio'         => null,
            'CondicionDePago'       => 1,
            'Observaciones'         => null,
            'ObservacionesImpresas' => null
        );

        // Tipo de Comprobante en Rango
        $dataTipoComprobante = $data;
        $dataTipoComprobante['TipoDeComprobante'] = 10;
        $dataTipoComprobante['Numero'] = 1;

        return array(
            // Numero Requerido
            array(
                'Numero Requerido',
                $data,
                'No se ingreso el numero del comprobante.'
            ),
            // Tipo de Comprobante en Rango
            array(
                'TipoComprobante Rango',
                $dataTipoComprobante,
                'El valor asigando a `TipoDeComprobante` es invalido<br>TipoDeComprobante: El valor asigando a `TipoDeComprobante` es invalido'
            )
        );
    }

    /**
     * @dataProvider validadoresProvider
     */
    public function testValidadores($msg, $data, $emsg)
    {
        try {
            $id = $this->_model->insert($data);    
        } catch (Rad_Db_Table_Exception $e) {
            
            if ($e->getMessage() == $emsg) return;
            
            $this->fail("$msg:\n Se esperaba:\n  $emsg \n y se obtuvo:\n   ".$e->getMessage());
        }

        $this->fail('$msg: Se esperaba una excepcion Rad_Db_Table_Exception');
    }
}