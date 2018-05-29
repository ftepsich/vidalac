<?php
require_once 'Rad/Db/Table.php';
/**
 * Model_DbTable_Usuarios
 *
 * @package     Aplicacion
 * @subpackage  Usuarios
 * @class       Model_DbTable_Usuarios
 * @extends     Rad_Db_Table_SemiReferencial
 * @author      Martin A. Santangelo
 * @copyright   SmartSoftware Argentina 2012
 */
class Model_DbTable_Usuarios extends Rad_Db_Table_SemiReferencial
{
    // Tabla mapeada
    protected $_name = 'Usuarios';
    protected $_sort = array('Nombre ASC');

    // Relaciones
    protected $_referenceMap    = array(   
        'GruposDeUsuarios' => array(
            'columns'           => 'GrupoDeUsuario',
            'refTableClass'     => 'Model_DbTable_GruposDeUsuarios',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'GruposDeUsuarios',
            'refColumns'        => 'Id',
        )   
    );

    /**
     * Inserta un Registro en la Tabla de Usuarios
     *
     * @param array $data
     * @return mixed
     */
    public function insert($data)
    {

        $this->_db->beginTransaction();
        try {
            $data['ClaveHash'] = password_hash($data['Clave'], PASSWORD_BCRYPT, array('salt' => '754CC93A968B7F919C1C6477457F3'));
            Rad_Log::user("Nuevo Usuario :".$data['Nombre']." con Clave Hash : ". $data['ClaveHash']);
            $id = Rad_Db_Table::insert($data);
            $this->_db->commit();
            return $id;
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    /**
     * Updateo un Registro en la Tabla de Usuarios
     *
     * @param array $data
     * @param mixwd $where
     * @return mixed
     */
    public function update($data, $where)
    {

        $this->_db->beginTransaction();
        try {
            if (isset($data['Clave'])) {
                $data['ClaveHash'] = password_hash($data['Clave'], PASSWORD_BCRYPT, array('salt' => '754CC93A968B7F919C1C6477457F3'));
                Rad_Log::user("Cambió de Clave en Usuario :".$data['Nombre']." ahora con Clave Hash :". $data['ClaveHash']);
            }
            Rad_Db_Table::update($data, $where);
            $this->_db->commit();
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }
    
}