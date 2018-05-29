<?php

class Liquidacion_Model_Liquidador_PostProcess_Ganancias extends Liquidacion_Model_Liquidador_PostProcess
{
    public function execute(Liquidacion_Model_Periodo $periodo, $tipo, $liquidacion, $agrupacion = null, $valor = null)
    {
        // Recupero todos los recibos del mes
        //

        /*
        array('EMPRESA', 'CONVENIO', 'CATEGORIA', 'GRUPO_PERSONAS', 'SERVICIO', 'GENERICO');
        */

        /*
        Rad_Log::debug("------------------- Ganancias -------------------");
        Rad_Log::debug($tipo);
        Rad_Log::debug($agrupacion);
        Rad_Log::debug($valor);
        */

        $and = "";

        if ($agrupacion && $valor) {
            switch ($agrupacion) {
                case 'SERVICIO':
                    $and = "AND Servicio = $valor";
                    break;
                /*
                case 'EMPRESA':
                    $and = "AND Servicio in (Select Id from Servicios where Empresa = $valor";
                    break;
                */
               default:
                    # code...
                    break;
            }
        }

        // $varLiq     = $liquidacion->getReadOnlyFields(); // ojo es un array
        //$empresaId  = $varLiq['Empresa'];
        $empresaId      = $liquidacion->Empresa;
        $liquidacionId  = $liquidacion->Id;
        $periodoId      = $periodo->getId();
        $periodoFD      = $periodo->getDesde()->format('Y-m-d');
        $periodoFH      = $periodo->getHasta()->format('Y-m-d'); 

        /*
        Ganancia al Bajar:
        1 : Normal (tambien la que se ejecuta mensualmente)
        2 : Final anual al mes de baja
        3 : Final al mes de baja
        */

        $where = "      Ajuste      = 0 
                    and Periodo     = $periodoId 
                    and Liquidacion = $liquidacionId 
                    and Servicio in (   Select Id 
                                        from   Servicios 
                                        where   Empresa = $empresaId
                                        and     (   ifnull(GananciaAlBajar,1) = 1 
                                                    or 
                                                    (   
                                                    ifnull(GananciaAlBajar,1) = 1
                                                    and     '$periodoFD' >  ifnull(FechaBaja,'2999-01-01')
                                                    and     '$periodoFH' <  ifnull(FechaBaja,'2999-01-01')
                                                    )
                                                )
                                    ) 
                    $and";

        echo '|| Ganancia  |||||||||||||||||||||||||||||||||||||||||||||||||||'.PHP_EOL;
        echo $where.PHP_EOL;
        echo '|| Ganancia  |||||||||||||||||||||||||||||||||||||||||||||||||||'.PHP_EOL;

        $M_LR = new Liquidacion_Model_DbTable_LiquidacionesRecibos;
        $R_LR = $M_LR->fetchAll($where);

        // Si existen liquidaciones para ese mes calculo
        if ($R_LR) {
            // $M_PGL  = new Rrhh_Model_DbTable_PersonasGananciasLiquidaciones;
            $M_LG   = new Liquidacion_Model_DbTable_LiquidacionesGanancias;
            $M_S    = new Rrhh_Model_DbTable_Servicios;
            foreach ($R_LR as $row) {
                // Recupero el servicio de la persona
                $servicio   = $M_S->fetchRow('Id = '.$row['Servicio']);
                $recibo     = $M_LR->fetchRow('Id = '.$row['Id']);
                // Armo el cuadro para ese mes y para esa persona
                // $M_PGL->generarCuadroGananciasPeriodo($servicio,$periodo,$liquidacion,$recibo);
                echo $M_LG->generarGananciasPeriodo($servicio,$periodo,$liquidacion,$recibo);
            }
        }
    }
}