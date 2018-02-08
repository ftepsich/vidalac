<?php
/**
 * Testeo de funcionalidad avanzada del modelo factura, estos test requieren una factura completa cargada
 */
class Facturacion_Model_DbTable_FacturasVentasAvanzadosTest extends Rad_Test_PHPUnit_BaseDatabaseTestCase
{
    protected $_modelClass = 'Facturacion_Model_DbTable_FacturasVentas';

    /**
     * Implements PHPUnit_Extensions_Database_TestCase::getDataSet().
     * @return PHPUnit_Extensions_Database_DataSet_IDataSet 
     */
    protected function getDataSet()
    {
        // Primero cargo datos referenciales que necesito
        $ds = new PHPUnit_Extensions_Database_DataSet_YamlDataSet(dirname(__FILE__) . '/_files/FacturasAvanzadosSeed.yml');

        // // Agrego los datos de facturas
        /*$ds->addYamlFile(dirname(__FILE__) . '/_files/FacturasSeed.yml');
        $ds->addYamlFile(dirname(__FILE__) . '/_files/ArticulosFacturasSeed.yml');*/
        return $ds;
    }

    public function testInsertarConceptosDesdeControlador(){
        // Generamos los conceptos impositivos
        $this->_model->insertarConceptosDesdeControlador(2);

        $ds = new Zend_Test_PHPUnit_Db_DataSet_QueryDataSet(
            $this->getConnection()
        );

        $ds->addTable('Comprobantes', 'SELECT 
            Id,
            Persona,
            Punto,
            Monto,
            Numero,
            TipoDeComprobante,
            LibroIVA,
            Divisa,
            ValorDivisa,
            ComprobantePadre,
            ConceptoImpositivo,
            ConceptoImpositivoPorcentaje,
            MontoImponible
         FROM Comprobantes');

        $this->assertDataSetsEqual(
            new PHPUnit_Extensions_Database_DataSet_YamlDataSet(
                dirname(__FILE__) . "/_files/FacturasVentasConceptosAssertion.yml"
            ),
            $ds
        );
    }

    /**
     * @depends testInsertarConceptosDesdeControlador
     */
    public function testRecuperarConceptosImpositivos()
    {
        // Generamos los conceptos impositivos
        $this->_model->insertarConceptosDesdeControlador(2);

        // recupero los conceptos generados
        $ci = $this->_model->recuperarConceptosImpositivos(2);

        $this->assertEquals(round($ci,2), 84, 'Los conceptos no se calcularon correctamente');
    }

    public function testCerrar()
    {
        // Generamos los conceptos impositivos
        $this->_model->insertarConceptosDesdeControlador(2);
        
        // cierro primero con la factura A
        $ci = $this->_model->cerrar(2);

        /**
         * Esto no deberia estar aca, lo pongo simeplemente para ver si los subscrivers 
         * funcionan correctamente y asientan al cerrar la factura
         */
        $ctaCte = new Contable_Model_DbTable_CuentasCorrientes();
        $libroIvaDet = new Contable_Model_DbTable_LibrosIVADetalles();

        // asento correctamente en ctacte?
        $saldo = $ctaCte->getSaldo(2);

        $this->assertEquals(1084, round($saldo,2), 'No se asento correctamente a la Cuenta Corriente Factura A');

        // cierro la factura B
        $ci = $this->_model->cerrar(1);

        // asento correctamente en ctacte
        $saldo = $ctaCte->getSaldo(2);

        $this->assertEquals(2084, round($saldo,2), 'No se asento correctamente a la Cuenta Corriente Factura B');

        // comprobar libro de iva
        $ds = $this->getLibroIvaDataset(2);

        $this->assertDataSetsEqual(
            new PHPUnit_Extensions_Database_DataSet_YamlDataSet(
                dirname(__FILE__) . "/_files/FacturasVentasALibroIvaAssertion.yml"
            ),
            $ds,
            'Error al asentar Libro IVA Factura A'
        );
        
        $ds = $this->getLibroIvaDataset(1);

        $this->assertDataSetsEqual(
            new PHPUnit_Extensions_Database_DataSet_YamlDataSet(
                dirname(__FILE__) . "/_files/FacturasVentasBLibroIvaAssertion.yml"
            ),
            $ds,
            'Error al asentar Libro IVA Factura B'
        );
    }

    protected function getLibroIvaDataset($id)
    {
        $ds = new Zend_Test_PHPUnit_Db_DataSet_QueryDataSet(
            $this->getConnection()
        );

        $ds->addTable('LibrosIVADetalles', 'SELECT 
            Comprobante,
            Persona,
            LibroIVA,
            TipoDeLibro,
            ImporteNetoGravado105,
            ImporteNetoGravado210,
            ImporteNetoGravado270,
            ImporteIVA105,
            ImporteIVA210,
            ImporteIVA270,
            ImporteImpuestosInternos,
            ImporteConceptosExentosONoGravados,
            ImportePercepcionesIVA,
            ImportePercepcionesGanancias,
            ImportePercepcionesSuss,
            ImportePercepcionesIB,
            ImporteOtrasPercepcionesImpuestosNacionales,
            ImporteOtrasPercepcionesImpuestosProvinciales,
            ImportePercepcionesTasaMunicipales,
            ImporteTotalComprobante,
            ImporteRetencionesIVA,
            ImporteRetencionesGanancias,
            ImporteRetencionesSuss,
            ImporteRetencionesIB,
            ImporteRetencionesTasaMunicipales,
            ImporteOtrasRetencionesImpuestosProvinciales,
            ImporteOtrasRetencionesImpuestosNacionales
         FROM LibrosIVADetalles WHERE Comprobante = '.$id);
        return $ds;
    }

    /**
     * @depends testInsertarConceptosDesdeControlador
     */
    public function testMontoTotalFacturas()
    {

        $monto = $this->_model->recuperarMontoTotal(1);
        $this->assertEquals(1000, round($monto,2), 'Error al calucular monto total Factura B');

        // Generamos los conceptos impositivos
        $this->_model->insertarConceptosDesdeControlador(2);

        $monto = $this->_model->recuperarMontoTotal(2);
        $this->assertEquals(1084, round($monto,2), 'Error al calucular monto total Factura A');
    }
}