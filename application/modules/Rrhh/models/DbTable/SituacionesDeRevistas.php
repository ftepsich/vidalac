<?php


class Rrhh_Model_DbTable_SituacionesDeRevistas extends Rad_Db_Table
{
    protected $_name = 'SituacionesDeRevistas';

    protected $_sort = array('Descripcion asc');

    protected $_referenceMap    = array(
        'TiposDeSueldos' => array(
            'columns'           => 'TipoDeSueldo',
            'refTableClass'     => 'Rrhh_Model_DbTable_TiposDeSueldos',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'TiposDeSueldos',
            'refColumns'        => 'Id',
            'comboPageSize'     => 20
        ),
        'Aplicacion' => array(
            'columns'           => 'Aplicacion',
            'refTableClass'     => 'Rrhh_Model_DbTable_SituacionesDeRevistasAplicaciones',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'SituacionesDeRevistasAplicaiones',
            'refColumns'        => 'Id',
            'comboPageSize'     => 20
        ),
        'CodigoAfip' => array(
            'columns'           => 'CodigoAFIP',
            'refTableClass'     => 'Afip_Model_DbTable_AfipSituacionesDeRevistas',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'AfipSituacionesDeRevistas',
            'refColumns'        => 'Id',
            'comboPageSize'     => 20
        )
    );

    protected $_defaultValues = array(
        'Activo' => 1
    );


    protected $_dependentTables = array('Rrhh_Model_DbTable_Servicios');

    public function init() {
        $this->_validators = array(
            'Descripcion' => array(
                array(  'Db_NoRecordExists',
                        'SituacionesDeRevistas',
                        'Descripcion',
                        array(  'field' => 'Id',
                                'value' => "{Id}"
                        )
                ),
                'messages' => array('El valor que intenta ingresar se encuentra repetido.')
            )
        );

        parent::init();
    }

    public function fetchActivo($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = "SituacionesDeRevistas.Activo = 1 ";
        $where = $this->_addCondition($where, $condicion);
        return parent::fetchAll($where, $order, $count, $offset);
    }

    public function fetchLicencias($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = "SituacionesDeRevistas.Aplicacion = 1 ";
        $where = $this->_addCondition($where, $condicion);
        return parent::fetchAll($where, $order, $count, $offset);
    }

    public function fetchBajas($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = "SituacionesDeRevistas.Aplicacion = 2 ";
        $where = $this->_addCondition($where, $condicion);
        return parent::fetchAll($where, $order, $count, $offset);
    }

    public function fetchNormal($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = "SituacionesDeRevistas.Aplicacion = 3 ";
        $where = $this->_addCondition($where, $condicion);
        return parent::fetchAll($where, $order, $count, $offset);
    }

}