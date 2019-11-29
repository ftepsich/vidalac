<?php

/**
 * Comprobantes
 *
 * @package 	Aplicacion
 * @subpackage 	Facturacion
 * @class 	Facturacion_Model_DbTable_Comprobantes
 * @extends	Rad_Db_Table
 */
class Facturacion_Model_DbTable_Comprobantes extends Rad_Db_Table
{

    /*
    protected static $IDCONCEPTO_1050   = 30;
    protected static $IDCONCEPTO_2100   = 29;
    protected static $IDCONCEPTO_2700   = 31;

    protected static $IDAFIP_1050   = 4;
    protected static $IDAFIP_2100   = 5;
    protected static $IDAFIP_2700   = 6;
    */

    // Tabla mapeada
    protected $_sort = array("Id desc");
    protected $_name = 'Comprobantes';

    // Relaciones
    protected $_referenceMap = array(
        'ConceptosImpositivos' => array(
            'columns'        => 'ConceptoImpositivo',
            'refTableClass'  => 'Base_Model_DbTable_ConceptosImpositivos',
            'refJoinColumns' => array('Descripcion'),
            'comboBox'       => true,
            'comboSource'    => 'datagateway/combolist',
            'refTable'       => 'ConceptosImpositivos',
            'refColumns'     => 'Id',
        ),
        'TiposDeDivisas' => array(
            'columns'        => 'Divisa',
            'refTableClass'  => 'Base_Model_DbTable_TiposDeDivisas',
            'refJoinColumns' => array('Descripcion'),
            'comboBox'       => true,
            'comboSource'    => 'datagateway/combolist',
            'refTable'       => 'TiposDeDivisas',
            'refColumns'     => 'Id',
        ),
        'TiposDeComprobantes' => array(
            'columns'        => 'TipoDeComprobante',
            'refTableClass'  => 'Facturacion_Model_DbTable_TiposDeComprobantes',
            'refJoinColumns' => array('Descripcion'),
            'comboBox'       => true,
            'comboSource'    => 'datagateway/combolist',
            'refTable'       => 'TiposDeComprobantes',
            'refColumns'     => 'Id',
        ),
        'Personas' => array(
            'columns'        => 'Persona',
            'refTableClass'  => 'Base_Model_DbTable_Personas',
            'refJoinColumns' => array('RazonSocial'),
            'comboBox'       => true,
            'comboSource'    => 'datagateway/combolist',
            'refTable'       => 'Personas',
            'refColumns'     => 'Id',
            'comboPageSize'  => 10
        ),
        'Clientes' => array(
            'columns'       => 'Persona',
            'refTableClass' => 'Base_Model_DbTable_Clientes',
            'refTable'      => 'Personas',
            'refColumns'    => 'Id'
        ),
        'LibrosIVA' => array(
            'columns'        => 'LibroIVA',
            'refTableClass'  => 'Contable_Model_DbTable_LibrosIVA',
            'refJoinColumns' => array('Descripcion'),
            'comboBox'       => true,
            'comboSource'    => 'ddatagateway/combolist/fetch/Abiertos',
            'refTable'       => 'LibrosIVA',
            'refColumns'     => 'Id'
        ),
        'ComprobantesRelacionados' => array(
            'columns'        => 'ComprobanteRelacionado',
            'refTableClass'  => 'Facturacion_Model_DbTable_Comprobantes',
            'refJoinColumns' => array('Numero'),
            'comboBox'       => true,
            'comboSource'    => 'datagateway/combolist',
            'refTable'       => 'Comprobantes',
            'refColumns'     => 'Id',
            'comboPageSize'  => 10
        ),
        'ComprobantesPadres' => array(
            'columns'        => 'ComprobantePadre',
            'refTableClass'  => 'Facturacion_Model_DbTable_Comprobantes',
            'refJoinColumns' => array('Numero'),
            'comboBox'       => true,
            'comboSource'    => 'datagateway/combolist',
            'refTable'       => 'Comprobantes',
            'refColumns'     => 'Id',
        ),
        'DepositoTercero' => array(
            'columns'        => 'DepositoEntrega',
            'refTableClass'  => 'Base_Model_DbTable_Depositos',
            'refJoinColumns' => array("Direccion"),
            'comboBox'       => true,
            'comboSource'    => 'datagateway/combolist',
            'refTable'       => 'Direcciones',
            'refColumns'     => 'Id',
            'comboPageSize'  => 10
        )
    );

    public function init()
    {
        parent::init();
        $this->_defaultValues['EsCliente']   = 0;
        $this->_defaultValues['EsProveedor'] = 0;
    }
    protected $_dependentTables = array(
        'Facturacion_Model_DbTable_FacturasComprasArticulos',
        'Facturacion_Model_DbTable_FacturasVentasArticulos',
        'Facturacion_Model_DbTable_ComprobantesImpositivos',
    );

    protected $_calculatedFields = array(
        'NumeroCompleto' => "fNumeroCompleto(Comprobantes.Id,'') COLLATE utf8_general_ci"
    );

    /**
     * Selecciona el libro de iva al cual debe asignarse un comprobante
     *
     * @param date $fechaEmision fecha de emision del comprobante
     *
     * @return int
     */
    protected function seleccionarLibroIVA ($fechaEmision)
    {
        $Libro = 0;
        //$Fecha = new Zend_Date();
        $M_LIVA = new Contable_Model_DbTable_LibrosIVA(array(), false);
        // Busco el libro correcto para esa factura
            $clausula = " mes = " . date("m", strtotime($fechaEmision)) . " and anio = " . date("Y", strtotime($fechaEmision));
        $R_Libro = $M_LIVA->fetchRow($clausula);
        if (!$R_Libro) {
            if (Rad_Confirm::confirm( "No Existe el Libro de IVA del mes ".date("m", strtotime($fechaEmision)).". Desea crearlo?", _FILE_._LINE_, array('includeCancel' => false)) == 'yes') {
                $R_Libro->Id = $M_LIVA->crearLibroIVA(date("m", strtotime($fechaEmision)),date("Y", strtotime($fechaEmision)));
            }
        }

        // Reviso si el libro correspondiente esta cerrado
        if ($R_Libro->Cerrado == 1) {
            // Busco el libro mas viejo abierto
            $R_LibroMasViejo = $M_LIVA->fetchRow("Cerrado = 0", array("Anio asc", "Mes asc"));
            if ($R_LibroMasViejo) {
                $Libro = $R_LibroMasViejo->Id;
            } else {
                throw new Rad_Db_Table_Exception("No existe un libro de IVA al que asignar la factura. Revise si no debe crear el libro de este mes desde el menu Contable.");
            }
        } else {
            $Libro = $R_Libro->Id;
        }
        return $Libro;
    }

    /**
     * Selecciona el periodo de imputacion sin iva al cual debe asignarse un comprobante
     *
     * @param date $fechaEmision fecha de emision del comprobante
     *
     * @return int
     */
    protected function seleccionarPeriodoImputacionSinIVA ($fechaEmision)
    {
        $Periodo = 0;
        //$Fecha = new Zend_Date();
        $M_PIVA = new Contable_Model_DbTable_PeriodosImputacionSinIVA(array(), false);
        // Busco el periodo correcto para ese comprobante
        $clausula = " mes = " . date("m", strtotime($fechaEmision)) . " and anio = " . date("Y", strtotime($fechaEmision));
        $R_Periodo = $M_PIVA->fetchRow($clausula);
        if (!$R_Periodo) {
            if (Rad_Confirm::confirm( "No Existe el Periodo de ImputaciÃ³n del mes ".date("m", strtotime($fechaEmision)).". Desea crearlo?", _FILE_._LINE_, array('includeCancel' => false)) == 'yes') {
                $R_Periodo->Id = $M_PIVA->crearPeriodo(date("m", strtotime($fechaEmision)),date("Y", strtotime($fechaEmision)));
            }
        }

        // Reviso si el periodo correspondiente esta cerrado
        if ($R_Periodo->Cerrado == 1) {
            // Busco el periodo mas viejo abierto
            $R_PeriodoMasViejo = $M_PIVA->fetchRow("Cerrado = 0", array("Anio asc", "Mes asc"));
            if ($R_PeriodoMasViejo) {
                $Periodo = $R_PeriodoMasViejo->Id;
            } else {
                throw new Rad_Db_Table_Exception("No existe un periodo de imputacion al que asignar el comprobante. Revise si no debe crear el periodo de imputaciÃ³n de este mes desde el menu Contable.");
            }
        } else {
            $Periodo = $R_Periodo->Id;
        }
        return $Periodo;
    }

    /**
     * Permite borrar los conceptos impositivos que sean hijos de un comprobante
     *
     * @param int $idComprobante 	identificador del comprobante
     * @param int $BorrarModificados 1/0 indica si se deben borrar los conceptos modificados manualmente
     *
     * @return boolean
     */
    protected function _eliminarConceptosHijos ($idComprobante, $BorrarModificados)
    {
        // comentado pq es un proceso interno y no deberia hacer este chequeos
        //        $this->salirSi_estaCerrado($idComprobante);

        $M_CIH = new Facturacion_Model_DbTable_ComprobantesImpositivos(array(), false);
        $R_CIH = $M_CIH->fetchAll("ComprobantePadre=$idComprobante");

        // Si hay conceptos los borro
        if (count($R_CIH)) {
            foreach ($R_CIH as $row) {
                if ($this->_esConceptoImpositivo($row->TipoDeComprobante) &&
                        ($BorrarModificados || !$row->Modificado)) {
                    Rad_PubSub::publish('CoI_preBorrarConcepto', $row);
                    Rad_Log::debug('_eliminarConceptosHijos');
                    $M_CIH->forceDelete($row->Id);
                    Rad_PubSub::publish('CoI_posBorrarConcepto', $row);
                }
            }
        }
    }

    /**
     * Cambia la imputacion de un comprobante al libro de iva de otro mes
     *
     * @param type $idComprobante
     * @param type $IdLibroIVA
     */
    public function cambiarImputacionIVA($idComprobante, $IdLibroIVA)
    {
        $idComprobante = $this->_db->quote($idComprobante,'INTEGER');
        $IdLibroIVA    = $this->_db->quote($IdLibroIVA,'INTEGER');

        $comprobante = $this->find($idComprobante)->current();

        if (!$comprobante) {
            throw new Rad_Db_Table_Exception("No se encontro el comprobante $idComprobante al que intenta cambiarle la imputacion IVA");
        }

        if (!$comprobante->Cerrado) {
            throw new Rad_Db_Table_Exception("El comprobante no esta cerrado");
        }

        $libroDeIva = $comprobante->findParentRow('Contable_Model_DbTable_LibrosIVA');

        if (!$libroDeIva) {
            throw new Rad_Db_Table_Exception("No se encontro el libro IVA $idComprobante original del comprobante.");
        }

        if ($libroDeIva->Cerrado) {
            throw new Rad_Db_Table_Exception("El comprobante se encuentra imputado a un libro de IVA cerrado ($libroDeIva->Descripcion). No puede ser modificado.");
        }

        $modelLibroIva = $libroDeIva->getTable();

        $libroIvaDestino = $modelLibroIva->find($IdLibroIVA)->current();

        if (!$libroIvaDestino) {
            throw new Rad_Db_Table_Exception("No se encontro el libro IVA $idComprobante al que intenta imputar el comprobante.");
        }

        if ($libroIvaDestino->Cerrado) {
            throw new Rad_Db_Table_Exception("El Libro de IVA al que intenta imputar el comprobante esta cerrado.");
        }
        //
        Rad_Db_Table::update(array('LibroIVA'=>$IdLibroIVA),'Id = '.$idComprobante);

        // Cambio el Libro a sus hijos (conceptos) USO UNA NUEVA INSTANCIA DE COMPROBANTES PARA QUE NO USE LOS PERMANENT
        $M_C = new Facturacion_Model_DbTable_Comprobantes();
        $M_C->update(
            array(
                'LibroIVA' => $IdLibroIVA
            ),
            ' Comprobantes.ComprobantePadre = ' . $idComprobante
        );

        // Buscamos el comprobante ya modificado
        $comprobante = $this->find($idComprobante)->current();

        $modelLibroIvaDet = new Contable_Model_DbTable_LibrosIVADetalles();
        $modelLibroIvaDet->asentarLibroIVA($comprobante);
    }

    public function cambiarTipoComprobante($row, $tipo)
    {
        Rad_Db_Table::update(['TipoDeComprobante' => $tipo], 'Id ='.$row->Id );

        $comp = $row->getTable()->find($row->Id)->current();

        $ctacte = new Contable_Model_DbTable_CuentasCorrientes();
        // actualizamos el registro en la cta cte
        $ctacte->asentarComprobante($comp);
    }

    /**
     * Permite asignar los Comprobantes Impositivos como formas de pago,
     * tambien sirve para acturlizar un comprobante ya asignado como forma de pago
     *
     * @param int $idComprobantePadre 	identificador del comprobante Padre (OP,Recibo)
     * @param int $idComprobanteImp 	identificador del comprobante a usar como forma de pago
     * @param decimas $Monto 	        Monto del Concepto impositivo
     *
     * @return boolean
     */
    public function reasignarCIcomoFormaDePago ($idComprobantePadre, $idComprobanteImp, $Monto)
    {
        // Armo un array del detalle
        $Renglon = array(
            'Comprobante' => $idComprobantePadre,
            'ComprobanteRelacionado' => $idComprobanteImp,
            'Observacion' => 'Retencion', //TODO: hacer q busque bien la descripcion
            'PrecioUnitario' => $Monto,
            'Cantidad' => 1
        );
        $M_CD = new Facturacion_Model_DbTable_ComprobantesDetalles(array(), false);
        $R_CD = $M_CD->fetchRow("Comprobante = $idComprobantePadre and ComprobanteRelacionado = $idComprobanteImp");
        // Si existe updateo sino inserto
        if ($R_CD) {
            $M_CD->update($Renglon, "Id = $R_CD->Id");
        } else {
            $id = $M_CD->insert($Renglon);
        }
    }

    /**
     * Permite cerrar los conceptos impositivos que sean hijos de un comprobante
     *
     * @param int $idComprobante 	identificador del comprobante
     *
     * @return boolean
     */
    protected function _cerrarConceptosHijos ($idComprobante)
    {
        $this->salirSi_estaCerrado($idComprobante);

        $M_CIH = new Facturacion_Model_DbTable_ComprobantesImpositivos(array(), false);
        $R_CIH = $M_CIH->fetchAll("ComprobantePadre=$idComprobante");

        // Si hay conceptos los cierro
        if (!empty($R_CIH)) {
            foreach ($R_CIH as $row) {
                if ($row->TipoDeComprobante == 10) {
                    $M_CIH->cerrarConcepto($row);
                }
            }
        }
    }

    /**
     * elimina el detalle de un comprobante
     *
     * @param int $idComprobante 	identificador del comprobante
     *
     * @return boolean
     */
    public function eliminarDetalle ($idComprobante)
    {
        $M_CD  = new Facturacion_Model_DbTable_ComprobantesDetalles(array(), false);
        $M_C   = new Base_Model_DbTable_Cheques(array(), false);
        $M_TB  = new Base_Model_DbTable_TransaccionesBancarias(array(), false);
        $M_TCC = new Facturacion_Model_DbTable_TarjetasDeCreditoCupones;

        $R_CD = $M_CD->fetchAll("Comprobante = $idComprobante");

        if ($R_CD) {
            foreach ($R_CD as $row) {
				// Marcamos los cheques
                if($row->Cheque){
                    $R_C = $M_C->find($row->Cheque)->current();
                    $R_C->ChequeEstado = 6;
                    $R_C->setReadOnly(false);
                    $R_C->save();
                }
				// Marcamos las transacciones Bancarias
                if($row->TransaccionBancaria){
                    $R_TB = $M_TB->find($row->TransaccionBancaria)->current();
                    $R_TB->Utilizado = 0;
                    $R_TB->setReadOnly(false);
                    $R_TB->save();
                }
                // Marcamos las transacciones Bancarias
                if($row->TarjetaDeCreditoCupon){
                    $R_TCC = $M_TCC->find($row->TarjetaDeCreditoCupon)->current();
                    $R_TCC->Utilizado = 0;
                    $R_TCC->setReadOnly(false);
                    $R_TCC->save();
                }

                $where = "Id = " . $row->Id;
                $M_CD->delete($where);
            }
        }
    }

    /**
     * elimina los comprobantes hijos del comprobante indicado
     *
     * @param int $idComprobante 	identificador del comprobante
     *
     * @return boolean
     */
    public function eliminarComprobantesHijos ($idComprobante)
    {
//        $this->salirSi_estaCerrado($idComprobante);

        $M_C = new Facturacion_Model_DbTable_Comprobantes();
        $Comprobante = $M_C->find($idComprobante)->current();

        $M_CR = new Facturacion_Model_DbTable_ComprobantesRelacionados(array(), false);
        $M_CR->eliminarRelacionesHijos($Comprobante);

        $M_CC = new Facturacion_Model_DbTable_ComprobantesCheques(array(), false);
        $M_CC->eliminarRelacionesHijosCheques($Comprobante);
    }

    /**
     * Recupera el Neto Gravado de un comprobante, en el caso que se especifique un concepto impositivo
     * recupera el Neto Gravado de los articulos que
     *
     * @param int $idComprobante identificador del comprobante
     *
     * @return decimal
     */
    public function recuperarNetoGravado ($idComprobante, $idConcepto = null)
    {
        if ($idConcepto) {
            $txtSQL = "SELECT fComprobante_NetoGravado_xCI($idComprobante,$idConcepto)";
        } else {
            $txtSQL = "SELECT fComprobante_NetoGravado($idComprobante)";
        }

        return  $this->_db->fetchOne($txtSQL);
    }

    public function recuperarNetoGravado_old ($idComprobante, $idConcepto = null) {
        $tieneDescuentoGeneral = 0;
        $dGral_Monto = 0;
        $dGral_Porcentaje = 0;
        $NetoGravadoGeneral = 0;

        $M_C = $this;

        // Recupero los descuentos Generales en la cabecera si es que hay
        $R_C  = $M_C->find($idComprobante)->current();
        $R_TC = $R_C->findParentRow('Facturacion_Model_DbTable_TiposDeComprobantes');


        if ($R_C) {
            if ($R_C->DescuentoEnMonto || $R_C->DescuentoEnPorcentaje) {
                $tieneDescuentoGeneral = 1;
                $dGral_Monto = $R_C->DescuentoEnMonto;
                $dGral_Porcentaje = $R_C->DescuentoEnPorcentaje;
            }
        }

        // Recupero el detalle del comprobante
        $M_CD = new Facturacion_Model_DbTable_ComprobantesDetalles(array(), false);

        if ($idConcepto) {
            $where = "ConceptoImpositivo = $idConcepto and Comprobante = $idComprobante";
            $NetoGravadoGeneral = $M_C->recuperarNetoGravado($idComprobante);
        } else {
            $where = "Comprobante = $idComprobante";
        }

        $R = $M_CD->fetchAll($where);

        if (count($R)) {
            if ($tieneDescuentoGeneral) {

                // Discrimina Impuesto ?
                // El neto grabado de comprobantes q no discriminan impuestos es igual a Monto/1+ConceptoImpositivo

                if ($R_TC->DiscriminaImpuesto) {
                    foreach ($R as $row) {
                        $NetoGravado += ( $row->Cantidad * $row->PrecioUnitario);
                    }
                } else {

                    foreach ($R as $row) {
                        $R_CI = $row->findParentRow('Base_Model_DbTable_ConceptosImpositivos');
                        $NetoGravado += ( $row->Cantidad * $row->PrecioUnitario / (1 + $R_CI->PorcentajeActual / 100));
                    }
                }

                if ($dGral_Monto) {
                    if ($idConcepto) {
                        // Como esta pidiendo solo una parte tengo que ver la proporcion del descuente que le corresponde
                        $NetoGravado -= ( $dGral_Monto * $NetoGravado / $NetoGravadoGeneral);
                    } else {
                        $NetoGravado -= $dGral_Monto;
                    }
                } else {
                    $NetoGravado = $NetoGravado - $NetoGravado * $dGral_Porcentaje;
                }
            } else {

                foreach ($R as $row) {
                    if (!$R_TC->DiscriminaImpuesto) {
                        $R_CI = $row->findParentRow('Base_Model_DbTable_ConceptosImpositivos');
                        $total = ($row->Cantidad * $row->PrecioUnitario / (1 + $R_CI->PorcentajeActual / 100));
                    } else {
                        $total = ($row->Cantidad * $row->PrecioUnitario);
                    }
                    $NetoGravado = $NetoGravado + ($total * (1 - $row->DescuentoEnPorcentaje / 100));
                    $NetoGravado = $NetoGravado - $row->DescuentoEnMonto;
                }
            }
        }

        return $NetoGravado;
    }

    /**
     * Recupera el Total de los conceptos Impositivos de un comprobante
     *
     * @param int $idComprobante 		identificador del comprobante
     *
     * @return decimal
     */
    public function recuperarConceptosImpositivos ($idComprobante)
    {
        $R_C = $this->find($idComprobante)->current();

        if (!$R_C) {

            return 0;

        } else {

            $R_TC = $R_C->findParentRow('Facturacion_Model_DbTable_TiposDeComprobantes');

            // Si es de tipo C no debe traer nada de impuestos
            if ($R_TC->TipoDeLetra == 3) {

                return 0;

            } else {

                if ($R_TC->DiscriminaImpuesto) {
                    // -----------------------------------------------------------------------
                    // OJO OJO OJO OJO OJO OJO OJO OJO OJO OJO OJO OJO OJO OJO OJO OJO OJO OJO
                    //
                    // Si o si tiene que se tiene que instanciar el modelo Comprobantes, ya que
                    // cuando se lo llama desde otros modelos como ser Facturas Ventas los per-
                    // manent values hacen que devuelva vacio el recordset.
                    // -----------------------------------------------------------------------
                    $M_C = new Facturacion_Model_DbTable_Comprobantes(array(), false);

                    $MontoConceptos = 0;

                    // Recupero los conceptos impositivos que son hijos del comprobante
                    $R_CI = $M_C->fetchAll("Comprobantes.ComprobantePadre = $idComprobante");

                    if (count($R_CI)) {
                        foreach ($R_CI as $row) {
                            $MontoConceptos = $MontoConceptos + $row->Monto;
                        }
                    }
                    return $MontoConceptos;
                } else {

                    $M_CD = new Facturacion_Model_DbTable_ComprobantesDetalles(array(), false);

                    $R = $M_CD->fetchAll("ComprobantesDetalles.Comprobante = $idComprobante");

                    $conceptos = 0;

                    foreach ($R as $row) {
                        $R_CI = $row->findParentRow('Base_Model_DbTable_ConceptosImpositivos');

                        if ($row->DescuentoEnPorcentaje) {
                            $total = $row->Cantidad * $row->PrecioUnitario * (1 - ($row->DescuentoEnPorcentaje/100) );
                        } else {
                            $total = $row->Cantidad * $row->PrecioUnitario;
                        }

                        $conceptos += $R_CI->PorcentajeActual * $total / ($R_CI->PorcentajeActual + 100);
                    }

                    return $conceptos;
                }
            }
        }
    }

    /**
     * Recupera el Monto Total de un comprobante
     *
     * @param int $idComprobante 		identificador del comprobante
     *
     * @return decimal
     */

    public function recuperarMontoTotal ($idComprobante) {
        $NetoGravado    = $this->recuperarNetoGravado($idComprobante);
        Rad_Log::debug('NG:'.$NetoGravado);
        $Impuestos      = $this->recuperarConceptosImpositivos($idComprobante);
        Rad_Log::debug('I:'.$Impuestos);
        return $NetoGravado + $Impuestos;
    }


    /**
     * Recupera el Monto sin utilizar de un comprobante
     *
     * @param int $idComprobante 		identificador del comprobante
     *
     * @return decimal
     */
    public function recuperarMontoDisponible ($idComprobante) {
        $MontoTotal     = $this->recuperarMontoTotal($idComprobante);

        $sql = "select 	ifnull(sum(CR.MontoAsignado)) as Usado
				from	ComprobantesRelacionados CR
                inner join Comprobantes CP on CP.Id = CR.ComprobantePadre
				where	CR.ComprobanteHijo 	= $idComprobante
                and     CP.Cerrado = 1";

        // Recupero cuanto se ha utilizado en algun OTRO comprobante de pago
        $R_D = $this->_db->fetchOne($sql);
        if ($R_D) {
            $Utilizado = $R_D;
        }

        if ($MontoTotal - $Utilizado > 0) {
            return $MontoTotal - $Utilizado;
        }
        else {
            return 0.00;
        }
    }

    /**
     * Recupera el multiplicador (positivo o negativo) del comprobante
     *
     * @param int $idComprobante 		identificador del comprobante
     *
     * @return decimal
     */
    public function recuperarMultiplicadorComprobante ($idComprobante)
    {
        $M_C = new Facturacion_Model_DbTable_Comprobantes();
        $R_C = $M_C->find($idComprobante)->current();
        if (!$R_C) {
            throw new Rad_Db_Table_Exception("No se localiza el comprobante.");
        }

        $M_TC = new Facturacion_Model_DbTable_TiposDeComprobantes(array(), false);
        $R_TC = $M_TC->fetchRow("Id= $R_C->TipoDeComprobante");
        if (!$R_TC) {
            throw new Rad_Db_Table_Exception("No se localiza el tipo del comprobante.");
        }

        $Multiplicador = $R_TC->Multiplicador;

        return $Multiplicador;
    }

    /**
     * Genera el proximo numero de un tipo de comprobante
     *
     * @param int $Punto 				Punto del cual se quiere recuperar el proximo valor
     * @param int $TipoDeComprobante 	Delimita por tipo de comprobante la generacion del numero
     *
     * @return decimal
     */
    public function recuperarProximoNumero ($Punto, $TipoDeComprobante = null)
    {
        if (!$TipoDeComprobante && !$Punto) {
            throw new Rad_Db_Table_Exception("Faltan datos para poder generar el proximo numero.");
        }

        $where = "";
        $and = "";

        if ($TipoDeComprobante) {
            $where = "TipoDeComprobante = $TipoDeComprobante";
            $and = " and ";
        }

        if ($Punto) {
            $where = $where . $and . "Punto = $Punto";
        }

        $R_C = $this->fetchRow($where, 'Id DESC for update');

        if (!$R_C) {
            return 1;
        } else {
            return $R_C->Numero + 1;
        }
    }

    /**
     * Recupera el Precio de un articulo de la lista de precio de una persona para compra
     *
     * @param int $idComprobante 		identificador del comprobante
     * @param int $idArticulo 			identificador del articulo
     *
     * @return decimal
     */
    public function recuperarPUdeListaDePrecio ($idComprobante, $idArticulo)
    {
        $PrecioUnitario = '0.00';
        if (!$idComprobante) {
            throw new Rad_Db_Table_Exception("No se encuentra el registro.");
        } else {
            $M_C = new Facturacion_Model_DbTable_Comprobantes();
            $R_C = $M_C->find($idComprobante)->current();
            if ($R_C) {
                $sql = "select  ifnull(PrecioUltimo,0) as Precio
                        from    PersonasRegistrosDePrecios
                        where   Articulo        = $idArticulo
                        AND     Persona         = $R_C->Persona
                        AND     TipoDeDivisa    = $R_C->Divisa
                        order by Id desc limit 1";

                $R_PLP = $this->_db->fetchOne($sql);
                if ($R_PLP) {
                    $PrecioUnitario = $R_PLP;
                }
            }
        }
        return $PrecioUnitario;
    }

    /**
     * Recupera el Precio de un articulo de la lista de precio propia para ventas
     *
     * @param int $idComprobante 		identificador del comprobante
     * @param int $idArticulo 			identificador del articulo
     *
     * @return decimal
     */
    public function recuperarPUdeListaDePrecioPropia ($idComprobante, $idArticulo)
    {
        $PrecioUnitario = '0.00';
        if (!$idComprobante) {
            throw new Rad_Db_Table_Exception("No se encuentra el registro.");
        } else {
            $M_C = new Facturacion_Model_DbTable_Comprobantes();
            $R_C = $M_C->find($idComprobante)->current();
            if ($R_C) {
                if (!$R_C->ListaDePrecio) {
                    $R_C->ListaDePrecio = 7;
                }
                $sql = "select 	ifnull(Precio,0) as Precio
						from	ArticulosListasDePrecios ALP,
						        ArticulosListasDePreciosDetalle ALPD
						where	ALP.Id = ALPD.ListaDePrecio
						AND 	ALPD.Articulo 	= $idArticulo
						AND 	ALP.Id 		= $R_C->ListaDePrecio
						order by ALPD.Id desc limit 1";

                $R_PLP = $this->_db->fetchOne($sql);
                if ($R_PLP) {
                    $PrecioUnitario = $R_PLP;
                }
            }
        }
        return $PrecioUnitario;
    }

    /**
     * Recupera el porcentaje de un concepto impositivo, primero intenta en forma puntual
     * para el cliente o proveedor sino en forma general
     *
     * @param int $persona 		identificador del cliente o proveedor
     * @param int $concepto		identificador del concepto impositivo
     *
     * @return decimal
     */
    public function recuperarPorcentajeConcepto ($persona, $concepto)
    {
        $porcentaje = null;

        // Veo si esta definido en forma particular para la persona
        if ($persona) {
            $M_PCI = new Base_Model_DbTable_PersonasConceptosImpositivos(array(), false);
            $R_PCI = $M_PCI->fetchRow("ConceptoImpositivo = $concepto and Persona = $persona");
            if ($R_PCI) {
                $porcentaje = $R_PCI->Porcentaje;
            }
        }

        // Si no tiene uno particular lo tomo del general
        if (!$porcentaje) {
            $M_CI = new Base_Model_DbTable_ConceptosImpositivos(array(), false);
            $R_CI = $M_CI->fetchRow("Id = $concepto");
            if (!$R_CI) {
                throw new Rad_Db_Table_Exception("No se localiza el concepto Impositivo.");
            } else {
                $porcentaje = $R_CI->PorcentajeActual;
            }
        }

        return $porcentaje;
    }

    /**
     * recuperar el registro de un Comprobante Impositivo Hijo
     *
     * @param int $idPadre 		identificador del comprobante a Padre
     * @param int $idConcepto	identificador del concepto impositivo
     *
     * @return Zend_Db_Table_Row
     */
    public function recuperarConceptoAsignado ($idComprobante, $idConcepto)
    {

        if (!$idComprobante || !$idConcepto) {
            throw new Rad_Db_Table_Exception("Faltan parametros necesarios.");
        }
        $R = $this->fetchRow("ConceptoImpositivo = $idConcepto and ComprobantePadre = $idComprobante");
        return $R;
    }

    /**
     * Retorna la imputacion presupuestaria a asignar a los conceptos impositivos hijos del Comprobante.
     * Siempre tomando desde el punto de vista de la empresa.
     *
     * @param int $idComprobante 		identificador del comprobante al que se le calcularan los impuestos
     *
     * @return varchar
     */
    public function recuperarImputacionFiscalDelComprobante ($idComprobante)
    {

        $sql = " select TC.Grupo as Grupo
                 from   TiposDeComprobantes TC
                        inner join Comprobantes C on C.TipoDeComprobante = TC.Id
                 where  C.Id = $idComprobante ";

        $R = $this->_db->fetchRow($sql);

        switch ($R['Grupo']) {
            case 1: case 7: case 11: case 13 : case 14: case 15: case 16: $imputacion = 'CreditoFiscal';
                break;    // FC, NCE, OP, NDR, Gas, Liq
            case 6: case 8: case 9: case 12 : $imputacion = 'DebitoFiscal';
                break;     // FV, NCR, RC, NDE
        }

        return $imputacion;
    }

    /**
     * crea o modifica los conceptos impositivos (NO IVA) de las facturas, tanto para compra como para venta
     *
     * @param int $idFactura 		identificador del comprobante al que se le calcularan los impuestos
     * @param int $idPersona 		identificador del cliente o proveedor
     * @param int $fechaEmision 	fecha de emision de la factura
     * @param int $filtro 		    indica si la factura es de venta o de compra
     *
     * @return none
     */
    public function recalcularConceptosFacturacion ($idFactura, $idPersona, $fechaEmision, $filtro)
    {

        switch ($filtro) {
            case "ParaVenta":
                $TipoDeComprobante = 12;
                break;
            case "ParaCompra":
                $TipoDeComprobante = 13;
                break;
            default :
        }

        $M_C = new Facturacion_Model_DbTable_Comprobantes(array(), false);

        // Recupero el NG
        $NetoGravado = $this->recuperarNetoGravado($idFactura);

        $sql = "
				select 	CI.Descripcion,
						ifnull(PCI.Porcentaje,ifnull(CI.PorcentajeActual,0)) as Porcentaje,
						CI.Id as Concepto,
						ifnull(PCI.MontoNoImponible,ifnull(CI.MontoMinimo,0)) as MontoMinimo,
						CI.TipoDeMontoMinimo
				from	ConceptosImpositivos CI
						inner join PersonasConceptosImpositivos PCI on CI.Id = PCI.ConceptoImpositivo
				where	PCI.Persona 	= $idPersona
				and		CI.$filtro 	    = 1
				and		CI.EsIVA 		= 0
				and		CI.FechaAlta <= '" . $fechaEmision . "'
				and		IFNULL(CI.FechaBaja,'2999-12-31') >= '" . $fechaEmision . "'
				order by CI.Descripcion";

        $R_IMP = $this->_db->fetchAll($sql);
        if (count($R_IMP)) {
            $M_C = new Facturacion_Model_DbTable_Comprobantes(array(), false);
            foreach ($R_IMP as $row) {

                $idConcepto = $row["Concepto"];

                $MontoImponible = $M_C->recuperarMontoImponibleFacturacion($idConcepto, $idFactura);

                if ($MontoImponible > 0) {

                    $Monto = $MontoImponible * $row['Porcentaje'] / 100;

                    // Cargo los datos que voy a necesitar despues
                    $Renglon = array(
                        'Persona' => $idPersona,
                        'ComprobantePadre' => $idFactura,
                        'TipoDeComprobante' => $TipoDeComprobante,
                        'Punto' => 1,
                        'Numero' => $M_C->recuperarProximoNumero(1, $TipoDeComprobante),
                        'FechaEmision' => date('Y-m-d'),
                        'Divisa' => 1,
                        'ValorDivisa' => 1,
                        'ConceptoImpositivo' => $row['Concepto'],
                        'ConceptoImpositivoPorcentaje' => $row['Porcentaje'],
                        'MontoImponible' => $MontoImponible,
                        'Monto' => $Monto
                    );

                    $R_H = $M_C->recuperarConceptoAsignado($idFactura, $idConcepto);
                    // Si el concepto ya esta creado lo updateo sino lo inserto
                    if ($R_H) {
                        $idCI = $R_H->Id;
                        // Si se modifico manualmente no lo updateo
                        if (!$R_H->Modificado) {
                            $M_C->update($Renglon, "Id = $idCI");
                        }
                    } else {
                        $idCI = $M_C->insert($Renglon);
                    }
                } else {
                    $R_H = $M_C->recuperarConceptoAsignado($idFactura, $idConcepto);
                    // Si el concepto ya esta creado lo borro
                    if ($R_H) {
                        $idCI = $R_H->Id;
                        $M_C->delete("Id = $idCI");
                    }
                }
            }
        }
    }

    /**
     * Retorna el Row de un comprobante
     * puede recibir un row de comprobante o un id de comprobante
     *
     * LLamar de esta manera
     * $RowComprobante = Facturacion_Model_DbTable_Comprobantes::recuperarRow($idComprobante);
     *
     * @param int|Rad_Db_Table_Row $idComprobante       identificador del Comprobante
     *
     * @return Rad_Db_Table_Row
     */
    public static function recuperarRow($Comprobante) {

        if ($Comprobante instanceof Rad_Db_Table_Row) {
            // Llego un row asi que simplemente lo devuelvo
            return $Comprobante;
        } else {
            // llego un id asi que busco el row
            // OJO no usar this por que me afectan los defaultvalues tiene que ser un comproabnte cualquiera
            $M_C = new Facturacion_Model_DbTable_Comprobantes(array(), false);
            $R = $M_C->find($Comprobante)->current();
            if (!$R) {
                throw new Rad_Db_Table_Exception(" XXX No se encuentra el comprobante solicitado. $Comprobante");
            }
            return $R;
        }
    }

    /**
     * Retorna el Grupo al que pertenece un comprobante
     * Ej: Factura Compra, Factura Venta, Pagos, Cobros, etc
     *
     * @param int|Rad_Db_Table_Row $idComprobante 	    identificador del Comprobante
     *
     * @return int
     */
    public function recuperarGrupoComprobante($idComprobante)
    {
        $RowComprobante = Facturacion_Model_DbTable_Comprobantes::recuperarRow($idComprobante);

        $M_TC = new Facturacion_Model_DbTable_TiposDeComprobantes(array(), false);
        $R_TC = $M_TC->find($RowComprobante->TipoDeComprobante)->current();
        if (!$R_TC) {
            throw new Rad_Db_Table_Exception("No se encuentra el tipo de Comprobante.");
        }

        return $R_TC->Grupo;
    }

    /**
     * Retorna la descripcion de un comprobante
     * Ej: FV: 0001-00000145, NDR: 0004-0012988, etc
     *
     * @param int $idComprobante 	    identificador del Comprobante
     *
     * @return varchar
     */
    public function recuperarDescripcionComprobante($idComprobante)
    {
        $R = $this->find($idComprobante)->current();
        if (!$R) {
            throw new Rad_Db_Table_Exception("No se encuentra el comprobante Padre.");
        }

        $sql = "SELECT fNumeroCompleto($idComprobante,'CG')";

        // Recupero el numero completo de un comprobante
        $NumeroCompleto = $this->_db->fetchOne($sql);
        if (!$NumeroCompleto) {
            $NumeroCompleto = "S/D";
        }

        return $NumeroCompleto;
    }

    /**
     * Retorna el Monto minimo imponible de un Concepto para una determinada Persona.
     * Veo si ese concepto tiene un valor particular para la persona sino lo busco en la tabla de conceptos impositivos.
     *
     * @param int $idConcepto 	    identificador del concepto impositivo
     * @param int $idPersona 	    identificador del Cliente/Proveedor
     *
     * @return decimal
     */
    public function recuperarMMIdelConcepto ($idConcepto, $idPersona)
    {
        $MMi = 0;

        $M_PCI = new Base_Model_DbTable_PersonasConceptosImpositivos(array(), false);
        $R_PCI = $M_PCI->fetchRow("Persona = $idPersona and ConceptoImpositivo = $idConcepto");
        // Veo si tiene uno particular sino busco el general
        if ($R_PCI) {
            if ($R_PCI->MontoNoImponible) {
                $MMi = $R_PCI->MontoNoImponible;
            }
        } else {
            $M_CI = new Base_Model_DbTable_ConceptosImpositivos(array(), false);
            $R_CI = $M_CI->find($idConcepto)->current();
            if ($R_CI && $R_CI->MontoMinimo) {
                $MMi = $R_CI->MontoMinimo;
            }
        }
        return $MMi;
    }

    /**
     * Retorna el Monto imponible de un Concepto para un determinado Comprobante.
     *
     *
     * @param int $idConcepto 	    identificador del concepto impositivo
     * @param int $idComprobante    identificador del comprobante de facturacion
     *
     * @return decimal
     */
    public function recuperarMontoImponibleFacturacion ($idConcepto, $idComprobante)
    {

        $M_C  = new Facturacion_Model_DbTable_Comprobantes(array(), false);
        $M_CI = new Base_Model_DbTable_ConceptosImpositivos(array(), false);

        // Recupero el NG del comprobante
        if ($M_CI->esIVA($idConcepto)) {
            $NetoGravado = $M_C->recuperarNetoGravado($idComprobante, $idConcepto);
        } else {
            $NetoGravado = $M_C->recuperarNetoGravado($idComprobante);
        }
        // Si tiene monto minimo el impuesto veo cuanto queda disponible para calcular el impuesto
        $MMIsinUsar = $M_C->recuperarMMIdisponibleFacturacion($idConcepto, $idComprobante);

        if ($MMIsinUsar > 0.00001 && $MMIsinUsar >= $NetoGravado) {
            $MontoImponible = 0;
        } else {
            $MontoImponible = $NetoGravado - $MMIsinUsar;
        }

        return $MontoImponible;
    }

    public function afip_RecuperarTotalesIVA($idComprobante)
    {
        $M_CI = new Base_Model_DbTable_ConceptosImpositivos;

        $Imp_NG_1050 = $this->recuperarMontoImponibleFacturacion($M_CI->iva105, $idComprobante);
        $row = $M_CI->find($M_CI->iva105)->current();

        $ivas['1050']['Monto']          = $Imp_NG_1050 * 0.105;
        $ivas['1050']['MontoImponible'] = $Imp_NG_1050;
        $ivas['1050']['codAfip']        = $row->Afip;

        $Imp_NG_2100 = $this->recuperarMontoImponibleFacturacion($M_CI->iva21, $idComprobante);
        $row = $M_CI->find($M_CI->iva21)->current();

        $ivas['2100']['Monto']          = $Imp_NG_2100 * 0.21;
        $ivas['2100']['MontoImponible'] = $Imp_NG_2100;
        $ivas['2100']['codAfip']        = $row->Afip;

        $Imp_NG_2700 = $this->recuperarMontoImponibleFacturacion($M_CI->iva27, $idComprobante);
        $row = $M_CI->find($M_CI->iva27)->current();

        $ivas['2700']['Monto']          = $Imp_NG_2700 * 0.27;
        $ivas['2700']['MontoImponible'] = $Imp_NG_2700;
        $ivas['2700']['codAfip']        = $row->Afip;

        $Imp_NG_0500 = $this->recuperarMontoImponibleFacturacion($M_CI->iva05, $idComprobante);
        $row = $M_CI->find($M_CI->iva05)->current();

        $ivas['0500']['Monto']          = $Imp_NG_0500 * 0.05;
        $ivas['0500']['MontoImponible'] = $Imp_NG_0500;
        $ivas['0500']['codAfip']        = $row->Afip;

        $Imp_NG_0250 = $this->recuperarMontoImponibleFacturacion($M_CI->iva025, $idComprobante);
        $row = $M_CI->find($M_CI->iva025)->current();

        $ivas['0250']['Monto']          = $Imp_NG_0250 * 0.025;
        $ivas['0250']['MontoImponible'] = $Imp_NG_0250;
        $ivas['0250']['codAfip']        = $row->Afip;

        $Imp_NG_0000 = $this->recuperarMontoImponibleFacturacion($M_CI->iva0, $idComprobante);
        $row = $M_CI->find($M_CI->iva0)->current();

        $ivas['0000']['Monto']          = 0;
        $ivas['0000']['MontoImponible'] = $Imp_NG_0000;
        $ivas['0000']['codAfip']        = $row->Afip;

        return $ivas;
    }

   

    public function afip_ImporteNetoNoGravado ($idComprobante)
    {
        $M_C    = new Facturacion_Model_DbTable_Comprobantes;
        $M_CI   = new Base_Model_DbTable_ConceptosImpositivos;
        return $M_C->recuperarMontoImponibleFacturacion($M_CI->ivaNoGravado, $idComprobante);
    }

    public function afip_ImporteNetoExento ($idComprobante)
    {
        $M_C    = new Facturacion_Model_DbTable_Comprobantes;
        $M_CI   = new Base_Model_DbTable_ConceptosImpositivos;
        return $M_C->recuperarMontoImponibleFacturacion($M_CI->ivaExcento, $idComprobante);
    }

    public function afip_ImporteNetoGravado ($idComprobante)
    {
        $M_C    = new Facturacion_Model_DbTable_Comprobantes;
        $M_CI   = new Base_Model_DbTable_ConceptosImpositivos;

        $NetoGravadoGeneral = $M_C->recuperarNetoGravado($idComprobante);
        $NetoIvaNoGravado   = $M_C->recuperarMontoImponibleFacturacion($M_CI->ivaNoGravado, $idComprobante);
        $NetoIvaExento      = $M_C->recuperarMontoImponibleFacturacion($M_CI->ivaExcento, $idComprobante);
        return $NetoGravadoGeneral - $NetoIvaNoGravado - $NetoIvaExento;// - $NetoIva0;
    }
    

    public function afip_MontoConceptosNoIVA ($idComprobante)
    {
        $Monto = 0;
        $sql = "select  ifnull(sum(C.Monto),0) as Monto
                from    Comprobantes C,
                        ConceptosImpositivos CI
                where   C.ConceptoImpositivo = CI.Id
                and     C.ComprobantePadre = $idComprobante
                and     CI.esIVA = 0";

        $R = $this->_db->fetchRow($sql);
        if ($R) {
            $Monto = $R['Monto'];
        }
        return $Monto;
    }

    public function afip_ArrayConceptosIVA ($idComprobante)
    {
        $comprobante = $this->find($idComprobante)->current();
        if (!$comprobante) throw new Rad_Db_Table_Exception('No se encontro el comprobante');

        // Si es factura o Nota A o M
        if (in_array($comprobante->TipoDeComprobante,array(24,28,29,32,37,40,79,81,82,85,86))) {

            $sql = "select  CI.afip as codAfip,
                            ifnull(C.MontoImponible,0) as MontoImponible,
                            ifnull(C.Monto,0) as Monto,
                            CI.Descripcion
                    from    Comprobantes C
                            left join ConceptosImpositivos CI on C.ConceptoImpositivo = CI.Id
                    where   C.ComprobantePadre = $idComprobante
                    and     CI.esIVA = 1
                    -- and     CI.PorcentajeActual <> 0
                    order by afip desc";
            $R = $this->_db->fetchAll($sql);
            // Si es factura o Nota  B,C,E
        } else if (in_array($comprobante->TipoDeComprobante,array(25,26,27,30,31,38,39,80,83,84,87,88))) {
            $R = $this->afip_RecuperarTotalesIVA($idComprobante);
        }

        return $R;
    }

    public function afip_ArrayConceptosNoIVA ($idComprobante)
    {
        $sql = "select  CI.afip as codAfip,
                        ifnull(C.MontoImponible,0) as MontoImponible,
                        ifnull(C.Monto,0) as Monto,
                        CI.Descripcion,
                        C.ConceptoImpositivoPorcentaje
                from    Comprobantes C
                        left join ConceptosImpositivos CI on C.ConceptoImpositivo = CI.Id
                where   C.ComprobantePadre = $idComprobante
                and     CI.esIVA = 0
                and     CI.PorcentajeActual <> 0
                order by afip desc";

        $R = $this->_db->fetchAll($sql);

        return $R;
    }

    /**
     * Retorna el Monto Minimo No Imponible que queda disponible para ese periodo
     * para un concepto determinado para un Cliente/Proveedor determinado.
     *
     * @param int $idConcepto 	    identificador del concepto impositivo
     * @param int $idPersona 	    identificador del Cliente/Proveedor
     * @param int $fechaEmision   	fecha de emision del comprobante padre
     *
     * @return decimal
     */
    public function recuperarMMIdisponibleFacturacion ($idConcepto, $idComprobantePadre)
    {

        $M_C = new Facturacion_Model_DbTable_Comprobantes(array(), false);

        // Recupero los valores del Comprobante Padre
        $R_P = $M_C->find($idComprobantePadre)->current();
        if (!$R_P) {
            throw new Rad_Db_Table_Exception("No se encuentra el comprobante Padre.");
        }

        $disponible = 0;
        $idPersona = $R_P->Persona;
        $MMI = $M_C->recuperarMMIdelConcepto($idConcepto, $idPersona);
        // Si tienen MMi proceso, sino retorno 0
        if ($MMI > 0.0001) {

            $NetoGrabado = 0;
            $GrupoComprobante = $M_C->recuperarGrupoComprobante($idComprobantePadre);
            $PrincipioMes = Rad_CustomFunctions::firstOfMonth($R_P->FechaEmision);
            $FinMes = Rad_CustomFunctions::lastOfMonth($R_P->FechaEmision);

            $sql = "select  *
                    from    Comprobantes
                    where   FechaEmision >= $PrincipioMes
                    and     FechaEmision >= $FinMes
                    and     Persona      =  $idPersona
                    and     TipoDeComprobante in (select Id from TiposDeComprobantes where Grupo = $GrupoComprobante)
                    and     Id <> $idComprobantePadre
                    ";

            // Recupero todos los demas comprobantes
            $R = $this->_db->fetchAll($sql);

            if (count($R)) {
                foreach ($R as $row) {
                    // Para cada comprobante recupero el neto Grabado
                    $NetoGrabado = $NetoGrabado + $M_C->recuperarNetoGravado($row['Id']);
                }
            }

            // Si queda algo lo informo como disponible
            if ($MMI > $NetoGrabado) {
                $disponible = $MMI - $NetoGrabado;
            }
        }
        return $disponible;
    }

    /**
     * Cierra el comprobante y publica el evento
     *
     * @param int $idComprobante 	identificador del comprobante a cerrar
     */
    public function cerrar ($idComprobante)
    {
        $rowComprobante     = $this->find($idComprobante)->current();
        $persona            = $rowComprobante->Persona;
        $tipoComprobante    = $rowComprobante->findParentRow("Facturacion_Model_DbTable_TiposDeComprobantes");
        $grupoComprobante   = $tipoComprobante->Grupo;

        // Reviso si aplica cambio dolar.
        $divisaCambio      = null;
        $valorDivisaCambio = null;
        $divisaDolarId     = 2;
        if (in_array($grupoComprobante, array(6))) { // Grupos de Comprobantes FV .
            $sql = "SELECT cambioactual FROM tiposdedivisas WHERE id = ".$divisaDolarId;
            $divisaCambio      = $divisaDolarId;
            $valorDivisaCambio = $this->_db->fetchOne($sql);
        }

        // Cierro el comprobante
        $data = array(  'DivisaCambio' => $divisaCambio,
            'ValorDivisaCambio' => $valorDivisaCambio,
            'Cerrado' => 1,
            'FechaCierre' => date('Y-m-d H:i:s')
        );

        parent::update($data, 'Id =' . $idComprobante);

        // Marco el proveedor o cliente dependiendo el tipo de tramite
        if (in_array($grupoComprobante, array(1,4,5,9)) && !Base_Model_DbTable_Personas::esProveedor($persona)) {
            Base_Model_DbTable_Personas::setProveedor($persona);
            Rad_Log::user("Marco en forma automatica a la Persona $rowComprobante->Persona como Proveedor.");
        }
        if ($rowComprobante->TipoDeComprobante != 15 && in_array($grupoComprobante, array(6,10,11)) && !Base_Model_DbTable_Personas::esCliente($persona)) {
            Base_Model_DbTable_Personas::setCliente($persona);
            Rad_Log::user("Marco en forma automatica a la Persona $rowComprobante->Persona como Cliente.");
        }

        // Log Usuarios
  if ( $rowComprobante->Numero == 0 ) {
            Rad_Log::user("Cerro comprobante ($tipoComprobante->Descripcion ID $rowComprobante->Id)");
        } else {
            Rad_Log::user("Cerro comprobante ($tipoComprobante->Descripcion NÂº $rowComprobante->Numero)");
        }
        // Publico...
        Rad_PubSub::publish('Comprobante_Cerrar', $rowComprobante);
    }

    /**
     * Permite anular un comprobante
     *
     * @param int $idComprobante 	identificador del comprobante a anular
     *
     */
    public function anular ($idComprobante)
    {
        // Controles
        $this->salirSi_EstaAnulado($idComprobante);
        $this->salirSi_TieneComprobantesPadres($idComprobante);

        // Anulo la factura
        $data = array('Anulado' => 1);
        parent::update($data, 'Id = ' . $idComprobante);
        $comprobante = $this->find($idComprobante)->current();

        $tipoComprobante = $comprobante->findParentRow("Facturacion_Model_DbTable_TiposDeComprobantes");
  // Log Usuarios
        if ( $comprobante->Numero == 0 ) {
            Rad_Log::user("Anulo comprobante ($tipoComprobante->Descripcion ID $comprobante->Id)");
        } else {
            Rad_Log::user("Anulo comprobante ($tipoComprobante->Descripcion NÂ° $comprobante->Numero)");
        }

        // Publico...
        Rad_PubSub::publish('Comprobante_Anular', $comprobante);
    }

    /***********************************************************************
      Funciones de Verificacion
     ********************************************************************** */

    /**
     * Verifica si el comprobante es de tipo A o M (discriminan IVA)
     * En los casos
     *
     * @param int $idComprobante 	identificador del comprobante a verificar
     *
     * @return boolean
     */
    public function esComprobanteAoM ($idComprobante)
    {
        $discrimina = true;

        $R_C = $this->find($idComprobante)->current();
        if (!$R_C) {
            throw new Rad_Db_Table_Exception("No se encontro el comprobante.");
        }

        // Debo ver si se trata de una Factura o similar o de un Pago o Cobro, en este ultimo caso
        // debo revisar las Facturas u otros comprobantes que se esten pagando si discriminan IVA.
        // Recupero el Grupo del Comprobante
        $Grupo = $this->recuperarGrupoComprobante($idComprobante);

        if ($Grupo == 9 || $Grupo == 11 ) {
            // Se trata de un pago o de un Cobro debo revisar los comprobantes que los componen, para eso
            // debo recuperar todos los comprobantes relacionados.

            $M_C = new Facturacion_Model_DbTable_Comprobantes(array(), false);

            $sql = "select  CH.*
                    from    Comprobantes CH, ComprobantesRelacionados CR
                    where   CH.Id = CR.ComprobanteHijo
                    and     CR.ComprobantePadre = $idComprobante";

            $R_CH = $this->_db->fetchAll($sql);

            if (count($R_CH)) {
                foreach ($R_CH as $row) {
                    // Para cada comprobante hijo reviso si discrimina impuestos
                    if (!$M_C->esComprobanteAoM($row['Id'])) {
                        // Si alguno no es A o M salgo
                        // $discrimina = true;

                        // VER: se cambio porq hay exepciones como en el caso de que la persona sea excento (como el estado q es agente de retencion)
                        $discrimina = true;
                        break;
                    }
                }
            }
        } else {
            $sql = "select DiscriminaImpuesto from TiposDeComprobantes where Id = $R_C->TipoDeComprobante";
            $R_TC = $this->_db->fetchRow($sql);
            if (!$R_TC["DiscriminaImpuesto"]) {
                $discrimina = false;
            }
        }
        //Rad_Log::debug($discrimina);
        if ($discrimina) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Verifica si el comprobante es de tipo entrada (FC, NDR, NCR)
     *
     * @param int|Rad_Db_Table_Row $idComprobante identificador del comprobante a verificar o
     *
     * @return boolean
     */
    public function esComprobanteEntrada ($idComprobante)
    {
        $R_C = Facturacion_Model_DbTable_Comprobantes::recuperarRow($idComprobante);
        if (!$R_C) {
            throw new Rad_Db_Table_Exception("No se encontro el comprobante.");
        }
        // Recupero el Grupo del Comprobante
        $Grupo = $this->recuperarGrupoComprobante($idComprobante);

        if ($Grupo == 1 || $Grupo == 8 || $Grupo == 13 || $Grupo == 4 || $Grupo == 14 || $Grupo == 15 || $Grupo == 16 || $Grupo == 17) {
            // Se trata de un Comprobante de entrada FC, NCR, NDR.
            return true;
        } else {
            return false;
        }
    }

    /**
     * Verifica si el comprobante es de tipo sin IVA.
     *
     * @param int|Rad_Db_Table_Row $idComprobante identificador del comprobante a verificar o
     *
     * @return boolean
     */
    public function esComprobanteSinIVA ($idComprobante)
    {
        $R_C = Facturacion_Model_DbTable_Comprobantes::recuperarRow($idComprobante);
        if (!$R_C) {
            throw new Rad_Db_Table_Exception("No se encontro el comprobante.");
        }
        // Recupero el Grupo del Comprobante
        $Grupo = $this->recuperarGrupoComprobante($idComprobante);

        if ($Grupo == 21) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * Verifica si el comprobante es un Concepto Impositivo
     *
     * @param int $idComprobante 	identificador del comprobante a verificar
     *
     * @return boolean
     */
    public function esComprobanteImpositivo ($idComprobante)
    {
        $R = $this->find($idComprobante)->current();
        if (!$R) {
            throw new Rad_Db_Table_Exception('No se encuentra el comprobante requerido.');
        }

        if ($R->ConceptoImpositivo && $R->ConceptoImpositivoPorcentaje) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Verifica si el tipo de un comprobante es Concepto Impositivo
     *
     * @param int $tipoComprobante 	identificador del tipo de un comprobante a verificar
     *
     * @return boolean
     */
    protected function _esConceptoImpositivo ($tipoComprobante)
    {

        // TODO : Hacer funcion _esConceptoImpositivo
        //$M_TC= new Model_DbTable_TiposDeComprobantes(array(),false);
        return true;
    }

    /**
     * Verifica si el tipo de comprobante discriminan IVA
     *
     * @param int $idComprobante 	identificador del comprobante a verificar
     *
     * @return boolean
     */
    public function elComprobanteDiscriminaIVA ($idTipoComprobante)
    {
        // Verifico que la cantidad y el precio unitario sean mayores que cero
        $M_TC = new Facturacion_Model_DbTable_TiposDeComprobantes(array(), false);

        $R_TC = $M_TC->find($idTipoComprobante)->current();
        if (!$R_TC) {
            throw new Rad_Db_Table_Exception("No se encontro el comprobante.");
        }
        //Rad_Log::debug('Paso');
        if ($R_TC->DiscriminaImpuesto) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Verifica si esta en moneda extranjera
     *
     * @param int $idComprobante 	identificador del comprobante a verificar
     *
     * @return boolean
     */
    public function estaEnMonedaExtranjera ($idComprobante)
    {
        $R_C = $this->find($idComprobante)->current();

        if (!$R_C)
            throw new Rad_Db_Table_Exception("No se localiza el comprobante. 1");

        if ($R_C->Divisa != 1) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Comprueba si existe el comprobante
     *
     * @param int $idComprobante 	identificador del comprobante a localizar
     *
     * @return boolean
     */
    public function existe ($idComprobante)
    {
        $R_C = $this->find($idComprobante)->current();

        if ($R_C) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Comprueba si el comprobante esta cerrado
     *
     * @param int|Rad_Db_Table_row $idComprobante    identificador del comprobante a cerrar
     *
     * @return boolean
     */
    public function estaCerrado ($Comprobante)
    {
        $RowComprobante = Facturacion_Model_DbTable_Comprobantes::recuperarRow($Comprobante);

        if ($RowComprobante->Cerrado == 1) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Comprueba si el comprobante es Contado
     *
     * @param int|Rad_Db_Table_row $idComprobante    identificador del comprobante a cerrar
     *
     * @return boolean
     */
    public function esContado ($Comprobante)
    {
        $RowComprobante = Facturacion_Model_DbTable_Comprobantes::recuperarRow($Comprobante);

        // 2 contado - 1 Cta Cte
        if ($RowComprobante->CondicionDePago == 2) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * Comprueba si el comprobante esta anulado
     *
     * @param int|Rad_Db_Table_row $Comprobante    identificador del comprobante a cerrar
     *
     * @return boolean
     */
    public function estaAnulado ($Comprobante)
    {
        $RowComprobante = Facturacion_Model_DbTable_Comprobantes::recuperarRow($Comprobante);
        return ($RowComprobante->Anulado) ? true : false;
    }

    /**
     * Comprueba si el comprobante que se esta relacionando (en el campo ComprobanteRelacionado)
     * no este realcionado a otro comprobante
     *
     * @param int|Rad_Db_Table_row $idComprobante    identificador del comprobante a cerrar
     *
     * @return boolean
     */
    public function elComprobanteRelacionadoYaEstaRelacionado ($Comprobante)
    {
        $RowComprobante = Facturacion_Model_DbTable_Comprobantes::recuperarRow($Comprobante);

        if ($RowComprobante->ComprobanteRelacionado) {
            $txtSQL = " ComprobanteRelacionado = $RowComprobante->ComprobanteRelacionado and Id <> $RowComprobante->Id and Cerrado = 1";
            $M_C = new Facturacion_Model_DbTable_Comprobantes(array(), false);
            $R_C = $M_C->fetchRow($txtSQL);
            if($R_C) {
                return true;
            } else {
               return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Comprueba si el comprobante tiene detalle
     *
     * @param int $idComprobante 	identificador del comprobante
     * @param int $tipoDetalle		identificador del tipo de detalle del renglon
     *
     * @return boolean
     */
    public function tieneDetalle ($idComprobante)
    {
        $M_CD = new Facturacion_Model_DbTable_ComprobantesDetalles(array(), false);
        $txtSQL = " Comprobante = $idComprobante ";

        $R_C = $M_CD->fetchRow($txtSQL);
        if ($R_C) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Comprueba si el comprobante tiene cheques asociados
     *
     * @param int $idComprobante    identificador del comprobante
     * @param int $tipoDetalle      identificador del tipo de detalle del renglon
     *
     * @return boolean
     */
    public function tieneChequesAsociados ($idComprobante)
    {
        $M_CC = new Facturacion_Model_DbTable_ComprobantesCheques(array(), false);
        $txtSQL = " Comprobante = $idComprobante ";

        $R_CC = $M_CC->fetchRow($txtSQL);
        if ($R_CC) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Comprueba si existe otro comprobante del mismo cliente sin cerrar
     *
     * @param int $persona       	    identificador del cliente o proveedor
     * @param int $tipoDeComprobante 	identificador del tipo de comprobante
     * @param int $idComprobante 	    identificador del comprobante (solo en el caso de update)
     *
     * @return boolean
     */
    public function existeOtroComprobanteSinCerrar ($persona, $tipoComprobante, $idComprobante = null)
    {

        // Recupero el grupo de un tipo de comprobante
        $M_TC = new Facturacion_Model_DbTable_TiposDeComprobantes(array(), false);
        $R_TC = $M_TC->find($tipoComprobante)->current();
        if (!$R_TC) {
            throw new Rad_Db_Table_Exception("No se encuentra el tipo de Comprobante.");
        }
        $grupo = $R_TC->Grupo;

        $sql = " select C.*
                 from   Comprobantes C
                 inner  join TiposDeComprobantes TC on C.TipoDeComprobante = TC.Id
                 where  C.Persona = $persona
                 and    TC.Grupo  = $grupo
                 and    C.Cerrado = 0 ";

        if ($idComprobante) {
            $sql = $sql . " and C.Id <> $idComprobante ";
        }

        $R = $this->_db->fetchAll($sql);

        if (count($R)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Comprueba si el comprobante tiene detalles con valores Cero
     *
     * @param int $idComprobante 	identificador del comprobante
     *
     * @return boolean
     */
    public function tieneDetalleConValorCero ($idComprobante)
    {
        $M_CD = new Facturacion_Model_DbTable_ComprobantesDetalles(array(), false);

        $txtSQL = "Comprobante = $idComprobante and ifnull(PrecioUnitario,0) = 0";

        $R_C = $M_CD->fetchRow($txtSQL);
        if ($R_C) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Verifica si el concepto ya se encuentra asignado
     *
     * @param int $ComprobantePadre 	identificador del comprobante a Padre a verificar
     * @param int $Concepto 			identificador del concepto impositivo
     *
     * @return boolean
     */
    public function estaElConceptoAsignado ($ComprobantePadre, $Concepto)
    {

        if (!$ComprobantePadre || !$Concepto) {
            throw new Rad_Db_Table_Exception("Faltan parametros necesarios.");
        }

        $R = $this->fetchRow("ConceptoImpositivo = $Concepto and ComprobantePadre = $ComprobantePadre");

        if ($R) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Verifica si el comprobante padre tiene comprobantes hijos relacionados
     *
     * @param int $ComprobantePadre 	identificador del comprobante a Padre a verificar
     *
     * @return boolean
     */
    public function tieneComprobantesHijos ($idComprobante)
    {
        if (!$idComprobante) {
            throw new Rad_Db_Table_Exception("Faltan parametros necesarios.");
        }

        $M_CR = new Facturacion_Model_DbTable_ComprobantesRelacionados(array(), false);

        $R = $M_CR->fetchRow("ComprobantePadre = $idComprobante");

        if ($R) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Verifica si el comprobante Hijo tiene comprobantes Padres relacionados
     *
     * @param int $ComprobanteHijo 	identificador del comprobante Hijo a verificar
     *
     * @return boolean
     */
    public function tieneComprobantesPadres ($idComprobante)
    {
        if (!$idComprobante) {
            throw new Rad_Db_Table_Exception("Faltan parametros necesarios.");
        }

        $M_CR = new Facturacion_Model_DbTable_ComprobantesRelacionados(array(), false);

        $R = $M_CR->fetchRow("ComprobanteHijo = $idComprobante");

        if ($R) {
            return true;
        } else {
            return false;
        }
    }

    /* **********************************************************************
      Funciones para salir
     * ********************************************************************* */

    /**
     * sale si el comprobante padre no tiene asociado ningun comprobante hijo
     *
     * @param int $ComprobantePadre 	identificador del comprobante a Padre a verificar
     *
     */
    public function salirSi_noTieneComprobantesHijos ($idComprobante)
    {
        if (!$this->tieneComprobantesHijos($idComprobante)) {
            throw new Rad_Db_Table_Exception("El comprobante padre no tiene comprobantes asociados.");
        }
        return $this;
    }

    /**
     * sale si el comprobante es al contado
     *
     * @param int $ComprobantePadre     identificador del comprobante a Padre a verificar
     *
     */
    public function salirSi_esContado ($idComprobante)
    {
        if ($this->esContado($idComprobante)) {
            throw new Rad_Db_Table_Exception("El comprobante que intenta asociar es Contado y no en Cta. Cte.");
        }
        return $this;
    }

    /**
     * sale si el comprobante Hijo tiene asociado algun comprobante padre
     *
     * @param int $ComprobanteHijo 	identificador del comprobante hijo a verificar
     *
     */
    public function salirSi_TieneComprobantesPadres ($idComprobante)
    {
        if ($this->tieneComprobantesPadres($idComprobante)) {
            throw new Rad_Db_Table_Exception("El comprobante hijo tiene comprobantes asociados.");
        }
        return $this;
    }

    /**
     * Verifica si el comprobante es un Concepto Impositivo
     *
     * @param int $idComprobante 	identificador del comprobante a verificar
     *
     * @return boolean
     */
    public function salirSi_esComprobanteImpositivo ($idComprobante)
    {
        if (!$this->esComprobanteImpositivo($idComprobante)) {
            throw new Rad_Db_Table_Exception("El comprobante padre no tiene comprobantes asociados.");
        }
        return $this;
    }


    /**
     * Verifica si el comprobante tiene el comprobante relacionado en el caso que lo necesite
     *
     * @param int|Rad_Db_Table_Row $idComprobante 	identificador del comprobante a verificar
     *
     * @return boolean
     */
    public function salirSi_NoTieneComprobanteRelacionado ($Comprobante)
    {
       $RowComprobante = Facturacion_Model_DbTable_Comprobantes::recuperarRow($Comprobante);

       // Por ahora solo los adelantos pero las NC y ND deberian tener
       // Se podria poner en un campo de la base de datos
       if ($RowComprobante->TipoDeComprobante == 51) {
            if ($RowComprobante->ComprobanteRelacionado) {
                return true;
            } else {
                return false;
            }
       }
       // si no requiere el campo comprobante relacionado devuelvo TRUE
       return true;
    }



    /**
     * sale si el comprobante padre tiene asignado ya el concepto
     *
     * @param int $ComprobantePadre 	identificador del comprobante a Padre a verificar
     * @param int $Concepto 			identificador del concepto impositivo
     *
     */
    public function salirSi_estaElConceptoAsignado($ComprobantePadre, $Concepto)
    {
        if ($this->estaElConceptoAsignado($ComprobantePadre, $Concepto)) {
            throw new Rad_Db_Table_Exception("El concepto que intenta ingresar ya se encuentra asignado a este comprobante.");
        }
        return $this;
    }

    /**
     * sale si no existe el comprobante
     *
     * @param int $idComprobante 	identificador del comprobante a verificar
     *
     */
    public function salirSi_noExiste ($idComprobante)
    {
        if (!$this->existe($idComprobante)) {
            throw new Rad_Db_Table_Exception("No se localiza el comprobante.");
        }
        return $this;
    }

    /**
     * sale si el comprobante NO es de tipo A o M (discriminan IVA)
     *
     * @param int $idComprobante 	identificador del comprobante a verificar
     *
     * @return boolean
     */
    public function salirSi_NoEsComprobanteAoM ($idComprobante)
    {
        if (!$this->esComprobanteAoM($idComprobante)) {
            throw new Rad_Db_Table_Exception("La operacion solicitada no es posible para un Comprobante que no discrimine impuestos.");
        }
        return $this;
    }

    /**
     * Sale si el comprobante esta cerrado
     *
     * @param int|Rad_Db_Table_Row $idComprobante 	identificador del comprobante a verificar
     *
     * @return boolean
     */
    public function salirSi_estaCerrado ($Comprobante)
    {
        if ($this->estaCerrado($Comprobante)) {
            throw new Rad_Db_Table_Exception("El comprobante se encuentra cerrado y no puede modificarse.");
        }
        return $this;
    }

    /**
     * sale si el comprobante no esta cerrado
     *
     * @param int $idComprobante 	identificador del comprobante a verificar
     *
     */
    public function salirSi_noEstaCerrado ($idComprobante)
    {
        if (!$this->estaCerrado($idComprobante)) {
            throw new Rad_Db_Table_Exception("El comprobante NO se encuentra cerrado.");
        }
        return $this;
    }

    /**
     * sale si el comprobante esta anulado
     *
     * @param int $idComprobante    identificador del comprobante a verificar
     *
     */
    public function salirSi_EstaAnulado ($idComprobante)
    {
        if ($this->estaAnulado($idComprobante)) {
            throw new Rad_Db_Table_Exception("El comprobante se encuentra anulado.");
        }
        return $this;
    }

    /**
     * sale si el comprobante no tiene detalle del tipo solicitado
     *
     * @param int $idComprobante 	identificador del comprobante a verificar
     *
     */
    public function salirSi_noTieneDetalle ($idComprobante)
    {
        if (!$this->tieneDetalle($idComprobante)) {
            // Recupero la descripcion del tipo de detalle
            throw new Rad_Db_Table_Exception("El comprobante no tiene registros de detalle asociados del tipo solicitado.");
        }
        return $this;
    }

    /**
     * sale si el comprobante no tiene cheque asociados del tipo solicitado
     *
     * @param int $idComprobante    identificador del comprobante a verificar
     *
     */
    public function salirSi_noTieneChequesAsociados ($idComprobante)
    {
        if (!$this->tieneChequesAsociados($idComprobante)) {
            // Recupero la descripcion del tipo de detalle
            throw new Rad_Db_Table_Exception("El comprobante no tiene cheques asociados del tipo solicitado.");
        }
        return $this;
    }

    /**
     * Sale si el comprobante que se intenta relacionar ya se encuentra relacionado a otro comprobante
     *
     * @param int|Rad_Db_Table_Row $Comprobante     identificador del comprobante a verificar
     *
     */
    public function salirSi_elComprobanteRelacionadoYaEstaRelacionado ($Comprobante)
    {
        if ($this->elComprobanteRelacionadoYaEstaRelacionado($Comprobante)) {
            // Recupero la descripcion del tipo de detalle
            throw new Rad_Db_Table_Exception("El comprobante que intenta relacionar se encuentra relacionado a otro Comprobante.");
        }
        return $this;
    }

    /**
     * sale si el comprobante tiene detalles del tipo indicado con valor 0
     *
     * @param int $idComprobante 	identificador del comprobante a verificar
     *
     */
    public function salirSi_tieneDetalleConValorCero ($idComprobante)
    {
        if ($this->tieneDetalleConValorCero($idComprobante)) {
            throw new Rad_Db_Table_Exception("El comprobante tiene al menos un detalle con Monto cero.");
        }
        return $this;
    }

    /**
     * sale si existe otro comprobante del mismo grupo sin cerrar para esa persona
     *
     * @param int $persona 	            identificador del cliente o proveedor
     * @param int $tipoDeComprobante 	identificador del tipo de comprobante
     * @param int $idComprobante 	    identificador del comprobante a verificar (opcional)
     *
     */
    public function salirSi_existeOtroComprobanteSinCerrar ($persona, $tipoComprobante, $idComprobante = null)
    {
        if ($this->existeOtroComprobanteSinCerrar($persona, $tipoComprobante, $idComprobante = null)) {
            throw new Rad_Db_Table_Exception("Existe otro comprobante del mismo tipo para esta persona que aun no se ha cerrado. Para poder ingresar otro debe cerrar el anterior o eliminarlo.");
        }
        return $this;
    }

    /**
     * Devuelve el id del comprobante por cual compensa
     *
     * @param int|Rad_Db_Table_row $idTipoDeComprobante    identificador del tipo comprobante
     *
     * @return int
     */
    public function compensaPorNota($idTipoDeComprobante)
    {
        $M_TC = new Facturacion_Model_DbTable_TiposDeComprobantes(array(), false);

        $R_TC = $M_TC->fetchRow("Id = $idTipoDeComprobante");

        return $R_TC->CompensaCon;
    }

    /**
     * sale si el comprobante no permite compensar con nota
     *
     * @param int|Rad_Db_Table_row $idTipoDeComprobante    identificador del tipo comprobante
     *
     */
    public function salirSi_noPermiteCompensarPorNota ($idTipoDeComprobante)
    {
        if (!$this->compensaPorNota($idTipoDeComprobante)) {
            throw new Rad_Db_Table_Exception("No se permite compensar el comprobante por Nota.");
        }
        return $this;
    }

    /**
     * sale si el comprobante ya esta compensado por otra nota
     *
     * @param int $idComprobante    identificador del comprobante
     *
     */
    public function salirSi_EstaCompensado($idComprobante)
    {

        $R_C = $this->fetchRow("ComprobanteRelacionado = $idComprobante and Cerrado = 1");

        if ($R_C) {
            throw new Rad_Db_Table_Exception("El comprobante ya tiene una nota de credito asociada.");
        }
        return $this;
    }
    
      /**
     *  Insert
     *
     * @param array $data   Valores que se insertarÃ¡n
     *
     */
    public function insert($data)
    {

        $idComprobante = Rad_Db_Table::insert($data);

        // Obtiene el registro del comprobante.
        $comprobante = $this->find($idComprobante)->current();
        // Obtiene el tipo de comprobante.
        $tipoComprobante = $comprobante->findParentRow("Facturacion_Model_DbTable_TiposDeComprobantes");
        // Log Usuarios
        if ( $comprobante->Numero == 0 ) {
            Rad_Log::user("Nuevo Comprobante ($tipoComprobante->Descripcion ID $idComprobante)");
        } else {
            Rad_Log::user("Nuevo Comprobante ($tipoComprobante->Descripcion NÂº $comprobante->Numero)");
        }
        return $idComprobante;

    }
    
    public function fetchCerrado ($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = "Cerrado = 1 and Anulado = 0";
        $where = $this->_addCondition($where, $condicion);
        return self::fetchAll($where, $order, $count, $offset);
    }

}