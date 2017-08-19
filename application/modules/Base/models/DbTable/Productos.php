<?php

class Base_Model_DbTable_Productos extends Base_Model_DbTable_Articulos
{
/**
 * Base_Model_DbTable_Productos
 *
 * Productos
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Base
 * @class Base_Model_DbTable_Productos
 * @extends Base_Model_DbTable_Articulos
 */

    protected $_name = 'Articulos';
    protected $_permanentValues = array(
    	'Tipo' => 1,
    	'EsMateriaPrima' => 1
    );

    /**
     * Retorna un array con las caracteristicas y sus respectivos valores dado el id del producto
     * @return array(
     * 		'valores' =>array (
     * 			'color' => 'rojo'
     * 		 ),
     * 		'campos' => array (
     * 			'color' => 3	//Tipo de campo
     * 		)
     * 	)
     */
    public function getCaracteristicas($id)
    {
        $campos = $this->_db->fetchAll("
                        SELECT C.Descripcion ,C.TipoDeCampo
                        FROM  ProductosCategorias PC
                            inner join Productos P
                                on PC.Id = P.ProductoCategoria
                            inner join ProductosCategoriasCaracteristicas PCC
                                on PC.Id = PCC.ProductoCategoria
                            inner join Caracteristicas C
                                on PCC.Caracteristica = C.Id
                        WHERE PCC.ProductoCategoria = P.ProductoCategoria
                            AND P.Id = $id
		");

        $valores = $this->_db->fetchPairs("
                        SELECT C.Descripcion, PCCV.Valor
                        FROM ProductosCategoriasCaracteristicas PCC
                            inner join Caracteristicas C
                                on PCC.Caracteristica = C.Id
                            inner join ProductosCategoriasCaracteristicasValores PCCV
                                on PCCV.ProductoCategoriaCaracteristica = PCC.Id
                            inner join Productos P
                                on P.Id = $id
                        WHERE PCC.ProductoCategoria = P.ProductoCategoria
                            AND PCCV.Producto = $id
		");

        return array('valores' => $valores, 'campos' => $campos);
    }



}