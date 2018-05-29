<?php
require 'Abstract.php';

/**
 * Renderiza el reporte pasado por parametro
 */
class Task_Test extends Task_Abstract
{
    public function run()
    {
       $liqSer = new Liquidacion_Model_LiquidadorServicio(
            new Liquidacion_Model_VariablesProvider
        );

        $ms = new Rrhh_Model_DbTable_Servicios;

        $mp = new Liquidacion_Model_DbTable_LiquidacionesPeriodos;

        $s = $ms->find(15)->current();

        $liqSer->liquidarServicio($s, $mp->getPeriodo(1));
    }
	/*
     * es llamado en caso de pasar -h o de no pasar los parametros requeridos
     */
    public function printHelp()
    {
        $c = new Rad_Util_Colors;
        echo "Renderiza un reporte".PHP_EOL;
        echo "----------------\n\n";
        echo "Parametros: ".$c('Reporte [salida] [param1 valor1 tipo1 ... param3 valor3 tipo3]','light_blue',null)."\n";
        echo "Ejemplo: ".$c('Factura pdf Id 1 int','light_blue',null).PHP_EOL;
    }
}

