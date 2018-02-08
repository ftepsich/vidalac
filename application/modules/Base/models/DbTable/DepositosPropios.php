<?php
require_once 'Rad/Db/Table.php';

/**
 * Base_Model_DbTable_Depositos
 *
 * Depositos
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Base
 * @author Martin Alejandro Santangelo
 * @class Base_Model_DbTable_Depositos
 * @extends Rad_Db_Table
 */
class Base_Model_DbTable_DepositosPropios extends Base_Model_DbTable_Depositos
{

    // Relaciones
    
    protected $_referenceMap = array(
        'TipoDeDireccion' => array(
            'columns' => 'TipoDeDireccion',
            'refTableClass' => 'Base_Model_DbTable_TiposDeDirecciones',
            'refJoinColumns' => array('Descripcion'),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'TiposDeDirecciones',
            'refColumns' => 'Id',
        ),
        'Localidades' => array(
            'columns' => 'Localidad',
            'refTableClass' => 'Base_Model_DbTable_Localidades',
            'refJoinColumns' => array('Descripcion','CodigoPostal'),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'Localidades',
            'refColumns' => 'Id',
            'comboPageSize' => 20
        ) 
    );
	
    public function init(){
        parent::init();
        $config = Rad_Cfg::get();
        $this->_permanentValues = array(
           'Persona' => $config->Base->idNuestraEmpresa,
           'TipoDeDireccion' => 2
        );
    }
}