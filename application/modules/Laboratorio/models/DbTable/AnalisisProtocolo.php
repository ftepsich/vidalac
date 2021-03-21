<?php
require_once('Rad/Db/Table.php');
/**
 *
 * Laboratorio_Model_DbTable_AnalisisProtocolo
 *
 * Anlisis Protocolo
 *
 *
 * @package 	Aplicacion
 * @subpackage 	Laboratorio
 * @class 		Laboratorio_Model_DbTable_AnalisisProtocolo
 * @extends		Rad_Db_Table
 * @copyright   SmartSoftware Argentina 2010
 */
class Laboratorio_Model_DbTable_AnalisisProtocolo extends Rad_Db_Table
{
	protected $_name = "AnalisisProtocolo";
	
	// Inicio  protected $_referenceMap --------------------------------------------------------------------------
    protected $_referenceMap    = array(
        'Muestras' => array(
            'columns'           => 'Muestra',
            'refTableClass'     => 'Laboratorio_Model_DbTable_AnalisisMuestras',
     		'refColumns'        => 'Id',
     		'refJoinColumns'    => array("Identificacion"),                     
     		'comboBox'			=> true,                                     
     		'comboSource'		=> 'datagateway/combolist',
            'refTable'			=> 'AnalisisMuestras',
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
        ),
);
	// fin  protected $_referenceMap -----------------------------------------------------------------------------


	
}