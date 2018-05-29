<?php
require 'Abstract.php';
require_once 'FactElect/Wsfev1.php';

class Task_WFEConsultar extends Task_Abstract
{
    public function run($tipo, $numero, $punto) {
        $params = array();
        $params['FeCompConsReq'] = array();
        $params['FeCompConsReq']['CbteTipo'] = $tipo;
        $params['FeCompConsReq']['CbteNro']  = $numero;
        $params['FeCompConsReq']['PtoVta']   = $punto;

        print_r(FactElect_Wsfev1::FECompConsultar($params));
    }

    /**
     * es llamado en caso de pasar -h o de no pasar los parametros requeridos
     */
    public function printHelp()
    {
        echo "Consulta una factura electronica a la AFIP\n parametros: tipo numero punto";
    }
}