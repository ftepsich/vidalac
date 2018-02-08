<?php
require_once 'Rad/Db/Table.php';

/**
 * Base_Model_DbTable_ZonasPorVendedores
 *
 * Sucursales Bancarias
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Base
 * @class Base_Model_DbTable_ZonasPorVendedores
 * @extends Rad_Db_Table
 */
class Base_Model_DbTable_BancosSucursales extends Rad_Db_Table
{

    protected $_name = 'BancosSucursales';
    Protected $_sort = array('Descripcion asc');
    protected $_defaultSource = self::DEFAULT_CLASS;
    protected $_defaultValues = array(
        'Sucursal' => 'S/D',
    );

    protected $_referenceMap = array(
        'Localidad' => array(
            'columns'           => 'Localidad',
            'refTableClass'     => 'Base_Model_DbTable_Localidades',
            'refJoinColumns'    => array('Descripcion', 'CodigoPostal'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'Localidades',
            'refColumns'        => 'Id',
            'comboPageSize'     => 10
        ),
        'Bancos' => array(
            'columns'           => 'Banco',
            'refTableClass'     => 'Base_Model_DbTable_Bancos',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'Bancos',
            'refColumns'        => 'Id'
        ),
    );

    public function init()
    {
        $this->_validators = array(
            'Descripcion' => array(
                array('Db_NoRecordExists',
                    'BancosSucursales',
                    'Descripcion',
                    array(
                        'field' => 'Id',
                        'value' => "{Id}"
                    )
                )
            ),
            'NumeroSucursal' => array(
                'NotEmpty',
                'allowEmpty'=>false,
                'messages' => array('Falta ingresar el numero de la sucursal.')
            )
        );
        parent::init();
    }

    private function makeDescripcion($data)
    {
        $B = new Base_Model_DbTable_Bancos(array(), false);
        $Banco = $B->find($data['Banco'])->current();
        $descBanco = "";
        if ($Banco)
            $descBanco = $Banco->Descripcion;

        $data['Descripcion'] = $descBanco . ' Suc ' . $data['Sucursal'] . ' Nro ' . $data[NumeroSucursal];

        return $data;
    }

    public function insert($data)
    {
        $data = $this->makeDescripcion($data);

        $id = parent::insert($data);
        return $id;
    }

    public function update($data, $where)
    {
        $data = $this->makeDescripcion($data);

        $id = parent::update($data, $where);
        return $id;
    }

}