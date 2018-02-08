<?php
require_once 'Zend/Acl.php';

/**
 * Rad_Acl
 *
 * Esta clase implementa la lista de control de accesso a los controladores del sistema
 * @copyright SmartSoftware Argentina
 * @package Rad
 * @subpackage Acl
 * @author Martin Alejandro Santangelo
 */
class Rad_Acl extends Zend_Acl
{
    const cacheNameSpace = 'Rad_Acl';

    protected static $_cache;
    protected static $_instance;

    public static function setCache($cache)
    {
        self::$_cache = $cache;
    }

    public static function getInstance($db)
    {
        // si tiene cache tomamos de este
        if (self::$_cache) {
            self::$_instance = self::$_cache->load(self::cacheNameSpace);
        }

        if (!self::$_instance) {

            self::$_instance = new Rad_Acl($db);

            if (self::$_cache) {
                self::$_cache->save(self::$_instance, self::cacheNameSpace, array("Acl"));
            }
        }

        return self::$_instance;
    }
    /**
     * 	Constructor de la clase
     *
     * 	@param Zend_Db_Adapter_Abstract $db
     */
    public function __construct($db)
    {
        $front = Zend_Controller_Front::getInstance();

        $select = $db   ->select()
                        ->from( 'RolesModulos', array('Privilegio') )
                        ->joinLeft(
                                'GruposDeUsuariosRoles',
                                'GruposDeUsuariosRoles.Rol = RolesModulos.Rol',
                                    array('Grupo' => 'GrupoDeUsuario')
                        )
                        ->joinLeft( 'Modulos', 'Modulos.Id = RolesModulos.Modulo',
                                    array('Recurso' => 'Controlador','Modulo' => 'Modulo') )
                        ->where("GrupoDeUsuario is not null");
        //Rad_Log::debug($select."aaa");
        /*
        $sql = 'SELECT R.Descripcion as Rol, M.Controlador as Recurso , RM.Privilegio
                FROM RolesModulos RM
                left join Roles R on R.Id = RM.Rol
                left join Modulos M on M.Id = RM.Modulo';
         */
        $resources = $db->fetchAll($select);

        $select = $db   ->select()
                        ->from('GruposDeUsuarios', array('Id', 'Descripcion'));
        /*
        $sql = 'SELECT Roles.Id, Roles.Descripcion, Roles.Rol AS Padre
                FROM Roles
                LEFT JOIN Roles AS hereda ON hereda.Id = Roles.Rol
                ORDER BY Roles.Rol ASC';
        */

        $grupos = $db->fetchAll($select);

        //Loop roles and put them in an assoc array by ID
//        $roleArray = array();

        foreach ($grupos as $r) {
            $grupo = new Zend_Acl_Role($r['Id']);
            //If inherit_name isn't null, have the role
            //inherit from that, otherwise no inheriti
            $this->addRole($grupo);
            $grupoArray[$r['Id']] = $grupo;
        }

        foreach ($resources as $r) {
            //Rad_Log::debug($r);
            $recurso = strtolower(($r['Modulo'])?$r['Modulo'].'/'.$r['Recurso']:$r['Recurso']);
            if (!in_array($r['Recurso'], $restemp)) {
                $resource = new Zend_Acl_Resource($recurso);
                $restemp[] = $r['Recurso'];
                $this->add($resource);

            }
            $grupo = $grupoArray[$r['Grupo']];
            if ($r['Privilegio']) {
                $this->allow($grupo, $recurso, $r['Privilegio']);
            } else {
                $this->allow($grupo, $recurso);
            }
        }
    }

    public function isAllowed($role = null, $resource = null, $privilege = null)
    {
        return ($role == 1) ? true : parent::isAllowed($role, strtolower($resource), $privilege);
    }

}
