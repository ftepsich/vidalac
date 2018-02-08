<?php

class Contable_Model_DbTable_LibrosDiarios extends Rad_Db_Table {

    /**
     * Tabla mapeada de la DB
     * @var string
     */
    protected $_name = 'LibrosDiarios';
    /**
     * Mapa de referencias
     * @var array
     */
    protected $_referenceMap = array(
        'Comprobantes' => array(
            'columns' => 'Comprobante',
            'refTableClass' => 'Facturacion_Model_DbTable_Comprobantes',
            'refJoinColumns' => array('o'),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'Comprobantes',
            'refColumns' => 'Id',
            ));
    /**
     * Modelos dependientes
     * @var array
     */
    protected $_dependentTables = array('Contable_Model_DbTable_LibrosDiariosDetalle');

    /**
     * Une dos array si el array a unir tiene elementos
     *
     * @param array $arrayDestino   Array que se debe agregar
     * @param array $supuestoArray  Array destino o al que se debe agregar el otro
     */

    public function mi_array_merge($arrayDestino,$supuestoArray) {
        if (is_Array($supuestoArray) && !empty($supuestoArray)) {
            $R = array_merge($arrayDestino,$supuestoArray);
            return $R;
        } else {
            return $arrayDestino;
        }
    }

    /**
     * Recuperar las Cuentas de los articulos de un comprobante
     *
     * @param int 	$idComprobante Identificador del Comprobante
     * @param int   $esDebe 1 o 0 dependiendo de si es debe o haber respectivamente
     *
     * @return array
     */
    public function ctas_Articulos($idComprobante, $esDebe) {

        if ($esDebe) {
            $columna1 = "Debe";
            $columna2 = "0 as Haber";
        } else {
            $columna1 = "Haber";
            $columna2 = "0 as Debe";
        }

        // Recupero los registos
        $sql = "select 	$idComprobante as Comprobante,
						PC.Id as Cuenta,
						SUM(CD.PrecioUnitario * CD.Cantidad) as $columna1,
						$columna2
				from 	Comprobantes C
				inner 	join ComprobantesDetalles CD 	on C.Id = CD.Comprobante
				inner 	join Articulos A 				on A.Id = CD.Articulo
				left 	join PlanesDeCuentas PC 		on A.Cuenta = PC.Id
				where 	C.Id = $idComprobante
				group by Cuenta
                ";

        $R = $this->_db->fetchAll($sql);

        // Validamos que los articulos tenga cuentas configuradas
        foreach ($R as $artRow) {
            if ($artRow['Cuenta'] == null) {
                throw new Rad_Db_Table_Exception('Existen articulos en el comprobante que no tienen Cuenta asociada.');
            }
        }

        return $R;
    }

    /**
     * Recuperar el monto de los articulos de un comprobante
     *
     * @param int 	$idComprobante Identificador del Comprobante
     *
     * @return array
     */
    public function montoArticulos($idComprobante) {
        // Recupero los registos
        $sql = "select 	SUM(CD.PrecioUnitario * CD.Cantidad) as Monto
				from 	ComprobantesDetalles CD
				inner 	join Articulos A 				on A.Id = CD.Articulo
				where 	CD.Comprobante = $idComprobante
                and     A.Tipo = 1
                ";
        $R = $this->_db->fetchOne($sql);
        return $R;
    }

    /**
     * Recuperar el monto de los articulos de un comprobante
     *
     * @param int 	$idComprobante Identificador del Comprobante
     *
     * @return array
     */
    public function montoDetalles($idComprobante) {
        // Recupero los registos
        $sql = "select 	SUM(CD.PrecioUnitario * CD.Cantidad) as Monto
				from 	ComprobantesDetalles CD
				where 	CD.Comprobante = $idComprobante
                ";
        $R = $this->_db->fetchOne($sql);
        return $R;
    }

    /**
     * Recuperar las Cuentas de los servicios o casos especielaes
     *
     * @param int 	Identificador del Comprobante
     *
     * @return array
     */
    public function ctas_Casuales($idComprobante, $esDebe) {

        if ($esDebe) {
            $columna1 = "Debe";
            $columna2 = "0 as Haber";
        } else {
            $columna1 = "Haber";
            $columna2 = "0 as Debe";
        }

        // Recupero los registos
        $sql = "select 	$idComprobante as Comprobante,
						CD.CuentaCasual as Cuenta,
						SUM(CD.PrecioUnitario * CD.Cantidad) as $columna1,
						$columna2
				from 	Comprobantes C
				inner 	join ComprobantesDetalles CD 	on C.Id = CD.Comprobante
				where 	C.Id = $idComprobante
                and     ( CD.Articulo is null
                        or
                          CD.Articulo in (Select A.Id from Articulos A where A.Tipo = 3)
                        )
				group by Cuenta
                ";

        $R = $this->_db->fetchAll($sql);

        return $R;
    }

    /**
     * Junta las cuentas casuales y normales de los articulos de un comprobante
     *
     * @param int 	Identificador del Comprobante
     *
     * @return array
     */
    public function ctas_DetalleComprobante($idComprobante, $esDebe) {
        $Cta_Articulo = $this->ctas_Articulos($idComprobante, $esDebe);
        $Cta_Casual = $this->ctas_Casuales($idComprobante, $esDebe);

        $R = array_merge($Cta_Articulo, $Cta_Casual);

        return $R;
    }

    /**
     * Recuperar los conceptos impositivos de cuentas activos de un comprobante
     *
     * @param int 	Identificador del Comprobante
     *
     * @return array
     */
    public function ctas_ImpuestosActivo($idComprobante, $esDebe) {

        if ($esDebe) {
            $columna1 = "Debe";
            $columna2 = "0 as Haber";
        } else {
            $columna1 = "Haber";
            $columna2 = "0 as Debe";
        }

        // Recupero los registos
        $sql = "select 	$idComprobante as Comprobante,
						CI.CuentaActivo as Cuenta,
						sum(ifnull(C.Monto,0)) as $columna1,
						$columna2
				from Comprobantes C
				inner join ConceptosImpositivos CI on CI.Id = C.ConceptoImpositivo
				where C.ComprobantePadre = $idComprobante
				group by CuentaActivo
                ";

        $R = $this->_db->fetchAll($sql);

        return $R;
    }

    /**
     * Recuperar los conceptos impositivos de cuentas pasivos de un comprobante
     *
     * @param int 	Identificador del Comprobante
     *
     * @return array
     */
    public function ctas_ImpuestosPasivo($idComprobante, $esDebe) {

        if ($esDebe) {
            $columna1 = "Debe";
            $columna2 = "0 as Haber";
        } else {
            $columna1 = "Haber";
            $columna2 = "0 as Debe";
        }

        // Recupero los registos
        $sql = "select 	$idComprobante as Comprobante,
						CI.CuentaActivo as Cuenta,
						SUM(ifnull(C.Monto,0)) as $columna1,
						$columna2
				from Comprobantes C
				inner join ConceptosImpositivos CI on CI.Id = C.ConceptoImpositivo
				where C.ComprobantePadre = $idComprobante
				group by CuentaPasivo
                ";

        $R = $this->_db->fetchAll($sql);
        return $R;
    }

    /**
     * Recuperar de un comprobante de pago/cobro la parte que se pago/cobro en efectivo
     *
     * @param int 	Identificador del Comprobante
     *
     * @return array
     */
    public function ctas_Cajas($idComprobante, $esDebe) {

        if ($esDebe) {
            $columna1 = "Debe";
            $columna2 = "0 as Haber";
        } else {
            $columna1 = "Haber";
            $columna2 = "0 as Debe";
        }

        // Recupero los registos
        $sql = "select  $idComprobante as Comprobante,
						CA.Cuenta as Cuenta,
						SUM(ifnull(CD.PrecioUnitario,0)) as $columna1,
						$columna2
				from    ComprobantesDetalles CD
                inner join Cajas CA on CA.Id = CD.Caja
				where 	CD.Comprobante = $idComprobante
                and     CD.Caja is not null
                group by CA.Cuenta
                ";

        $R = $this->_db->fetchAll($sql);
        return $R;
    }

    /**
     * Recuperar de un comprobante de Cobro la parte que se pago con depositos
     *
     * @param int 	Identificador del Comprobante
     *
     * @return array
     */
    public function ctas_CobroConDepositos($idComprobante, $esDebe) {

        if ($esDebe) {
            $columna1 = "Debe";
            $columna2 = "0 as Haber";
        } else {
            $columna1 = "Haber";
            $columna2 = "0 as Debe";
        }

        // Recupero los registos
        $sql = "select  $idComprobante as Comprobante,
						CB.Cuenta as Cuenta,
						SUM(ifnull(CD.PrecioUnitario,0)) as $columna1,
						$columna2
				from    ComprobantesDetalles CD
                inner join TransaccionesBancarias TB on TB.Id = CD.TransaccionBancaria
                inner join CuentasBancarias CB on CB.Id = TB.CtaDestino
				where 	CD.Comprobante = $idComprobante
                and     TB.TipoDeTransaccionBancaria = 2
                group by CB.Cuenta
                ";

        $R = $this->_db->fetchAll($sql);
        return $R;
    }

    /**
     * Recuperar de un comprobante de Cobro la parte que se pago con transferencias bancarias
     *
     * @param int 	Identificador del Comprobante
     *
     * @return array
     */
    public function ctas_CobroConTransferencias($idComprobante, $esDebe) {

        if ($esDebe) {
            $columna1 = "Debe";
            $columna2 = "0 as Haber";
        } else {
            $columna1 = "Haber";
            $columna2 = "0 as Debe";
        }

        // Recupero los registos
        $sql = "select  $idComprobante as Comprobante,
						CB.Cuenta as Cuenta,
						SUM(ifnull(CD.PrecioUnitario,0)) as columna1,
						$columna2
				from    ComprobantesDetalles CD
                inner join TransaccionesBancarias TB on TB.Id = CD.TransaccionBancaria
                inner join CuentasBancarias CB on CB.Id = TB.CtaDestino
				where 	CD.Comprobante = $idComprobante
                and     TB.TipoDeTransaccionBancaria = 1
                group by CB.Cuenta
                ";

        $R = $this->_db->fetchAll($sql);
        return $R;
    }

    /**
     * Recuperar de un comprobante de pago la parte que se pago con depositos
     * Ojo en este caso tengo que poner la caja de la que se saco la plata para
     * hacer el deposito.
     *
     * @param int 	Identificador del Comprobante
     *
     * @return array
     */
    public function ctas_PagoConDepositos($idComprobante, $esDebe) {

        if ($esDebe) {
            $columna1 = "Debe";
            $columna2 = "0 as Haber";
        } else {
            $columna1 = "Haber";
            $columna2 = "0 as Debe";
        }
        // Recupero los registos
        $sql = "select  $idComprobante as Comprobante,
						C.Cuenta as Cuenta,
						SUM(ifnull(CD.PrecioUnitario,0)) as $columna1,
						$columna2
				from    ComprobantesDetalles CD
                inner join TransaccionesBancarias TB on TB.Id = CD.TransaccionBancaria
                inner join Cajas C on TB.Caja = C.Id
				where 	CD.Comprobante = $idComprobante
                and     TB.TipoDeTransaccionBancaria = 2
                group by C.Cuenta
                ";

        $R = $this->_db->fetchAll($sql);
        return $R;
    }

    /**
     * Recuperar de un comprobante de pago la parte que se pago con transferencias bancarias
     *
     * @param int 	Identificador del Comprobante
     *
     * @return array
     */
    public function ctas_PagoConTransferencias($idComprobante, $esDebe) {

        if ($esDebe) {
            $columna1 = "Debe";
            $columna2 = "0 as Haber";
        } else {
            $columna1 = "Haber";
            $columna2 = "0 as Debe";
        }

        // Recupero los registos
        $sql = "select  $idComprobante as Comprobante,
						CB.Cuenta as Cuenta,
						SUM(ifnull(CD.PrecioUnitario,0)) as $columna1,
						$columna2
				from    ComprobantesDetalles CD
                inner join TransaccionesBancarias TB on TB.Id = CD.TransaccionBancaria
                inner join CuentasBancarias CB on CB.Id = TB.CtaDestino
				where 	CD.Comprobante = $idComprobante
                and     TB.TipoDeTransaccionBancaria = 1
                group by CB.Cuenta
                ";

        $R = $this->_db->fetchAll($sql);
        return $R;
    }

    /**
     * Recuperar de un comprobante de pago la parte que se pago con cheques propios
     *
     * @param int 	Identificador del Comprobante
     *
     * @return array
     */
    public function ctas_PagoConChequesPropios($idComprobante, $esDebe) {

        if ($esDebe) {
            $columna1 = "Debe";
            $columna2 = "0 as Haber";
        } else {
            $columna1 = "Haber";
            $columna2 = "0 as Debe";
        }
        // Recupero los registos
        $sql = "select  $idComprobante as Comprobante,
						CB.Cuenta as Cuenta,
						SUM(ifnull(CD.PrecioUnitario,0)) as $columna1,
						$columna2
				from    ComprobantesDetalles CD
                inner join Cheques CH on CH.Id = CD.Cheque
                inner join Chequeras CHS on CHS.Id = CH.Chequera
                inner join CuentasBancarias CB on CB.Id = CD.CtaDestino
				where 	CD.Comprobante = $idComprobante
                and     CH.TipoDeEmisorDeCheque = 1
                group by CB.Cuenta
                ";

        $R = $this->_db->fetchAll($sql);
        return $R;
    }

    /**
     * Recuperar de un comprobante el monto que se pago con cheques propios
     *
     * @param int 	Identificador del Comprobante
     *
     * @return decimal
     */
    public function montosChequesPropios($idComprobante, $esDebe) {
        // Recupero los registos
        $sql = "select	SUM(ifnull(CD.PrecioUnitario,0)) as Monto
				from    ComprobantesDetalles CD
                inner join Cheques CH on CH.Id = CD.Cheque
				where 	CD.Comprobante = $idComprobante
                and     CH.TipoDeEmisorDeCheque = 1
                ";

        $R = $this->_db->fetchOne($sql);
        return $R;
    }

    /**
     * Recuperar de un comprobante el monto que se pago con cheques de terceros
     *
     * @param int 	Identificador del Comprobante
     *
     * @return decimal
     */
    public function montoChequesTerceros($idComprobante) {
        // Recupero los registos
        $sql = "select	SUM(ifnull(CD.PrecioUnitario,0)) as Monto
				from    ComprobantesDetalles CD
                inner join Cheques CH on CH.Id = CD.Cheque
				where 	CD.Comprobante = $idComprobante
                and     CH.TipoDeEmisorDeCheque <> 1
                ";

        $R = $this->_db->fetchOne($sql);
        return $R;
    }

    /**
     * Recuperar de un comprobante de pago la parte que se pago con cheques de terceros
     *
     * @param int 	Identificador del Comprobante
     *
     * @return array
     */
    /*
      public function ctas_PagoConChequesDeTerceros($idComprobante,$esDebe) {
      // Recupero los registos
      $sql = "select  $idComprobante as Comprobante,
      CB.Cuenta as Cuenta,
      SUM(CD.PrecioUnitario) as Monto,
      $esDebe as EsDebe
      from    ComprobantesDetalles CD
      inner join Cheques CH on CH.Id = CD.Cheque
      inner join Chequeras CHS on CHS.Id = CH.Chequera
      inner join CuentasBancarias CB on CB.Id = CD.CtaDestino
      where 	CD.Comprobante = $idComprobante
      and     CH.TipoDeEmisorDeCheque = 1
      group by CB.Cuenta
      ";

      $R = $this->_db->fetchAll($sql);

      return $R;
      }
     */

    /**
     * Arma el detalle del asiento contable segun el tipo de comprobante
     *
     * @param <row> Registro del Comprobante
     *
     * @return array
     */
    public function armarAsientoContableDetalle($row, $Grupo) {
        $Asiento = array();
        $monto = 0;

        switch ($Grupo) {
            case 1: case 13:
                /*
                  FC y NDR
                  -----------------------------
                  Detalle FC o NDR (+A)
                  IVA CF (+A)
                  Percepciones (+A)
                  a Proveedores (+P)
                  -----------------------------
                 */

                $M_FC = new Facturacion_Model_DbTable_FacturasCompras();

                // Recupero la cuenta Proveedores
                $cfg = Rad_Cfg::get();
                $CtaProveedor = $cfg->PlanesDeCuentas->Proveedores->Id;

                // Recupero los renglones del asiento y los coloco en el array Asiento

                // Debe
                $Asiento = $this->mi_array_merge($Asiento,$this->ctas_DetalleComprobante($row->Id, 1));

                $Asiento = $this->mi_array_merge($Asiento,$this->ctas_ImpuestosActivo($row->Id, 1));

                // Haber
                $monto = $M_FC->recuperarMontoTotal($row->Id);
                if ($monto && $monto > 0.0001) {
                    $R = array(array(
                        'Comprobante' => $row->Id,
                        'Cuenta'    => $CtaProveedor,
                        'Haber'     => $monto,
                        'Debe'      => 0)
                    );

                    $Asiento = $this->mi_array_merge($Asiento,$R);

                    $monto = 0;
                }
                break;

            case 6:
                /*
                  FV
                  -----------------------------
                  Ctas por Cobrar (+A)
                  a IVA DF (+P)
                  a Percepciones (+P)
                  a Ventas (+G)
                  -----------------------------
                 */

                $M_FV = new Facturacion_Model_DbTable_FacturasVentas();

                // Recupero la cuenta Proveedores
                $cfg = Rad_Cfg::get();
                $CtaCtasPorCobrar   = $cfg->PlanesDeCuentas->CtasPorCobrar->Id;
                $CtaVentas          = $cfg->PlanesDeCuentas->CtaVentas->Id;

                // Debe
                $monto = $M_FV->recuperarMontoTotal($row->Id);
                if ($monto && $monto > 0.0001) {
                    $R = array(array(
                        'Comprobante' => $row->Id,
                        'Cuenta'    => $CtaCtasPorCobrar,
                        'Debe'     => $monto,
                        'Haber'    => 0)
                    );
                    $Asiento = $this->mi_array_merge($Asiento,$R);
                    $monto = 0;
                }

                // Haber
                $monto = $this->montoArticulos($row->Id);
                Rad_Log::debug($monto);

                if ($monto && $monto > 0.0001) {
                    $R = array(array(
                        'Comprobante' => $row->Id,
                        'Cuenta'    => $CtaVentas,
                        'Haber'     => $monto,
                        'Debe'    => 0)
                    );
                    $Asiento = $this->mi_array_merge($Asiento,$R);
                    $monto = 0;
                }

                $Asiento = $this->mi_array_merge($Asiento,$this->ctas_Casuales($row->Id, 0));
                $Asiento = $this->mi_array_merge($Asiento,$this->ctas_ImpuestosPasivo($row->Id, 0));
                break;
            case 12:
                /*
                  NDE
                  -----------------------------
                  Ctas por Cobrar (+A)
                  a IVA DF (+P)
                  a Percepciones (+P)
                  a Cuentas Casuales (?)
                  a Ventas (+G)
                  -----------------------------
                 */

                $M_FV = new Facturacion_Model_DbTable_FacturasVentas();

                // Recupero las cuentas
                $cfg = Rad_Cfg::get();
                $CtasPorCobrar = $cfg->PlanesDeCuentas->CtasPorCobrar->Id;
                $CtaVentas = $cfg->PlanesDeCuentas->CtaVentas->Id;

                // Recupero los renglones del asiento y los coloco en el array Asiento
                // Debe
                $monto = $M_FV->recuperarMontoTotal($row->Id);
                if ($monto && $monto > 0.0001) {
                    $R = array(array(
                        'Comprobante' => $row->Id,
                        'Cuenta'    => $CtasPorCobrar,
                        'Debe'     => $monto,
                        'Haber'    => 0)
                    );
                    $Asiento = $this->mi_array_merge($Asiento,$R);
                    $monto = 0;
                }

                // Haber
                $monto = $M_FV->recuperarNetoGravado($row->Id);
                if ($monto && $monto > 0.0001) {
                    $R = array(array(
                        'Comprobante' => $row->Id,
                        'Cuenta'    => $CtaVentas,
                        'Haber'     => $monto,
                        'Debe'    => 0)
                    );
                    $Asiento = $this->mi_array_merge($Asiento,$R);
                    $monto = 0;
                }

                $Asiento = $this->mi_array_merge($Asiento,$this->ctas_ImpuestosPasivo($row->Id, 0));
                $Asiento = $this->mi_array_merge($Asiento,$this->ctas_Casuales($row->Id, 0));

                break;
            case 8:
                /*
                  NCR
                  -----------------------------
                  Proveedores (-P)
                  a Detalle FC o NDR (-A)
                  a IVA CF (-A)
                  a Percepciones (-A)
                  -----------------------------
                 */

                $M_FC = new Facturacion_Model_DbTable_FacturasCompras();

                // Recupero la cuenta Proveedores
                $cfg = Rad_Cfg::get();
                $CtaProveedor = $cfg->PlanesDeCuentas->Proveedores->Id;

                // Debe
                $monto = $M_FC->recuperarMontoTotal($row->Id);
                if ($monto && $monto > 0.0001) {
                    $R = array(array(
                        'Comprobante' => $row->Id,
                        'Cuenta'    => $CtaProveedor,
                        'Debe'     => $monto,
                        'Haber'    => 0)
                    );
                    $Asiento = $this->mi_array_merge($Asiento,$R);
                    $monto = 0;
                }

                //Haber
                $Asiento = $this->mi_array_merge($Asiento,$this->ctas_DetalleComprobante($row->Id, 0));
                $Asiento = $this->mi_array_merge($Asiento,$this->ctas_ImpuestosActivo($row->Id, 0));

                break;
            case 7:
                /*
                  NCE
                  -----------------------------
                  IVA DF (+P)
                  Percepciones (+P)
                  Cuentas Casuales (?)
                  Ventas (+G)
                  a Ctas por Cobrar (+A)
                  -----------------------------
                 */

                $M_FV = new Facturacion_Model_DbTable_FacturasVentas();

                // Recupero la cuenta Proveedores
                $cfg = Rad_Cfg::get();
                $CtasPorCobrar = $cfg->PlanesDeCuentas->CtasPorCobrar->Id;
                $CtaVentas = $cfg->PlanesDeCuentas->CtaVentas->Id;

                // Recupero los renglones del asiento y los coloco en el array Asiento
                //Debe
                $monto = $M_FV->recuperarNetoGravado($row->Id);
                if ($monto && $monto > 0.0001) {
                    $R = array(array(
                        'Comprobante' => $row->Id,
                        'Cuenta'    => $CtaVentas,
                        'Debe'     => $monto,
                        'Haber'    => 0)
                    );
                    $Asiento = $this->mi_array_merge($Asiento,$R);
                    $monto = 0;
                }

                $Asiento = $this->mi_array_merge($Asiento,$this->ctas_ImpuestosPasivo($row->Id, 1));
                $Asiento = $this->mi_array_merge($Asiento,$this->ctas_Casuales($row->Id, 1));

                //Haber
                $monto = $M_FV->recuperarMontoTotal($row->Id);
                if ($monto && $monto > 0.0001) {
                    $R = array(array(
                        'Comprobante' => $row->Id,
                        'Cuenta'    => $CtasPorCobrar,
                        'Haber'     => $monto,
                        'Debe'    => 0)
                    );
                    $Asiento = $this->mi_array_merge($Asiento,$R);
                    $monto = 0;
                }

                break;
            case 9:
                /*
                  PAGOS
                  -----------------------------
                  Proveedores (-P)
                  a Impuestos (+P)            -> Retenciones
                  a Caja (-A)                 -> Efectivo y Depositos (y cheques propios)
                  a Bancos (-A)               -> Transferencias
                  a Recaudaciones a Dep (-A)  -> Cheques de Terceros
                  a Cheques entregados (+P) ??  -> Cheques Propios  ---->   Por ahora no, ya que no se cargan los
                                                                            asientos de las transacciones bancarias
                  -----------------------------
                 */

                $M_OP = new Facturacion_Model_DbTable_OrdenesDePagos();

                // Recupero las cuentas
                $cfg = Rad_Cfg::get();
                $CtaProveedores = $cfg->PlanesDeCuentas->Proveedores->Id;
                $CtaRecaudacionesADepositar = $cfg->PlanesDeCuentas->RecaudacionesADepositar->Id;
                $CtaCaja = $cfg->PlanesDeCuentas->Cajas->Id;

                // Debe
                $monto = $M_OP->recuperarMontoTotal($row->Id);
                if ($monto && $monto > 0.0001) {
                    $R = array(array(
                        'Comprobante' => $row->Id,
                        'Cuenta'    => $CtaProveedores,
                        'Debe'     => $monto,
                        'Haber'    => 0)
                    );
                    $Asiento = $this->mi_array_merge($Asiento,$R);
                    $monto = 0;
                }

                // Haber
                $monto = $M_OP->montoChequesTerceros($row->Id);
                if ($monto && $monto > 0.0001) {
                    $R = array(array(
                        'Comprobante' => $row->Id,
                        'Cuenta'    => $CtaRecaudacionesADepositar,
                        'Haber'     => $monto,
                        'Debe'    => 0)
                    );
                    $Asiento = $this->mi_array_merge($Asiento,$R);
                    $monto = 0;
                }

                $monto = $M_OP->montoChequesPropios($row->Id);
                if ($monto && $monto > 0.0001) {
                    $R = array(array(
                        'Comprobante' => $row->Id,
                        'Cuenta'    => $CtaCaja,
                        'Haber'     => $monto,
                        'Debe'    => 0)
                    );
                    $Asiento = $this->mi_array_merge($Asiento,$R);
                    $monto = 0;
                }

                $Asiento = $this->mi_array_merge($Asiento,$this->ctas_Cajas($row->Id, 0));
                $Asiento = $this->mi_array_merge($Asiento,$this->ctas_ImpuestosPasivo($row->Id, 0));
                $Asiento = $this->mi_array_merge($Asiento,$this->ctas_PagoConTransferencias($row->Id, 0));
                /* El pago con depositos sale de la caja con que se hizo el deposito */
                $Asiento = $this->mi_array_merge($Asiento,$this->ctas_PagoConDepositos($row->Id, 0));

                break;
            case 11:
                /*
                  COBROS
                  -----------------------------
                  Impuestos (+A)                -> Retenciones
                  Caja (+A)                     -> Efectivo
                  Bancos (+A)                   -> Depositos, Cheques Propios y Transferencias
                  Recaudaciones a Dep (+A)      -> Cheques de Terceros
                  a Cuentas por Cobrar (-A)
                  -----------------------------
                 */

                $M_R = new Facturacion_Model_DbTable_Recibos();

                // Recupero las cuentas
                $cfg = Rad_Cfg::get();
                $CtaBanco                   = $cfg->PlanesDeCuentas->Bancos->Id;
                $CtaRecaudacionesADepositar = $cfg->PlanesDeCuentas->RecaudacionesADepositar->Id;
                $CtaCaja                    = $cfg->PlanesDeCuentas->Cajas->Id;
                $CtaCtasPorCobrar           = $cfg->PlanesDeCuentas->CtasPorCobrar->Id;

                // Debe
                $Asiento = $this->mi_array_merge($Asiento,$this->ctas_ImpuestosPasivo($row->Id, 1));
                $Asiento = $this->mi_array_merge($Asiento,$this->ctas_Cajas($row->Id, 1));
                $Asiento = $this->mi_array_merge($Asiento,$this->ctas_PagoConTransferencias($row->Id, 1));
                $Asiento = $this->mi_array_merge($Asiento,$this->ctas_PagoConDepositos($row->Id, 1));

                $monto = $M_R->montoChequesPropios($row->Id);
                if ($monto && $monto > 0.0001) {
                    $R = array(array(
                        'Comprobante' => $row->Id,
                        'Cuenta'    => $CtaBanco,
                        'Debe'     => $monto,
                        'Haber'    => 0)
                    );
                    $Asiento = $this->mi_array_merge($Asiento,$R);
                    $monto = 0;
                }
                $monto = $M_R->montoChequesTerceros($row->Id);
                if ($monto && $monto > 0.0001) {
                    $R = array(array(
                        'Comprobante' => $row->Id,
                        'Cuenta'    => $CtaRecaudacionesADepositar,
                        'Debe'     => $monto,
                        'Haber'    => 0)
                    );
                    $Asiento = $this->mi_array_merge($Asiento,$R);
                    $monto = 0;
                }

                // Haber
                $monto = $M_R->recuperarMontoTotal($row->Id);
                if ($monto && $monto > 0.0001) {
                    $R = array(array(
                        'Comprobante' => $row->Id,
                        'Cuenta'    => $CtaCtasPorCobrar,
                        'Haber'     => $monto,
                        'Debe'    => 0)
                    );
                    $Asiento = $this->mi_array_merge($Asiento,$R);
                    $monto = 0;
                }
                break;

            case 14: case 15: case 16:
                /*
                  GB
                  -----------------------------
                  Comisiones y Gastos Bancarios (+Per)
                  IVA CF (+A)
                  a Bancos (-A)
                  -----------------------------
                 */

                $M_FC = new Facturacion_Model_DbTable_FacturasCompras();

                // Recupero la cuenta Bancos
                $cfg = Rad_Cfg::get();
                $CtaBancos = $cfg->PlanesDeCuentas->Bancos->Id;
                $CtaGastosBancarios = $cfg->PlanesDeCuentas->GastosBancarios->Id;

                // Recupero los renglones del asiento y los coloco en el array Asiento

                // Debe
                $monto = $this->montoDetalles($row->Id);
                if ($monto && $monto > 0.0001) {
                    $R = array(array(
                        'Comprobante' => $row->Id,
                        'Cuenta'      => $CtaGastosBancarios,
                        'Debe'     => $monto,
                        'Haber'    => 0)
                    );
                    $Asiento = $this->mi_array_merge($Asiento,$R);
                    $monto = 0;
                }

                $Asiento = $this->mi_array_merge($Asiento,$this->ctas_ImpuestosActivo($row->Id, 1));

                // Haber
                $monto = $M_FC->recuperarMontoTotal($row->Id);
                if ($monto && $monto > 0.0001) {
                    $R = array(array(
                        'Comprobante' => $row->Id,
                        'Cuenta'    => $CtaBancos,
                        'Haber'     => $monto,
                        'Debe'    => 0)
                    );
                    $Asiento = $this->mi_array_merge($Asiento,$R);
                    $monto = 0;
                }

                // Si es una liquidacion de cheques tengo que hacer un asiento para registrar
                // que los cheques se cobraron, por ahora no ya que no se estan cargando los
                // asientos de las transacciones bancarias
                /*
                if ($Grupo == 16) {

                    // Inserto la cabecera del segundo asiento
                    $Cabecera = array(
                        'Comprobante' => $row->Id,
                        'NroAsiento' => 2,
                        'FechaAsiento' => date('Y-m-d H:i:s')
                    );
                    $idAsiento = $this->insert($Cabecera);

                    // Recupero los datos de los cheques
                    // todo: debo recuperar el total por cuenta bancaria

                    //-----------------------------
                    //Comisiones y Gastos Bancarios (+Per)
                    //IVA CF (+A)
                    //a Bancos (-A)
                    //-----------------------------


                }
                */


                break;

        }

        return $Asiento;
    }

    /**
     * Arma el asiento contable segun el tipo de comprobante
     *
     * @param <row> Registro del Comprobante
     *
     * @return none
     */
    public function armarAsientoContable($row) {

        $M_TC = new Facturacion_Model_DbTable_TiposDeComprobantes();
        $M_GC = new Facturacion_Model_DbTable_TiposDeGruposDeComprobantes();

        // Recupero el detalle del tipo de comprobante para usar el grupo
        $R_TC = $M_TC->find($row->TipoDeComprobante)->current();
        // Recupero el detalle del grupo del comprobante para saber si se debe asentar
        $R_GC = $M_GC->find($R_TC->Grupo)->current();

        if ($R_GC && $R_GC->GeneraAsiento) {

            $M_LDD = new Contable_Model_DbTable_LibrosDiariosDetalle();

            $Asiento = $this->armarAsientoContableDetalle($row,$R_TC->Grupo);

            // Antes de insertar borro si ya existe
            $this->quitarAsientoContable($row);

            // Inserto la cabecera
            $Cabecera = array(
                'Comprobante' => $row->Id,
                'NroAsiento' => 1,
                'FechaAsiento' => date('Y-m-d H:i:s')
            );
            $idAsiento = $this->insert($Cabecera);

            // Recorro los renglones del asiento insertando el detalle
            foreach ($Asiento as $row) {
                $row['Asiento'] = $idAsiento;
                // Rad_Log::debug($row);
                if($row['Debe'] || $row['Haber']) {
                    $id = $M_LDD->insert($row);
                }
            }
        }
    }

    /**
     * Borra el asiento contable de un comprobante,
     * el detalle se borra por la cascada de la relacion
     *
     * @param <row> Registro del Comprobante
     *
     * @return none
     */
    public function quitarAsientoContable($row) {
        /**
         * El borrado del detalle (LibrosDiariosDetalles) se realiza por integridad en la base
         */
        if (!$row->Id) {
            throw new Rad_Db_Table_Exception("No se localiza el identificador del comprobante.");
        } else {
            $this->delete("Comprobante = ".$row->Id);
        }
    }

}

