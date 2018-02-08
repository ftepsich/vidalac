<?php

require_once 'Rad/Db/Table.php';

class Base_Model_DbTable_DebitoDirectoDeCuentaBancaria extends Base_Model_DbTable_TransaccionesBancarias
{

    protected $_name = "TransaccionesBancarias";
    protected $_sort = array("Fecha desc");
    protected $_defaultSource = self::DEFAULT_CLASS;

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
            'comboSource' => 'datagateway/combolist/fetch/EsPropia',
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

    /*

    OJO !!!!!!!!!!!!!!!!!!!!!!! Tipo de movimiento 2 = SALIENTE
    Tanto en el Permanet como en el default
    Se debe heredar este modelo en dos Entrante y Saliente
    no debe ser como esta ahora, esta mal

    */    

    protected $_permanentValues = array(
        'TipoDeTransaccionBancaria' => 4,
        'TipoDeMovimiento' => 2
    );

    protected $_defaultValues = array(
        'TipoDeTransaccionBancaria' => 4,
        'TipoDeMovimiento' => 2,
        'Utilizado' => '0'
    );

    public function init()
    {
        $this->_validators = array(
            'CtaDestino' => array(
                'NotEmpty',
                'allowEmpty'=>false,
                'messages' => array('Falta ingresar la cuenta origen (propia).')
            ),
            'Monto'=> array(
                array( 'GreaterThan',
                        0
                ),
                'messages' => array('El monto no puede ser 0 (cero)')
            )
        );
        //$this->_referenceMap ['VBancosCuentasDestino'] ['comboSource'] = 'datagateway/combolist/fetch/EsPropia';
        $this->_referenceMap ['Personas']['refTableClass'] = 'Base_Model_DbTable_Proveedores';
        
        //Esto es para las entrantes no las saliente. Por ahora este modelo es saliente
        //$cfg = Rad_Cfg::get();
        //$this->_permanentValues ['Persona'] = $cfg->Base->idNuestraEmpresa;

        parent::init();
    }

    public function insert($data)
    {
        try {
            $this->_db->beginTransaction();

            $id = parent::insert($data);
            $rt = $this->find($id)->current();
            Rad_PubSub::publish('Contable_DebitoDirectoCtaBancaria_Insertado', $rt);
            $this->_db->commit();

            return $id;
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

     /**
     * Delete
     *
     * @param array $where 	Registros que se deben eliminar
     *
     */
    public function delete($where)
    {
        try {
            $this->_db->beginTransaction();
            $reg = $this->fetchAll($where);

            foreach ($reg as $row) {
                // Publico y Borro
                Rad_PubSub::publish('Contable_DebitoDirectoCtaBancaria_Borrado', $row);
                parent::delete('Id =' . $row->Id.' ');
            }
            $this->_db->commit();
            return true;
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    public function fetchAll($where = null, $order = null, $count = null, $offset = null)
    {
        $where = $this->_addCondition($where, " TipoDeTransaccionBancaria = 4");
        return parent::fetchAll($where, $order, $count, $offset);
    }

}
