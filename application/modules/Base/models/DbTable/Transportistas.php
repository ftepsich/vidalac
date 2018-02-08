<?php
require_once('Rad/Db/Table.php');

class Base_Model_DbTable_Transportistas extends Base_Model_DbTable_Personas
{
    protected $_name = "Personas";
    Protected $_sort = array("RazonSocial asc");
	protected $_permanentValues = array('EsTransporte' => 1);
    // Inicio  protected $_referenceMap --------------------------------------------------------------------------
    protected $_referenceMap = array(
        'ModalidadesIVA' => array(
            'columns' => 'ModalidadIVA',
            'refTableClass' => 'Base_Model_DbTable_ModalidadesIVA',
            'refJoinColumns' => array("Descripcion"), 	// De esta relacion queremos traer estos campos por JOIN
            'comboBox' => true, 						
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'ModalidadesIVA',
            'refColumns' => 'Id'
        ),
        'TiposDeInscripcionesGanancias' => array(
            'columns' => 'ModalidadGanancia',
            'refTableClass' => 'Base_Model_DbTable_TiposDeInscripcionesGanancias',
            'refJoinColumns' => array("Descripcion"), 	// De esta relacion queremos traer estos campos por JOIN
            'comboBox' => true, 						
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'TiposDeInscripcionesGanancias',
            'refColumns' => 'Id'
        )
    );

}

?>