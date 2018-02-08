<?php

/**
 * Tipos de exportaciones
 *
 * Esta tabla se usa para facturacion electronica de exportacion
 * y sus datos son obtenidos a travez del web service wsfex
 *
 * @package     Aplicacion
 * @subpackage  Facturacion
 * @class       Facturacion_Model_DbTable_ComprobantesDetalles
 * @extends     Rad_Db_Table
 */
class Facturacion_Model_DbTable_ComprobantesDetalles extends Rad_Db_Table
{
    // Tabla mapeada
    protected $_name = 'ComprobantesDetalles';
    // Relaciones
    protected $_referenceMap = array(
        'ConceptosImpositivos' => array(
            'columns' => 'ConceptoImpositivo',
            'refTableClass' => 'Base_Model_DbTable_ConceptosImpositivos',
            'refJoinColumns' => array('Descripcion'),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'ConceptosImpositivos',
            'refColumns' => 'Id',
        ),
        'TransaccionesBancarias' => array(
            'columns' => 'TransaccionBancaria',
            'refTableClass' => 'Base_Model_DbTable_TransaccionesBancarias',
            'refJoinColumns' => array('Observaciones'),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'TransaccionesBancarias',
            'refColumns' => 'Id',
        ),
        'Cheques' => array(
            'columns' => 'Cheque',
            'refTableClass' => 'Base_Model_DbTable_Cheques',
            'refJoinColumns' => array('NoALaOrden'),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'Cheques',
            'refColumns' => 'Id',
        ),
        'Articulos' => array(
            'columns' => 'Articulo',
            'refTableClass' => 'Base_Model_DbTable_Articulos',
            'refJoinColumns' => array('Descripcion'),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'Articulos',
            'refColumns' => 'Id',
        ),
        'ComprobantesRelacionados' => array(
            'columns' => 'ComprobanteRelacionado',
            'refTableClass' => 'Facturacion_Model_DbTable_Comprobantes',
            'refJoinColumns' => array('Numero'),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'Comprobantes',
            'refColumns' => 'Id',
        ),
        'Comprobantes' => array(
            'columns' => 'Comprobante',
            'refTableClass' => 'Facturacion_Model_DbTable_Comprobantes',
            'refJoinColumns' => array('Numero'),
            //'comboBox' => true,
            //'comboSource' => 'datagateway/combolist',
            'refTable' => 'Comprobantes',
            'refColumns' => 'Id',
        )
    );
    protected $_dependentTables = array();

    /**
     * Recalcula el precio unitario de los articulos en el caso que se ubiera expresado en otra moneda
     *
     * @param array $idRow	identificador del comprobante
     *
     */
    public function recalcularPrecioUnitario($idComprobante)
    {
        $this->_db->beginTransaction();
        try {
            $M_FC = new Facturacion_Model_DbTable_Comprobantes(array(), false);

            // Recupero la cabecera
            $R_FC = $M_FC->find($idComprobante)->current();
            if (!$R_FC) throw new Rad_Db_Table_Exception('No se encuentra el comprobante requerido.');

            $R_FCD = $this->fetchAll("Comprobante = $idComprobante");

            if ($R_FCD) {
                foreach ($R_FCD as $row) {
                    // Calculo el PU en moneda local
                    $data['PrecioUnitario'] = 0;
                    if ($row->PrecioUnitarioMExtranjera) {
                        $data['PrecioUnitario'] = $row->PrecioUnitarioMExtranjera * $R_FC->ValorDivisa;
                    } else {
                        $data['PrecioUnitario'] = $row->PrecioUnitario;
                    }
                    parent::update($data, 'Id=' . $row->Id);
                }
            }
            $this->_db->commit();
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    /**
     * recupera el tipo de iva a aplicar a un articulo
     *
     * @param array $Articulo
     *
     */
    public function recuperarArticuloIVA($Articulo)
    {
        $this->_db->beginTransaction();
        try {
            $M_A = new Base_Model_DbTable_Articulos(array(), false);
            $idIVA = 1;
            $R_A = $M_A->find($Articulo)->current();
            if ($R_A) {
                $idIVA = $R_A->IVA;
            }

            $this->_db->commit();
            return $idIVA;
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    /**
     * Verifica si un articulo ya se encuentra en un comprobante
     *
     * @param int $idComprobante    identificador del Comprobante
     * @param int $idArticulo       identificador del articulo
     *
     * @return boolean
     */
    public function estaElArticuloEnComprobante($idComprobante, $idArticulo)
    {
        /* OJO ... se llama desde otros modelos y puede tener problemas con los permanent y default values */

        $M_CD = new Facturacion_Model_DbTable_ComprobantesDetalles(array(), false);
        $R_CD = $M_CD->fetchAll("ComprobantesDetalles.Comprobante = $idComprobante and ComprobantesDetalles.Articulo = $idArticulo");
        if (count($R_CD)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * recupera la cantidad de un articulo en un comprobante
     *
     * @param int $idComprobante    identificador del Comprobante
     * @param int $idArticulo       identificador del articulo
     *
     * @return int
     */
    public function recuperarCantidadArticulo($idComprobante, $idArticulo)
    {
        /* OJO ... se llama desde otros modelos y puede tener problemas con los permanent y default values */

        $Cantidad = 0;
        $M_CD = new Facturacion_Model_DbTable_ComprobantesDetalles(array(), false);
        $R_CD = $M_CD->fetchAll("ComprobantesDetalles.Comprobante = $idComprobante and ComprobantesDetalles.Articulo = $idArticulo");
        if (count($R_CD)) {
            $Cantidad = $R_CD->Cantidad;
        }
        return $Cantidad;
    }
}
