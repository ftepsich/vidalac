<?php
require_once 'Rad/Db/Table.php';

/**
 * Base_Model_DbTable_Empleados
 *
 * Administrador de Empleados
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Base
 * @class Base_Model_DbTable_Empleados
 * @extends Base_Model_DbTable_Personas
 */
class Base_Model_DbTable_Empleados extends Base_Model_DbTable_Personas
{
    protected $_permanentValues = array(
        'EsEmpleado' => 1
    );

    protected $_referenceMap = array(
        'Sexos' => array(
            'columns'           => 'Sexo',
            'refTableClass'     => 'Base_Model_DbTable_Sexos',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'Sexos',
            'refColumns'        => 'Id',
            'comboPageSize'     => 20
        ),
        'TiposDeDocumentos' => array(
            'columns'           => 'TipoDeDocumento',
            'refTableClass'     => 'Base_Model_DbTable_TiposDeDocumentos',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'TiposDeDocumentos',
            'refColumns'        => 'Id',
            'comboPageSize'     => 20
        ),
        'EstadosCiviles' => array(
            'columns'           => 'EstadoCivil',
            'refTableClass'     => 'Base_Model_DbTable_EstadosCiviles',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'EstadosCiviles',
            'refColumns'        => 'Id',
            'comboPageSize'     => 20
        )
    );

/**
     * Devuelve la antiguedad que tenga un empleado
     *
     * @param row       $servicio     Servicio a liquidar
     * @param object    $periodo      periodo a liquidar
     * @param int|null  $empresa      Id de la empresa
     * @return boolean
    */

    public static function getAntiguedadBase($servicio, $periodo, $empresa = null, $afecha = null)
    {
        $anios      = 0;
        $meses      = 0;
        $dias       = 0;
        $fechames   = 0;

        if ($afecha) {
            $f          = new datetime($afecha);
            $fecha      = $f->format('Y-m-d');
            $fechames   = $f->format('m');

        } else {
            $fecha      = $periodo->getHasta()->format('Y-m-d');
            $fechames   = $periodo->getHasta()->format('m');
        }

        $filtroEmpresa = ($empresa) ? " AND S.Empresa = $empresa " : " AND S.Empresa = $servicio->Empresa ";

        //MODIFICACION DE LA CONSULTA QUE CALCULA LA ANTIGUEDAD HECHO POR MAXI (CULPARLO A EL)

        
        $sql = "SELECT  IFNULL(SUM(YEAR( IF( IFNULL(S.FechaBaja,'2199-12-31') > '$fecha', '$fecha', S.FechaBaja))  - 
                    YEAR( IF( IFNULL(P.AntiguedadReconocidaAFecha,'1900-01-01') > S.FechaAlta, P.AntiguedadReconocidaAFecha,S.FechaAlta) ) - 
                    IF( MONTH( IF( IFNULL(S.FechaBaja,'2199-12-31') > '$fecha', '$fecha', S.FechaBaja) ) < MONTH( IF( IFNULL(P.AntiguedadReconocidaAFecha,'1900-01-01') > S.FechaAlta, P.AntiguedadReconocidaAFecha,S.FechaAlta)), 1, 
                        IF ( MONTH(IF( IFNULL(S.FechaBaja,'2199-12-31') > '$fecha', '$fecha', S.FechaBaja)) = MONTH(IF( IFNULL(P.AntiguedadReconocidaAFecha,'1900-01-01') > S.FechaAlta, P.AntiguedadReconocidaAFecha,S.FechaAlta)), 
                            IF (DAY( IF( IFNULL(S.FechaBaja,'2199-12-31') > '$fecha', '$fecha', S.FechaBaja) ) < DAY( IF( IFNULL(P.AntiguedadReconocidaAFecha,'1900-01-01') > S.FechaAlta, P.AntiguedadReconocidaAFecha,S.FechaAlta) ),1,0 )
                        ,0)
                    )),0) AS anios, 

                    IFNULL(SUM(MONTH(IF( IFNULL(S.FechaBaja,'2199-12-31') > '$fecha', '$fecha', S.FechaBaja)) - 
                    MONTH( IF( IFNULL(P.AntiguedadReconocidaAFecha,'1900-01-01') > S.FechaAlta, P.AntiguedadReconocidaAFecha,S.FechaAlta)) + 12 * 
                    IF( MONTH(IF( IFNULL(S.FechaBaja,'2199-12-31') > '$fecha', '$fecha', S.FechaBaja))<MONTH(IF( IFNULL(P.AntiguedadReconocidaAFecha,'1900-01-01') > S.FechaAlta, P.AntiguedadReconocidaAFecha,S.FechaAlta)), 1,
                        IF(MONTH(IF( IFNULL(S.FechaBaja,'2199-12-31') > '$fecha', '$fecha', S.FechaBaja))=MONTH(IF( IFNULL(P.AntiguedadReconocidaAFecha,'1900-01-01') > S.FechaAlta, P.AntiguedadReconocidaAFecha,S.FechaAlta)),
                            IF (DAY(IF( IFNULL(S.FechaBaja,'2199-12-31') > '$fecha', '$fecha', S.FechaBaja))<DAY(IF( IFNULL(P.AntiguedadReconocidaAFecha,'1900-01-01') > S.FechaAlta, P.AntiguedadReconocidaAFecha,S.FechaAlta)),1,0)
                        ,0)
                    ) - 
                    IF(MONTH(IF( IFNULL(S.FechaBaja,'2199-12-31') > '$fecha', '$fecha', S.FechaBaja))<>MONTH(IF( IFNULL(P.AntiguedadReconocidaAFecha,'1900-01-01') > S.FechaAlta, P.AntiguedadReconocidaAFecha,S.FechaAlta)),(DAY(IF( IFNULL(S.FechaBaja,'2199-12-31') > '$fecha', '$fecha', S.FechaBaja))<DAY(IF( IFNULL(P.AntiguedadReconocidaAFecha,'1900-01-01') > S.FechaAlta, P.AntiguedadReconocidaAFecha,S.FechaAlta))), 
                        IF (DAY(IF( IFNULL(S.FechaBaja,'2199-12-31') > '$fecha', '$fecha', S.FechaBaja))<DAY(IF( IFNULL(P.AntiguedadReconocidaAFecha,'1900-01-01') > S.FechaAlta, P.AntiguedadReconocidaAFecha,S.FechaAlta)),1,0 ) 
                    )),0) AS meses, 
                    
                    IFNULL(SUM(DAY( IF( IFNULL(S.FechaBaja,'2199-12-31') > '$fecha', '$fecha', S.FechaBaja) ) - 
                    DAY( IF( IFNULL(P.AntiguedadReconocidaAFecha,'1900-01-01') > S.FechaAlta, P.AntiguedadReconocidaAFecha,S.FechaAlta) ) +30 * (DAY(IF( IFNULL(S.FechaBaja,'2199-12-31') > '$fecha', '$fecha', S.FechaBaja)) < DAY(IF( IFNULL(P.AntiguedadReconocidaAFecha,'1900-01-01') > S.FechaAlta, P.AntiguedadReconocidaAFecha,S.FechaAlta)))),0) AS dias
                FROM    Servicios S
                INNER JOIN Personas P on P.Id  = S.Persona
                WHERE   S.FechaAlta <= '$fecha'
                AND     ifnull(S.FechaBaja,'2199-12-31') >= ifnull(P.AntiguedadReconocidaAFecha,'1900-01-01')
                $filtroEmpresa
                AND     S.Persona   = ".$servicio->Persona;

        $db = Zend_Registry::get("db");
        $antiguedad = $db->fetchRow($sql);


        if (count($antiguedad)) {

            if($antiguedad['dias']>29 && ($fechames != 1 && $fechames != 3 && $fechames != 5 && $fechames != 7 && $fechames != 8 && $fechames != 10 && $fechames != 12)) {
                $antiguedad['meses'] += 1; 
                $dias = $antiguedad['dias']-30;
            } else {
                if($antiguedad['dias']>30 && ($fechames == 1 || $fechames == 3 || $fechames == 5 || $fechames == 7 || $fechames == 8 || $fechames == 10 || $fechames == 12)) {
                    $antiguedad['meses'] += 1; 
                    $dias = $antiguedad['dias']-30;
                } else {
                    $dias = $antiguedad['dias'];
                }
            }

        	if($antiguedad['meses']>11) {
        		$antiguedad['anios'] += 1; 
        		$meses = $antiguedad['meses']-12;
        	} else {
        		$meses = $antiguedad['meses'];
        	}
            $anios = $antiguedad['anios'];         
        }

        echo '++ getAntiguedadBase ++++++++++++++++++++++++++++++++++++++++++++++++++++++'.PHP_EOL;
        echo 'anios:'. $anios.PHP_EOL;
        echo 'meses:'. $meses.PHP_EOL;
        echo 'dias :'. $dias.PHP_EOL;
        return array('anios' => $anios, 'meses' => $meses, 'dias' => $dias);
    }

/**
     * Devuelve la antiguedad que tenga un empleado
     *
     * @param row       $servicio     Servicio a liquidar
     * @param object    $periodo      periodo a liquidar
     * @param int|null  $empresa      Id de la empresa
     * @return boolean
    */
  
    public static function getAntiguedadReconocida($servicio, $periodo, $empresa = null)
    {
        $anios = 0;
        $meses = 0;
        $dias  = 0;
        // Veo la antiguedad reconocida a una fecha
        $sql  = "SELECT     AntiguedadReconocidaAnio    as anios, 
                            AntiguedadReconocidaMes     as meses, 
                            AntiguedadReconocidaDia     as dias, 
                            AntiguedadReconocidaAFecha  as aFecha
                FROM    Personas 
                WHERE   Id = ".$servicio->Persona;

        $db = Zend_Registry::get("db");
        $a  = $db->fetchRow($sql);

        if ($a) {
            $anios = ($a['anios'])? $a['anios'] : 0;
            $meses = ($a['meses'])? $a['meses'] : 0;
            $dias  = ($a['dias'])? $a['dias'] : 0;
        }
        //--------------------------------------------------------------------------------------------------------------------------------        
        //--------------------------------------------------------------------------------------------------------------------------------
        // esto es solo para collana facundo porq solo por 5 dias no da igual que ayelen y no le suma un año de antiguedad en mayo...(Maxi)
        if($servicio->Persona == 79){
            $dias  = $dias + 6;
        }
        //--------------------------------------------------------------------------------------------------------------------------------
        //--------------------------------------------------------------------------------------------------------------------------------        
        echo '++ getAntiguedadReconocida ++++++++++++++++++++++++++++++++++++++++++++++++++++++'.PHP_EOL;
        echo 'anios:'. $anios.PHP_EOL;
        echo 'meses:'. $meses.PHP_EOL;
        echo 'dias :'. $dias.PHP_EOL;        
        
        return array('anios' => $anios, 'meses' => $meses, 'dias' => $dias);
    }

    public static function getAntiguedadCompleta($servicio, $periodo, $empresa = null, $afecha = null)
    {
        echo '1afecha :'. $afecha.PHP_EOL;
        $aBase = Base_Model_DbTable_Empleados::getAntiguedadBase($servicio, $periodo, $empresa, $afecha);
        $aReco = Base_Model_DbTable_Empleados::getAntiguedadReconocida($servicio, $periodo, $empresa);
        
        $a = new datetime('1900-01-01');
        if ($aBase['anios'])       $a->modify( "+{$aBase['anios']} year" );
        if ($aReco['anios'])       $a->modify( "+{$aReco['anios']} year" );
        if ($aBase['meses'])       $a->modify( "+{$aBase['meses']} month" );
        if ($aReco['meses'])       $a->modify( "+{$aReco['meses']} month" );
        if ($aBase['dias'])        $a->modify( "+{$aBase['dias']} day" );
        if ($aReco['dias'])        $a->modify( "+{$aReco['dias']} day" );

            //$a->modify( '-1 day' );
            //$dias  = $a->format('d');
            
            if($a->format('Y-m-d') == '1900-01-01'){
                $dias  = $a->format('d') - 1;
            } else {
                $dias  = $a->format('d');
            }
            $meses = $a->format('m') - 1;
            $anios = $a->format('Y') - 1900;
            
        echo '++ getAntiguedadCompleta ++++++++++++++++++++++++++++++++++++++++++++++++++++++'.PHP_EOL;
        echo 'anios:'. $anios.PHP_EOL;
        echo 'meses:'. $meses.PHP_EOL;
        echo 'dias :'. $dias.PHP_EOL; 

        $anios = ($anios<0) ? 0:$anios;
        $meses = ($meses<0) ? 0:$meses;
        $dias =  ($dias<0)  ? 0:$dias;        
        
        return array('anios' => $anios, 'meses' => $meses, 'dias' => $dias);
    }

    public static function getAniosAntiguedadEstandar($servicio, $periodo, $empresa = null)
    {
        $a = Base_Model_DbTable_Empleados::getAntiguedadCompleta($servicio, $periodo, $empresa = null);
        return $a['anios'];
    }

    public static function getAniosAntiguedadCamioneros($servicio, $periodo, $empresa = null)
    {
        //$a = Base_Model_DbTable_Empleados::getAntiguedadCompleta($servicio, $periodo, $empresa);
        
        $mesPeriodo         = $periodo->getHasta()->format('m');
        $anioPeriodo        = $periodo->getHasta()->format('Y');

        echo 'mesPeriodo:'. $mesPeriodo.PHP_EOL;
        echo 'anioPeriodo:'. $anioPeriodo.PHP_EOL;

        if ($mesPeriodo == 12) {
            $a = Base_Model_DbTable_Empleados::getAntiguedadCompleta($servicio, $periodo);
        } else {
            // Recupero el periodo 12 del año anterior
            $anioAnterior       = $anioPeriodo - 1;
            $finAnioAnterior    = $anioAnterior.'-12-31';

            echo 'anioAnterior:'. $anioAnterior.PHP_EOL;
            echo 'finAnioAnterior:'. $finAnioAnterior.PHP_EOL;

            $a = Base_Model_DbTable_Empleados::getAntiguedadCompleta($servicio, $periodo, $empresa, $finAnioAnterior);
        }

        if ($a['meses'] && $a['meses'] > 6) $a['anios'] = $a['anios'] + 1;

        return $a['anios'];
    }

    /**
     * Devuelve la antiguedad que tenga un empleado
     *
     * @param row       $servicio     Servicio a liquidar
     * @param object    $periodo      periodo a liquidar
     * @param int|null  $empresa      Id de la empresa
     * @return boolean
    */
    public static function getAntiguedad($servicio, $periodo, $empresa = null)
    {
        /* TODO: Implementar antiguedad reconocida a una fecha */

        $db = Zend_Registry::get("db");

        $filtroEmpresa = ($empresa) ? " AND S.Empresa = $empresa " : " AND S.Empresa = $servicio->Empresa ";

        $sql = "    SELECT  DATE_ADD(   '1900-01-01',
                                        INTERVAL SUM(
                                                        DATEDIFF(
                                                                if( ifnull(S.FechaBaja,'2199-12-31') > LP.FechaHasta,
                                                                    LP.FechaHasta,
                                                                    S.FechaBaja)
                                                                ,
                                                                S.FechaAlta
                                                        )
                                                    )
                                        day) as Antiguedad
                    FROM    Servicios S,
                            LiquidacionesPeriodos LP
                    WHERE   S.FechaAlta <= LP.FechaHasta
                    $filtroEmpresa
                    AND     LP.Id       = ".$periodo->getId()."
                    AND     S.Persona   = ".$servicio->Persona;

        $antiguedad = $db->fetchOne($sql);

        // Veo la antiguedad reconocida a una fecha
        $sql2 = "SELECT     AntiguedadReconocidaAnio, 
                            AntiguedadReconocidaMes, 
                            AntiguedadReconocidaDia, 
                            AntiguedadReconocidaAFecha
                FROM    Personas 
                WHERE   Id = ".$servicio->Persona;

        $sql2 = "SELECT AntiguedadReconocidaAnio FROM Personas WHERE Id = ".$servicio->Persona;        
        $anioReconocido =  $db->fetchOne($sql2);
        $anioReconocido = ($anioReconocido)? $anioReconocido : 0;
        $sql2 = "SELECT AntiguedadReconocidaMes  FROM Personas WHERE Id = ".$servicio->Persona;        
        $mesReconocido =  $db->fetchOne($sql2);
        $mesReconocido = ($mesReconocido)? $mesReconocido : 0;
        $sql2 = "SELECT AntiguedadReconocidaDia  FROM Personas WHERE Id = ".$servicio->Persona;        
        $diaReconocido =  $db->fetchOne($sql2);
        $diaReconocido = ($diaReconocido)? $diaReconocido : 0;

        if ($antiguedad) {
            $a = new datetime($antiguedad);

            // ajusto con lo reconocido
            if ($anioReconocido) $a->modify( "+$anioReconocido year" );
            if ($mesReconocido)  $a->modify( "+$mesReconocido month" );
            if ($diaReconocido)  $a->modify( "+$diaReconocido day" );

            // Como arranca a sumar de 1900-01-01 ya trae un mes y un dia demas, asi que los resto
            $a->modify( '-1 day' );
            //$a->modify( '-1 month' );
            $aniosAntiguedad = $a->format('Y') - 1900;
        } else {
            $aniosAntiguedad = 0;

            if ($anioReconocido || $mesReconocido || $diaReconocido) {
                $a = new datetime('1900-01-01');

                // ajusto con lo reconocido
                if ($anioReconocido) $a->modify( "+$anioReconocido year" );
                if ($mesReconocido)  $a->modify( "+$mesReconocido month" );
                if ($diaReconocido)  $a->modify( "+$diaReconocido day" );

                // Como arranca a sumar de 1900-01-01 ya trae un mes y un dia demas, asi que los resto
                $a->modify( '-1 day' );
                //$a->modify( '-1 month' );
                $aniosAntiguedad = $a->format('Y') - 1900;                
            }

        }
        return ($aniosAntiguedad < 0)? 0 : $aniosAntiguedad;
    }

    /**
     * Devuelve la antiguedad que tenga un camionero
     * Segun el CCT 40/89 la antiguedad se actualiza cada principio de año
     * y si el segmento de tiempo es mayor a 6 meses suma un año mas
     *
     * @param row       $servicio     Servicio a liquidar
     * @param object    $periodo      periodo a liquidar
     * @param int|null  $empresa      Id de la empresa
     * @return boolean
    */
    public static function getAntiguedadCamioneros($servicio, $periodo, $empresa = null)
    {

        $antiguedad         = 0;

        $filtroEmpresa      = ($empresa) ? " AND S.Empresa = $empresa " : " AND S.Empresa = $servicio->Empresa ";
        // La antiguedad cambia cada 31/12 de cada año
        $mesPeriodo         = $periodo->getHasta()->format('m');
        $anioPeriodo        = $periodo->getHasta()->format('Y');
        $finPeriodo         = $periodo->getHasta()->format('Y-m-d');
        $persona            = $servicio->Persona;

        $anio = ($mesPeriodo == 12) ? $anioPeriodo : $anioPeriodo -1;

        $ultimaFechaCambio  = $anio . "-12-31";

        // ojo... deberia aplanar los servicios por las dudas se superpongan

        $sql = "SELECT  S.FechaAlta,
                        if (ifnull(S.FechaBaja,'$ultimaFechaCambio') >= '$ultimaFechaCambio',
                            '$ultimaFechaCambio',
                            S.FechaBaja) as FechaBaja
                FROM    Servicios S
                WHERE   S.FechaAlta <= '$ultimaFechaCambio'
                AND     S.Persona   = $persona
                $filtroEmpresa ";

        $db = Zend_Registry::get("db");
        $R  = $db->fetchAll($sql);

        if ($R) {

            $ff = new datetime();
            $fi = new datetime();

            foreach ($R as $row) {
                $fMin = new datetime($row['FechaAlta']);
                $aMin = $fMin->format('Y');

                $fMax = new datetime($row['FechaBaja']);
                $aMax = $fMax->format('Y');

                // Veo si el servicio es dentro del mismo año
                if ($aMin == $aMax) {
                    $cd[$aMin] += $fMax->diff($fMin)->days;
                } else {

                    // Reviso el inicio
                    $fi->setDate($aMin,12,31);
                    $cd[$aMin] += $fMin->diff($fi)->days;

                    // Reviso el final
                    $ff->setDate($aMax,01,01);
                    $cd[$aMax] += $ff->diff($fMax)->days;

                    // Reviso el medio (Si queda un periodo al medio) ... ojo resta dos ???
                    $antiguedad += $aMax - $aMin - 1;
                }
            }

            // Reviso veo si en algun periodo dio mas de 180 dias y en cada caso le sumo un año de antiguedad

            foreach ($cd as $row => $valor) {
                if ($valor >= 180) $antiguedad += 1;
            }

            return ($antiguedad < 0)? 0 : $antiguedad;
        } else {
            return 0;
        }
    }

    /**
     * Devuelve la cantidad de dias trabajados en ese servicio en un periodo
     *
     * @param row       $servicio     Servicio a liquidar
     * @param object    $periodo      periodo a liquidar
     * @return boolean
    */
    public function getDiasTrabajados($servicio, $periodo)
    {
        return  0;
    }


    /**
     * Devuelve 1 si es el primer semestre y 2 si es el segundo
     *
     * @param row       $servicio     Servicio a liquidar
     * @param object    $periodo      periodo a liquidar
     * @return boolean
    */
    public function getSacSemestre($servicio, $periodo)
    {
        $mesInicioSemestre     = $periodo->getFechaInicioSemestre('m');

        if ($mesInicioSemestre == 1) { 
            return 1; 
        } else {
            return 2;
        }
    }

    /**
     * Devuelve la antiguedad que tenga un empleado
     *
     * @param row       $servicio     Servicio a liquidar
     * @param object    $periodo      periodo a liquidar
     * @param int|null  $empresa      Id de la empresa
     * @return boolean
    */
    public static function getAguinaldoDiasTrabajados($servicio, $periodo, $empresa = null)
    {
        $db = Zend_Registry::get("db");

//      $filtroEmpresa = ($empresa) ? " AND S.Empresa = $empresa " : " AND S.Empresa = $servicio->Empresa ";

        $diasTrabajados = 0;

        $inicioSemestre     = $periodo->getFechaInicioSemestre('Y-m-d');
        $finSemestre        = $periodo->getFechaFinSemestre('Y-m-d');

        $FechaInicioSiguienteSemestre = new DateTime($finSemestre);
        $FechaInicioSiguienteSemestre->modify('+1 day');
        $inicioSiguienteSemestre = $FechaInicioSiguienteSemestre->format('Y-m-d');

        /*
        $FechaFinAnteriorSemestre = new DateTime($inicioSemestre);
        $FechaFinAnteriorSemestre->modify('-1 day');
        $finAnteriorSemestre    = $FechaFinAnteriorSemestre->format('Y-m-d');
        */

        $sql = "    SELECT  DATE_ADD(
                                    '1900-01-01',
                                    INTERVAL SUM(
                                                DATEDIFF(
                                                if( ifnull(S.FechaFin,'2199-12-31') > '$finSemestre',
                                                    '$inicioSiguienteSemestre', -- OJO es asi para sumar un dia mas a cada SS para que sea inclusive
                                                    DATE_ADD(S.FechaFin, interval 1 day)
                                                )
                                                ,
                                                if( S.FechaInicio < '$inicioSemestre',
                                                    '$inicioSemestre',
                                                    S.FechaInicio)
                                                )
                                            )
                                     day) as Antiguedad
                    FROM    ServiciosSituacionesDeRevistas S
                    inner join Servicios Se on Se.Id = S.Servicio
                    inner join SituacionesDeRevistas SS on SS.Id = S.SituacionDeRevista
                    WHERE   S.Persona  = {$servicio->Persona}
                    and     Se.Empresa = {$servicio->Empresa}
                    and     ifnull(SS.NoSumaParaAguinaldo,0) <> 1
                    and     S.FechaInicio <= '$finSemestre'
                    and     ifnull(S.FechaFin,'2199-01-01') >= '$inicioSemestre'
                    ";

        /*
        $sql = "    SELECT  DATE_ADD(
                                    '1900-01-01',
                                    INTERVAL SUM(
                                                DATEDIFF(
                                                if( ifnull(S.FechaFin,'2199-12-31') > '2014-06-30',
                                                    '2014-07-01', -- OJO es asi para sumar un dia mas a cada SS para que sea inclusive
                                                    DATE_ADD(S.FechaFin, interval 1 day)
                                                )
                                                ,
                                                if( S.FechaInicio < '2014-01-01',
                                                    '2014-01-01',
                                                    S.FechaInicio)
                                                )
                                            )
                                     day) as Antiguedad
                    FROM    ServiciosSituacionesDeRevistas S
                    inner join SituacionesDeRevistas SS on SS.Id = S.SituacionDeRevista
                    WHERE   S.Persona = {$servicio->Persona}
                    and     ifnull(SS.NoSumaParaAguinaldo,0) <> 1
                    and     S.FechaInicio <= '2014-06-30'
                    and     ifnull(S.FechaFin,'2199-01-01') >= '2014-01-01'
                    ";
        */
        $cantDias = $db->fetchOne($sql);

        if ($cantDias) {
            // Como arranca a sumar de 1900-01-01 ya trae un mes y un dia demas, asi que los resto
            $a = new datetime($cantDias);
            $b = new datetime('1900-01-01');
            $diasTrabajados = $b->diff($a)->days;
        }
        // hay que sumar uno para que incluya el ultimo dia
        return $diasTrabajados;
    }

    /**
     * Devuelve el monto del sac de un semestre
     *
     * @param row       $servicio     Servicio a liquidar
     * @param object    $periodo      periodo a liquidar
     * @param int       $d            1: MaxDevengado, 2: mes MaxDevengado
     * @return boolean
    */
    public function getAguinaldoDevengado($servicio, $periodo,$d)
    {

        /* Lo del recibo actual que aun no esta en la base */
        $montoMesActual = 0;
        $montoMesActual = Liquidacion_Model_Variable_Concepto_Remunerativo::getSum();

        /* TODO: Acomodar los meses del semestre */
        $anio = $periodo->getAnio();

        if ($periodo->getMes() > 6) {
            $primerMes      = 7;
            $anteultimoMes  = 11;
            $ultimoMes      = 12;
        } else {
            $primerMes      = 1;
            $anteultimoMes  = 5;
            $ultimoMes      = 6;
        }

        $sql = "    SELECT      sum(LRD.Monto) as Monto, LRD.PeriodoDevengado, LP.Anio, LP.Valor
                    From        LiquidacionesRecibosDetalles LRD
                    inner join  LiquidacionesRecibos LR     on  LR.Id   = LRD.LiquidacionRecibo
                    inner join  Liquidaciones L             on  L.Id    = LR.Liquidacion
                    inner join  LiquidacionesPeriodos LP    on  LP.Id   = LRD.PeriodoDevengado
                    inner join  VariablesDetalles VD        on  VD.Id   = LRD.VariableDetalle
                    inner join  Variables V                 on  V.Id    = VD.Variable
                    where   LR.Ajuste   = 0
                    and     L.TipoDeLiquidacion = 1
                    and     LR.Persona  = {$servicio->Persona}
                    and     LP.Anio     = $anio
                    and     LP.Valor    >= $primerMes
                    and     LP.Valor    <= $ultimoMes -- el ultimo mes es el qeu estoy liquidando y no esta aun en la base
                    and     V.TipoDeConceptoLiquidacion in (1,2) -- Remunerativos
                    and     ifnull(V.NoSumaEnSAC,0) <> 1
                    group by LRD.PeriodoDevengado
                    order by 1 desc
        ";

        $db = Zend_Registry::get("db");
        $R  = $db->fetchAll($sql);

        if ($R) {
            $monto  = 0;
            $meses  = 0;
            $sac    = 0;

            foreach ($R as $row) {
                $meses++;

                // como lo ordeno de mayor a menor solo miro el valor del primero
                if (!$monto) {
                    $monto              = $row['Monto'];
                    $mesMaxDevengado    = $row['Valor'];
                }
            }
        }
            

            // a meses le sumo uno por el mes actual que lo traigo aparte por que a esta altura no esta
            // grabado en la base de datos

        if ($monto >= $montoMesActual) {
            $mesMax    = $mesMaxDevengado;
            $montoUsar = $monto;
        } else {
            $mesMax    = $ultimoMes;
            $montoUsar = $montoMesActual;
        }

        /*
        echo '-- SAC SAC SAC ----------------------------------------------------------'.PHP_EOL;
        echo $sql.PHP_EOL;
        echo '-------------------------------------------------------------------------'.PHP_EOL;
        echo '$mesMaxDevengado = '.$mesMaxDevengado.PHP_EOL;
        echo '$monto = '.$monto.PHP_EOL;
        echo '$montoMesActual = '.$montoMesActual.PHP_EOL;
        echo '$meses = '.$mesMax.PHP_EOL;
        //echo '$sac = '.$sac.PHP_EOL;
        echo '-------------------------------------------------------------------------'.PHP_EOL;
        */
        if ($d == 1) {
            return $montoUsar;
        } else {
            return $mesMax;
        }
    }

    /**
     * Devuelve el maximo SueldoBruto devengado en los ultimos 12 meses
     *
     * @param row       $servicio     Servicio a liquidar
     * @param object    $periodo      periodo a liquidar
     * @param int       $d            1: MaxDevengado, 2: mes MaxDevengado
     * @return boolean
    */
    public function getMaxDevengadoUltimos12Meses($servicio, $periodo, $d)
    {

        $mesPeriodo         = $periodo->getHasta()->format('m');
        $anioPeriodo        = $periodo->getHasta()->format('Y');

        /* TODO: Acomodar los meses del semestre */

        $sql = "    SELECT      sum(LRD.Monto) as Monto, LRD.PeriodoDevengado, LP.Anio, LP.Valor
                    From        LiquidacionesRecibosDetalles LRD
                    inner join  LiquidacionesRecibos LR     on  LR.Id   = LRD.LiquidacionRecibo
                    inner join  Liquidaciones L             on  L.Id    = LR.Liquidacion
                    inner join  LiquidacionesPeriodos LP    on  LP.Id   = LRD.PeriodoDevengado
                    inner join  VariablesDetalles VD        on  VD.Id   = LRD.VariableDetalle
                    inner join  Variables V                 on  V.Id    = VD.Variable
                    where   LR.Ajuste   = 0
                    and     L.TipoDeLiquidacion = 1
                    and     LR.Persona  = {$servicio->Persona}
                    and     (
                            ( LP.Anio = $anioPeriodo and LP.Valor >= 1 and LP.Valor < $mesPeriodo) -- ojo es menor y no menor o igual ya que no lo tengo al mes este en un recibo aun
                            or
                            ( LP.Anio = $anioPeriodo-1 and LP.Valor >= $mesPeriodo and LP.Valor <= 12)
                            )
                    and     V.TipoDeConceptoLiquidacion in (1,2) -- Remunerativos
                    -- Va tambien ya que el sac no se tiene en cuenta ni las otras cosas
                    and     ifnull(V.NoSumaEnSAC,0) <> 1
                    group by LRD.PeriodoDevengado
                    order by 1 desc
        ";

        $db = Zend_Registry::get("db");
        $R  = $db->fetchAll($sql);

        if ($R) {
            $monto  = 0;
            $meses  = 0;
            $sac    = 0;

            foreach ($R as $row) {
                $meses++;

                // como lo ordeno de mayor a menor solo miro el valor del primero
                if (!$monto) {
                    $monto              = $row['Monto'];
                    $mesMaxDevengado    = $row['Valor'];
                }
            }

            // $montoMesActual = Liquidacion_Model_Variable_Concepto_Remunerativo::getSum();

            // a meses le sumo uno por el mes actual que lo traigo aparte por que a esta altura no esta
            // grabado en la base de datos

                $mesMax    = $mesMaxDevengado;
                $montoUsar = $monto;
            /*
            if ($monto >= $montoMesActual) {
                $mesMax    = $mesMaxDevengado;
                $montoUsar = $monto;
            } else {
                $mesMax    = $mesPeriodo;
                $montoUsar = $montoMesActual;
            }
            */
            if ($d == 1) {
                return $montoUsar;
            } else {
                return $mesMax;
            }
        } else {
            return 0;
        }
    }

    /**
     * Devuelve los años trabajados por un empleado a los fines de las indemnizaciones
     *
     * @param row       $servicio     Servicio a liquidar
     * @param object    $periodo      periodo a liquidar
     * @param int|null  $empresa      Id de la empresa
     * @return boolean
    */
    public static function getAniosTrabajadosParaIndemnizacion($servicio, $periodo, $empresa = null)
    {
        /* TODO: Implementar antiguedad reconocida a una fecha */

        $db = Zend_Registry::get("db");

        $filtroEmpresa = ($empresa) ? " AND S.Empresa = $empresa " : " AND S.Empresa = $servicio->Empresa ";

        $sql = "    SELECT  DATE_ADD(   '1900-01-01',
                                        INTERVAL SUM(
                                                        DATEDIFF(
                                                                if( ifnull(S.FechaBaja,'2199-12-31') > LP.FechaHasta,
                                                                    LP.FechaHasta,
                                                                    S.FechaBaja)
                                                                ,
                                                                S.FechaAlta
                                                        )
                                                    )
                                        day) as Antiguedad
                    FROM    Servicios S,
                            LiquidacionesPeriodos LP
                    WHERE   S.FechaAlta <= LP.FechaHasta
                    $filtroEmpresa
                    AND     LP.Id       = ".$periodo->getId()."
                    AND     S.Persona   = ".$servicio->Persona;

        $antiguedad = $db->fetchOne($sql);

        if ($antiguedad) {
            // Como arranca a sumar de 1900-01-01 ya trae un mes y un dia demas, asi que los resto
            $a = new datetime($antiguedad);
            $a->modify( '-1 day' );
            //$a->modify( '-1 month' );
            $aniosAntiguedad = $a->format('Y') - 1900;

            $mesAntiguedad = $a->format('m') -1; // en este caso hay que restar 1
            if ($mesAntiguedad > 2 ) $aniosAntiguedad = $aniosAntiguedad + 1;
        } else {
            $aniosAntiguedad = 0;
        }

        return $aniosAntiguedad;
    }


    public function fetchEsDeProduccion($where = null, $order = null, $count = null, $offset = null)
    {
        // Joineo con areas de trabajo
        $j = $this->getJoiner();
        $j->joinDep(
            'Base_Model_DbTable_AreasDeTrabajosPersonas',
            array(
                'AreaDeTrabajo' => 'AreaDeTrabajo'
            ),
            'Empleados'
        );

        $where = $this->_addCondition($where, "AreaDeTrabajo = 1");

        $rtn = parent:: fetchAll($where, $order, $count, $offset);
        // quito el join, porque la ejecucion puede seguir y donde llamen a otro fetch va a seguir joineando
        $j->clear();

        return $rtn;
    }



}