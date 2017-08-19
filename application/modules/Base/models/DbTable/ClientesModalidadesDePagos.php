<?php
require_once 'Rad/Db/Table.php';

/**
 * Base_Model_DbTable_ClientesModalidadesDePagos
 *
 * Conceptos Impositivos
 *
 * @package     Aplicacion
 * @subpackage 	Base
 * @class       Base_Model_DbTable_ClientesModalidadesDePagos
 * @extends     Rad_Db_Table
 * @copyright   SmartSoftware Argentina 2010
 */

class Base_Model_DbTable_ClientesModalidadesDePagos extends Rad_Db_Table
{
    protected $_name = "PersonasModalidadesDePagos";
    
    
    /**
     * Validadores
     *
     * Descripcion  -> no vacio
     *
     */
    protected $_validators = array(
        'Descripcion' => array(
            'NotEmpty',
            'allowEmpty'=>false,
            'messages' => array('Falta ingresar la modalidad de pago.')
        )
    );
    
    protected $_referenceMap = array(
        'ModalidadesDePagos' => array(
            'columns'           => 'ModalidadDePago',
            'refTableClass'     => 'Base_Model_DbTable_ModalidadesDePagos',
            'refColumns'        => 'Id',
            'refJoinColumns'    => array("Descripcion"),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'ModalidadesDePagos'
        ),
        'Personas' => array(
            'columns'           => 'Persona',
            'refTableClass'     => 'Base_Model_DbTable_Clientes',
            'refJoinColumns'    => array("RazonSocial"),
            //'comboBox'          => true,
            //'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'Personas',
            'refColumns'        => 'Id',
        )
    );

}