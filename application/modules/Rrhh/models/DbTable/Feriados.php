<?php

require_once('Rad/Db/Table.php');

class Rrhh_Model_DbTable_Feriados extends Rad_Db_Table
{
    protected $_name = 'Feriados';

    protected $_referenceMap = array(
        'TiposDeFeriados' => array(
            'columns' => 'TipoDeFeriado',
            'refTableClass' => 'Rrhh_Model_DbTable_TiposDeFeriados',
            'refJoinColumns' => array('Descripcion'),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'TiposDeFeriados',
            'refColumns' => 'Id',
            'comboPageSize' => 20
        ),
        'Convenios' => array(
            'columns' => 'Convenio',
            'refTableClass' => 'Rrhh_Model_DbTable_Convenios',
            'refJoinColumns' => array('Descripcion'),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'Convenios',
            'refColumns' => 'Id',
            'comboPageSize' => 20
        )
    );

    protected $_dependentTables = array('Rrhh_Model_DbTable_ServiciosFeriados');

    /**
    *   Devuelve la cantidad de dias feriados del periodo
    *   Tiene en cuenta el feriado especifico del CCT del servicio
    *
    *   @param row      $servicio   Servicio del Agente
    *   @param object   $periodo    periodo que se esta liquidando
    */
    public function getFeriadosPeriodo($servicio, $periodo) {
        $fd     = $periodo->getDesde()->format('Y-m-d');
        $fh     = $periodo->gethasta()->format('Y-m-d');
        // Esto es para que en una sola linea me traiga los feriados genericos (convenio = null) y los del convenio
        $feriadoCCT    = ($servicio->Convenio) ? " ifnull(convenio,{$servicio->Convenio}) = {$servicio->Convenio} and " : "";
        $where         = $feriadoCCT . " FechaEfectiva >= $fd and FechaEfectiva <= $fh ";

        $R = $this->fetchAll($where);
        return count($R);
    }

}