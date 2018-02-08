<?php
require_once 'Rad/Db/Table.php';

/**
 * Base_Model_DbTable_Telefonos
 *
 * Telefonos
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Base
 * @author Martin Alejandro Santangelo
 * @class Base_Model_DbTable_Telefonos
 * @extends Rad_Db_Table
 */
class Base_Model_DbTable_Telefonos extends Rad_Db_Table
{

    protected $_name = "Telefonos";
    
    /**
     * Validadores
     *
     * Tipo         -> no vacio
     * Numero       -> no vacio
     * Persona      -> no vacio
     *
     */
    protected $_validators = array(
        'TipoDeTelefono' => array(
            'NotEmpty',
            'allowEmpty'=>false,
            'messages' => array('Falta ingresar el tipo de telefono.')
        ),
        'Numero' => array(
            'NotEmpty',
            'allowEmpty'=>false,
            'messages' => array('Falta ingresar el numero de telefono.')
        ),        
        'Persona' => array(
            'NotEmpty',
            'allowEmpty'=>false,
            'messages' => array('No se asocio correctamente la direccion a la persona correspondiente.')
        )
    );    
    
    protected $_referenceMap = array(
        'Personas' => array(
            'columns' => 'Persona',
            'refTableClass' => 'Base_Model_DbTable_Personas',
            'refTable' => 'Personas',
            'refColumns' => 'Id'
        ),
        'Depositos' => array(
            'columns' => 'Deposito',
            'refTableClass' => 'Base_Model_DbTable_Depositos',
            'refTable' => 'Depositos',
            'refColumns' => 'Id'
        ),
        'TiposDeTelefonos' => array(
            'columns' => 'TipoDeTelefono',
            'refTableClass' => 'Base_Model_DbTable_TiposDeTelefonos',
            'refJoinColumns' => array("Descripcion"),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'TiposDeTelefonos',
            'refColumns' => 'Id'
        ),
    );

}