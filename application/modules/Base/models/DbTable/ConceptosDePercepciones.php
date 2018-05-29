<?php
require_once('Rad/Db/Table.php');

class Base_Model_DbTable_ConceptosDePercepciones extends Rad_Db_Table
{
    protected $_name = "ConceptosImpositivos";
    //protected $_dependentTables = array("Model_DbTable_ConceptosImpositivos");
    
    // Inicio  protected $_referenceMap --------------------------------------------------------------------------
    protected $_referenceMap    = array(
        'CuentasActivos' => array(
            'columns'           => 'CuentaActivo',
            'refTableClass'     => 'Contable_Model_DbTable_PlanesDeCuentas',
            'refJoinColumns'    => array("Descripcion"),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist/fetch/ActivoPercepciones',
            'refTable'          => 'PlanesDeCuentas',
            'refColumns'        => 'Id',
            'comboPageSize'     => 20
        ),
        'CuentasPasivos' => array(
            'columns'           => 'CuentaPasivo',
            'refTableClass'     => 'Contable_Model_DbTable_PlanesDeCuentas',
            'refJoinColumns'    => array("Descripcion"),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist/fetch/PasivoPercepciones',
            'refTable'          => 'PlanesDeCuentas',
            'refColumns'        => 'Id',
            'comboPageSize'     => 20
        ),
        'TiposDeConceptos' => array(
            'columns'           => 'TipoDeConcepto',
            'refTableClass'     => 'Base_Model_DbTable_TiposDeConceptos',
            'refJoinColumns'    => array("Descripcion"),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'TiposDeConceptos',
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
         'TiposDeMontosMinimos'   => array(
            'columns'           => 'TipoDeMontoMinimo',
            'refTableClass'     => 'Base_Model_DbTable_TiposDeMontosMinimos',
            'refJoinColumns'    => array("Descripcion"),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'TiposDeMontosMinimos',
            'refColumns'        => 'Id'
        ),
        'Jurisdicciones' => array(
            'columns'           => 'Jurisdiccion',
            'refTableClass'     => 'Afip_Model_DbTable_AfipProvincias',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'AfipProvincias',
            'refColumns'        => 'Id',
            'comboPageSize'     => '10'
        )
    );
    // fin  protected $_referenceMap -----------------------------------------------------------------------------

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

    public function init()     {
        // Seteo los valores por defecto =====================
        $this-> _defaultSource = self::DEFAULT_CLASS;
        // Sugerimos un numero de factura
        
        $this-> _defaultValues = array (
            //'Divisa' => '1',
            //'ValorDivisa' => '0',
            'FechaAlta'     => date('Y-m-d'),
            //'FechaUltimoCambio' => date('Y-m-d')
            'FechaUltimoCambio' => date('Y-m-d'),
            'ParaVenta' => '0',
            'ParaCompra' => '1',
            'ParaCobro' => '0',
            'ParaPago' => '0',
            'ParaCalculoCosto' => '0',
            'EsRetencion' => '0',
            'EsPercepcion' => '1',
            'EsIva' => '0',
            'EsIvaDefault' => '0',
            'SeAplicaEmpresa' => '0',
            'EnUso' => '1',
            'MontoMinimo' => '0'
            
        );
        
        // ===================================================  
        parent::init();
    }
   

       
    public function fetchAll ($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = "EsPercepcion = 1";
        $where = $this->_addCondition($where, $condicion);
        return parent:: fetchAll ($where , $order , $count , $offset );
    }   
}