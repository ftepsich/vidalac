<?php
require_once 'Rad/Db/Table.php';

/**
 * Base_Model_DbTable_ConceptosImpositivos
 *
 * Conceptos Impositivos
 *
 * @package     Aplicacion
 * @subpackage 	Base
 * @class       Base_Model_DbTable_ConceptosImpositivos
 * @extends     Rad_Db_Table
 * @copyright   SmartSoftware Argentina 2010
 */
class Base_Model_DbTable_ConceptosImpositivos extends Rad_Db_Table
{

    protected $_name = 'ConceptosImpositivos';
    protected $_sort = array('Descripcion asc');
    protected $_defaultSource = self::DEFAULT_CLASS;
    protected $_defaultValues = array(
        'TipoDeMontoMinimo'     => '2',
        'EnUso'                 => '1',
        'ParaVenta'             => '0',
        'ParaCompra'            => '0',
        'ParaCobro'             => '0',
        'ParaPago'              => '0',
        'ParaCalculoCosto'      => '0',
        'ParaVenta'             => '0',
        'EsRetencion'           => '0',
        'EsPercepcion'          => '0',
        'EsIva'                 => '0',
        'EsIvaDefault'          => '0',
        'SeAplicaEmpresa'       => '0',
        'MontoMinimo'           => '0'
    );

    /* Preseteos de IVA y datos fiscales */

    public $iva21;
    public $iva105;
    public $iva27;
    public $iva0;
    public $ivaExcento;
    public $ivaNoGravado;
    public $iva025;
    public $iva05;

    public $impInterno;

    public $AmbitoNacional;
    public $AmbitoProvincial;
    public $AmbitoMunicipal;

    // Inicio  protected $_referenceMap --------------------------------------------------------------------------
    protected $_referenceMap = array(
        'CuentasActivos' => array(
            'columns'           => 'CuentaActivo',
            'refTableClass'     => 'Contable_Model_DbTable_PlanesDeCuentas',
            'refJoinColumns'    => array("Descripcion"),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist/fetch/CuentaImpositivaActivo',
            'refTable'          => 'PlanesDeCuentas',
            'refColumns'        => 'Id',
            'comboPageSize'     => 20
        ),
        'CuentasPasivos' => array(
            'columns'           => 'CuentaPasivo',
            'refTableClass'     => 'Contable_Model_DbTable_PlanesDeCuentas',
            'refJoinColumns'    => array("Descripcion"),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist/fetch/CuentaImpositivaPasivo',
            'refTable'          => 'PlanesDeCuentas',
            'refColumns'        => 'Id',
            'comboPageSize'     => 20
        ),
        'EntesRecaudadores' => array(
            'columns'           => 'EnteRecaudador',
            'refTableClass'     => 'Base_Model_DbTable_EntesRecaudadores',
            'refJoinColumns'    => array("Descripcion"),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'EntesRecaudadores',
            'refColumns'        => 'Id'
        ),
        'TiposDeConceptos' => array(
            'columns'           => 'TipoDeConcepto',
            'refTableClass'     => 'Base_Model_DbTable_TiposDeConceptos',
            'refJoinColumns'    => array("Descripcion"),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'TiposDeConceptos',
            'refColumns'        => 'Id'
        )
        ,
        'TiposDeMontosMinimos' => array(
            'columns'           => 'TipoDeMontoMinimo',
            'refTableClass'     => 'Base_Model_DbTable_TiposDeMontosMinimos',
            'refJoinColumns'    => array("Descripcion"),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'TiposDeMontosMinimos',
            'refColumns'        => 'Id'
        )
    );

    protected $_validators = array(
        'Descripcion' => array(
            'NotEmpty',
            'allowEmpty'=>false,
            array(
                'Db_NoRecordExists',
                'ConceptosImpositivos',
                'Descripcion',
                'Id <> {Id}'
            ),
            'messages' => array(
                'Falta ingresar la Descripción.',
                'Ya existe un concepto con la misma descripción.'
            )
        )
    );

    protected $_dependentTables = array(
        'Base_Model_DbTable_ClientesConceptosImpositivos',
        'Base_Model_DbTable_ProveedoresConceptosImpositivos'
    );

    public function init()
    {
        $cfg = Rad_Cfg::get();

        $this->iva27           = $cfg->ConceptosImpositivos->iva27;
        $this->iva21           = $cfg->ConceptosImpositivos->iva21;
        $this->iva105          = $cfg->ConceptosImpositivos->iva105;
        $this->iva05           = $cfg->ConceptosImpositivos->iva05;
        $this->iva025          = $cfg->ConceptosImpositivos->iva025;
        $this->iva0            = $cfg->ConceptosImpositivos->iva0;
        $this->ivaExcento      = $cfg->ConceptosImpositivos->ivaExento;
        $this->ivaNoGravado    = $cfg->ConceptosImpositivos->ivaNoGravado;

        $this->impInterno      = $cfg->ConceptosImpositivos->impInterno;

        $this->AmbitoNacional   = $cfg->Ambito->Nacional;
        $this->AmbitoProvincial = $cfg->Ambito->Provincial;
        $this->AmbitoMunicipal  = $cfg->Ambito->Municipal;

        parent::init();
    }

    /**
     * verifica si un concepto impositivo es de tipo IVA
     *
     * @param int $idConcepto       identificador del Concepto impositivo
     *
     * @return int
     */
    public function esIVA($idConcepto) {

        $R = $this->find($idConcepto)->current();
        if (!$R) {
            throw new Rad_Db_Table_Exception("No se puede verificar si el concepto $idConcepto impositivo es IVA.");
        }
        return $R->EsIVA;
    }

    public function fetchEsIVA($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = " EsRetencion = 0 and EsPercepcion = 0 and EsIVA = 1 and EnUso = 1 ";
        $order = " EsIVADefault desc";
        $where = $this->_addCondition($where, $condicion);
        return parent:: fetchAll($where, $order, $count, $offset);
    }

    public function fetchEsIVACompra($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = " ParaCompra = 1 and EsRetencion = 0 and EsPercepcion = 0 and EsIVA = 1 and EnUso = 1 ";
        $order = " EsIVADefault desc";
        $where = $this->_addCondition($where, $condicion);
        return parent:: fetchAll($where, $order, $count, $offset);
    }

    public function fetchEsIVAVenta($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = " ParaVenta = 1 and EsRetencion = 0 and EsPercepcion = 0 and EsIVA = 1 and EnUso = 1 ";
        $order = " EsIVADefault desc";
        $where = $this->_addCondition($where, $condicion);
        return parent:: fetchAll($where, $order, $count, $offset);
    }

    public function fetchNoEsIva($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = "EsIVA = 0";
        //$order = " EsIVADefault desc";
        $where = $this->_addCondition($where, $condicion);
        return parent:: fetchAll($where, $order, $count, $offset);
    }

    public function fetchParaCompras($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = "ParaCompra = 1 and EsIVA = 0 and EnUso = 1";
        //$order = " EsIVADefault desc";
        $where = $this->_addCondition($where, $condicion);
        return parent:: fetchAll($where, $order, $count, $offset);
    }

    public function fetchParaPagos($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = "ParaPago = 1 and EsIVA = 0 and EnUso = 1";
        //$order = " EsIVADefault desc";
        $where = $this->_addCondition($where, $condicion);
        return parent:: fetchAll($where, $order, $count, $offset);
    }

    public function fetchParaVentas($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = "ParaVenta = 1 and EsIVA = 0 and EnUso = 1";
        //$order = " EsIVADefault desc";
        $where = $this->_addCondition($where, $condicion);
        return parent:: fetchAll($where, $order, $count, $offset);
    }

    public function fetchParaCobros($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = "ParaCobro = 1 and EsIVA = 0 and EnUso = 1";
        //$order = " EsIVADefault desc";
        $where = $this->_addCondition($where, $condicion);
        return parent:: fetchAll($where, $order, $count, $offset);
    }
    public function fetchParaProveedores($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = "(ParaPago = 1 or ParaCompra=1) and EsIVA = 0 and EnUso = 1";
        //$order = " EsIVADefault desc";
        $where = $this->_addCondition($where, $condicion);
        return parent:: fetchAll($where, $order, $count, $offset);
    }

      public function fetchParaCliente($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = "(ParaPago = 1 or ParaVenta=1) and EsIVA = 0 and EnUso = 1";
        //$order = " EsIVADefault desc";
        $where = $this->_addCondition($where, $condicion);
        return parent:: fetchAll($where, $order, $count, $offset);
    }

    public function exportadorSIAGER(){     
     $sql = "call SIAGER_exportador_Retenciones()";
     throw new Rad_Db_Table_Exception('No se puede generar el reporte.'); 
     $reporte = $this->_db->fetchAll($sql);
     return $reporte;
    }
}
