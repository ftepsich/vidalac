<?php
class Produccion_Model_DbTable_Actividades extends Rad_Db_Table
{
    protected $_name = 'Actividades';

    protected $_referenceMap    = array(
            );

    protected $_dependentTables = array('Produccion_Model_DbTable_LineasDeProduccionesActividades','Produccion_Model_DbTable_LineasDeProduccionesPersonas');	

    /**
     * recuperar el maximo de empleados de una actividad
     *
     * @param int $idActividad 	identificador de la Actividad
     *
     * @return Zend_Db_Table_Row
     */
    public function recuperarMaximoDeEmpleados ($idActividad)
    {
        $R_A = $this->find($idActividad)->current();

        if (!$R_A)
            throw new Rad_Db_Table_Exception("No se localiza la Actividad.");
			
        return $R_A->EmpleadosMax;	

    }	
}