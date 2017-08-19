<?php

/**
 * Contiene la logica para verificar y retornar el estado Financiero de una persona con la empresaa
 */
class Contable_Model_EstadosCuentasPersonas
{
    public function exedeLimiteDeCredito ($persona, $monto)
    {
        $cc = new Contable_Model_DbTable_CuentasCorrientes();

        if (is_int($persona)) {
            $p = new Model_DbTable_Personas();
            $persona = $p->find($idPersona)->current();
        }

        if (!$persona->LimiteDeCredito) return false;

        $saldo = $cc->getSaldo($persona->Id);

        return $persona->LimiteDeCredito < ($saldo + $monto);
    }

    /**
     * Verifica si la Factura no excede con el limite de credito
     */
    public function verificarLimiteDeCredito ($rowComp)
    {
        // si no es factura venta retorno
        $tipoDeComprobante = $rowComp->findParentRow('Facturacion_Model_DbTable_TiposDeComprobantes');
        if ($tipoDeComprobante->Grupo != 6) return;

        $persona = $rowComp->findParentRow('Base_Model_DbTable_Personas');

        $model = $rowComp->getTable();

        $monto = $model->recuperarMontoTotal($rowComp->Id);

        if ($this->exedeLimiteDeCredito($persona, $monto)) {
            throw new Rad_Exception('El comprobante excede el limite de credito del cliente');
        }
    }

    /**
     * Devuelve el estado financiero real de una persona a determinada fecha
     */
    public function estadoFinancieroAFecha ($idPersona, $fecha = null)
    {
        $db = Zend_Registry::get('db');

        if (!$fecha) {
            $fecha = '9999-12-31';
        }
        $sql = 'SELECT fPersona_Cuenta_Saldo_A_Fecha(?, ?) AS saldo';
        $params = array($idPersona, $fecha);
        $rowset = $db->query($sql, $params);
        $row = $rowset->fetch();

        return $row['saldo'];
    }

    /**
     * Devuelve el estado de cuenta corriente de una persona a determinada fecha
     */
    public function cuentaCorrienteAFecha ($idPersona, $fecha = null)
    {
        $db = Zend_Registry::get('db');

        $sql = 'SELECT IFNULL(SUM(Haber) - SUM(Debe), 0) AS saldo
                FROM CuentasCorrientes
                WHERE Persona = ? ';
        
        $params = array($idPersona);
        if ($fecha) {
            $sql .= 'AND FechaComprobante <= ?';
            $params[] = $fecha;
        }
        $rowset = $db->query($sql, $params);
        $row = $rowset->fetch();

        return $row['saldo'];
    }
}