<?php

/**
 * Clase dedicada a Validar de archivos anexados a campos de una tabla
 * 
 * @package Rad
 * @subpackage Db_Table
 * @author Martin A. Santangelo
 */
class Rad_Db_Table_AttachedFileValidate
{
    /**
     * Modelo
     * @var Rad_Db_Table
     */
    protected $_model;

    /**
     * Constructor de la clase
     * @param $model Rad_Db_table Instancia del modelo
     */
    public function __construct($model)
    {
        $this->_model = $model;
    }

    /**
     * Valida un archivo para ser anexado al campo $field
     * @param $field string Campo
     * @param $file  string Archivo
     */
    public function isValid($field, $file) {
        $modelAttached = $this->_model->getAttachedFiles();

        if (!array_key_exists($field, $modelAttached)) {
            throw new Rad_Db_Table_AttachedFileValidate_Exception("El campo $field no tiene configurado soporte para anexar archivos en el modelo ".get_class($this->_model));
        }

        $validadores = $modelAttached[$field]['validators'];

        if ($validadores != null && !is_array($validadores)) {
            throw new Rad_Db_Table_AttachedFileValidate_Exception("Los validadores del archivo anexo para el campo $field deben ser un arrat en el modelo ".get_class($this->_model));          
        }

        $msg = array();

        foreach ($validadores as $key => $cfg) {
            $vClass = 'Zend_Validate_File_'.$cfg[0];

            $validador = new $vClass(@$cfg[1]);

            if (!$validador->isValid($file)) {
                $msg += $validador->getMessages();
            }
        }

        if (!empty($msg)) {
            throw new Rad_Db_Table_Exception("Error al actualizar registro", array($field => $msg));
        }
    }
}