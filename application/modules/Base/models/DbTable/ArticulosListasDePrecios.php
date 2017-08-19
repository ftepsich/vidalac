<?php
require_once 'Rad/Db/Table.php';

/**
 * Base_Model_DbTable_ArticulosListasDePrecios
 *
 * Listas de Precios de Articulos
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Base
 * @class Base_Model_DbTable_ArticulosListasDePrecios
 * @extends Rad_Db_Table
 */
class Base_Model_DbTable_ArticulosListasDePrecios extends Rad_Db_Table
{

    protected $_name = 'ArticulosListasDePrecios';
    
    /**
     * Init del Modelo
     */
    public function init()
    {
        $this->_validators = array(
            'Descripcion' => array(
                array(
                    'Db_NoRecordExists',
                    'ArticulosListasDePrecios',
                    'Descripcion',
                    array(
                        'field' => 'Id',
                        'value' => "{Id}"
                    )
                ),
                'messages' => array('El nombre de la lista de precio ya existe.')
            )
        );

        parent::init();
    }    

    public function insert($data)
    {
        /*si viene este valor (ListaDefault) pongo el resto de los registros en 0 al campo ListaDefault
        solo un registro puede ser ListaDefault*/        
        if($data['ListaDefault']){
            $this->_db->query("update $this->_name set ListaDefault = 0;");
        }
        
        $id = parent::insert($data);

        $modelArticulos = new Base_Model_DbTable_Articulos();
        $articulos = $modelArticulos->fetchAll('EsProducido = 1');
        $modelDetalle = new Base_Model_DbTable_ArticulosListasDePreciosDetalle();

        foreach ($articulos as $articulo) {
            $row = $modelDetalle->createRow(array('ListaDePrecio' => $id,
                        'Articulo' => $articulo->Id,
                        'FechaInforme' => date('Y-m-d')
                    ));
            $row->save();
        }
        
        return $id;
    }
    
    /**
     * Update
     *
     * @param array $data 	Valores que se cambiaran
     * @param array $where 	Registros que se deben modificar
     *
     */
    public function update($data, $where)
    {
        $this->_db->beginTransaction();
        try {
            /*si viene este valor (ListaDefault) pongo el resto de los registros en 0 al campo ListaDefault
            solo un registro puede ser ListaDefault*/
            if($data['ListaDefault']){
                $this->_db->query("update $this->_name set ListaDefault = 0;");
            }           
            parent::update($data, $where);
            
            $this->_db->commit();
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    public function delete($where)
    {
        $modelDetalle = new Base_Model_DbTable_ArticulosListasDePreciosDetalle();

        $modelListasDePrecios = new Base_Model_DbTable_ArticulosListasDePrecios();
        $rowset = $modelListasDePrecios->fetchAll($where);

        foreach ($rowset as $row) {
            $modelDetalle->delete('ListaDePrecio = ' . $row->Id);
            parent::delete('Id = ' . $row->Id);
        }
    }
    
    protected $_dependentTables = array('Base_Model_DbTable_ArticulosListasDePreciosDetalle');

}