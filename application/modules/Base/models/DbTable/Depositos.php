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
class Base_Model_DbTable_Depositos extends Base_Model_DbTable_Direcciones
{

  
    
    /**
     * Validadores
     *
     * Numero       -> valor unico
     * ValorDivisa  -> no negativo
     * Punto        -> no vacio
     * FechaEmision -> no vacia
     * Letra        -> no vacio, valor valido
     *
     */
    protected $_validators = array(
        'Persona' => array(
            'NotEmpty',
            'allowEmpty'=>false,
            'messages' => array('No se asocio correctamente la direccion a la persona correspondiente.')
        ),
        'Direccion' => array(
            'allowEmpty'=>false
        ),
        'Localidad' => array(
            'allowEmpty'=>false
        ),
        'TiposDeDirecciones' => array(
            'allowEmpty'=>false
        )
    );
    
//    public function init(){
//        parent::init();
//        // $config = Rad_Cfg::get();
//        // $this->_permanentValues = array(
//            // 'Persona' => $config->Base->idNuestraEmpresa
//        // );
//    }


    public function fetchPropio($where = null, $order = null, $count = null, $offset = null)
    {
        $idNuestraEmpresa = Rad_Cfg::get()->Base->idNuestraEmpresa;
        $condicion = ($idNuestraEmpresa) ? " Persona in ($idNuestraEmpresa) " : " 1 = 2 ";
        $where = $this->_addCondition($where, $condicion);
        return parent:: fetchAll($where, $order, $count, $offset);
    }

}