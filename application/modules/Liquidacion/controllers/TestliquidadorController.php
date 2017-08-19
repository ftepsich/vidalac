<?php

/**
 * Liquidacion_TestLiquidadorController
 *
 *
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Liquidacion
 * @extends Rad_Window_Controller_Action
 */
class Liquidacion_TestLiquidadorController extends Rad_Window_Controller_Action
{
    protected $title = 'Testing Liquidacion';

    public function initWindow()
    {

    }

    public function testAction()
    {
        // ini_set('display_errors', 1);
        $this->_helper->viewRenderer->setNoRender(true);

        $req = $this->getRequest();

        $log = '<div class="title">Calculando ('.date('H:i').')</div>';
        $log .= '<pre>';
        $conceptos = array();

        $indentacion = 0;

        $i = function() use(&$indentacion, &$log){
            if ($indentacion >= 0) return str_repeat('<idt>| </idt>',$indentacion);
        };


        /**
         * Engancho los enventos para capturar el log de lo calculado
         */
        Rad_PubSub::subscribe(
            'Liquidador/Variable/postCalcular',
            function($valor, $servicio, $evaluador, $var) use (&$log, &$conceptos, $i, &$indentacion) {
                $indentacion--;
                if ($var instanceof Liquidacion_Model_Variable_Concepto) {
                    switch ($var->getTipo()) {
                        case 1:
                            $tipo = '1. Remunerativos';
                            break;
                        case 2:
                            $tipo = '2. Remunerativos Extra';
                            break;
                        case 3:
                            $tipo = '3. No Remunerativos';
                            break;
                        case 4:
                            $tipo = '5. Descuentos';
                            break;
                        case 5:
                            $tipo = '4. No Remunerativos Extra';
                            break;
                        default:
                            $tipo = 'Desconocido';
                            break;
                    }
                    $conceptos[] = array($tipo, $var->getNombre(), str_pad($var->getCodigo(),3,'0'),$var->getDetalle(),$valor);
                }
                if ($valor) {
                    $log .= $i().'<span class="resultado"><b>'.$var->getNombre().'</b> = '.$valor.'</span>'.PHP_EOL;
                    $log .= $i().PHP_EOL;
                }
            }

        );
        Rad_PubSub::subscribe(
            'Liquidador/Variable/preCalcular',
            function($formula, $servicio, $evaluador, $var) use (&$log, $i, &$indentacion) {
                if ($var instanceof Liquidacion_Model_Variable_Concepto) {
                    $log .= $i().'Concepto: <b>';
                } else {
                    $log .= $i().'Variable: <b>';
                }

                $log .= $var->getNombre().'</b> <span class="formula">['.$formula.']</span>'.PHP_EOL;
                $indentacion++;
            }
        );

        Rad_PubSub::subscribe(
            'Liquidador/Variable/postCalcularSelector',
            function($valor, $servicio, $evaluador, $var) use (&$log, $i, &$indentacion) {
                $log .= $i().' = '.var_export($valor, true).PHP_EOL;
                if (!$valor) $log .= $i().PHP_EOL;
            }
        );
        Rad_PubSub::subscribe(
            'Liquidador/Variable/preCalcularSelector',
            function($formula, $servicio, $evaluador, $var) use (&$log, $i, &$indentacion) {
                $log .= $i().'Selector <b>'.$var->getNombre().'</b>: <span class="formula">['.$formula.']</span>';
            }
        );

        $time_start = microtime(true);

        $modelPeriodos = new Liquidacion_Model_DbTable_LiquidacionesPeriodos;
        $modelLiquidaciones = new Liquidacion_Model_DbTable_Liquidaciones;

        $p = $modelPeriodos->getPeriodo($req->periodo);

        if (!$p) throw new Rad_Exception('No se encontro el periodo '.$req->periodo);

        $l = new Liquidacion_Model_LiquidadorServicio(
             new Liquidacion_Model_VariablesProvider
        );

        $ms = new Rrhh_Model_DbTable_Servicios;

        $db = $ms->getAdapter();

        $serv = $db->quote($req->servicio, 'INTEGER');

        $s = $ms->fetchRow("Id = $serv");

        if (!$s) throw new Rad_Exception('No se encontro el servicio');

        try {
            $liq = $modelLiquidaciones->createRow();
            $liq->TipoDeLiquidacion  = 1;
            $liq->LiquidacionPeriodo = $req->periodo;
            $liq->Ejecutada          = date('Y-m-d H:i:s');
            $liq->Empresa            = $serv->Empresa;

            $l->liquidarServicio($s, $p, $liq);
        } catch (Exception $e) {
            $log .= '<span style="color:red">Error: '.$e->getMessage(). ' en linea '.$e->getLine(). ' de '.$e->getFile();
        }

        $time_end = microtime(true);
        $time = round($time_end - $time_start, 4);

        $m = memory_get_peak_usage();


        $log .= '</pre>';
        $log .= "<div class='lasttitle'>Terminado en $time segundos utilizando $m bytes de memoria</div>";
        $output = array(
            'success'   => true,
            'log'       => $log,
            'conceptos' => $conceptos
        );

        Zend_Wildfire_Channel_HttpHeaders::getInstance()->flush(); // fix para q no rompa el envio a firebug
        $data = Zend_Json::encode($output);

        // hay algunos caracteres de besura en el buffer
        ob_clean();
        echo $data;
    }
}