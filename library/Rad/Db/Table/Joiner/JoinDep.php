<?php
/** 
 * @copyright SmartSoftware Argentina
 * @package Rad
 * @subpackage Db_Table
 * @author Martin Alejandro Santangelo
 */

require_once 'Rad/Db/Table/Joiner/Join.php';

/**
 * Rad_Db_Table_JoinDep
 * 
 *
 * @copyright SmartSoftware Argentina
 * @package Rad
 * @subpackage Db_Table
 * @author Martin Alejandro Santangelo
 */
class Rad_Db_Table_Joiner_JoinDep extends Rad_Db_Table_Joiner_Join
{
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
            $join[] = array($this->_alias.$this->_model->getName() => $this->_model->getName());
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
    
    public function __construct(Rad_Db_Table $model, $conditions, $fields = null, $alias = null, $group = null)
    {
        parent::__construct( $model, $conditions, $fields, $alias, $group);

        $this->_fields     = $fields;
        $this->_alias      = $alias;
        $this->_conditions = $conditions;
        $this->_group      = $group;   
    }

    public function getModelAlias()
    {
        return $this->_alias.$this->_model->getName();
    }

}