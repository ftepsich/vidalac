<?php
/**
 * Rad_Db_Table
 *
 * @copyright SmartSoftware Argentina
 * @package Rad
 * @subpackage Db_Table
 * @author Martin Alejandro Santangelo
 */
require_once 'Zend/Db/Table/Abstract.php';

/**
 * Rad_Db_Table
 *
 * Clase base para todos los modelos del sistema
 * Implementa autojoins con tablas relacionadas
 * Estos AutoJoins no solo traen los campos requeridos de las tablas relacionadas, sino tambien sus metadatos.
 * Los metadatos de campos relacionados se guardan con el nomber tablaCampo sin espacios ni puntos
 *
 * @copyright SmartSoftware Argentina
 * @package Rad
 * @subpackage Db_Table
 * @author Martin Alejandro Santangelo
 */
class Rad_Db_Table extends Zend_Db_Table_Abstract
{
    /**
     * Row Particular
     * @var string
     */
    protected $_rowClass = 'Rad_Db_Table_Row';
    protected $_rowsetClass = 'Rad_Db_Table_Rowset';

    /**
     * Define los validadores por defecto segun el tipo de campo de la DB
     *
     * @var array
     */
    private $_basicValidators = array(
        'tinyint'  => array('Int'),
        'bigint'   => array('Int'),
        'int'      => array('Int'),
        'decimal'  => array('Float'),
        'date'     => array('Date'),
        'datetime' => array(array('Date', 'Y-m-d H:i:s')),
        'time'     => array(array('Date', 'H:i')),
        'bit'      => array(array('Between', 0, 1))
            //'varchar' => array(),
            //'text'    => array(),
    );

    /**
     * @var Rad_Db_Table_Joiner
     */
    protected $_joiner;

    /**
     * Define los filtros de entrada por defecto segun el tipo de campo de la DB
     *
     * @var array
     */
    private $_basicFilters = array(
        /**
         * Si agrego este filtro guarda igual si se incluyen letras y numeros
         * pero el cliente no se entera hasta que recarga
         * Como la aplicacion trabaja por ajax por ahora no es conveniente.
         * Es preferible que salte el error del validador de arriba que es visible por el usuario
         */
        'tinyint'  => array('IntOrNull'),
        'int'      => array('IntOrNull'),
        'bigint'   => array('IntOrNull'),
        'decimal'  => array('FloatOrNull'),
        'date'     => array('StringOrNull'),
        'datetime' => array('StringOrNull')
    );

    /**
     * Valores fijos de columnas
     * Se agregan al where tanto en el insert, update y el fetch
     */
    protected $_permanentValues = array();

    /**
     * los campos contenidos en este array no seran modificables por un update
     */
    protected $_readOnlyFields  = array();

    /**
     * Define los filtros que tendra el modelo
     *
     * @var array
     */
    protected $_fetchFilters = null;
    /**
     * Almacena los mismos metadatos que $_metadata pero de las columnas joineadas de las otras tablas
     *
     * @var array
     */
    protected $_metadataJoins = array();
    /**
     * Almacena los  metadatos de los campos calculados
     *
     * @var array
     */
    protected $_metadataCalculated = array();
    /**
     * nombre del campo por el que se ordenanran los listados por defecto (Solo se usa en los datagateway)
     *
     * @var array
     */
    protected $_sort = null;
    /**
     * Por defecto toma los valores default de la base de datos
     * El campo debe estar como NOT NULL y tener un DEFAULT VALUE
     * @var unknown_type
     */
    protected $_defaultSource = self::DEFAULT_DB;
    /**
     * Especifica que la grilla agrupe automaticamente por el campo (Solo se usa en los datagateway)
     * TODO: sacar del modelo, esto es de visualizacion
     * @var string
     */
    protected $_gridGroupField = '';
    protected $_gridGroupFieldOrderDirection = 'ASC';

    /**
     * configuracion de los Joins configurados automaticamente segun la configuracion del modelo
     *
     * @var array
     */
    protected $_joins = array();

    /**
     * Realizar Joins configurados automaticamente en el fetchAll
     *
     * @var bool
     */
    protected $_fetchWithAutoJoins = false;
    /**
     * Realizar Joins configurados automaticamente en el fetchAll
     *
     * @var bool
     */
    protected $_fetchWithCalcFields = false;
    /**
     * Array con los campos calculados que se retornaran en el fetchAll pertenecientes a este modelo (no a los traidos por los joins)
     *
     * @var array
     */
    protected $_calculatedFields = array();

    /**
     * Array con los campos calculados locales con callback
     *
     * @var array
     */
    protected $_localCalculatedFields = array();
    /**
     * Array con los campos que se retornaran en el fetchAll pertenecientes a este modelo (no a los traidos por los joins)
     * Si esta vacio se muestran todos los campos
     *
     * @var array
     */
    protected $_visibleFields = array();
    /**
     * Filter array for insert/update methods
     * @var array
     */
    protected $_filters = null;
    /**
     * Validator array for insert/update methods
     * @var array
     */
    protected $_validators = null;
    /**
     * Almacena los validadores originales con los tags de los campos
     * @var array
     */
    protected $_validatorsOrig;

    /**
     * Indica que campos almacenan archivos anexados
     *
     * array(
     *     'Foto' => array (
     *          'validators' => array (
     *              array('Filessize', 25000),
     *              array('Extension', 'jpg,png')
     *          )
     *      )
     * )
     *
     * @var array
     */
    protected $_attachedFiles = array();

    /**
     * @return array
     */
    public function getAttachedFiles()
    {
        return $this->_attachedFiles;
    }

    /**
     * @return Rad_Db_Table_Filters retorna el contenedor de filtros
     */
    public function getFetchFilters ()
    {
        if (!$this->_fetchFilters) {
            $this->_fetchFilters = new Rad_Db_Table_Filters($this);
        }
        return $this->_fetchFilters;
    }

    /**
     * @return string|array
     */
    public function getSort ()
    {
        return $this->_sort;
    }

    /**
     * @param string|array $_sort
     */
    public function setSort ($_sort)
    {
        $this->_sort = $_sort;
    }

    /**
     * @return array
     */
    public function getAutoJoins ()
    {
        return $this->_joins;
    }

    public function getJoiner()
    {
        if (!$this->_joiner) {
            require_once 'Rad/Db/Table/Joiner.php';
            $this->_joiner = new Rad_Db_Table_Joiner($this);
        }

        return $this->_joiner;
    }

    /**
     * Dado un campo de la table $campo retorna la referencia a otra si esta existe
     * @param $campo
     */
    public function getFieldReference($campo)
    {
        foreach ($this->_referenceMap as $ref) {
            if ($ref['columns'] == $campo) {
                return $ref;
            }
        }
        return null;
    }

    public function getReferenceByRule($rule)
    {
        $refMap = $this->_getReferenceMapNormalized();

        if (!$refMap[$rule]) {
            throw new Rad_Db_Table_Exception("No existe una referencia con nombre: $rule");
        }

        return $refMap[$rule];
    }

    public function getReferenceAndRule($tableClassname)
    {
        $refMap = $this->_getReferenceMapNormalized();
        foreach ($refMap as $rule => $reference) {
            if ($reference[self::REF_TABLE_CLASS] == $tableClassname) {
                return array('rule' => $rule, 'reference' => $reference);
            }
        }
    }

    /**
     * Retorna los el mapa de relacion para las relaciones muchos a muchos
     *
     * @param Rad_Db_Table|string  $intersection
     * @param string              $match
     * @param string              $iRule
     * @param string              $mRule
     */
    public function getManyToManyRelationMap ($intersection, $match, $iRule = null, $mRule = null)
    {
        if ($intersection instanceof Rad_Db_Table) {
            $intersectionTable = $intersection;
        } else if(is_string($intersection)){
            $intersectionTable = new $intersection(array(), false);
        } else {
            throw new InvalidArgumentException('Rad_Db_Table::getManyToManyRelationMap => el parametro $intersection debe ser string o una instancia de Rad_Db_Table');
        }


        $ret['caller'] = $intersectionTable->getReference(get_class($this), $iRule);
        $ret['match']  = $intersectionTable->getReference($match, $mRule);
        $ret['table']  = $intersectionTable->getName();

        if ($ret['caller'] && $ret['match']) {
            return $ret;
        } else {
            return false;
        }
    }

    /**
     * @return string
     */
    public function getGridGroupFieldOrderDirection ()
    {
        return $this->_gridGroupFieldOrderDirection;
    }

    /**
     * @param string $_gridGroupFieldOrderDirection
     */
    public function setGridGroupFieldOrderDirection ($gridGroupFieldOrderDirection)
    {
        $this->_gridGroupFieldOrderDirection = $gridGroupFieldOrderDirection;
    }

    /**
     * @return string
     */
    public function getGridGroupField ()
    {
        return $this->_gridGroupField;
    }

    /**
     * @param string $_gridGroupField
     */
    public function setGridGroupField ($_gridGroupField)
    {
        $this->_gridGroupField = $_gridGroupField;
    }

    public function isFieldVisible ($field)
    {
        if (empty($this->_visibleFields))
            return true;
        return isset($this->_visibleFields[$field]);
    }

    /**
     * @return array
     */
    public function getVisibleFields ()
    {
        return $this->_visibleFields;
    }

    /**
     * @param array $_visibleFields
     */
    public function setVisibleFields ($_visibleFields)
    {
        $this->_visibleFields = array_combine($_visibleFields, $_visibleFields);
    }

    /**
     * Valida y Filtra los datos
     *
     * @param array $data
     * @param array $filters
     * @param array $validators
     * @return boolean / array Validation success / Error messages
     */
    protected function isValid (&$data, $options = null)
    {
        $this->_setFieldValuesToValidators($data);

        $input = new Zend_Filter_Input($this->_filters, $this->_validators, $data, $options);
        if ($input->hasInvalid()) {

            return $input->getInvalid();
        } else {
            if ($input->hasMissing()) {
                return $input->getMissing();
            } else {
                $data = $input->getUnescaped();
                return true;
            }
        }
    }

    protected function _setPermanentValues ($data)
    {
        $cols = $this->_getCols();
        foreach ($this->_permanentValues as $field => $value) {
            if (!in_array($field, $cols))
                throw new Rad_Db_Table_Exception("El campo `$field` al que intento setear un valor permanente no existe.");
            if (is_array($value)) {
                if (isset($data[$field]) && !in_array($data[$field], $value)) {
                    throw new Rad_Db_Table_Exception("El valor asigando a `$field` es invalido", array($field => array("El valor asigando a `$field` es invalido")));
                }
            } else {
                $data[$field] = $value;
            }
        }
        return $data;
    }

    protected function _appendPermanentValuesToWhere ($where)
    {
        $cols = $this->_getCols();

        foreach ($this->_permanentValues as $field => $value) {

            if (!in_array($field, $cols))
                throw new Rad_Db_Table_Exception("El campo `$field` al que intento setear un valor permanente no existe.");

            if (!is_array($value)) {
                if ($where instanceof Zend_Db_Table_Select) {
                    $where->where($this->_name . ".$field = ?", $value);
                } else if (is_array($where)) {
                    $where[] = $this->_db->quoteInto($this->_name . ".$field = ?", $value);
                } else {
                    $where = (($where) ? "$where AND " : '') . $this->_db->quoteInto($this->_name . ".$field = ?", $value);
                }
            } else {
                if ($where instanceof Zend_Db_Table_Select) {
                    $where->where($this->_name . ".$field IN (?)", $value);
                } else if (is_array($where)) {
                    $where[] = $this->_db->quoteInto($this->_name . ".$field IN (?)", $value);
                } else {
                    $where = (($where) ? "$where AND " : '') . $this->_db->quoteInto($this->_name . ".$field IN (?)", $value);
                }
            }
        }
        return $where;
    }

    /**
     * Setea los valores de los campos a los tags de los validadores
     * Por ejemplo:
     *  Si un validador tiene un tag {Id} este sera reemplazado por el valor del campo 'Id' del array de datos
     *
     * @param array $data
     */
    protected function _setFieldValuesToValidators ($data)
    {
        foreach ($data as $key => $value) {
            if ($value === null) {
                $data[$key] = 'null';
            } else {
                $data[$key] = $value;
            }
        }

        // Guardamos los validadores en un array temporal. sino una vez reemplazados los tags de los campos por sus valores los permdemos
        if (!$this->_validatorsOrig) {
            $this->_validatorsOrig = $this->_validators;
        } else {
            $this->_validators = $this->_validatorsOrig;
        }

        $keys = array_keys($data);

        foreach ($keys as $k => $key) {
            $keys[$k] = '{' . $key . '}';
        }

        $tempData = $data;

        $primarys = $this->getPrimaryKeys();

        foreach ($primarys as $prim) {
            // si no esta el tag lo agrego
            if (!in_array('{'.$prim.'}',$keys)) {
                $keys[] = '{'.$prim.'}';
            }

            // si no tiene valor le doy 0
            if (!is_numeric($tempData[$prim])) {
               $tempData[$prim] = '0';
            }
        }

//        Rad_Log::debug(array_values($tempData));
        Rad_CustomFunctions::recursive_array_replace($keys, array_values($tempData), $this->_validators);
    }

    /**
     * Genera y agrega los validadores segun los tipos de datos de los campos de la tabla.
     * @return array
     */
    protected function _addBasicValidators ()
    {
        $cols = $this->_getCols();
        foreach ($cols as $field) {
            if (!isset($this->_validators[$field])) {
                $this->_validators[$field] = array();
            }
            if (!isset($this->_filters[$field])) {
                $this->_filters[$field] = array();
            }

            $val = @$this->_basicValidators[$this->_metadata[$field]['DATA_TYPE']];

            if ($val) {
                $this->_validators[$field] = array_merge($this->_validators[$field], $val);
            }

            if ($this->_metadata[$field]['NULLABLE'] === false && $field != 'Id') {

               $this->_validators[$field][] = $this->getNotEmptyValidator($this->_metadata[$field]);
               $this->_validators[$field]['presence']   =  'requiered';
//               $this->_validators[$field]['allowEmpty'] =  false;
//               $this->_validators[$field]['presence'] = "required";
            }

            $filter = @$this->_basicFilters[@$this->_metadata[$field]['DATA_TYPE']];

            if ($filter) {
                $this->_filters[$field] = array_merge($filter, $this->_filters[$field]);
            }
        }
    }

    /**
     * Retorna el validador NotEmpty correctamente configurado segun el tipo de campo
     *
     * @param array $fieldType
     */
    protected function getNotEmptyValidator($fieldMetadata)
    {
        $val = new Zend_Validate_NotEmpty();

        switch ($fieldMetadata['DATA_TYPE']) {
            /**
             * Para los casos numericos los tomo como string sino por defecto me toma el cero como vacio
             */
            case  'int':
            case  'bigint':
            case  'decimal':
            case  'tinyint':
            $val->setType(Zend_Validate_NotEmpty::STRING);
                break;
        }
        $val->setMessage('Debe ingresar un dato en '.$fieldMetadata['COLUMN_NAME']);

        return $val;
    }

    /**
     * borras los campos con key contenidos en $fields del array data
     *
     * @param array datos
     * @param array campos a borrar
     */
    protected function _unsetFrom($data, $fields)
    {
        foreach ($fields as $field) {
            unset($data[$field]);
        }
        return $data;
    }

    /**
     * Actualiza un registro
     * Esta funcion sobrescribe la del padre agregando validacion y filtros
     *
     * @param  array  $data  Column-value pairs.
     * @param  array  $where Where
     * @return mixed         The primary key of the row inserted.
     */
    public function update (array $data, $where)
    {
        // quitamos los solo lectura
        $data  = $this->_unsetFrom($data, $this->_readOnlyFields);

        // quitamos datos de archivos atachados
        $data  = $this->_unsetFrom($data, $this->_attachedFiles);

        // seteamos los valores fijos si los hay
        $data  = $this->_setPermanentValues($data);



        $valid = $this->isValid($data, array(
            'allowEmpty' => true,
            'filterNamespace' => 'Rad_Filter',
            //'breakChainOnFailure' => true,
            'notEmptyMessage' => "Debe ingresar algun valor en '%field%'",
            'missingMessage'  => "Debe ingresar el dato '%field%'")
        );

        if (empty($data)) {
            return 0;
        }
        if ($valid === true && !empty($where)) {
            $where = $this->_appendPermanentValuesToWhere($where);
            return parent::update($data, $where);
        } else {

            throw new Rad_Db_Table_Exception("Error al actualizar registro", $valid);
        }
    }

    /**
     * Inserta un nuevo registro
     * Esta funcion sobrescribe la del padre agregando validacion y filtros
     *
     * @param  array  $data  Column-value pairs.
     * @return mixed         The primary key of the row inserted.
     */
    public function insert (array $data)
    {
        // quitamos datos de archivos atachados
        $data  = $this->_unsetFrom($data, $this->_attachedFiles);

        // seteamos los valores fijos si los hay
        $data = $this->_setPermanentValues($data);

        $valid = $this->isValid($data, array(
            'allowEmpty'      => true,
            'filterNamespace' => 'Rad_Filter',
            'notEmptyMessage' => "Debe ingresar algun valor en '%field%'",
            'missingMessage'  => "Debe ingresar el dato '%field%'")
        );

        if ($valid === true) {
            return parent::insert($data);
        } else {
            throw new Rad_Db_Table_Exception("Error al insertar registro", $valid, get_class($this));
        }
    }

    public function delete ($where)
    {
        $where = $this->_appendPermanentValuesToWhere($where);
        return parent::delete($where);
    }

    public function setFieldAttachedFile($field, $fileId, $row)
    {
        if (array_key_exists($field, $this->_attachedFiles)) {
            parent::update(array($field => $fileId), "Id = $row->Id");
        } else {
            throw new Rad_Db_Table_Exception("El campo $field no tiene configurado soporte para anexar archivos en el modelo ".get_class($this));
        }
    }

    /**
     * @return bool
     */
    public function getFetchWithAutoJoins ()
    {
        return $this->_fetchWithAutoJoins;
    }

    /**
     * set _fetchWithAutoJoins
     */
    public function setFetchWithAutoJoins ($value)
    {
        $this->_fetchWithAutoJoins = $value;
    }

    /**
     * @return bool
     */
    public function getFetchWithCalcFields()
    {
        return $this->_fetchWithCalcFields;
    }

    /**
     * set _fetchWithAutoJoins
     */
    public function setFetchWithCalcFields ($value)
    {
        $this->_fetchWithCalcFields = $value;
    }



    /**
     * Cosntructor de la Clase
     *
     * @param array $config Configuracion Zend_Db_Table_Abstract
     * @param bool $withJoins Define si el modelo configurara los AutoJoins definidos
     */
    public function __construct ($config = array(), $withJoins = false)
    {
        $this->_fetchWithAutoJoins  = $withJoins;
        $this->_fetchWithCalcFields = $withJoins;

        parent::__construct($config);
    }

    /**
     * Inicializa el modelo
     */
    public function init ()
    {
        $this->_addBasicValidators();
        if ($this->_fetchWithAutoJoins) {
            $this->_configAutoJoins();
        }
    }

    /**
     * retorna las columnas del modelo
     * @return array
     */
    public function getColumns ()
    {
        return $this->_getCols();
    }

    /**
     * retorna la tabla a la que referencia el modelo
     * @return string
     */
    public function getName ()
    {
        return $this->_name;
    }

    /**
     * retorna las columnas del modelo que son primary keys
     * @return array
     */
    public function getPrimaryKeys ()
    {
        if (!$this->_primary)
            $this->_setupPrimaryKey();
        return $this->_primary;
    }

    /**
     * Configura los Joins
     */
    protected function _configAutoJoins ()
    {
        $refMap = $this->_getReferenceMapNormalized();

        foreach ($refMap as $ruleName => $rule) {

            // Si hay columnas para El join configurada lo hacemos
            if (isset($rule['refJoinColumns'])) {
                // pido el joiner en cada iteracion para solo crear el objeto si hay realmente un join q hacer
                $joiner = $this->getJoiner();

                // TODO: sacar lo de los combos de aca
                $useCombo = isset($rule['comboBox']) && $rule['comboBox'] === true;

                if ($useCombo) {

                    if (count($rule['refColumns']) > 1 || count($rule['columns']) > 1) {
                        throw new Rad_Db_Table_Exception(__CLASS__ . "No puede usarse el parametro comboBox en relaciones con mas de una columna en la relacion $ruleName", array(), __CLASS__);
                    }

                    $k  = key($rule['refJoinColumns']);

                    // despues el join le agrega en lombre del campo
                    $rule['refJoinColumns']['_cdisplay'] = $rule['refJoinColumns'][$k];

                    unset($rule['refJoinColumns'][$k]);

                    $refModel  = Rad_GridDataGateway_ModelMetadata::getModelClassInfo($rule['refTableClass']);

                    $fieldName = $rule['columns'][0];
                    $this->_metadata[$fieldName]['COMBO_SOURCE']   = @$rule['comboSource'] . '/model/' . @$refModel['model'] . '/m/' . @$refModel['module'];
                    $this->_metadata[$fieldName]['COMBO_TPL']      = @$rule['comboTpl'];
                    $this->_metadata[$fieldName]['COMBO_PAGESIZE'] = @$rule['comboPageSize'];
                    $this->_metadata[$fieldName]['COMBO_TABLE']    = @$rule['refTable'];
                    $this->_metadata[$fieldName]['REL_LINK']       = @$rule['relLink'];
                }

                $joiner->joinRef($ruleName, $rule['refJoinColumns']);
            }
        }
    }

    /**
     * Retorna los metadatos de las tablas con el agregado de los campos joineados
     */
    public function getMetadataWithJoins ()
    {
        if (empty($this->_metadata)) {
            $this->_setupPrimaryKey();
        }
        // si ya estan generados los metas de los joins
        if (!$this->_metadataJoins) {
            // si tenemos un joiner le pedimos los metadatos
            if ($this->_joiner) {
                $this->_metadataJoins = $this->_joiner->getJoinsMetadata();

            }

        }

        if ($this->_fetchWithCalcFields) {
            if (empty($this->_metadataCalculated)) {
                $this->_genCalcMetadata();
            }
        }

        return array_merge_recursive($this->_metadata, $this->_metadataJoins, $this->_metadataCalculated);
    }

    protected function _genCalcMetadata()
    {
        $meta = array();
        foreach ($this->_calculatedFields as $f => $calc) {
            $meta[$f]['CALCULATED_FIELD'] = true;
            $meta[$f]['COLUMN_NAME']      = $f;
            $meta[$f]['COLUMN_CALC']      = $calc;
        }
        $this->_metadataCalculated = $meta;
    }

    /**
     * Esconde un Campo al retornar un fetchAll
     *
     * @param string $field
     */
    public function hideField ($field)
    {
        unset($this->_visibleFields[$field]);
    }

    /**
     * Muestra un Campo al retornar un fetchAll
     *
     * @param string $field
     */
    public function showField ($field)
    {
        $this->_visibleFields[$field] = $field;
    }

    /**
     * Agrega de manera segura una condicion al where
     *
     * @param mixed  $where
     * @param string $condition
     */
    protected function _addCondition($where, $condition)
    {
        if (($where instanceof Zend_Db_Table_Select)) {
            $this->_where($where, $condition);
        } else {
            if (is_array($where)) {
                $where[] = $condition;
            } else {
                $where .= ($where)?(" AND $condition"):$condition;
            }
        }
        return $where;
    }

    /**
     * Sobreescrita para que use mi propia clase select
     * Returns an instance of a Zend_Db_Table_Select object.
     *
     * @param bool $withFromPart Whether or not to include the from part of the select based on the table
     * @return Zend_Db_Table_Select
     */
    public function select($withFromPart = self::SELECT_WITHOUT_FROM_PART)
    {
        require_once 'Zend/Db/Table/Select.php';
        $select = new Rad_Db_Table_Select($this);
        if ($withFromPart == self::SELECT_WITH_FROM_PART) {
            $select->from($this->info(self::NAME), Zend_Db_Table_Select::SQL_WILDCARD, $this->info(self::SCHEMA));
        }
        return $select;
    }

    protected function _genJoinsArray()
    {
        if (empty($this->_joiner)) return array();

        if (!empty($this->_joins)) return $this->_joins;

        $this->_joins = $this->_joiner->getDbJoins();
    }

    public function addLocalCalculatedField($name, $callback)
    {
        $this->_localCalculatedFields[$name] = new Rad_Db_Table_CalculatedColumn($name, $callback, $this);
    }

    public function getLocalCalculatedFields()
    {
        if ($this->_fetchWithCalcFields) {
            return $this->_localCalculatedFields;
        }
        return array();
    }

    public function hasLocalCalculatedFields()
    {
        return !empty($this->_localCalculatedFields);
    }

    // protected function _addLocalCalculatedFields(&$results)
    // {
    //     foreach ($results as $row) {
    //         foreach ($this->_localCalculatedFields as $name => $calColumn) {

    //         }
    //     }
    // }


    /**
     * Sobreescribe la funcion fetchAll agregando el soporte para autoJoins
     * Fetches all rows.
     *
     * Honors the Zend_Db_Adapter fetch mode.
     *
     * @param string|array|Zend_Db_Table_Select $where  OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param string|array                      $order  OPTIONAL An SQL ORDER clause.
     * @param int                               $count  OPTIONAL An SQL LIMIT count.
     * @param int                               $offset OPTIONAL An SQL LIMIT offset.
     * @return Zend_Db_Table_Rowset_Abstract The row results per the Zend_Db_Adapter fetch mode.
     */
    public function fetchAll ($where = null, $order = null, $count = null, $offset = null)
    {

//        if (!$this->_fetchWithCalcFields && !$this->_fetchWithAutoJoins) {
//           return parent::fetchAll($where, $order, $count, $offset);
//        }
        if (!($where instanceof Zend_Db_Table_Select)) {
            $select = $this->select();

            $select->setIntegrityCheck(false);

            if ($where !== null) {
                $this->_where($select, $where);
            }
            if ($order !== null) {
                $this->_order($select, $order);
            }

            if ($count !== null || $offset !== null) {
                $select->limit($count, $offset);
            }
        } else {
            $select = $where;
            $select->setIntegrityCheck(false);
        }
        // Ya trae form?
        $from = $select->getPart('from');

        if (empty($from)) {
            // Si tiene campos visibles solo mostramos esos, o si no todos.
            if (empty($this->_visibleFields)) {
                $vFields = array(new Zend_Db_Expr("SQL_CALC_FOUND_ROWS $this->_name.*"));            // OJO SOLO COMPATIBLE CON MYSQL!!!!!!!!!!!!!!!!!!!!!!!
            } else {
                $vFields = $this->_visibleFields;
                $vFields[key($vFields)] = new Zend_Db_Expr("SQL_CALC_FOUND_ROWS " . key($vFields));
            }
        }

        // Si tiene calculados los agregamos.
        if ($this->_fetchWithCalcFields && !empty($this->_calculatedFields)) {
            foreach ($this->_calculatedFields as &$v) {
                $v = new Zend_Db_Expr($v);
            }
            $vFields = array_merge($vFields, $this->_calculatedFields);
        }

        $select->from($this->_name, $vFields);


        if ($this->_fetchWithAutoJoins) {
            $this->_genJoinsArray();

            foreach ($this->_joins as $join) {
                $select->joinLeft($join[0], $join[1], $join[2]);
                if ($join[3]) {
                    $select->group($join[3]);
                }
            }
        }

        // Si tenemos un contenedor de filtros agregamos los filtros al where
        if ($this->_fetchFilters) {
            $this->_fetchFilters->appendFilters($select);
        }

        $this->_appendPermanentValuesToWhere($select);
       
        return parent::fetchAll($select);
    }
}
