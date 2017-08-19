<?php
/**
 * 
 */
class Facturacion_Model_DbTable_ComprobantesRelacionadosDetalles extends Rad_Db_Table
{
    protected $_name = 'ComprobantesRelacionadosDetalles';
    protected $_referenceMap = array(
        'ComprobantesRelacionados' => array(
            'columns' => 'ComprobanteRelacionado',
            'refTableClass' => 'Facturacion_Model_DbTable_ComprobantesRelacionados',
            'refJoinColumns' => array('o'),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'ComprobantesRelacionados',
            'refColumns' => 'Id',
        )
    );
    protected $_dependentTables = array();

    /**
     * Delete
     * En el caso que elimine el ultimo de los detalles de una relacion
     * elimina la relacion tambien.
     *
     * @param array $where 		detalle de lo que se debe borrar
     *
     * @return boolean
     */
    //OJO no se debe usar ya que la OC esta cerrada cuando se asocia por lo tanto ya no se puede modificar


    public function delete($where)
    {
        $this->_db->beginTransaction();
        try {
            $R_CRD = $this->fetchAll($where);
            if (!$R_CRD) {
                // No se encuentra lo que se debe borrar
                // puede suceder cuando regresa de borrar un componente de una factura que llego a 0
            } else {

                $M_CR = new Facturacion_Model_DbTable_ComprobantesRelacionados(array(), false);
                $M_CD = new Facturacion_Model_DbTable_ComprobantesDetalles(array(), false);

                foreach ($R_CRD as $row) {
                    $whereRow = "Id = $row->Id";
                    $idCR = $row->ComprobanteRelacionado;

                    // Reduzco la cantidad en Comprobante Detalle
                    $R_CR = $M_CR->fetchRow("Id = $row->ComprobanteRelacionado");
                    if (!$R_CR) {
                        throw new Rad_Db_Table_Exception('No se encuentra el comprobante.');
                    }

                    $R_CD = $M_CD->fetchRow("Comprobante = $R_CR->ComprobantePadre and Articulo = $row->Articulo");
                    if ($R_CD) {
                        $NuevaCant = $R_CD->Cantidad - $row->Cantidad;

                        if ($NuevaCant > 0.001) {
                            $data = array("Cantidad" => $R_CD->Cantidad - $row->Cantidad);
                            $M_CD->update($data, "Id = $R_CD->Id");
                        } else {
                            $M_CD->delete("Id = $R_CD->Id");
                        }
                    }

                    // Borro el registro
                    parent::delete($whereRow);

                    // Veo si queda algun componente de ese articulo en la relacion,
                    // de no quedar debo borrar la entrada en ComprobantesDetalles
                    $compPadre = $M_CR->fetchRow("Id = $idCR");

                    $sql = "	select 	CR.*
                                from 	ComprobantesRelacionados CR,
                                                ComprobantesRelacionadosDetalles CRD
                                where	CR.Id = CRD.ComprobanteRelacionado
                                and		CR.ComprobantePadre = $compPadre->Id
                                and		CRD.Articulo = $row->Articulo ";

                    $R = $this->_db->fetchAll($sql);
                    if (!count($R)) {
                        $M_CD->delete(" Comprobante = $compPadre->Id and Articulo = $row->Articulo");
                    }


                    // Veo si queda algun componente en la relacion si no es asi
                    // borro la relacion
                    $R_CRD2 = $this->fetchAll("ComprobanteRelacionado = $idCR");
                    if (!$R_CRD2) {
                        $M_CR->delete("Id= $idCR");
                    }
                }
            }
            $this->_db->commit();
            return true;
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    
    
    
    
    
    
    public function delete2($where)
    {
        $this->_db->beginTransaction();
        try {
            $R_CRD = $this->fetchAll($where);
            if (!$R_CRD) {
                // No se encuentra lo que se debe borrar
                // puede suceder cuando regresa de borrar un componente de una factura que llego a 0
            } else {

                $M_CR = new Facturacion_Model_DbTable_ComprobantesRelacionados(array(), false);
                $M_CD = new Facturacion_Model_DbTable_ComprobantesDetalles(array(), false);

                foreach ($R_CRD as $row) {
                    $whereRow = "Id = $row->Id";
                    $idCR = $row->ComprobanteRelacionado;

                    /*
                     *  ojo lo saco para que no genere problemas cuando modifico la cantidad de RA por ejemplo
                     *

                    // Reduzco la cantidad en Comprobante Detalle
                    $R_CR = $M_CR->fetchRow("Id = $row->ComprobanteRelacionado");
                    if (!$R_CR) {
                        throw new Rad_Db_Table_Exception('No se encuentra el comprobante.');
                    }

                    $R_CD = $M_CD->fetchRow("Comprobante = $R_CR->ComprobantePadre and Articulo = $row->Articulo");
                    if ($R_CD) {
                        $NuevaCant = $R_CD->Cantidad - $row->Cantidad;

                        if ($NuevaCant > 0.001) {
                            $data = array("Cantidad" => $R_CD->Cantidad - $row->Cantidad);
                            $M_CD->update($data, "Id = $R_CD->Id");
                        } else {
                            $M_CD->delete("Id = $R_CD->Id");
                        }
                    }
                    */
                    // Borro el registro
                    parent::delete($whereRow);

                    // Veo si queda algun componente de ese articulo en la relacion,
                    // de no quedar debo borrar la entrada en ComprobantesDetalles
                    $compPadre = $M_CR->fetchRow("Id = $idCR");

                    $sql = "	select 	CR.*
								from 	ComprobantesRelacionados CR,
										ComprobantesRelacionadosDetalles CRD
								where	CR.Id = CRD.ComprobanteRelacionado
								and		CR.ComprobantePadre = $compPadre->Id
								and		CRD.Articulo = $row->Articulo ";

                    $R = $this->_db->fetchAll($sql);
                    if (!count($R)) {
                        $M_CD->delete(" Comprobante = $compPadre->Id and Articulo = $row->Articulo");
                    }


                    // Veo si queda algun componente en la relacion si no es asi
                    // borro la relacion
                    $R_CRD2 = $this->fetchAll("ComprobanteRelacionado = $idCR");
                    if (!$R_CRD2) {
                        $M_CR->delete("Id= $idCR");
                    }
                }
            }
            $this->_db->commit();
            return true;
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    public function eliminarRelacionesDetalleHijo($idRel)
    {
        parent::delete('ComprobanteRelacionado = '.$idRel);
    }

}

