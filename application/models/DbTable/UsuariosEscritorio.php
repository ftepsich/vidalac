<?php
require_once('Rad/Db/Table.php');
/**
 * Model_DbTable_UsuariosEscritorio
 *
 * @package     Aplicacion
 * @subpackage  Usuarios
 * @class       Model_DbTable_UsuariosEscritorio
 * @extends     Rad_Db_Table
 * @author      Martin A. Santangelo
 * @copyright   SmartSoftware Argentina 2012
 */
class Model_DbTable_UsuariosEscritorio extends Rad_Db_Table
{
    protected $_name = "UsuariosEscritorio";
    protected $_sort = array("Nombre Asc");

    public function init()
    {
        $this->_validators = array(
            // valido que no este agrenado dos veces el mismo menu al usuario
            'MenuPrincipal' => array(
                array(
                    'Db_NoRecordExists',
                    'UsuariosEscritorio',
                    'MenuPrincipal',
                    "Usuario = ".Zend_Auth::getInstance()->getIdentity()->Id
                ),
                'messages' => array('Ya tiene este Acceso directo en el escritorio.')
            )
        );
        parent::init();
    }

    // Inicio  protected $_referenceMap --------------------------------------------------------------------------
    protected $_referenceMap    = array(
        'Usuario' => array(
            'columns'           => 'Usuario',
            'refTableClass'     => 'Model_DbTable_Usuarios',
            'refJoinColumns'    => array("Nombre"),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'Usuarios',
            'refColumns'        => 'Id',
        ),
        'MenuPrincipal' => array(
            'columns'           => 'MenuPrincipal',
            'refTableClass'     => 'Model_DbTable_MenuesPrincipales',
            'refJoinColumns'    => array("Texto"), 
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'MenuesPrincipales',
            'refColumns'        => 'Id',
        )
    );
    // fin  protected $_referenceMap -----------------------------------------------------------------------------
    
    // TODO: Sobreescribir fetchAll para que cada usuario solo pueda ver su propia configuracion, o como se q se tenga q hacer
    public function fetchConfig($idUsuario)
    {
        $select = $this->_db->select()
                            ->from    ( array ( 'UC' => 'UsuariosEscritorio'),
                                        array ( 'UC.Usuario', 'MP.Id', 'MP.Texto', 'MP.TienePanel', 'MP.Icono', 'M.Nombre', 'Url' => new Zend_Db_Expr ("CONCAT(M.Modulo,'/',M.Controlador,IF(M.Accion!='','/',''),M.Accion,M.Parametros)"))
                                      )
                            ->joinLeft( array(  'MP' => 'MenuesPrincipales'),
                                        'MP.Id = UC.MenuPrincipal',
                                        array()
                                      )
                            ->joinLeft( array(  'M'  => 'Modulos'),
                                        'MP.Modulo = M.Id',
                                        array()
                                      )
                            ->where   ( 'MP.Activo = 1 And UC.Usuario = '.$idUsuario );
        

        $stmt = $this->_db->query($select);
        return $stmt->fetchAll();
    }
}