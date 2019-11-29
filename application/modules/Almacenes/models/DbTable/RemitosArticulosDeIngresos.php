
<?php

require_once('Rad/Db/Table.php');

/**
 * @class 		Almacenes_Model_DbTable_RemitosArticulosDeIngresos
 * @extends		Facturacion_Model_DbTable_ComprobantesDetalles
 *
 *
 * Remitos Articulos
 *
 * Detalle de la cabecera de la tabla
 * Campos:
 * 		Id					-> Identificador Unico
 * 		Comprobante			-> identificador del Remito
 * 		Articulo			-> identificador del articulo
 * 		Cantidad			-> Cantidad de elementos del articulo indicado
 * 		Modificado			-> Bndera que indica si fue modificado manualmente
 * 		Observaciones		-> Obs. internas
 *
 *
 * @package 	Aplicacion
 * @subpackage 	Almacenes
 *
 */
class Almacenes_Model_DbTable_RemitosArticulosDeIngresos extends Almacenes_Model_DbTable_RemitosArticulos
{

    protected $_referenceMap = array(
        'Articulos' => array(
            'columns' => 'Articulo',
            'refTableClass' => 'Base_Model_DbTable_ArticulosGenericos',
            'refJoinColumns' => array(
                "Descripcion",
                "DescArreglada" => "IF(ComprobantesDetalles.Articulo is null,
                                        CAST(ComprobantesDetalles.Observaciones AS CHAR CHARSET utf8),
                                        CAST(Articulos.Descripcion AS CHAR CHARSET utf8)
                                    )",
                'Tipo'
            ),
            'comboBox' => true, 
            'comboSource' => 'datagateway/combolist/fetch/EsArticuloParaCompra',
            'refTable' => 'Articulos',
            'refColumns' => 'Id',
            'comboPageSize' => 10
        ),
        'Remitos' => array(
            'columns'         => 'Comprobante',
            'refTableClass'   => 'Almacenes_Model_DbTable_RemitosDeEntradas',
            'refJoinColumns'  => array('Numero'),
            'comboBox'        => true,
            'comboSource'     => 'datagateway/combolist',
            'refTable'        => 'Comprobantes',
            'refColumns'      => 'Id',
        )
    );

    protected $_dependentTables = array("Almacenes_Model_DbTable_Mmis");

    /**
     * Validadores
     *
     * Articulo  -> no vacio
     *
     */
    protected function _setValidadores ()
    {
        $this->_validators = array(
            'Articulo' => array(
                'allowEmpty' => false
            )
        );
    }

    public function init()
    {
        parent::init();
        if ($this->_fetchWithAutoJoins) {

            $this->getJoiner()->joinDep(
                'Almacenes_Model_DbTable_Mmis',
                 array('CantidadPaletizada' => 'sum(CantidadOriginal)'),
                 'RemitosArticulosIngresos'
            );
        }
    }

    /**
     * Inseta un Registro
     *
     * @param array $data
     * @return mixed
     */
    public function insert($data)
    {

        $this->_db->beginTransaction();
        try {

            if (isset($data['Cantidad']) && $data['Cantidad'] <= 0) {
                throw new Rad_Db_Table_Exception('La cantidad no puede ser 0 (cero).');
            }

            if ($data['Articulo'] && $this->estaElArticuloEnComprobante($data['Comprobante'], $data['Articulo'])) {
                // el articulo se encuentra en el comprobante => updatear cantidad
                $Rx = $this->fetchRow("ComprobantesDetalles.Comprobante = " . $data['Comprobante'] . " and ComprobantesDetalles.Articulo = " . $data['Articulo']);
                if ($Rx) {
                    $data["Cantidad"] = $Rx->Cantidad + $data["Cantidad"];
                }
                $this->update($data, "ComprobantesDetalles.Id = $Rx->Id");
                $id = $Rx->Id;
                // Publico
                Rad_PubSub::publish('Almacenes_RA_Updateado', $Rx);
            } else {
                $id = parent::insert($data);
                $R_Ins = $this->find($id)->current();
                Rad_PubSub::publish('Almacenes_RA_Insertado', $R_Ins);
            }

            $this->_db->commit();
            return $id;
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    /**
     * updateo un registro
     *
     * @param array $data
     * @param mixwd $where
     * @return mixed
     */
    public function update($data, $where)
    {

        $this->_db->beginTransaction();
        try {

            // Verifico las cuestiones de forma
            if (isset($data['Cantidad']) && $data['Cantidad'] <= 0) {
                throw new Rad_Db_Table_Exception('La cantidad no puede ser 0 (cero).');
            }

            $M_RE = new Almacenes_Model_DbTable_RemitosDeEntradas(array(), false);

            $reg = $this->fetchAll($where);

            if (count($reg)) {
                foreach ($reg as $row) {
                    // Salgo si no se puede modificar la factura
                    $M_RE->salirSi_estaCerrado($row['Comprobante']);
                    parent::update($data, "$this->_name.Id = " . $row['Id']);
                    Rad_PubSub::publish('Almacenes_RA_Updateado', $row);
                }
            }

            $this->_db->commit();
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
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

            $M_RE = new Almacenes_Model_DbTable_RemitosDeIEntaradas(array(), false);

            $reg = $this->fetchAll($where);

            // Si tiene articulos los borro
            if (count($reg)) {

                foreach ($reg as $row) {
                    // Salgo si no se puede modificar la factura
                    $M_RE->salirSi_estaCerrado($row['Comprobante']);
                }
                foreach ($reg as $row) {
                    // Publico y borro el renglon
                    parent::delete("Id =" . $row['Id']);
                    Rad_PubSub::publish('Almacenes_RA_Borrado', $row);
                }
            }
            $this->_db->commit();
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    public function fetchStockAlmacen($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = "Articulos.TipoDeControlDeStock = 1";

        $where = $this->_addCondition($where, $condition);

        return parent:: fetchAll($where, $order, $count, $offset);
    }
}