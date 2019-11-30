<?php

/**
 * Base_Model_DbTable_ChequesDarDestinos
 *
 * Esta clase hereda de Cheques
 *
 * @class Base_Model_DbTable_ChequesDarDestinos
 * @extends Base_Model_DbTable_Cheques
 */
class Base_Model_DbTable_ChequesDarDestinos extends Base_Model_DbTable_Cheques
{

    /**
     * Validadores
     *
     * ChequeEstado         -> no vacio
     * FechaDeMovimiento    -> no vacio
     * CuentaDeMovimiento   -> no vacio
     * FechaEmision         -> no vacia
     * Letra                -> no vacio, valor valido
     *
     */
    protected $_validators = array(
        'ChequeEstado' => array(
            'NotEmpty',
            'allowEmpty'=>false,
            'messages' => array('Falta ingresar el Estado del Cheque.')
        )
    );

    public function init()
    {
        $this->_referenceMap ['ChequesEstados'] ['comboSource'] = 'datagateway/combolist/fetch/VenderDepositar';
        parent::init();
    }

    public function update ($data, $where)
    {
        $reg = $this->fetchAll($where);

        foreach ($reg as $row) {
            // Si lo puso como disponible Id=6 entonces blanqueo los valores fechaMoviemiento y CuentaMovimiento
            if ($data['ChequeEstado'] == 6) {
                $data['CuentaDeMovimiento'] = "";
                $data['FechaDeMovimiento']  = "";
            } else {
                if( ($data['ChequeEstado'] && $data['ChequeEstado'] != $row['ChequeEstado']) || is_null($data['TieneRecibo'])){                
                    if (!$data['FechaDeMovimiento'] && !$row['FechaDeMovimiento']) {
                        throw new Rad_Db_Table_Exception('Debe ingresar la fecha de la operacion.');
                    }
                    if (!$data['ChequeEstado'] && !$row['ChequeEstado']) {
                        throw new Rad_Db_Table_Exception('Debe ingresar el Estado del Cheque.');
                    }
                    // Si se entrega a un socio no hay relacion con una cuenta bancaria destino, sino controlar

                    
                }

            }

            if (($data['ChequeEstado'] == $row['ChequeEstado']) || (!$data['ChequeEstado'] && $row['ChequeEstado'])) {
                if (Rad_Confirm::confirm( "No se cambio el estado del cheque. Desea continuar?", _FILE_._LINE_, array('includeCancel' => false)) == 'yes') {
                    return Rad_Db_Table::update($data, $where);
                }
            } else {
                return Rad_Db_Table::update($data, $where);
            }

        }
    }

}