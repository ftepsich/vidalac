<?php

class Base_Model_DbTable_ArticulosGenericos extends Base_Model_DbTable_Articulos
{
    protected $_permanentValues = array('Tipo' => 1, 'EsMateriaPrima' => 0);

    /**
     * Inserta un Articulo
     * @param array $data Datos
     */
    public function insert($data)
    {
        try {
            $this->_db->beginTransaction();

            // Es Final
            if ($data['EsFinal'] == 1) {
                $this->_validarTieneControlStock($data);
                $this->_validarTieneMarca($data);
                $this->_validarTieneIva($data);
            } else {
                // si el articulo no es final no puede ser ni para compra ni para venta
                $this->_validarEsParaCompra($data, true);
                $this->_validarEsParaVenta($data, true);
            }

            $this->_validarTieneUnidadDeMedida($data);

            $data['Codigo'] = $this->_generarCodigo($data);

            // inserto
            $id = parent::insert($data);



            $this->_db->commit();

            return $id;

        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }

    }

    /**
     * Update
     *
     * @param array $data   Valores que se cambiaran
     * @param array $where  Registros que se deben modificar
     *
     */
    public function update($data, $where)
    {
        $this->_db->beginTransaction();
        try {

            $reg = $this->fetchAll($where);

            foreach ($reg as $row) {


                // Controlo que no quiera modificar elementos a una formula
                if($data['UnidadDeMedida'] && $data['UnidadDeMedida'] != $row->UnidadDeMedida) {

                    $sql = "SELECT c.Id
                                FROM Comprobantes c
                                    INNER JOIN ComprobantesDetalles cd on c.Id = cd.Comprobante
                                    INNER JOIN TiposDeComprobantes tc ON tc.Id = c.TipoDeComprobante
                                WHERE cd.Articulo = $row->Id and tc.Grupo in (1,4,6,7,8,10,12,13)";

                    $comprobante = $this->_db->fetchAll($sql);

                    if($comprobante){
                        throw new Rad_Db_Table_Exception('No se permite cambiar la unidad de medida porque el articulo ya esta asociada a un comprobante.');
                    }
                }
            }

            parent::update($data, $where);

            $this->_db->commit();
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }



    /**
     * Genera el Codigo unico que identifica al Articulo
     * @param array $data Datos
     * @param string $where
     */
    protected function _generarCodigo($data, $where = null)
    {
        $condicion = 'Tipo = 1';

        if ($where) {
            $condicion .= ' AND Id <> ' . $data['Id'];
        }

        $CodigoAnterior = $this->fetchRow($condicion, 'Codigo DESC');
        if ($CodigoAnterior && $CodigoAnterior->Codigo) {
            $Codigo = $CodigoAnterior->Codigo + 1;
        } else {
            $Codigo = $data['Tipo'] * 10000;
        }

        return $Codigo;
    }
}