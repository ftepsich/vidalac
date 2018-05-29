<?php

/**
 * Se encarga de comprar dos recibos
 *
 * @package     Aplicacion
 * @subpackage  Liquidacion
 * @class       Liquidacion_Model_ComparadorRecibos
 * @copyright   SmartSoftware Argentina
 * @author      Martin Alejandro Santangelo
 */
class Liquidacion_Model_ComparadorRecibos
{
    public static function comparar($idR1, $idR2)
    {
        $db = Zend_Registry::get('db');
        $diferencias = array();

        echo ' -- Inicio Comparacion recibos --------------------------------------------------------------'.PHP_EOL;
        echo ' Recibo 1: '.$idR1.PHP_EOL;
        echo ' Recibo 2: '.$idR2.PHP_EOL;

        /*
        // Arreglo para que no reste los de un retroactivo anterior (conceptos devengados en de un periodo anterior)
        $sql = " SELECT LRD.VariableDetalle,
        				sum(IF(LRD.LiquidacionRecibo = $idR1,-LRD.Monto,LRD.Monto)) as Monto
        		 FROM 	LiquidacionesRecibosDetalles LRD
                 inner join VariablesDetalles VD on VD.Id = LRD.VariableDetalle
                 inner join Variables V on V.Id = VD.Variable
        		 where 	(LRD.LiquidacionRecibo = $idR1 or LRD.LiquidacionRecibo = $idR2)
        		 and 	LRD.PeriodoDevengado in (Select Periodo from LiquidacionesRecibos where Id = $idR1)
                 and    ifnull(V.NoGeneraRetroactivo,0) <> 1
        		 group by VariableDetalle"
        ;
        */

        // Recupero el Periodo del recibo original
        $sql = "SELECT Periodo FROM LiquidacionesRecibos where Id = $idR1";
        $per = $db->fetchRow($sql);
        $periodoDevengado = $per['Periodo'];

        // Busco las distintas variables de los dos recibos
        $sql = " Select distinct V.Id as Variable
                 FROM    LiquidacionesRecibosDetalles LRD
                 inner join VariablesDetalles VD on VD.Id = LRD.VariableDetalle
                 inner join Variables V on V.Id = VD.Variable
                 where  (LRD.LiquidacionRecibo = $idR1 or LRD.LiquidacionRecibo = $idR2)
                 and    LRD.PeriodoDevengado in (Select Periodo from LiquidacionesRecibos where Id = $idR1)
                 and    ifnull(V.NoGeneraRetroactivo,0) <> 1
        ";

        /*
        echo "--- sql 1 Retro ---".PHP_EOL;;
        echo $sql.PHP_EOL;;
        echo "--- sql 1 ---".PHP_EOL;;
        */


        $v = $db->fetchAll($sql);
        // reviso una por una la diferencia entre uno y otro

        if ($v) {

            $Conceptos = Service_TableManager::get('Liquidacion_Model_DbTable_VariablesDetalles_ConceptosLiquidacionesDetalles');

            foreach ($v as $row) {

                // PK: 2014-09-24 redondeo a dos decimales para que no detecte diferencias con el arreglo de ayer.

                $sql = "    select  VD.Variable,
                                    /* Ojo el detalle uso LRD2 primero ya que es el mas actual y el ifnull ya que el concepto puede no haber estado liquidado en LRD1 o LRD2 */
                                    ifnull(LRD2.Detalle,LRD1.Detalle) as Detalle,
                                    ifnull(LRD1.VariableDetalle,LRD2.VariableDetalle) as VariableDetalle,
                                    0 - sum(round(ifnull(LRD1.Monto,0),2)) + SUM(round(ifnull(LRD2.Monto,0),2)) as Monto
                            FROM  VariablesDetalles VD
                            left join LiquidacionesRecibosDetalles LRD1 on VD.Id = LRD1.VariableDetalle and LRD1.LiquidacionRecibo = $idR1 and LRD1.Monto <> 0 and LRD1.PeriodoDevengado = $periodoDevengado
                            left join LiquidacionesRecibosDetalles LRD2 on VD.Id = LRD2.VariableDetalle and LRD2.LiquidacionRecibo = $idR2 and LRD2.Monto <> 0 and LRD2.PeriodoDevengado = $periodoDevengado
                            Where  (LRD1.VariableDetalle is not null or LRD2.VariableDetalle is not null )
                            and    VD.Variable = ".$row['Variable']. "
                            GROUP BY VD.Variable";

                $m = $db->fetchRow($sql);

                //echo $m['VariableDetalle'].PHP_EOL.$m['Monto'];

//                if ($m && abs($m['Monto']) > 0.005) {
                
                // Lo modifico para qeu no salgan las dif de redondeo del cambio de 4 a dos digitos
                // 
                if ($m && abs($m['Monto']) > 0.01) {


                    echo "--- sql 2 Retro que dieron diferencia ---".PHP_EOL;
                    echo $sql.PHP_EOL;
                    echo 'v '.$m['Variable'].PHP_EOL;
                    echo 'vd '.$m['VariableDetalle'].PHP_EOL;
                    echo 'm '.$m['Monto'].PHP_EOL;
                    echo "--- sql 2 ---".PHP_EOL;


                    $ConceptoCodigo = $Conceptos->getCodigo($m['VariableDetalle']);
                    $ConceptoNombre = $Conceptos->getNombre($m['VariableDetalle']);

                    $diferencias[] = array( 'VariableDetalle'   => $m['VariableDetalle'],
                                            'Detalle'           => $m['Detalle'],
                                            'Monto'             => $m['Monto'],
                                            'ConceptoCodigo'    => $ConceptoCodigo,
                                            'ConceptoNombre'    => $ConceptoNombre
                                            );
                }

            }
        }

        /*
        echo " -- Diferencia ----------".PHP_EOL;
        print_r($diferencias);
        echo ''.PHP_EOL;
        */
        echo ' -- Fin Comparacion recibos --------------------------------------------------------------'.PHP_EOL;

        return $diferencias;



        /*
        // Arreglo para que no reste los de un retroactivo anterior (conceptos devengados en de un periodo anterior)
        $sql = " SELECT LRD.VariableDetalle,
                        sum(IF(LRD.LiquidacionRecibo = $idR1,-LRD.Monto,LRD.Monto)) as Monto
                 FROM   LiquidacionesRecibosDetalles LRD
                 inner join VariablesDetalles VD on VD.Id = LRD.VariableDetalle
                 inner join Variables V on V.Id = VD.Variable
                 where  (LRD.LiquidacionRecibo = $idR1 or LRD.LiquidacionRecibo = $idR2)
                 and    LRD.PeriodoDevengado in (Select Periodo from LiquidacionesRecibos where Id = $idR1)
                 and    ifnull(V.NoGeneraRetroactivo,0) <> 1
                 group by VariableDetalle"
        ;

        $diff = $db->fetchAll($sql);

        // retorno solo si tiene valor
        $t = array_filter($diff, function($v){
            return $v['Monto'] != 0;

        });


        echo "--B-----------".PHP_EOL;
        print_r($t);
        echo "--FB----------".PHP_EOL;
        */
    }
}