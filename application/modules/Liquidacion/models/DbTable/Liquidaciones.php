<?php
class Liquidacion_Model_DbTable_Liquidaciones extends Rad_Db_Table
{
    protected $_name            = 'Liquidaciones';

    protected $_gridGroupField  = 'LiquidacionPeriodo';

    protected $_sort            = array('LiquidacionPeriodo desc','Empresa asc','TipoDeLiquidacion desc');

    protected $_gridGroupFieldOrderDirection = 'DESC';

    protected $_readOnlyFields  = array(
        'TipoDeLiquidacion',
        'LiquidacionPeriodo',
        'Empresa',
        'Usuario',
        'Ejecutada',
        'FechaDeCierre'
    );

    protected $_referenceMap    = array(

	    'TiposDeLiquidaciones' => array(
            'columns'           => 'TipoDeLiquidacion',
            'refTableClass'     => 'Liquidacion_Model_DbTable_TiposDeLiquidaciones',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'			=> true,
            'comboSource'		=> 'datagateway/combolist',
            'refTable'			=> 'TiposDeLiquidaciones',
            'refColumns'        => 'Id',
        ),
        'LiquidacionesPeriodos' => array(
            'columns'           => 'LiquidacionPeriodo',
            'refTableClass'     => 'Liquidacion_Model_DbTable_LiquidacionesPeriodos',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'LiquidacionesPeriodos',
            'refColumns'        => 'Id',
        ),
        'Empresas' => array(
            'columns'           => 'Empresa',
            'refTableClass'     => 'Base_Model_DbTable_Empresas',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'Empresas',
            'refColumns'        => 'Id',
        ),
        'ApJubBancos' => array(
            'columns'           => 'ApJubBanco',
            'refTableClass'     => 'Base_Model_DbTable_Bancos',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'Bancos',
            'refColumns'        => 'Id',
        ),
        'ApJubPeriodos' => array(
            'columns'           => 'ApJubPeriodo',
            'refTableClass'     => 'Liquidacion_Model_DbTable_LiquidacionesPeriodos',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'LiquidacionesPeriodos',
            'refColumns'        => 'Id',
        ),
        'Usuarios' => array(
            'columns'           => 'Usuario',
            'refTableClass'     => 'Model_DbTable_Usuarios',
            'refJoinColumns'    => array('Nombre'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'Usuarios',
            'refColumns'        => 'Id',
        )
    );

    protected $_dependentTables = array(
        'Liquidacion_Model_DbTable_LiquidacionesRecibos',
        'Liquidacion_Model_DbTable_LiquidacionesVariablesDesactivadas'
    );

    public function update($data, $where)
    {
        $result = $this->fetchAll($where);

        // si quiero setear en cerrada da liquidacion primero valido
        if ($data['Cerrada'] == 1) {
            foreach($result as $row) {
                if ($row->Cerrada) throw new Rad_Db_Table_Exception('Esta liquidación se encuentra cerrada y no puede modificarse.');
                if ( (!$data['FechaPago'] && !$row->FechaPago) || (!$data['ApJubPeriodo'] && !$row->ApJubPeriodo) || (!$data['ApJubBanco'] && !$row->ApJubBanco) || (!$data['ApJubFechaDeposito'] && !$row->ApJubFechaDeposito)) {
                    throw new Rad_Db_Table_Exception('Antes de cerrar deben estar cargados Fecha de Pago, Ultimo Aporte Jub., Fecha Deposito, Banco Deposito');
                }
            }
            $data['FechaCierre'] = date('Y-m-d H:i:s');
        } else {
            foreach($result as $row) {
                if ($row->Cerrada) throw new Rad_Db_Table_Exception('Esta liquidación se encuentra cerrada y no puede modificarse.');
            }
        }

        return parent::update($data, $where);
    }

    public function delete($where)
    {
        $db = $this->_db;

        $liqRecibos = new Liquidacion_Model_DbTable_LiquidacionesRecibos;

        $db->beginTransaction();
        try {

            $porBorrar = $this->fetchAll($where);

            foreach ($porBorrar as $row) {
                if ($row->Cerrada) throw new Rad_Db_Table_Exception("No se puede borrar la liquidacion $row->Id ya que se encuentra cerrada.");

                $liqRecibos->delete("Liquidacion = $row->Id");
            }

            parent::delete($where);

            $db->commit();
        } catch (Exception $e) {
            $db->rollback();
            throw $e;
        }
    }
}
