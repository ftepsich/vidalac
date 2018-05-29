<?php

/**
 * Rad_Mapper
 * 
 * Mapeador de modelos de logica para Ext.Direct
 *
 * @class Rad_Mapper
 * @package Rad
 * @author Martin A. Santangelo
 */
class Rad_Mapper
{
    /**
     * Clase que se va a instanciar para mapear
     * Sobreescribir al heredar
     */
    protected $_class = null;
    
    /**
     * Modelo instanciado
     */
    protected $_model = null;
    
    /**
     * Instancia el modelo definido en $_class
     */
    public function __construct ()
    {
        if ($this->_class) {
            try {
                $this->_model = new $this->_class(array(), true);
            } catch (Exception $e) {
                throw new Exception('No existe el modelo '.$this->_model);
            }
        }
    }
    
    /*
     * Devuelve un registro del modelo
     */
    public function get ($id)
    {
        if ($this->_model) {
            $row = $this->_model->find($id);
            if (count($row)) {
                return $row->current()->toArray();
            }
        } else {
            throw new Exception('No se creo el modelo para este mapper');
        }
    }
    
}
