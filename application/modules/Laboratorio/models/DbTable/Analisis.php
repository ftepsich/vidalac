<?php
require_once('Rad/Db/Table.php');
/**
 *
 * Laboratorio_Model_DbTable_Analisis
 *
 * Analisis
 *
 *
 * @package 	Aplicacion
 * @subpackage 	Laboratorio
 * @class 		Laboratorio_Model_DbTable_Analisis
 * @extends		Rad_Db_Table
 * @copyright   SmartSoftware Argentina 2010
 */
class Laboratorio_Model_DbTable_Analisis extends Rad_Db_Table
{
	protected $_name = "Analisis";
	protected $_sort = array ("TipoAnalisis asc","Descripcion asc");
	
  
	// Inicio  protected $_referenceMap --------------------------------------------------------------------------
    protected $_referenceMap    = array(
        'TiposDeAnalisis' => array(
            'columns'           => 'TipoAnalisis',
            'refTableClass'     => 'Laboratorio_Model_DbTable_TiposDeAnalisis',
     		'refJoinColumns'    => array("Descripcion"),                     
     		'comboBox'			=> true,                                     
     		'comboSource'		=> 'datagateway/combolist',
            'refTable'			=> 'TiposDeAnalisis',
            'refColumns'        => 'Id'
		),
		'TiposDeCampos' => array(
            'columns'           => 'TipoDeCampo',
            'refTableClass'     => 'Base_Model_DbTable_TiposDeCampos',
     		'refJoinColumns'    => array("Descripcion"),                    
     		'comboBox'			=> true,                                    
     		'comboSource'		=> 'datagateway/combolist',
            'refTable'			=> 'TiposDeCampos',
            'refColumns'        => 'Id'
		),
     
    
	);
	// fin  protected $_referenceMap -----------------------------------------------------------------------------

	protected $_dependentTables = array(
		'Laboratorio_Model_DbTable_AnalisisValoresListas',
		'Laboratorio_Model_DbTable_AnalisisProtocolo',
		'Laboratorio_Model_DbTable_AnalisisModelos'
	);	
	
}