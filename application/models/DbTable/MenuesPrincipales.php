<?php
require_once 'Rad/Db/Table.php';

/**
 * Model_DbTable_MenuesPrincipales
 *
 * @package     Aplicacion
 * @subpackage  Desktop
 * @class       Model_DbTable_MenuesPrincipales
 * @extends     Rad_Db_Table
 * @author      Martin A. Santangelo
 * @copyright   SmartSoftware Argentina 2012
 */
class Model_DbTable_MenuesPrincipales extends Rad_Db_Table
{
    protected $_name = "MenuesPrincipales";
    protected $_sort = array('Texto asc');
    protected $_referenceMap = array(
        'Modulos' => array(
            'columns'        => 'Modulo',
            'refTableClass'  => 'Model_DbTable_Modulos',
            'refJoinColumns' => array('Nombre'),
            'comboBox'       => true,
            'comboSource'    => 'datagateway/combolist',
            'refTable'       => 'Modulos',
            'refColumns'     => 'Id',
            'comboPageSize'  => 10
        ),
        'MenuesPrincipales1' => array(
            'columns'        => 'MenuPrincipal',
            'refTableClass'  => 'Model_DbTable_MenuesPrincipales',
            'refJoinColumns' => array('Texto'),
            'comboBox'       => true,
            'comboSource'    => 'datagateway/combolist',
            'refTable'       => 'MenuesPrincipales',
            'refColumns'     => 'Id',
            'comboPageSize'  => 10
        )
    );

    public function getStartMenu ($parent = null)
    {
        return $this->fetchFullMenu($parent);
    }

    /**
     * Devuelve un rowset con TODOS los elementos del menu
     * OJO: mantener este metodo como privado
     */
    private function fetchFullMenu ($parent)
    {
        $where = 'WHERE MP.Activo = 1 '. (($parent) ? 'AND MenuPrincipal = '.$parent : '');

        $sql = "SELECT  MP.Id menuId,
                        MP.Modulo, 
                        MP.MenuPrincipal, 
                        MP.Texto,
                        MP.Orden, 
                        MP.Activo, 
                        MP.Icono, 
                        M.Id,
                        M.Nombre,
                        M.Titulo,
                        
                        M.Controlador,

                        M.Accion,
                        M.Parametros,
                        M.Modulo,
                        
                        CONCAT(M.Modulo, '/', M.Controlador, IF(M.Accion != '', '/', ''), M.Accion, M.Parametros) AS Url,
                        MP.TienePanel
                        /*    
                        -- PK: Para que no arme panel cuando no tiene hijos --> Tambien lo hace en el que lo ensambla ??
                        CASE
                            WHEN ((SELECT COUNT(*) FROM MenuesPrincipales WHERE MenuPrincipal = MP.Id) > 0) THEN 1
                            ELSE 0
                        END AS TienePanel
                        */
                FROM MenuesPrincipales MP
                LEFT JOIN Modulos M on MP.Modulo = M.Id
                $where
                ORDER BY MP.TienePanel desc, MP.Texto";

        // Rad_Log::debug($sql);

        $stmt = $this->_db->query($sql);
        $rows = $stmt->fetchAll();

        // Rad_Log::debug($rows);

        return $rows;
    }
}