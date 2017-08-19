<?php
/**
 * Model_Fiscalizar
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Facturacion
 * @author Martin Alejandro Santangelo
 */

/**
 * Model_Fiscalizar
 *
 * Fiscaliza un comprobante utilizando el adaptador
 * correspondiente al punto de venta
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Facturacion
 * @author Martin Alejandro Santangelo
 */
class Facturacion_Model_Fiscalizar
{

    /**
     * Fiscaliza el comprobante enviado
     *
     * @param Rad_Db_Table_Row $comprobante
     */
    public static function fiscalizar(Rad_Db_Table_Row $comprobante)
    {
        $adapter = self::getAdapter($comprobante);
        $adapter->fiscalizar($comprobante);
    }

    /**
     * Abrir comprobante (para los que necesitan Concomitancia)
     *
     * @param  Rad_Db_Table_Row $comprobante [description]
     * @return int numero de factura
     */
//    public static function abrirComprobante(Rad_Db_Table_Row $comprobante)
//    {
//        $adapter = self::getAdapter($comprobante);
//        return $adapter->abrirComprobante($comprobante);
//    }

    /**
     * agregar item al comprobante abierto
     * @param  Rad_Db_Table_Row $item        Item
     */
//    public static function agregarItem(Rad_Db_Table_Row $item, Rad_Db_Table_Row $comprobante)
//    {
//        $adapter = self::getAdapter($comprobante);
//        $adapter->agregarItem($item, $comprobante);
//    }

//    public static function agregarPago($desc, $monto, Rad_Db_Table_Row $comprobante, $tarjeta = '')
//    {
//        $adapter = self::getAdapter($comprobante);
//        $adapter->agregarPago($desc, $monto, $comprobante, $tarjeta);
//    }

//    public static function anular($comprobante)
//    {
//        $adapter = self::getAdapter($comprobante);
//        $adapter->anular($comprobante);
//    }

    /**
     * Fiscaliza el comprobante enviado
     *
     * SE USA SOLAMENTE EN CASO DE FALLOS Y Q EL COMPROBANTE HAYA SIDO CERRADO
     * POR EJEMPLO EN ADAPTADOR DE FACTURAS PREIMPRESAS SI OCURREN UN ERROR CON
     * LA IMPRESION EL SISTEMA AUN CIERRA EL COMPROBANTE
     *
     * @param Rad_Db_Table_Row $comprobante
     */
    public static function refiscalizar(Rad_Db_Table_Row $comprobante)
    {
        $adapter = self::getAdapter($comprobante);
        if (!$adapter->getPermiteRefiscalizar()) {
            throw new Facturacion_Model_Fiscalizar_Exception('El Punto de Venta no soporta Refiscalizar');
        }
        $adapter->fiscalizar($comprobante);
    }
    /**
     * Obtiene el adaptador necesario para fiscalizar este comprobante
     *
     * @param Rad_Db_Table_Row $comprobante
     */
    protected static function getAdapter(Rad_Db_Table_Row $comprobante)
    {
        $adatadores   = new Base_Model_DbTable_AdaptadoresFiscalizaciones();
        $puntosVentas = new Base_Model_DbTable_PuntosDeVentas();

        $punto = $puntosVentas->find($comprobante->Punto)->current();
        if (!$punto) throw new Facturacion_Model_Fiscalizar_Exception('El punto de venta de este comprobante no se encontro');

        $adaptador = $adatadores->find($punto->Adaptador)->current();
        if (!$adaptador) throw new Facturacion_Model_Fiscalizar_Exception('El adaptador de este comprobante no se encontro');
        $class    = $adaptador->Class;
        $instance = new $class();
        return $instance;
    }
}