<?php
/**
 * @package     Aplicacion
 * @subpackage  Afip
 * @class       Afip_Model_DbTable_AfipCuitPaises * @extends     Rad_Db_Table
 * @copyright   SmartSoftware Argentina
 */
class Afip_Model_DbTable_AfipCuitPaises extends Rad_Db_Table
{
    protected $_name = 'AfipCuitPaises';

    protected $_sort = array('Descripcion asc');    

    protected $_referenceMap = array(
        
    'AfipTiposDeSujetos' => array(
        'columns'           => 'TipoDeSujeto',
        'refTableClass'     => 'Afip_Model_DbTable_AfipTiposDeSujetos',
        'refJoinColumns'    => array('Descripcion'),
        'comboBox'          => true,
        'comboSource'       => 'datagateway/combolist',
        'refTable'          => 'AfipTiposDeSujetos',
        'refColumns'        => 'Id',
        'comboPageSize'     => 20
    )
    );

    protected $_dependentTables = array('Base_Model_DbTable_PaisesCuit');
}