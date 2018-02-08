<?php

class Model_ImportarBancosMovimientos
{

	public function setData ($data)
	{
		$this->data = $data;
	}
	
	public function setBanco ($banco)
	{
		$this->banco = $banco;
	}
	
	public function setFieldFormat ($order, $type, $column, $columnId, $format)
	{
		$this->fields[$order-1] = array (	'type'		=> $type,
											'column'	=> $column,
											'columnId'	=> $columnId,
											'format'	=> $format);
	}
	
	public function setDelimiter ($delimiter)
	{
		$this->delimiter = str_replace('TAB', "\t", $delimiter);
	}

	public function setDecimalSeparator ($separator)
	{
		$this->decimalSeparator = ($separator) ? $separator : ',';
	}
	
	public function getParsed ()
	{
		setlocale(LC_TIME, 'es_AR.UTF-8');
		$toFormat = (function_exists('str_getcsv')) ? str_getcsv($this->data, $this->delimiter) : Rad_CustomFunctions::str_getcsv($this->data, $this->delimiter);
		
		//Zend_Wildfire_Plugin_FirePhp::send($toFormat);
		
		try {
			$db = Zend_Registry::get('db');
			$db->beginTransaction();
			
			$datos = array();

			// Itera sobre los registros			
			foreach ($toFormat as &$reg) {
				
				$dato = array();
				$dato['Banco'] = $this->banco;
				
				// Itera sobre los campos configurados
				foreach ($this->fields as $i => $datafield)
				{
					// Ignora registro cuando la columna Concepto contiene el texto 'Saldo Inicial'
					if (($datafield['column'] == 'Concepto') && (strpos($reg[$i],'Saldo Inicial') !== false)) continue;
					
					switch ($datafield['type']) {
						// -------------------------------------------------------------------------------------
						// --------------------------------------- Texto ---------------------------------------
						case 1:
							$dato[$datafield['column']] = trim(str_replace('"',null,$reg[$i]));
							break;
						// -------------------------------------------------------------------------------------
						// --------------------------------------- Entero --------------------------------------
						case 2:
							// Saco las comas, puntos, comillas y dobles comillas
							$dato[$datafield['column']] = str_replace(array("'",'"',',','.'),null,$reg[$i]);
							if (!$reg[$i]) unset($reg[$i]);
							
							// Si no es numerico, null, o no esta seteado, corto el proceso							
							if (!is_numeric($dato[$datafield['column']]) && !is_null($dato[$datafield['column']]) && !isset($dato[$datafield['column']]))
								throw new Zend_Exception("Formato incorrecto: {$datafield['column']} debe ser un Entero");
								
							break;
						// -------------------------------------------------------------------------------------
						// --------------------------------------- Decimal -------------------------------------
						case 3:
							// Saco las comillas y dobles comillas
							$reg[$i] = str_replace(array("'",'"'), null, $reg[$i]);
							
							if (!$reg[$i] || in_array($reg[$i], array(0, 0.00)))
								unset($reg[$i]);
							
							// Formateo decimal valido
							switch ($this->decimalSeparator) {
								case ',':
									$decimal = str_replace(',','.', str_replace('.', null, $reg[$i]));
									break;
								case '.':
									$decimal = str_replace(',', null, $reg[$i]);
									break;
							}
							
							if (!is_numeric($decimal) && !is_null($decimal) && !isset($decimal))
								throw new Zend_Exception("Formato incorrecto: {$datafield['column']} debe ser un Decimal");
							
							// Proceso segun el tipo de columna
							switch ($datafield['columnId']) {
								case 7:
									if ($decimal < 0) {
										$dato['Debito'] = $decimal * (-1);
									} else if ($decimal > 0) {
										$dato['Credito'] = $decimal;
									}
									break;
								case 3:
								case 4:
									if ($decimal < 0) $decimal *= -1;
									$dato[$datafield['column']] = $decimal;
									break;
								default:
									$dato[$datafield['column']] = $decimal;
									break;
							}
							
							break;
						// -------------------------------------------------------------------------------------
						// --------------------------------------- Fecha ---------------------------------------
						case 4:
							// Si no se setea el Formato de Fecha toma por defecto d/m/Y
							$formato = ($datafield['format']) ? $datafield['format'] : '%d/%m/%Y';
							
							// Si no se pudo convertir la fecha en el formato establecido corto el proceso
							if (!($fecha = strptime($reg[$i], $formato)))
								throw new Zend_Exception("Formato incorrecto: {$datafield['column']} debe ser una fecha con formato '{$formato}'");
								
							// Formato compatible mysql
							$dato[$datafield['column']] = sprintf("%04d-%02d-%02d", $fecha['tm_year']+1900, $fecha['tm_mon']+1, $fecha['tm_mday']);
							break;
							
						default:
							break;
					}
				}
				if (count($dato)) $datos[] = $dato;
			}
			
			$detalle = $this->_insert($datos);
			$db->commit();
			
			return $detalle;
			
		} catch (Exception $e) {
			$db->rollBack();
			throw $e;
		}
	}
	
	private function _insert ($data)
	{
		$bm = new Model_DbTable_BancosMovimientos(array(), false);
		
		$respuesta = array('success' => true, 'total' => 0, 'importados' => 0, 'omitidos' => 0);
		
		foreach ($data as $item) {
			
			$where = array();
			foreach ($item as $key => $value) {
				if (!$value) {
					$value = ' IS NULL';
				} else if (is_string($value) && !is_numeric($value)) {
					$value = " = '$value'";
				} else {
					$value = " = $value";
				}
				$where[] = $key.$value;
			}
			
			$registroImportado = $bm->fetchRow($where);
			if (!$registroImportado) {
				$bm->insert($item);
				$respuesta['importados']++;
			} else {
				Zend_Wildfire_Plugin_FirePhp::send($item);
				$respuesta['omitidos']++;
			}
		}
		$respuesta['total'] = $respuesta['importados'] + $respuesta['omitidos'];
		
		return $respuesta;
	}
	
}
