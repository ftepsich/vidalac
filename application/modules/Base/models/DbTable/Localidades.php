<?php
require_once 'Rad/Db/Table.php';

/**
 * Base_Model_DbTable_Localidades
 *
 * Direcciones
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Base
 * @class Base_Model_DbTable_Localidades
 * @extends Rad_Db_Table
 */
class Base_Model_DbTable_Localidades extends Rad_Db_Table
{

    protected $_name = 'Localidades';
    protected $_referenceMap = array(
        'Provincias' => array(
            'columns'           => 'Provincia',
            'refTableClass'     => 'Base_Model_DbTable_Provincias',
            'refJoinColumns'    => array("Descripcion"),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'Provincias',
            'refColumns'        => 'Id'
        )
    );

    protected $_dependentTables = array('Base_Model_DbTable_Direcciones');

    public function init()
    {
        $this->_validators = array(
            'Descripcion' => array(
                array(
                    'Db_NoRecordExists',
                    'Localidades',
                    'Descripcion',
                    'Provincia = {Provincia} AND Id <> {Id}'
                ),
                'messages' => array('Ya existe la localidad')
            )
        );

        parent::init();
    }

    public function getNombre($id) 
    {
        $R_L    = $this->find($id)->current();
        $M_P    = new Base_Model_DbTable_Provincias;
        $R_P    = $M_P->find($R_L->Id)->current();

        return $R_L->Descripcion . ' [' . $R_P->Descripcion . ']';
    }

    public function fetchTieneDirecciones($where = null, $order = null, $count = null, $offset = null)
    {
        /*
            Este fetch se usaen los reportes estadisticos para filtrar los clientes que tienen comprobantes.
        */
        $j  = $this->getJoiner();
        $jc = $j->with('Base_Model_DbTable_Direcciones');
        if (!$jc) {
            $j->joinDep('Base_Model_DbTable_Direcciones',array(),null,null,'Localidades.Id');
        }
        $condicion = 'LocalidadesDirecciones.Id is not null';
        $where = $this->_addCondition($where, $condicion);        

        return parent::fetchAll($where, $order, $count, $offset);
    }  

}