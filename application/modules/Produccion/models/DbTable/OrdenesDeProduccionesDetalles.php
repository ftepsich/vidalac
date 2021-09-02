<?php
/**
 * Produccion_Model_DbTable_OrdenesDeProduccionesDetalles
 *
 * @package     Aplicacion
 * @subpackage 	Produccion
 * @class       Produccion_Model_DbTable_OrdenesDeProduccionesDetalles
 * @extends     Rad_Db_Table
 * @copyright   SmartSoftware Argentina 2010
 */
class Produccion_Model_DbTable_OrdenesDeProduccionesDetalles extends Rad_Db_Table
{

    protected $_name = 'OrdenesDeProduccionesDetalles';

    // protected $_gridGroupField = 'ProductoDescripcion';

    /**
     * Valores Default tomados del modelo y no de la base
     *
     */
    protected $_defaultSource = self::DEFAULT_CLASS;

    protected $_referenceMap = array(
        'ArticulosVersiones' => array(
            'columns'        => 'ArticuloVersion',
            'refTableClass'  => 'Base_Model_DbTable_ArticulosVersiones',
            'refJoinColumns' => array("Descripcion"),
            'comboBox'       => true, 
            'comboSource'    => 'datagateway/combolist/fetch/EsArticuloInsumo',
            'refTable'       => 'ArticulosVersiones',
            'refColumns'     => 'Id',
            'comboPageSize'  => 10
        ),
        'OrdenesDeProducciones' => array(
            'columns'       => 'OrdenDeProduccion',
            'refTableClass' => 'Produccion_Model_DbTable_OrdenesDeProducciones',
            'refTable'      => 'OrdenesDeProducciones',
            'refColumns'    => 'Id'
        )
    );

    protected $_calculatedFields = array(
        'Stock' => "fStockArticuloEsInsumo(OrdenesDeProduccionesDetalles.ArticuloVersion) COLLATE utf8_general_ci"
    );

    protected $_dependentTables = array();

    public function init()
    {
        $this->_defaultValues = array(
            'Fecha' => date('Y-m-d H:i:s')
        );

        parent::init();

        if ($this->_fetchWithAutoJoins) {

            $j = $this->getJoiner();
            $j->with('ArticulosVersiones')
              ->joinRef('Articulos',array('ArticuloDescripcion' => 'Descripcion','ArticuloCodigo' => 'Codigo'));
            $j->joinDep(
                'Produccion_Model_DbTable_OrdenesDeProduccionesMmis',
                array(
                    'AsignadosTotal' => 'SUM({remote}.CantidadActual)'
                ),
                null,
                null,
                'OrdenesDeProduccionesDetalles.Id'
            );
        }

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

        $summary       = array();
        $modelArticulo = new Base_Model_DbTable_Articulos();
        $session       = new Zend_Session_Namespace('OrdenesDeProducciones');

        foreach ($return['rows'] as &$row) {
            // Si tiene Mmis asignados temporal, cuento las cantidades
            $mmis = @array_keys($session->MmisAsignadosTemporal[$row['OrdenDeProduccion']][$row['Id']]);

            if ($mmis) {
                $select = $this->_db->select()
                              ->from('Mmis', array('SUM(CantidadActual) as sum'))
                              ->where('Id in ('.implode(',', $mmis).')');

                $mmis = $this->_db->query($select)->fetchAll();
                $row['AsignadosTemporal'] = $mmis[0]['sum'];
            } else {
                $row['AsignadosTemporal'] = 0;
            }

            $articulo = $modelArticulo->find($row['Articulo'])->current();

            $requerido[$row[$this->_gridGroupField]] = $row['Requerido'];
            $unidad[$row[$this->_gridGroupField]]    = $row['Unidad'];
            // Obtener la cantidad de producto por unidad en la unidad de medida configurada en el producto
            //$summary[$row[$this->_gridGroupField]]['Cantidad'] += $modelArticulo->getCantidadProductoArticulo($articulo) * $row['Cantidad'];
        }

        foreach($summary as $grupo => $summaryDetalle) {
            $summary[$grupo]['Cantidad'] = 'Total: '.$summary[$grupo]['Cantidad'].
                ' de '.sprintf('%01.2f',$requerido[$grupo]).' '.$unidad[$grupo];
        }

        $return['summaryData'] = $summary;
        return $return;
    }

    public function delete($where)
    {
        /* Tengo que verificar que no se hayan asignado palets
           a este pedido de materiales antes de borrarlo */

        $modelOrdenesProdMmis = Service_TableManager::get('Produccion_Model_DbTable_OrdenesDeProduccionesMmis');

        $aBorrar = $this->fetchAll($where);

        foreach ($aBorrar as $row) {
            $c = $modelOrdenesProdMmis->getCantidadAsignada($row->Id);

            if ($c) throw new Rad_Db_Table_Exception('No se puede borrar este registro. El almacen ya asigno '.$c.' al mismo.');
        }

        parent::delete($where);
    }

    public function asignarMmi ($idODPDetalle, $idMmi)
    {
        $modelODPMmis = new Produccion_Model_DbTable_OrdenesDeProduccionesMmis();
        $modelMmis = new Base_Model_DbTable_Mmis();

        try {
            $this->_db->beginTransacion();

            $ODPDetalle = $this->find($idODPDetalle)->current();
            $mmi = $modelMmis->find($idMmi)->current();

            if (!count($ODPDetalle))
                throw new Rad_Db_Table_Exception('No se encuentra el detalle de la Orden de Produccion');

            if (!count($mmi))
                throw new Rad_Db_Table_Exception('No se encuentra el MMI');

            $ODPMmi = $modelODPMmis->create(array(
                'OrdenDeProduccionDetalle'  => $ODPDetalle->Id,
                'Mmi'                       => $mmi->Id,
                'CantidadActual'            => $mmi->CantidadActual
            ));
            $ODPMmi->save();

            $this->_db->commit();
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }
}