<?php
require_once('Rad/Db/Table.php');

class Base_Model_DbTable_ConceptosFacturacionServiciosPrestados extends Base_Model_DbTable_Articulos
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
        'EsFinal'               => '1',
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

}