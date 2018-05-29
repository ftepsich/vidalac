<?php
class Rrhh_Model_DbTable_ServiciosFeriados extends Rad_Db_Table
{
    protected $_name = 'ServiciosFeriados';

    protected $_referenceMap    = array(

        'Servicios' => array(
            'columns'           => 'Servicio',
            'refTableClass'     => 'Rrhh_Model_DbTable_Servicios',
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'Servicios',
            'refColumns'        => 'Id',
        ),
        'Feriados' => array(
            'columns'           => 'Feriado',
            'refTableClass'     => 'Rrhh_Model_DbTable_Feriados',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'Feriados',
            'refColumns'        => 'Id',
        )    );

    protected $_dependentTables = array();

   /**
     * Jerarquia que afecta una modificacion realizada desde este modelo
     */
    protected $_logLiquidcionJerarquia = 1; // 1: Servicio

    /**
     * Insert
     *
     * @param array $data
     * @return mixed
     */
    public function insert($data) {
        try {
            $this->_db->beginTransaction();

            // Controlo que no este cargado ese feriado para ese servicio

            $sql = "Servicio = {$data['Servicio']} and Feriado = {$data['Feriado']}";
            $reg = $this->fetchAll($sql);

            if (count($reg)) throw new Rad_Db_Table_Exception('El feriado trabajado que intenta informar ya se encuentra ingresado.');

            // inserto el registro
            $id  = parent::insert($data);

            // Guardo el Log de Liquidaciones --------------------------------------------------
            //$rowAct = $this->find($id)->current();
            //$M_NL   = new Liquidacion_Model_DbTable_NovedadesDeLiquidaciones;
            //$M_NL->asentarNovedad('I', $rowAct, null, null, null, $this->_logLiquidcionJerarquia, $rowAct->Servicio);
            // ----------------------------------------------------------- Fin Log Liquidaciones

            $this->_db->commit();

            return $id;
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    /**
     * Update
     *
     * @param array $data   Informacion a cambiar
     * @param array $where  Registros que se deben modificar
     */
    public function update($data,$where) {
        throw new Rad_Db_Table_Exception('No se puede modificar un Feriado Trabajado, eliminelo de ser necesario e ingreselo con la fecha correcta.');
    }

    /**
     * Delete
     *
     * @param array $where  Registros que se deben eliminar
     */
    public function delete ($where)
    {
        try {
            $this->_db->beginTransaction();
            $reg = $this->fetchAll($where);

            $M_NL   = new Liquidacion_Model_DbTable_NovedadesDeLiquidaciones;

            foreach ($reg as $row) {

                parent::delete('Id =' . $row->Id);

                // Guardo el Log de Liquidaciones --------------------------------------------------
                $M_NL->asentarNovedad('D', $row, null, $this->_logLiquidcionCampos, $this->_logLiquidcionFechas, $this->_logLiquidcionJerarquia, $row->Servicio);
                // ----------------------------------------------------------- Fin Log Liquidaciones
            }
            $this->_db->commit();
            return true;
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    /**
    *   Devuelve la cantidad de dias feriados que una persona trabajo
    *   sin distinguir entre dias laborables normales o domingos
    *   elcaso del sabado a la tarde se toma como hs extras * 2
    *
    *   @param row      $servicio   Servicio del Agente
    *   @param object   $periodo    periodo que se esta liquidando
    */
    public static function getFeriadosTrabajados($servicio, $periodo) {
        $fd     = $periodo->getDesde()->format('Y-m-d');
        $fh     = $periodo->getHasta()->format('Y-m-d');

        $sql =   "  SELECT  count(SF.Id) as Cantidad
                    FROM    ServiciosFeriados SF
                    INNER JOIN Feriados F on F.Id = SF.Feriado
                    WHERE   SF.Servicio = {$servicio->Id}
                    AND     F.FechaEfectiva >= '$fd'
                    AND     F.FechaEfectiva <= '$fh'
                    ";
        $db = Zend_Registry::get('db');
        return $db->fetchOne($sql);
    }

    /**
    *   Devuelve la cantidad de dias feriados en la semana laboral
    *
    *   @param row      $servicio   Servicio del Agente
    *   @param object   $periodo    periodo que se esta liquidando
    */
    public static function getFeriadosLaborablesTrabajados($servicio, $periodo) {
        $fd     = $periodo->getDesde()->format('Y-m-d');
        $fh     = $periodo->getHasta()->format('Y-m-d');

        $sql =   "  SELECT  count(SF.Id) as Cantidad
                    FROM    ServiciosFeriados SF
                    INNER JOIN Feriados F on F.Id = SF.Feriado
                    WHERE   SF.Servicio = {$servicio->Id}
                    AND     DATE_FORMAT(F.FechaEfectiva, '%w') <> 0
                    AND     F.FechaEfectiva >= '$fd'
                    AND     F.FechaEfectiva <= '$fh'
                    ";

        $db = Zend_Registry::get('db');
        return $db->fetchOne($sql);
    }

    /**
    *   Devuelve la cantidad de dias feriados domingos trabajados
    *
    *   @param row      $servicio   Servicio del Agente
    *   @param object   $periodo    periodo que se esta liquidando
    */
    public static function getFeriadosDomingosTrabajados($servicio, $periodo) {
        $fd     = $periodo->getDesde()->format('Y-m-d');
        $fh     = $periodo->getHasta()->format('Y-m-d');

        $sql =   "  SELECT  count(SF.Id) as Cantidad
                    FROM    ServiciosFeriados SF
                    INNER JOIN Feriados F on F.Id = SF.Feriado
                    WHERE   SF.Servicio = {$servicio->Id}
                    AND     DATE_FORMAT(F.FechaEfectiva, '%w') = 0
                    AND     F.FechaEfectiva >= '$fd'
                    AND     F.FechaEfectiva <= '$fh'
                    ";

        $db = Zend_Registry::get('db');
        return $db->fetchOne($sql);
    }

}