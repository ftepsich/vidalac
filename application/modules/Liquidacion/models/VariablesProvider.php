<?php

/**
 * Liquidacion_Model_Liquidador
 *
 * Se encarga de conseguir la variable que corresponde a un servicio y periodo dado
 *
 * @package     Aplicacion
 * @subpackage  Liquidacion
 * @class       Liquidacion_Model_VariablesProvider
 * @copyright   SmartSoftware Argentina
 * @author      Martin Alejandro Santangelo
 */
class Liquidacion_Model_VariablesProvider
{
    /**
     * Retorna los conceptos para un servicio y periodo dado teniendo en cuenta las jerarquias
     * @author  Maximiliano Carelli
     */
    public function getConceptos(Liquidacion_Model_Periodo $periodo, $servicio, $liq, $tipoVar = 1)
    {
        $db = Zend_Registry::get('db');
        $conceptos = new Liquidacion_Model_VariableCollection;

        //throw new Liquidacion_Model_Exception(print_r($liq,true));

        /*primero traigo todos los conceptos de liquidaciones y empiezo a recorrelos de a uno
        preguntando por el orden de jerarquia si tiene algun detalle que lo pise.
        */

        if ($tipoVar != 1 && $tipoVar != 5)  throw new Liquidacion_Model_Exception('tipoVar debe ser 1 o 5');

        /*
        if ($tipoVar == 1) {
            $M_ConceptosLiquidaciones           = Service_TableManager::get('Liquidacion_Model_DbTable_Variables_ConceptosLiquidaciones');
        } else if ($tipoVar == 5) {
            $M_ConceptosLiquidaciones           = Service_TableManager::get('Liquidacion_Model_DbTable_Variables_ConceptosLiquidacionesExtras');
        } else {
            throw new Liquidacion_Model_Exception('tipoVar debe ser 1 o 5');
        }
        */

        $M_ConceptosLiquidacionesDetalles   = Service_TableManager::get('Liquidacion_Model_DbTable_VariablesDetalles_ConceptosLiquidacionesDetalles');

        //recupero los datos del periodo Mes y Año;
        if (!$periodo) throw new Liquidacion_Model_Exception("No se encontro el Periodo.");

        $fechaAlta = $periodo->getDesde()->format('Y-m-d');
        $fechaBaja = $periodo->getHasta()->format('Y-m-d');

        $mes  = $periodo->getDesde()->format('n');
        $anio = $periodo->getDesde()->format('Y');

        //busco primero los conceptos remunerativos (PASO COMO CONDICION EL PERIODO TAMBIEN)
        //$R_ConceptosLiquidaciones = $M_ConceptosLiquidaciones->fetchAll("FechaAlta <= '$fechaAlta' AND IFNULL(FechaBaja,'2099-12-31') >= '$fechaBaja' AND Activo = 1 AND IFNULL(NoCalculada,0) <> 1", array('TipoDeConceptoLiquidacion asc', 'Codigo asc'));

        // $R_ConceptosLiquidaciones = $M_ConceptosLiquidaciones->getConceptosLiquidar($fechaAlta,$fechaBaja);

        $sql = "SELECT      distinct V.Id, V.Nombre, V.Codigo, V.TipoDeConceptoLiquidacion, V.TipoDeConcepto, TCL.OrdenEjecucion, V.Descripcion
                FROM        Variables V
                INNER JOIN  TiposDeConceptosLiquidaciones TCL on TCL.Id = V.TipoDeConceptoLiquidacion
                INNER JOIN  VariablesTiposDeLiquidaciones VTL on V.Id   = VTL.Variable
                WHERE   V.FechaAlta                         <= '$fechaAlta'
                AND     IFNULL(V.FechaBaja,'2099-12-31')    >= '$fechaBaja'
                AND     V.Activo                            = 1
                AND     V.TipoDeVariable                    = $tipoVar
                AND     (
                        IFNULL(V.NoCalculada,0)             <> 1
                        OR  ((select  count(V1.Id)
                            from    VariablesDetalles VD1
                            Inner join Variables V1 on V1.Id = VD1.Variable
                            INNER JOIN  VariablesTiposDeLiquidaciones VTL1 on V1.Id   = VTL1.Variable
                            WHERE   V1.TipoDeVariable         = $tipoVar
                            AND     V1.Id                   = V.Id
                            AND     VD1.FechaDesde              <= '$fechaAlta'
                            AND     IFNULL(VD1.FechaHasta,'2099-01-01')    >= '$fechaBaja'
                            AND     IFNULL(V1.NoCalculada,0) <> 0
                            AND     V1.Activo = 1
                            AND     VD1.VariableJerarquia <> 6
                            AND     VD1.Historico = 0
                            AND     VTL1.TipoDeLiquidacion = ".(int)$liq->TipoDeLiquidacion."
                            ) > 0)
                        )
                AND     VTL.TipoDeLiquidacion = ".(int)$liq->TipoDeLiquidacion." ORDER BY TCL.OrdenEjecucion asc, V.Codigo asc ";

    //echo '++ SSSQQQLLL +++++++++++++++++++++++++++++++++++++++++++'.PHP_EOL;
    // echo $sql.PHP_EOL;
    //echo '+++++++++++++++++++++++++++++++++++++++++++ SSSQQQLLL ++'.PHP_EOL;

        $conceptosLiquidar = $db->fetchAll($sql);

        // throw new Liquidacion_Model_Exception(count($conceptosLiquidar));
        // $cl = $conceptosLiquidar;
        // if (!$conceptosLiquidar || count($conceptosLiquidar) == 0) {
        //     throw new Liquidacion_Model_Exception("No se encontro Ningun concepto para el Periodo del Mes $mes del $anio.");
        // }
        // if (!$R_ConceptosLiquidarLiquidaciones) throw new Liquidacion_Model_Exception("No se encontro Ningun concepto para el Periodo del Mes $mes del $anio.");

        //comienzo a recorrerlos uno por uno

        foreach ($conceptosLiquidar as $row) {
            $formula    = null;
            $jerarquia  = null;
            $sql        = '';

            // Veo que tenga al menos una formula ??
            $where      = " Variable = {$row['Id']} AND FechaDesde <= '$fechaAlta' AND IFNULL(FechaHasta,'2099-12-31') >= '$fechaBaja'";
            $R          = $M_ConceptosLiquidacionesDetalles->fetchAll($where);

            if (count($R)) {

                $whereComun = " AND     v.TipoDeVariable        = $tipoVar
                                AND     vd.FechaDesde           <= '$fechaAlta'
                                AND     IFNULL(vd.FechaHasta,'2099-12-31') >= '$fechaBaja'
                                AND     v.Id                    = {$row['Id']}
                                AND     vd.Historico            <> 1
                                -- AND     IFNULL(v.NoCalculada,0) <> 1
                            ";

                /*
                --------------------------------------------------------------------------------
                SERVICIO
                --------------------------------------------------------------------------------
                */
                if (!$formula) {
                    $sql = "SELECT      vd.Id, 
                                        trim(vd.Formula)        as Formula, 
                                        trim(vd.Selector)       as Selector, 
                                        trim(vd.FormulaDetalle) as FormulaDetalle, 
                                        v.NoHabitual, 
                                        v.NoCalculada, 
                                        v.NoGeneraRetroactivo, 
                                        v.NoSumaEnSAC, 
                                        v.EsSAC, 
                                        v.NoCuentaParaGanancia
                    FROM        Variables v
                            INNER JOIN  VariablesDetalles vd ON v.Id = vd.Variable
                            WHERE       vd.VariableJerarquia    = 1
                            AND         vd.Servicio             = $servicio->Id
                            $whereComun
                            ";

                    $jerarquia  = $db->fetchRow($sql);
                    $formula    = ($jerarquia) ? $jerarquia : null;
                }
                /*
                --------------------------------------------------------------------------------
                GRUPO DE PERSONAS
                --------------------------------------------------------------------------------
                */
                if (!$formula) {
                    $sql = "SELECT      vd.Id, 
                                        trim(vd.Formula)        as Formula, 
                                        trim(vd.Selector)       as Selector, 
                                        trim(vd.FormulaDetalle) as FormulaDetalle, 
                                        v.NoHabitual, 
                                        v.NoCalculada, 
                                        v.NoGeneraRetroactivo, 
                                        v.NoSumaEnSAC, 
                                        v.EsSAC, 
                                        v.NoCuentaParaGanancia
                            FROM        Variables v
                            INNER JOIN  VariablesDetalles vd         ON v.Id = vd.Variable
                            INNER JOIN  GruposDePersonas gp          ON vd.GrupoDePersona = gp.Id
                            INNER JOIN  GruposDePersonasDetalles gpd ON gp.Id = gpd.GrupoDePersona
                            INNER JOIN  Servicios s                  ON gpd.Persona = s.Persona
                            WHERE       vd.VariableJerarquia    = 2
                            AND         s.Id                    = $servicio->Id 
                            /* Grupo Activo */
                            AND         gp.FechaAlta                        <= '$fechaAlta'
                            AND         IFNULL(gp.FechaBaja,'2099-12-31')   >= '$fechaBaja'
                            /* Persona dentro del Grupo Activa */
                            AND         gpd.FechaAlta                       <= '$fechaAlta'
                            AND         IFNULL(gpd.FechaBaja,'2099-12-31')  >= '$fechaBaja'                            
                            $whereComun
                            ";

                    $jerarquia  = $db->fetchRow($sql);
                    $formula    = ($jerarquia) ? $jerarquia : null;
                }
                /*
                --------------------------------------------------------------------------------
                CATEGORIA
                --------------------------------------------------------------------------------
                */
                if (!$formula) {
                    $sql = "SELECT      vd.Id, 
                                        trim(vd.Formula)        as Formula,
                                        trim(vd.Selector)       as Selector,
                                        trim(vd.FormulaDetalle) as FormulaDetalle,
                                        v.NoHabitual, 
                                        v.NoCalculada, 
                                        v.NoGeneraRetroactivo, 
                                        v.NoSumaEnSAC, 
                                        v.EsSAC, 
                                        v.NoCuentaParaGanancia
                            FROM        Variables v
                            INNER JOIN  VariablesDetalles vd    ON v.Id = vd.Variable
                            INNER JOIN  Servicios s             ON vd.ConvenioCategoria = s.ConvenioCategoria
                            WHERE       vd.VariableJerarquia        = 3
                            AND         s.Id                        = $servicio->Id
                            $whereComun
                            ";

                    $jerarquia  = $db->fetchRow($sql);
                    $formula    = ($jerarquia) ? $jerarquia : null;
                }
                /*
                --------------------------------------------------------------------------------
                EMPRESA
                --------------------------------------------------------------------------------
                */
                if (!$formula) {
                    $sql = "SELECT      vd.Id, 
                                        trim(vd.Formula)        as Formula,
                                        trim(vd.Selector)       as Selector,
                                        trim(vd.FormulaDetalle) as FormulaDetalle,
                                        v.NoHabitual, 
                                        v.NoCalculada, 
                                        v.NoGeneraRetroactivo, 
                                        v.NoSumaEnSAC, 
                                        v.EsSAC, 
                                        v.NoCuentaParaGanancia
                            FROM        Variables v
                            INNER JOIN  VariablesDetalles vd    ON v.Id = vd.Variable
                            INNER JOIN  Servicios s             ON vd.Empresa = s.Empresa
                            WHERE       vd.VariableJerarquia    = 4
                            AND         s.Id                    = $servicio->Id
                            $whereComun
                            ";

                    $jerarquia  = $db->fetchRow($sql);
                    $formula    = ($jerarquia) ? $jerarquia : null;
                }
                /*
                --------------------------------------------------------------------------------
                CONVENIO
                --------------------------------------------------------------------------------
                */
                if (!$formula) {
                    $sql = "SELECT      vd.Id, 
                                        trim(vd.Formula)        as Formula,
                                        trim(vd.Selector)       as Selector,
                                        trim(vd.FormulaDetalle) as FormulaDetalle,
                                        v.NoHabitual, 
                                        v.NoCalculada, 
                                        v.NoGeneraRetroactivo, 
                                        v.NoSumaEnSAC, 
                                        v.EsSAC, 
                                        v.NoCuentaParaGanancia
                            FROM        Variables v
                            INNER JOIN  VariablesDetalles vd    ON v.Id = vd.Variable
                            INNER JOIN  Servicios s             ON vd.Convenio = s.Convenio
                            WHERE       vd.VariableJerarquia    = 5
                            AND         s.Id                    = $servicio->Id
                            $whereComun
                            ";

                    $jerarquia  = $db->fetchRow($sql);
                    $formula    = ($jerarquia) ? $jerarquia : null;
                }
                /*
                --------------------------------------------------------------------------------
                GENERICO
                --------------------------------------------------------------------------------
                */
                if (!$formula) {

                    $sql = "SELECT      vd.Id, 
                                        trim(vd.Formula)        as Formula,
                                        trim(vd.Selector)       as Selector,
                                        trim(vd.FormulaDetalle) as FormulaDetalle,
                                        v.NoHabitual, 
                                        v.NoCalculada, 
                                        v.NoGeneraRetroactivo, 
                                        v.NoSumaEnSAC, 
                                        v.EsSAC, 
                                        v.NoCuentaParaGanancia
                            FROM        Variables v
                            INNER JOIN  VariablesDetalles vd    ON v.Id = vd.Variable
                            WHERE       vd.VariableJerarquia    = 6
                            $whereComun
                            ";

                    //throw new Rad_Db_Table_Exception($sql);

                    $jerarquia  = $db->fetchRow($sql);
                    $formula    = ($jerarquia) ? $jerarquia : null;
                }

                // Agrego la formula al provider

                if ($formula) {
                    switch ($row['TipoDeConceptoLiquidacion']) {
                        case 5:
                            $new_var = new Liquidacion_Model_Variable_Concepto_NoRemunerativo(     $formula['Id'], $row['Nombre'], $periodo, $row['Codigo'], $row['TipoDeConceptoLiquidacion'], $row['TipoDeConcepto'], $row['Descripcion'], $formula['Formula'], $formula['Selector'], $formula['FormulaDetalle']);
                            break;
                        case 4:
                            $new_var = new Liquidacion_Model_Variable_Concepto_Descuento(          $formula['Id'], $row['Nombre'], $periodo, $row['Codigo'], $row['TipoDeConceptoLiquidacion'], $row['TipoDeConcepto'], $row['Descripcion'], $formula['Formula'], $formula['Selector'], $formula['FormulaDetalle']);
                            break;
                        case 3:
                            $new_var = new Liquidacion_Model_Variable_Concepto_NoRemunerativoBase( $formula['Id'], $row['Nombre'], $periodo, $row['Codigo'], $row['TipoDeConceptoLiquidacion'], $row['TipoDeConcepto'], $row['Descripcion'], $formula['Formula'], $formula['Selector'], $formula['FormulaDetalle']);
                            break;
                        case 2:
                            $new_var = new Liquidacion_Model_Variable_Concepto_Remunerativo(       $formula['Id'], $row['Nombre'], $periodo, $row['Codigo'], $row['TipoDeConceptoLiquidacion'], $row['TipoDeConcepto'], $row['Descripcion'], $formula['Formula'], $formula['Selector'], $formula['FormulaDetalle']);
                            break;
                        case 1:
                            $new_var = new Liquidacion_Model_Variable_Concepto_RemunerativoBase(   $formula['Id'], $row['Nombre'], $periodo, $row['Codigo'], $row['TipoDeConceptoLiquidacion'], $row['TipoDeConcepto'], $row['Descripcion'], $formula['Formula'], $formula['Selector'], $formula['FormulaDetalle']);
                            break;
                        default:
                            throw new Liquidacion_Model_Exception('Tipo de concepto desconocido');
                            break;
                    }

                    $new_var->setCaracteristicas(array(

                         'NoHabitual'           => $formula['NoHabitual'],
                         'NoCalculada'          => $formula['NoCalculada'],
                         'NoGeneraRetroactivo'  => $formula['NoGeneraRetroactivo'],
                         'NoSumaEnSAC'          => $formula['NoSumaEnSAC'],
                         'EsSAC'                => $formula['EsSAC'],
                         'NoCuentaParaGanancia' => $formula['NoCuentaParaGanancia']
                    ));

                    $conceptos->add($new_var);
                }
            }
        }

        return $conceptos;
    }

    public function getConceptosExtras(Liquidacion_Model_Periodo $periodo, $servicio)
    {
        $conceptosExtras = $this->getConceptos($periodo, $servicio, 5);

        //TODO: ordenarlos

        return $conceptosExtras;
    }

    /**
     * Retorna variables de sistema (Son reservadas no usar el mismo nombre!)
     *
     * @param  string $nombre variable
     * @return mixed          valor de la variable
     */
    protected function _getVaribleEspecial($nombre, Liquidacion_Model_Periodo $periodo)
    {

        switch ($nombre) {
            case '@sumRemunerativosBase':   return Liquidacion_Model_Variable_Concepto_RemunerativoBase::getSum();  break;
            case '@sumRemunerativos':       return Liquidacion_Model_Variable_Concepto_Remunerativo::getSum();      break;
            case '@sumNoRemunerativosBase': return Liquidacion_Model_Variable_Concepto_NoRemunerativoBase::getSum();break;
            case '@sumNoRemunerativos':     return Liquidacion_Model_Variable_Concepto_NoRemunerativo::getSum();    break;
        }

        switch (substr($nombre,0,1)) {
            case '@':
                // ---------- Primitivas
                Liquidacion_Model_Variable_Primitiva_Implementacion::init();
                return new Liquidacion_Model_Variable_Primitiva(
                    $nombre,
                    $periodo,
                    Liquidacion_Model_Variable_Primitiva_Implementacion::getImplementacion($nombre)
                );
                break;
            case '#':
                // ---------- Tables
                $n  = substr($nombre,1);
                $id = Rrhh_Model_DbTable_LiquidacionesTablas::getIdPorNombre($n,$periodo);
                if (!$id) throw new Liquidacion_Model_Exception("No se encontro el nombre de la tabla $n en el periodo ".Liquidacion_Model_Periodo::getDescripcion($periodo));
                return $id;
                break;
            case '%':
                // ---------- Caracteristica
                $n  = substr($nombre,1);
                $id = Model_DbTable_Caracteristicas::getIdByName($n);
                if (!$id) throw new Liquidacion_Model_Exception("No se encontro el nombre de la caracteristica $n");
                return $id;
                break;
        }
    }

    /**
     *  Retorna una variable segun su nombre por periodo y servicio, o un escalar con un valor dado
     */
    public function getVariable($nombre, Liquidacion_Model_Periodo $periodo, $servicio)
    {
        // si es una variable especial (id de caracteristacas, tablas, etc) retornamos su valor
        $var = $this->_getVaribleEspecial($nombre, $periodo);

        if ($var !== null) {
            if ($var instanceof Liquidacion_Model_Variable) return $var;
            return new Liquidacion_Model_Variable(null, $nombre, '', '', $periodo , $var);
        }
            // si es una variable generica o un parametro lo leemos de la db
        $mv = Service_TableManager::get('Liquidacion_Model_DbTable_Variables_Variables');

        $vd = $mv->getVariablePeriodo($nombre, $periodo);

        if ($vd) {
            return new Liquidacion_Model_Variable($vd->Id, $nombre, '', '', $periodo , $vd->Formula);
        } else {
            throw new Liquidacion_Model_Exception("La variable $nombre no tiene valor definido para el periodo ".$periodo->getDescripcion());
            // return new Liquidacion_Model_Variable(null, $nombre, $periodo , '0');
        }
    }
}
