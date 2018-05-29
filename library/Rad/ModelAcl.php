<?php

/**
 * Rad_ModelAcl
 * 
 * @copyright SmartSoftware Argentina
 * @package Rad
 * @subpackage ModelAcl
 * @author Martin Alejandro Santangelo
 */

/**
 * Clase Rad_ModelAcl
 * 
 * Esta clase implementa una lista de control de acceso a los modelos segun el Rol del usuario autenticado.
 * 
 * La ACL se arma de las tablas RolesModelos y Roles
 * 
 * @copyright SmartSoftware Argentina
 * @package Rad
 * @subpackage ModelAcl
 * @author Martin Alejandro Santangelo
 */
class Rad_ModelAcl
{
    const cacheNameSpace = 'Rad_ModelAcl';

    /**
     * @var array de privilegios
     */
    private $privileges;

    /**
     * 	Constructor de la clase
     *
     * 	@param Zend_Db_Adapter_Abstract $db
     */
    public function __construct($db)
    {

        $auth = Zend_Auth::getInstance();
        $this->identity = $auth->getIdentity();
        if ($this->identity->GrupoDeUsuario != 1)
            $this->getPrivFromDb($db);
    }

    /**
     * 	Setea en $privileges los privilegios del usuario para los modelos
     *
     * 	@param Zend_Db_Adapter_Abstract $db
     */
    private function getPrivFromDb($db)
    {
        $cache = Zend_Registry::get('fastCache');
        if (APPLICATION_ENV != 'development') {
            $this->privileges = $cache->load(self::cacheNameSpace . $this->identity->GrupoDeUsuario);
        }
        if (!$this->privileges) {
            // Privilegios a partir de los Modelos
            $priv = $db->fetchAll("
                SELECT Modelos.Descripcion as Modelo, Ver, Modificar, Crear, Borrar, 1 as Menu
                FROM RolesModelos 
                    INNER JOIN GruposDeUsuariosRoles on GruposDeUsuariosRoles.Rol = RolesModelos.Rol 
                    INNER JOIN Modelos ON (RolesModelos.Modelo = Modelos.Id)
                WHERE GruposDeUsuariosRoles.GrupoDeUsuario = {$this->identity->GrupoDeUsuario}");
            // Privilegios a partir de los Modulos
            $privModulos = $db->fetchAll("
                SELECT Modelos.Descripcion as Modelo,
                    MAX(ModulosModelos.Ver) AS Ver,
                    MAX(ModulosModelos.Crear) AS Crear,
                    MAX(ModulosModelos.Modificar) AS Modificar,
                    MAX(ModulosModelos.Borrar) AS Borrar
                FROM ModulosModelos
                    INNER JOIN Modulos ON (ModulosModelos.Modulo = Modulos.Id)
                    INNER JOIN RolesModulos ON (RolesModulos.Modulo = Modulos.Id)
                    INNER JOIN GruposDeUsuariosRoles on GruposDeUsuariosRoles.Rol = RolesModulos.Rol
                    INNER JOIN Modelos ON (ModulosModelos.Modelo = Modelos.Id)
                WHERE (GruposDeUsuariosRoles.GrupoDeUsuario = {$this->identity->GrupoDeUsuario})
                GROUP BY Modelos.Descripcion");
            $this->privileges = array();
            
            foreach ($priv as $row) {
                $modelo = $row['Modelo'];
                unset($row['Modelo']);
                // Solo mostrar en el menu si se dio permisos a traves del modelo
                if ($this->privileges[$modelo]['Menu']) {
                    $row['Menu'] = 1;
                }
                Rad_Log::debug($modelo);
                $this->privileges[$modelo] = $row;
                Rad_Log::debug($this->privileges[$modelo]);                
            }
            foreach ($privModulos as $row) {
                $modelo = $row['Modelo'];
                unset($row['Modelo']);
                if ($this->privileges[$modelo]['Menu']) {
                    $row['Menu'] = 1;
                };
                $this->privileges[$modelo] = max($row, $this->privileges[$modelo]);
            }
            
            $cache->save($this->privileges, self::cacheNameSpace . $this->identity->GrupoDeUsuario, array('ModelAcl'));
        }
    }
    
    /**
     *
     */
    public function analyzePath($path)
    {
        $exploded = explode('/', $path);
        
        $pairs = array();
        $current = null;
        foreach ($exploded as $key => $val) {
            if ($val) {
                if (!$current) {
                    $current = $val;
                } else {
                    $pairs[$current] = $val;
                    unset($current);
                }
            }
        }
        
        return $pairs;
    }

    /**
     * 	Retorna true si el usuario tiene permiso de insertar en el modelo
     *
     * 	@param Rad_DbTable modelo
     *  @return bool
     */
    public function allowInsert($modelo)
    {
        if ($this->identity->GrupoDeUsuario == 1) {
            return true;
        }
        return $this->privileges[$modelo]['Crear'];
    }

    /**
     * 	Retorna true si el usuario tiene permiso de borrar en el modelo
     *
     * 	@param Rad_DbTable modelo
     *  @return bool
     */
    public function allowDelete($modelo)
    {
        if ($this->identity->GrupoDeUsuario == 1) {
            return true;
        }
        return $this->privileges[$modelo]['Borrar'];
    }

    /**
     * 	Retorna true si el usuario tiene permiso de editar en el modelo
     *
     * 	@param Rad_DbTable modelo
     *  @return bool
     */
    public function allowUpdate($modelo)
    {
        if ($this->identity->GrupoDeUsuario == 1) {
            return true;
        }
        return $this->privileges[$modelo]['Modificar'];
    }

    /**
     * 	Retorna true si el usuario tiene permiso de ver en el modelo
     *
     * 	@param Rad_DbTable modelo
     *  @return bool
     */
    public function allowView($modelo)
    {
        if ($this->identity->GrupoDeUsuario == 1) {
            return true;
        }
        return $this->privileges[$modelo]['Ver'];
    }
    
    /**
     * 	Retorna true si el usuario tiene permiso de ver el modelo en el menu
     *
     * 	@param Rad_DbTable modelo
     *  @return bool
     */
    public function allowMenu($modelo)
    {
        if ($this->identity->GrupoDeUsuario == 1) {
            return true;
        }
        return $this->privileges[$modelo]['Menu'];
    }
}
