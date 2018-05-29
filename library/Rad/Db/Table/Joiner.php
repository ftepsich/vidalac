<?php
/** 
 * @copyright SmartSoftware Argentina
 * @package Rad
 * @subpackage Db_Table
 * @author Martin Alejandro Santangelo
 */

/**
 * Rad_Db_Table_Joiner
 *
 * Uso
 *   $joiner = $facturas->getJoiner();
 *
 *   $joiner->joinRef('LetrasDeFacturas', array('Descripcion'))
 *          ->joinRef('Proveedores', array('Provedor', 'Id'))
 *             ->with('Proveedores')
 *             ->joinRef('TipoDeProveedores', array('Descripcion'))
 *             ->joinRef('TipoDeInscripcion', array('Descripcion'));
 * 
 * @copyright SmartSoftware Argentina
 * @package Rad
 * @subpackage Db_Table
 * @author Martin Alejandro Santangelo
 */
class Rad_Db_Table_Joiner
{
    /**
     * @var array
     */
    protected $_joinsMetaData;
    /**
     * @var Rad_Db_Table
     */
    protected $_model;
    /**
     * @var array
     */
    protected $_joins = array();
    /**
     * @var string
     */
    protected $_alias;

    public function fieldExist()
    {
        return true;
    }

    protected function getModel()
    {
        $this->_model;
    }

    /**
     * retorna el array de Joins para el DbTable
     * @return array
     */
    public function getDbJoins()
    {
        $dbJoins = array();
        
        foreach ($this->_joins as $key => $join) {
            $join->buildJoins($dbJoins);
        }

        return $dbJoins;
    }

    public function clear()
    {
        foreach ($this->_joins as $key => $join) {
            $join->clear();
        }
        $this->_joins = array();
    }

    /**
     * retorna el array de Joins para el DbTable
     * @return array
     */
    public function getJoinsMetadata()
    {
        $dbJoinsMeta = array();
        foreach ($this->_joinsMetaData as $field => $meta) {
            $dbJoinsMeta[$field] = $meta;
        }
        
        foreach ($this->_joins as $key => $join) {
            $join->getJoinsMetadata($dbJoinsMeta);
        }

        return $dbJoinsMeta;
    }

    /**
     * Constrcutor
     * 
     * @param Rad_Db_Table $model
     */
    public function __construct(Rad_Db_Table $model)
    {
        $this->_model  = $model;
        $this->_joins  = array();
    }

    /**
     * retorna un join dado su alias
     * @param string $alias
     */
    public function with($alias)
    {
        return $this->_joins[$alias];
    }

    protected function _replaceTagsRef($alias, &$field, $rule)
    {
        $field = str_replace('{remote}', $alias, $field);
        $field = str_replace('{local}', $this->getModelAlias(), $field);
        
        foreach ($rule[Zend_Db_Table_Abstract::COLUMNS] as $k => $v) {
            $field = str_replace("{lField$k}",$v, $field);
        }
        
        foreach ($rule[Zend_Db_Table_Abstract::REF_COLUMNS] as $k => $v) {
            $field = str_replace("{rField$k}",$v, $field);
        }

    }

    protected function _replaceTagsDep($alias, &$field, $rule)
    {
        $field = str_replace('{remote}', $alias, $field);
        $field = str_replace('{local}', $this->getModelAlias(), $field);
        
        foreach ($rule[Zend_Db_Table_Abstract::COLUMNS] as $k => $v) {
            $field = str_replace("{rField$k}",$v, $field);
        }
        
        foreach ($rule[Zend_Db_Table_Abstract::REF_COLUMNS] as $k => $v) {
            $field = str_replace("{lField$k}",$v, $field);
        }

    }

    /**
     * Crea un Join con un modelo al que este hace referencia
     * 
     * @param string $refName
     * @param array  $fields
     */
    public function joinRef($refName, $fields)
    {

        $rule       = $this->_model->getReferenceByRule($refName);
        $modelClass = $rule['refTableClass'];

        // Instancio el modelo
        $refModel   = new $modelClass;
        // obtengo metadatos
        $refTableMetaData = $refModel->info();
        // armo las condiciones del join
        $conditions = $this->_makeConditions($rule, $refName);

        $normalizedDescFields = array();

        foreach ($fields as $k => $field) {
            // fix para que no agregue los nombres adelante de los campos con _cdisplay
            // TODO: quitar esto, adaptar el resto del sistema a los nombres con la nomenclatura comun


            if (!isset($refTableMetaData['metadata'][$field])) {
                //throw new Rad_Exception("Db Table Joiner: El campo $field no pertenece al modelo ".get_class($this->_model));
                // si no le puso alias
                if (is_int($k)) {
                    throw new Rad_Exception('Por ser calculado debe especificar un alias para '.$field);
                }
                $joinedName = $refName . $k;

                $this->_replaceTagsRef($this->getModelAlias().$refName, $field, $rule);
                $this->_joinsMetaData[$joinedName]['CALCULATED_FIELD'] = true;
            } else {
                if (!is_int($k) && $k == '_cdisplay') {
                    $joinedName = $rule[Zend_Db_Table_Abstract::COLUMNS][0].'_cdisplay';
                } else {
                    $joinedName = $this->_alias.$refName . (is_int($k) ? $field : $k);    
                }
                // Metadatos de los campos joineados
                $this->_joinsMetaData[$joinedName] = $refTableMetaData['metadata'][$field];
                $this->_joinsMetaData[$joinedName]['JOINED_FIELD'] = true;
                $this->_joinsMetaData[$joinedName]['TABLE_ALIAS']  = $this->_alias.$refName;
                $this->_joinsMetaData[$joinedName]['TABLE']        = $refModel->getName();
                $this->_joinsMetaData[$joinedName]['REF_COLUMNS']  = $rule[Zend_Db_Table_Abstract::COLUMNS];
            }
            $normalizedDescFields[$joinedName] = $field;
        }

        // agrego los metadatos de los campos de la tabla principal
        foreach ($rule[Zend_Db_Table_Abstract::COLUMNS] as $key => $field) {
            if (!$this->_fields || in_array($field, $this->_fields)){
                $this->_joinsMetaData[$this->_alias.$field]['JOINED_COLUMNS']   = $normalizedDescFields;
                $this->_joinsMetaData[$this->_alias.$field]['JOIN_REF_COLUMNS'] = $rule[Zend_Db_Table_Abstract::REF_COLUMNS];
            }
        }


        $join = new Rad_Db_Table_Joiner_Join($refModel, $conditions, $normalizedDescFields, $this->_alias.$refName);

        $this->_joins[$refName] = $join;

        return $this;
    }

    /**
     * Crea un Join con un modelo que hace referencia a este
     * 
     * @param string|Rad_Db_Table  $refClass Clase de la tabla dependiente
     * @param array                $fields   Campos a joinear
     * @param string|array         $refName  Nombre de la referencia de la tabla dependiente a la local
     */
    public function joinDep($refClass, $fields, $refName = null, $conditions = null, $group = null)
    {

        if ($refClass instanceof Rad_Db_Table) {
            $refModel = $refClass;
            $refClass = get_class($refModel);
        } else if(is_string($refClass)){
            $refModel = new $refClass; 
        } else {
            throw new InvalidArgumentException('Rad_Db_Table_Joiner::joinDep => el parametro $refClass debe ser string o una instancia de Rad_Db_Table');
        }

        // si la condicion adicional viene como string la paso a array
        if ($conditions && is_string($conditions)) {
            $conditions = (array) $conditions;
        }
        
        if ($refName) {
            $rule = $refModel->getReferenceByRule($refName);
        } else {
            $mString = get_class($this->_model);
            $r = $refModel->getReferenceAndRule($mString);
            if (!$r) {
                throw new Rad_Exception("El modelo $refClass no hace referencia a ".$mString);
            }
            $rule    = $r['reference'];
            $refName = $r['rule'];
        }
        
        // obtengo metadatos
        $refTableMetaData = $refModel->info();
        // armo las condiciones del join
        $conditions = $this->_makeConditionsDep($rule, $refName, $refModel, $conditions);

        $normalizedDescFields = array();

        foreach ($fields as $k => $field) {

            // es calculado?

            if (!isset($refTableMetaData['metadata'][$field])) {
                // si no le puso alias
                if (is_int($k)) {
                    throw new Rad_Exception('Por ser calculado debe especificar un alias para '.$field);
                }
                //$joinedName = $this->_alias . $refName . $refModel->getName() . $k;
                $joinedName = $k;

                $this->_joinsMetaData[$joinedName]['CALCULATED_FIELD'] = true;
                $this->_replaceTagsDep($refName.$refModel->getName(), $field, $rule);
    
            } else {
                $joinedName =  (is_int($k) ? ($this->_alias . $refName . $refModel->getName() . $field) : $k);
                // Metadatos de los campos joineados
                $this->_joinsMetaData[$joinedName] = $refTableMetaData['metadata'][$field];
                $this->_joinsMetaData[$joinedName]['JOINED_FIELD'] = true;
                $this->_joinsMetaData[$joinedName]['TABLE_ALIAS']  = $this->_alias.$refName.$refModel->getName();
                $this->_joinsMetaData[$joinedName]['TABLE']        = $refModel->getName();
                $this->_joinsMetaData[$joinedName]['REF_COLUMNS']  = $rule[Zend_Db_Table_Abstract::COLUMNS];
            }
            $normalizedDescFields[$joinedName] = $field;
        }

        if ($group === null) {
            $group = self::_makeGroupDep($rule, $refName);
        }

        $join = new Rad_Db_Table_Joiner_JoinDep(
            $refModel, $conditions, $normalizedDescFields, $this->_alias.$refName, $group
        );

        $this->_joins[$refClass] = $join;

        return $this;
    }

    public function getModelAlias()
    {
        return ($this->_alias)?$this->_alias:$this->_model->getName();
    }


    /**
     * Crea las condiciones para el join dada una regla del referenceMap $rule y un nombre de referencia
     * 
     * @param array  $rule
     * @param string $refName
     */
    protected function _makeConditions(array $rule, $refName)
    {
        $modelName  = $this->getModelAlias();

        $conditions = '';

        foreach ($rule[Zend_Db_Table_Abstract::REF_COLUMNS] as $k => $field) {

            if ($conditions) {
                $conditions .= " and ";
            }
            $conditions .= $this->_alias.$refName . ".$field = $modelName." . $rule[Zend_Db_Table_Abstract::COLUMNS][$k];
        }
        return $conditions;
    }

    /**
     * Crea las condiciones para el join dada una regla del referenceMap $rule y un nombre de referencia
     * 
     * @param array        $rule
     * @param string       $refName
     * @param Rad_Db_Table $refModel
     * @param array|null   $conditions
     */
    protected function _makeConditionsDep(array $rule, $refName, $refModel,  $conditions)
    {
        $modelName  = $this->getModelAlias();

        //condiciones adicionales
        if (!$conditions) {
            $conditions = array();    
        } else {
            foreach ($conditions as $k => $c) {

                $this->_replaceTagsDep($this->_alias.$refName.$refModel->getName(), $conditions[$k], $rule);
            }
        }

        foreach ($rule[Zend_Db_Table_Abstract::REF_COLUMNS] as $k => $field) {

            $conditions[]= $modelName . ".$field = {$this->_alias}{$refName}{$refModel->getName()}." . $rule[Zend_Db_Table_Abstract::COLUMNS][$k];
        }
        return implode(' and ', $conditions);
    }

    protected function _makeGroupDep(array $rule, $refName)
    {
        $groups = array();

        $modelName  = $this->getModelAlias();

        foreach ($rule[Zend_Db_Table_Abstract::REF_COLUMNS] as $k => $field) {

            $groups[] = $modelName . ".$field";

        }
        return implode(',', $groups);
    }
}