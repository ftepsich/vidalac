<?php
/** 
 * @copyright SmartSoftware Argentina
 * @package Rad
 * @subpackage Db_Table
 * @author Martin Alejandro Santangelo
 */

require_once 'Rad/Db/Table/Joiner.php';

/**
 * Rad_Db_Table_Join
 * 
 * Representa un Join con la Tabla $_model, que trae los campos $_fields
 * 
 * Brinda tambien la posibilidad de Crear nuevos joins desde este modelo a los referenciales
 *
 * @copyright SmartSoftware Argentina
 * @package Rad
 * @subpackage Db_Table
 * @author Martin Alejandro Santangelo
 */
class Rad_Db_Table_Joiner_Join extends Rad_Db_Table_Joiner
{
    /**
     * @var array
     */
    protected $_fields;
    /**
     * @var string
     */
    protected $_conditions;
    /**
     * @var string
     */
    protected $_group;

    public function fieldExist($field)
    {
        return in_array($field, $this->_fields);
    }

    public function buildJoins(&$j)
    {
        $this->_buildJoin($j);

        foreach ($this->_joins as $key => $join) {
            $join->buildJoins($j);
        }
    }

    /**
     * retorna el array de metadatos de los campos Joineados para el DbTable
     * @return array
     */
    public function getJoinsMetadata( &$dbJoinsMeta )
    {   
        foreach ($this->_joinsMetaData as $field => $meta) {
            $dbJoinsMeta[$field] = $meta;
        }
        
        foreach ($this->_joins as $key => $join) {
            $join->getJoinsMetadata($dbJoinsMeta);
        }
    }

    protected function _buildJoin(&$j)
    {
        $join = array();

        if ($this->_alias) {
            $join[] = array($this->_alias => $this->_model->getName());
        } else {
            $join[] = $this->_model->getName();
        }

        $join[] = $this->_conditions;
        $join[] = $this->_fields;
        
        if ($this->_group) {
            $join[] = $this->_group;
        }

        $j[] = $join;
    }

    public function getModel()
    {
        return $this->_model;
    }

    public function getFields()
    {
        return $this->_fields;
    }
    
    public function __construct(Rad_Db_Table $model, $conditions, $fields = null, $alias = null, $group = null)
    {
        parent::__construct($model);

        $this->_fields     = $fields;
        $this->_alias      = $alias;
        $this->_conditions = $conditions;
        $this->_group      = $group;   
    }
}