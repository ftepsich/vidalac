<?php
require_once('Rad/Db/Table.php');

class Base_Model_DbTable_ConceptosFacturacionServicios extends Base_Model_DbTable_Articulos
{
    protected $_name = "Articulos";
    protected $_sort = array ("Descripcion asc","Tipo asc");

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
            'comboSource' => 'datagateway/combolist/fetch/Perdidas',
            'comboPageSize' => 20,
            'refTable' => 'PlanesDeCuentas',
            'refColumns' => 'Id',
            'comboPageSize' => 20
        ),
        'IVA' => array(
            'columns' => 'IVA',
            'refTableClass' => 'Base_Model_DbTable_ConceptosImpositivos',
            'refJoinColumns' => array("Descripcion"),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist/fetch/EsIVA',
            'refTable' => 'IVA',
            'refColumns' => 'Id'
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
        'ArticulosGrupos' => array(
            'columns'           => 'ArticuloGrupo',
            'refTableClass'     => 'Base_Model_DbTable_ArticulosGrupos',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'ArticulosGrupos',
            'refColumns'        => 'Id',
            'comboPageSize'     => 10
        )
    );


    // Para poner un valor por defecto en un campo--------
    protected $_defaultSource = self::DEFAULT_CLASS;

    protected $_permanentValues = array ('Tipo' => '3');

    protected $_defaultValues = array (
        'Tipo'                  => '3',
        'EsProducido'           => '0',
        'RequiereProtocolo'     => '0',
        'SeUtilizaParaFason'    => '0',
        'EsInsumo'              => '0',
        'EsParaCompra'          => '1',
        'EsParaVenta'           => '0',
        'EsFinal'               => '0',
        'EsMateriaPrima'        => '0',
        'PesoNeto'              => '0',
        'PesoBruto'             => '0',
        'RequiereLote'          => '0',
        'IVA'                   => '1',
        'TipoDeControlDeStock'  => '3'

    );

    protected function _makeDescripcion ($data)
    {
        return $data;
    }

    public function fetchEsIVA($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = " EsRetencion = 0 and EsPercepcion = 0 and EsIVA = 1 and EnUso = 1 ";
        $order = " EsIVADefault desc";
        $where = $this->_addCondition($where, $condicion);
        return parent:: fetchAll($where, $order, $count, $offset);
    }
    // fin Public Init -------------------------------------------------------------------------------------------
}