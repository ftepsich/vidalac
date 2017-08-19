<?php

class Model_UsuariosEscritorioMapper extends Rad_Mapper
{
    protected $_class = 'Model_DbTable_UsuariosEscritorio';

    // Autenticacion hardcodeada
    public function authorize($identity)
    {
        return true;
    }

    public function addShortcut($id) {

        $db = Zend_Registry::get('db');

        $id = $db->quote($id, 'INTEGER');

        $usuario = Zend_Auth::getInstance()->getIdentity()->Id;

        if (!$usuario) {
            throw new Rad_Exception('No se encuentra logueado');
        }

        $this->_model->insert(array('Usuario' => $usuario, 'MenuPrincipal' => $id));
    }

    public function removeShortcut($id) {

        $db = Zend_Registry::get('db');

        $id = $db->quote($id, 'INTEGER');

        $usuario = Zend_Auth::getInstance()->getIdentity()->Id;

        if (!$usuario) {
            throw new Rad_Exception('No se encuentra logueado');
        }

        $this->_model->delete('Usuario = '. $usuario . ' and MenuPrincipal = '. $id);
    }
}