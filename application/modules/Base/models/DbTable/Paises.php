<?php
require_once 'Rad/Db/Table.php';

/**
 * Base_Model_DbTable_Paises
 *
 * Paises
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Base
 * @author Martin Alejandro Santangelo
 * @class Base_Model_DbTable_Paises
 * @extends Rad_Db_Table
 */
class Base_Model_DbTable_Paises extends Rad_Db_Table
{

    protected $_name = 'Paises';

    protected $_referenceMap = array(
        'AfipPaises' => array(
            'columns'           => 'Afip',
            'refTableClass'     => 'Afip_Model_DbTable_AfipPaises',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'AfipPaises',
            'refColumns'        => 'Id',
            'comboPageSize'     => '10'
        )
    );

    protected $_sort = array('Descripcion asc');

    protected $_dependentTables = array(
        'Base_Model_DbTable_Provincias'
    );

    public function init()
    {
        $this->_validators = array(
            'Descripcion' => array(
                array('Db_NoRecordExists',
                    'Paises',
                    'Descripcion',
                    array(
                        'field' => 'Id',
                        'value' => "{Id}"
                    )
                )
            ),
            'CodigoTel' => array(
                array('Db_NoRecordExists',
                    'Paises',
                    'CodigoTel',
                    array(
                        'field' => 'Id',
                        'value' => "{Id}"
                    )
                )
            ),
            'Nacionalidad' => array(
                array('Db_NoRecordExists',
                    'Paises',
                    'Nacionalidad',
                    array(
                        'field' => 'Id',
                        'value' => "{Id}"
                    )
                )
            )
        );
        parent::init();
    }

//    public function  update ($data, $where)
//    {
//        if (Rad_Confirm::confirm( 'De verdad queres updatear?', '123456', array('includeCancel' => true)) == 'yes') {
//            parent::update ($data, $where);
//        } else {
//            throw new Exception ('Respuesta no esperada');
//        }
//    }

}