<?php

/**
 * Base_Model_DbTable_Emails
 *
 * Direcciones
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Base
 * @author Martin Alejandro Santangelo
 * @class Base_Model_DbTable_Emails
 * @extends Base_Model_DbTable_Emails
 */
class Base_Model_DbTable_Emails extends Rad_Db_Table
{

    // Tabla mapeada
    protected $_name = 'Emails';
    
    /**
     * Validadores
     *
     * Email        -> no vacio y formato correcto
     * Persona      -> no vacio
     *
     */
    protected $_validators = array(
        'Email' => array(
            'NotEmpty',
            'allowEmpty'=>false,
            'EmailAddress',
            'messages' => array(
                'Falta ingresar el Email.',
                'El formato de email es incorrecto'
            )
        ),
        'Persona' => array(
            'NotEmpty',
            'allowEmpty'=>false,
            'messages' => array('No se asocio correctamente la direccion a la persona correspondiente.')
        )
    );    
    
    
    // Relaciones
    protected $_referenceMap = array(
        'Personas' => array(
            'columns' => 'Persona',
            'refTableClass' => 'Base_Model_DbTable_Personas',
            'refJoinColumns' => array('RazonSocial'),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'Personas',
            'refColumns' => 'Id'
        )
    );

}