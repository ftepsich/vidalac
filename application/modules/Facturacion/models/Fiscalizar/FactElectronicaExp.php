<?php
require_once 'Adapter.php';

/**
 * Facturacion_Model_Fiscalizar_FactElectronicaExp
 *
 * Adaptador para fiscalizar comprobantes de exportacion usando el web service de la afip wsfex
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Facturacion
 * @author Martín Alejandro Santangelo
 */
class Facturacion_Model_Fiscalizar_FactElectronicaExp extends Facturacion_Fiscalizar_Adapter_Abstract
{
    /**
     * @var bool
     */
    protected $requiereImpresion = true;

    /**
     * [$templateImpresion description]
     * @var string
     */
    public $templateImpresion = "Comp_FacturaEmitida_ElectronicaExportacion";

    /**
     * @var bool
     */
    protected $generaNumero      = true;
    /**
     * Indica si el adaptador permite refiscalizar
     */
    protected $permiteRefiscalizar = true;
    /**
     * @var array
     */
    protected $tiposComprobantes = array(
        27, 59, 61
    );

    /**
     * Fiscaliza el comprobante
     *
     * @param Rad_Db_Table_Row $comprobante
     * @throws Exception
     * @throws Facturacion_Model_Fiscalizar_Exception
     */
    public function fiscalizar(Rad_Db_Table_Row $comprobante)
    {
        // hace las verificaciones pertinentes
        $comprobantesModel = new Facturacion_Model_DbTable_Facturas();

        require_once 'FactElect/Wsfex.php';

        parent::fiscalizar($comprobante);

        $punto = $this->_getPunto($comprobante);

        // Obtenemos el tipo de comprobante Nuestro
        $tipoComprobantes = $comprobante->findParentRow('Facturacion_Model_DbTable_TiposDeComprobantes');

        if (!$tipoComprobantes) throw new Facturacion_Model_Fiscalizar_Exception('No se enconctro el tipo de comprobante');

        $afipTiposDeComprobantes = $tipoComprobantes->findParentRow('Afip_Model_DbTable_AfipTiposDeComprobantes');
        if (!$afipTiposDeComprobantes) throw new Facturacion_Model_Fiscalizar_Exception('No se enconctro el tipo de comprobante en las tablasde Afip');

        // Obtenemos la divisa nuestra
        $tipoDivisa = $comprobante->findParentRow('Base_Model_DbTable_TiposDeDivisas');
        if (!$tipoDivisa) throw new Facturacion_Model_Fiscalizar_Exception('No se enconctro el tipo de divisa');

        // Obtenemos la divisa de Afip a partir de la nuestra
        $afipDivisa = $tipoDivisa->findParentRow('Afip_Model_DbTable_AfipMonedas');
        if (!$afipDivisa) throw new Facturacion_Model_Fiscalizar_Exception('No se enconctro el tipo de divisa en las tablas de AFIP');

        // Obtengo el cliente
        $cliente = $comprobante->findParentRow('Base_Model_DbTable_Personas');
        if (!$cliente) throw new Facturacion_Model_Fiscalizar_Exception('No se encontro el cliente');

        // Datos de la exportacion
        $datosExportacion = $this->_getDatosExportacion($comprobante);

        // incoterm
        $incoterm = $datosExportacion->findParentRow('Afip_Model_DbTable_AfipIncoterms');
        if (!$incoterm) throw new Facturacion_Model_Fiscalizar_Exception('Debe cargar el Incoterm');

        // conceptos asociados
        $coneptos = $datosExportacion->findParentRow('Afip_Model_DbTable_AfipConceptosIncluidos');
        if (!$coneptos) throw new Facturacion_Model_Fiscalizar_Exception('Debe cargar el campo conceptos a exportar');

        // Cuit Pais destino
        $paisCuit = $datosExportacion->findParentRow('Base_Model_DbTable_PaisesCuit');
        if (!$paisCuit) throw new Facturacion_Model_Fiscalizar_Exception('No se encontro el registro de PaisesCuit');
        $afipPaisCuit = $paisCuit->findParentRow('Afip_Model_DbTable_AfipCuitPaises');
        if (!$afipPaisCuit) throw new Facturacion_Model_Fiscalizar_Exception('No se encontro el registro de AfipPaisesCuit');

        // Pais destino
        $pais = $datosExportacion->findParentRow('Base_Model_DbTable_Paises');
        if (!$pais) throw new Facturacion_Model_Fiscalizar_Exception('No se encontro el registro de Paises');

        $afipPais = $pais->findParentRow('Afip_Model_DbTable_AfipPaises');
        if (!$afipPais) throw new Facturacion_Model_Fiscalizar_Exception('No se encontro el registro de AfipPaises');

        // Idioma
        $idioma = $datosExportacion->findParentRow('Base_Model_DbTable_Idiomas');
        if (!$idioma) throw new Facturacion_Model_Fiscalizar_Exception('No se encontro el registro de Idiomas');

        $idiomaAfip = $idioma->findParentRow('Afip_Model_DbTable_AfipIdiomas');
        if (!$idioma) throw new Facturacion_Model_Fiscalizar_Exception('No se encontro el registro de AfipIdiomas');

        $auth = null;

        // Si ya se encuentra numerada probamos traer la info de la afip
        if ($comprobante->Numero) {
            try {
                $params = array();
                $params['Cmp'] = array();
                $params['Cmp']['Cbte_tipo'] = $afipTiposDeComprobantes->Codigo;
                $params['Cmp']['Cbte_nro']  = $comprobante->Numero;
                $params['Cmp']['Punto_vta'] = $punto;

                $auth = FactElect_Wsfex::FEXGetCMP($params);

            } catch(Exception $e) {
                // Si la factura no se encuentra en la afip simplemente ignoro el error y sigo con el proceso de registro
                if($e->getCode() != 1020) throw $e;
            }
        }


        // Si esta autorizado guardamos la info y salimos
        if ($auth) {
            $this->_persistLostResponse($auth, $comprobante);
            return;
        }

        $numeroFactura = $this->_getFacturaNumero($comprobante, $afipTiposDeComprobantes, $punto);

        // Cotizacion de la divisa, si es pesos es 1
        if ($comprobante->Divisa == 1) {
            $cotizacion = 1;
        } else {
            $cotizacion = $comprobante->ValorDivisa;
        }

        $params = new StdClass();

        // Estructura de la solicitud
        $params->Cmp->Id                = $this->_getRequestId();
        $params->Cmp->Cbte_Tipo         = $afipTiposDeComprobantes->Codigo;
        $params->Cmp->Tipo_expo         = $coneptos->Codigo;
        $params->Cmp->Punto_vta         = $punto;
        $params->Cmp->Cbte_nro          = $numeroFactura;
        $params->Cmp->Fecha_cbte        = date('Ymd', strtotime($comprobante->FechaEmision));
        $params->Cmp->Dst_cmp           = $afipPais->Codigo;
        $params->Cmp->Cliente           = $cliente->RazonSocial;
        $params->Cmp->Cuit_pais_cliente = $afipPaisCuit->Cuit;
        $params->Cmp->Domicilio_cliente = $this->_getDireccionCliente($cliente);
        $params->Cmp->Moneda_Id         = $afipDivisa->Codigo;
        $params->Cmp->Moneda_ctz        = $cotizacion;
        $params->Cmp->Imp_total         = round($comprobantesModel->recuperarMontoTotal($comprobante->Id)/$cotizacion, 2);
        $params->Cmp->Forma_pago        = $datosExportacion->FormaDePago;
        $params->Cmp->Incoterms         = $incoterm->Codigo;
        $params->Cmp->Incoterms_Ds      = substr($incoterm->Descripcion, 0, 20);
        $params->Cmp->Idioma_cbte       = $idiomaAfip->Codigo;

        $this->_setCmpRelacionado($params, $comprobante);

        if ( $comprobante->Observaciones ) $params->Cmp->Obs = $comprobante->Observaciones;

        $this->_setPermisosExportacion($params, $comprobante);

        $this->_setItems($params, $comprobante);

        try {
            $result = FactElect_Wsfex::FEXAuthorize($params);
        } catch (Exception $e){
            // Si algo falla el comprobante debe volver a tener numero 0 sino no lo fiscalizara de nuevo.
            $comprobante->getTable()->setNumeroFactura_Fiscalizador(0, $comprobante->Id);
            throw $e;
        }

        $this->_persistResponse($result, $comprobante, $params);
    }

    protected function _getPunto($comprobante)
    {
        $puntosModel    = new Base_Model_DbTable_PuntosDeVentas();
        $punto          = $puntosModel->find($comprobante->Punto)->current();
        if (!$punto) throw new Facturacion_Model_Fiscalizar_Exception('No se encontro el punto de venta');
        return $punto->Numero;
    }

    protected function _getInfoAfip($id)
    {
        $pModel         = new Facturacion_Model_DbTable_FacturacionElectronicaAfip();
        $persisitido    = $pModel->fetchRow('Comprobante = $id');
        return $persisitido;
    }

    protected function _persistResponse($result, $comprobante, $params)
    {
        $data['Cuit']        = $result->FEXResultAuth->Cuit;
        $data['Comprobante'] = $comprobante->Id;
        $data['Direccion']   = $params->Cmp->Domicilio_cliente;
        $data['PtoVta']      = $result->FEXResultAuth->Punto_vta;
        $data['CbteTipo']    = $result->FEXResultAuth->Cbte_tipo;
        $data['FchProceso']  = null;
        $data['Resultado']   = $result->FEXResultAuth->Resultado;
        $data['Concepto']    = null;
        $data['DocTipo']     = null;
        $data['DocNro']      = $result->FEXResultAuth->Cliente;
        $data['CbteDesde']   = $result->FEXResultAuth->Cbte_nro;
        $data['CbteHasta']   = $result->FEXResultAuth->Cbte_nro;
        $data['CbteFch']     = $result->FEXResultAuth->Fch_cbte;
        $data['CAE']         = $result->FEXResultAuth->Cae;
        $data['CAEFchVto']   = $result->FEXResultAuth->Fch_venc_Cae;
        $data['Obs']         = $result->FEXResultAuth->Motivos_Obs;

        $pModel = new Facturacion_Model_DbTable_FacturacionElectronicaAfip();
        $pModel->insert($data);
    }

    protected function _persistLostResponse($result, $comprobante)
    {
        $data['Cuit']        = $result->FEXResultGet->Cuit;
        $data['Comprobante'] = $comprobante->Id;
        $data['PtoVta']      = $result->FEXResultGet->Punto_vta;
        $data['Direccion']   = $result->FEXResultGet->Domicilio_cliente;
        $data['CbteTipo']    = $result->FEXResultGet->Cbte_Tipo;
        $data['FchProceso']  = null;
        $data['Resultado']   = $result->FEXResultGet->Resultado;
        $data['Concepto']    = null;
        $data['DocTipo']     = null;
        $data['DocNro']      = $result->FEXResultGet->Cliente;
        $data['CbteDesde']   = $result->FEXResultGet->Cbte_nro;
        $data['CbteHasta']   = $result->FEXResultGet->Cbte_nro;
        $data['CbteFch']     = $result->FEXResultGet->Fch_cbte;
        $data['CAE']         = $result->FEXResultGet->Cae;
        $data['CAEFchVto']   = $result->FEXResultGet->Fch_venc_Cae;
        $data['Obs']         = $result->FEXResultGet->Motivos_Obs;

        $pModel = new Facturacion_Model_DbTable_FacturacionElectronicaAfip();
        $row    = $pModel->fetchRow("Comprobante = $comprobante->Id");

        if ($row) {
            $pModel->update($data,"Id = $row->Id");
        } else {
            $pModel->insert($data);
        }
    }

    protected function _getFacturaNumero($comprobante, $afipTiposDeComprobantes, $punto)
    {
        $ultCmp = FactElect_Wsfex::FEXGetLast_CMP(
            array(
                'Auth' => array(
                    'Pto_venta'   => $punto,
                    'Cbte_Tipo'   => $afipTiposDeComprobantes->Codigo
                )
            )
        );
        $ULT_CBTE       = $ultCmp->FEXResult_LastCMP->Cbte_nro;
        $numeroFactura  = $ULT_CBTE + 1;

        // Guardamos el numero en el comprobante

        $comprobante->getTable()->setNumeroFactura_Fiscalizador($numeroFactura, $comprobante->Id);

        return $numeroFactura;
    }

    protected function _getRequestId()
    {
        $ultId  = FactElect_Wsfex::FEXGetLast_ID();
        return $ultId->FEXResultGet->Id + 1;
    }

    protected function _getDireccionCliente($cliente)
    {
        $direcciones = $cliente->findDependentRowset('Base_Model_DbTable_Direcciones');

        if (count($direcciones) == 0)
            throw new Facturacion_Model_Fiscalizar_Exception('El cliente no tiene dirección cargada y la misma es obligatoria en una factura electronica de exportación');

        $dir = '';

        foreach($direcciones as $direccion) {
            if ($direccion->TipoDeDireccion == 1) {
                $dir = Base_Model_DbTable_Direcciones::getTextDireccion($direccion);
                break;
            }
        }

        if (!$dir) {
            $dir = Base_Model_DbTable_Direcciones::getTextDireccion($direcciones[0]);
        }

        if (strlen($dir) > 300) $dir = substr($dir, 0, 300);

        return $dir;
    }

    protected function _getDatosExportacion($comprobante)
    {
        $model = new Facturacion_Model_DbTable_ComprobantesDeExportaciones;

        $datosexportacion = $model->fetchRow('Comprobante = '.$comprobante->Id);

        if (!$datosexportacion) throw new Facturacion_Model_Fiscalizar_Exception('No se encontraron datos de exportacion para el comprobante '.$comprobante->Id);

        return $datosexportacion;
    }

    protected function _setPermisosExportacion($params, $comprobante)
    {
        $modelPermisos = new Facturacion_Model_DbTable_PermisosExportaciones;

        $rowsetPermisos = $modelPermisos->fetchAll('Comprobante = '.$comprobante->Id);

        // si no tiene permisos salimos
        if (count($rowsetPermisos) == 0) {
            // si no es una factura, no lleva el campo Permiso_existente
            if ($comprobante->TipoDeComprobante != 27) $v = '';
            else $v = 'N';

            $params->Cmp->Permiso_existente = $v;
            return;
        }

        $permisos = array();

        foreach($rowsetPermisos as $perm){
            $pais = $perm->findParentRow('Base_Model_DbTable_Paises');
            $afippais = $pais->findParentRow('Afip_Model_DbTable_AfipPaises');

            $p = array(
                'Id_permiso' => $perm->NroPermiso,
                'Dst_merc'   => $afippais->Codigo
            );
            $permisos[0] = $p;
        }
        $params->Cmp->Permiso_existente = 'S';
        $params->Cmp->Permisos = $permisos;
    }

    protected function _setItems($params, $comprobante)
    {
        $modelFactVentArt = new Facturacion_Model_DbTable_FacturasVentasArticulos(array(), true);

        $modelArticulos   = new Base_Model_DbTable_Articulos;

        $articulos = $modelFactVentArt->fetchAll('Comprobante = '.$comprobante->Id);

        if (count($articulos) == 0) throw new Facturacion_Model_Fiscalizar_Exception('No se econtraron artículos en el comprobante');

        $items = array();

        foreach($articulos as $art) {
            $a = array();

            // Si tiene articulo
            if ($art->Articulo) {

                $articulo = $modelArticulos->find($art->Articulo)->current();
                if (!$articulo) throw new Facturacion_Model_Fiscalizar_Exception('No se econtraro el artículo '.$art->Articulo);

                $unidadDeMedida = $articulo->findParentRow('Base_Model_DbTable_UnidadesDeMedidas');
                if (!$unidadDeMedida) throw new Facturacion_Model_Fiscalizar_Exception('No se econtraro la unidad de medida ');

                $afipUnidadDeMedida = $unidadDeMedida->findParentRow('Afip_Model_DbTable_AfipUnidadesDeMedidas');
                if (!$afipUnidadDeMedida) throw new Facturacion_Model_Fiscalizar_Exception('No se econtraro la unidad de medida de afip');

                $a['Pro_umed'] = $afipUnidadDeMedida->Codigo;
            } else {
                $a['Pro_umed'] = 7;
            }

            if (((float) $art->PrecioUnitarioMExtranjera) == 0 ) {
                $monto = $art->PrecioUnitario;
            } else {
                $monto = $art->PrecioUnitarioMExtranjera;
            }

            // calculamos el descuento si lo tiene
            if ($art->DescuentoEnPorcentaje) {
                $bonificacion = ($art->DescuentoEnPorcentaje / 100 * $monto) * $art->Cantidad;
                $bonificacion = round($bonificacion, 6);
            } else {
                $bonificacion = 0;
            }

            $a['Pro_codigo']      = $art->ArticulosCodigo;
            $a['Pro_ds']          = $art->ArticulosDescArreglada;
            $a['Pro_qty']         = $art->Cantidad;
            $a['Pro_precio_uni']  = $monto;
            $a['Pro_total_item']  = round(($monto * $art->Cantidad) - $bonificacion, 2);

            if ($bonificacion) $a['Pro_bonificacion'] = $bonificacion;
            else $a['Pro_bonificacion'] = 0;

            $items[] = $a;
        }

        $params->Cmp->Items = $items;
    }

    protected function _setCmpRelacionado(&$params, $comprobante)
    {
        // si el comprobante tiene relacionado otro
        if ($comprobante->ComprobanteRelacionado) {
            $modelFEA = new Facturacion_Model_DbTable_FacturacionElectronicaAfip;

            $rowFe = $modelFEA->fetchRow('Comprobante = '.$comprobante->ComprobanteRelacionado);

            $cmps = array();
            $cmps[0]->Cbte_tipo      = $rowFe->CbteTipo;
            $cmps[0]->Cbte_punto_vta = $rowFe->PtoVta;
            $cmps[0]->Cbte_nro       = $rowFe->CbteDesde;
            $cmps[0]->Cbte_cuit      = $rowFe->Cuit;
            $params->Cmp->Cmps_asoc = $cmps;

        }
    }
}
