<?php
require_once('Rad/Db/Table.php');

class Base_Model_DbTable_EntesRecaudadores extends Rad_Db_Table {

    protected $_name = "EntesRecaudadores";
    // Inicio  protected $_referenceMap --------------------------------------------------------------------------
    protected $_referenceMap = array(
        'Ambitos' => array(
            'columns' => 'Ambito',
            'refTableClass' => 'Base_Model_DbTable_Ambitos',
            'refJoinColumns' => array("Descripcion"), // De esta relacion queremos traer estos campos por JOIN
            'comboBox' => true, // Armar un combo con esta relacion - Algo mas queres haragan programa algo :P -
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'Ambitos',
            'refColumns' => 'Id'
        )
    );
    // fin  protected $_referenceMap -----------------------------------------------------------------------------

    // Inicio Public Init ----------------------------------------------------------------------------------------
    public function init() {
        $this->_validators = array(
            'Descripcion' => array(
                array('Db_NoRecordExists',
                    'EntesRecaudadores',
                    'Descripcion',
                    array(
                        'field' => 'Id',
                        'value' => ($_POST["Id"]) ? $_POST["Id"] : ($_POST["node"] ? $_POST["node"] : 0)
                    )
                )
            ),
            'Ambito' => array(
                'allowEmpty' => false
            )
        );
        parent::init();
    }

    // fin Public Init -------------------------------------------------------------------------------------------
    

}
?>