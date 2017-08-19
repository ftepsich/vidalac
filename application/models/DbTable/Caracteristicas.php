<?php
class Model_DbTable_Caracteristicas extends Rad_Db_Table
{
    protected $_name = 'Caracteristicas';

    protected $_referenceMap    = array(

        'TiposDeCampos' => array(
            'columns'           => 'TipoDeCampo',
            'refTableClass'     => 'Model_DbTable_TiposDeCampos',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'TiposDeCampos',
            'refColumns'        => 'Id',
            'comboPageSize'     => '10'
        )
    );

    protected $_validators = array(
        'Descripcion' => array(
            array(  // que no se repita el nombre si el Id es diferente
                'Db_NoRecordExists',
                'Caracteristicas',
                'Descripcion',
                array(
                    'field' => 'Id',
                    'value' => "{Id}"
                )
            )
        )
    );

    protected $_dependentTables = array('Model_DbTable_CaracteristicasListas',
                                        'Model_DbTable_CaracteristicasModelos');


    // , Model_DbTable_ModelosCaracterizablesCaracteristicas'

    /**
     * Inserta un registro
     *
     * @param array $data
     *
     */
    public function insert($data) {
        $data['Nombre'] = str_replace(" ","",ucwords(strtolower($data['Descripcion'])));
        return $id = parent::insert($data);
    }

    /**
     * Modifica uno o mas registros
     *
     * @param array $data
     * @param array $where
     *
     */
    public function update($data,$where) {
        if (isset($data['Descripcion'])) {
            $data['Nombre'] = str_replace(" ","",ucwords(strtolower($data['Descripcion'])));
        }
        return parent::update($data,$where);
    }

    static public function getIdByName($nombre)
    {
        $db     = Zend_Registry::get('db');
        $nombre = $db->quote($nombre);
        return $db->fetchOne("SELECT Id FROM Caracteristicas WHERE Nombre = $nombre");
    }

    public function fetchEsLista($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = "TipoDeCampo = 5";
        $where = $this->_addCondition($where, $condicion);
        return parent:: fetchAll($where, $order, $count, $offset);
    }
}