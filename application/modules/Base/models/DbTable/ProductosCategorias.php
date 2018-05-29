<?php
require_once('Rad/Db/Table.php');
/**
 * Base_Model_DbTable_ProductosCategorias
 *
 * Productos categorioas
 *
 *
 * @package 	Aplicacion
 * @subpackage 	Base
 * @class 		Laboratorio_Model_DbTable_FormulasProductos
 * @extends		Rad_Db_Table
 * @copyright   SmartSoftware Argentina 2010
 */
class Base_Model_DbTable_ProductosCategorias extends Rad_Db_Table_SemiReferencial
{
	// Tabla mapeada
	protected $_name = "ProductosCategorias";
	
	protected $_dependentTables = array(
        'Base_Model_DbTable_Productos',
        'Base_Model_DbTable_ProductosCategoriasCaracteristicas',
        'Base_Model_DbTable_ProductosSubCategorias'
    );
	
	public function init()     {
			$this -> _validators = array(
				'Descripcion'=> array(	
                    array(	'Db_NoRecordExists',
                            'ProductosCategorias',
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
                            'ProductosCategorias',
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
	
    /**
     * sale si la categoria es packaging
     *
     * @param int $idCategoria identificador del comprobante a verificar
     *
     */
    public function salirSi_esPackaging($idCategoria)
    {
        if ($this->esPackaging($idCategoria)) {
            throw new Rad_Db_Table_Exception("La Categoria es Packaging y no puede modificarse ni eliminarse.");
        }
        return $this;
    }

    /**
     * Comprueba si la categoria es packaging
     *
     * @param int $idCategoria 	identificador de la categoria
     * 
     * @return boolean
     */
    public function esPackaging($idCategoria)
    {
        $R_PC = $this->find($idCategoria)->current();

        if ($R_PC->Id == 1) {
            return true;
        } else {
            return false;
        }
    }	
	
}