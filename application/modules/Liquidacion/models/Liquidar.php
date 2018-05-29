<?php
require_once 'Liquidador.php';
use Rad\Util\Math\Evaluator;


/**
 * Ejecuta una liquidacion almacenando en las tablas:
 * Liquidaciones
 * LiquidacionesRecibos
 * LiquidacionesRecibosDetalles
 *
 * @package     Aplicacion
 * @subpackage  Liquidacion
 * @class       Liquidacion_Model_Liquidar
 * @copyright   SmartSoftware Argentina
 * @author      Martin Alejandro Santangelo
 */
class Liquidacion_Model_Liquidar
{
    protected $_liquidadoRetroactivo = false;
    protected $_esReliquidacion      = false;
    protected static $liquidacion   = null;


    public function __construct()
    {
        // // seteo los manejadores
        // Liquidacion_Model_Variable_Concepto::setPostCalcular($this);
        // Liquidacion_Model_LiquidadorServicio::setPostLiquidarServicio($this);
        $this->modelLiquidaciones                   = new Liquidacion_Model_DbTable_Liquidaciones;
        $this->modelRecibos                         = new Liquidacion_Model_DbTable_LiquidacionesRecibos;
        $this->modelRecibosDetalles                 = new Liquidacion_Model_DbTable_LiquidacionesRecibosDetalles;
        $this->modelPersonaGanaciasLiquidaciones    = new Rrhh_Model_DbTable_PersonasGananciasLiquidaciones;
        $this->modelPersonaGanaciasProrrateos       = new Rrhh_Model_DbTable_PersonasGananciasProrrateos;
    }

    public static function getLiquidacion()
    {
        return self::$liquidacion;
    }

    /**
     * Liquida un periodo dado, puede pasarse una agrupacion y un valor para filtrar
     *
     * @param $periodo
     * @param $tipo
     * @param null $empresa        Valor para la agrupacion
     * @return mixed
     */
    public function liquidar($periodo, $tipo, $empresa)
    {
        // Controlo que no este tirando una liq si ya existe
        $w           = " LiquidacionPeriodo = $periodo and TipoDeLiquidacion = $tipo and Empresa = $empresa ";
        $existeLiq   = $this->modelLiquidaciones->fetchRow($w);
        if ($existeLiq) throw new Liquidacion_Model_Exception('Ya exise una liquidacion para el conjunto de valores que ingreso.');

        // Controlo que no exista una liq anterior abierta
        $w           = " TipoDeLiquidacion = $tipo and Empresa = $empresa
                         and LiquidacionPeriodo not in (select Id from LiquidacionPeriodo) $periodo and";
        //$existeLiq   = $this->modelLiquidaciones->fetchRow($w);
        //if ($existeLiq) throw new Liquidacion_Model_Exception('Ya exise una liquidacion para el conjunto de valores que ingreso.');

        // la liquiodacion es por empresa
        $IdL = $this->_liquidar($periodo, $tipo, 'EMPRESA', $empresa);
        // Marco que finalizo correctamente
        $this->modelLiquidaciones->update(array('FinalizadaCorrectamente' => 1),"Id = $IdL");
        return $IdL;
    }

    public function reliquidar($liquidacion, $periodo, $tipo, $agrupacion = null, $valor = null)
    {
        $db          = $this->modelLiquidaciones->getAdapter();
        $liquidacion = $db->quote($liquidacion, 'INTEGER');
        $liq         = $this->modelLiquidaciones->find($liquidacion)->current();

        if (!$liq) throw new Liquidacion_Model_Exception('No se encontro la liquidación');

        if ($liq->Cerrada) throw new Liquidacion_Model_Exception('La liquidación se encuentra cerrada');

        $this->_esReliquidacion = true;
        self::$liquidacion      = $liq;

        return $this->_liquidar($periodo, $tipo, $agrupacion, $valor);
    }

    /**
     * Liquida un periodo dado, puede pasarse una agrupacion y un valor para filtrar
     *
     * @param int|Model_Liquidaciones_Periodo  $periodo
     * @param int                              $tipo
     * @param string                           $agrupacion  (Rrhh_Model_DbTable_Servicios::$JERARQUIAS)
     * @param mixed                            $valor       Valor para la agrupacion
     * @return mixed
     * @throws Liquidacion_Model_Exception
     */
    public function liquidarPrueba($periodo, $tipo, $agrupacion = null, $valor = null)
    {
        return $this->_liquidar($periodo, $tipo, $agrupacion, $valor, true);
    }

    /**
     * Liquida un periodo dado, puede pasarse una agrupacion y un valor para filtrar
     *
     * @param int|Model_Liquidaciones_Periodo  $periodo
     * @param int                              $tipo
     * @param string                           $agrupacion  (Rrhh_Model_DbTable_Servicios::$JERARQUIAS)
     * @param mixed                            $valor       Valor para la agrupacion
     * @param bool                          $prueba      especifica si es una liquidacion de prueba
     * @return mixed
     * @throws Liquidacion_Model_Exception
     */
    protected function _liquidar($periodo, $tipo, $agrupacion = null, $valor = null, $prueba = false)
    {
        if (!($periodo instanceof Liquidacion_Model_Periodo)){
            $modelPeriodo = Service_TableManager::get('Liquidacion_Model_DbTable_LiquidacionesPeriodos');
            $p = $modelPeriodo->getPeriodo($periodo);
            if ($p) {
                $periodo = $p;
            } else {
                throw new Liquidacion_Model_Exception('No se encontro el periodo '.$periodo);
            }
        }

        $this->periodo = $periodo->getId();

        // preparo las variables y modelos
        $this->tmpConceptos = array();

        // si esta seteado estoy reliquidando dentro de la misma, asi que no creo otra liquidacion
        if (!$this->_esReliquidacion) {
            $this->_crearRegistroLiq($tipo, $valor, $prueba);
        }

        // -- Liquidacion comun --
        $liq = $this->_getLiquidador('Liquidacion_Model_Liquidador');

        $liq->liquidar($periodo, $tipo, self::$liquidacion, $agrupacion, $valor);

        // -- Liquidacion de retroactivos --
        $this->_liquidadoRetroactivo = true; // este flag se utiliza para que sepa que esta en el modo retroactivo

        $liqRetro = $this->_getLiquidador('Liquidacion_Model_LiquidadorRetroactivos');

        $liqRetro->liquidar($periodo, $tipo, self::$liquidacion, $agrupacion, $valor);

        // lo vuelvo a false para poder hacer otra liquidacion normalmente
        $this->_liquidadoRetroactivo = false;
        // $db->commit();
        $this->_executePostProcess($periodo, $tipo, self::$liquidacion, $agrupacion, $valor);

        return self::$liquidacion->Id;
    }

    protected function _executePostProcess($periodo, $tipo, $liquidacion, $agrupacion, $valor)
    {
        $cfg = Rad_Cfg::get('/configs/liquidacion.yml');

        foreach($cfg->liquidacionPostProcess as $process){
            $p = new $process;
            $p->execute($periodo, $tipo, $liquidacion, $agrupacion, $valor);
        }
    }

    protected function _getLiquidador($class)
    {
        $liqSer = new Liquidacion_Model_LiquidadorServicio(
            new Liquidacion_Model_VariablesProvider
        );

        $liqSer->getEventDispatcher()->on('servicio_liquidado', array($this, 'liquidado'));
        $liqSer->getEventDispatcher()->on('concepto_liquidado', array($this, 'calculado'));

        $liq = new $class($liqSer);

        return $liq;
    }

    /**
     * Crea la cabecera de una liquidacion
     */
    protected function _crearRegistroLiq($tipo, $empresa, $prueba = false)
    {
        $usuario = Zend_Auth::getInstance()->getIdentity()->Id;

        $liq = $this->modelLiquidaciones->createRow();
        $liq->TipoDeLiquidacion         = $tipo;
        $liq->EsDePrueba                = ($prueba)?1:0;
        $liq->LiquidacionPeriodo        = $this->periodo;
        $liq->Ejecutada                 = date('Y-m-d H:i:s');
        $liq->Usuario                   = $usuario;
        $liq->Empresa                   = $empresa;
        $liq->Cerrada                   = 0;
        $liq->FinalizadaCorrectamente   = 0;

        $liq->save();

        self::$liquidacion = $liq;
        return;
    }

    // se llama al terminar de de calcular un concepto (lo hago aca pq incerto en el mismo orden q se calculan)
    public function calculado(  $valor,
                                Rad_Db_Table_Row                    $servicio,
                                Evaluator                           $evaluador,
                                Liquidacion_Model_Variable_Concepto $concepto,
                                Liquidacion_Model_Periodo           $periodo)
    {
        // guardo los conceptos en una estructura temporal
        $this->tmpConceptos[$concepto->getNombre()] = array(
            'valor'         => $valor,
            'id'            => $concepto->getId(),
            'detalle'       => $concepto->getResultadoDetalle(),
            'codigo'        => $concepto->getCodigo(),
            'descripcion'   => $concepto->getDescripcion()
        );
    }

    protected function _crearRecibo(Rad_Db_Table_Row $servicio, Liquidacion_Model_Periodo $periodo)
    {
        $ajuste = $this->getCantidadRecibos($periodo->getId(), $servicio->Id);

        if (!$this->_liquidadoRetroactivo) {
            // if ($ajuste) throw new Liquidacion_Model_Exception('Ya existe una liquidacion para este periodo');
            $ajuste = 0;
        }

        $recibo = $this->modelRecibos->createRow();
        $recibo->Liquidacion 	= self::$liquidacion->Id;
        $recibo->Persona     	= $servicio->Persona;
        $recibo->Servicio    	= $servicio->Id;
        $recibo->Ajuste      	= $ajuste;
        $recibo->Periodo     	= $periodo->getId();
        $recibo->FechaCalculo   = date('Y-m-d H:i:s');
        // $recibo->VariablesCalculadas = json_encode($evalMath->getVars());

        return $recibo->save();
    }

    // se llama al terminar de calcular un servicio
    public function liquidado(
        Rad_Db_Table_Row $servicio,
        Evaluator $evaluador,
        Liquidacion_Model_VariablesProvider $variablesProvider,
        Liquidacion_Model_LiquidadorServicio $liqServicio,
        Liquidacion_Model_Periodo $periodo)
    {
        $db = Zend_Registry::get('db');

        // Rad_Jobs_Base::log("Calculado recibo servicio {$servicio->Id} periodo ".$periodo->getId());

        $db->beginTransaction();

        try {
            if ($this->_esReliquidacion && !$this->_liquidadoRetroactivo) {
                // borro el recibo
                $this->modelRecibos->delete("Servicio = {$servicio->Id} AND Liquidacion = ".self::$liquidacion->Id);
                // borro los recibos de ajustes de esta liquidacion
                $this->modelRecibos->delete("Servicio = {$servicio->Id} AND Ajuste <> 0 AND Liquidacion = ".self::$liquidacion->Id." and Persona = {$servicio->Persona}" );

                // si es Normal
                if (self::$liquidacion->TipoDeLiquidacion == 1) {
                    // Borro lo de ganancia
                    $this->modelPersonaGanaciasLiquidaciones->delete("Liquidacion = ".self::$liquidacion->Id." and Persona = {$servicio->Persona}");
                }
                // ver si tengo el id del recibo viejo ... por ahora cascada
                // $this->modelPersonaGanaciasProrrateos->delete()
            }

            // creo el recibo
            $idRecibo = $this->_crearRecibo($servicio, $periodo);

            // guardo los conceptos en el detalle del recibo
            foreach ($this->tmpConceptos as $key => $v) {
                $detalle = $this->modelRecibosDetalles->createRow();
                $detalle->LiquidacionRecibo = $idRecibo;
                $detalle->VariableDetalle   = $v['id'];

                // PK 23-09-2014 guardo los valores de monto y monto calculado con dos decimales
                // hago esto para no cambiar ahora la tabla y afectar los valores de los recibos de
                // meses anteriores
                $detalle->MontoCalculado    = round($v['valor'], 2, PHP_ROUND_HALF_UP);
                $detalle->Monto             = round($v['valor'], 2, PHP_ROUND_HALF_UP);

                $detalle->Detalle           = $v['detalle'];
                $detalle->ConceptoCodigo    = $v['codigo'];
                $detalle->ConceptoNombre    = $v['descripcion'];
                $detalle->PeriodoDevengado  = $periodo->getId();
                $detalle->save();
            }

            $this->tmpConceptos = array();

            $mVarCalculadas = Service_TableManager::get('Liquidacion_Model_DbTable_LiquidacionesVariablesCalculadas');

            // guardo las variables calculadas
            foreach ($liqServicio->getVariables() as $key => $variable) {
                $r = $mVarCalculadas->createRow();
                $r->LiquidacionRecibo = $idRecibo;
                $r->VariableDetalle   = $variable->getId();
                $r->Nombre            = $variable->getNombre();
                $r->Valor             = $evaluador->getVar($variable->getNombre()); //valor calculado en el evaluador para la variable

                $r->save();
            }

            // si estoy liquidando retroactivos comparo las liquidaciones
            if ($this->_liquidadoRetroactivo) {
                // COMITEO PQ SINO EL COMPARADOR NO VE EL RECIBO VAYA UNO A SABER PQ (MARTIN)
                $db->commit();
                $db->beginTransaction();

                $recAnterior = $this->_getReciboAnterior($idRecibo, $servicio, $periodo);

                if (!$recAnterior) throw new Liquidacion_Model_Exception('No se encontro el recibo anterior para comparar el retroactivo');
                // Rad_Jobs_Base::log("comparando $recAnterior, $idRecibo ".$db->getTransactionCount());

                $diff = Liquidacion_Model_ComparadorRecibos::comparar($recAnterior, $idRecibo);
                // Rad_Jobs_Base::log(print_r($diff,true));
                if (!empty($diff)) {
                    $reciboRetro = $this->_getReciboRetroactivo($servicio, $periodo);

                    foreach ($diff as $row) {
                        $detalle = $this->modelRecibosDetalles->createRow();
                        $detalle->LiquidacionRecibo = $reciboRetro;
                        $detalle->VariableDetalle   = $row['VariableDetalle'];
                        $detalle->Detalle           = $row['Detalle'];
                        // PK 23-09-2014 guardo los valores de monto y monto calculado con dos decimales
                        // hago esto para no cambiar ahora la tabla y afectar los valores de los recibos de
                        // meses anteriores
                        $detalle->MontoCalculado    = round($row['Monto'], 2, PHP_ROUND_HALF_UP);
                        $detalle->Monto             = round($row['Monto'], 2, PHP_ROUND_HALF_UP);

                        // PK 21-11-2014 agrego las descripciones editables
                        $detalle->ConceptoCodigo    = $row['ConceptoCodigo'];
                        $detalle->ConceptoNombre    = $row['ConceptoNombre'];

                        $detalle->PeriodoDevengado  = $periodo->getId();
                        $detalle->save();
                    }
                }
            }
            $db->commit();
        } catch (Exception $e) {
            $db->rollback();
            throw $e;
        }
    }

    /**
     *  Devuelver el id del Recibo de Sueldo donde debe colocar los retroactivos
     *
     *
    */
    protected function _getReciboRetroactivo($servicio, $periodo)
    {
        $db = Zend_Registry::get('db');
        //paso el periodo de la liquidacion original no la del retroactivo
        $rec = $db->fetchOne("  SELECT  LR.Id
                                FROM    LiquidacionesRecibos LR
                                INNER JOIN Liquidaciones L on L.Id = LR.Liquidacion
                                WHERE   LR.Periodo  = {$this->periodo}
                                AND     LR.Servicio = {$servicio->Id}
                                AND     LR.Ajuste = 0
                                AND     L.TipoDeLiquidacion = 1
                            ");

        if ($rec) return $rec;

        $modelPeriodo = Service_TableManager::get('Liquidacion_Model_DbTable_LiquidacionesPeriodos');
        $p = $modelPeriodo->getPeriodo($this->periodo);

        return $this->_crearRecibo($servicio, $p);
    }

    /**
     *  $id id del recibo actual
     *
     *
    */
    protected function _getReciboAnterior($idReciboActual, $servicio, $periodoDelRetro)
    {
        $idServicio = $servicio->Id;
        $idPeriodo  = $periodoDelRetro->getId();
        $anio       = $periodoDelRetro->getAnio();
        $mes        = $periodoDelRetro->getMes();

        $M_LR           = Service_TableManager::get('Liquidacion_Model_DbTable_LiquidacionesRecibos');
        $M_L            = Service_TableManager::get('Liquidacion_Model_DbTable_Liquidaciones');
        $M_LP           = Service_TableManager::get('Liquidacion_Model_DbTable_LiquidacionesPeriodos');

        $reciboActual       = $M_LR->find($idReciboActual)->current();
        $liquidacionActual  = $M_L->find($reciboActual->Liquidacion)->current();
        $rowPeriodoAct      = $M_LP->find($liquidacionActual->LiquidacionPeriodo)->current();

        $db = Zend_Registry::get('db');

        $sql = "    SELECT  LR.Id
                    FROM    LiquidacionesRecibos LR
                    inner join Liquidaciones L              on L.Id     = LR.Liquidacion
                    inner join LiquidacionesPeriodos LP     on LP.Id    = L.LiquidacionPeriodo
                    where   LR.Servicio     = $idServicio
                    and     LR.Periodo      = $idPeriodo
                    and     LR.Id           < $idReciboActual
                    /* Para que no seleccione mal cuando se recalcula algo viejo */
                    and     L.LiquidacionPeriodo    in (
                        SELECT  LP1.Id
                                                        FROM LiquidacionesPeriodos LP1
                                                        WHERE   (
                                                                  ( LP1.Anio = {$rowPeriodoAct->Anio} and LP1.Valor < {$rowPeriodoAct->Valor} )
                                                                  OR
                                                                  ( LP1.Anio < {$rowPeriodoAct->Anio})
                                                                )
                                                        )
                    and     L.TipoDeLiquidacion in (    SELECT  L2.TipoDeLiquidacion
                                                        FROM    LiquidacionesRecibos LR2
                                                        inner join Liquidaciones L2 on L2.Id = LR2.Liquidacion
                                                        where LR2.Id = $idReciboActual )
                    order by 1 desc limit 1";

        $rec = $db->fetchOne($sql);

        echo '=========================================================='.PHP_EOL;
        echo $sql.PHP_EOL;
        echo '=========================================================='.PHP_EOL;

        return $rec;
    }

    protected function getCantidadRecibos($periodo, $servicio)
    {
        $db = Zend_Registry::get('db');
        $count = $db->fetchOne("SELECT count(Id) as count FROM LiquidacionesRecibos where Servicio = $servicio and Periodo = $periodo");
        return $count;
    }
}
