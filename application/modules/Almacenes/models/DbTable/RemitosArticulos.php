<?php

require_once('Rad/Db/Table.php');

/**
 * @class 		Almacenes_Model_DbTable_RemitosArticulos
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
class Almacenes_Model_DbTable_RemitosArticulos extends Facturacion_Model_DbTable_ComprobantesDetalles
{

    protected $_name = "ComprobantesDetalles";
    protected $_validators = array(
        'Cantidad' => array(
            array('GreaterThan', "0"),
            'messages' => array(
                'Debe ser mayor que 0'
            )
        ),
        'Articulo' => array(
            'allowEmpty' => false
        )
    );

    // no se q sorcho hace, lo comente: Martin
    protected $_calculatedFields = array(
        'CantAsociada' => "fCantSinAsociarRelHijo(ComprobantesDetalles.Comprobante,ComprobantesDetalles.Articulo)"
    );

    // Inicio  protected $_referenceMap --------------------------------------------------------------------------
    protected $_referenceMap = array(
        'Articulos' => array(
            'columns' => 'Articulo',
            'refTableClass' => 'Base_Model_DbTable_ArticulosGenericos',
            'refJoinColumns' => array(
                "Descripcion",
                "DescArreglada" => "IF(ComprobantesDetalles.Articulo is null,ComprobantesDetalles.Observaciones,Articulos.Descripcion)",
                'Tipo'
            ),
            'comboBox' => true, // Armar un combo con esta relacion - Algo mas queres haragan programa algo :P -
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'Articulos',
            'refColumns' => 'Id',
            'comboPageSize' => 10
        ),
        'Articulos1' => array(
            'columns' => 'Articulo',
            'refTableClass' => 'Base_Model_DbTable_Articulos',
            'refTable' => 'Articulos',
            'refColumns' => 'Id'
        ),        
        'Remitos' => array(
            'columns' => 'Comprobante',
            'refTableClass' => 'Almacenes_Model_DbTable_Remitos',
            'refJoinColumns' => array('Numero'),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'Comprobantes',
            'refColumns' => 'Id',
        )
    );

    /**
     * Inseta un Registro
     *
     * @param array $data
     * @return mixed
     */
    public function insert($data)
    {
        try {
            $this->_db->beginTransaction();

            $M_R = new Almacenes_Model_DbTable_Remitos(array(), false);

            $R_R = $M_R->find($data['Comprobante'])->current();
            if (!$R_R) {
                throw new Rad_Db_Table_Exception('No se encuentra el comprobante requerido.');
            }

            $M_R->salirSi_estaCerrado($data['Comprobante']);

            // Reviso si viene el articulo, de no venir revisar los otros campos requeridos
            if (!$data['Articulo']) {
                throw new Rad_Db_Table_Exception('No se indico el articulo.');
            }

            // Inserto el articulo y publico
            $id = parent::insert($data);

            /* 	TODO: Ojo con las dobles publicaciones, aca se publica en el hijo y aca tambien (padre)
              $R_Ins = $this->find($id)->current();
              Rad_PubSub::publish('Almacenes_RA_Insertado',$R_Ins);
             */

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

            $M_R = new Almacenes_Model_DbTable_Remitos(array(), false);
            $reg = $this->fetchAll($where);
            $data['Modificado'] = 1;
            foreach ($reg as $row) {
                // Salgo si no se puede modificar la factura
                $M_R->salirSi_estaCerrado($row['Comprobante']);

                // Recupero la cabecera
                $R_R = $M_R->find($row['Comprobante'])->current();
                if (!$R_R) {
                    throw new Rad_Db_Table_Exception('No se encuentra el comprobante requerido.');
                }
                // Updateo
                parent::update($data, "Id=" . $row['Id']);

                /* 	TODO: Ojo con las dobles publicaciones, aca se publica en el hijo y aca tambien (padre)
                  Rad_PubSub::publish('Almacenes_RA_Updateado',$row);
                 */
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

            $M_R = new Almacenes_Model_DbTable_Remitos(array(), false);

            $reg = $this->fetchAll($where);

            // Si tiene articulos los borro
            if (count($reg)) {

                foreach ($reg as $row) {
                    // Salgo si no se puede modificar la factura
                    $M_R->salirSi_estaCerrado($row['Comprobante']);
                }
                foreach ($reg as $row) {
                    // Publico y borro el renglon
                    parent::delete("Id =" . $row['Id']);
                    /* 	TODO: Ojo con las dobles publicaciones, aca se publica en el hijo y aca tambien (padre)
                      Rad_PubSub::publish('Almacenes_RA_Borrado',$row);
                     */
                }
            }
            $this->_db->commit();
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

}
