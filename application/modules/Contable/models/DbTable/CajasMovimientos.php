<?php

/**
 * Contable_DbTable_CajasMovimientos
 *
 * Detalle de los movimientos de las Cajas
 *
 * @copyright SmartSoftware Argentina
 * @class Contable_DbTable_CajasMovimientos
 * @extends Rad_Db_Table
 * @package Aplicacion
 * @subpackage Contable
 */
class Contable_Model_DbTable_CajasMovimientos extends Rad_Db_Table
{

    protected $_name = 'CajasMovimientos';
    protected $_defaultSource = self::DEFAULT_CLASS;
    protected $_sort = 'Fecha desc';

    protected $_referenceMap = array(
        'Cajas' => array(
            'columns' => 'Caja',
            'refTableClass' => 'Contable_Model_DbTable_Cajas',
            'refJoinColumns' => array('Descripcion'),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'Cajas',
            'refColumns' => 'Id',
        ),
        'TiposDeMovimientosCajas' => array(
            'columns' => 'TipoDeMovimiento',
            'refTableClass' => 'Contable_Model_DbTable_TiposDeMovimientosCajas',
            'refJoinColumns' => array('Descripcion'),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'TiposDeMovimientosCajas',
            'refColumns' => 'Id',
        ),
        'ComprobantesDetalles' => array(
            'columns' => 'ComprobanteDetalle',
            'refTableClass' => 'Facturacion_Model_DbTable_ComprobantesDetalles',
            'refJoinColumns' => array('Comprobante'),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'ComprobantesDetalles',
            'refColumns' => 'Id',
        )
    );
    
    protected $_calculatedFields = array(
        'NumeroCompleto' => "fNumeroCompleto(ComprobantesDetalles.Comprobante,'') COLLATE utf8_general_ci"
    );
    
    public function init()
    {
        $this->_defaultValues = array (
            'Fecha' => date('Y-m-d H:i:s'),
        );
        parent::init();
    }

    /**
     * Borra los movimientos de cajas q no esten asociados a un comprobante detalle
     * 
     * @param array $where 	Registros que se deben eliminar
     */
    public function delete ($where)
    {
        try {
            $this->_db->beginTransaction();
            $reg = $this->fetchAll($where);

            foreach ($reg as $R_CM) {
                if(!$R_CM->ComprobanteDetalle){
                    parent::delete('Id =' . $R_CM->Id);  
                } else {
                    throw new Rad_Db_Table_Exception('No se puede eliminar el movimiento porque esta asociado a un comprobante.');
                }
            }
            $this->_db->commit();
            return true;
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }    
    
    /**
     * Recupera el monto de una caja determinada
     * 
     * @param int $idCaja Identificador de la caja
     * @return float
     * 
     */
    public function saldoCaja($idCaja) {
        $sql = "select  ifnull(sum(Monto),0) 
                from    CajasMovimientos CM 
                        inner join TiposDeMovimientosCajas TCM 
                        on CM.TipoDeMovimiento = TCM.Id and TCM.EsDeArqueo <> 1
                where   CM.Caja = $idCaja";
    
        return $this->_db->fetchOne($sql);
    }
    
    /**
     * Asienta un movimiento de una caja que provengan de un comprobante determinado
     *
     * @param array $data 	Valores que se insertaran
     *
     */
    public function asentarMovimientoDesdeComprobante($rowComprobante) {

        if (!$rowComprobante->Id) {
            throw new Rad_Db_Table_Exception("No se localiza el identificador del comprobante.");
        } else {
            
            $M_C = new Facturacion_Model_DbTable_Comprobantes(array());
            
            // Averiguo el grupo del comprobante, esto solo es para Ordenes de Pago y para Recibos
            $grupo = $M_C->recuperarGrupoComprobante($rowComprobante);           
            
            $multiplicador = 1;
            
            if ($grupo == 9 || $grupo == 11) {
                
                // Si habia registros de este comprobante los elimino primero para no duplicarlos
                $this->quitarMovimientoDesdeComprobante($rowComprobante);                
                
                // Si es Orden de Pago verifico el saldo
                if ($grupo == 9) {
                    $multiplicador = -1;
                    $sql = " select sum(CD.PrecioUnitario) as Monto,
                                    C.Id as Caja,
                                    C.PermiteNegativo,
                                    C.Descripcion as NombreCaja
                             from   ComprobantesDetalles CD
                                    inner join Cajas C on C.Id = CD.Caja
                             where  CD.Comprobante = $rowComprobante->Id
                             group by CD.Caja                    
                            ";                
                    $R = $this->_db->fetchAll($sql);                    
                    foreach ($R as $row) {
                        if (!$row['PermiteNegativo']) {
                            // Veo si da el saldo
                            $saldoCaja      = $this->saldoCaja($row['Caja']);
                            $montoExtraer   = $row['Monto'];
                            if ($saldoCaja - $montoExtraer < 0.0001 ){                                
                                throw new Rad_Db_Table_Exception("La {$row['NombreCaja']} no tiene saldo suficiente para la operacion que intenta realizar.");
                            }                                
                        }                        
                    } // foreach ($R as $row)
                }  // if (count($R))                
             
                // Todo OK, asiento los movimientos de la caja
                $sql = " select CD.PrecioUnitario as Monto,
                                CD.Id as ComprobanteDetalle,
                                CD.Comprobante as IdComprobante,
                                C.Id as Caja,
                                C.PermiteNegativo,
                                C.Descripcion as NombreCaja
                         from   ComprobantesDetalles CD
                                inner join Cajas C on C.Id = CD.Caja
                         where  CD.Comprobante = {$rowComprobante['Id']}                   
                        ";
                                        
                $R = $this->_db->fetchAll($sql);                    
                foreach ($R as $row) {
                    $Renglon = array(
                        'Caja' => $row['Caja'],
                        'Monto' => $row['Monto'] * $multiplicador,
                        'ComprobanteDetalle' => $row['ComprobanteDetalle'],
                        'Fecha' => date('Y-m-d H:i:s'),
                        'TipoDeMovimiento' => 4,
                        'Descripcion' =>$M_C->recuperarDescripcionComprobante($row['IdComprobante'])
                    );
                    $this->insert($Renglon);
                } // foreach ($R as $row)                          
            } // if ($grupo == 9 || $grupo == 11)
        }
        return true;
    }

    /**
     * Quitar movimiento de una caja que provengan de un comprobante determinado
     *
     * @param array $data 	Valores que se insertaran
     *
     */
    public function quitarMovimientoDesdeComprobante($rowComprobante) {

        if (!$rowComprobante->Id) {
            throw new Rad_Db_Table_Exception("No se localiza el identificador del comprobante.");
        } else {
            try { 
                $this->_db->beginTransaction();
                // Recupero el detalle de lo que se pago en efectivo
                $M_CD = new Facturacion_Model_DbTable_ComprobantesDetalles(array());
                $R_CD = $M_CD->fetchAll("Comprobante = $rowComprobante->Id");
                if ($R_CD) {
                    foreach ($R_CD as $row) {
                        $this->delete("ComprobanteDetalle = ".$row->Id);
                    }
                }
                $this->_db->commit();
                return true;
            } catch (Exception $e) {
                $this->_db->rollBack();
                throw $e;
            }            
        }
    }   

    /**
     * Asienta un movimiento de una caja que provengan de una Transaccion
     *
     * @param array $data 	Valores que se insertaran
     *
     */
    public function asentarMovimientoDesdeTransaccion($rowTransaccion) {
       
        $rt = $rowTransaccion;
       
        if ($rt->Caja) {
            try {            
                $this->_db->beginTransaction();
                // Veo que tenga el monto sino tiro error
                if ($rt->Monto < 0.0001) {
                    throw new Rad_Db_Table_Exception("No se ha ingresado el monto o el mismo es menor que cero.");
                }
                $Monto = $rt->Monto;
                // Veo que signo tiene que tener el monto basado en el tipo de Transaccion
                if ($rt->TipoDeMovimiento == 2 || $rt->TipoDeTransaccionBancaria == 2) {
                    $Monto = $Monto * (-1);
                    if($rt->TipoDeTransaccionBancaria == 2) {
                        $Descripcion = "Deposito Saliente (De Caja Propia a Cuenta Propia)";
                    } else {
                        $Descripcion = "Deposito Saliente (De Caja Propia a Cuenta Proveedor)";                        
                    }
                } else {
                    $Descripcion = "Extraccion Bancaria (De Cuenta Propia a Caja Propia)";
                }
                
                if ($rt->Numero) $Descripcion = $Descripcion." N: ".$rt->Numero;
                
                // Armo un array del movimiento en caja
                $RenglonCajaMovimiento = array(
                    'Descripcion'           => $Descripcion,
                    'Caja'                  => $rt->Caja,
                    'Monto'                 => $Monto,
                    'Fecha'                 => $rt->Fecha. " 00:00:00",
                    'TiposDemovimientos'    => 3,
                    'Observaciones'         => $rt->Observaciones,
                    'TransaccionBancaria'  => $rt->Id
                );

                //Inserto
                $id = $this->insert($RenglonCajaMovimiento);
            
                $this->_db->commit();
                return true;
            } catch (Exception $e) {
                $this->_db->rollBack();
                throw $e;
            }
        }                 
    }
    
    /**
     * Quitar movimiento de una caja que provengan de una Transaccion
     *
     * @param array $data 	Valores que se insertaran
     *
     */
    public function quitarMovimientoDesdeTransaccion($rowTransaccion) {

        $rt = $rowTransaccion;
       
        if ($rt->Caja) {
            try { 
                $this->_db->beginTransaction();
                $this->delete("TransaccionBancaria = $rt->Id and Caja = $rt->Caja ");
                $this->_db->commit();
                return true;
            } catch (Exception $e) {
                $this->_db->rollBack();
                throw $e;
            }
        }
    }  

    /**
     *  Permite mover dinero de una caja a otra.
     *
     * @param array $CajaOrigen 	
     * @param array $CajaDestino 	
     * @param array $Descripcion 	
     * @param array $Monto              
     * @param array $Monto              
     * @param array $Fecha             
     *
     */
    public function movimientosEntreCajas($CajaOrigen,$CajaDestino,$Descripcion,$Monto,$Fecha) {
        try { 
            $this->_db->beginTransaction();
            
            $M_C = new Contable_Model_DbTable_Cajas(array(), false);
            
            $R_CO = $M_C->find($CajaOrigen)->current();
            
            if (!count($R_CO)) {
                throw new Rad_Db_Table_Exception("No se encontro la Caja Origen.");
            }  
            
            $R_CD = $M_C->find($CajaDestino)->current();
            
            if (!count($R_CD)) {
                throw new Rad_Db_Table_Exception("No se encontro la Caja Destino.");
            }                  
                           
            if ($Monto <= 0) {
                throw new Rad_Db_Table_Exception("El monto debe ser mayor a 0 (cero).");
            } 
            
            if(!$R_CO->PermiteNegativo && $Monto > $M_C->recuperarSaldoCaja($CajaOrigen)) {
                throw new Rad_Db_Table_Exception("El monto supera al saldo de la Caja.");
            }
            
            $RenglonExtraccionCaja = array(
                'Caja' => $CajaOrigen,
                'Monto' => $Monto*(-1),
                'Fecha' => $Fecha,
                'TipoDeMovimiento' => 3,
                'Descripcion' =>'Movimiento entre Caja. Se transfirio a la caja: '.$R_CD->Descripcion,
                'Observaciones' => $Descripcion   
            );
            $this->insert($RenglonExtraccionCaja);
            
            $RenglonIngresoCaja = array(
                'Caja' => $CajaDestino,
                'Monto' => $Monto,
                'Fecha' => $Fecha,
                'TipoDeMovimiento' => 3,
                'Descripcion' =>'Movimiento entre Caja. Ingreso de la caja: '.$R_CO->Descripcion,
                'Observaciones' => $Descripcion   
            );
            $this->insert($RenglonIngresoCaja);
            $this->_db->commit();

            return true;
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    public function fetchDeEntrada($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = "CajasMovimientos.Monto >= 0";
        $where = $this->_addCondition($where,$condicion);
        return parent:: fetchAll($where, $order, $count, $offset);
    }

    public function fetchDeSalida($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = "CajasMovimientos.Monto < 0";
        $where = $this->_addCondition($where,$condicion);
        return parent:: fetchAll($where, $order, $count, $offset);
    }
    
}
