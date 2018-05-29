<?php
require_once('Rad/Db/Table.php');

class Almacenes_Model_DbTable_MmisMovimientos extends Rad_Db_Table
{
    protected $_name = "MmisMovimientos";
    
    protected $_referenceMap    = array(
        'Mmis' => array(
            'columns'           => 'Mmi',
            'refTableClass'     => 'Almacenes_Model_DbTable_Mmis',
            'refJoinColumns'    => array("Identificador"),                     // De esta relacion queremos traer estos campos por JOIN
            'comboBox'          => true,                                     // Armar un combo con esta relacion - Algo mas queres haragan programa algo :P -
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'Mmis',
            'refColumns'        => 'Id'
        ),
        'UbicacionesOrigenes' => array(
            'columns'           => 'UbicacionOrigen',
            'refTableClass'     => 'Almacenes_Model_DbTable_Ubicaciones',
            'refJoinColumns'    => array("Descripcion"),                     // De esta relacion queremos traer estos campos por JOIN
            'comboBox'          => true,                                     // Armar un combo con esta relacion - Algo mas queres haragan programa algo :P -
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'Ubicaciones',
            'refColumns'        => 'Id',
            'comboPageSize'     => 10
        ),
        'AlmacenesOrigenes' => array(
            'columns'           => 'AlmacenOrigen',
            'refTableClass'     => 'Almacenes_Model_DbTable_Almacenes',
            'refJoinColumns'    => array("Descripcion"),                     // De esta relacion queremos traer estos campos por JOIN
            'comboBox'          => true,                                     // Armar un combo con esta relacion - Algo mas queres haragan programa algo :P -
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'Almacenes',
            'refColumns'        => 'Id',
            'comboPageSize'     => 10
        ),
        'UbicacionesDestino' => array(
            'columns'           => 'UbicacionDestino',
            'refTableClass'     => 'Almacenes_Model_DbTable_Ubicaciones',
            'refJoinColumns'    => array("Descripcion"),                     // De esta relacion queremos traer estos campos por JOIN
            'comboBox'          => true,                                     // Armar un combo con esta relacion - Algo mas queres haragan programa algo :P -
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'Ubicaciones',
            'refColumns'        => 'Id',
            'comboPageSize'     => 10
        ),
        'AlmacenesDestino' => array(
            'columns'           => 'AlmacenDestino',
            'refTableClass'     => 'Almacenes_Model_DbTable_Almacenes',
            'refJoinColumns'    => array("Descripcion"),                     // De esta relacion queremos traer estos campos por JOIN
            'comboBox'          => true,                                     // Armar un combo con esta relacion - Algo mas queres haragan programa algo :P -
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'Almacenes',
            'refColumns'        => 'Id',
            'comboPageSize'     => 10
        )
    );

    public function init()     {
        
        $this-> _defaultSource = self::DEFAULT_CLASS;
        
        
        $this-> _defaultValues = array (
            'Fecha'     => date('Y-m-d H:i:s'),
            'Cantidad'  => '0'
        );

        parent::init();
    }
    
    /**
    *   Retorna el hostirial completo de la mercaderia de un Mmi. (incluye la de los mmis padres)
    */ 
    public function getMovimientos($idMmi)
    {
        //ESPERAR A Q PABLO HAGA EL STORE
    }

    /**
    *   Retorna verdadero si tiene un movimiento a mas del de creacion
    *   Obs. el movimiento de creacion se genera junto con el insert del Mmi
    */
    public function tieneMovimientos($Mmi) 
    {
        $movimientos = $this->fetchAll(" Mmi = $Mmi and MmiAccion not in  (1,2)");
        if (count($movimientos) > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Crea una descripcion de la accion que se esta realizando sobre un mmi 
     *
     * @param array             $data       Valores a modificar en el update
     * @param int               $accion     Accion que se esta logeando
     * @param Rad_Db_Table_Row  $anterior   Un registro de Mmi con el valor antes del update
     *
     * @return string
     */
    public function _crearDescripcion($data, $accion, $anterior) {

        $identificador = $anterior->Identificador;

        //Rad_Log::debug("accion: $accion");

        switch ($accion) {
            case 1:
                // ------------- Crear MMi desde remito 
                $sql        = " select  fNumeroCompleto(CD.Comprobante,'G') COLLATE utf8_general_ci
                                from    ComprobantesDetalles CD
                                where   CD.Id = ".$data['RemitoArticulo'];
                $nroRemito  = $this->_db->fetchOne($sql);
                $d          = "Mmi ".$data['Identificador']." creado desde el remito $nroRemito.";
                break;
            case 2:
                // ------------- Crear MMi desde otro MMi
                $mmiPadre   = $this->_db->fetchOne("Select Identificador from Mmis where Id = ".$data['MmiPadre']);
                $d          = "Mmi ".$data['Identificador']." creado desde el Mmi $mmiPadre.";
                break;
            case 3:
                // ------------- Partir MMi 
                // $data trae los datos del hijo, $anterior trae los del padre
                $regHijo    = $data;
                $regPadre   = $anterior;
                $d          = "Mmi ".$regPadre->Identificador." partido, generando el Mmi". $regHijo['Identificador'];
                break;
            case 4:
                // ------------- Modificar cantidad de un mmi 
                // Veo si incremento o decremento
                if ($anterior->CantidadActual > $data['CantidadActual']) {
                    $acc            = "decremento";
                    $CantidadModif  = $anterior->CantidadActual - $data['CantidadActual'];
                } else {
                    $acc            = "incremento";
                    $CantidadModif  = $data['CantidadActual'] - $anterior->CantidadActual;
                }
                $um         = $this->_db->fetchOne("select Descripcion from UnidadesDeMedidas where Id=".$anterior->UnidadDeMedida);
                $d          = "Mmi $identificador se $acc en ".$CantidadModif." $um quedando con ".$data['CantidadActual']." $um.";
                break;
            case 5:
                // ------------- Modificar Articulo o Remito de origen de un MMI
                // Debo ver si el articulo del nuevo remito es el mismo que el del anterior
                $artAnt     = $this->_db->fetchAll("Select Articulo from ComprobantesDetalles where Id = ".$anterior->RemitoArticulo);
                $artNuevo   = $this->_db->fetchAll("Select Articulo from ComprobantesDetalles where Id = ".$data['RemitoArticulo']);
                if ($artNuevo != $artAnt) {
                    // Se cambio el articulo 
                } else {
                    // Se cambio el RemitoArticulo
                }
                break;
            case 6:
                // ------------- Mover un MMI
                // Debo ver que tengo tanto en el anterior como en el data para poner el orgen y el destino
                
                // Origen --> Lo DEBO buscar del ultimo movimiento del mmi
                $sql = "    select  ifnull(A.Descripcion,'')       as AlmacenAnterior, 
                                    ifnull(U.Descripcion,'')       as UbicacionAnterior
                            from    MmisMovimientos MM
                            left join Almacenes A on A.Id = MM.AlmacenDestino
                            left join Ubicaciones U on U.Id = MM.UbicacionDestino
                            where   Mmi=".$anterior->Id."
                            and     MmiAccion in (6,1,2) 
                            and     (AlmacenDestino is not null or UbicacionDestino is not null) 
                            order by fecha desc limit 1";
                $posicionAnterior = $this->_db->fetchRow($sql);

                if (count($posicionAnterior)) {
                    $AlmacenOrigen      = $posicionAnterior['AlmacenAnterior'];
                    $UbicacionOrigen    = $posicionAnterior['UbicacionAnterior'];
                }
                
                // Formateo el origen
                if ($AlmacenOrigen && $UbicacionOrigen) {
                    $Origen = " de $AlmacenOrigen $UbicacionOrigen";
                } else {
                    $Origen = " de $AlmacenOrigen$UbicacionOrigen";
                }

                // Destino  --> viene en el $data (Data trae la info despues del movimiento)
                if (isset($data['Almacen']) && $data['Almacen']) {
                    $AlmacenDestino     = $this->_db->fetchOne("select ifnull(Descripcion,'') from Almacenes where Id=".$data['Almacen']);
                }
                if (isset($data['Ubicacion']) && $data['Ubicacion']) {
                    $UbicacionDestino   = $this->_db->fetchOne("select ifnull(Descripcion,'') from Ubicaciones where Id=".$data['Ubicacion']);
                }
                
                // Formateo el destino
                if ($AlmacenDestino && $UbicacionDestino) {
                    $Destino = " a $AlmacenDestino $UbicacionDestino";
                } else {
                    $Destino = " a $AlmacenDestino$UbicacionDestino";
                }                

                $d          = "Mmi $identificador movido".$Origen.$Destino.".";
                break;
            case 7:
                // ------------- Cerrar un MMI
                $d          = "Mmi $identificador cerrado.";
                break;
            case 8:
                $sql        = " select  fNumeroCompleto(CD.Comprobante,'') COLLATE utf8_general_ci 
                                from    ComprobantesDetalles CD 
                                where   CD.Id = ".$data['RemitoArticuloSalida'];
                $nroRemito  = $this->_db->fetchOne($sql);
                $d          = "Mmi $identificador asignado al remito $nroRemito.";
                break;
            case 9:
                $d          = "Mmi $identificador movido a produccion.";
                break;
            case 10:
                $d          = "Mmi $identificador se el incorpora el Mmi ";
                break;
            case 11:
                if($data['HabilitadoParaProduccion']) {
                    $acc    = "habilitado";
                } else {
                    $acc    = "deshabilitado";
                }
                $d          = "Mmi $identificador $acc para produccion.";
                break;
            case 12:
                $d          = "Mmi $identificador se le han modificado datos no relevantes.";
                break;
            case 13:
                $d          = "Mmi $identificador se abrio nuevamente.";
                break;
            case 14:
                // Veo si incremento o decremento
                if ($anterior->CantidadOriginal > $data['CantidadOriginal']) {
                    $acc            = "decremento";
                    $CantidadModif  = $anterior->CantidadOriginal - $data['CantidadOriginal'];
                } else {
                    $acc            = "incremento";
                    $CantidadModif  = $anterior->CantidadOriginal + $data['CantidadOriginal'];
                }
                $um     = $this->_db->fetchOne("select Descripcion from UnidadesDeMedidas where Id=".$anterior->UnidadDeMedida);
                $d      = "Mmi $identificador se $acc su cantidad Original en ".$CantidadModif." $um quedando en ".$data['CantidadOriginal']." $um.";
                break;
            case 15:
                $sql        = " select  fNumeroCompleto(CD.Comprobante,'') COLLATE utf8_general_ci 
                                from    ComprobantesDetalles CD 
                                where   CD.Id = ".$anterior->RemitoArticuloSalida;
                $nroRemito  = $this->_db->fetchOne($sql);
                $d          = "Mmi $identificador desasignado del remito $nroRemito.";
                break;
            case 16:
                $d = 'Mmi creado en Producción';
                break;
            case 17:
                $subArticulo     = $this->_db->fetchOne("select Descripcion from Articulos where Id=".$data['Articulo']);
                $articuloOrginal = $this->_db->fetchOne("select Descripcion from Articulos where Id=".$anterior->Articulo);

                $d = 'Mmi Articulo de $articuloOrginal convertido en subarticulo $subArticulo';
                break;
            default:
                $d = 'Descripcion no detallada.';
                break;
        }
        return $d;
    }

    /**
     * Retorna el proximo numero de operacion para un conjunto de acciones sobre un Mmi
     * Se supone que esta funcion debe llamarse desde dentro de una transaccion
     *
     * @return int
     */
    public function getProximaOperacion() {
        $nro = $this->_db->fetchOne("select ifnull(max(operacion),0) from MmisMovimientos;");
        return $nro+1;
    }

    /**
     * Retorna la ultima operacion que se realizo sobre un Mmi
     * Se supone que esta funcion debe llamarse desde dentro de una transaccion
     *
     * @return int
     */
    public function getUltimaOperacion($idMMI) {
        $nro = $this->_db->fetchOne("select ifnull(max(operacion),1) from MmisMovimientos where Mmi = $idMMI;");
        return $nro;        
    }


    /**
     * sale si el el Mmi tiene logeado movimientos
     *
     * @param Rad_Db_Table_Row $row   Un registro de Mmi
     * 
     * @return boolean
     */
    public function salirSi_tieneMovimientos($row)
    {
        if ($this->tieneMovimientos($row->Id)) {
            throw new Rad_Db_Table_Exception('El palet: '.$row->Identificador.' posee movimientos y no se permite la operacion que intenta realizar.');
        }
        return $this;
    }

    public function fetchMmisMovimientosDelDia($where = null, $order = null, $count = null, $offset = null)
    {
        $fecha      = date("Y-m-d");
        $condicion  = "MmisMovimientos.Fecha >= '".$fecha."'";
        $where = $this->_addCondition($where, $condicion);
        return self::fetchAll($where, $order, $count, $offset);
    }
}