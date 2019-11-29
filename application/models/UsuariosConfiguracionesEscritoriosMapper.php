<?php

class Model_UsuariosConfiguracionesEscritoriosMapper extends Rad_Mapper
{
    protected $_class = 'Model_DbTable_UsuariosConfiguracionesEscritorios';

    // Autenticacion hardcodeada
    public function authorize($identity)
    {
        return true;
    }

    public function guardarColor($color) {

        $db = Zend_Registry::get('db');

        $usuario = Zend_Auth::getInstance()->getIdentity()->Id;

        if (!$usuario) {
            throw new Rad_Exception('No se encuentra logueado');
        }

        $row = $this->_model->fetchAll('Usuario = '.$usuario)->current();

        if (!$row) {
            $row = $this->_model->createRow();
            $row->ImagenFondo         = '';
            $row->Usuario             = $usuario;
            $row->ImagenFondoPosicion = 'center';
        }

        if(!preg_match('/^[a-fA-F0-9]{6}$/i', $color)) {
            throw new Rad_Exception('El color tiene un formato erroneo');
        }
        $row->setReadOnly(false); 
        $row->ColorFondo = $color;
        $row->save();
    }

    public function guardarFondo($fondo) {

        $db = Zend_Registry::get('db');

        $fondo = $db->quote($fondo);

        $usuario = Zend_Auth::getInstance()->getIdentity()->Id;

        if (!$usuario) {
            throw new Rad_Exception('No se encuentra logueado');
        }

        $row = $this->_model->fetchAll('Usuario = '.$usuario)->current();

        if (!$row) {
            $row = $this->_model->createRow();
            $row->ColorFondo          = '000000';
            $row->Usuario             = $usuario;
            $row->ImagenFondoPosicion = 'center';
        }

        $row->setReadOnly(false); 
        $row->ImagenFondo = $fondo;
        $row->save();
    }

    public function quitarFondo() {

        $usuario = Zend_Auth::getInstance()->getIdentity()->Id;

        if (!$usuario) {
            throw new Rad_Exception('No se encuentra logueado');
        }

        $row = $this->_model->fetchAll('Usuario = '.$usuario)->current();

        if (!$row) {
            $row = $this->_model->createRow();
            $row->ColorFondo          = '000000';
            $row->Usuario             = $usuario;
            $row->ImagenFondoPosicion = 'center';
        }

        $row->setReadOnly(false); 
        $row->ImagenFondo = null;
        $row->save();
    }

    public function guardarFondoPosicion($posicion) {

        $db = Zend_Registry::get('db');

        if (!in_array($posicion, array('center','tile'))) {
            throw new Rad_Exception('Formato erroneo en la posicion');
        }

        $usuario = Zend_Auth::getInstance()->getIdentity()->Id;

        if (!$usuario) {
            throw new Rad_Exception('No se encuentra logueado');
        }

        $row = $this->_model->fetchAll('Usuario = '.$usuario)->current();

        if (!$row) {
            $row = $this->_model->createRow();
            $row->ColorFondo          = '000000';
            $row->Usuario             = $usuario;
        }

        $row->setReadOnly(false); 
        $row->ImagenFondoPosicion = $posicion;
        $row->save();
    }
}