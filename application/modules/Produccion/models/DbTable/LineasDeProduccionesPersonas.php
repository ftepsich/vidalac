<?php

/**
 * Produccion_Model_DbTable_LineasDeProduccionesPersonas
 *
 * @package     Aplicacion
 * @subpackage 	Produccion
 * @class       Produccion_Model_DbTable_LineasDeProduccionesPersonas
 * @extends     Rad_Db_Table
 * @copyright   SmartSoftware Argentina 2010
 */
class Produccion_Model_DbTable_LineasDeProduccionesPersonas extends Rad_Db_Table
{
    protected $_name = 'LineasDeProduccionesPersonas';

    protected $_validators = array(
        'Actividad'  => array(
            array(
                'Db_NoRecordExists',
                'LineasDeProduccionesPersonas',
                'Actividad',
                'Persona = {Persona} AND Produccion = {Produccion} AND Id <> {Id}'
            ),
            'messages' => array('El empleado ya realiza esa actividad en dicha produccion.')
        )
    );
    
    protected $_referenceMap    = array(
        
	    'Producciones' => array(
            'columns'           => 'Produccion',
            'refTableClass'     => 'Produccion_Model_DbTable_Producciones',
            'refTable'		=> 'Producciones',
            'refColumns'        => 'Id',
        ),
	    'Personas' => array(
            'columns'           => 'Persona',
            'refTableClass'     => 'Base_Model_DbTable_Personas',
            'refJoinColumns'    => array('RazonSocial','Dni'),
            'comboBox'		=> true,        
            'comboSource'	=> 'datagateway/combolist',
            'refTable'		=> 'Personas',
            'refColumns'        => 'Id',
        ),
	    'Actividades' => array(
            'columns'           => 'Actividad',
            'refTableClass'     => 'Produccion_Model_DbTable_Actividades',
            'refTable'		=> 'Actividades',
            'refColumns'        => 'Id',
        )    );

    protected $_dependentTables = array();
    
    /**
     * Inserta un registro autonumerandolo
     *
     * @param array $data
     * @return mixed
     */
    public function insert($data) {
        try {
            $this->_db->beginTransaction();
            
            $M_P = new Produccion_Model_DbTable_Producciones(array(), false);
            
            if (!$M_P->estaDetenidaOAceptada($data['Produccion'])) {
                throw new Rad_Db_Table_Exception("No se puede modificar: la Orden De Produccion esta Iniciada.");
            }
			
            $M_A = new Produccion_Model_DbTable_Actividades(array(), false);
			
            $EmpleadosMax = $M_A->recuperarMaximoDeEmpleados($data['Actividad']);

            $rowset = $this->fetchAll("Produccion = {$data['Produccion']} and Actividad = {$data['Actividad']}");			

            $cantidad = count($rowset);

            if($cantidad >= $EmpleadosMax){
                    throw new Rad_Exception('La Actividad ya tiene el maximo de empleados asociados.');
            }
            

            $id = parent::insert($data);
            $this->_db->commit();
            return $id;
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }
    
    /**
     * 	Update
     *
     * @param array $data 	Valores que se cambiaran
     * @param array $where 	Registros que se deben modificar
     *
     */
    public function update($data, $where)
    {
        try {
            $this->_db->beginTransaction();
            $M_P = new Produccion_Model_DbTable_Producciones(array(), false);
            $reg = $this->fetchAll($where);

            foreach ($reg as $row) {
                if (!$M_P->estaDetenidaOAceptada($row->Produccion)) {
                    throw new Rad_Db_Table_Exception("No se puede modificar: la Orden De Produccion esta Iniciada.");
                }
            }
            parent::update($data, $where);
            $this->_db->commit();

            return true;
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    /**
     * Delete
     *
     * @param array $where 	Registros que se deben eliminar
     *
     */
    public function delete($where)
    {
        try {
            $this->_db->beginTransaction();
            $M_P = new Produccion_Model_DbTable_Producciones(array(), false);            
            $reg = $this->fetchAll($where);

            foreach ($reg as $row) {
                if (!$M_P->estaDetenidaOAceptada($row->Produccion)) {
                    throw new Rad_Db_Table_Exception("No se puede modificar: la Orden De Produccion esta Iniciada.");
                }
            }
            parent::delete($where);     
            $this->_db->commit();
            return true;
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    } 	
	
}