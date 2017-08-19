<?php
/**
 * @package     Aplicacion
 * @subpackage  Default
 * @class       Rrhh_Model_DbTable_PersonasGananciasPluriempleo
 * @extends     Rad_Db_Table
 * @copyright   SmartSoftware Argentina
 */
class Rrhh_Model_DbTable_PersonasGananciasPluriempleo extends Rad_Db_Table
{
    protected $_name = 'PersonasGananciasPluriempleo';

    protected $_sort = array('CuitEmpleador asc','Persona asc');

    protected $_readOnlyFields = array(
        'FechaCarga'
    );

    protected $_validators = array(
        'CuitEmpleador' => array(
            array('Regex', '(\d{2}-\d{8}-\d{1})'),
            /*
            array(
                'Db_NoRecordExists',
                'PersonasGananciasPluriempleo',
                'CuitEmpleador',
                "Id <> {Id} AND CuitEmpleador = '{CuitEmpleador}'"
            ),*/
            'messages' => array('Formato de Cuit Incorrecto'/*,
                                'Ya existe ese Cuit.'*/
            ),
        )
    );

    protected $_referenceMap = array(

    'Personas' => array(
        'columns'           => 'Persona',
        'refTableClass'     => 'Base_Model_DbTable_Personas',
        'refJoinColumns'    => array('RazonSocial'),
        'comboBox'          => true,
        'comboSource'       => 'datagateway/combolist',
        'refTable'          => 'Personas',
        'refColumns'        => 'Id',
        'comboPageSize'     => '10'
    )
    );

    protected $_dependentTables = array('Rrhh_Model_DbTable_PersonasGananciasPluriempleoDetalle',
                                        'Rrhh_Model_DbTable_PersonasGananciasPluriempleoPeriodos');
}