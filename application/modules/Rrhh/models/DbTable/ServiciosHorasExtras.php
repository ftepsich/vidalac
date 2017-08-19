<?php
class Rrhh_Model_DbTable_ServiciosHorasExtras extends Rad_Db_Table
{
    protected $_name = 'ServiciosHorasExtras';

    protected $_sort = array ('Anio DESC','Mes DESC');

    protected $_referenceMap    = array(

	    'TipoDeHoraExtra' => array(
            'columns'           => 'TipoDeHoraExtra',
            'refTableClass'     => 'Rrhh_Model_DbTable_TiposDeHorasExtras',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'			=> true,
            'comboSource'		=> 'datagateway/combolist',
            'refTable'			=> 'TiposDeHorasExtras',
            'refColumns'        => 'Id',
        ),
	    'Servicios' => array(
            'columns'           => 'Servicio',
            'refTableClass'     => 'Rrhh_Model_DbTable_Servicios',
            'refJoinColumns'    => array('Convenio'),
            'comboBox'			=> true,
            'comboSource'		=> 'datagateway/combolist',
            'refTable'			=> 'Servicios',
            'refColumns'        => 'Id',
            'comboPageSize'     => 20
        ),
        'Meses' => array(
            'columns'           => 'Mes',
            'refTableClass'     => 'Base_Model_DbTable_Meses',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'Meses',
            'refColumns'        => 'Id',
        )
    );

    protected $_dependentTables = array();

    /**
     * Campos a tener en cuenta para el log de la liquidacion, son aquellos que pueden generar retroactivos
     */
    protected $_logLiquidcionCampos = array(    'Servicio', 'Horas', 'Mes', 'Anio', 'TipoDeHoraExtra' );

   /**
     * Campos de fechas a tener en cuenta (inicio, fin, cierre)
     */
    protected $_logLiquidcionFechas = array(    'periodoAnio'   =>  'Anio',
                                                'periodoMes'    =>  'Mes'
    );

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

            $sql = "Servicio = {$data['Servicio']} and Anio = {$data['Anio']} and Mes = {$data['Mes']} and TipoDeHoraExtra = {$data['TipoDeHoraExtra']}";
            $reg = $this->fetchAll($sql);

            if (count($reg)) throw new Rad_Db_Table_Exception('Ya existen horas extras ingresadas para ese periodo con las mismas caracteristicas. Modifique las existentes en lugar de ingresar un nuevo registro.');

            // inserto el registro
            $id  = parent::insert($data);

            // Guardo el Log de Liquidaciones --------------------------------------------------
            $rowAct = $this->find($id)->current();
            $M_NL   = new Liquidacion_Model_DbTable_NovedadesDeLiquidaciones;
            $M_NL->asentarNovedad('I', $rowAct, null, $this->_logLiquidcionCampos, $this->_logLiquidcionFechas, $this->_logLiquidcionJerarquia, $rowAct->Servicio);
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
    public function update ($data, $where)
    {
        try {
            $this->_db->beginTransaction();
            $reg = $this->fetchAll($where);

            $M_NL = new Liquidacion_Model_DbTable_NovedadesDeLiquidaciones;

            foreach ($reg as $row) {

                parent::update($data,'Id ='.$row->Id);

                // Guardo el Log de Liquidaciones --------------------------------------------------
                $rowAct = $this->find($row->Id)->current();
                $M_NL->asentarNovedad('U', $rowAct, $row, $this->_logLiquidcionCampos, $this->_logLiquidcionFechas, $this->_logLiquidcionJerarquia, $rowAct->Servicio);
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

}