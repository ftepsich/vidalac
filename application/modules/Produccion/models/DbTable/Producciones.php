<?php

/**
 * Produccion_Model_DbTable_Producciones
 *
 * @package     Aplicacion
 * @subpackage 	Produccion
 * @class       Produccion_Model_DbTable_Producciones
 * @extends     Rad_Db_Table
 * @copyright   SmartSoftware Argentina 2010
 * @author      Martin Santangelo
 */
class Produccion_Model_DbTable_Producciones extends Rad_Db_Table
{

    protected $_name = 'Producciones';
    protected $_referenceMap = array(
        'OrdenesDeProducciones' => array(
            'columns' => 'OrdenDeProduccion',
            'refTableClass' => 'Produccion_Model_DbTable_OrdenesDeProducciones',
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'OrdenesDeProducciones',
            'refColumns' => 'Id',
        ),
        'ProduccionesMotivosDeFinalizaciones' => array(
            'columns' => 'MotivoDeFinalizacion',
            'refTableClass' => 'Produccion_Model_DbTable_ProduccionesMotivosDeFinalizaciones',
            'refJoinColumns' => array('Descripcion'),
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'ProduccionesMotivosDeFinalizaciones',
            'refColumns' => 'Id',
        )
    );
    protected $_dependentTables = array('Produccion_Model_DbTable_LineasDeProduccionesPersonas');

    /**
     * Asocia los empleados a una produccion
     *
     * @param int $idProduccion	identificador de la Produccion
     * @param int $idEmpleado	identificador del Empleado
     * @param int $idActividad	identificador de la Actividad
     *
     * @return Zend_Db_Table_Row
     */
    public function asociarEmpleados($idProduccion, $idEmpleado, $idActividad)
    {
        $db = $this->getAdapter();
        $idProduccion = $db->quote($idProduccion, 'INTEGER');
        $idEmpleado = $db->quote($idEmpleado, 'INTEGER');
        $idActividad = $db->quote($idActividad, 'INTEGER');

        $M_LPP = new Produccion_Model_DbTable_LineasDeProduccionesPersonas();

        // Verifico q exista la linea de tiempo
        $R_P = $this->find($idProduccion)->current();

        if (!$R_P)
            throw new Rad_Exception('No se encontro la linea de tiempo.');

        // Verifico q exista el empleado
        $M_E = new Base_Model_DbTable_Empleados();

        $R_E = $M_E->find($idEmpleado)->current();

        if (!count($R_E))
            throw new Rad_Exception('No se encontro el empleado.');

        // Verifico q exista la actividad
        $M_A = new Produccion_Model_DbTable_Actividades();

        $R_A = $M_A->find($idActividad)->current();

        if (!$R_A)
            throw new Rad_Exception('No se encontro la actividad.');

        // Creamos un registro
        $row = $M_LPP->createRow();
        $row->Produccion = $idProduccion;
        $row->Persona = $idEmpleado;
        $row->Actividad = $idActividad;
        $row->save();
    }

    /**
     * borra los empleados asociados a una produccion
     *
     * @param int $idProduccion	identificador de la Produccion
     * @param int $idActividad	identificador de la Actividad
     *
     * @return Zend_Db_Table_Row
     */
    public function desasociarEmpleados($idProduccion, $idActividad)
    {
        $db = $this->getAdapter();
        $idProduccion = $db->quote($idProduccion, 'INTEGER');
        $idActividad = $db->quote($idActividad, 'INTEGER');

        $M_LPP = new Produccion_Model_DbTable_LineasDeProduccionesPersonas();

        // Verifico q exista la linea de tiempo
        $M_P = new Produccion_Model_DbTable_Producciones();

        $R_P = $M_P->find($idProduccion)->current();

        if (!$R_P)
            throw new Rad_Exception('No se encontro la linea de tiempo.');

        // Verifico q exista la actividad
        $M_A = new Produccion_Model_DbTable_Actividades();

        $R_A = $M_A->find($idActividad)->current();

        if (!$R_A)
            throw new Rad_Exception('No se encontro la actividad.');

        // borro todos los registros con la prod. y act. pasada como parametros
        $where = "Produccion = $idProduccion And Actividad = $idActividad";
        $M_LPP->delete($where);
    }

    /**
     * crea una linea de tiempo en caso de q no exista
     *
     * @param int $idOrdenDeProduccion	identificador de la Produccion
     *
     * @return Zend_Db_Table_Row
     */
    public function iniciarLineaDeTiempo($idOrdenDeProduccion)
    {
        // Verifico q exista la orden de produccion
        $M_ODP = Service_TableManager::get('Produccion_Model_DbTable_OrdenesDeProducciones');

        $idOrdenDeProduccion = $this->getAdapter()->quote($idOrdenDeProduccion, 'INTEGER');

        $orden = $M_ODP->find($idOrdenDeProduccion)->current();

        if (!$orden)
            throw new Rad_Exception('No se encontro la Orden de Produccion.');


        // Si Detenida o aceptada tiramos una excepcion
        if ($orden->Estado != 2 && $orden->Estado != 6 && $orden->Estado != 4) {
            $row = $this->fetchRow("OrdenDeProduccion = $idOrdenDeProduccion", "Id Desc");
            return $row->Id;
        }
        // Ya hay una linea de tiempo abierta?
        $row = $this->fetchRow("OrdenDeProduccion = $idOrdenDeProduccion And Final is null");

        if ($row) {
            return $row->Id;
        }

        // Sino creamos una
        $row = $this->createRow();
        $row->Comienzo = date('Y-m-d H:i:s');
        $row->OrdenDeProduccion = $idOrdenDeProduccion;
        $id = $row->save();
        return $id;
    }

    /**
     * crea una linea de tiempo en caso de q no exista
     *
     * @param int $idProduccion	identificador de la Produccion
     *
     * @return Zend_Db_Table_Row
     */
    public function iniciarProducccion($iProduccion)
    {
        try {
            $this->_db->beginTransaction();
            $idProduccion = $this->_db->quote($iProduccion, 'INTEGER');

            // Verifico q exista la produccion
            $R_P = $this->find($idProduccion)->current();

            if (!$R_P) {
                throw new Rad_Exception('No se encontro la Produccion.');
            }

            // Verifico q exista la orden de produccion
            $M_ODP = Service_TableManager::get('Produccion_Model_DbTable_OrdenesDeProducciones');

            $R_ODP = $M_ODP->find($R_P->OrdenDeProduccion)->current();

            // Controlo q la orden de Produccion este en estado Aceptada o Detenida
            if ($this->estaDetenidaOAceptada($iProduccion)) {
                $R_P->Comienzo = date("Y-m-d H:i:s");
                $R_P->setReadOnly(false);
                $R_P->save();

                $M_ODP->cambiarEstado($R_ODP->Id, 4);
            }

            $this->_db->commit();
            return true;
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    /**
     * controla que la orden de produccion este aceptada o detenida
     *
     * @param int $idProduccion	identificador de la Produccion
     *
     * @return Zend_Db_Table_Row
     */
    public function estaDetenidaOAceptada($idProduccion)
    {
        $M_ODP = Service_TableManager::get('Produccion_Model_DbTable_OrdenesDeProducciones');
        $R_P = $this->find($idProduccion)->current();

        if (!$R_P)
            throw new Rad_Db_Table_Exception("No se localiza la Produccion.");

        //TODO: Se puede usar un parent Row aca
        $R_ODP = $M_ODP->find($R_P->OrdenDeProduccion)->current();

        if (!$R_ODP)
            throw new Rad_Db_Table_Exception("No se localiza la Orden de Produccion.");

        if ($R_ODP->Estado == 2 || $R_ODP->Estado == 6) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 	Genera los mmis de una produccion dada.
     *
     * 	@param Int $idProduccion
     * 	@param Int $cantidadarticulos
     * 	@param Int $cantidadporpalet
     *  @param Int $tipoPalet
     *
     */
    public function generarMmiProduccion($idProduccion, $cantidadarticulos, $cantidadporpalet, $tipoPalet)
    {
        try {
            $db = $this->getAdapter();
            $db->beginTransaction();

            $idProduccion = $db->quote($idProduccion, 'INTEGER');
            $tipoPalet = $db->quote($tipoPalet, 'INTEGER');

            $M_M = new Almacenes_Model_DbTable_Mmis();
            $M_TP = new Almacenes_Model_DbTable_TiposDePalets();
            $M_PMM = new Produccion_Model_DbTable_ProduccionesMmisMovimientos();

            // Verifico q exista la produccion
            $R_P = $this->find($idProduccion)->current();
            if (!$R_P)
                throw new Rad_Exception('No se encontro la linea de tiempo.');

            // Verifico que la orden este en estado de producion
            $R_OP = $R_P->findParentRow('Produccion_Model_DbTable_OrdenesDeProducciones');
            if ($R_OP->Estado != 4) {
                throw new Rad_Exception('Este orden no puede modificarse');
            }

            // Verifico que la produccion este abierta (Martin)
            if ($R_P->Final)
                throw new Rad_Exception('Este turno esta finalizado');


            // Verifico q venga la cantidad de articulos
            if ($cantidadarticulos <= 0)
                throw new Rad_Exception('La cantidad de articulo debe ser mayor a 0.');

            // Verifico q venga la cantidad por palet
            if ($cantidadporpalet <= 0)
                throw new Rad_Exception('La cantidad por palet debe ser mayor a 0.');

            // Verifico q exista el tipo de palet
            $R_TP = $M_TP->find($tipoPalet)->current();
            if (!$R_TP)
                throw new Rad_Exception('No se encontro el tipo de palet.');

            //-------------------------------------------------------------------------------
            //-------------------------------------------------------------------------------
            //--- Verifico q el ultimo mmi de la misma Orden de Produccion este completo ----
            //-------------------------------------------------------------------------------
            //-------------------------------------------------------------------------------
            // Busco el ultimo Mmi generado de la produccion
            $M_OP = Service_TableManager::get('Produccion_Model_DbTable_OrdenesDeProducciones');

            // Verifico q exista la Orden De Produccion
            $R_OP = $M_OP->find($R_P->OrdenDeProduccion)->current();

            if (!$R_OP)
                throw new Rad_Exception('No se encontro la orden de produccion.');

            $ultimommi = $M_OP->devolverMmisGenerados($R_P->OrdenDeProduccion, 1, "Mmis.Id desc");

            if ($ultimommi) $ultimommi = $ultimommi[0];

            // Completar si se desea y no esta completo el ultimo mmi generado de esa orden de produccion
            if ($ultimommi) {
                if ($ultimommi['CantidadActual'] < $cantidadporpalet) {
                    // Preguntar por confirmacion si quiere completar el palet
                    if (Rad_Confirm::confirm('El ultimo palet generado no esta completo.<BR>¿Desea Completarlo?', __FILE__ . __LINE__, array('includeCancel' => true)) == 'yes') {
                        $cantidadtemp = ($cantidadporpalet - $ultimommi['CantidadActual']);
                        $R_PMM = $M_PMM->createRow();

                        if ($cantidadtemp <= $cantidadarticulos) {
                            $ultimommi['CantidadActual'] = $cantidadporpalet;
                            //guardo el movimiento en Producciones Mmis Movimientos
                            $cant = $cantidadtemp;

                            $cantidadarticulos -= $cantidadtemp;
                        } else {
                            $ultimommi['CantidadActual'] += $cantidadarticulos;

                            //guardo el movimiento en Producciones Mmis Movimientos
                            $cant = $cantidadarticulos;

                            $cantidadarticulos = 0;
                        }
                        $ultimommi['CantidadOriginal'] = $ultimommi['CantidadActual'];

                        $M_M->update($ultimommi, "Id = {$ultimommi['Id']}");

                        $M_PMM->registratMovimientoDeMmiEnProduccion($idProduccion, $ultimommi['Id'], $cant, 2);
                    }
                }
            }


            //-------------------------------------------------------------------------------
            //-------------------------------------------------------------------------------
            //------------------------ Creo Mmi por cantidad de palet -----------------------
            //-------------------------------------------------------------------------------
            //-------------------------------------------------------------------------------
            // Recupero el articulo de la orden de Produccion para completar campos del Mmi
            $M_A = new Base_Model_DbTable_Articulos();
            $R_A = $M_A->find($R_OP->Articulo)->current();

            if (!$R_A)
                throw new Rad_Exception('No se encontro el articulo de la orden de produccion para generar el Mmi.');

            // // Recupero el articulo de la orden de Produccion para completar campos del Mmi
            // $M_A = new Base_Model_DbTable_Articulos();
            // $R_A = $M_A->find($R_OP->Articulo)->current();
            // if (!$R_A)
            //     throw new Rad_Exception('No se encontro el articulo de la orden de produccion para generar el Mmi.');

            // Recupero la Linea de produccion de la orden de Produccion para completar campos del Mmi
            $M_LP = new Produccion_Model_DbTable_LineasDeProducciones();
            $R_LP = $M_LP->find($R_OP->LineaDeProduccion)->current();

            if (!$R_LP) {
                throw new Rad_Exception('No se encontro la linea de produccion de la orden de produccion para generar el Mmi.');
            }

            // Recupero el Deposito
            $R_Almacen = $R_LP->findParentRow('Almacenes_Model_DbTable_Almacenes');


            // Intancio el Modelo ProduccionesMmis para relacionar los Mmis generados a cada produccion
            $M_PM = new Produccion_Model_DbTable_ProduccionesMmis();

            while ($cantidadarticulos) {
                if ($cantidadarticulos >= $cantidadporpalet) {
                    $cantidad = $cantidadporpalet;
                    $cantidadarticulos -= $cantidadporpalet;
                } else {
                    $cantidad = $cantidadarticulos;
                    $cantidadarticulos = 0;
                }

                $M_R                           = $M_M->createRow();
                $M_R->Almacen                  = $R_LP->Interdeposito;
                $M_R->CantidadActual           = $cantidad;
                $M_R->CantidadOriginal         = $cantidad;
                $M_R->UnidadDeMedida           = $R_A->UnidadDeMedida;
                $M_R->Articulo                 = $R_A->Id;
                $M_R->Descripcion              = 'Mmi de la OP: ' . $R_OP->Id . ' ,P: ' . $idProduccion;
                $M_R->FechaIngreso             = date("Y-m-d H:i:s");
                $M_R->HabilitadoParaProduccion = 0;
                $M_R->ParaFason                = 0;
                $M_R->ArticuloVersion          = $R_OP->ArticuloVersion;
                $M_R->Lote                     = $R_OP->Lote;
                $M_R->Deposito                 = $R_Almacen->Deposito;
                $M_R->MmiTipo                  = 2;
                $M_R->Ubicacion                = null;
                $M_R->TipoDePalet              = $tipoPalet;

                $id = $M_R->save();

                $R_PM = $M_PM->createRow();
                $R_PM->Produccion = $idProduccion;
                $R_PM->Mmi = $id;
                $R_PM->save();

                //guardo el movimiento en Producciones Mmis Movimientos
                $M_PMM->registratMovimientoDeMmiEnProduccion($idProduccion, $id, $cantidad, 2);
            }

            $this->_db->commit();
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    public function detenerProduccion($id, $motivo, $comentario)
    {
        try {
            $this->_db->beginTransaction();

            $id = $this->_db->quote($id, 'INTEGER');
            $motivo = $this->_db->quote($motivo, 'INTEGER');
            $comentario = $this->_db->quote($comentario);
            // Verifico q exista la produccion
            $R_P = $this->find($id)->current();


            if (!$R_P) {
                throw new Rad_Exception('No se encontro la produccion que intenta cerrar.');
            }
            // Verificamos q no este cerrada
            if ($R_P->Final) {
                throw new Rad_Exception('Esta producion ya esta Detenida');
            }
            // Verificamos que la orden de produccion este en estado "Produciendo"
            $M_OP = Service_TableManager::get('Produccion_Model_DbTable_OrdenesDeProducciones');


            $R_OP = $M_OP->find($R_P->OrdenDeProduccion)->current();
            // Jamas se deveria dar pero por las dudas...
            if (!$R_OP->Estado == 4) {
                throw new Rad_Exception('Esta orden no esta en el estado correcto.<br>Comuniquese con el soporte tecnico');
            }

            // Cerramos la produccion
            $R_P->Final                   = date("Y-m-d H:i:s");
            $R_P->setReadOnly(false);
            $R_P->MotivoDeFinalizacion    = $motivo;
            $R_P->DescripcionFinalizacion = $comentario;
            $R_P->save();
            // Marcamos la orden como detenida
            $M_OP->cambiarEstado($R_P->OrdenDeProduccion, 6);

            Rad_Log::user("Detuvo la producción: $R_P->OrdenDeProduccion");

            $this->_db->commit();
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    public function finalizarProduccion($id)
    {
        try {
            $this->_db->beginTransaction();

            $id = $this->_db->quote($id, 'INTEGER');
            // Verifico q exista la produccion
            $R_P = $this->find($id)->current();


            if (!$R_P) {
                throw new Rad_Exception('No se encontro la produccion que intenta finalizar.');
            }
            // Verificamos q no este cerrado el turno
            if ($R_P->Final) {
                throw new Rad_Exception('Esta producion ya esta Finalizada');
            }
            // Verificamos que la orden de produccion este en estado "Produciendo"
            $M_OP = Service_TableManager::get('Produccion_Model_DbTable_OrdenesDeProducciones');
            $R_OP = $M_OP->find($R_P->OrdenDeProduccion)->current();

            // Obtenemos lo producido hasta el momento
            $producidoHastaAhora = $M_OP->devolverCantidadProducida($R_P->OrdenDeProduccion);

            if ($R_OP->Cantidad > $producidoHastaAhora) {
                $confirm = Rad_Confirm::confirm(
                                "La orden de producción indica una cantidad de $R_OP->Cantidad y solo se produjeron " . (float) $producidoHastaAhora . "<br>
                     Esta seguro que quiere finalizarla?", __DIR__ . __FILE__ . __LINE__, array('includeCancel' => false)
                );

                // No deberia pasar jamas pero....
                if ($confirm != 'yes') {
                    throw new Rad_Exception('Estado Incorrecto, comuniquese con el servicio tecnico');
                }
            }

            // Jamas se deberia dar pero por las dudas...
            if (!$R_OP->Estado == 4) {
                throw new Rad_Exception('Esta orden no esta en el estado correcto.<br>Comuniquese con el soporte tecnico');
            }

            $this->moverMmisATemporal($R_P->OrdenDeProduccion);

            // Cerramos la produccion
            $R_P->Final = date("Y-m-d H:i:s");
            $R_P->setReadOnly(false);
            $R_P->save();
            // Marcamos la orden como detenida
            $M_OP->cambiarEstado($R_P->OrdenDeProduccion, 7);

            Rad_Log::user("Finalizo la produccion: $R_P->OrdenDeProduccion turno: $R_P->Id");

            $this->_db->commit();
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    /**
     * Se usa para aumentar la cantidad de los palets de materia prima
     * @param type $idmmi
     * @param type $cantidad
     * @param type $unidaddemedida
     * @param type $idProduccion
     * @return type
     */
    public function retornarMercaderiaAMmi($idmmi, $cantidad, $unidaddemedida = null, $idProduccion = null)
    {
        //TODO: Verificar que este usuario tiene una produccion abierta con este mmi
        try {
            $this->getAdapter()->beginTransaction();
            $idmmi = $this->getAdapter()->quote($idmmi, 'INTEGER');
            $idProduccion = $this->getAdapter()->quote($idProduccion, 'INTEGER');

            $M_M = new Almacenes_Model_DbTable_Mmis();
            $M_OPM = new Produccion_Model_DbTable_OrdenesDeProduccionesMmis();
            $M_PMM = new Produccion_Model_DbTable_ProduccionesMmisMovimientos();

            // Verifico q exista la orden de produccion y que este en un estado correcto
            $this->_checkEstadoOrden($idProduccion);

            // Verifico q venga la cantidad
            if ($cantidad <= 0)
                throw new Rad_Exception('La cantidad debe ser mayor a 0.');


            // es valido el mmi
            $this->_isValidMmi($idmmi, $M_M, $M_OPM);

            // Vemos que la cantidad que intenta retornar no sea mayor que la utilizada en esta produccion por este turno
            $cantidadUtilizada = $M_PMM->retornarCantidadUtilizadaDeMateriaPrima($idmmi, $idProduccion);

            $cantidad = $M_M->agregarMercaderiaAMmi($idmmi, $cantidad, $unidaddemedida, $cantidadUtilizada);
            if ($cantidad === false) {
                throw new Rad_Exception('La cantidad que intenta retornar al pallet es mayor que la que utilizo');
            }

            $M_PMM->registratMovimientoDeMmiEnProduccion($idProduccion, $idmmi, $cantidad, 1);

            $this->getAdapter()->commit();
            return true;
        } catch (Exception $e) {
            $this->getAdapter()->rollBack();
            throw $e;
        }
    }



     /**
     * Quita toda la materia prima de un Mmi, lo deja vacio.
     * @param type $idmmi
     * @param type $idProduccion
     * @return boolean
     */
    public function quitarTodaMercaderiaAMmi($ids, $idProduccion = null)
    {
        $idMmis = explode(',',$ids);
        //TODO: Verificar que este usuario tiene una produccion abierta con este mmi
        try {

            $db = $this->getAdapter();
            $db->beginTransaction();
            $idProduccion = $db->quote($idProduccion, 'INTEGER');

            $M_M = new Almacenes_Model_DbTable_Mmis();
            $M_OPM = new Produccion_Model_DbTable_OrdenesDeProduccionesMmis();
            $M_PMM = new Produccion_Model_DbTable_ProduccionesMmisMovimientos();

            // Verifico q exista la orden de produccion y que este en un estado correcto
            $this->_checkEstadoOrden($idProduccion);

            foreach ($idMmis as $idmmi) {
                $idmmi = $db->quote($idmmi, 'INTEGER');
                // es valido el mmi
                $R_M = $this->_isValidMmi($idmmi, $M_M, $M_OPM);

                // lo vacio
                $cant = $R_M->CantidadActual;
                if ($cant == 0) throw new Rad_Exception("El Mmi ya se encuentra Vacío");

                $R_M->CantidadActual = 0;
                $R_M->save();

                $M_PMM->registratMovimientoDeMmiEnProduccion($idProduccion, $idmmi, ((-1) * $cant),1);
            }

            $this->getAdapter()->commit();
            return true;
        } catch (Exception $e) {
            $this->getAdapter()->rollBack();
            throw $e;
        }
    }

    protected function _checkEstadoOrden($idProduccion)
    {
        $R_P = $this->find($idProduccion)->current();
        if (!$R_P) {
            throw new Rad_Exception('No se encontro la Producción.');
        }

        $orden = $R_P->findParentRow('Produccion_Model_DbTable_OrdenesDeProducciones');

        if (!$orden) {
            throw new Rad_Exception('No se encontro la Orden de Produccion.');
        }

        // Si Detenida o aceptada tiramos una excepcion
        if ($orden->Estado != 2 && $orden->Estado != 6 && $orden->Estado != 4) {
            throw new Rad_Exception('Esta orden no puede modificarse');
        }
    }

    protected function _isValidMmi($idmmi, $M_M, $M_OPM)
    {
        // Verifico q exista el Mmi
        $R_M = $M_M->find($idmmi)->current();

        if (!$R_M) {
            throw new Rad_Exception('No se encontro el Mmi.');
        }
        // Verifico q sea de materia prima el Mmi
        $R_OPM = $M_OPM->fetchRow("Mmi = $idmmi");

        if (!$R_OPM) {
            throw new Rad_Exception('El Mmi no pertenece a materia prima.');
        }

        return $R_M;
    }

     /**
     * Se usa para reducir la cantidad de los palets de materia prima
     * @param type $idmmi
     * @param type $cantidad
     * @param type $unidaddemedida
     * @param type $idProduccion
     * @return boolean
     */
    public function quitarMercaderiaAMmi($idmmi, $cantidad, $unidaddemedida = null, $idProduccion = null)
    {
        //TODO: Verificar que este usuario tiene una produccion abierta con este mmi
        try {
            $this->getAdapter()->beginTransaction();
            $idmmi = $this->getAdapter()->quote($idmmi, 'INTEGER');
            $idProduccion = $this->getAdapter()->quote($idProduccion, 'INTEGER');

            $M_M = new Almacenes_Model_DbTable_Mmis();
            $M_OPM = new Produccion_Model_DbTable_OrdenesDeProduccionesMmis();
            $M_PMM = new Produccion_Model_DbTable_ProduccionesMmisMovimientos();

            // Verifico q exista la orden de produccion y que este en un estado correcto
            $this->_checkEstadoOrden($idProduccion);

            // Verifico q venga la cantidad
            if ($cantidad <= 0)
                throw new Rad_Exception('La cantidad debe ser mayor a 0.');

            // es valido el mmi
            $this->_isValidMmi($idmmi, $M_M, $M_OPM);

            $cantidad = $M_M->quitarMercaderiaAMmi($idmmi, $cantidad, $unidaddemedida);

            $M_PMM->registratMovimientoDeMmiEnProduccion($idProduccion, $idmmi, ((-1) * $cantidad),1);

            $this->getAdapter()->commit();
            return true;
        } catch (Exception $e) {
            $this->getAdapter()->rollBack();
            throw $e;
        }
    }

    protected function moverMmisATemporal($idOrdenProd)
    {
        $M_ODP = Service_TableManager::get('Produccion_Model_DbTable_OrdenesDeProducciones');

        $idsMmis = array();

        $materiaPrima = $M_ODP->devolverMmisMateriaPrima($idOrdenProd);

        foreach ($materiaPrima as $mmi) {
           $idsMmis[] = $mmi['Id'];
        }

        if(!empty($idsMmis)){
            $producidos = $M_ODP->devolverMmisGenerados($idOrdenProd);

            foreach ($producidos as $mmi) {
               $idsMmis[] = $mmi['Id'];
            }

            $M_Mmi = Service_TableManager::get('Almacenes_Model_DbTable_Mmis');

            $M_Mmi->update(
                array('Almacen' => null, 'Ubicacion' => null),
                "Id in (".implode($idsMmis,',').")"
            );
        }
    }

    /**
     * Clona los mismo empleados de la produccion anterior de la misma orden de produccion
     *
     * @param int $idProduccion	identificador de la Produccion
     *
     * @return Zend_Db_Table_Row
     */
    public function clonarEmpleados($idProduccion)
    {
        try {
            $db = $this->getAdapter();
            $db->beginTransaction();

            $idProduccion = $db->quote($idProduccion, 'INTEGER');

            $M_LPP = Service_TableManager::get('Produccion_Model_DbTable_LineasDeProduccionesPersonas');
            $M_ODP = Service_TableManager::get('Produccion_Model_DbTable_OrdenesDeProducciones');

            // Verifico q exista la linea de tiempo actual
            $R_P = $this->find($idProduccion)->current();

            if (!$R_P){
                throw new Rad_Exception('No se encontro la linea de tiempo actual.');
            }

            // Verifico q la orden de produccion este detenida
            $R_ODP = $M_ODP->find($R_P->OrdenDeProduccion)->current();

            if ($R_ODP->Estado != 6){
                throw new Rad_Exception('La Orden De Producción debe estar detenida para realizar dicha acción.');
            }

            // Traigo la linea de tiempo anterior para clonar los empleados.
            $R_P_Anterior = $this->fetchRow("OrdenDeProduccion = $R_ODP->Id and Id < $idProduccion", "Id Desc");
            if (!$R_P_Anterior){
                throw new Rad_Exception('No se encontro la linea de tiempo anterior.');
            }

            // Busco los empleados y en q actividad trabajaron en la linea de produccion anterior para clonarlos.
            $R_LPP = $M_LPP->fetchAll("Produccion = $R_P_Anterior->Id");
            if (!$R_LPP){
                throw new Rad_Exception('No se encontraron registros para clonar.');
            }

            //Clono los registros de la Linea de Prodccion anterior
            foreach ($R_LPP as $rowest) {
                // Creamos un registro
                $row = $M_LPP->createRow();
                $row->Produccion = $idProduccion;
                $row->Persona    = $rowest->Persona;
                $row->Actividad  = $rowest->Actividad;
                $row->save();
            }

            $this->_db->commit();
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }

    }


}
