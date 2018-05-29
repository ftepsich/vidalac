<?php

class Base_Model_DbTable_ArticulosGenericosTest extends Rad_Test_PHPUnit_BaseDatabaseTestCase
{
    protected $_modelClass = 'Base_Model_DbTable_ArticulosGenericos';

    /**
     * Implements PHPUnit_Extensions_Database_TestCase::getDataSet().
     * @return PHPUnit_Extensions_Database_DataSet_IDataSet 
     */
    protected function getDataSet()
    {
        // Primero cargo datos referenciales que necesito
        $ds = new PHPUnit_Extensions_Database_DataSet_YamlDataSet(dirname(__FILE__) . '/_files/ArticulosGenericosSeed.yml');
        return $ds;
    }

    /**
     * Test de insert en Facturas Ventas
     */
    public function testInsert()
    {

        $data = array( 
            'Tipo'                       => 1,
            'Codigo'                     => 1000,
            'Descripcion'                => 'Articulo 1',
            'CodigoDeBarras'             => "",
            'UnidadDeMedida'             => 5,
            'UnidadDeMedidaDeProduccion' => 5,
            'FactorDeConversion'         => 1,
            'TipoDeControlDeStock'       => 1,
            'EsInsumo'                   => 1,
            'EsProducido'                => 1,
            'EsParaVenta'                => 1,
            'EsParaCompra'               => 1,
            'EsFinal'                    => 1,
            'RequiereLote'               => 0,
            'IVA'                        => 1,
            'Marca'                      => 1,
            'EsMateriaPrima'             => 0,
            'RequiereProtocolo'          => 0,
            'Cuenta'                     => 1,
            'Leyenda'                    => "X",
            'RNPA'                       => "",
            'DescripcionLarga'           => 'Articulo 1 x 50kg'
        );

        $id = $this->_model->insert($data);

        //$insertado = $this->_model->find($id)->current();
    
        // Vemos si se inserto el Articulo
        $ds = new Zend_Test_PHPUnit_Db_DataSet_QueryDataSet(
            $this->getConnection()
        );

        $ds->addTable('Articulos', 'SELECT * FROM Articulos');

        $this->assertDataSetsEqual(
            new PHPUnit_Extensions_Database_DataSet_YamlDataSet(
                dirname(__FILE__) . "/_files/ArticulosGenericosInsertIntoAssertion.yml"
            ),
            $ds
        );

        // Vemos si creo la version inicial
        $ds1 = new Zend_Test_PHPUnit_Db_DataSet_QueryDataSet(
            $this->getConnection()
        );

        $ds1->addTable('ArticulosVersiones', 'SELECT Id, Articulo, Version, Descripcion FROM ArticulosVersiones');

        $this->assertDataSetsEqual(
            new PHPUnit_Extensions_Database_DataSet_YamlDataSet(
                dirname(__FILE__) . "/_files/ArticulosVersionesInicialAssertion.yml"
            ),
            $ds1
        );
    }

    /**
     * Test de insert en Facturas Ventas
     */
    public function testCreateRow()
    {
        $data = array( 
            'Tipo'                       => 1,
            'Codigo'                     => 1000,
            'Descripcion'                => 'Articulo 1',
            'CodigoDeBarras'             => "",
            'UnidadDeMedida'             => 5,
            'UnidadDeMedidaDeProduccion' => 5,
            'FactorDeConversion'         => 1,
            'TipoDeControlDeStock'       => 1,
            'EsInsumo'                   => 1,
            'EsProducido'                => 1,
            'EsParaVenta'                => 1,
            'EsParaCompra'               => 1,
            'EsFinal'                    => 1,
            'RequiereLote'               => 0,
            'IVA'                        => 1,
            'Marca'                      => 1,
            'EsMateriaPrima'             => 0,
            'RequiereProtocolo'          => 0,
            'Cuenta'                     => 1,
            'Leyenda'                    => "X",
            'RNPA'                       => "",
            'DescripcionLarga'           => 'Articulo 1 x 50kg'
        );


        $row = $this->_model->createRow($data);

        $id = $row->save();

        // Vemos si se inserto el Articulo
        $ds = new Zend_Test_PHPUnit_Db_DataSet_QueryDataSet(
            $this->getConnection()
        );

        $ds->addTable('Articulos', 'SELECT * FROM Articulos');

        $this->assertDataSetsEqual(
            new PHPUnit_Extensions_Database_DataSet_YamlDataSet(
                dirname(__FILE__) . "/_files/ArticulosGenericosInsertIntoAssertion.yml"
            ),
            $ds
        );

        // Vemos si creo la version inicial
        $ds1 = new Zend_Test_PHPUnit_Db_DataSet_QueryDataSet(
            $this->getConnection()
        );

        $ds1->addTable('ArticulosVersiones', 'SELECT Id, Articulo, Version, Descripcion FROM ArticulosVersiones');

        $this->assertDataSetsEqual(
            new PHPUnit_Extensions_Database_DataSet_YamlDataSet(
                dirname(__FILE__) . "/_files/ArticulosVersionesInicialAssertion.yml"
            ),
            $ds1
        );
    }


    // /**
    //  * Proveedor de datos para test de validadores
    //  */
    // public static function validadoresProvider()
    // {
    //     $data = array(
    //         'Id'                    => 10,
    //         'TipoDeComprobante'     => 24,
    //         'Punto'                 => 1,
    //         'Numero'                => 0,
    //         'Persona'               => 1,
    //         'LibroIVA'              => 1,
    //         'FechaEmision'          => '2012-09-01',
    //         'FechaVencimiento'      => null,
    //         'Divisa'                => 1,
    //         'ValorDivisa'           => 1,
    //         'ListaDePrecio'         => null,
    //         'CondicionDePago'       => 1,
    //         'Observaciones'         => null,
    //         'ObservacionesImpresas' => null
    //     );

    //     // Tipo de Comprobante en Rango
    //     $dataTipoComprobante = $data;
    //     $dataTipoComprobante['TipoDeComprobante'] = 10;
    //     $dataTipoComprobante['Numero'] = 1;

    //     return array(
    //         // Numero Requerido
    //         array(
    //             'Numero Requerido',
    //             $data,
    //             'No se ingreso el numero del comprobante.'
    //         ),
    //         // Tipo de Comprobante en Rango
    //         array(
    //             'TipoComprobante Rango',
    //             $dataTipoComprobante,
    //             'El valor asigando a `TipoDeComprobante` es invalido<br>TipoDeComprobante: El valor asigando a `TipoDeComprobante` es invalido'
    //         )
    //     );
    // }

    // /**
    //  * @dataProvider validadoresProvider
    //  */
    // public function testValidadores($msg, $data, $emsg)
    // {
    //     try {
    //         $id = $this->_model->insert($data);    
    //     } catch (Rad_Db_Table_Exception $e) {
            
    //         if ($e->getMessage() == $emsg) return;
            
    //         $this->fail("$msg:\n Se esperaba:\n  $emsg \n y se obtuvo:\n   ".$e->getMessage());
    //     }

    //     $this->fail('$msg: Se esperaba una excepcion Rad_Db_Table_Exception');
    // }
}