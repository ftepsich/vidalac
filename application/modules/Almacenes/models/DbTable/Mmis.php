<?php

require_once('Rad/Db/Table.php');

/**
 * Mmi
 *
 * @package     Aplicacion
 * @subpackage  Almacenes
 * @class       Almacenes_Model_DbTable_Mmis
 * @extends     Rad_Db_Table
 */
class Almacenes_Model_DbTable_Mmis extends Rad_Db_Table
{
    protected $_gridGroupField = 'Almacen';
    protected $_name = "Mmis";
    protected $_sort = array ('Deposito asc','Almacen asc');
    protected $_validators = array(
        'CantidadOriginal' => array(
            array('GreaterOrEqThan', "{CantidadActual}"),
            'messages' => array('Debe ser mayor que le actual')
        )
    );

    // Inicio  protected $_referenceMap --------------------------------------------------------------------------
    protected $_referenceMap = array(
        'Depositos' => array(
            'columns' => 'Deposito',
            'refTableClass' => 'Base_Model_DbTable_DepositosPropios',
            'refJoinColumns' => array("Direccion"), // De esta relacion queremos traer estos campos por JOIN
            'comboBox' => true, // Armar un combo con esta relacion - Algo mas queres haragan programa algo :P -
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'Direcciones',
            'refColumns' => 'Id'
        ),
        'Almacenes' => array(
            'columns' => 'Almacen',
            'refTableClass' => 'Almacenes_Model_DbTable_Almacenes',
            'refJoinColumns' => array("Descripcion"), // De esta relacion queremos traer estos campos por JOIN
            'comboBox' => true, // Armar un combo con esta relacion - Algo mas queres haragan programa algo :P -
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'Almacenes',
            'refColumns' => 'Id'
        ),
        'Ubicaciones' => array(
            'columns' => 'Ubicacion',
            'refTableClass' => 'Almacenes_Model_DbTable_Ubicaciones',
            'refJoinColumns' => array("Descripcion"), // De esta relacion queremos traer estos campos por JOIN
            'comboBox' => true, // Armar un combo con esta relacion - Algo mas queres haragan programa algo :P -
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'Ubicaciones',
            'refColumns' => 'Id'
        ),
        'UnidadesDeMedidas' => array(
            'columns' => 'UnidadDeMedida',
            'refTableClass' => 'Base_Model_DbTable_UnidadesDeMedidas',
            'refJoinColumns' => array("Descripcion"), // De esta relacion queremos traer estos campos por JOIN
            'comboBox' => true, // Armar un combo con esta relacion - Algo mas queres haragan programa algo :P -
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'UnidadesDeMedidas',
            'refColumns' => 'Id'
        ),
        'TiposDePalets' => array(
            'columns' => 'TipoDePalet',
            'refTableClass' => 'Almacenes_Model_DbTable_TiposDePalets',
            'refJoinColumns' => array("Descripcion"), // De esta relacion queremos traer estos campos por JOIN
            'comboBox' => true, // Armar un combo con esta relacion - Algo mas queres haragan programa algo :P -
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'TiposDePalets',
            'refColumns' => 'Id'
        ),
        'MmisTipos' => array(
            'columns' => 'MmiTipo',
            'refTableClass' => 'Almacenes_Model_DbTable_MmisTipos',
            'refJoinColumns' => array("Descripcion"), // De esta relacion queremos traer estos campos por JOIN
            'comboBox' => true, // Armar un combo con esta relacion - Algo mas queres haragan programa algo :P -
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'MmisTipos',
            'refColumns' => 'Id'
        ),
        'Lotes' => array(
            'columns' => 'Lote',
            'refTableClass' => 'Almacenes_Model_DbTable_Lotes',
            'refJoinColumns' => array('Numero'),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'Lotes',
            'refColumns' => 'Id',
        ),
        'Articulos' => array(
            'columns' => 'Articulo',
            'refTableClass' => 'Base_Model_DbTable_Articulos',
            'refJoinColumns' => array("Descripcion"), // De esta relacion queremos traer estos campos por JOIN
            'comboBox' => true, // Armar un combo con esta relacion - Algo mas queres haragan programa algo :P -
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'Articulos',
            'refColumns' => 'Id'
        ),
        'ArticulosVersiones' => array(
            'columns' => 'ArticuloVersion',
            'refTableClass' => 'Base_Model_DbTable_ArticulosVersiones',
            'refJoinColumns' => array("Descripcion"), // De esta relacion queremos traer estos campos por JOIN
            'comboBox' => true, // Armar un combo con esta relacion - Algo mas queres haragan programa algo :P -
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'ArticulosVersiones',
            'refColumns' => 'Id'
        ),
        'RemitosArticulosSalida' => array(
            'columns' => 'RemitoArticuloSalida',
            'refTableClass' => 'Almacenes_Model_DbTable_RemitosArticulosDeSalidas',
            'refTable' => 'Articulos',
            'refColumns' => 'Id'
        ),
        'RemitosArticulosEntrada' => array(
            'columns' => 'RemitoArticulo',
            'refTableClass' => 'Almacenes_Model_DbTable_RemitosArticulosDeEntradas',
            'refTable' => 'Articulos',
            'refColumns' => 'Id'
        )

    );

    protected $_dependentTables = array("Almacenes_Model_DbTable_MmisMovimientos");

    // fin  protected $_referenceMap -----------------------------------------------------------------------------

    /**
     * Inserta un registro y setea automaticamente el identificador (AA01-YY .. ZZ99-YY)
     *
     * @param array $data
     * @return mixed
     */

    public function insert($data)
    {
        try {
            $this->_db->beginTransaction();
            $data['Identificador'] = $this->_crearIdentificadorMmi($data);

            //Zend_Wildfire_Plugin_FirePhp::send($data, '$data');
            $id = parent::insert($data);

            if($data['RemitoArticulo']){
                $M_RE = new Almacenes_Model_DbTable_RemitosDeEntradas();
                $M_RAE = new Almacenes_Model_DbTable_RemitosArticulosDeEntradas();

                $R_RAE = $M_RAE->find($data['RemitoArticulo'])->current();

                $M_RE->marcarRemitoPaletizadoTotal($R_RAE->Comprobante);
            }

            //logueamos el movimiento
            $data['Id'] = $id;
            $this->_guardarMovimiento($data, 'insert');
            $this->_db->commit();
            return $id;
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    /**
     * Borrar Palets
     * @param type $where
     */
    public function delete($where)
    {
        // Solo permitimos borrar MMis si estos no poseen movimientos en el sistema.
        // Solo pueden tener el movimiento de creacion.
        $rowset = $this->fetchAll($where);
        if ($rowset) {
            // Instancio los movimientos
            $M_MM = new Almacenes_Model_DbTable_MmisMovimientos();
            // Recorro el recorset y veo si alguno tiene movimientos cargados
            foreach ($rowset as $row) {
                $M_MM->salirSi_tieneMovimientos($row);

                if($row['RemitoArticulo']){
                    $M_RE = new Almacenes_Model_DbTable_RemitosDeEntradas();
                    $M_RAE = new Almacenes_Model_DbTable_RemitosArticulosDeEntradas();

                    $R_RAE = $M_RAE->find($row['RemitoArticulo'])->current();

                    $M_RE->desmarcarRemitoPaletizadoTotal($R_RAE->Comprobante);
                }

            }
            parent::delete($where);
        }
        // TODO: PK -> Deberia haber un else para cuando nada cumple con el $Where y no hay que borrar nada ?????
    }

    /**
     * Modificar Palets
     *
     * @param type $data
     * @param type $where
     */
    public function update($data, $where)
    {
        try {
            $this->_db->beginTransaction();

            $registrosAActualizar = $this->fetchAll($where);

            foreach ($registrosAActualizar as $reg) {
                // Si el Mmi se quedo vacio lo saco desaparece del almacen
                // TODO: PK--> dejar disponible palet ????

                $this->salirSi_ubicacionOcupada($data['Ubicacion']);

                //$regOriginal = $this->find($reg['Id'])->current();

                // logueamos el movimiento --> Ojo lo hacemos antes de ver si cerro para evitar movimientos innecesarios.
                $this->_guardarMovimiento($data, 'update', $reg);

                $data2 = $data;
                if (@isset($data['CantidadActual']) && $data['CantidadActual'] == 0) {
                    // Si borro estos valores si le retornan la mercaderia quedan en la nebulosa los palets
                    // $data2['Deposito']       = null;
                    // $data2['Ubicacion']      = null;
                    // $data2['Almacen']        = null;
                    $data2['FechaCierre']    = date('Y-m-d H:i:s');
                }
                // updateo
                parent::update($data2, "Id = ". $reg['Id']);
            }
            $this->_db->commit();
            //return $r;
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    /**
     * Genera el proximo identificador de la forma (AA01-YY .. ZZ99-YY)
     *
     * @param array $data
     * @return mixed
     */
    protected function _crearIdentificadorMmi($data)
    {
        try {
            $this->_db->beginTransaction();

            // Recupero el ultimo insertado
            $rowset = $this->fetchAll(null, 'Id DESC', 1, 0);

            // Desarmo el numero
            $char1  = $rowset->current()->Identificador[0];
            $char2  = $rowset->current()->Identificador[1];
            $number = substr($rowset->current()->Identificador, 2, 2);
            $year   = substr($rowset->current()->Identificador, 5, 2);

            if ((date('y') == $year) && count($rowset)) {
                if (($number + 1) > 99) {
                    $number = '01';
                    if ((chr(ord($char2) + 1)) > 'Z') {
                        $char2 = 'A';
                        if ((chr(ord($char1) + 1)) > 'Z') {
                            throw new Rad_Db_Table_Exception('El valor del identificador es demasiado grande.');
                        } else {
                            $char1++;
                        }
                    } else {
                        $char2++;
                    }
                } else {
                    $number++;
                }
                $newIdentificador = $char1 . $char2 . sprintf('%02d', $number) . '-' . $year;
            } else {
                $newIdentificador = 'AA01-' . date('y');
            }
            $this->_db->commit();
            return $newIdentificador;
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    /**
     *   Logea los movimeientos de un mmi y las modificaciones que sufre
     *
     *   @param array               $data       array con los datos a cambiar en el registro
     *   @param string              $accion
     *   @param Rad_Db_Table_Row    $actual     Row antes de modificarse
     *   @return mixed
     */
    protected function _guardarMovimiento($data, $accion, $actual = null)
    {
        // Si no esta instanciado lo instancio al modelo MmisMovimientos
        if (!$this->mmisMovimientos) {
            $this->mmisMovimientos = new Almacenes_Model_DbTable_MmisMovimientos();
        }

        //$mmiMovimiento = $this->mmisMovimientos->createRow();

        // Seteo los datos comunes en el array que voy a pasar los datos
        $mov['Fecha']               = date('Y-m-d H:i:s');

        $mov['Operacion']           = $this->mmisMovimientos->getProximaOperacion();
        // Veo que accion esta haciendo y completo los datos faltantes y la descripcion

        switch ($accion) {
            case 'insert':
                $mov['Mmi']                 = $data['Id'];
                // Veo si se creo a partir de otro palet o desde un remito
                if ($data['MmiPadre']) {
                    $mov['MmiAccion']       = 2;

                    // -------------------------------------------
                    // ------ Logueo que el padre se partio ------
                    // -------------------------------------------

                        // Debo logear que se partio el Padre para formar el hijo
                        $actualP = $this->find($data['MmiPadre'])->current();
                        $movP['MmiAccion']      = 3;
                        $movP['Mmi']            = $data['MmiPadre'];
                        $movP['Fecha']          = $mov['Fecha'];
                        // Recupero la ultima operacion sobre el mmi (son las dos disminuciones por el partido del mmi)
                        $movP['Operacion']      = $this->mmisMovimientos->getUltimaOperacion($data['MmiPadre']);
                        $movP['CantidadActual'] = $actualP->CantidadActual;
                        // OJO ... se esta logeando al Padre y es el unico caso que como data van los datos del HIJO !!!!!!!!!!!!!!
                        $movP['Descripcion']    = $this->mmisMovimientos->_crearDescripcion($data, $movP['MmiAccion'], $actualP);
                        $id = $this->mmisMovimientos->insert($movP);

                    // ------ Fin del logueo del Padre -----------

                    // Le registro la accion al usuario
                    Rad_Log::user($movP['Descripcion']);

                } else {
                    if (!$data['RemitoArticulo']) {
                        $mov['MmiAccion']       = 16;
                    } else {
                        $mov['MmiAccion']       = 1;
                    }

                }
                $mov['Cantidad']            = $data['CantidadActual'];
                $mov['CantidadActual']      = $data['CantidadActual'];
                $mov['UbicacionDestino']    = $data['Ubicacion'];
                $mov['AlmacenDestino']      = $data['Almacen'];
                // actualP contiene los datos del padre, solo va cuando se parte un palet
                $mov['Descripcion']         = $this->mmisMovimientos->_crearDescripcion($data, $mov['MmiAccion'], $actualP);
                $id = $this->mmisMovimientos->insert($mov);

                // Le registro la accion al usuario
                Rad_Log::user($mov['Descripcion']);

                break;
            case 'update':
                // En el caso que se modifiquen varios de los datos, se crea un movimiento por cada uno
                $mov['Mmi']                 = $actual->Id;
                $mov['CantidadActual']      = $actual->CantidadActual;

                // En el caso que NO venga $actual, recupero el registro antes de updatearse
                if (!$actual) $actual = $this->fetchRow('Id='.$data['Id']);

                // Si no viene y no se localiza el registro tirar error
                if (!$actual) throw new Rad_Db_Table_Exception('No se encuentra el Mmi que intenta modificar.');

                // Tengo que ver si se modifico algun campo que debe ser logeado el movimiento

                // ------------------- Veo si cambio el articulo
                if (array_key_exists('Articulo',$data))
                {
                    $mov['MmiAccion']           = 17;
                    $mov['Articulo']            = $actual->Articulo;
                    $mov['Descripcion']         = $this->mmisMovimientos->_crearDescripcion($data, $mov['MmiAccion'], $actual);
                    $id = $this->mmisMovimientos->insert($mov);

                    // Le registro la accion al usuario
                    Rad_Log::user($mov['Descripcion']);

                    // Lo limpio para ver si se updateo otro campo que no sea uno de los tabulados
                    unset($data['Articulo']);
                }

                // ------------------- Veo si cambio la cantidad Original
                if (array_key_exists('CantidadOriginal',$data) && $data['CantidadOriginal'] != $actual->CantidadOriginal)
                {
                    $mov['MmiAccion']           = 14;
                    $mov['Cantidad']            = abs($actual->CantidadOriginal - $data['CantidadOriginal']);
                    $mov['Descripcion']         = $this->mmisMovimientos->_crearDescripcion($data, $mov['MmiAccion'], $actual);
                    $id = $this->mmisMovimientos->insert($mov);

                    // Le registro la accion al usuario
                    Rad_Log::user($mov['Descripcion']);

                    // Lo limpio para ver si se updateo otro campo que no sea uno de los tabulados
                    unset($data['CantidadOriginal']);
                }

                // ------------------- Veo si cambio la cantidad Actual
                if (array_key_exists('CantidadActual',$data) && $data['CantidadActual'] != $actual->CantidadActual)
                {
                    $mov['MmiAccion']           = 4;
                    $mov['Cantidad']            = abs($actual->CantidadActual - $data['CantidadActual']);
                    $mov['CantidadActual']      = $data['CantidadActual'];
                    $mov['Descripcion']         = $this->mmisMovimientos->_crearDescripcion($data, $mov['MmiAccion'], $actual);
                    $id = $this->mmisMovimientos->insert($mov);

                    // Le registro la accion al usuario
                    Rad_Log::user($mov['Descripcion']);

                    // Lo limpio para ver si se updateo otro campo que no sea uno de los tabulados
                    unset($data['CantidadActual']);
                }

                // ------------------- Veo si cambio la ubicacion
                if ((array_key_exists('Ubicacion',$data) && $data['Ubicacion'] != $actual->Ubicacion) ||
                    (array_key_exists('Almacen',$data)   && $data['Almacen']   != $actual->Almacen) )
                {

                    // Ojo... cada vez que se mueve algo primero se lleva a la zona de intercambio del sistema,
                    // y se setean a null Almacen y Ubicacion
                    // Entonces cuando llega con Almacen null y Ubicacion null significa que se movio a la zona de intercambio.
                    // En ese caso se logea, despues si se vuelve al lugar original hay que borrar el movimiento, si lo lleva
                    // a un lugar diferente updatear el destino y la descripcion.

                    // OJO... aca si tengo que usar isset !!
                    if (isset($data['Almacen']) || isset($data['Ubicacion'])) {

                        // Origen --> Lo DEBO buscar del ultimo movimiento del mmi
                        $sql = "    select  AlmacenDestino      as AlmacenAnterior,
                                            UbicacionDestino    as UbicacionAnterior
                                    from    MmisMovimientos
                                    where   Mmi=".$actual->Id."
                                    and     MmiAccion in (6,1,2)
                                    and     (AlmacenDestino is not null or UbicacionDestino is not null)
                                    order by fecha desc limit 1";
                        $posicionAnterior = $this->_db->fetchRow($sql);

                        if (count($posicionAnterior)) {
                            $mov['AlmacenOrigen']   = $posicionAnterior['AlmacenAnterior'];
                            $mov['UbicacionOrigen'] = $posicionAnterior['UbicacionAnterior'];
                        }
                        $mov['AlmacenDestino']      = $data['Almacen'];
                        $mov['UbicacionDestino']    = $data['Ubicacion'];

                        // Si se movio a otro lugar logueo, sino si el Origen y el destino son iguales no logueo nada
                        if ($mov['AlmacenDestino'] != $mov['AlmacenOrigen'] || $mov['UbicacionDestino'] != $mov['UbicacionOrigen']) {
                            $mov['MmiAccion']           = 6;
                            $mov['Descripcion']         = $this->mmisMovimientos->_crearDescripcion($data, $mov['MmiAccion'], $actual);
                            $id = $this->mmisMovimientos->insert($mov);

                            // Le registro la accion al usuario
                            Rad_Log::user($mov['Descripcion']);
                        }
                    } else {
                        // Si llega aca es que se movio a la zona de intercambio del sistema, no es un movimiento real
                        // por lo tanto no debe logearse.
                    }

                    // Lo limpio para ver si se updateo otro campo que no sea uno de los tabulado
                    unset($data['Almacen']);
                    unset($data['Ubicacion']);
                }

                // ------------------- Veo si se cerro
                if ((array_key_exists('FechaCierre',$data)      && $data['FechaCierre']     != $actual->FechaCierre) ||
                    (array_key_exists('CantidadActual',$data)   && $data['CantidadActual']  == 0)                    )
                {
                    // Veo si fue un despacho ---> se envio en un remito
                    /*
                    if( (array_key_exists('Almacen',$data)      && !$data['Almacen'])      &&
                        (array_key_exists('Ubicacion',$data)    && !$data['Ubicacion'])    &&
                        (array_key_exists('FechaCierre',$data)  && !$data['FechaCierre'])   ) {
                            // 8: Despachado
                            $mov['MmiAccion'] = 8;
                        } else {
                    */
                            if (array_key_exists('FechaCierre',$data) && !$data['FechaCierre']) {
                                $mov['MmiAccion'] = 13;
                            } else {
                                $mov['MmiAccion'] = 7;
                            }
                    /*    } */

                    $mov['Descripcion']         = $this->mmisMovimientos->_crearDescripcion($data, $mov['MmiAccion'], $actual);
                    $mov['CantidadActual']      = $data['CantidadActual'];
                    $id = $this->mmisMovimientos->insert($mov);

                    // Le registro la accion al usuario
                    Rad_Log::user($mov['Descripcion']);

                    // Lo limpio para ver si se updateo otro campo que no sea uno de los tabulados
                    unset($data['FechaCierre']);
                    unset($data['CantidadActual']);
                }

                // ------------------- Veo si se habilito/deshabilito para produccion
                if (array_key_exists('HabilitadoParaProduccion',$data) && $data['HabilitadoParaProduccion'] != $actual->HabilitadoParaProduccion) {
                    $mov['MmiAccion']           = 11;
                    $mov['Descripcion']         = $this->mmisMovimientos->_crearDescripcion($data, $mov['MmiAccion'], $actual);
                    $id = $this->mmisMovimientos->insert($mov);

                    // Le registro la accion al usuario
                    Rad_Log::user($mov['Descripcion']);

                    // Lo limpio para ver si se updateo otro campo que no sea uno de los tabulados
                    unset($data['HabilitadoParaProduccion']);
                }

                // ------------------- Veo si se utilizo en un Remito
                if (array_key_exists('RemitoArticuloSalida',$data) && $data['RemitoArticuloSalida'] != $actual->RemitoArticuloSalida) {

                    if ($data['RemitoArticuloSalida']) {
                        // Asigno
                        $mov['MmiAccion']           = 8;
                    } else {
                        // Desasigno
                        $mov['MmiAccion']           = 15;
                    }

                    $mov['CantidadActual']      = $actual->CantidadActual;
                    $mov['Descripcion']         = $this->mmisMovimientos->_crearDescripcion($data, $mov['MmiAccion'], $actual);
                    $id = $this->mmisMovimientos->insert($mov);

                    // Le registro la accion al usuario
                    Rad_Log::user($mov['Descripcion']);

                    // Lo limpio para ver si se updateo otro campo que no sea uno de los tabulados
                    unset($data['RemitoArticuloSalida']);
                }

                // ------------------- Veo si se cambio la referencia al RemitoArticulo
                if (array_key_exists('RemitoArticulo',$data) && $data['RemitoArticulo'] != $actual->RemitoArticulo) {
                    $mov['MmiAccion']           = 5;
                    $mov['Descripcion']         = $this->mmisMovimientos->_crearDescripcion($data, $mov['MmiAccion'], $actual);
                    $id = $this->mmisMovimientos->insert($mov);

                    // Le registro la accion al usuario
                    Rad_Log::user($mov['Descripcion']);

                    // Lo limpio para ver si se updateo otro campo que no sea uno de los tabulados
                    unset($data['RemitoArticulo']);
                }

                // ------------------- Veo si modifico algun otro dato
                unset($data['Deposito']);
                if (count($data)>0) {

                    $mov['MmiAccion']           = 12;
                    $mov['Descripcion']         = $this->mmisMovimientos->_crearDescripcion($data, $mov['MmiAccion'], $actual);
                    $id = $this->mmisMovimientos->insert($mov);

                    // Le registro la accion al usuario
                    Rad_Log::user($mov['Descripcion']);
                }
                break;
            default:
                new Rad_Db_Table_Exception('No se puede identificar el tipo de operacion que intenta realizar sobre el Mmi.');
                break;
        }
    }

    /**
     *  Mueve todos los mmis asignados a un remito al predeposito
     *
     */
    public function moverPredepositoRemito($idRemito, $predeposito)
    {
        $modelAlmacen = new Almacenes_Model_DbTable_Almacenes();
        $almacen = $modelAlmacen->find($predeposito)->current();

        if (!$almacen) new Rad_Db_Table_Exception('No se encontro el Predeposito');

        if ($almacen->TipoDeAlmacen != 2) new Rad_Db_Table_Exception('El almacen enviado no es un Predeposito');

        $mmis = $this->getIdxRemitoDeSalida($idRemito);

        if ($mmis) {
            $mmis = implode(',', $mmis);
            $this->update(array('Almacen' => $predeposito, 'Ubicacion' => null), "Id in ($mmis)");
        } else {
            throw new Rad_Db_Table_Exception('No se encontro el Remito');
        }
    }

    /**
     *  Retorna un array con los Ids de los mmis que estan asigandos al remito de salida $idRemito
     */
    public function getIdxRemitoDeSalida($idRemito)
    {
        if (is_array($idRemito)) {
            $idRemito = implode(',', $idRemito);
        }
        $mmis = $this->_db->fetchCol("
            select  m.Id
            from    Mmis m
            join    ComprobantesDetalles ra on ra.Id = m.RemitoArticuloSalida and ra.Comprobante in ($idRemito)
        ");
        return $mmis;
    }

    /**
     * Cierra todos los mmis asignados a los remitos de salidas pasados en el array
     *
     * @param int $idRemitos
     *
     */
    public function cerrarMmisRemitosSalidas($idRemitos)
    {
        $ids = $this->getIdxRemitoDeSalida($idRemitos);
        if ($ids) {
            $ids = implode(',', $ids);
            $this->cerrarMmis("Id in ($ids)");
        }
    }

    /**
     * Cierra todos los mmis enviados en el $where
     *
     * @param mixed $where      Remitos a cerrar
     *
     */
    protected function cerrarMmis($where)
    {
        $data = array(
                    'Almacen'       => null,
                    'Ubicacion'     => null,
                    'FechaCierre'   => date('Y-m-d H:i:s')
                );
        $this->update($data,$where);
    }

    public function mmiEnProduccionActiva($mmis) {
        $sql = "    select  count(*) as mmiEnProduccionActiva
                    from    OrdenesDeProduccionesMmis OPM
                    inner join OrdenesDeProduccionesDetalles OPD    on OPM.OrdenDeProduccionDetalle = OPD.Id
                    inner join OrdenesDeProducciones OP             on OP.Id = OPD.OrdenDeProduccion
                    inner join OrdenesDeProduccionesEstados OPE     on OPE.Id = OP.Estado and OPE.EsFinal <> 1
                    where OPM.Mmi = $mmis;  ";

        //throw new Rad_Db_Table_Exception($sql);

        $mmiEnProduccionActiva = $this->_db->fetchOne($sql);

        //throw new Rad_Db_Table_Exception($mmiEnProduccionActiva);

        if ($mmiEnProduccionActiva) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Sale si el mmi esta en una Orden de Produccion activa
     *
     * @param int $mmis Identificador del mmi
     */
    public function salirSi_mmiEnProduccionActiva($mmis)
    {
        if ($this->mmiEnProduccionActiva($mmis)) {
            throw new Rad_Db_Table_Exception("El mmi se ecuentra asignado a una Orden de Produccion activa y no puede moverse hasta que la Produccion se finalice.");
        }
    }

    public function asignarMmiRemitoArticulo($mmis, $remitoArticulo)
    {
        try {
            $mmisRows = $this->find($mmis);

            // Buscamos el Remito Articulo
            $modelRemitoArt = new Almacenes_Model_DbTable_RemitosArticulosDeSalidas();
            $rowRemitoArt   = $modelRemitoArt->find($remitoArticulo)->current();

            // Existe el remito articulo?
            if (!$rowRemitoArt) throw new Rad_Db_Table_Exception('El RemitoArticulo no existe.');


            $this->_db->beginTransaction();

            $remitoArticulo     = $this->_db->quote($remitoArticulo, 'INTEGER');

            $mmis               = implode(',', $mmis);
            $cantidadAsignada   = $this->_db->fetchOne("select sum(CantidadActual) from Mmis where Id in ($mmis) Or RemitoArticuloSalida = $remitoArticulo");
            $cantidadRemito     = $this->_db->fetchOne("select Cantidad from ComprobantesDetalles where Id = $remitoArticulo");

            if ($cantidadAsignada > $cantidadRemito) throw new Rad_Db_Table_Exception('Esta intentando asignar una cantidad mayor del remito.');

            foreach ($mmisRows as $mmi) {
                //Zend_Wildfire_Plugin_FirePhp::send('Asignado');

                //controla que el articulo del remito sea el mismo que el del mmi
                if ($rowRemitoArt->Articulo != $mmi->Articulo) throw new Rad_Db_Table_Exception('El articulo del MMI no corresponde al del item del remito');

                // Verificamos que no este asigando ya a un remito
                if ($mmi->RemitoArticuloSalida) {
                    throw new Rad_Db_Table_Exception('El Mmi ya esta asignado');
                }

                $mmi->RemitoArticuloSalida = $remitoArticulo;
                $mmi->save();
            }
            $this->_db->commit();
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    public function getTrazabilidad($id)
    {
        $db     = $this->_db;
        $id     = $db->quote($id, 'INTEGER');
        $data   = $db->fetchAll("call RecorridoMmi($id)");

        return $data;
    }

    public function cambiarArticuloAMmi($id, $articuloversion)
    {
        $mmiAModificar = $this->find($id)->current();

        if (!$mmiAModificar) throw new Rad_Db_Table_Exception('No se encontro el mmi que intenta modificar');

        if ($mmiAModificar->FechaCierre) throw new Rad_Db_Table_Exception('El mmi esta cerrado. No puede modificarlo!');

        if ($mmiAModificar->RemitoArticuloSalida) throw new Rad_Db_Table_Exception('El mmi esta asignado a un remito. No puede modificarlo!');

        try {
            $this->_db->beginTransaction();

            $model_Articulos = new Base_Model_DbTable_Articulos();

            $reg = $model_Articulos->getEstructuraArbol($mmiAModificar->ArticuloVersion);

            $subarticulo = 0;
            $subarticuloversion = 0;

            foreach ($reg["desglose"] as $row) {
                if($row["ArticuloVersionId"] == $articuloversion){
                    $subarticuloversion = $row["ArticuloVersionId"];
                    $subarticulo        = $row["ArticuloId"];
                    $cantidad           = $row['CantidadTotal'];
                    break;
                }
            }

            if($subarticulo == 0 || $subarticulo == 0) {
                throw new Rad_Db_Table_Exception('El articulo del MMI no corresponde a ningun subarticulo del item del remito');
            } else {
                $data['ArticuloVersion']   = $subarticuloversion;
                $data['Articulo']          = $subarticulo;
                $data['CantidadOriginal']  = 'CantidadOriginal * '.$cantidad;
                $data['CantidadActual']    = 'CantidadActual * '.$cantidad;
                $this->update($data,"Id = $id");
            }

            $this->_db->commit();
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    public function partirMmi($id, $cantidad)
    {
        $mmiAPartir = $this->find($id)->current();

        if (!$mmiAPartir) throw new Rad_Db_Table_Exception('No se encontro el mmi que intenta partir');

        if ($mmiAPartir->FechaCierre || $mmiAPartir->CantidadActual == 0) throw new Rad_Db_Table_Exception('El mmi esta cerrado. No puede partirlo!');

        if ($mmiAPartir->RemitoArticuloSalida) throw new Rad_Db_Table_Exception('El mmi esta asignado a un remito. No puede partirlo!');

        $cantidadActual     = $mmiAPartir->CantidadActual;
        $CantidadOriginal   = $mmiAPartir->CantidadOriginal;
        //vemos que la cantidad del mmi a partir sea mayor que la del mmi que queremos sacar de el
        if ($cantidadActual <= $cantidad) throw new Rad_Db_Table_Exception('Ud. quiere partir el Mmi en porciones mas grandes o iguales que la cantidad actual');

        try {
            $this->_db->beginTransaction();

            // Al padre le saco la cantidad que necesito para crear al hijo (Triste realidad !)
            $data['CantidadActual']     = $cantidadActual - $cantidad;
            $data['CantidadOriginal']   = $CantidadOriginal - $cantidad;

            $u = $this->update($data, "Id = $id");

            // Creo el hijo a partir del padre antes de la modificacion
            $hijo = $mmiAPartir->toArray();
            unset($hijo['Id']);
            unset($hijo['Almacen']);
            unset($hijo['Ubicacion']);

            $hijo['CantidadActual']     = $cantidad;
            $hijo['CantidadOriginal']   = $cantidad;
            $hijo['MmiPadre']           = $id;
            $hijo['FechaIngreso']       = date('Y-m-d H:i:s');
            $idN = $this->insert($hijo);

            $this->_db->commit();
            return $idN;
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    public function cambiarCantidadMmi($id, $cantidad)
    {
        $mmiAModificar = $this->find($id)->current();

        if (!$mmiAModificar) throw new Rad_Db_Table_Exception('No se encontro el mmi que intenta modificar');

        if ($mmiAModificar->FechaCierre) throw new Rad_Db_Table_Exception('El mmi esta cerrado. No puede modificarlo!');

        if ($mmiAModificar->RemitoArticuloSalida) throw new Rad_Db_Table_Exception('El mmi esta asignado a un remito. No puede modificarlo!');

        $CantidadOriginal = $mmiAModificar->CantidadOriginal;

        //vemos que la cantidad original del mmi a modificar sea mayor que la cantidad modificada
        if ($CantidadOriginal < $cantidad) throw new Rad_Db_Table_Exception('Ud. quiere cambiar la cantidad del Mmi en porciones mas grandes que la cantidad original');
        try {
            $this->_db->beginTransaction();

            $data['CantidadActual'] = $cantidad;
            $this->update($data,"Id = $id");
            $this->_db->commit();
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    public function eliminarMmi($id)
    {
        $mmiAEliminar = $this->find($id)->current();

        // Rad_Log::debug($mmiAEliminar->toArray());
        if (!$mmiAEliminar) throw new Rad_Db_Table_Exception('No se encontro el mmi que intenta modificar');

        if ($mmiAEliminar->FechaCierre) throw new Rad_Db_Table_Exception('El mmi esta cerrado. No puede Eliminarlo!');

        if ($mmiAEliminar->RemitoArticuloSalida) throw new Rad_Db_Table_Exception('El mmi esta asignado a un remito. No puede Eliminarlo!');

        try {
            $this->_db->beginTransaction();

            $this->delete("Id = $id");
            $this->_db->commit();
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    /**
     * Quitar mercederia de mmi
     *
     * @param int $mmi              identificador del Mmi
     * @param int $cantidad         cantidad a quitar
     * @param int $unidaddemedida   unidad de medida en que viene la cantidad
     *
     * @return Zend_Db_Table_Row
     */
    public function quitarMercaderiaAMmi($mmi, $cantidad, $unidaddemedida = null)
    {
        try {
            $db = $this->getAdapter();

            $mmi            = $db->quote($mmi, 'INTEGER');
            $unidaddemedida = $db->quote($unidaddemedida, 'INTEGER');

            $rt = $this->_verificarCantidad($cantidad, $unidaddemedida, $mmi);

            $rt['mmi']->CantidadActual -= $rt['cant'];
            $rt['mmi']->save();

            $this->_db->commit();

            return $rt['cant'];
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    /**
     * Agregar mercederia de mmi
     *
     * @param int $mmi             identificador del Mmi
     * @param num $cantidad        cantidad a quitar
     * @param int $unidaddemedida  unidad de medida en que viene la cantidad
     * @param num $limite          limite maximo que puede devolver en unidades de articulo
     *
     * @return numeric o false en caso de exeder el limite
     */
    public function agregarMercaderiaAMmi($mmi, $cantidad, $unidaddemedida = null, $limite = null)
    {
        try {
            $db = $this->getAdapter();

            $mmi            = $db->quote($mmi, 'INTEGER');
            $unidaddemedida = $db->quote($unidaddemedida, 'INTEGER');

            $rt = $this->_verificarCantidad($cantidad, $unidaddemedida, $mmi);

            if ($limite && $limite < $rt['cant']) {
                return false;
            }

            $rt['mmi']->CantidadActual += $rt['cant'];
            $rt['mmi']->FechaCierre    = null;
            $rt['mmi']->save();

            $this->_db->commit();

            return $rt['cant'];
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    protected function _verificarCantidad($cantidad, $unidaddemedida, $mmi)
    {
        $db = $this->getAdapter();

        $M_A = Service_TableManager::get('Base_Model_DbTable_Articulos');

        // Verifico q exista el Mmi
        $R_M = $this->find($mmi)->current();
        if (!$R_M) throw new Rad_Exception('No se encontro el mmi.');

        // Verifico q venga la cantidad
        if ($cantidad <= 0) throw new Rad_Exception('La cantidad debe ser mayor a 0.');

        // Descuento al mmi la cantidad correspondiente de acuerdo a la unidad de medida
        if($unidaddemedida){
            $descontar = ($cantidad / ($M_A->getCantidadProducto($R_M->ArticuloVersion,1,$unidaddemedida)));
            if ($descontar <= 0) throw new Rad_Exception('La cantidad luego de convertir la unidad es '.$descontar);
        } else {
            $descontar = $cantidad;
        }

        $descontar   = $db->quote($descontar, 'DECIMAL(12,4)');

        return array('cant' => $descontar, 'mmi' => $R_M);
    }

    /**
    *   Retorna verdadero si existe un MMI en la ubcacion destino
    */
    public function ubicacionOcupada($Ubicacion)
    {
        if ($Ubicacion) {
            $ocupado = $this->fetchAll(" Mmis.Ubicacion = $Ubicacion ");
            if (count($ocupado) > 0) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * sale si existe un Mmi en una ubicacion
     *
     * @param Rad_Db_Table_Row $row   Un registro de Mmi
     *
     * @return boolean
     */
    public function salirSi_ubicacionOcupada($Ubicacion)
    {
        if ($this->ubicacionOcupada($Ubicacion)) {
            throw new Rad_Db_Table_Exception('La ubicacion destino se encuentra ocupada.');
        }
        return $this;
    }

    public function fetchMmisAbiertos ($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = "FechaCierre is not null";
        $where = $this->_addCondition($where, $condicion);
        return parent:: fetchAll($where, $order, $count, $offset);
    }

    /**
     * Agrega metadatos extra como summary server-side y mmis temporales asignados
     *
     * @param array $return
     * @param mixed $start
     * @param mixed $end
     * @param mixed $sort
     * @param mixed $where
     * @param mixed $fetch
     * @return array
     */
    public function addExtraMetadata($return, $start, $end, $sort, $where, $fetch)
    {

        $summary = array();
        $modelArticulo = new Base_Model_DbTable_Articulos();
        $session = new Zend_Session_Namespace('OrdenesDeProducciones');

        $mmisAsignadosTemporal = array();


        // acomodo los mmis asignados por id de MMI
        foreach ($session->MmisAsignadosTemporal as $odp) {
            foreach ($odp as $idODPDetalle => $mmis) {
                foreach ($mmis as $mmi => $true) {
                    $mmisAsignadosTemporal[$mmi] = $idODPDetalle;
                }
            }
        }

        foreach ($return['rows'] as &$row) {
            // Si tiene Mmis asignados temporal, cuento las cantidades
            $row['AsignadoODPDetalleTemporal'] = @$mmisAsignadosTemporal[$row['Id']];
        }

        return $return;
    }
}