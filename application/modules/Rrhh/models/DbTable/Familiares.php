<?php
require_once 'Rad/Db/Table.php';

/**
 * Rrhh_Model_DbTable_Familiares
 *
 * Administrador de Empleados
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Base
 * @class Base_Model_DbTable_Empleados
 * @extends Base_Model_DbTable_Personas
 */
class Rrhh_Model_DbTable_Familiares extends Base_Model_DbTable_Personas
{
    protected $_permanentValues = array(
        'EsFamiliar' => 1
    );

    protected $_referenceMap = array(
        'Sexos' => array(
            'columns'           => 'Sexo',
            'refTableClass'     => 'Base_Model_DbTable_Sexos',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'Sexos',
            'refColumns'        => 'Id'
        ),
        'TiposDeDocumentos' => array(
            'columns'           => 'TipoDeDocumento',
            'refTableClass'     => 'Base_Model_DbTable_TiposDeDocumentos',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'TiposDeDocumentos',
            'refColumns'        => 'Id'
        ),
        'EstadosCiviles' => array(
            'columns'           => 'EstadoCivil',
            'refTableClass'     => 'Base_Model_DbTable_EstadosCiviles',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'EstadosCiviles',
            'refColumns'        => 'Id'
        )
    );

    protected $_dependentTables = array('Rrhh_Model_DbTable_PersonasAfiliacionesAdherentes');

    public function insert($data) {
        //$objDT              = new DateTime('NOW');
        //$data['FechaAlta']  = $objDT->format("Y-m-d H:i:s");
        $data['Id']         = parent::insert($data);
        return $data['Id'];
    }

    public function update($data,$where) {
        parent::update($data,$where);
    }

    public function delete($where){
        parent::delete($where);
    }
}
