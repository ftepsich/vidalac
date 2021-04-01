<?php

require_once('Rad/Db/Table.php');

class Base_Model_DbTable_TransaccionesBancarias extends Rad_Db_Table
{
    // Tabla mapeada
    protected $_name = "TransaccionesBancarias";
    // Relaciones
    protected $_referenceMap = array(
        'Personas' => array(
            'columns' => 'Persona',
            'refTableClass' => 'Base_Model_DbTable_Personas',
            'refJoinColumns' => array('RazonSocial'),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'Personas',
            'refColumns' => 'Id',
            'comboPageSize' => 10
        ),
        'CuentasBancariasO' => array(
            'columns' => 'CtaOrigen',
            'refTableClass' => 'Base_Model_DbTable_CuentasBancarias',
            'refJoinColumns' => array('Numero'),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'CuentasBancarias',
            'refColumns' => 'Id'
        ),
        'CuentasBancariasD' => array(
            'columns' => 'CtaDestino',
            'refTableClass' => 'Base_Model_DbTable_CuentasBancarias',
            'refJoinColumns' => array('Numero'),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'CuentasBancarias',
            'refColumns' => 'Id'
        ),
        'TiposDeMovimientosBancarios' => array(
            'columns' => 'TipoDeMovimiento',
            'refTableClass' => 'Base_Model_DbTable_TiposDeMovimientosBancarios',
            'refJoinColumns' => array('Descripcion'),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'TiposDeMovimientosBancarios',
            'refColumns' => 'Id'
        ),
        'TiposDeTransaccionesBancarias' => array(
            'columns' => 'TipoDeTransaccionBancaria',
            'refTableClass' => 'Base_Model_DbTable_TiposDeTransaccionesBancarias',
            'refJoinColumns' => array('Descripcion'),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'TiposDeTransaccionesBancarias',
            'refColumns' => 'Id'
        ),
        'VBancosCuentasOrigen' => array(
            'columns' => 'CtaOrigen',
            'refTableClass' => 'Base_Model_DbTable_VBancosCuentas',
            'refJoinColumns' => array('Descripcion'),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'VBancosCuentas',
            'refColumns' => 'CuentaBancariaId',
            'comboPageSize' => 20
        ),
        'VBancosCuentasDestino' => array(
            'columns' => 'CtaDestino',
            'refTableClass' => 'Base_Model_DbTable_VBancosCuentas',
            'refJoinColumns' => array('Descripcion'),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist/fetch/NoEsPropia',
            'refTable' => 'VBancosCuentas',
            'refColumns' => 'CuentaBancariaId',
            'comboPageSize' => 20
        ),
         'Cajas' => array(
            'columns' => 'Caja',
            'refTableClass' => 'Contable_Model_DbTable_Cajas',
            'refJoinColumns' => array('Descripcion'),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'Cajas',
            'refColumns' => 'Id'
        )
    );
    protected $_dependentTables = array();

    public function insert($data)
    {
        try {
            $this->_db->beginTransaction();

            $this->salirSi_FaltanCamposObligatorios($data);

            $id = parent::insert($data);

            $this->_db->commit();
            return $id;
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    public function update($data, $where)
    {
        try {
            $this->_db->beginTransaction();

            $reg = $this->fetchAll($where);
            foreach ($reg as $row) {
                $this->salirSi_NoSePuedeModificar($row->Id);
                $this->salirSi_FaltanCamposObligatorios($row);
            }
            parent::update($data, $where);

            $this->_db->commit();
            return true;
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    public function delete($where)
    {
        try {
            $this->_db->beginTransaction();

            $reg = $this->fetchAll($where);
            foreach ($reg as $row) {
                $this->salirSi_NoSePuedeModificar($row->Id);
            }
            parent::delete($where);

            $this->_db->commit();
            return true;
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    public function sePuedeModificar($idTransaccion)
    {
        //Verifico si esta incluido en alguna orden de pago o Recibo

        $sql = "select count(CD.Id) as rta
                from    ComprobantesDetalles CD, Comprobantes C
                where   CD.Comprobante = C.Id
                and     CD.TransaccionBancaria = $idTransaccion
                and     C.Anulado = 0";

        $Cant = 0;
        $Cant = $this->_db->fetchOne($sql);

        if ($Cant > 0) {
            return false;
        } else {
            return true;
        }
    }

    public function salirSi_NoSePuedeModificar($idTransaccion)
    {
        if (!$this->sePuedeModificar($idTransaccion)) {
            throw new Rad_Db_Table_Exception("La Transaccion bancaria seleccionada no puede modificarse. Esto se debe a que se encuentra incluida en una orden de Pago o Recibo.");
        }
    }

    public function marcarComoUsada($idTransaccion)
    {
        $RS = $this->find($idTransaccion);
        foreach ($RS as $R) {
            if ($R->Utilizado == 1) {
                throw new Rad_Db_Table_Exception("Se intenta utilizar una transaccion ya utilizada.");
            }
            parent::update(array('Utilizado' => 1), "Id = $R->Id");
        }
    }

    public function marcarComoDisponible($idTransaccion)
    {
        $R = $this->find($idTransaccion)->current();
        if (!$R) {
            throw new Rad_Db_Table_Exception("No se puede localizar la transaccion bancaria seleccionada.");
        }
        if ($R->Utilizado == 0) {
            throw new Rad_Db_Table_Exception("Se intenta liberar una transaccion no utilizada.");
        }

        parent::update(array('Utilizado' => 0), "Id = $idTransaccion");
    }

    public function salirSi_FaltanCamposObligatorios($data)
    {
        $incompleto = "";
        $sep = "";

        //Verifico los datos comunes
        if (isset($data['Monto']) && !$data['Monto']) {
            $incompleto = "Monto";
            $sep = ", ";
        }
        if (isset($data['Fecha']) && !$data['Fecha']) {
            $incompleto = $sep . "Fecha";
            $sep = ", ";
        }
        if (isset($data['TipoDeTransaccionBancaria']) && !$data['TipoDeTransaccionBancaria']) {
            $incompleto = $sep . "Tipo de Transaccion";
            $sep = ", ";
        }
        if (isset($data['TipoDeMovimiento']) && !$data['TipoDeMovimiento']) {
            $incompleto = $sep . "Tipo de Movimiento";
            $sep = ", ";
        }

        //Verifico que los datos particulares esten completos
        if (!$incompleto) {
            switch ($data['TipoDeTransaccionBancaria']) {
                case 1: // transferencia
                    if (isset($data['CtaDestino']) && !$data['CtaDestino']) {
                        $incompleto = $sep . "Cta Destino";
                        $sep = ", ";
                    }
                    if ($data['TipoDeMovimiento'] != 1) {
                        if (isset($data['CtaOrigen']) && !$data['CtaOrigen']) {
                            $incompleto = $sep . "Cta Origen";
                            $sep = ", ";
                        }
                    }
                    break;
                case 2: // deposito
                    if (isset($data['CtaDestino']) && !$data['CtaDestino']) {
                        $incompleto = $sep . "Cta Destino";
                        $sep = ", ";
                    }
                    break;
                case 3: // extraccion
                    if (isset($data['CtaDestino']) && !$data['CtaDestino']) {
                        $incompleto = $sep . "Cta Destino";
                        $sep = ", ";
                    }
                    break;
            }

            switch ($data['TipoDeMovimiento']) {
                case 1: case 2:// Entrada o salida
                    if ((isset($data['Cliente']) && !$data['Cliente'])) {
                        $incompleto = $sep . "Cliente";
                        $sep = ", ";
                    }
                    if ((isset($data['Proveedor']) && !$data['Proveedor'])) {
                        $incompleto = $sep . "Proveedor";
                        $sep = ", ";
                    }
                    break;
            }
        }

        // Si falto completar algo salgo
        if ($incompleto) {
            throw new Rad_Db_Table_Exception("Faltan completar datos obligatorios para la transaccion que intenta realizar.<BR>Detalle: $incompleto");
        }
    }

    public function fetchNoUtilizadoDeEntrada($where = null, $order = null, $count = null, $offset = null)
    {

        $where = $this->_addCondition($where, "Utilizado <> 1 and TipoDeMovimiento = 1" );
        return parent::fetchAll($where, $order, $count, $offset);
    }

    public function fetchNoUtilizadoDeSalida($where = null, $order = null, $count = null, $offset = null)
    {

        $where = $this->_addCondition($where, "Utilizado <> 1 and TipoDeMovimiento = 2" );
        return parent::fetchAll($where, $order, $count, $offset);
    }

}