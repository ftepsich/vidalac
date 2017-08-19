<?php
/**
 * @package     Aplicacion
 * @subpackage  Default
 * @class       Rrhh_Model_DbTable_PersonasGananciasDeducciones
 * @extends     Rad_Db_Table
 * @copyright   SmartSoftware Argentina
 */
class Rrhh_Model_DbTable_PersonasGananciasDeducciones extends Rad_Db_Table
{
    protected $_name = 'PersonasGananciasDeducciones';

    protected $_sort = array('Persona asc','GananciaDeduccion asc');

    protected $_referenceMap = array(

    'Personas' => array(
        'columns'           => 'Persona',
        'refTableClass'     => 'Base_Model_DbTable_Personas',
        'refJoinColumns'    => array('RazonSocial'),
        'comboBox'          => true,
        'comboSource'       => 'datagateway/combolist',
        'refTable'          => 'Personas',
        'refColumns'        => 'Id',
        'comboPageSize'     => '30'
    )
,
    'MesDesde' => array(
        'columns'           => 'MesDesde',
        'refTableClass'     => 'Base_Model_DbTable_Meses',
        'refJoinColumns'    => array('Descripcion'),
        'comboBox'          => true,
        'comboSource'       => 'datagateway/combolist',
        'refTable'          => 'Meses',
        'refColumns'        => 'Id'
    )
,
    'MesHasta' => array(
        'columns'           => 'MesHasta',
        'refTableClass'     => 'Base_Model_DbTable_Meses',
        'refJoinColumns'    => array('Descripcion'),
        'comboBox'          => true,
        'comboSource'       => 'datagateway/combolist',
        'refTable'          => 'Meses',
        'refColumns'        => 'Id'
    )
,
    'MesInicioImputacion' => array(
        'columns'           => 'MesInicioImputacion',
        'refTableClass'     => 'Base_Model_DbTable_Meses',
        'refJoinColumns'    => array('Descripcion'),
        'comboBox'          => true,
        'comboSource'       => 'datagateway/combolist',
        'refTable'          => 'Meses',
        'refColumns'        => 'Id'
    )
,
    'GananciasConceptos' => array(
        'columns'           => 'GananciaDeduccion',
        'refTableClass'     => 'Liquidacion_Model_DbTable_GananciasConceptos',
        'refJoinColumns'    => array('Descripcion'),
        'comboBox'          => true,
        'comboSource'       => 'datagateway/combolist/fetch/DeduccionesPersonales',
        'refTable'          => 'GananciasConceptos',
        'refColumns'        => 'Id',
        'comboPageSize'     => '30'
    )
,
    'Empresas' => array(
        'columns'           => 'Empresa',
        'refTableClass'     => 'Base_Model_DbTable_Empresas',
        'refJoinColumns'    => array('Descripcion'),
        'comboBox'          => true,
        'comboSource'       => 'datagateway/combolist',
        'refTable'          => 'Empresas',
        'refColumns'        => 'Id'
    )
,
    'Familiar' => array(
        'columns'           => 'Familiar',
        'refTableClass'     => 'Rrhh_Model_DbTable_Familiares',
        'refJoinColumns'    => array('RazonSocial'),
        'comboBox'          => true,
        'comboSource'       => 'datagateway/combolist',
        'refTable'          => 'Personas',
        'refColumns'        => 'Id',
        'comboPageSize'     => '10'
    )


    );

    protected $_dependentTables = array();

    /**
    * Inserta una Deducción
    * @param array $data Datos
    */
    public function insert($data)
    {
        $data['FechaCarga'] = date('Y-m-d H:i:s');
        $id = parent::insert($data);
        return $id;
    }   


}