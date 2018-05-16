<?php

/**
 * Contable_Model_DbTable_Cajas
 *
 * Cajas
 *
 * @copyright SmartSoftware Argentina
 * @class Contable_Model_DbTable_Cajas
 * @extends Rad_Db_Table
 * @package Aplicacion
 * @subpackage Contable
 */
class Contable_Model_DbTable_Cajas extends Rad_Db_Table_SemiReferencial {

    protected $_name = 'Cajas';
    protected $_referenceMap = array(
        'Cuentas' => array(
            'columns' => 'Cuenta',
            'refTableClass' => 'Contable_Model_DbTable_PlanesDeCuentas',
            'refJoinColumns' => array("Descripcion"),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist/fetch/Cajas',
            'refTable' => 'PlanesDeCuentas',
            'refColumns' => 'Id',
            'comboPageSize' => 20
        )
    );


    protected $_dependentTables = array('Contable_Model_DbTable_CajasMovimientos');

    // Inicio Public Init ----------------------------------------------------------------------------------------
    public function init() {
        $this->_validators = array(
            'Descripcion' => array(
                array('Db_NoRecordExists',
                    'Cajas',
                    'Descripcion',
                    array(
                        'field' => 'Id',
                        'value' => ($_POST["Id"]) ? $_POST["Id"] : ($_POST["node"] ? $_POST["node"] : 0)
                    )
                )
            ),
            'Cuenta' => array(
                'allowEmpty' => false
            )
        );
        parent::init();
    }

    // fin Public Init -------------------------------------------------------------------------------------------

    /**
     * Recupera el monto de una caja determinada
     * @param int $idCaja Valores que se insertaran
     * @return float
     */

    public function recuperarSaldoCaja($idCaja) {
        $cajasMovimientos = new Contable_Model_DbTable_CajasMovimientos();
        
        $sql = 'Select Sum(Monto) from CajasMovimientos where Caja = '.$idCaja;
        
        return  $this->_db->fetchOne($sql);
    }

    /**
     * Verifica si la caja solicitada puede tomar valores negativos
     * 
     * @param int $idCaja Identificador de la caja
     * @return boolean
     * 
     */
    public function permiteValoresNegativos($idCaja) {
        $R_CM = $this->fetchAll("Id = $idCaja");
        return ($R_CM->PermiteNegativo) ? true : false;
    }

}