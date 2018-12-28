<?php

require_once 'Rad/Db/Table.php';

class Almacenes_Model_DbTable_Almacenes extends Rad_Db_Table
{

    protected $_name = "Almacenes";

    protected $_referenceMap = array(
        'Depositos' => array(
            'columns' => 'Deposito',
            'refTableClass' => 'Base_Model_DbTable_DepositosPropios',
            'refJoinColumns' => array("Comentario"),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist/fetch/Propio',
            'refTable' => 'Depositos',
            'refColumns' => 'Id'
        ),
        'TiposDeAlmacenes' => array(
            'columns' => 'TipoDeAlmacen',
            'refTableClass' => 'Almacenes_Model_DbTable_TiposDeAlmacenes',
            'refJoinColumns' => array("Descripcion"),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'TiposDeAlmacenes',
            'refColumns' => 'Id'
        ),
        'IncrementoFila' => array(
            'columns' => 'IncrementoFila',
            'refTableClass' => 'Almacenes_Model_DbTable_TiposDeIncrementos',
            'refJoinColumns' => array("Descripcion"),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'TiposDeIncrementos',
            'refColumns' => 'Id'
        ),
        'IncrementoAltura' => array(
            'columns' => 'IncrementoAltura',
            'refTableClass' => 'Almacenes_Model_DbTable_TiposDeIncrementos',
            'refJoinColumns' => array("Descripcion"),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'TiposDeIncrementos',
            'refColumns' => 'Id'
        ),
        'IncrementoProfundidad' => array(
            'columns' => 'IncrementoProfundidad',
            'refTableClass' => 'Almacenes_Model_DbTable_TiposDeIncrementos',
            'refJoinColumns' => array("Descripcion"),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'TiposDeIncrementos',
            'refColumns' => 'Id'
        ),
        'Perspectiva' => array(
            'columns' => 'Perspectiva',
            'refTableClass' => 'Almacenes_Model_DbTable_AlmacenesPerspectivas',
            'refJoinColumns' => array("Descripcion"),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'AlmacenesPerspectivas',
            'refColumns' => 'Id'
        )
    );

    public function init ()
    {
        $this->_validators = array(
            'Descripcion' => array(
                array('Db_NoRecordExists',
                    'Almacenes',
                    'Descripcion',
                    array(
                        'field' => 'Id',
                        'value' => "{Id}"
                    )
                )
            ),
            'TipoDeAlmacen' => array(
                'allowEmpty'=>false
            )
        );
        parent::init();
    }

    /**
     * Inserta un registro y crea las ubicaciones de ese almacen
     *
     * @param array $data
     * @return mixed
     */
    public function insert ($data)
    {
        $this->_db->beginTransaction();
        try {
            $id = parent::insert($data);
            if ($data['TieneRack']) {

                $M_U = new Almacenes_Model_DbTable_Ubicaciones;
                $regAlmacen = $this->find($id)->current();
                $M_U->crearUbicaciones($regAlmacen);
                /*
                $stmt = $this->_db->prepare('INSERT INTO Ubicaciones (Almacen, Fila, Profundidad, Altura, Descripcion) VALUES (?, ?, ?, ?, ?)');

                for ($fila = 1; $fila <= $data['RackCantFila']; $fila++)
                    for ($profundidad = 1; $profundidad <= $data['RackCantProfundidad']; $profundidad++)
                        for ($altura = 1; $altura <= $data['RackCantAltura']; $altura++)
                            $stmt->execute(array(
                                $id,
                                $fila,
                                chr($profundidad + 64),
                                $altura . 'P',
                                $fila . chr($profundidad + 64) . $altura . 'P'
                            ));
                */
            }
            $this->_db->commit();
            return $id;
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    public function update($data, $where)
    {
        $this->_db->beginTransaction();
        try {
            $registros = $this->fetchAll($where);
            $M_U = new Almacenes_Model_DbTable_Ubicaciones;


            foreach ($registros as $key => $reg) {

                if ($data['RackCantFila'] < $reg->RackCantFila || $data['RackCantProfundidad'] < $reg->RackCantProfundidad || $data['RackCantAltura'] < $reg->RackCantAltura) {
                    throw new Rad_Db_Table_Exception('No puede achicar un almacen ya creado');
                } else if ($data['RackCantFila'] != $reg->RackCantFila || $data['RackCantProfundidad'] != $reg->RackCantProfundidad || $data['RackCantAltura'] != $reg->RackCantAltura) {
                    $reg->setFromArray($data);
                    $M_U->crearUbicaciones($reg);
                }
            }

            $id = parent::update($data, $where);

            $this->_db->commit();
            return $id;
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    /**
     * Comprueba que el almacen no tenga ubicaciones ocupadas antes de borrar
     *
     */
    public function delete ($where)
    {
        $this->_db->beginTransaction();
        try {
            $registro = $this->fetchAll($where)->current();
            $ubicaciones = new Almacenes_Model_DbTable_Ubicaciones(array(), false);

            $sql = "select U.Descripcion,M.Identificador
                    from Ubicaciones U, Mmis M
                    where U.Id = M.Ubicacion
                    and U.Almacen = ".$registro->Id;

            $ocupados = $this->_db->fetchAll($sql);

            if (count($ocupados)) throw new Rad_Db_Table_Exception('Este almacen tiene ubicaciones ocupadas.');

            $stmt = $this->_db->prepare('DELETE FROM Ubicaciones WHERE Almacen = ?');
            $stmt->execute(array($registro->Id));

            $ubicaciones->delete('Almacen = ' . $registro->Id);
            $id = parent::delete($where);

            $this->_db->commit();
            return $id;
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    /**
     * Mueve un Mmi de una ubicacion (o Mmi directo) a otra (o a Almacen no rackeable)
     *
     * @param int $almacenOrigen
     * @param int $almacenDestino
     * @param array $items
     * @param int $deposito deposito en el que se esta trabajando
     */
    public function moverMmis ($almacenOrigen, $almacenDestino, $items, $deposito)
    {
        try {
            $this->_db->beginTransaction();

            $almacenOrigen  = $this->getAdapter()->quote($almacenOrigen, 'INTEGER');
            $almacenDestino = $this->getAdapter()->quote($almacenDestino, 'INTEGER');
            $deposito       = $this->getAdapter()->quote($deposito, 'INTEGER');

            $aAlmacenMmis = $this->_getMmis($almacenOrigen, $items);

            $this->_setMmis($aAlmacenMmis['AlmacenOrigen'], $almacenDestino, $aAlmacenMmis['Mmis'], $deposito);

            $this->_db->commit();
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    /**
     * Obtiene los Mmis que se van a mover,
     * de acuerdo desde donde se estan moviendo
     *
     * AlmacenOrigen == TEMPORAL     -> $items == Mmis
     * AlmacenOrigen == RACKEABLE    -> $items == Ubicaciones
     * AlmacenOrigen == NO RACKEABLE -> $items == Mmis
     *
     * @param int $almacenOrigen
     * @param array $items
     * @return Zend_Db_Table_Rowset Mmis
     */
    protected function _getMmis ($almacenOrigen, $items)
    {
        $rAlmacen  = $this->find($almacenOrigen)->current();
//        Rad_Log::debug($rAlmacen);
        $mMmis = new Almacenes_Model_DbTable_Mmis();

        $mmis = array();
        $aMmis = array();
        // No tiene Almacen (viene del Temporal) o tiene Almacen y NO es rackeable
        if (!$almacenOrigen || ($rAlmacen && !$rAlmacen->TieneRack)) {
            // ES TEMPORAL O NO RACKEABLE !!!
            // Busco los Mmis
            //Rad_Log::debug('DESDE TEMPORAL O NO RACKEABLE!!!');

            foreach ($items as $mov) {
                $mmis[] = $mov->desde;
                $rsMmis = $mMmis->find($mov->desde);   //PK: mov->desde trae un id de mmi?

                if (!count($rsMmis)) throw new Rad_Exception('No se encontraron algunos Mmis');

                $rMmi = $rsMmis->current();

                // Controlo que el mmi no este en una produccion activa -----------------------------
                $mMmis->salirSi_mmiEnProduccionActiva($rMmi->Id);

                $aMmis[] = array(
                    'desde' => $rMmi,
                    'hacia' => $mov->hacia
                );
            }
            $rsMmis = $mMmis->find($mmis);
            if (count($rsMmis) != count($mmis)) throw new Rad_Exception('No se encontraron algunos Mmis');
        } elseif ($rAlmacen) {
            // ES RACKEABLE !!!
            // Busco los Mmis que estan en las ubicaciones
            // Rad_Log::debug('DESDE RACKEABLE!!!');

            foreach ($items as $mov) {
                $mmis[] = $mov->desde;
                $rsMmis = $mMmis->fetchAll(
                    $mMmis->select()->where('Ubicacion = ?', $mov->desde)
                );
                if (!count($rsMmis)) throw new Rad_Exception('No se encontraron algunos Mmis');

                $rMmi = $rsMmis->current();

                // Controlo que el mmi no este en una produccion activa -----------------------------
                $mMmis->salirSi_mmiEnProduccionActiva($rMmi->Id);

                $aMmis[] = array(
                    'desde' => $rMmi,
                    'hacia' => $mov->hacia
                );
            }
            $rsMmis = $mMmis->fetchAll(
                $mMmis->select()->where('Ubicacion IN (?)', $mmis)
            );
        } else {
            throw new Rad_Exception('No es encontro el Almacen de origen');
        }
        return array(
            'AlmacenOrigen' => $rAlmacen,
            'Mmis'          => $aMmis
        );
    }

    /**
     * Mueve efectivamente los Mmis
     *
     * @param Zend_Db_Table_Rowset|null $almacenOrigen
     * @param int|null                  $almacenDestino
     * @param array                     $mmis Movimiento: [desde:(Rowset Mmi), hacia: (Id ubic|null)]
     * @param int                       $deposito
     */
    protected function _setMmis ($rAlmacenOrigen, $almacenDestino, $mmis, $deposito)
    {
        $db = $this->getAdapter();

        $rAlmacenDestino = $this->find($almacenDestino)->current();

        $ubicaciones = new Almacenes_Model_DbTable_Ubicaciones;

        if ($rAlmacenOrigen->Id == $rAlmacenDestino->Id && (!$rAlmacenOrigen->TieneRack && !$rAlmacenDestino->TieneRack)) {
            throw new Rad_Exception('No se pueden mover MMIs en el mismo almacen no rackeable');
        }

        if ($rAlmacenDestino) {
            $depositoDestino = $rAlmacenDestino->findParentRow('Base_Model_DbTable_DepositosPropios');
        }


        foreach ($mmis as $mmi) {

            if ($mmi['hacia']) {

                $ubicDestino = $ubicaciones->find($mmi['hacia'])->current();

                if (!$ubicDestino) throw new Rad_Exception('No se encuentra la ubicacion destino');

                if (!$ubicDestino->Existente) throw new Rad_Exception('La ubicación destino se encuentra anulada');
            }

            if ($depositoDestino && ($depositoDestino->Id != $mmi['desde']->Deposito || $deposito != $depositoDestino->Id)) {
                throw new Rad_Exception('No se pueden mover MMIs entre almacenes de distintos depositos');
            } else if ($deposito != $mmi['desde']->Deposito ) {
                throw new Rad_Exception('No se pueden mover MMIs entre almacenes de distintos depositos');
            }

            $mmi['desde']->Almacen     = ($rAlmacenDestino) ? $rAlmacenDestino->Id : null;
            $mmi['desde']->Ubicacion   = ($mmi['hacia']) ? $mmi['hacia'] : null;
            $mmi['desde']->Deposito    = $deposito;
            $mmi['desde']->setReadOnly(false);
            $mmi['desde']->save();
        }
    }

    public function fetchInterdeposito($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = " Almacenes.TipoDeAlmacen = 3";
        $where = $this->_addCondition($where, $condicion);
        return parent:: fetchAll($where, $order, $count, $offset);
    }

}
