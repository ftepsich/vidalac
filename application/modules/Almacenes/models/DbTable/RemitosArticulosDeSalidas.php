<?php

require_once('Rad/Db/Table.php');

/**
 * @class Almacenes_Model_DbTable_RemitosArticulos
 * @extends Facturacion_Model_DbTable_ComprobantesDetalles
 *
 *
 * Remitos Articulos
 *
 * Detalle de la cabecera de la tabla
 * Campos:
 * Id               -> Identificador Unico
 * Comprobante      -> Identificador del Remito
 * Articulo         -> Identificador del articulo
 * Cantidad         -> Cantidad de elementos del articulo indicado
 * Modificado       -> Bndera que indica si fue modificado manualmente
 * Observaciones    -> Obs. internas
 *
 * @package 	Aplicacion
 * @subpackage 	Almacenes
 *
 */
class Almacenes_Model_DbTable_RemitosArticulosDeSalidas extends Almacenes_Model_DbTable_RemitosArticulos
{

    protected $_referenceMap = array(
        'Articulos' => array(
            'columns'       => 'Articulo',
            'refTableClass' => 'Base_Model_DbTable_ArticulosGenericos',
            'comboBox'      => true, 
            'comboSource'   => 'datagateway/combolist/fetch/EsArticuloParaVenta',
            'refTable'      => 'Articulos',
            'refColumns'    => 'Id',
            'comboPageSize' => 10,
            'refJoinColumns' => array(
                "Descripcion",
                "DescArreglada" => "IF(ComprobantesDetalles.Articulo is null,ComprobantesDetalles.Observaciones,Articulos.Descripcion)",
                'Tipo', "Codigo"
            )
        ),
        'Remitos' => array(
            'columns'        => 'Comprobante',
            'refTableClass'  => 'Almacenes_Model_DbTable_Remitos',
            'refTable'       => 'Comprobantes',
            'refColumns'     => 'Id',
        ),
        'RemitosDeSalidas' => array(
            'columns'        => 'Comprobante',
            'refTableClass'  => 'Almacenes_Model_DbTable_RemitosDeSalidas',
            'refTable'       => 'Comprobantes',
            'refColumns'     => 'Id',
        ),
        'RemitosSinRemitoSalida' => array(
            'columns'        => 'Comprobante',
            'refTableClass'  => 'Almacenes_Model_DbTable_RemitosSinRemitoSalida',
            'refTable'       => 'Comprobantes',
            'refColumns'     => 'Id',
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
                array('MmiCantAsociadaSalida' => 'sum(CantidadActual)'),
                'RemitosArticulosSalida'
            );
        }
    }

    /**
     * Inseta un Registro (No debe usarse desde el controlador)
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
                throw new Rad_Db_Table_Exception('El articulo ya se encuentra cargado, modifique la cantidad en lugar de agregar nuevamente el mismo articulo.');
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
     * Inseta un Registro desde el controlador
     *
     * @param array $data
     * @return mixed
     */

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
            $M_RE = new Almacenes_Model_DbTable_RemitosDeSalidas(array(), false);
            $reg = $this->fetchAll($where);

            foreach ($reg as $row) {
                // Salgo si no se puede modificar la factura
                $M_RE->salirSi_estaCerrado($row['Comprobante']);

                // Updateo
                parent::update($data, "$this->_name.Id=" . $row['Id']);
                Rad_PubSub::publish('Almacenes_RA_Updateado', $row);
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

            $M_RE = new Almacenes_Model_DbTable_RemitosDeSalidas(array(), false);

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
                    Rad_PubSub::publish('Facturacion_RA_Borrado', $row);
                }
            }
            $this->_db->commit();
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }
}