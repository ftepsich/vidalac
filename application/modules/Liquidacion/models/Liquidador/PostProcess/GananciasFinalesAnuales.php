<?php

class Liquidacion_Model_Liquidador_PostProcess_GananciasFinalesAnuales extends Liquidacion_Model_Liquidador_PostProcess
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
        //$mes            = $periodo->getDesde()->format('m');
        $anio           = $periodo->getDesde()->format('Y');
        $periodoFD      = $periodo->getDesde()->format('Y-m-d');
        $periodoFH      = $periodo->getHasta()->format('Y-m-d');       

        /*
        Ganancia al Bajar:
        1 : Normal
        2 : Final anual al mes de baja
        3 : Final al mes de baja
        */

        $where = "		Ajuste 		= 0 
        			and Periodo 	= $periodoId 
        			and Liquidacion = $liquidacionId 
        			and Servicio in (	Select 	Id 
        								from 	Servicios 
        								where 	Empresa = $empresaId 
        								and 	ifnull(GananciaAlBajar,1) = 2
        								and 	ifnull(FechaBaja,'2999-01-01') >= '$periodoFD' 
        								and 	ifnull(FechaBaja,'2999-01-01') <= '$periodoFH'
        							) 
        			$and
        			";

       	echo '|| Ganancia Anuales |||||||||||||||||||||||||||||||||||||||||||||||||||'.PHP_EOL;
        echo $where.PHP_EOL;
        echo '|| Ganancia Anuales |||||||||||||||||||||||||||||||||||||||||||||||||||'.PHP_EOL;

        $M_LR = new Liquidacion_Model_DbTable_LiquidacionesRecibos;
        $R_LR = $M_LR->fetchAll($where);

        // Si existen liquidaciones para ese mes calculo
        if ($R_LR) {
            $M_PGLA  = new Rrhh_Model_DbTable_PersonasGananciasLiquidacionesAnuales;
            $M_S     = new Rrhh_Model_DbTable_Servicios;
            foreach ($R_LR as $row) {
                // Recupero el servicio de la persona
                //$servicio   = $M_S->fetchRow('Id = '.$row['Servicio']);
                //$recibo     = $M_LR->fetchRow('Id = '.$row['Id']);
                $idRecibo 	= $row['Id'];
                $idPersona  = $row['Persona'];
                // Armo el cuadro para ese mes y para esa persona
                $M_PGLA->generarAjusteGananciasAnual($anio,$idPersona,$liquidacionId,$idRecibo);
                //    generarCuadroGananciasPeriodo($servicio,$periodo,$liquidacion,$recibo);
            }
        }
    }
}