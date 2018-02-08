<?php

/**
 * Comprobantes Bancarios
 * 
 * @class       Facturacion_Model_DbTable_ComprobantesBancarios
 * @extends     Facturacion_Model_DbTable_FacturasCompras
 * @package     Aplicacion
 * @subpackage  Facturacion
 *
 */
class Facturacion_Model_DbTable_ComprobantesBancarios extends Facturacion_Model_DbTable_FacturasCompras
{
    protected $_permanentValues = array(
        'TipoDeComprobante' => array(47, 49, 50)
    );

    public function init()
    {
        $this->_referenceMap['TiposDeComprobantes']['comboSource'] = 'datagateway/combolist/fetch/LiquidacionesYGastosBancarios';
        $this->_referenceMap['CuentasBancarias']                   = array(
            'columns'        => 'CuentaBancaria',
            'refTableClass'  => 'Base_Model_DbTable_CuentasBancarias',
            'refJoinColumns' => array('Cbu'),
            'comboBox'       => true,
            'comboSource'    => 'datagateway/combolist/fetch/EsPropia',
            'refTable'       => 'CuentasBancarias',
            'refColumns'     => 'Id',
        );
        $this->_referenceMap['Personas']                   = array(
            'columns' => 'Persona',
            'refTableClass' => 'Base_Model_DbTable_Personas',
            'refJoinColumns' => array('RazonSocial'),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist/fetch/EsBanco',
            'refTable' => 'Personas',
            'refColumns' => 'Id',
            'comboPageSize' => 10
        );        
        $this->_defaultValues['CondicionDePago'] = 1;
        $this->_validators['CuentaBancaria']     = array(
            'NotEmpty',
            'allowEmpty' => false,
            'messages'   => array('Falta ingresar la Cuenta Bancaria')
        );

        parent::init();
    }

    /**
     *  Update
     *
     * @param array $data   Valores que se cambiaran
     * @param array $where  Registros que se deben modificar
     *
     */
    public function update($data, $where)
    {
        try {
            $this->_db->beginTransaction();
            $M_CC = new Facturacion_Model_DbTable_ComprobantesCheques(array(), false);

            $reg = $this->fetchAll($where);

            foreach ($reg as $row_cb) {
                if($data['CuentaBancaria'] && ($row_cb->CuentaBancaria != $data['CuentaBancaria'])) {
                    $R_CC = $M_CC->fetchAll("Comprobante = $row_cb->Id");
                    if ($R_CC) {
                        foreach ($R_CC as $row_cc) {
                            $M_CC->delete("Id = ".$row_cc->Id);
                        }
                    }
                }  
            }   

            parent::update($data, $where);

            $this->_db->commit();

            return true;
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    public function eliminarDetalle($idFactura)
    {
        $this->_db->beginTransaction();
        try {
            $M_CBA = new Facturacion_Model_DbTable_ComprobantesBancariosArticulos(array(), false);

            $R_CB = $this->find($idFactura)->current();

            $R_CBA = $R_CB->findDependentRowset('Facturacion_Model_DbTable_ComprobantesBancariosArticulos',
                            'FacturaCompra');

            if ($R_CBA) {
                foreach ($R_CBA as $row) {
                    $M_CBA->forceDelete($row->Id);
                }
            }
            $this->_db->commit();
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    /**
     * Permite cerrar un Comprobante y los comprobantes Hijos
     *
     * @param int $idComprobante    identificador del Comprobante a cerrar
     *
     */
    public function cerrar ($idComprobante)
    {
        try {
            // Inicio despues la transaccion ya q el fiscalizador debe poder modificar datos sin q despues se realice un rollback
            $this->_db->beginTransaction();

            $R_C = $this->find($idComprobante)->current();

            // Controles para liquidaciones bancarias
            if($this->recuperarGrupoComprobante($R_C) == 16) {
                $this->salirSi_noTieneChequesAsociados($idComprobante);
            }
            parent::cerrar($idComprobante);
            
            $this->_db->commit();
            return true;
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }


}