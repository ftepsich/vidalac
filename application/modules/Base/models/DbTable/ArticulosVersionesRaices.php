<?php
require_once('Rad/Db/Table.php');
/**
 * Base_Model_DbTable_ArticulosVersionesRaices
 *
 * @package     Aplicacion
 * @subpackage  Desktop
 * @class       Base_Model_DbTable_ArticulosVersionesRaices
 * @extends     Rad_Db_Table
 * @author      Martin A. Santangelo
 * @copyright   SmartSoftware Argentina 2012
 */
class Base_Model_DbTable_ArticulosVersionesRaices extends Rad_Db_Table
{
    // Tabla mapeada
    protected $_name = "ArticulosVersionesRaices";

    // Relaciones
    protected $_referenceMap    = array(
        
            'ArticulosVersiones' => array(
            'columns'           => 'ArticuloVersionRaiz',
            'refTableClass'     => 'Base_Model_DbTable_ArticulosVersiones',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'ArticulosVersiones',
            'refColumns'        => 'Id',
        ),
            'ArticulosVersionesDetalles' => array(
            'columns'           => 'ArticuloVersionDetalle',
            'refTableClass'     => 'Base_Model_DbTable_ArticulosVersionesDetalles',
            'refJoinColumns'    => array('ArticuloVersionHijo'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'ArticulosVersionesDetalles',
            'refColumns'        => 'Id',
        )
    );

    /**
     * Validadores
     *
     * ArticuloVersionDetalle       -> valor unico conjuntamente con ArticuloVersionRaiz
     *
     */
    protected $_validators = array(
        'ArticuloVersionDetalle' => array(
            'NotEmpty',
            'allowEmpty'=>false,
            array(
                'Db_NoRecordExists',
                'ArticulosVersionesRaices',
                'ArticuloVersionRaiz',
                'ArticuloVersionDetalle = {ArticuloVersionRaiz} AND  Id <> {Id}'
            ),
            'messages' => array(
                'La Relacion que intenta realizar ya existe'
            )
        )
    );    
    
    protected $_dependentTables = array();  
    
}