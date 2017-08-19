<?php
require_once 'Rad/Db/Table.php';

/**
 * Base_Model_DbTable_PersonasRegistrosDePreciosInformados
 *
 * Registra los ultimos Precios Informados de los Proveedores y tambien de los Clientes
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Base
 * @class Base_Model_DbTable_PersonasRegistrosDePreciosInformados
 * @extends Rad_Db_Table
 */
class Base_Model_DbTable_PersonasRegistrosDePreciosInformados extends Base_Model_DbTable_PersonasRegistrosDePrecios
{
    protected $_permanentValues = array(	'TipoDeRegistroDePrecio' => 3    );
	protected $_defaultValues 	= array(	'TipoDeRegistroDePrecio' => 3    );

}
