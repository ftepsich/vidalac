<?php
require 'Abstract.php';

/**
 * Renderiza el reporte pasado por parametro
 */
class Task_RenderReport extends Task_Abstract
{
    public function run($reporte, $output = 'pdf', 
        $param1 = null, $param1v = null, $param1type=null,
        $param2 = null, $param2v = null, $param2type=null,
        $param3 = null, $param3v = null, $param3type=null) {

        $engine = new Rad_BirtEngine();

        if ($param1 && $param1type) {
            $engine->setParameter($param1, $param1v, $param1type);
        }
        if ($param2 && $param2type) {
            $engine->setParameter($param2, $param2v, $param2type);
        }
        if ($param3 && $param3type) {
            $engine->setParameter($param3, $param3v, $param3type);
        }

        $engine->renderFromFile(APPLICATION_PATH . "/../birt/Reports/$reporte.rptdesign", $output);
        $this->_doSomeThing($engine, $reporte, $output);
    }


    protected function _doSomeThing($engine, $reporte, $output)
    {
        if ($output == 'msword') $output = 'doc';
        file_put_contents($reporte.'.'.$output, $engine->getStream());
    }

    /**
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

