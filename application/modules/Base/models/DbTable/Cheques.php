<?php
require_once 'Rad/Db/Table.php';

/**
 * Base_Model_DbTable_Cheques
 *
 * Esta clase es heredada por ChequesPropios y ChequesDeTerceros
 *
 * @class Base_Model_DbTable_Cheques
 * @extends Rad_Db_Table
 */
class Base_Model_DbTable_Cheques extends Rad_Db_Table
{
    protected $_name = 'Cheques';
    protected $_referenceMap = array(
        'BancosSucursales' => array(
            'columns'        => 'BancoSucursal',
            'refTableClass'  => 'Base_Model_DbTable_BancosSucursales',
            'refJoinColumns' => array('Descripcion'),
            'comboBox'       => true,
            'comboSource'    => 'datagateway/combolist',
            'refTable'       => 'BancosSucursales',
            'refColumns'     => 'Id',
            'comboPageSize'  => 20
        ),
        'ChequesEstados' => array(
            'columns'        => 'ChequeEstado',
            'refTableClass'  => 'Base_Model_DbTable_ChequesEstados',
            'refJoinColumns' => array('Descripcion'),
            'comboBox'       => true,
            'comboSource'    => 'datagateway/combolist',
            'refTable'       => 'ChequesEstados',
            'refColumns'     => 'Id'
        ),
        'TiposDeEmisoresDeCheques' => array(
            'columns'        => 'TipoDeEmisorDeCheque',
            'refTableClass'  => 'Contable_Model_DbTable_TiposDeEmisoresDeCheques',
            'refJoinColumns' => array('Descripcion'),
            'comboBox'       => true,
            'comboSource'    => 'datagateway/combolist',
            'refTable'       => 'TiposDeEmisoresDeCheques',
            'refColumns'     => 'Id'
        ),
        'TiposDeCheques' => array(
            'columns'        => 'TipoDeCheque',
            'refTableClass'  => 'Contable_Model_DbTable_TiposDeCheques',
            'refJoinColumns' => array('Descripcion'),
            'comboBox'       => true,
            'comboSource'    => 'datagateway/combolist',
            'refTable'       => 'TiposDeCheques',
            'refColumns'     => 'Id'
        ),
        'Empleados' => array(
            'columns' => 'Persona',
            'refTableClass' => 'Base_Model_DbTable_Empleados',
            'refTable' => 'Personas',
            'refColumns' => 'Id'
        ),
        'Proveedores' => array(
            'columns'        => 'Persona',
            'refTableClass'  => 'Base_Model_DbTable_Proveedores',
            'refJoinColumns' => array('RazonSocial'),
            'comboBox'       => true,
            'comboSource'    => 'datagateway/combolist',
            'refTable'       => 'Personas',
            'refColumns'     => 'Id',
            'comboPageSize'  => 20
        ),
        'Clientes' => array(
            'columns'        => 'Persona',
            'refTableClass'  => 'Base_Model_DbTable_Clientes',
            'refJoinColumns' => array('RazonSocial'),
            'comboBox'       => true,
            'comboSource'    => 'datagateway/combolist',
            'refTable'       => 'Personas',
            'refColumns'     => 'Id',
            'comboPageSize'  => 20
        ),        
        'Chequeras' => array(
            'columns'        => 'Chequera',
            'refTableClass'  => 'Base_Model_DbTable_Chequeras',
            'refJoinColumns' => array('NumeroDeChequera'),
            'comboBox'       => true,
            'comboSource'    => 'datagateway/combolist',
            'refTable'       => 'Chequeras',
            'refColumns'     => 'Id',
            'comboPageSize'  => 20
        ),
        'CuentasMovimientos' => array(
            'columns'        => 'CuentaDeMovimiento',
            'refTableClass'  => 'Base_Model_DbTable_VBancosCuentas',
            'refJoinColumns' => array('Descripcion'),
            'comboBox'       => true,
            'comboSource'    => 'datagateway/combolist',
            'refTable'       => 'VBancosCuentas',
            'refColumns'     => 'CuentaBancariaId',
            'comboPageSize'  => 20
        )
        
    );
    
    public function init()
    {
        $this->_referenceMap ['CuentasMovimientos'] ['comboSource'] = 'datagateway/combolist/fetch/EsPropia';
        
        parent::init();
    }   

    public function insert($data)
    {
        $data['MontoEnLetras'] = Rad_CustomFunctions::num2letras(round($data['Monto'], 2, PHP_ROUND_HALF_UP));
        return parent::insert($data);
    }

    public function update($data, $where)
    {
        $data['MontoEnLetras'] = Rad_CustomFunctions::num2letras(round($data['Monto'], 2, PHP_ROUND_HALF_UP));
        return parent::update($data, $where);
    }

    public function delete($where)
    {
        // los cheques no se borran solo se anulan...
        // ojo hay que ver las excepciones.
        return parent::update(array('ChequeEstado' => 5), $where);
    }
    
    /**
     * Verifica si la fecha de emision es menor a la fecha de cobro 
     *
     * @param date $FechaDeEmision 
     * @param date $FechaDeCobro
     * @param date $FechaDeVencimiento 
     * 
     * @return boolean
     */
    public function verificaFechaEmisionMenorFechaCobro ($FechaDeEmision,$FechaDeCobro,$FechaDeVencimiento)
    {
        if ($FechaDeEmision && $FechaDeCobro) {
           if ($FechaDeEmision >= $FechaDeCobro) {
             throw new Rad_Db_Table_Exception('La fecha de emision debe ser menor a la fecha de cobro.');
            }
        }
        if ($FechaDeEmision && $FechaDeVencimiento) {
           if ($FechaDeEmision >= $FechaDeVencimiento) {
             throw new Rad_Db_Table_Exception('La fecha de emision debe ser menor a la fecha de vencimiento.');
            }
        }
        return true;
    }

    public function fetchDisponibles($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = 'ChequeEstado = 6';
        $where = $this->_addCondition($where, $condicion);
        return parent::fetchAll($where, $order, $count, $offset);
    }

    public function fetchDarDestino($where = null, $order = null, $count = null, $offset = null)
    {
        // $condicion = 'ChequeEstado in (4,6,10,11,12,13,14,15,16)';
        $condicion = 'ChequeEstado in (SELECT ce.Id FROM ChequesEstados ce WHERE ifnull(ce.ParaDarDestino,0) <> 0) ';
        $where = $this->_addCondition($where, $condicion);
        return parent::fetchAll($where, $order, $count, $offset);
    }
    
    public function fetchDisponiblesPropios($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = 'ChequeEstado = 6 and TipoDeEmisorDeCheque = 1';
        $where = $this->_addCondition($where, $condicion);
        return parent::fetchAll($where, $order, $count, $offset);
    }

    public function fetchEntregadosASocios($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = 'ChequeEstado in (11,12,13)';
        $where = $this->_addCondition($where, $condicion);
        // Piso el orden... me interesa que sea siempre de esta forma
        $order = 'FechaDeMovimiento DESC';
        return parent::fetchAll($where, $order, $count, $offset);
    }

    public function fetchAsociadosYFaltantesDeCobrar ($where = null, $order = null, $count = null, $offset = null)
    {
        if ($where instanceof Zend_Db_Table_Select) {
            $select = $where;
        } else {
            $select = $this->select();
            if (!is_null($where)) {
                $this->_where($select, $where);
            }
        }
          
        if ($order !== null) {
            $this->_order($select, $order);
        }
        if ($count !== null || $offset !== null) {
            $select->limit($count, $offset);
        }
        $select->having('((Cheques.ChequeEstado in (4,10) AND Cheques.Id not in (Select Cheque from ComprobantesCheques)) OR  checked = 1)');
        return self::fetchAll($select);
    }

}