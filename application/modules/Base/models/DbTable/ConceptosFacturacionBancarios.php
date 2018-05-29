<?php
require_once 'Rad/Db/Table.php';

/**
 * Base_Model_DbTable_ConceptosFacturacionBancarios
 *
 * ConceptosBancarios
 *
 * @package     Aplicacion
 * @subpackage 	Base
 * @class       Base_Model_DbTable_ConceptosBancarios
 * @extends     Rad_Db_Table
 * @copyright   SmartSoftware Argentina 2010
 */
class Base_Model_DbTable_ConceptosFacturacionBancarios extends Base_Model_DbTable_Articulos
{

    protected $_name = 'Articulos';
    protected $_defaultSource = self::DEFAULT_CLASS;
    
    protected $_permanentValues = array ('Tipo' => '4');  

    protected $_defaultValues = array (
        'Tipo'                  => '4',
        'EsProducido'           => '0',
        'RequiereProtocolo'     => '0',
        'SeUtilizaParaFason'    => '0',
        'EsInsumo'              => '0',
        'EsParaCompra'          => '0',
        'EsParaFason'           => '0',
        'EsFinal'               => '1',
        'EsMateriaPrima'        => '0',
        'PesoNeto'              => '0',
        'PesoBruto'             => '0',
        'EsParaVenta'           => '0',
        'EnDesuso'              => '0',
        'RequiereLote'          => '0',
        'IVA'                   => '1',
        'TipoDeControlDeStock'  => '1'
    );    
    
    protected $_referenceMap = array(
        'TiposDeArticulos' => array(
            'columns' => 'Tipo',
            'refTableClass' => 'Base_Model_DbTable_TiposDeArticulos',
            'refJoinColumns' => array('Descripcion'),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist/fetch/Validos',
            'refTable' => 'TiposDeArticulos',
            'refColumns' => 'Id',
        ),
        'PlanesDeCuentas' => array(
            'columns' => 'Cuenta',
            'refTableClass' => 'Contable_Model_DbTable_PlanesDeCuentas',
            'refJoinColumns' => array('Descripcion'),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist/fetch/PlanCuentaImputable',
            'comboPageSize' => 20,
            'refTable' => 'PlanesDeCuentas',
            'refColumns' => 'Id',
            'comboPageSize' => 20
        ),
        'ArticulosGrupos' => array(
            'columns'           => 'ArticuloGrupo',
            'refTableClass'     => 'Base_Model_DbTable_ArticulosGrupos',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'ArticulosGrupos',
            'refColumns'        => 'Id',
            'comboPageSize'     => 10
        ),
        'ArticulosSubGrupos' => array(
            'columns'           => 'ArticuloSubGrupo',
            'refTableClass'     => 'Base_Model_DbTable_ArticulosSubGrupos',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'ArticulosSubGrupos',
            'refColumns'        => 'Id',
            'comboPageSize'     => 10
        ),
        'IVA' => array(
            'columns' => 'IVA',
            'refTableClass' => 'Base_Model_DbTable_ConceptosImpositivos',
            'refJoinColumns' => array("Descripcion"),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist/fetch/EsIVA',
            'refTable' => 'IVA',
            'refColumns' => 'Id'
        )
    );
    
    protected $_dependentTables = array(
        'Base_Model_DbTable_Articulos',
        'Facturacion_Model_DbTable_FacturasComprasArticulos'
    );

}