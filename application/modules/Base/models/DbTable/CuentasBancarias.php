<?php

require_once 'Rad/Db/Table.php';

/**
 * Base_Model_DbTable_CuentasBancarias
 *
 * Cuentas Bancarias
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Base
 * @class Base_Model_DbTable_CuentasBancarias
 * @extends Rad_Db_Table
 */
class Base_Model_DbTable_CuentasBancarias extends Rad_Db_Table
{

    protected $_name = 'CuentasBancarias';
    protected $_sort = array('BancosSucursales.Descripcion asc');

    protected $_validators = array(
        'Cbu' => array(
            'NotEmpty',
            'allowEmpty'=>false,
            array('Regex', '(^\d{22}$)'),
            array(
                'Db_NoRecordExists',
                'CuentasBancarias',
                'Cbu',
                'Cbu = "{Cbu}" AND Id <> {Id}'
            ),
            'messages' => array(
                'Falta ingresar el CBU.',
                'Formato Incorrecto del Cbu',
                'El CBU ingresado ya se encuentra asociado a otra cuenta'
            )
        ),
        'Numero' => array(
            'NotEmpty',
            'allowEmpty'=>false,
            'messages' => array(
                'Falta ingresar el Numero de la cuenta.'
            )
        ),
        'CuitTitular' => array(
            array('Regex', '(\d{2}-\d{8}-\d{1})'),
            'messages' => array('Formato Incorrecto del Cuit')
        )
    );

    protected $_referenceMap = array(
        'BancosSucursales' => array(
            'columns' => 'BancoSucursal',
            'refTableClass' => 'Base_Model_DbTable_BancosSucursales',
            'refJoinColumns' => array('Descripcion'),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'BancosSucursales',
            'refColumns' => 'Id',
            'comboPageSize' => 20
        ),
        'TiposDeCuentas' => array(
            'columns' => 'TipoDeCuenta',
            'refTableClass' => 'Base_Model_DbTable_TiposDeCuentas',
            'refJoinColumns' => array('Codigo'),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'TiposDeCuentas',
            'refColumns' => 'Id',
            'comboPageSize' => 20
        ),
        'Personas' => array(
            'columns' => 'Persona',
            'refTableClass' => 'Base_Model_DbTable_Personas',
            'refTable' => 'Personas',
            'refColumns' => 'Id'
        ),
        /**
         * OJO.... no quitar Clientes y Proveedores
         *
         * Se agregaron Clientes y Proveedores para que se puedan usar las funciones
         * findDependentRowset como las que estan en el delete de personas cuando intentamos
         * eliminar un cliente o un proveedor
         */
        'Clientes' => array(
            'columns' => 'Persona',
            'refTableClass' => 'Base_Model_DbTable_Clientes',
            'refTable' => 'Personas',
            'refColumns' => 'Id'
        ),
        'Empleados' => array(
            'columns' => 'Persona',
            'refTableClass' => 'Base_Model_DbTable_Empleados',
            'refTable' => 'Personas',
            'refColumns' => 'Id'
        ),
        'Proveedores' => array(
            'columns' => 'Persona',
            'refTableClass' => 'Base_Model_DbTable_Proveedores',
            'refTable' => 'Personas',
            'refColumns' => 'Id'
        ),
        'Cuentas' => array(
            'columns'           => 'Cuenta',
            'refTableClass'     => 'Contable_Model_DbTable_PlanesDeCuentas',
            'refJoinColumns'    => array("Descripcion"),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist/fetch/Bancos',
            'refTable'          => 'PlanesDeCuentas',
            'refColumns'        => 'Id',
            'comboPageSize'     => 20
        )
    );

    public function insert ($data)
    {
        $this->_db->beginTransaction();
        try {

            // Si no es propia y no tiene titular o cuit del titular,
            // se toma los valores de la persona
            if (!$data['Propia'] && (!$data['Titular'] || !$data['CuitTitular'])) {
                $M_Personas = new Base_Model_DbTable_Personas();

                $row = $M_Personas->fetchRow('Id = ' . $data['Persona']);
                if (!$data['Titular']) {
                    $data['Titular'] = $row->RazonSocial;
                }
                if (!$data['CuitTitular']) {
                    $data['CuitTitular'] = $row->Cuit;
                }
            }

            $id = parent::insert($data);

            $this->_db->commit();
            return $id;
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }


    /**
     * Verifica si ya existe el CBU (no se ocupa ya que tiene un validador)
     *
     * @param array $data   Datos insertados o modificados
     *
     */
    public function repiteCBU ($data) {

        if (isset($data['Cbu']) && $data['Cbu']) {

            $where  = " Cbu = '{$data['Cbu']}'";

            // por si viene de un update
            $where .= (isset($data['Id']) && $data['Id']) ? " and Id <> {$data['Id']} "  : "";

            Rad_Log::debug($where);

            $row = $this->fetchRow($where);

            if (count($row) > 0) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Sale si ya existe el CBU
     *
     * @param array $data   Datos insertados o modificados
     *
     */
    public function salirSi_repiteCBU($data) {
        if ($this->repiteCBU($data)) {
            throw new Rad_Db_Table_Exception("El CBU ingresado ya se encuentra cargado en el sistema para otra cuenta.");
        }
        return $this;
    }


    public function fetchEsPropia ($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = 'Propia = 1';
        $where = $this->_addCondition($where, $condicion);
        return self::fetchAll($where, $order, $count, $offset);
    }

}