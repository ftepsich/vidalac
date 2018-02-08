<?php

/**
 * Base_Model_DbTable_ChequesPropios
 *
 * Cheques propios emitidos por la empresa
 *
 * @class Base_Model_DbTable_ChequesPropios
 * @extends Base_Model_DbTable_Cheques
 */
class Base_Model_DbTable_ChequesPropios extends Base_Model_DbTable_Cheques
{

    protected $_sort = array('Chequera_cdisplay Asc','Numero Asc');
        
    protected $_permanentValues = array(
        'TipoDeEmisorDeCheque' => 1
    );
    
    public function init ()
    {
        unset($this->_referenceMap['Clientes']);
        parent::init();

        // Y esto para que?
        // $config = Rad_Cfg::get();
        if ($this->_fetchWithAutoJoins) {
            $j = $this->getJoiner();
            $j->joinDep('Facturacion_Model_DbTable_ComprobantesDetalles', array(
                'OrdenDePago' => "fNumeroCompleto(ChequesComprobantesDetalles.Comprobante,'G')"
            ), 'Cheques');
                  
        }

        // $this->addAutoJoin(
        //         'ComprobantesDetalles',
        //         'Cheques.Id = ComprobantesDetalles.Cheque',
        //         array(
        //             'OrdenDePago' => "fNumeroCompleto(ComprobantesDetalles.Comprobante,'G')"
        //         ),
        //         'Cheques.Id'
        // );

        
    }

    public function update ($data, $where)
    {
        // Si es automatico ver si ya esta impreso
        //unset($data['Persona']);

        $reg = $this->fetchAll($where);

        foreach ($reg as $row) {
            if(($data['Persona'] != $row['Persona']) && ($row['ChequeEstado'] != 1 || $row['Generador'])){
                throw new Rad_Db_Table_Exception("No se permite modificar la persona a quien va destinado el cheque.");
            }
            
            if ($row['ChequeEstado'] != 6 && $row['ChequeEstado'] != 1 && $row['Impreso']) { 
            	// SI YA ESTA IMPRESO O UTILIZADO SOLO PUEDE MODIFICAR EL CAMPO COBRADO y FECHA COBRO
            	
                // acomodo el formato del Monto para que sea igual del row
                $data['Monto']     = number_format($data['Monto'],2, '.', '');
            	// armo un array con las diferencias entre el data y el row
                $dif               = array_diff_assoc($data, $row->toArray());
                // throw new Rad_Db_Table_Exception(print_r($data,1));
            	// throw new Rad_Db_Table_Exception(print_r($dif,1));
                // Quito los dos campos del array ya que puede venir alguno de los dos o los dos
                // si hay otro campo que se pueda modificar despues de impreso se debe agregar aqui
                if (array_key_exists('Cobrado',$dif)) unset($dif['Cobrado']);
                if (array_key_exists('FechaDeCobro',$dif)) unset($dif['FechaDeCobro']);
				// si borre todos los que se pueden modificar si queda alguno bloqueo la modificacion.
                if (count($dif)) {
	                throw new Rad_Db_Table_Exception('Solo puede modificar cheques vacios, disponibles y no impresos.<br><br>Informacion de Control:<br>'.print_r($dif,1));
				}
            }

            if (!$data['Monto'] || !$data['FechaDeEmision'] || !$data['PagueseA']) {
                throw new Rad_Db_Table_Exception("Deben completarse 'Monto', 'Fecha de Emision' y 'Paguese A'");
            }
            //$data['ChequeEstado'] = 6;
            $data['MontoEnLetras'] = Rad_CustomFunctions::num2letras(round($data['Monto'], 2, PHP_ROUND_HALF_UP));
            
            $this->verificaFechaEmisionMenorFechaCobro($data["FechaDeEmision"],$data["FechaDeCobro"],$data["FechaDeVencimiento"]);            
            
            return Rad_Db_Table::update($data, $where);
        }
    }

    public function fetchDisponibles ($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = "ChequeEstado = 6";
        $where = $this->_addCondition($where, $condicion);
        return self::fetchAll($where, $order, $count, $offset);
    }

    public function fetchSinUsar ($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = "ChequeEstado = 1";
        $where = $this->_addCondition($where, $condicion);
        return self::fetchAll($where, $order, $count, $offset);
    }

    public function fetchConfeccionados ($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = "ChequeEstado <> 1";
        $where = $this->_addCondition($where, $condicion);
        return self::fetchAll($where, $order, $count, $offset);
    }
    
    public function marcarNoImpreso ($ids)
    {
        $cheques = $this->find($ids);
        if (!count($cheques)) {
            throw new Rad_Db_Table_Exception('No se encuentran los cheques');
        }

        try {
            $this->_db->beginTransaction();
            foreach ($cheques as $cheque) {
                if (!$cheque->Impreso) {
                    throw new Rad_Db_Table_Exception('El cheque '.$cheque->Numero.' aun no esta impreso');
                }
                $this->_db->update(
                    $this->_name,
                    array('Impreso' => 0),
                    'Id = '.$cheque->Id
                );
            }
            $this->_db->commit();
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
        return $cheques = $this->find($ids)->toArray();
    }
    
    public function sePuedenImprimir($ids)
    {
        /**
         * 0: No impreso
         * 1: Impreso
         * 2: Para impresion
         */
        // Revisa que todos los cheques que se quieren imprimir
        // no se encuentren impresos todavia
        $yaImpresos = $this->fetchAll("Cheques.Id IN ($ids) AND Impreso = 1");

        if (count($yaImpresos)) {
            throw new Rad_Exception('Algunos de los cheques ya se encuentra impresos');
        }
        $chequesRows = $this->fetchAll("Cheques.Id IN ($ids)","Cheques.Numero asc");

        foreach ($chequesRows as $cheque) {
            if ($chequeAnt && $chequeAnt->Numero + 1 != $cheque->Numero)
                throw new Rad_Exception('Los cheques deben ser correlativos');
            $chequeAnt = $cheque;
        }
    }
    
    /**
     * @param $id array
     */
    public function enviarAImpresora($ids)
    {
        require_once 'PrintIpp/CupsPrintIPP.php';
        
        //$ids = implode(',',$id);
            
        $this->sePuedenImprimir($ids);
        
        try {
            $cfg = Rad_Cfg::get();
            
            // Generamos el PDF
            $report = new Rad_BirtEngine();

            $formato    = "pdf";
            $where      = "Where Id IN ($ids)";
            $file       = APPLICATION_PATH . '/../birt/Reports/Cheques.rptdesign';
           
            $report->renderFromFile($file, 'pdf', array('WHERE' => $where));

            /*------------ modificacion para que no imprima con cups ---------------*/
            $NombreReporte  = "Cheques___".date('YmdHis');
            $report->sendStream($NombreReporte);
            /*----------------------------------------------------------------------*/
            
            /*------------ impresion por cups ---------------*/
            /* 
            $ipp = new CupsPrintIPP();
            $ipp->with_exceptions = true;

            $ipp->handle_http_exceptions = true;
            //$ipp->setAuthentication('root','vidalac116059');
            $ipp->setHost($cfg->Cheques->Cups);
            $ipp->setPrinterURI($cfg->Cheques->Printer);
            $ipp->setCopies(1);
//            $ipp->setAttribute("page-border",'double');
//            $ipp->getJobAttributes();
           
//            $ipp->setAttribute("fit-on-page",false);
            
            $ipp->setData($report->getStream()); // le mandamos el pdf
            
//            $ipp->setAttribute("scaling",105);
            $ipp->setAttribute("position",'top');
            $ipp->setAttribute("page-top", '-1');
            $ipp->validateJob();
          
//            file_put_contents('/var/www/t.txt', print_r($ipp->debug,true));
            $ipp->printStreamJob();
            */
            /*------------ fin impresion por cups ---------------*/
            
            parent::update(array('Impreso' => 1), "Id IN ($ids)");
        
        } catch (Exception $e) {
            //Rad_Log::err($e->getMessage());
            throw new Rad_Exception('Error al imprimir Cheque/s:<br>'.$e->getMessage());
        }
    }

    public function marcarTieneRecibo ($ids)
    {
        $cheques = $this->fetchAll("Cheques.Id IN ($ids)");
        if (!count($cheques)) {
            throw new Rad_Db_Table_Exception('No se encuentran los cheques');
        }
        try {
            $this->_db->beginTransaction();
            foreach ($cheques as $cheque) {
                if ($cheque->TieneRecibo) {
                    throw new Rad_Db_Table_Exception('El cheque '.$cheque->Numero.' ya tiene recibo');
                }
                $this->_db->update(
                    $this->_name,
                    array('TieneRecibo' => 1),
                    'Id = '.$cheque->Id
                );
            }
            $this->_db->commit();
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
        // return $cheques = $this->find($ids)->toArray();
    }    

}
