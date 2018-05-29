<?php
/**
 * Tablas semi referenciales (contienen datos default pero los usuarios pueden agregar otros)
 *
 * @package Rad
 * @subpackage Db_Table
 * @author Martin A. Santangelo
 */
class Rad_Db_Table_SemiReferencial extends Rad_Db_Table
{
    /**
     * Todos los Ids menores o iguales no pueden ser modificados
     */
    protected $_idLimitados  = 1;

    protected $_mensajeError = 'Usted no tiene permiso para modificar este registro';

    /**
     *  Verifica que no se este intentando realizar una operacion sobre el registro uno
     */
    protected function _checkReadOnly($where)
    {

        $where = $this->_addCondition($where, "{$this->_name}.Id <= {$this->_idLimitados}");

        $resultset = $this->fetchAll($where);

        if (count($resultset)) {
           throw new Rad_Db_Table_Exception($this->_mensajeError);
        }
    }

    public function update($data, $where)
    {
        $this->_checkReadOnly($where);
        parent::update($data, $where);
    }

    public function delete($where)
    {
        $this->_checkReadOnly($where);
        parent::delete($where);
    }
}
