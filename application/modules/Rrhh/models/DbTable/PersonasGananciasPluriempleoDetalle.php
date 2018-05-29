<?php
/**
 * @package     Aplicacion
 * @subpackage  Default
 * @class       Rrhh_Model_DbTable_PersonasGananciasPluriempleoDetalle * @extends     Rad_Db_Table
 * @copyright   SmartSoftware Argentina
 */
class Rrhh_Model_DbTable_PersonasGananciasPluriempleoDetalle extends Rad_Db_Table
{
    protected $_name = 'PersonasGananciasPluriempleoDetalle';

    protected $_referenceMap = array(
        'PersonasGananciasPluriempleo' => array(
            'columns'           => 'PersonaGananciaPluriempleo',
            'refTableClass'     => 'Rrhh_Model_DbTable_PersonasGananciasPluriempleo',
            'refTable'          => 'PersonasGananciasPluriempleo',
            'refColumns'        => 'Id',
        )
    );

    public function insert($data)
    {
        $data['FechaCarga'] = date('Y-m-d H:i:s');
        return parent::insert($data);
    }

    protected $_dependentTables = array();
}