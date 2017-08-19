<?php
require_once 'Rad/Window/Controller/Action.php';
// Todo: llevar esto otro archivo ya q otros tambien lo usan
define ('CAMPO_ENTERO',		1);
define ('CAMPO_DECIMAL',	2);
define ('CAMPO_FECHA',		3);
define ('CAMPO_TEXTO',		4);
define ('CAMPO_LISTA',		5);
define ('CAMPO_BOLEANO',	6);
define ('CAMPO_FECHAYHORA', 7);
/**
 * Laboratorio_AsignarResultadosDeAnalisisController
 *
 * Resultados de Analisis
 *
 * @package 	Aplicacion
 * @subpackage 	Laboratorio
 * @class 		Laboratorio_AsignarResultadosDeAnalisisController
 * @extends		Rad_Window_Controller_Action
 * @author      Martin A. Santangelo
 * @copyright   SmartSoftware Argentina 2010
 */
class Laboratorio_AsignarResultadosDeAnalisisController extends Rad_Window_Controller_Action
{
    protected $title = "Resultados de Analisis";
	
	protected function _getAnalisisEditors()
	{
		$modeloAnalisis = new Laboratorio_Model_DbTable_Analisis(array(), false);
		
		$analisis = $modeloAnalisis->fetchAll();
		$rtn = array();
		
		foreach ($analisis as $k => $car) {
			
			switch ($car->TipoDeCampo) {
				case CAMPO_ENTERO:
					$rtn[] = "'$car->Descripcion': new Ext.grid.GridEditor(new Ext.form.NumberField({selectOnFocus:true, decimalPrecision:0}))";
				break;
				case CAMPO_DECIMAL:
					$rtn[] = "'$car->Descripcion': new Ext.grid.GridEditor(new Ext.form.NumberField({selectOnFocus:true, decimalPrecision:6}))";
				break;
				case CAMPO_FECHA:
					$rtn[] = "'$car->Descripcion': new Ext.grid.GridEditor(new Ext.ux.form.XDateField({format:'d/m/Y'}))";
				break;
				case CAMPO_BOLEANO:
					$rtn[] = "'$car->Descripcion': new Ext.grid.GridEditor(
						new Ext.form.ComboBox({
						store: ['Si', 'No'], 
						triggerAction: 'all',
						selectOnFocus:true,
					}))";
				break;
				case CAMPO_LISTA:
					
					$lista = $car->findDependentRowset('Laboratorio_Model_DbTable_AnalisisValoresListas');
					$data = array();
					foreach ($lista as $v) {
						$data[] = ($v->Valor);
					}
					
					$store = json_encode($data);
					$rtn[] = "'$car->Descripcion': new Ext.grid.GridEditor(
						new Ext.form.ComboBox({
						store: $store, 
						typeAhead: true,
						triggerAction: 'all',
						selectOnFocus:true,
					}))";
					
				break;
				case CAMPO_FECHAYHORA:
					$rtn[] = "'$car->Descripcion':new Ext.grid.GridEditor( new Ext.ux.form.DateTime(new Ext.ux.form.XDateField({dateFormat:'d/m/Y', timeFormat: 'H:i:s'})))";
				break;
			}
		}
		$rtn = implode(',', $rtn);
		return '{'.$rtn.'}';
	}
	
	public function getanalisisAction()
	{
		$this->_helper->viewRenderer->setNoRender(true);
		$request = $this->getRequest();
		$id = $request->id;
		try	{
			$analisisMuestras = new Laboratorio_Model_DbTable_AnalisisMuestras(array(), false);
			$muestra  		  = $analisisMuestras->find($id)->current();
			
			if (!$muestra) throw new Rad_Exception('No se encontro la muestra');
			
			$db = $analisisMuestras->getAdapter();
			
			$analisis = $analisisMuestras->getAnalisis($muestra->Id);
			
			$dataf = array();
			foreach($analisis['campos'] as $k => $v) {
				$v['Valor'] = $analisis['valores'][$v['Descripcion']];
				
				if ($v['Valor'] === null){
					$v['Valor'] = '';
				} else if ($v['TipoDeCampo'] == 3 || $v['TipoDeCampo'] == 7 ) {
					$tmp = str_replace(array('-',' ',':'),',',$v['Valor']);
					$v['Valor'] = new Zend_Json_Expr("new Date($tmp)");
				}
				$dataf[$v['Descripcion']] = $v['Valor'];
			}
			
			echo "{success: true, data: ".Zend_Json::encode($dataf, false, array('enableJsonExprFinder' => true))."}";
		} catch (Rad_Db_Table_Exception $e) {
			echo "{success: false, msg: '".addslashes($e->getMessage()) ."'}";
	    }	
	}
    
    public function initWindow ()
    {
		// ----------------------------------------------------------------------------------------------------------
		// GRILLA HIJA
		// ----------------------------------------------------------------------------------------------------------

		$this->view->editors = $this->_getAnalisisEditors();

		// ----------------------------------------------------------------------------------------------------------
		// GRILLA PADRE
		// ----------------------------------------------------------------------------------------------------------

		$abmForm = $this->view->radForm(
			'Laboratorio_Model_DbTable_AnalisisMuestras',  				// Nombre del Modelo
			'datagateway'
		);
        $this->view->form = $abmForm;

			
        $grillaHijaP->abmWindowWidth	= 740;
		$grillaHijaP->abmWindowHeight	= 350;
		$grillaHijaP->abmWindowTitle	= 'Agregar muestras';
		$grillaHijaP->fetch         	= 'NoControlados';
		$grillaHijaP->abmForm			= new Zend_Json_Expr($abmForm);
		$grillaHijaP->sm                = new Zend_Json_Expr("
			new Ext.grid.RowSelectionModel({
				singleSelect: true,
				listeners: { 
					rowselect: function(i,id, r) {
						if (!r.data.Id) return;
						Rad.callRemoteJsonAction ({
							url: '/Laboratorio/Asignarresultadosdeanalisis/getanalisis',
							params: {
								id: r.data.Id
							},
							success: function (response) {
								g = Ext.getCmp('gridAnalisisValores');
								g.setSource(response.data);
							}
						});	
					}
				}
			})
		");

		$this->view->grid = $this->view->radGrid('Laboratorio_Model_DbTable_AnalisisMuestras' ,$grillaHijaP,'abmeditor');

    }
	
	public function savepropertyAction()
	{
		$this->_helper->viewRenderer->setNoRender(true);
		$request 	= $this->getRequest();
		$value 		= $request->value;
		$for   		= $request->id;
		$property   = $request->property;
		
		try	{
			$analisisMuestras = new Laboratorio_Model_DbTable_AnalisisMuestras(array(), false);
			$muestra 		  = $analisisMuestras->find($for)->current();
			if (!$muestra) throw new Rad_Exception('No se encontro la muestra');
			
			$analisisProtocolos = new Laboratorio_Model_DbTable_AnalisisProtocolo(array(), false);
			
			$analisisYvalores   = $analisisMuestras->getAnalisis($muestra->Id);
			
			//Existe la propiedad?
			$exist = false;
			foreach ($analisisYvalores['campos'] as $campo) {
				if ($campo['Descripcion'] == $property) {
					$exist = true;
					break;
				}
			}
			
			if (!$exist) throw new Rad_Exception('El analisis no esta asignado al producto');
			
			$db  	   = $analisisMuestras->getAdapter();
			//$value 	  = $db->quote($value);
			$for	   = $db->quote($for,'INTEGER');
			$qproperty = $db->quote($property);
			
		
			
			$row = $analisisProtocolos->fetchRow("Muestra = $for and Analisis = {$campo['Id']}");
			if (!$row) $row = $analisisProtocolos->createRow();
			
			//si es una Fecha acomodamos el valor
			if ($campo['TipoDeCampo'] == 3 || $campo['TipoDeCampo'] == 7) {
				$value = str_replace('T',' ', $value);
			}
			
			$row->Muestra = $for;
			$row->Valor    = $value;
			$row->Analisis = $campo['Id'];
			$row->save();
			
			echo "{success: true}";
		} catch (Exception $e) {
			echo "{success: false, msg: '".addslashes($e->getMessage()) ."'}";
	    }	
	}
}
