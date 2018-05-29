<?php

require_once 'Rad/Db/Table.php';

/**
 * Base_Model_DbTable_CuentasBancariasPropias
 *
 * Cuentas Bancarias Propias
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Base
 * @class Base_Model_DbTable_CuentasBancarias
 * @extends Rad_Db_Table
 */
class Base_Model_DbTable_CuentasBancariasPropias extends Base_Model_DbTable_CuentasBancarias
{
    public function init ()
    {
        $config = Rad_Cfg::get();

        $idNuestraEmpresa = $config->Base->idNuestraEmpresa;

        $M_P = new Base_Model_DbTable_Personas;
        $R_P = $M_P->find($idNuestraEmpresa)->current();

        $this->_permanentValues = array(
            'Persona'       => $idNuestraEmpresa,
            'Propia'        => 1,
            'CuitTitular'   => $R_P->Cuit
        );

        parent::init();
    }

}