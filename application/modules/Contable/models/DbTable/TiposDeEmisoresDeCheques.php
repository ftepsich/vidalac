<?php
require_once('Rad/Db/Table.php');

class Contable_Model_DbTable_TiposDeEmisoresDeCheques extends Rad_Db_Table
{

    protected $_name = "TiposDeEmisoresDeCheques";

    // Inicio Public Init ----------------------------------------------------------------------------------------
    public function init()
    {
        $this->_validators = array(
            'Descripcion' => array(
                array('Db_NoRecordExists',
                    'TiposDeEmisoresDeCheques',
                    'Descripcion',
                    array(
                        'field' => 'Id',
                        'value' => "{Id}"
                    )
                )
            )
        );

        parent::init();
    }

    // fin Public Init -------------------------------------------------------------------------------------------	

    public function fetchNoPropios($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = "Id <> 1";
        $where = $this->_addCondition($where, $condicion);
        return parent:: fetchAll($where, $order, $count, $offset);
    }

}
