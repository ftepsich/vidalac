<?php
require_once 'Rad/Db/Table.php';

/**
 * Base_Model_DbTable_Provincias
 *
 * Provincias
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Base
 * @class Base_Model_DbTable_Provincias
 * @extends Rad_Db_Table
 */
class Base_Model_DbTable_Provincias extends Rad_Db_Table
{
    protected $_readOnlyFields  = array(
        'AFIP'
    );

    protected $_name = 'Provincias';

    protected $_sort = array ('Descripcion ASC');    
    
    protected $_validators = array(
        'Descripcion' => array(
            array(
                'Db_NoRecordExists',
                'Provincias',
                'Descripcion',
                array(
                    'field' => 'Id',
                    'value' => "{Id}"
                )
            ),
            'messages' => 'Ya existe una provincia con este nombre'
        )
    );
    
    protected $_referenceMap = array(
        'Paises' => array(
            'columns'           => 'Pais',
            'refTableClass'     => 'Base_Model_DbTable_Paises',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'Paises',
            'refColumns'        => 'Id',
            'comboPageSize'     => '10'
        ),
        'AfipProvincias' => array(
            'columns'           => 'Afip',
            'refTableClass'     => 'Afip_Model_DbTable_AfipProvincias',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'AfipProvincias',
            'refColumns'        => 'Id',
            'comboPageSize'     => '10'
        )
    );

    protected $_dependentTables = array('Base_Model_DbTable_Localidades');


    public function getNombre($id) 
    {
        $R_P  = $this->find($id)->current();
        return $R_P->Descripcion;
    }


}