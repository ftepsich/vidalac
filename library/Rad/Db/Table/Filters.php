<?php
class Rad_Db_Table_Filters_Exception extends Rad_Exception{}

class Rad_Db_Table_Filters
{
	private   $_model = null;
	protected $_cols  = null;
	
	private $_filters = array();
	
	/**
	 * Constructor de la clase
	 */
	public function __construct($model)
	{
		$this->_model = $model;
		$this->_cols  = $model->getMetadataWithJoins();
	}
	
	public function clearFilters()
	{
		$this->_filters = array();
	}
	
	/**
	 *	Agrega los filtros
	 */
	public function addFilter($filters) 
	{
		if (is_array($filters)) {
			$this->addFilterFromArray($filters);
		} elseif ($filters instanceof Rad_Db_Table_Filter_Abstract) {
			if ($this->_getFieldType($filters->getField())) {
				$this->_filters[] = $filters;
			} else {
				throw new Rad_Db_Table_Filters_Exception("No existe el campo `{$filters->getField()}`en el modelo");
			}
		}
	}
	
	protected function addFilterFromArray($filters)
	{
		$field = $filters[0];

		$filterType = $this->_fetchFilterMap[$this->_getFieldType($field)];

		if (!$filterType) throw new Rad_Db_Table_Filters_Exception('No existe el mapeo para el tipo '.$this->_getFieldType($field));
		
		$class = 'Rad_Db_Table_Filter_'.$this->_fetchFilterMap[$this->_getFieldType($field)];
		
		$this->_filters[] = new $class(
			$this->_getNormalizedField($field), 
			$filters[1],
			$filters[2],
			$this->_model->getAdapter()
		);
	}
	
	protected function _getNormalizedField($field)
	{
		return $this->_getFieldTable($field).'.'.$field;
	}
	
	protected function _getFieldType($field) 
	{
		return $this->_cols[$field]['DATA_TYPE'];
	}
		
	protected function _getFieldTable($field) 
	{
		if ($this->_cols[$field]['TABLE_ALIAS']) {
			return $this->_cols[$field]['TABLE_ALIAS'];
		} else {
			return $this->_cols[$field]['TABLE_NAME'];
		}
	}
	
	public function appendFilters($select)
	{
		foreach ($this->_filters as $filter) {
			$filter->appendFilterSql($select);
		}
	}
	
	/**
     * Define los Rad_Db_Table_Filter por defecto segun el tipo de campo de la DB
     *
     * @var array
     */
	private $_fetchFilterMap = array (
		'tinyint'  => 'Bool',
    	'int'      => 'Int',
    	'decimal'  => 'Int',
    	'date'     => 'Date',
    	'datetime' => 'Datetime',
    	'bit'      => 'Bool',
    	'varchar'  => 'String',
	);
}