<?php

/**
 * Facturacion_Model_DbTable_TiposDeComprobantes
 *
 * Tipos de comprobantes
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Facturacion
 */
class Facturacion_Model_DbTable_TiposDeComprobantes extends Rad_Db_Table
{

    // Tabla mapeada
    protected $_name = 'TiposDeComprobantes';
    // Relaciones
    // Inicio  protected $_referenceMap --------------------------------------------------------------------------
    protected $_referenceMap = array(
        'TiposDeGruposDeComprobantes' => array(
            'columns'        => 'Grupo',
            'refTableClass'  => 'Facturacion_Model_DbTable_TiposDeGruposDeComprobantes',
            'refJoinColumns' => array("Codigo"),
            'comboBox'       => true,
            'comboSource'    => 'datagateway/combolist',
            'refTable'       => 'TiposDeGruposDeComprobantes',
            'refColumns'     => 'Id'
        ),
        'AfipTiposDeComprobantes' => array(
            'columns'        => 'Afip',
            'refTableClass'  => 'Afip_Model_DbTable_AfipTiposDeComprobantes',
            'refJoinColumns' => array("Codigo"),
            'comboBox'       => true,
            'comboSource'    => 'datagateway/combolist',
            'refTable'       => 'AfipTiposDeComprobantes',
            'refColumns'     => 'Id'
        ),
    );

    protected $_dependentTables = array(
        'Facturacion_Model_DbTable_FacturasCompras',
        'Facturacion_Model_DbTable_FacturasVentas',
        'Facturacion_Model_DbTable_Facturas'
    );

    public function fetchNotasProveedores($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = " TiposDeComprobantes.Grupo = 2";
        $where = $this->_addCondition($where, $condicion);
        return parent:: fetchAll($where, $order, $count, $offset);
    }

    public function fetchFacturasCompras($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = " TiposDeComprobantes.Grupo = 1";
        $where = $this->_addCondition($where, $condicion);
        return parent:: fetchAll($where, $order, $count, $offset);
    }

    public function fetchFacturasVentas($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = " (TiposDeComprobantes.Grupo = 6) and TiposDeComprobantes.Id not in (26,28)";
        $where = $this->_addCondition($where, $condicion);
        return parent:: fetchAll($where, $order, $count, $offset);
    }

    public function fetchFacturasVentasNotasEmitidas($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = " TiposDeComprobantes.Grupo in(6,7,12) and TiposDeComprobantes.Id not In (26,28,31,32,39,40,52,54,56,65,66)";
        $where = $this->_addCondition($where, $condicion);
        return parent:: fetchAll($where, $order, $count, $offset);
    }

    public function fetchPuntoDeVenta($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = " TiposDeComprobantes.Grupo in(7,12) Or TiposDeComprobantes.Id In (52,54,56)";
        $where = $this->_addCondition($where, $condicion);
        return parent:: fetchAll($where, $order, $count, $offset);
    }

    public function fetchFacturasComprasNotasRecibidas($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = "TiposDeComprobantes.Grupo in (1, 8, 13) and TiposDeComprobantes.Id not in (20,34,42)";
        $where = $this->_addCondition($where, $condicion);
        return parent:: fetchAll($where, $order, $count, $offset);
    }

    public function fetchLiquidacionesYGastosBancarios($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = "TiposDeComprobantes.Grupo in (14, 15, 16)";
        $where = $this->_addCondition($where, $condicion);
        return parent:: fetchAll($where, $order, $count, $offset);
    }

    public function fetchComprobantesDeIngreso($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = "TiposDeComprobantes.Grupo in (1, 8, 13, 14, 15, 16) and TiposDeComprobantes.Id not in (20,34,42))";
        $where = $this->_addCondition($where, $condicion);
        return parent:: fetchAll($where, $order, $count, $offset);
    }

    public function fetchEsRemito($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = " TiposDeComprobantes.Grupo = 4";
        $where = $this->_addCondition($where, $condicion);
        return self::fetchAll($where, $order, $count, $offset);
    }

    public function fetchEsRemitoDeSalida($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = " TiposDeComprobantes.Grupo = 10";
        $where = $this->_addCondition($where, $condicion);
        return self::fetchAll($where, $order, $count, $offset);
    }

    public function fetchEsRemitoDeSalidaComun($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = " TiposDeComprobantes.Grupo = 10 AND TiposDeComprobantes.Id <> 15";
        $where = $this->_addCondition($where, $condicion);
        return self::fetchAll($where, $order, $count, $offset);
    }

    public function fetchEsRecibo($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = " TiposDeComprobantes.Id = 48";
        $where = $this->_addCondition($where, $condicion);
        return self::fetchAll($where, $order, $count, $offset);
    }

    public function fetchEsOrdenDePago($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = " TiposDeComprobantes.Id = 7";
        $where = $this->_addCondition($where, $condicion);
        return self::fetchAll($where, $order, $count, $offset);
    }

    public function fetchEsOrdenDePagoSinIVA($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = " TiposDeComprobantes.Id = 69";
        $where = $this->_addCondition($where, $condicion);
        return self::fetchAll($where, $order, $count, $offset);
    }

    public function fetchComprobantesSinIVA($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = " TiposDeComprobantes.Grupo = 21";
        $where = $this->_addCondition($where, $condicion);
        return self::fetchAll($where, $order, $count, $offset);
    }
}
