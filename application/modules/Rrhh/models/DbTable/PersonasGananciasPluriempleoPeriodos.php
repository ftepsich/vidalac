<?php
/**
 * @package     Aplicacion
 * @subpackage  Default
 * @class       Rrhh_Model_DbTable_PersonasGananciasPluriempleoPeriodos
 * @extends     Rad_Db_Table
 * @copyright   SmartSoftware Argentina
 */
class Rrhh_Model_DbTable_PersonasGananciasPluriempleoPeriodos extends Rad_Db_Table
{
    protected $_name = 'PersonasGananciasPluriempleoPeriodos';

    protected $_sort = array('PersonaGananciaPluriempleo asc','FechaInicio desc');

    protected $_referenceMap = array(
        'Empresas' => array(
            'columns'           => 'EmpresaQueRetiene',
            'refTableClass'     => 'Base_Model_DbTable_Empresas',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'Empresas',
            'refColumns'        => 'Id',
            'comboPageSize'     => '10'
        ),
        'PersonasGananciasPluriempleo' => array(
            'columns'           => 'PersonaGananciaPluriempleo',
            'refTableClass'     => 'Rrhh_Model_DbTable_PersonasGananciasPluriempleo',
            'refTable'          => 'PersonasGananciasPluriempleo',
            'refColumns'        => 'Id',
        )
    );

    protected $_dependentTables = array();
}