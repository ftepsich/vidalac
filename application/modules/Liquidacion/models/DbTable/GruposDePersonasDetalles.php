<?php
class Liquidacion_Model_DbTable_GruposDePersonasDetalles extends Rad_Db_Table
{
    protected $_name = 'GruposDePersonasDetalles';

    protected $_referenceMap    = array(
        
	    'GruposDePersonas' => array(
            'columns'           => 'GrupoDePersona',
            'refTableClass'     => 'Liquidacion_Model_DbTable_GruposDePersonas',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'			=> true,
            'comboSource'		=> 'datagateway/combolist',
            'refTable'			=> 'GruposDePersonas',
            'refColumns'        => 'Id'
        ),
	    'Personas' => array(
            'columns'           => 'Persona',
            'refTableClass'     => 'Base_Model_DbTable_Empleados',
            'refJoinColumns'    => array('RazonSocial'),
            'comboBox'			=> true,
            'comboSource'		=> 'datagateway/combolist',
            'refTable'			=> 'Personas',
            'refColumns'        => 'Id',
            'comboPageSize' => 20
        )    
    );

    protected $_dependentTables = array();  

    /**
     * Validadores
     *
     * FechaBaja    -> mayor a fecha alta
     *
     */
    protected $_validators = array(
        'FechaBaja'=> array(
            array( 'GreaterThan',
                    '{FechaAlta}'
            ),
            'messages' => array('La fecha de baja no puede ser menor e igual que la fecha de alta.')
        )
    );

    /**
     * Inserta un ArticuloVersion
     * @param array $data Datos
     */
    public function insert($data)
    {
        try 
        {
            $this->_db->beginTransaction();

            if($data['Persona'] && $data['GrupoDePersona'] && $data['FechaAlta']){
                $sqlPersonasMismoGrupo = "SELECT Id FROM GruposDePersonasDetalles WHERE GrupoDePersona = ".$data['GrupoDePersona']." AND Persona = ".$data['Persona']." AND FechaBaja >= '".$data['FechaAlta']."'";

                $personasMismoGrupo = $this->_db->fetchAll($sqlPersonasMismoGrupo);

                if($personasMismoGrupo){
                    throw new Liquidacion_Model_Exception("El empleado se superpone en algun período para ese grupo.");
                }

            } else {
                throw new Liquidacion_Model_Exception("Faltan completar campos obligatorios.");
            }

            $id = parent::insert($data);

            $this->_db->commit();
            return $id;
        } catch(Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }            
    }

    /**
     * Update
     *
     * @param array $data   Valores que se cambiaran
     * @param array $where  Registros que se deben modificar
     *
     */
    public function update($data, $where)
    {
        $this->_db->beginTransaction();
        try {

            $reg = $this->fetchAll($where);

            foreach ($reg as $row) {

                if($data['Persona'] && $data['Persona'] != $row->Persona){
                    throw new Liquidacion_Model_Exception("No se puede modificar el campo empleado. Elimine el Registro y vuelva a cargarlo.");
                }

                if($data['FechaAlta'] && $data['FechaAlta'] != $row->FechaAlta){
                    $sqlPersonasMismoGrupo = "SELECT Id FROM GruposDePersonasDetalles WHERE GrupoDePersona = ".$row->GrupoDePersona." AND Persona = ".$row->Persona." AND FechaBaja >= '".$data['FechaAlta']."'";

                    $personasMismoGrupo = $this->_db->fetchAll($sqlPersonasMismoGrupo);

                    if($personasMismoGrupo){
                        throw new Liquidacion_Model_Exception("El empleado se superpone en algun período para ese grupo.");
                    }
                } 
            }
    
            parent::update($data, $where);
            
            $this->_db->commit();
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }    


}