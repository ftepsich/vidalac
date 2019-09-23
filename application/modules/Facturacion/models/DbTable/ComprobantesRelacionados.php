<?php

class Facturacion_Model_DbTable_ComprobantesRelacionados extends Rad_Db_Table
{

    // Tabla mapeada
    protected $_name = 'ComprobantesRelacionados';
    // Relaciones
    protected $_referenceMap = array(
        'ComprobantePadre' => array(
            'columns' => 'ComprobantePadre',
            'refTableClass' => 'Facturacion_Model_DbTable_Comprobantes',
            //'refJoinColumns'  => array('MontoImponible'),
            //'comboBox'        => true,
            //'comboSource'     => 'datagateway/combolist',
            'refTable' => 'Comprobantes',
            'refColumns' => 'Id'
        ),
        'ComprobanteHijo' => array(
            'columns' => 'ComprobanteHijo',
            'refTableClass' => 'Facturacion_Model_DbTable_Comprobantes',
            //'refJoinColumns'  => array('MontoImponible'),
            //'comboBox'        => true,
            //'comboSource'     => 'datagateway/combolist',
            'refTable' => 'Comprobantes',
            'refColumns' => 'Id'
        )
    );
    /**
     * Variables que modifican el comportamiento de la asociacion, se debe indicar cual es el modelo
     * hijo y cual el modelo padre.
     * Se utilizan en las funciones: asociarComprobanteHijoConPadre y agregarComprobanteHijoAPadre
     */
    protected $_class_comprobantePadre  = "Facturacion_Model_DbTable_ComprobantesDetalles";
    protected $_class_comprobanteHijo   = "Facturacion_Model_DbTable_ComprobantesDetalles";
    protected $_dependentTables         = array("Facturacion_Model_DbTable_ComprobantesRelacionadosDetalles");

    /**
     * Update
     *
     * No se permite esta operacion
     *
     * @param array $data   Valores que se cambiaran
     * @param array $where  Registros que se deben modificar
     *
     */
    public function update($data, $where)
    {
        throw new Rad_Db_Table_Exception("No se puede modificar la relacion entre dos comprobantes. De ser necesario, borre la relacion y luego creela correctamente.");
    }

    /**
     * hijosQueRestanOSuman
     *
     * Devuelve los registros de ComprobantesRelacionados que suman o restan en el comprobante padre.
     * Va a depender del tipo del comprobante padre y del tipo del comprobante hijo.
     *
     * @param int $idPadre  identificador del comprobante padre
     * @param int $op       1 para los que devuelva los que suman y -1 para los que restan
     *
     */
    public function hijosQueRestanOSuman($idPadre,$op) {

        $idPadre = $this->_db->quote($idPadre, 'INTEGER');

        if ($op == 1) {
            $operador = " > ";
        } else {
            $operador = " < ";
        }

        $sql = "select  CR.*,
                        fComprobante_Monto_Disponible(CR.ComprobanteHijo) as Disponible
                from    ComprobantesRelacionados CR
                        inner join Comprobantes C on C.Id = CR.ComprobanteHijo
                where   CR.Comprobantepadre = $idPadre
                and     fSigno_Comprobante_xID(CR.ComprobantePadre,CR.Comprobantehijo) $operador 0
                order by C.FechaEmision asc
                ";

        // Rad_Log::debug($sql);
        return $this->_db->fetchAll($sql);
   }

    /**
     * updatearMontoAsignado
     *
     * Se encarga de actualizar el valor del campo MontoAsignado de la tabla ComprobantesRelacionados
     *
     * @param int|array $post   Array con el detalle del post
     *
     */
    public function updatearMontoAsignado($ComprobantePadre)
    {

        if (is_array($ComprobantePadre)) {
            $idPadre   = $this->_db->quote($ComprobantePadre['Id'], 'INTEGER');
        } else {
            $idPadre   = $this->_db->quote($ComprobantePadre, 'INTEGER');
        }

        //Rad_Log::debug($arr);
        // Veo si viene el padre
        if ($idPadre) {

            // si esta cerrado no tengo que hacer nada

            $R_CR = $this->fetchAll("ComprobantePadre = $idPadre");

            if (count($R_CR)){

                $grupoPago  = array(9,11,22);
                // Veo que el padre sea un comprobante de pago
                $M_C = new Facturacion_Model_DbTable_Comprobantes(array(), false);
                if ( in_array($M_C->recuperarGrupoComprobante($idPadre), $grupoPago) ) {

                // Si tiene un hijo calculo lo asignado

                    $MontoPagado    = 0;
                    $Comp_qSuma     = 0;
                    $Comp_qResta    = 0;

                    $MontoPagado    = $this->_db->fetchOne("SELECT fCompPago_Monto_Pagado($idPadre)");
                    $Comp_qSuma     = $this->_db->fetchOne("SELECT fCompPago_Monto_qSuma($idPadre)");
                    $Comp_qResta    = $this->_db->fetchOne("SELECT fCompPago_Monto_qResta($idPadre)");

                    // throw new Rad_Db_Table_Exception("<".$Comp_qSuma."><".$Comp_qResta."><".$MontoPagado.">");

                    $valoresApagar = $Comp_qSuma - $Comp_qResta;

                    if ($Comp_qResta > $Comp_qSuma) {
                        $diferencia = $Comp_qSuma - $Comp_qResta - $MontoPagado;
                    } else {
                        $diferencia = $Comp_qSuma - $Comp_qResta + $MontoPagado;
                    }

                    // Veo si se pago totalmente o parcialmente
                    if (abs($diferencia) < 0.01) {
                        // ------------------------------------------------------------------------------------
                        // --- Se pago totalmente
                        // ------------------------------------------------------------------------------------
                        // asi que asigno a montoAsociado todo lo disponible de los comprobantes asociados

                        foreach ($R_CR as $row) {
                            // Armo el array para updatear el valor monto asociado de la relacion
                            $where= "Id=".$row['Id'];
                            $data['MontoAsociado'] = $this->_db->fetchOne("SELECT fComprobante_Monto_Disponible(".$row['ComprobanteHijo'].")");
                            parent::update($data,$where);
                        }

                    } else {
                        // ------------------------------------------------------------------------------------
                        // --- Se pago pacialmente
                        // ------------------------------------------------------------------------------------
                        // hay que ver si hay mas a favor o en contra y controlar
                        // que no quede algun comprobante a asociar con valor 0.

                        if ($Comp_qSuma > $Comp_qResta + $MontoPagado) {
                            //  Se pago menos que el total
                            //  =>   lo que resta se asigna todo lo disponible
                            //       y lo que suma se asigna todo lo posible ($Comp_qResta + $MontoPagado)

                            // Recupero los que restan (si existen) y asigno
                            if ($Comp_qResta > 0) {
                                $op = -1;
                                $arrRestan = $this->hijosQueRestanOSuman($idPadre,$op);
                                foreach ($arrRestan as $row) {
                                    // Armo el array para updatear el valor monto asociado de la relacion
                                    $where= "Id=".$row['Id'];
                                    $data['MontoAsociado'] = $row['Disponible'];
                                    parent::update($data,$where);
                                }
                            }

                            // Recupero todos los que suman y asigno
                            $op = 1;
                            $arrSuman = $this->hijosQueRestanOSuman($idPadre,$op);

                            // Ej Fv 120 - ND 20 y Pago de 90 => que de la FV se pagaron 110 y 10 se deben.
                            $MontoPorAsignar = $MontoPagado + $Comp_qResta;
                            foreach ($arrSuman as $row) {
                                // Debo ver si exitan comprobantes que no se utilice nada
                                if ($MontoPorAsignar == 0) {
                                    throw new Rad_Db_Table_Exception("Existen comprobantes a los que no se le afectara nada del pago, retirelos para continuar.");
                                } else {
                                    if ($MontoPorAsignar > $row['Disponible']) {
                                        $MontoPorAsignar = $MontoPorAsignar - $row['Disponible'];
                                        $Asignar = $row['Disponible'];
                                    } else {
                                        $Asignar = $MontoPorAsignar;
                                        $MontoPorAsignar = 0;
                                    }
                                    // Armo el array para updatear el valor monto asociado de la relacion
                                    $where= "Id=".$row['Id'];
                                    $data['MontoAsociado'] = $Asignar;
                                    parent::update($data,$where);
                                }
                            }


                        } else {

                            // TODO : Aca hay que controlar lo del pago en efectivo, si paga en exceso y paga en efectivo tirar error

                            // Incluye los cheques y el efectivo
                            $MontoPagadoEnEfectivo    = $this->_db->fetchOne("SELECT fCompPago_Monto_Pagado_con_Efectivo($idPadre)");

//                          se quito el control por pedido de patricia el 22/12/2015 para comprobante Id = 65441 (Maxi)
//                            if ($MontoPagadoEnEfectivo > 0.0001) {
                                // Pago con efectivo por demás
//                                throw new Rad_Db_Table_Exception("Esta pagando en exceso en efectivo o con cheques, revise los pagos antes de continuar.");
//                            }

                            // Se esta pagando por demás, hay que ver si es un error o se uso parcialmente
                            // una nota que descuenta.
                            if (!$Comp_qResta && $MontoPagado) {
                                // Pago por demas ---> es un error
                               //throw new Rad_Db_Table_Exception("Esta pagando en exceso, revise los pagos antes de continuar.");
                            } else {
                                // Tienen notas que descuentan y/o pagos, alguna de las notas que restan
                                // se usara parcialmente

                                // Recupero todos los que suman y asigno
                                if ($Comp_qSuma > 0) {
                                    $op = 1;
                                    $arrSuman = $this->hijosQueRestanOSuman($idPadre,$op);
                                    foreach ($arrSuman as $row) {
                                        // Armo el array para updatear el valor monto asociado de la relacion
                                        $where= "Id=".$row['Id'];
                                        $data['MontoAsociado'] = $row['Disponible'];
                                        parent::update($data,$where);
                                    }
                                }

                                // Recupero todos los que restan y asigno
                                $op = -1;
                                $arrRestan = $this->hijosQueRestanOSuman($idPadre,$op);

                                if ($Comp_qResta > $Comp_qSuma) {
                                    $MontoPorAsignar = $Comp_qSuma - $MontoPagado;
                                } else {
                                    $MontoPorAsignar = $MontoPagado + $Comp_qSuma;
                                }
                                foreach ($arrRestan as $row) {
                                    // Debo ver si exitan comprobantes que no se utilice nada
                                    if ($MontoPorAsignar == 0) {
                                         throw new Rad_Db_Table_Exception("Existen comprobantes a los que no se le afectara nada del pago, retirelos para continuar.");
                                    } else {
                                        if ($MontoPorAsignar > $row['Disponible']) {
                                            $MontoPorAsignar = $MontoPorAsignar - $row['Disponible'];
                                            $Asignar = $row['Disponible'];
                                        } else {
                                            $Asignar = $MontoPorAsignar;
                                            $MontoPorAsignar = 0;
                                        }
                                        // Armo el array para updatear el valor monto asociado de la relacion
                                        $where= "Id=".$row['Id'];
                                        $data['MontoAsociado'] = $Asignar;
                                        parent::update($data,$where);
                                    }
                                } // foreach ($arrRestan as $row)
                            } // else -- if (!$Comp_qResta && $MontoPagado)
                        } // else -- if ($Comp_qSuma > $Comp_qResta + $MontoPagado)
                    } // else -- if (abs($diferencia) < 0.01)
                } // if ( in_array($M_C->recuperarGrupoComprobante($idPadre), $grupoPago) )
            } // if (count($R_CR))
        } // if ($idPadre)
    } // function



    /**
     * Delete
     *
     * @param array $where  Registros que se deben eliminar
     *
     */
    public function delete($where)
    {
        $this->_db->beginTransaction();
        try {
            $M_C = new Facturacion_Model_DbTable_Comprobantes(array(), false);

            $R_CR = $this->fetchAll($where);

            foreach ($R_CR as $row) {
                $M_C->salirSi_estaCerrado($row->ComprobantePadre);
            }

            // borro los de la Relacion
            $M_CRD = new Facturacion_Model_DbTable_ComprobantesRelacionadosDetalles(array(), false);
            foreach ($R_CR as $row) {
                $M_CRD->delete("ComprobanteRelacionado = $row->Id");
            }

            parent::delete($where);

            $this->_db->commit();
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    /**
     *  recupera el id de la relacion entre dos comprobantes
     *
     * @param int $idPadre  identificador del Comprobante Padre
     * @param int $idHijo   identificador del Comprobante Hijo
     *
     */
    public function recuperarIdRelacion($idPadre, $idHijo)
    {
        $R_FCR = $this->fetchRow("ComprobantePadre = $idPadre and ComprobanteHijo = $idHijo");
        if (!$R_FCR) {
            throw new Rad_Db_Table_Exception('No se encuentra la relacion entre los comprobantes indicados.');
        }
        return $R_FCR->Id;
    }

    /**
     *  recupera la cantidad de un articulo que tiene en el detalle un comprobante
     *
     * @param int $idComprobante    identificador del Comprobante
     * @param int $idArticulo       identificador del Articulo
     *
     */
    public function recuperarCantidadCompDetalle($idComprobante, $idArticulo)
    {
        $sql = " Select ifnull(CD.Cantidad,0)
                from    ComprobantesDetalles CD
                where   CD.Articulo         = $idArticulo
                and     CD.Comprobante      = $idComprobante";
        $Cantidad = $this->_db->fetchOne($sql);

        if ($Cantidad > 0.001) {
            return $Cantidad;
        } else {
            return 0;
        }
    }

    /**
     * Recupera de un Articulo determinado de un Comprobante (Padre) la cantidad del mismo
     * que se ha asociado a algun otro Comprobante (Hijo)
     *
     * @param int $idComprobante    identificador del comprobante
     * @param int $idArticulo       identificador del articulo
     *
     * @return decimal
     */
    public function comprobanteComoPadre_CantAsociada($idComprobante, $idArticulo)
    {

        $sql = "    Select  ifnull(sum(CRD.Cantidad),0) as Cantidad
                    from    ComprobantesRelacionadosDetalles CRD
                            inner join ComprobantesRelacionados CR
                            on CRD.ComprobanteRelacionado = CR.Id
                    where   CRD.Articulo        = $idArticulo
                    and     CR.ComprobantePadre = $idComprobante";
        $CantidadAsociada = $this->_db->fetchOne($sql);

        if ($CantidadAsociada > 0.001) {
            return $CantidadAsociada;
        } else {
            return 0;
        }
    }

    /**
     * Recupera de un Articulo determinado de un Comprobante (Padre) la cantidad del mismo
     * que no se ha asociado a ningun otro Comprobante (Hijo)
     *
     * @param int $idComprobante    identificador del comprobante
     * @param int $idArticulo       identificador del articulo
     *
     * @return decimal
     */
    public function comprobanteComoPadre_CantSinAsociar($idComprobante, $idArticulo)
    {

        $sql = "    Select  ifnull(sum(RA.Cantidad),0) as Cantidad
                    from    ComprobantesDetalles RA
                    where   RA.Articulo     = $idArticulo
                    and     RA.Comprobante  = $idComprobante";
        $CantidadEnDetalle = $this->_db->fetchOne($sql);

        $CantidadAsociada = $this->comprobanteComoPadre_CantAsociada($idComprobante, $idArticulo);

        $CantidadSinAsociar = $CantidadEnDetalle - $CantidadAsociada;

        if ($CantidadSinAsociar > 0.001) {
            return $CantidadSinAsociar;
        } else {
            return 0;
        }
    }

    /**
     * Recupera de un Articulo determinado de un Comprobante (Hijo) la cantidad del mismo
     * que no se ha asociado a ningun otro Comprobante (Padre)
     *
     * @param int $idComprobante    identificador del Comprobante
     * @param int $idArticulo       identificador del articulo
     *
     * @return decimal
     */
    public function comprobanteComoHijo_CantSinAsociar($idComprobante, $idArticulo)
    {

        $sql = "    Select  ifnull(sum(RA.Cantidad),0) as Cantidad
                    from    ComprobantesDetalles RA
                    where   RA.Articulo     = $idArticulo
                    and RA.Comprobante  = $idComprobante";
        $CantidadEnDetalle = $this->_db->fetchOne($sql);

        $sql = "    Select  ifnull(sum(CRD.Cantidad),0) as Cantidad
                    from    ComprobantesRelacionadosDetalles CRD
                            inner join ComprobantesRelacionados CR
                            on CRD.ComprobanteRelacionado = CR.Id
                    where   CRD.Articulo        = $idArticulo
                    and     CR.ComprobanteHijo  = $idComprobante";
        $CantidadAsociada = $this->_db->fetchOne($sql);

        $CantidadSinAsociar = $CantidadEnDetalle - $CantidadAsociada;

        if ($CantidadSinAsociar > 0.001) {
            return $CantidadSinAsociar;
        } else {
            return 0;
        }
    }

    public function getDetalleCantidadesRelacionadas($idComprobante) {

        $idComprobante = $this->_db->quote($idComprobante, 'INTEGER');

        $data = $this->_db->fetchAll("
        select   A.Descripcion, CD.Articulo, ifnull(sum(CD.Cantidad),0) as  CantidadComp, (
                select  ifnull(sum(CRD.Cantidad),0)
                from    `ComprobantesRelacionados` CR,
                        `ComprobantesRelacionadosDetalles` CRD
                where   CR.Id = CRD.ComprobanteRelacionado
                and     CR.ComprobanteHijo = C.Id and CD.Articulo = CRD.Articulo
            ) as  CantidadCompRel
        from  `Comprobantes` C,
            `ComprobantesDetalles` CD,
            `Articulos` A
        where   CD.`Comprobante` = C.`Id` and A.Id = CD.Articulo
        and     C.Id = $idComprobante group by CD.Articulo");

        return $data;
    }

    /**
     * Permite asociar los Articulos de un Comprobante(Hijo) a un Comprobante(Padre) que ya esta cerrado.
     *
     * Ej: Asociar un Remito a una Factura ya cerrada.
     *
     * @param int $idHijo   identificador del Comprobante Hijo a asociar
     * @param int $idPadre  identificador del Comprobante Padre al que se asociara el Hijo
     *
     * @return boolean
     */
    public function asociarComprobanteHijoConPadre($idHijo, $idPadre)
    {
        $this->_db->beginTransaction();
        try {
            $idRel = $this->recuperarIdRelacion($idPadre, $idHijo);

            $M_CRD = new Facturacion_Model_DbTable_ComprobantesRelacionadosDetalles(array(), false);
            $M_CD_P = new $this->_class_comprobantePadre(array(), false);

            $R_CD_P = $M_CD_P->fetchAll("Comprobante = $idPadre");
            if (!$R_CD_P) {
                throw new Rad_Db_Table_Exception('El Comprobante Padre no tienen Articulos.');
            }
            // Recorro los articulos de la factura
            foreach ($R_CD_P as $rowCD_P) {
                $idArticulo = $rowCD_P->Articulo;
                // Busco cuanto del articulo esta asociado entre el Hijo y el Padre
                $sinRecibir = $this->comprobanteComoPadre_CantSinAsociar($idPadre, $idArticulo);
                if ($sinRecibir > 0.00) {
                    // Recupero lo que queda disponible del Articulo del Hijo
                    $disponible = $this->comprobanteComoHijo_CantSinAsociar($idHijo, $idArticulo);
                    if ($disponible > 0.00) {
                        if ($disponible > $sinRecibir) {
                            $cantAsociar = $sinRecibir;
                        } else {
                            $cantAsociar = $disponible;
                        }

                        $data = array('ComprobanteRelacionado' => $idRel,
                            'Articulo' => $idArticulo,
                            'Cantidad' => $cantAsociar
                        );
                        // Veo si para esa relacion ya se agrego ese articulo en ese caso updateo
                        $R_CRD = $M_CRD->fetchRow("ComprobanteRelacionado= $idRel and Articulo= $idArticulo");

                        if ($R_CRD) {
                            $data['Id'] = $R_CRD->Id;
                            $data['Cantidad'] = $R_CRD->Cantidad + $cantAsociar;
                            $M_CRD->update($data, "Id = $R_CRD->Id");
                        } else {
                            $M_CRD->insert($data);
                        }
                    }
                }
            }
            $this->_db->commit();
            return $true;
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    /**
     *  Permite agregar los Articulos de un Comprobante Hijo a un Comprobante Padre que no este cerrado
     *  Se utiliza como parte del paso de seleccionar elementos para una Comprobante que
     *  se esta creando.
     *
     * @param int $idRel        identificador de la relacion entre el remito y la factura
     * @param int $idRemito     identificador del remito que dio ingreso a la mercaderia de la Factura
     *
     */
    public function agregarComprobanteHijoAPadre($idHijo, $idPadre)
    {
        $this->_db->beginTransaction();
        try {

            $idRel = $this->recuperarIdRelacion($idPadre, $idHijo);

            $M_CD_H = new $this->_class_comprobanteHijo();
            $M_CRD  = new Facturacion_Model_DbTable_ComprobantesRelacionadosDetalles();
            $M_CD_P = new $this->_class_comprobantePadre();
            $M_CD   = new Facturacion_Model_DbTable_ComprobantesDetalles();
            // Arreglar para que funcione bien lo de las listas de precio, se modifico para que no ande mal lo de RemitosDeIngreso #220
            // Se cambio esta linea para llamar al hijo (factura ventas articulos) porq sino no trae el precio de la lista de precio en los articulos
            //$M_CD  = new Facturacion_Model_DbTable_FacturasVentasArticulos(array(), false);

            $R_CD_H = $M_CD_H->fetchAll("Comprobante = $idHijo");

            /*
            Tengo que ver el tipo del comprobante Padre es decir al que le estoy agrgando en la relacion en el caso de ser una FV
            debo cambiar el modelo con el que inserto de ComprobantesRelacionadosDetalles a FacturasVentasArticulos para que use
            correctamente la lista de precio
            */

            // Veo el grupo del padre
            $M_C        = new Facturacion_Model_DbTable_Comprobantes();
            $GrupoPadre = $M_C->recuperarGrupoComprobante($idPadre);
            if ($GrupoPadre == 6) {
                $M_FV = new Facturacion_Model_DbTable_FacturasVentasArticulos();
            }

            if (count($R_CD_H)) {
                foreach ($R_CD_H as $rowHijo) {
                    // Recupero lo que queda disponible del articulo.
                    $disponible = $this->comprobanteComoHijo_CantSinAsociar($idHijo, $rowHijo->Articulo);
                    if ($disponible > 0.001) {

                        // Debo verificar si ese articulo esta asociado si no es asi lo inserto
                        $R_CD_P = $M_CD_P->fetchRow(" Comprobante = $idPadre and Articulo = $rowHijo->Articulo");

                        if (!$R_CD_P) {

                            // Inserto el articulo en CD_P ----> ojo lo hago en el modelo CD para que no dispare las publicaciones

                            $dataCD_P = array(
                                'Comprobante'        => $idPadre,
                                'Articulo'           => $rowHijo->Articulo,
                                'Cantidad'           => $disponible,
                                'ConceptoImpositivo' => $M_CD_H->recuperarArticuloIVA($rowHijo->Articulo)
                            );
                            if ($GrupoPadre == 6) {
                                // Es una FV por lo tanto uso el de el modelo especificamente por el tema de las Listas de Precio
                                $M_FV->insert($dataCD_P);
                            } else {
                                $M_CD->insert($dataCD_P);
                            }

                            // inserto la relacion

                            $dataR["Cantidad"] = $disponible;
                            $dataR["Articulo"] = $rowHijo->Articulo;
                            $dataR["ComprobanteRelacionado"] = $this->recuperarIdRelacion($idPadre, $rowHijo->Comprobante);
                            $M_CRD->insert($dataR);

                        } else {
                            // Debo verificar cuanto de los ingresado en CD_P esta asociado, si es igual
                            // agrego todo si no solo la diferencia (update en ambos casos)

                            $cantAsociadaDelArticulo    = $this->comprobanteComoPadre_CantAsociada($idPadre, $rowHijo->Articulo);
                            $cantEnElDetalle            = $this->recuperarCantidadCompDetalle($idPadre, $rowHijo->Articulo);

                            //if ($cantAsociadaDelArticulo < $cantEnElDetalle) {
                            if (abs($cantEnElDetalle - $cantAsociadaDelArticulo) > 0.0001) {

                                $dif = $cantEnElDetalle - $cantAsociadaDelArticulo;

                                if ($dif < $disponible) {
                                    $Cant_Asociar = $disponible;
                                    $Cant_Agregar = $disponible - $dif;
                                } else {
                                    $Cant_Asociar = $dif - $disponible;
                                    $Cant_Agregar = 0;
                                }

                            } else {
                                $Cant_Asociar = $disponible;
                                $Cant_Agregar = $disponible;
                            }

                            // Ahora updateo segun haga falta

                            if ($Cant_Agregar) {
                                $wh = "Id=$R_CD_P->Id";
                                $data["Cantidad"] = $cantEnElDetalle + $Cant_Agregar;
                                $M_CD->update($data,$wh);
                            }

                            if ($Cant_Asociar) {
                                $data2["Cantidad"] = $Cant_Asociar;
                                $data2["Articulo"] = $rowHijo->Articulo;
                                $data2["ComprobanteRelacionado"] = $this->recuperarIdRelacion($idPadre, $rowHijo->Comprobante);
                                $M_CRD->insert($data2);
                            }


                        }

                        // En el caso que el articulo ya este asignado updateo la cantidad, sino inserto
                        /*
                        $M_CD = new Facturacion_Model_DbTable_ComprobantesDetalles(array(), false);

                        if ($M_CD_P->estaElArticuloEnComprobante($dataCD_P['Comprobante'], $dataCD_P['Articulo'])) {
                            $Rx = $M_CD_P->fetchRow("Comprobante = " . $dataCD_P['Comprobante'] . " and Articulo = " . $dataCD_P['Articulo']);
                            if ($Rx) {
                                $dataCD_P["Cantidad"] = $dataCD_P["Cantidad"] + $Rx->Cantidad;
                            }
                            $M_CD->update($dataCD_P, "ComprobantesDetalles.Id = $Rx->Id");
                            $id = $Rx->Id;
                        } else {
                            $M_CD->insert($dataCD_P);
                        }
                        */

                        /*
                        // Inserto la relacion
                        $data = array('ComprobanteRelacionado' => $idRel,
                            'Articulo' => $rowHijo->Articulo,
                            'Cantidad' => $disponible
                        );
                        // Veo si para esa relacion ya se agrego ese articulo en ese caso updateo
                        $R_CRD = $M_CRD->fetchRow("ComprobanteRelacionado= $idRel and Articulo = $rowHijo->Articulo");
                        if ($R_CRD) {
                            $data['Id'] = $R_CRD->Id;
                            $data['Cantidad'] = $R_CRD->Cantidad + $disponible;
                            $M_CRD->update($data, "Id = $R_CRD->Id");
                        } else {
                            $M_CRD->insert($data);
                        }
                        */
                    }
                }
            }
            $this->_db->commit();
            return $idRel;
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    /**
     * Reasocia los elementos de dos Comprobantes (Padre e Hijo)
     *
     * Ej: remitos con una factura.
     * Se utiliza cuando se modifica las cantidades de un articulo en una factura
     * o en el caso que se elimine un articulo de una factura.
     *
     * Se debe usar con el publicador antes de la modificacion del registro
     *
     * @param int $idFactura    identificador de la factura
     * @param int $idArticulo   identificador del articulo
     *
     * @return decimal
     */
    public function reasociarHijoConPadre($rowPreUp)
    {


        $idCD           = $rowPreUp->Id;
        $idArticulo     = $rowPreUp->Articulo;
        $idPadre        = $rowPreUp->Comprobante;
        $cantidadAnt    = $rowPreUp->Cantidad;

        if ($idArticulo) {

            $M_CD = new Facturacion_Model_DbTable_ComprobantesDetalles(array(), false);

            $R_CD = $M_CD->find($idCD)->current();

            if (!$R_CD) {
                // Si no encuentra el registro significa que es un delete
                $cantPorDesasociar = $cantidadAnt;
                $this->desasociar($idPadre, $idArticulo, $cantPorDesasociar);
            } else {
                // Si encuentra el registro significa que es un update
                // Debo recuperar la cantidad actual en la Factura y compararlo con lo
                // que mando para ver cuanto es la diferencia

                if ($R_CD->Articulo != $idArticulo) {
                    throw new Rad_Db_Table_Exception('No se puede modificar el Articulo de un comprobante, de ser necesario elimielo e ingreselo nuevamente.');
                }

                if ($R_CD->Cantidad != $cantidadAnt) {
                    if ($R_CD->Cantidad > $cantidadAnt) {
                        // Incremento la cantidad
                        $cantPorAsociar = $R_CD->Cantidad - $cantidadAnt;
                        // Asocio
                        //Rad_Log::debug($cantPorAsociar);
                        $this->asociar($idPadre, $idArticulo, $cantPorAsociar);
                    } else {

                        // Debo ver si la cantidad que desasocia realmente estaba asociada a algun comprobante Hijo
                        // es decir que se Hubiera seleccionado desde el comprobante hijo y no cargado a mano.
                        $CantidadActualmenteAsociada = $this->comprobanteComoPadre_CantAsociada($idPadre, $idArticulo);


                        if ($CantidadActualmenteAsociada > 0.0001) {
                            // Cantidad que se cargo manualmente y no por relacionar en el wizard
                            $CantidadNOAsociada = $cantidadAnt - $CantidadActualmenteAsociada;
                            // Veo cuanto desasocio comparando el estado antes y despues del registro
                            $CantidadQueDesasocia = $cantidadAnt - $R_CD->Cantidad;

                            if ($CantidadNOAsociada < $CantidadQueDesasocia) {
                                $cantPorDesasociar = $CantidadQueDesasocia - $CantidadNOAsociada;
                                // Desasocio
                                $this->desasociar($idPadre, $idArticulo, $cantPorDesasociar);
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Desasocia los elementos de dos Comprobantes (Padre e Hijo)
     *
     * Ej: remitos con una factura.
     * Se utiliza cuando se decrementa la cantidad de un articulo en una factura
     * o en el caso que se elimine un articulo de una factura.
     *
     * Se debe usar con el publicador antes de la modificacion del registro
     *
     * @param int $idPadre      identificador de la factura
     * @param int $idArticulo   identificador del articulo
     * @param decimal $cantPorDesasociar cantidad que se debe desasociar
     *
     * @return decimal
     */
    public function desasociar($idPadre, $idArticulo, $cantPorDesasociar)
    {
        // Recupero los remitos ordenados del mas nuevo al mas viejo
        $sql = $this->_db->select()
                        ->from(array('RD' => 'ComprobantesDetalles'),
                                array('RD.Cantidad'))
                        ->join(array('R' => 'Comprobantes'),
                                'R.Id = RD.Comprobante',
                                array('R.FechaEmision', 'idRemito' => 'R.Id'))
                        ->join(array('FCR' => 'ComprobantesRelacionados'),
                                'FCR.ComprobanteHijo= R.Id',
                                array('idFCR' => 'FCR.Id'))
                        ->join(array('FCRD' => 'ComprobantesRelacionadosDetalles'),
                                'FCRD.ComprobanteRelacionado= FCR.Id',
                                array('CantAsociada' => 'FCRD.Cantidad',
                                    'idFCRD' => 'FCRD.Id'))
                        ->where('RD.Articulo = ?', $idArticulo)
                        ->where('FCRD.Articulo = ?', $idArticulo)
                        ->where('FCR.ComprobantePadre = ?', $idPadre)
                        ->order('R.FechaEmision DESC');
        // Puede ser que ubiera borrado o modificado productos de una Factura que no
        // esten relacionados a un Remito en ese caso el join viene vacio y no hace
        // falta hacer nada


        $rX = $this->_db->query($sql);
        $R = $rX->fetchAll();

        // Si hay relaciones que modificar las modifico
        if (count($R)) {

            $M_CRD = new Facturacion_Model_DbTable_ComprobantesRelacionadosDetalles(array(), false);

            foreach ($R as $row) {
                if ($cantPorDesasociar > 0.001) {
                    if ($row["CantAsociada"] <= $cantPorDesasociar) {
                        // Elimino el registro de ComprobantesRelacionadosDetalles
                        $M_CRD->delete2("Id=" . $row["idFCRD"]);
                        $cantPorDesasociar = $cantPorDesasociar - $row["CantAsociada"];
                    } else {
                        // Disminuyo la cantidad asociada
                        $data['Cantidad'] = $row["CantAsociada"] - $cantPorDesasociar;
                        $M_CRD->update($data, "Id=" . $row["idFCRD"]);
                        $cantPorDesasociar = 0;
                    }
                }
            }
        }
    }

    /**
     * Asocia los elementos de dos Comprobantes (Padre e Hijo)
     *
     * Ej: remitos con una factura.
     * Se utiliza cuando se incrementa la cantidad de un articulo en una factura
     *
     * Se debe usar con el publicador antes de la modificacion del registro
     *
     * @param int $idPadre      identificador de la factura
     * @param int $idArticulo   identificador del articulo
     * @param decimal $cantPorAsociar cantidad que se debe Asociar
     *
     * @return decimal
     */
    public function asociar($idPadre, $idArticulo, $cantPorAsociar)
    {


        // Recupero los remitos ordenados delcomprobanteComoHijo mas viejo al mas nuevo
        $sql = $this->_db->select()
                        ->from(array('RD' => 'ComprobantesDetalles'),
                                array('RD.Cantidad'))
                        ->join(array('R' => 'Comprobantes'),
                                'R.Id = RD.Comprobante',
                                array('R.FechaEmision', 'idRemito' => 'R.Id'))
                        ->join(array('FCR' => 'ComprobantesRelacionados'),
                                'FCR.ComprobanteHijo= R.Id',
                                array('idFCR' => 'FCR.Id'))
                        ->join(array('FCRD' => 'ComprobantesRelacionadosDetalles'),
                                'FCRD.ComprobanteRelacionado= FCR.Id',
                                array('CantAsociada' => 'FCRD.Cantidad',
                                    'idFCRD' => 'FCRD.Id'))
                        ->where('RD.Articulo = ?', $idArticulo)
                        ->where('FCRD.Articulo = ?', $idArticulo)
                        ->where('FCR.ComprobantePadre = ?', $idPadre)
                        ->order('R.FechaEmision ASC');

        $rX = $this->_db->query($sql);
        $R = $rX->fetchAll();
    
        $idFCRD = 0;
        $cantidadFCRD = 0;

        if (count($R)) {

            $M_CRD = new Facturacion_Model_DbTable_ComprobantesRelacionadosDetalles(array(), false);

            // voy a recorrer los remitos relacionados y ver si alguno tine
            // disponible articulos sin facturar del tipo que se modifico
            foreach ($R as $row) {
                if ($cantPorAsociar > 0.001) {
                    // Recupero lo que queda disponible del articulo.
                    $disponible = $this->comprobanteComoHijo_CantSinAsociar($row["idRemito"], $idArticulo);
                    $cantidadAgregar = 0;
                    if ($disponible > 0.001) {
                        if ($disponible >= $cantPorAsociar) {
                            $cantidadAgregar = $cantPorAsociar;
                            $cantPorAsociar = 0;
                        } else {
                            $cantidadAgregar = $disponible;
                            $cantPorAsociar = $cantPorAsociar - $disponible;
                        }
                        $data['Cantidad'] = $row["CantAsociada"] + $cantidadAgregar;
                        $M_CRD->update($data, "Id=" . $row["idFCRD"]);
                    }
                    $idFCRD = $row["idFCRD"];
                    $cantidadFCRD = $row["CantAsociada"] + $cantidadAgregar;
                }
            }

           // Si existe aun un exceso se lo asignamos al ultimo comprobante relacionado.

            if ($cantPorAsociar > 0.001) {
               if ( $idFCRD <> 0 ) {
                  $data['Cantidad'] = $cantidadFCRD + $cantPorAsociar;
                  $M_CRD->update($data, "Id=" . $row["idFCRD"]);
                  $cantPorAsociar = 0;
               }
            }

            // Si sobro tengo qeu ver si a alguno de los hijos le queda algo sin asociar y en ese caso reasociarlo
            // Ojo en este caso no estan en la tabal CRD aun, asi que tendre que crear la reacion

            $disponible = 0;

            if ($cantPorAsociar > 0.001) {

                $M_CR = new Facturacion_Model_DbTable_ComprobantesRelacionados(array(), false);

                $sql = " select * from ComprobantesDetalles CD
                         where  Comprobante in (select distinct(ComprobanteHijo) from ComprobantesRelacionados where ComprobantePadre = $idPadre )
                         and    Articulo =  $idArticulo ";

                $rY = $this->_db->query($sql);
                $R2 = $rY->fetchAll();

                if ($R2) {
                    foreach ($R2 as $row2) {
                        $disponible = $this->comprobanteComoHijo_CantSinAsociar($row2["Comprobante"], $idArticulo);

                        if ($disponible > 0.001) {
                            if ($disponible >= $cantPorAsociar) {
                                $cantidadAgregar = $cantPorAsociar;
                                $cantPorAsociar = 0;
                            } else {
                                $cantidadAgregar = $disponible;
                                $cantPorAsociar = $cantPorAsociar - $disponible;
                            }

                            // Recupero el id de la Relacion
                            $idRel = $this->recuperarIdRelacion($idPadre, $row2["Comprobante"]);

                            // Armo el array por insertar
                            $data['ComprobanteRelacionado'] = $idRel;
                            $data['Cantidad'] = $cantidadAgregar;
                            $data['Articulo'] = $idArticulo;
                            $M_CRD->insert($data, "Id=" . $row["idFCRD"]);
                        }

                    }
                }
            }
        }
    }

    /**
     * elimina los comprobantes hijos del comprobante indicado
     *
     * @param int $idComprobante    identificador del comprobante
     *
     * @return boolean
     */
    public function eliminarRelacionesHijos($row)
    {
        $R_CR = $this->fetchAll('ComprobantePadre = '.$row->Id);
        $M_CRD = new Facturacion_Model_DbTable_ComprobantesRelacionadosDetalles(array(), false);
            // borro los de la Relacion

        foreach ($R_CR as $row) {
            $M_CRD->eliminarRelacionesDetalleHijo($row->Id);
            parent::delete('Id = '.$row->Id);
        }

    }

}
