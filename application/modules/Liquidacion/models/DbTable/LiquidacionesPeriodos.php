<?php
class Liquidacion_Model_DbTable_LiquidacionesPeriodos extends Rad_Db_Table
{
    protected $_name = 'LiquidacionesPeriodos';

    protected $_referenceMap    = array(

	    'TiposDeLiquidacionesPeriodos' => array(
            'columns'           => 'TipoDeLiquidacionPeriodo',
            'refTableClass'     => 'Liquidacion_Model_DbTable_TiposDeLiquidacionesPeriodos',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'			=> true,
            'comboSource'		=> 'datagateway/combolist',
            'refTable'			=> 'TiposDePeriodosDeLiquidaciones',
            'refColumns'        => 'Id',
            'comboPageSize'     => '10'
        )
    );

    protected $_dependentTables = array('Liquidacion_Model_DbTable_Liquidaciones');

    /**
     * Validadores
     *
     * FechaHasta    -> mayor a fechadesde
     *
     */
    protected $_validators = array(
        'FechaHasta'=> array(
            array( 'GreaterThan',
                    '{FechaDesde}'
            ),
            'messages' => array('La fecha de baja no puede ser menor e igual que la fecha de alta.')
        )
    );
    
    /**
     * Devuelve el objeto periodo de un periodo solicitado
     *
     * @param  int                          $id     identificador del periodo
     * @return Liquidacion_Model_Periodo             objeto periodo
     */
    public function getPeriodo($id)
    {
        $id = $this->_db->quote($id, 'INTEGER');
        $p  = $this->find($id)->current();
        if ($p) return new Liquidacion_Model_Periodo($p->FechaDesde, $p->FechaHasta, $id);
        return null;
    }

    /**
     * Inserta los periodos correspondientes a los parametros que recibe
     *
     * @param  int  $anio    año del periodo a liquidar
     * @param  int  $tipo    tipo del periodo ya sea mensual, quincenal, semanal
     * @return none
     */
    public function generarLiquidacionesPeriodos($anio,$tipo)
    {

        //throw new Rad_Db_Table_Exception(print_r($data,true));
        $this->_db->beginTransaction();
        try {

            if(!$anio || !$tipo) throw new Rad_Exception("Faltan los parametros correspondientes.");

            $periodo = $this->fetchRow("Anio = $anio and TipoDeLiquidacionPeriodo = $tipo");

            if($periodo) throw new Rad_Exception("Ya Existen Periodos para el año $anio.");

            if($tipo == 1){ //mensual

                for ($mes = 1; $mes <= 12; $mes++){
                    $fd = date("Y-m-d",(mktime(0, 0, 0, $mes, 01, $anio)));
                    $fh = date("Y-m-d",(mktime(0, 0, 0, $mes, date("d",(mktime(0,0,0,$mes+1,1,$anio)-1)), $anio)));

                    //inserto el peridodo
                    $Renglon = array(
                        'Anio'                      => $anio,
                        'Valor'                     => $mes,
                        'FechaDesde'                => $fd,
                        'FechaHasta'                => $fh,
                        'TipoDeLiquidacionPeriodo'  => $tipo,
                        'Descripcion'               => $anio . " - " . str_pad($mes, 2, "0", STR_PAD_LEFT)
                    );
                    $row = $this->createRow($Renglon);
                    $row->save();
                }
            } else {
            if($tipo == 2) { //quincenal
                $contador_quincena = 1;
                for ($mes = 1; $mes <= 12; $mes++){
                    //primer quincena
                    $fd1 = date("Y-m-d",(mktime(0, 0, 0, $mes, 01, $anio)));
                    $fh1 = date("Y-m-d",(mktime(0, 0, 0, $mes, 15, $anio)));
                    //inserto el peridodo
                    $Renglon1 = array(
                        'Anio'                      => $anio,
                        'Valor'                     => $contador_quincena,
                        'FechaDesde'                => $fd1,
                        'FechaHasta'                => $fh1,
                        'TipoDeLiquidacionPeriodo'  => $tipo,
                        'Descripcion'               => $anio . " - q" . str_pad($contador_quincena, 2, "0", STR_PAD_LEFT)
                    );
                    $row1 = $this->createRow($Renglon1);
                    $row1->save();
                    $contador_quincena += 1;
                    $fd2 = date("Y-m-d",(mktime(0, 0, 0, $mes, 16, $anio)));
                    $fh2 = date("Y-m-d",(mktime(0, 0, 0, $mes, date("d",(mktime(0,0,0,$mes+1,1,$anio)-1)), $anio)));
                    //inserto el peridodo
                    $Renglon2 = array(
                        'Anio'                      => $anio,
                        'Valor'                     => $contador_quincena,
                        'FechaDesde'                => $fd2,
                        'FechaHasta'                => $fh2,
                        'TipoDeLiquidacionPeriodo'  => $tipo,
                        'Descripcion'               => $anio . " - q" . str_pad($contador_quincena, 2, "0", STR_PAD_LEFT)
                    );
                    $row2 = $this->createRow($Renglon2);
                    $row2->save();
                    $contador_quincena += 1;
                }
            } else {
                if($tipo == 3) {//semanal

                    $primer_dia_semana = mktime(0, 0, 0, 01, 01, $anio);
                    $ultimo_dia_semana = mktime(0, 0, 0, 01, 01, $anio);
                    $contador_semana = 1;

                    while(date("W",$primer_dia_semana) >= $contador_semana){

                        if(date("w",$primer_dia_semana)!=1){
                            $primer_dia_semana -= (86400*(date("w",$primer_dia_semana)-1));
                        }

                        if(date("w",$ultimo_dia_semana)!=0){
                            $ultimo_dia_semana += (86400*(7 - date("w",$ultimo_dia_semana)));
                        }

                        $fd = date("Y-m-d",$primer_dia_semana);
                        $fh = date("Y-m-d",$ultimo_dia_semana);

                        //inserto el peridodo
                        $Renglon = array(
                            'Anio'                      => $anio,
                            'Valor'                     => $contador_semana,
                            'FechaDesde'                => $fd,
                            'FechaHasta'                => $fh,
                            'TipoDeLiquidacionPeriodo'  => $tipo,
                            'Descripcion'               => $anio . " - s" . str_pad($contador_semana, 2, "0", STR_PAD_LEFT)
                        );
                        $row = $this->createRow($Renglon);
                        $row->save();

                        $ultimo_dia_semana += 86400;
                        $primer_dia_semana = $ultimo_dia_semana;

                        $contador_semana += 1;
                        }
                    }
                }
            }
            $this->_db->commit();
            return $id;
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    public function fetchUltimos12Meses($where = null, $order = null, $count = null, $offset = null)
    {
        //$condicion = "LibrosIVADetalles.TipoDeLibro = 1";
        //$where = $this->_addCondition($where, $condicion);
        //$order = array( " LiquidacionesPeriodos.FechaDesde desc ");

        $where->limit(12, 0);
        $where->order('LiquidacionesPeriodos.FechaDesde desc');

        return parent::fetchAll($where, $order, $count, $offset);
    }

}

