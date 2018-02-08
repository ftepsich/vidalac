<?php

class Liquidacion_Model_Liquidador_PostProcess_GananciasBeneficios extends Liquidacion_Model_Liquidador_PostProcess
{
    public function execute(Liquidacion_Model_Periodo $periodo, $tipo, $liquidacion, $agrupacion = null, $valor = null)
    {


        //No va mas se calcula con ganancia

        //$M_LR = new Liquidacion_Model_DbTable_LiquidacionesRecibos;
        //$M_LR->putGananciaBeneficios($periodo,$tipo, $liquidacion, $agrupacion = null, $valor = null);


        // Recupero todos los recibos del mes
        //

        /*
        array('EMPRESA', 'CONVENIO', 'CATEGORIA', 'GRUPO_PERSONAS', 'SERVICIO', 'GENERICO');
        */

        /*
        Rad_Log::debug("------------------- Ganancias Beneficios -------------------");
        Rad_Log::debug($tipo);
        Rad_Log::debug($agrupacion);
        Rad_Log::debug($valor);
        */

    }
}
