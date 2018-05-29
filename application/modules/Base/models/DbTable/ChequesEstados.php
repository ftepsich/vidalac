<?php

require_once('Rad/Db/Table.php');

class Base_Model_DbTable_ChequesEstados extends Rad_Db_Table
{

    protected $_name = "ChequesEstados";

    public function init()
    {

        $this->_validators = array(
            'Descripcion' => array(
                array('Db_NoRecordExists',
                    'ChequesEstados',
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

    public function fetchNormales($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = "Id In (2,3,4,5,6)";
        $where = $this->_addCondition($where, $condicion);
        return parent:: fetchAll($where, $order, $count, $offset);
    }

    public function fetchParaTerceros($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = "Id not in (1,3,7)";
        $where = $this->_addCondition($where, $condicion);
        return parent:: fetchAll($where, $order, $count, $offset);
    }
    
    public function fetchVenderDepositar($where = null, $order = null, $count = null, $offset = null)
    {
        
        // 4: Vendido, 6: Disponibles, 10: Depositado, 11: Retirado por socios
        $condicion = "Id In (4,6,10,11,12,13,14,15,16)";
        $where = $this->_addCondition($where, $condicion);
        return parent::fetchAll($where, $order, $count, $offset);
    }

    public function fetchRetiradoPorSocio($where = null, $order = null, $count = null, $offset = null)
    {
        
        // 4: Vendido, 6: Disponibles, 10: Depositado, 11: Retirado por socios
        $condicion = "Id In (11,12,13)";
        $where = $this->_addCondition($where, $condicion);
        return parent::fetchAll($where, $order, $count, $offset);
    }        

}