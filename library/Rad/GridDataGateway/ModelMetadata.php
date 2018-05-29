<?php

/**
 * Rad_AutoGridGateway_MetadataGenerator
 * 
 * Generador de metadatos de modelos para la capa de presentacion 
 * implementada usando las grillas ExtJs
 *
 * @autor: Martin A. Santangelo
 * @copyright: SmartSoftware Argentina 2010
 */
class Rad_GridDataGateway_ModelMetadata
{

    /**
     * Metadatos del modelo
     * @var array
     */
    protected $_metadata;
    /**
     * Modelo
     * @var Rad_Model_DbTable
     */
    protected $_model;
    /**
     * Nombre del Modelo
     * @var String
     */
    protected $_modelName;
    /**
     * clase del Modelo
     * @var String
     */
    protected $_modelClass;
    /**
     * Modulo al que pertenece el Modelo
     * @var String
     */
    protected $_module;
    /**
     * Seccion del ini para mezaclar con los metadatos autogenerados
     * @var String
     */
    protected $_iniSection;
    /**
     * Seccion del ini para mezaclar con los metadatos autogenerados
     * @var Zend_Cache
     */
    protected static $_cache;

    /**
     * 	Constructor de la clase
     *
     *  @param Rad_Db_Table $model
     *  @param string $iniSection
     */
    public function __construct($model, $iniSection='default')
    {
        $this->_model = $model;
        $this->_iniSection = $iniSection;
        $this->parseClass();
    }

    /**
     * Obtiene el nombre del modelo y el módulo analizando el nombre
     */
    protected function parseClass()
    {
        $this->_modelClass = get_class($this->_model);

        $info = self::getModelClassInfo($this->_modelClass);

        $this->_modelName = $info['model'];
        $this->_module = $info['module'];
    }

    /**
     * Retorna el modulo y el nombre del modelo de la clase pasada
     * 
     * TODO: Mover a una clase utilitaria no deberia estar aca
     * 
     * @param string $class
     * @return array
     */
    public static function getModelClassInfo($class)
    {
        $model = substr($class, strpos($class, 'Model_DbTable_')+14);
        $class = explode('_', $class);

        if ($class[0] == 'Model') {
            $module = 'default';
        } else {
            $module = $class[0];
        }
        return array('model' => $model, 'module' => $module);
    }

    /**
     * Retorna la configuracion del ini
     *
     * @param string $class   Clase
     * @param string $section Seccion del ini a cargar
     * @return array
     */
    public static function getModelClassIni($class, $section = null, $js = true)
    {
        $dS = DIRECTORY_SEPARATOR;
        $i = self::getModelClassInfo($class);

        if ($i['module'] != 'default') {
            $file = $dS . 'modules' . $dS . $i['module'] . $dS . 'models' . $dS . 'DbTable' . $dS . str_replace('_', $dS, $i['model']) . ".ini";
        } else {
            $file = $dS . 'models' . $dS . 'DbTable' . $dS . str_replace('_', $dS, $i['model']) . ".ini";
        }
        return Rad_CustomFunctions::loadIniConfig($file, $section, $js);
    }

    /**
     * Setea el cache de la clase
     * @param Zend_Cache $cache
     */
    public static function setCache($cache)
    {
        self::$_cache = $cache;
    }

    /**
     * Obtiene el Id para guardar los metadatos en cache
     *
     * @param bool $editors
     * @param bool $js
     */
    protected function _getCacheId($editors, $js)
    {
        return "Rad_GridDataGateway" . $this->_modelClass . $this->_iniSection . ($editors ? "1" : "0") . ($js ? "1" : "0");
    }

    /**
     * Retorna la configuracion del ini
     * @return array
     */
    protected function _getModelIni()
    {
        $dirS = DIRECTORY_SEPARATOR;
        if ($this->_module != 'default') {
            $file = $dirS . 'modules' . $dirS . $this->_module . $dirS . 'models' . $dirS . 'DbTable' . $dirS .  str_replace('_', $dirS, $this->_modelName). ".ini";
        } else {
            $file = $dirS . 'models' . $dirS . 'DbTable' . $dirS . str_replace('_', $dirS, $this->_modelName) . ".ini";
        }
        return Rad_CustomFunctions::loadIniConfig($file, $this->_iniSection, $js);
    }

    /**
     * Verifica que esten generados los metadatos o los genera
     *
     * @param bool $editors
     * @param bool $js
     */
    protected function checkMetaLoaded($editors, $js)
    {
        if (!$this->_metadata) {
            if (self::$_cache) {
                $idCache = $this->_getCacheId($editors, $js);
                $this->_metadata = self::$_cache->load($idCache);
                if (!$this->_metadata) {
                    $this->generateMetaData($editors, $js);
                }
            } else {
                $this->generateMetaData($editors, $js);
            }
        }
    }

    /**
     * Generados los metadatos
     *
     * @param bool $editors
     * @param bool $js
     */
    protected function generateMetaData($editors, $js)
    {
        $modelMetadata    = $this->_model->getMetadataWithJoins();
        $modelFieldConfig = $this->_getModelIni();

        foreach ($modelFieldConfig as $field => $iniMetaData) {

            if ($this->_model->isFieldVisible($field)) {
                $this->_buildFieldMetadata(
                    $field, $iniMetaData, $modelMetadata[$field], $editors, $js
                );
            }
            unset($modelMetadata[$field]);
        }

        /**
         * Si hay algun campo definido en el modelo para la grilla que no existe en la DB tambien lo agregamos
         * Esto lo usamos para campos calculados
         */
        foreach ($modelMetadata as $field => $metaData) {
            if ($this->_model->isFieldVisible($field)) {
                $this->_metadata->fields[] = Rad_DbFieldToExtMapper::getMetaDataFromNoDbField($field, $metaData, false, $js);
            }
        }

        $this->addCommonMetadata();

        // Si hay cache configurado guardamos los metadatos
        if (self::$_cache) {
            self::$_cache->save($this->_metadata, $idCache, array("Rad_GridDataGateway"));
        }
    }

    /**
     *  Agrega la configuracion comun para los stores
     *
     */
    protected function addCommonMetadata()
    {
        $primary = $this->_model->getPrimaryKeys();

        $groupField = $this->_model->getGridGroupField();

        $this->_metadata->groupField      = $groupField;
        $this->_metadata->root            = "rows";
        $this->_metadata->idProperty      = $primary[1];
        $this->_metadata->messageProperty = 'msg';
        $this->_metadata->totalProperty   = "count";
        $this->_metadata->successProperty = "success";
        $this->_metadata->start           = ($_POST['start']) ? $_POST['start'] : 0;
        $this->_metadata->limit           = ($_POST['limit']) ? $_POST['limit'] : 20;
    }

    /**
     *  Retorna los metadatos de los campos
     *
     * 	@param bool $editors
     * 	@param bool $js
     */
    public function getFieldsMetadata($editors = true, $js = true)
    {
        $this->checkMetaLoaded($editors, $js);
        return $this->_metadata->fields;
    }

    /**
     *  Retorna los metadatos de los campos
     *
     * 	@param bool $editors
     * 	@param bool $js
     */
    public function getMetadata($editors = true, $js = true)
    {
        $this->checkMetaLoaded($editors, $js);
        return $this->_metadata;
    }

    /**
     * Genera los metadatos Extjs para el campo $field tomando los metadatos de la DB y de los ini
     *
     * @param $field
     * @param $iniMetadata
     * @param $tableMetadata
     * @param $editors  		genera editores
     * @param $js  		        No genera ni JavaScrips ni renderers
     */
    protected function _buildFieldMetadata($field, $iniMetadata, $tableMetadata, $editors = true, $js = true)
    {
        $fieldMetadata = array();
        $withEditor = $editors && @$iniMetadata['editable'] === true;

        // pasamos los datos del link del editor a la grilla para la configuracion del campo
        $tableMetadata['REL_LINK'] = $iniMetadata['editor']['link'];

        $fieldMetadata = Rad_DbFieldToExtMapper::getAutoGridMetaDataFromField($field, $tableMetadata, $withEditor, $js);

        //Si el modelo tiene configuraciones de editor y no se deben incluir en los metadatos hay q borrarlos. Si no se incluiran editores por defecto.
        if (!$withEditor) {
            unset($iniMetadata['editor']);
        }
        // Aplicamos la configuracion del campo en el modelo tambien
        if ($iniMetadata) {
            // un campo en la grilla nunca deberia ser hidden

            if (@$iniMetadata['editor']['xtype'] == 'hidden') {
                unset($iniMetadata['editor']['xtype']);
            }

            $resultMetadata = Rad_CustomFunctions::mergeConf($fieldMetadata, $iniMetadata);
        } else {
            $resultMetadata = $fieldMetadata;
        }

        $this->_metadata->fields[] = $resultMetadata;
    }

}