<?php
require_once('Rad/Db/Table.php');
/**
 * Base_Model_DbTable_TiposDeDivisas
 *
 * Tipos De Divisas
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Base
 * @class Base_Model_DbTable_TiposDeDivisas
 * @extends Rad_Db_Table
 */
class Base_Model_DbTable_TiposDeRelacionesArticulos extends Rad_Db_Table
{
	protected $_name = "TiposDeRelacionesArticulos";

	public function init()     {
			$this -> _validators = array(
				'Descripcion'=> array(	
                    array(	'Db_NoRecordExists',
                            'TiposDeRelacionesArticulos',
                            'Descripcion',
                            array(
                                'field' => 'Id',
                                'value' => "{Id}"
                            )
                    )
                )
			);
			
		parent::init();
	}

    protected $_dependentTables = array(
        'Base_Model_DbTable_ArticulosVersionesDetalles',
    );

    public function fetchNoFormula($where = null, $order = null, $count = null, $offset = null)
    {

        $where = $this->_addCondition($where, "Id <> 1");
        
        return parent:: fetchAll($where, $order, $count, $offset);
    }    

}