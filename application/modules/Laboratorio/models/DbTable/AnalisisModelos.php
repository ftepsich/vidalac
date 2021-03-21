<?php
require_once('Rad/Db/Table.php');
/**
 *
 * Laboratorio_Model_DbTable_AnalisisModelos
 *
 * Anlisis  modelos
 *
 *
 * @package 	Aplicacion
 * @subpackage 	Laboratorio
 * @class 		Laboratorio_Model_DbTable_AnalisisModelos
 * @extends		Rad_Db_Table
 * @copyright   SmartSoftware Argentina 2010
 */
class Laboratorio_Model_DbTable_AnalisisModelos extends Rad_Db_Table
{
	protected $_name = "AnalisisModelos";
		
	// Inicio  protected $_referenceMap --------------------------------------------------------------------------
    protected $_referenceMap    = array(
        'AnalisisTiposModelos' => array(
            'columns'           => 'AnalisisTipoModelo',
            'refTableClass'     => 'Laboratorio_Model_DbTable_AnalisisTiposModelos',
     		'refJoinColumns'    => array("Descripcion"),                    
     	     'comboBox'			=> true,                                     
     		'comboSource'		=> 'datagateway/combolist',
            'refTable'			=> 'AnalisisTiposModelos',
            'refColumns'        => 'Id',
     		'comboPageSize'     => 20            //DEFINE EL TAMAÑO DE LA PAGINA DEL COMBO (Se arma un combo de busqueda)
			
        ),
        'Analisis' => array(
            'columns'           => 'Analisis',
            'refTableClass'     => 'Laboratorio_Model_DbTable_Analisis',
     		'refJoinColumns'    => array("Descripcion"),                     
       		'comboBox'			=> true,                                    
     		'comboSource'		=> 'datagateway/combolist',
            'refTable'			=> 'Analisis',
            'refColumns'        => 'Id',
            'comboPageSize'     => 20            //DEFINE EL TAMAÑO DE LA PAGINA DEL COMBO (Se arma un combo de busqueda)
        )
		 
);
	// fin  protected $_referenceMap -----------------------------------------------------------------------------
	
	// Inicio Public Init ----------------------------------------------------------------------------------------
    public function init()     {
		$this -> _validators = array(
		'Analisis'=> array(
                array('Db_NoRecordExists',
                        'AnalisisModelos', //
                        'Analisis',
                        'AnalisisTipoModelo = '.$this->_db->quote($_POST["AnalisisTipoModelo"]).
                        ' and Analisis = '.$this->_db->quote($_POST["Analisis"]).
                        ' and Id <> '.$this->_db->quote(($_POST["Id"])?$_POST["Id"]:($_POST["node"]?$_POST["node"]:0))
                )
            )
		);
		parent::init();
	}
	// fin Public Init -------------------------------------------------------------------------------------------
	/*
	public function insert($data)
	{
	    Zend_Wildfire_Plugin_FirePhp::send($data);
	    $this->_db->beginTransaction();
	    try {
	        $id = parent::insert($data);
	        //TODO: Ver si se puede implementar usando relaciones en vez del find
	        $bienesDelInventario = new Model_DbTable_BienesDelInventario(array(),false);
	        $bienesDelInventarioCaracteristicas = new Model_DbTable_BienesDelInventarioCaracteristicas(array(),false);

	        $bienesDelInventario = new Model_DbTable_BienesDelInventario(array(),false);
	        $bienesDelInventarioCaracteristicas = new Model_DbTable_BienesDelInventarioCaracteristicas(array(),false);

	        
	        $Bienes = $bienesDelInventario->fetchAll("Bien = ".$this->_db->quote($data['Bien']));
	        foreach ($Bienes as $Bien) {
	            $caracteristicasDelBien = array(
	                'BienDelInventario' => $Bien->Id,
	                'BienCaracteristica' => $id
	            );
	            $row = $bienesDelInventarioCaracteristicas->createRow($caracteristicasDelBien);
	            $row->save();
	            //$bienesDelInventarioCaracteristicas->insert($caracteristicasDelBien);
	        }
	        $this->_db->commit();
	        return $id;
	    } catch (Exception $e) {
	        $this->_db->rollBack();
	        throw $e;
	    }
	}
	*/
}
