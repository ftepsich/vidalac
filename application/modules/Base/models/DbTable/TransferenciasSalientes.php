<?php

require_once('Rad/Db/Table.php');

class Base_Model_DbTable_TransferenciasSalientes extends Base_Model_DbTable_TransaccionesBancarias
{

    protected $_name = "TransaccionesBancarias";
    protected $_sort = array("Fecha desc");
    protected $_defaultSource = self::DEFAULT_CLASS;
    protected $_permanentValues = array(
        'TipoDeTransaccionBancaria' => 1,
        'TipoDeMovimiento' => 2
    );
    protected $_defaultValues = array(
        'TipoDeTransaccionBancaria' => 1,
        'TipoDeMovimiento' => 2,
        'Utilizado' => '0'
    );

    public function init()
    {
        $this->_validators = array(
            'CtaOrigen' => array(
                'NotEmpty',
                'allowEmpty'=>false,
                'messages' => array('Falta ingresar la cuenta origen (propia).')
            ),            
            'CtaDestino' => array(
                'NotEmpty',
                'allowEmpty'=>false,
                'messages' => array('Falta ingresar la cuenta destino (proveedor).')
            ),
            'Monto'=> array(
                array( 'GreaterThan',
                        0
                ),
                'messages' => array('El monto no puede ser 0 (cero)')
            )
        );
        $this->_referenceMap ['VBancosCuentasOrigen'] ['comboSource'] = 'datagateway/combolist/fetch/EsPropia';
        parent::init();
    }

    public function insert($data)
    {
        try {
            $this->_db->beginTransaction();

            $id = parent::insert($data);
            $rt = $this->find($id)->current();
            Rad_PubSub::publish('Contable_TranSaliente_Insertado', $rt);
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
                Rad_PubSub::publish('Contable_TranSaliente_Borrado', $row);                
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
        $condicion = "TipoDeTransaccionBancaria = 1 and TipoDeMovimiento = 2";
        $where = $this->_addCondition($where, $condicion);
        return parent::fetchAll($where, $order, $count, $offset);
    }

}
