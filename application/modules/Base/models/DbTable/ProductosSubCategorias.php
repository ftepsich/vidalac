<?php
require_once('Rad/Db/Table.php');
/**
 * Base_Model_DbTable_ProductosSubCategorias
 *
 * Productos categorioas
 *
 *
 * @package 	Aplicacion
 * @subpackage 	Base
 * @class 		Base_Model_DbTable_ProductosSubCategorias
 * @extends		Rad_Db_Table
 * @copyright   SmartSoftware Argentina 2010
 */
class Base_Model_DbTable_ProductosSubCategorias extends Rad_Db_Table
{
	// Tabla mapeada
	protected $_name = "ProductosSubCategorias";

	// Relaciones
    protected $_referenceMap    = array(
        
	        'ProductosCategorias' => array(
            'columns'           => 'ProductoCategoria',
            'refTableClass'     => 'Base_Model_DbTable_ProductosCategorias',
     		'refJoinColumns'    => array('Descripcion'),
     		'comboBox'			=> true,
     		'comboSource'		=> 'datagateway/combolist',
            'refTable'			=> 'ProductosCategorias',
            'refColumns'        => 'Id',
        )   
	);

	public function init()     {
			$this -> _validators = array(
				'Descripcion'=> array(	
                    array(	'Db_NoRecordExists',
                            'ProductosSubCategorias',
                            'Descripcion',
                            array(
                                'field' => 'Id',
                                'value' => "{Id}"
                            )
                    ),
					'messages' => array('El valor existe - Ingrese un valor que no este utilizado')
                ),
				'DescripcionR'=> array(	
                    array(	'Db_NoRecordExists',
                            'ProductosSubCategorias',
                            'DescripcionR',
                            array(
                                'field' => 'Id',
                                'value' => "{Id}"
                            )
                    ),
					'messages' => array('El valor reducido existe - Ingrese un valor que no este utilizado')
                )				
			);
			
		parent::init();
	}
	
	protected $_dependentTables = array('Base_Model_DbTable_Productos');
	
}