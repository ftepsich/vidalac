<?php
require_once 'Rad/Db/Table.php';

class Base_Model_DbTable_CodigosActividadesAfip extends Rad_Db_Table
{

    // Tabla
    protected $_name = "CodigosActividadesAfip";
    protected $_sort = array('Descripcion asc');
    protected $_defaultSource = self::DEFAULT_CLASS;
    protected $_defaultValues = array(
        'Porcentaje'      => '0',
        'ParaClientes'    => '0',
        'ParaProveedores' => '0'
    );
    // Relaciones
    protected $_referenceMap = array(
        'Jurisdicciones' => array(
            'columns'           => 'Jurisdiccion',
            'refTableClass'     => 'Afip_Model_DbTable_AfipProvincias',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'AfipProvincias',
            'refColumns'        => 'Id',
            'comboPageSize'     => '10'
        )
    );

    // Validaciones
    protected $_validators = array(
        'Descripcion' => array(
            array(
                'Db_NoRecordExists',
                'Provincias',
                'Descripcion',
                array(
                    'field' => 'Id',
                    'value' => "{Id}"
                )
            ),
            'messages' => 'Ya existe una actividad con el mismo codigo.'
        ),
        'Descripcion' => array(
            'NotEmpty',
            'allowEmpty'=>false,
            'messages' => array(
                'Falta ingresar la Descripción de la actividad.'
            )
        ),
        'Jurisdiccion' => array(
            'NotEmpty',
            'allowEmpty'=>false,
            'messages' => array(
                'Falta ingresar la Jurisdiccion de la actividad.'
            )
        )
    );
    // Dependencias
    protected $_dependentTables = array();

    public function fetchParaClientes ($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion  = "ParaClientes = 1";
        $where      = $this->_addCondition($where, $condicion);
        return parent:: fetchAll ($where , $order , $count , $offset );
    }

    public function fetchParaProveedores ($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion  = "ParaProveedores = 1";
        $where      = $this->_addCondition($where, $condicion);
        return parent:: fetchAll ($where , $order , $count , $offset );
    }

}