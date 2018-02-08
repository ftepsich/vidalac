<?php
class Rrhh_Model_DbTable_PersonasAfiliacionesAdherentes extends Rad_Db_Table
{
    protected $_name = 'PersonasAfiliacionesAdherentes';

    protected $_referenceMap    = array(
        
	    'PersonasAfiliaciones' => array(
            'columns'           => 'PersonaAfiliacion',
            'refTableClass'     => 'Rrhh_Model_DbTable_PersonasAfiliaciones',
            'refJoinColumns'    => array('Persona'),
            'comboBox'			=> true,
            'comboSource'		=> 'datagateway/combolist',
            'refTable'			=> 'PersonasAfiliaciones',
            'refColumns'        => 'Id',
        ),
	    'FamiliaresPersonas' => array(
            'columns'           => 'FamiliarPersona',
            'refTableClass'     => 'Rrhh_Model_DbTable_FamiliaresPersonas',
            'refJoinColumns'    => array('Id'),
            'comboBox'			=> true,
            'comboSource'		=> 'datagateway/combolist',
            'refTable'			=> 'FamiliaresPersonas',
            'refColumns'        => 'Id',
            'comboPageSize'     => 20
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


    public function init()
    {
        parent::init();

        if ($this->_fetchWithAutoJoins) {
            $j = $this->getJoiner();
            $j->with('FamiliaresPersonas')
                ->joinRef('Personas', array(
                    'RazonSocial' => 'TRIM({remote}.RazonSocial)',
                    'Denominacion' => 'TRIM({remote}.Denominacion)',
                    'Dni' => 'TRIM({remote}.Dni)'
                ));
        }
    } 

}