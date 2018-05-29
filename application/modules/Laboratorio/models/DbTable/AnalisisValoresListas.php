<?php
require_once('Rad/Db/Table.php');
/**
 *
 * Laboratorio_Model_DbTable_AnalisisValoresListas
 *
 * Analisis Valores listas
 *
 *
 * @package 	Aplicacion
 * @subpackage 	Laboratorio
 * @class 		Laboratorio_Model_DbTable_AnalisisValoresListas
 * @extends		Rad_Db_Table
 * @copyright   SmartSoftware Argentina 2010
 */
class Laboratorio_Model_DbTable_AnalisisValoresListas extends Rad_Db_Table
{
	// Tabla mapeada
	protected $_name = "AnalisisValoresListas";

	// Relaciones
    protected $_referenceMap    = array(
        
	        'Analisis' => array(
            'columns'           => 'Analisis',
            'refTableClass'     => 'Laboratorio_Model_DbTable_Analisis',
     		'refJoinColumns'    => array('Descripcion'),
     		'comboBox'			=> true,
     		'comboSource'		=> 'datagateway/combolist',
            'refTable'			=> 'Analisis',
            'refColumns'        => 'Id',
        )    
	);
	
	protected $_dependentTables = array();	
	
}

