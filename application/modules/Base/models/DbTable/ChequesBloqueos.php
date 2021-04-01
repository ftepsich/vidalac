<?php
require_once('Rad/Db/Table.php');

class Base_Model_DbTable_ChequesBloqueos extends Rad_Db_Table {

    protected $_name = "ChequesBloqueos";
    
    protected $_validators = array(
        'ChequeBoqueoTipo' => array(
            'NotEmpty',
            'allowEmpty'=>false,
            'messages' => array('Debe ingresar el tipo de bloque del cheque.')
        ),
        'FechaInicio' => array(
            'NotEmpty',
            'allowEmpty'=>false,
            'messages' => array('Debe ingresar la fecha de inicio del bloqueo del cheque.')
        ),
        'FechaFin' => array(
            'NotEmpty',
            'allowEmpty'=>false,
            'messages' => array('Debe ingresar la fecha de fin del bloqueo del cheque.')
        )
    );
    
    
    protected $_referenceMap = array(
        'ChequesBloqueosTipos' => array(
            'columns' => 'ChequeBloqueoTipo',
            'refTableClass' => 'Base_Model_DbTable_ChequesBloqueosTipos',
            'refJoinColumns' => array("Descripcion"), // De esta relacion queremos traer estos campos por JOIN
            'comboBox' => true, 
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'ChequesBloqueosTipos',
            'refColumns' => 'Id')
    );

}
?>