<?php
class Rrhh_Model_DbTable_LiquidacionesTablas extends Rad_Db_Table
{
    protected $_name = 'LiquidacionesTablas';

    protected $_sort = array('FechaDesde desc','Descripcion asc');

    protected $_referenceMap    = array(

        'Convenios' => array(
            'columns'           => 'Convenio',
            'refTableClass'     => 'Rrhh_Model_DbTable_Convenios',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'Convenios',
            'refColumns'        => 'Id',
            'comboPageSize'     => 10
        ),
        'TiposDeLiquidacionesTablas' => array(
            'columns'           => 'TipoDeLiquidacionTabla',
            'refTableClass'     => 'Rrhh_Model_DbTable_TiposDeLiquidacionesTablas',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'TiposDeLiquidacionesTablas',
            'refColumns'        => 'Id',
            'comboPageSize'     => 10
        ),
        'Grupos' => array(
            'columns'           => 'Grupo',
            'refTableClass'     => 'Rrhh_Model_DbTable_LiquidacionesTablasCategoriasGrupos',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'LiquidacionesTablasCategoriasGrupos',
            'refColumns'        => 'Id',
            'comboPageSize'     => 10
        )
    );

    protected $_dependentTables = array();

    /**
     * Validadores
     *
     * FechaHasta    -> mayor a fechadesde
     *
     */
    /*
    protected $_validators = array(
        'FechaHasta'=> array(
            array( 'GreaterThan',
                    '{FechaDesde}'
            ),
            'messages' => array('La fecha de baja no puede ser menor e igual que la fecha de alta.')
        ),
        'Descripcion' => array(
                array(  'Db_NoRecordExists',
                        'LiquidacionesTablas',
                        'Nombre',
                        array(  'field' => 'Id',
                                'value' => "{Id}"
                        )
                ),
                'messages' => array('El Nombre que intenta ingresar se encuentra repetido.')
        )
    );
    */

    /**
    * Busca el detalle de una tabla con los parametros Tabla,Valor(escalar o rango), y el periodo
    *
    * @param int            $tabla      Id de la tabla
    * @param string|int     $tabla      Valor que se requiere
    * @param obj periodo    $periodo    Periodo sobre el cual buscar
    *
    */
    public static function getValor($tabla, $valor, Liquidacion_Model_Periodo $periodo)
    {
        $db = Zend_Registry::get('db');
        $conceptos = array();

        //recupero los datos del periodo Mes y Año;
        if (!$periodo)  throw new Liquidacion_Model_Exception("No se encontro el Periodo.");

        //$mes    = $periodo->getDesde()->format('n');
        //$anio   = $periodo->getDesde()->format('Y');

        $fd = $periodo->getDesde()->format('Y-m-d');
        $fh = $periodo->getHasta()->format('Y-m-d');
        //$fh = ($fh) ? $fh : '2199-01-01';

        $sql    = " Select  Id, TipoDeLiquidacionTabla
                    From    LiquidacionesTablas
                    Where   Id = $tabla
                    AND     FechaDesde                      <= '$fd'
                    AND     ifnull(FechaHasta,'2199-01-01') >= '$fh'
                ";

        $R_LT   = $db->fetchRow($sql);
        if (!$R_LT) throw new Liquidacion_Model_Exception("No se encontro la tabla $tabla en dicho periodo.");

        $idTabla    = $R_LT['Id'];
        $tipoTabla  = $R_LT['TipoDeLiquidacionTabla'];

        // Ahora me fijo que si el tipo de tabla es escalar o por rango para recuperar el valor el valor
        $sql = "Select * from LiquidacionesTablasDetalles Where ";
        if($tipoTabla == 1) {
            // Es por Rango
            $sql .= "LiquidacionTabla = $idTabla and InicioRango <= $valor and FinRango > $valor";
        } else {
            // Es Escalar
            $sql .= "LiquidacionTabla = $idTabla and InicioRango = $valor";
        }

        $R_LTD      = $db->fetchRow($sql);
        if (!$R_LTD) {
            return 0;
            // throw new Liquidacion_Model_Exception("No se encontro ningun valor para ese periodo en la tabla $tabla.");
        } else {
            return $R_LTD['Valor'];
        }
    }

    /**
    * Retorna el Id de una tabla segun el $nombre
    *
    * @param string         $nombre     Nombre de la tabla
    * @param obj periodo    $periodo    Periodo sobre el cual buscar
    *
    */
    public static function getIdPorNombre($nombre,$periodo)
    {
        $db         = Zend_Registry::get('db');
        $nombre     = $db->quote($nombre);

        $fechaDesde = $periodo->getDesde()->format('Y-m-d');
        //$fechaHasta = $periodo->getHasta()->format('Y-m-d');
        //$fechaHasta = ($fechaHasta) ? $fechaHasta : '2199-01-01';

        $sql = "SELECT  Id
                FROM    LiquidacionesTablas
                WHERE   Nombre = $nombre
                AND     FechaDesde <= '$fechaDesde'
                AND     ifnull(FechaHasta,'2199-01-01') > '$fechaDesde'";
        $id  = $db->fetchOne($sql);
        return $id;
    }

    /**
     * Inserta un registro
     *
     * @param array $data
     *
     */
    public function insert($data) {
        $data['Nombre'] = str_replace(" ","",ucwords(strtolower($data['Descripcion'])));
        $this->salirSi_existeNombre($data['Nombre'],null);
        return $id = parent::insert($data);
    }

    /**
     * Modifica uno o mas registros
     *
     * @param array $data
     * @param array $where
     *
     */
    public function update($data,$where) {
        if (isset($data['Descripcion'])) {
            $data['Nombre'] = str_replace(" ","",ucwords(strtolower($data['Descripcion'])));
        }
        $reg = $this->fetchAll($where)->current();

        $this->salirSi_existeNombre($data['Nombre'],$reg['Id']);

        parent::update($data,$where);
    }

    /**
     * Borra los registros indicados
     *
     * @param array $where
     *
     */
    public function delete($where)
    {

        try {
            $this->_db->beginTransaction();

            $mLiquidacionTablaDetalle = new Rrhh_Model_DbTable_LiquidacionesTablasDetalles;

            $reg = $this->fetchAll($where);

            if (count($reg)) {
                foreach ($reg as $row) {
                    //no se permite eliminar la liquidacion tabla si tiene detalle

                    $rLiquidacionTablaDetalle = $mLiquidacionTablaDetalle->fetchAll("LiquidacionTabla = $row->Id");
                    if(count($rLiquidacionTablaDetalle)>0){
                        throw new Rad_Db_Table_Exception("No se puede eliminar el registro cuando tiene detalle.");
                    } else {
                        parent::delete('Id ='.$row->Id);
                    }
                }
            }
            $this->_db->commit();
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    /**
     * Revisa si el nombre ingresado es igual a otro superponiendose en el rango de fechas
     *
     * @param nombre   nombre a comparar
     * @return boolean
    */
    public function existeNombre($nombre, $idTabla) {
        $where  = ($idTabla) ? "Nombre = '$nombre' and LiquidacionesTablas.Id <> '".$idTabla."'":"Nombre = '$nombre'";
        //Rad_Log::debug($where);
        $R  = $this->fetchAll($where);
        //Rad_Log::debug($R);
        if (count($R)>0) {
            return true;
        } else {
            return false;
        }

    }


    /**
     * Sale si el nombre es igual a otro superponiendose en el rango de fechas
     *
     * @param $nombre   nombre a comparar
     * @return boolean
    */
    public function salirSi_existeNombre($nombre, $idTabla) {
        //Rad_Log::debug($idTabla);
        if ($this->existeNombre($nombre, $idTabla)) throw new Rad_Db_Table_Exception('El nombre ingresado ya existe.');
    }

    /**
     * Genera todos los detalles de la tabla con un nuevo periodo y valor
     *
     * @param $fecha          fecha del inicio del nuevo periodo
     * @param $idTabla        Id de la tabla
     * @param $valor          valor nuevo
     * @param $porcentaje     es para saber si el valor es un porcentaje o monto fijo
     * @return none
    */
    public function generarDetallesTablas($fecha, $idTabla, $valor, $porcentaje = false)
    {
        if (!$idTabla) throw new Rad_Db_Table_Exception("La tabla no existe");

        try {
            $this->_db->beginTransaction();

            //le resto un dia para la fecha de cierre
            $FechaHasta = new DateTime($fecha);
            $FechaHasta->sub(new DateInterval('P1D'));


            $model_LiquidacionesTablasDetalles = new Rrhh_Model_DbTable_LiquidacionesTablasDetalles;

            //recorro los detalles de la tabla
            $row_LiquidacionesTablasDetalless = $model_LiquidacionesTablasDetalles->fetchAll("LiquidacionTabla = ".$idTabla);

            if (!$row_LiquidacionesTablasDetalless) throw new Rad_Db_Table_Exception("La tabla no tiene detalle");

            // Cierro al dia anterior
            $dataU  = array("FechaHasta" => $FechaHasta->format('Y-m-d'));
            parent::update($dataU, "Id = ".$idTabla);

            $row_LiquidacionesTablas = $this->find($idTabla)->current();

            // Creo el nuevo registro de la tabla
            $dataLT = array(
                'Nombre'                    => $row_LiquidacionesTablas->Nombre,
                'Descripcion'               => $row_LiquidacionesTablas->Descripcion,
                'Convenio'                  => $row_LiquidacionesTablas->Convenio,
                'Grupo'                     => $row_LiquidacionesTablas->Grupo,
                'TipoDeLiquidacionTabla'    => $row_LiquidacionesTablas->TipoDeLiquidacionTabla,
                'FechaDesde'                => $fecha,
                'FechaHasta'                => null
            );
            $idLT= parent::insert($dataLT);

            foreach ($row_LiquidacionesTablasDetalless as $rowTablasDetalles) {
                //calculos los valores de la tabla detalle si viene en pocentaje o valor fijo
                $valor = ($valor) ? $valor:0;
                if($porcentaje){
                    $valorTD = $rowTablasDetalles->Valor + (($rowTablasDetalles->Valor * $valor)/100);
                } else {
                    $valorTD = $rowTablasDetalles->Valor + $valor;
                }
                //inserto el nuevo registro con los valores actualizados desde la fecha
                $dataLTD = array(
                    'LiquidacionTabla'  => $idLT,
                    'Descripcion'       => $rowTablasDetalles->Descripcion,
                    'InicioRango'       => $rowTablasDetalles->InicioRango,
                    'FinRango'          => $rowTablasDetalles->FinRango,
                    'Valor'             => $valorTD
                );
                $idLTD = $model_LiquidacionesTablasDetalles->insert($dataLTD);
            }
            $this->_db->commit();
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }
}