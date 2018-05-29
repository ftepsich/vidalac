<?php
/**
 * Facturacion_Model_DbTable_TarjetasDeCreditoCupones
 *
 * @package     Aplicacion
 * @subpackage  Default
 * @class       Model_DbTable_TarjetasDeCreditoCupones
 * @extends     Rad_Db_Table
 * @copyright   SmartSoftware Argentina
 */
class Facturacion_Model_DbTable_TarjetasDeCreditoCupones extends Rad_Db_Table
{
    protected $_name = 'TarjetasDeCreditoCupones';

    protected $_referenceMap = array(
        'TarjetasDeCredito' => array(
            'columns'           => 'TarjetaDeCredito',
            'refTableClass'     => 'Facturacion_Model_DbTable_TarjetasDeCredito',
            'refJoinColumns'    => array('Numero'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'TarjetasDeCredito',
            'refColumns'        => 'Id',
            'comboPageSize'     => '10'
        ),
        'TiposDeMovimientos' => array(
            'columns'           => 'TipoDeMovimiento',
            'refTableClass'     => 'Facturacion_Model_DbTable_TiposDeMovimientosTarjetas',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'TiposDeMovimientosTarjetas',
            'refColumns'        => 'Id',
            'comboPageSize'     => '10'
        )
    );

    protected $_dependentTables = array();

    public function insert($data)
    {
        // Genero el numero de cupon ya que en los cobros con tarjeta no se pide este numero (por ahora)
        if (!$data['NumeroCupon']) {
            $row = $this->fetchRow('TarjetaDeCredito ='.$data['TarjetaDeCredito'], 'Id DESC for update');
            $data['NumeroCupon'] = $row->Id + 1;
        }
        return parent::insert($data);
    }

    public function marcarComoUsado($idCupon)
    {
        $RS = $this->find($idcupon);
        foreach ($RS as $row) {
            if ($row->Utilizado == 1) throw new Rad_Db_Table_Exception("Se intenta utilizar una cupon de tarjeta ya utilizado.");
            parent::update(array('Utilizado' => 1), "Id = $row->Id");
        }
    }

    public function marcarComoDisponible($idCupon)
    {
        $R = $this->find($idTransaccion)->current();
        if (!$R)                throw new Rad_Db_Table_Exception("No se puede localizar el cupon seleccionado.");
        if ($R->Utilizado == 0) throw new Rad_Db_Table_Exception("Se intenta liberar un cupon no utilizado.");
        parent::update(array('Utilizado' => 0), "Id = $idCupon");
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