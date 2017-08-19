<?php

require_once('Rad/Db/Table.php');

class Base_Model_DbTable_Bancos extends Rad_Db_Table
{

    protected $_name = "Bancos";
    Protected $_sort = array("Descripcion asc");

    protected $_referenceMap = array(
        'Personas' => array(
            'columns' => 'Persona',
            'refTableClass' => 'Base_Model_DbTable_Proveedores',
            'refJoinColumns' => array('RazonSocial'),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'Personas',
            'refColumns' => 'Id',
            'comboPageSize' => 20
        )
    );

    protected $_dependentTables = array(    'Liquidacion_Model_DbTable_Liquidaciones'
                                        );




    // Inicio Public Init ----------------------------------------------------------------------------------------
    public function init()
    {
        $this->_validators = array(
            'Prioridad' => array(
                array(
                    'Db_NoRecordExists',
                    'Bancos',
                    'Prioridad',
                    array(
                        'field' => 'Id',
                        'value' => "{Id}"
                    )
                ),
                'messages' => array('El valor existe - Ingrese un valor que no este utilizado por otro Banco')
            ),
            'Descripcion' => array(
                array(
                    'Db_NoRecordExists',
                    'Bancos',
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

    /**
     *
     * @param <type> $where
     * @param <type> $order
     * @param <type> $count
     * @param <type> $offset
     * @return <type>
     */
    public function fetchUtilizados($where = null, $order = null, $count = null, $offset = null)
    {
        $where = $this->_addCondition($where, "Bancos.Utilizado = 1");
        return parent::fetchAll($where, $order, $count, $offset);
    }

}