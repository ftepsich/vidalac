<?php
require_once 'ComprobantesDetalles.php';

/**
 * @class       Facturacion_Model_DbTable_ComprobantesSinIVAArticulos
 * @extends     Facturacion_Model_DbTable_ComprobantesDetalles
 *
 * Comprobantes Sin IVA Articulos
 *
 * @package     Aplicacion
 * @subpackage  Facturacion
 *
 */
class Facturacion_Model_DbTable_ComprobantesSinIVAArticulos extends Facturacion_Model_DbTable_ComprobantesDetalles
{
    protected $_name = 'ComprobantesDetalles';

    protected $_validators = array();

    protected $_referenceMap = array(
        'ComprobantesSinIVA' => array(
            'columns'        => 'Comprobante',
            'refTableClass'  => 'Facturacion_Model_DbTable_ComprobantesSinIVA',
            'refJoinColumns' => array('Numero'),
            'comboBox'       => true,
            'comboSource'    => 'datagateway/combolist',
            'refTable'       => 'Comprobantes',
            'refColumns'     => 'Id',
        ),
        'PlanesDeCuentas' => array(
            'columns'        => 'CuentaCasual',
            'refTableClass'  => 'Contable_Model_DbTable_PlanesDeCuentas',
            'refJoinColumns' => array('Descripcion', 'Jerarquia'),
            'comboBox'       => true,
            'comboSource'    => 'datagateway/combolist/fetch/PlanCuentaImputable',
            'refTable'       => 'PlanesDeCuentas',
            'refColumns'     => 'Id',
            'comboPageSize'  => 20
        )
    );

    /** 
     * Insert
     *
     * @param array $data   Datos a insertar
     * @return mixed
     * @throws Rad_Db_Table_Exception
     * @throws Exception
     */
    public function insert($data)
    {
        try {
            $this->_db->beginTransaction();

            $M_CSI = new Facturacion_Model_DbTable_ComprobantesSinIVA(array(), false);

            $data['Cantidad'] = 1;

            $R_CSI = $M_CSI->find($data['Comprobante'])->current();

            if (!$R_CSI) throw new Rad_Db_Table_Exception('No se encuentra el comprobante requerido.');

            $M_CSI->salirSi_estaCerrado($data['Comprobante']);

            if (isset($data['CuentaCasual']) && !$data['CuentaCasual']) throw new Rad_Db_Table_Exception('Falta ingresar la cuenta a la cual asociar uno de los detalles.');

            $id = parent::insert($data);

            $this->_db->commit();
            return $id;
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    /**
     *
     * Update
     *
     * @param array $data
     * @param array $where
     * @return mixed|void
     * @throws Rad_Db_Table_Exception
     * @throws Exception
     */
    public function update($data, $where)
    {
        $this->_db->beginTransaction();

        try {

            $data['Cantidad'] = 1;

            $M_CSI = new Facturacion_Model_DbTable_ComprobantesSinIVA(array(), false);
            $reg = $this->fetchAll($where);

            foreach ($reg as $row) {
                // Salgo si no se puede modificar la factura
                $M_CSI->salirSi_estaCerrado($row['Comprobante']);

                // Recupero la cabecera
                $R_CSI = $M_CSI->find($row['Comprobante'])->current();
                if (!$R_CSI) throw new Rad_Db_Table_Exception('No se encuentra el comprobante requerido.');

                // Updateo
                parent::update($data, 'Id=' . $row['Id']);
            }

            $this->_db->commit();
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    /**
     *
     * Delete
     *
     * @param $id
     * @throws Rad_DbTable_Exception
     */
    public function forceDelete($id)
    {
         $row = $this->find($id)->current();
         if (!$row) throw new Rad_DbTable_Exception('No existe el articulo del comprobante que esta intentando eliminar.');
         parent::delete('Id =' . $row->Id);
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

            $M_CSI = new Facturacion_Model_DbTable_ComprobantesSinIVA(array(), false);

            $reg = $this->fetchAll($where);

            // Si tiene articulos los borro
            if (count($reg)) {

                foreach ($reg as $row) {
                    // Salgo si no se puede modificar la factura
                    $M_CSI->salirSi_estaCerrado($row['Comprobante']);
                }
                foreach ($reg as $row) {
                    parent::delete('Id =' . $row['Id']);
                }
            }
            $this->_db->commit();
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }
}
