<?php

/**
 * Base_Model_DbTable_ChequesPropios
 *
 * Cheques propios emitidos por la empresa
 *
 * @class Base_Model_DbTable_ChequesPropios
 * @extends Base_Model_DbTable_Cheques
 */
class Base_Model_DbTable_ChequesDeTerceros extends Base_Model_DbTable_Cheques
{

    protected $_defaultSource = self::DEFAULT_CLASS;
    
    protected $_permanentValues = array(
        'TipoDeEmisorDeCheque' => 2
    );
    
    protected $_defaultValues = array(
        'ChequeEstado' => '8',
        'NoALaOrden' => '0',
        'ChequeManual' => '0',
        'Cruzado' => '0',
        'Impreso' => '0'
        
    );
    
    /**
     * Validadores
     *
     * FechaDeVencimiento -> no vacia
     *
     */
    protected $_validators = array(
        'FechaDeVencimiento' => array(
            'NotEmpty',
            'allowEmpty'=>false,
            'messages' => array('Falta ingresar la Fecha de Vencimiento.')
        )
    );    

    public function init ()
    {
        $this->_referenceMap['ChequesEstados']['comboSource'] = 'datagateway/combolist/fetch/ParaTerceros';	
		
        parent::init ();
    }

    // los cheques de terceros pueden ser borrados si no se utilizaron
    public function delete ($where)
    {
        $condicion = "ChequeEstado <> 8 ";
        
        $tempWhere = $where;
        
        if (is_array($where)) {
            $tempWhere[] = $condicion;
        } else {
            $tempWhere = $tempWhere ? $tempWhere . ' and ' . $condicion : $condicion;
        }
        
        $exist = $this->fetchRow($tempWhere);
        
        if ($exist) {
            throw new Rad_Db_Table_Exception('No puede borrar cheques utilizados');
        }
        
        return Rad_Db_Table::delete($where);
    }
	


    public function fetchIngresados ($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = "ChequeEstado = 8";
        $where = $this->_addCondition($where, $condicion);
        return self:: fetchAll($where, $order, $count, $offset);
    }
    

}
