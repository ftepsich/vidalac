<?php
/**
 * Rad_DataGateway_Controller_Action
 *
 * @copyright SmartSoftware Argentina
 * @package Rad
 * @subpackage DataGateway
 * @author Martin Alejandro Santangelo
 */
require_once 'Zend/Controller/Action.php';
require_once 'Rad/DbFieldToExtMapper.php';

/**
 * Rad_DataGateway_Controller_Action
 * Esta clase sirve de gateway de acceso a todos los _modelos
 *
 * @copyright SmartSoftware Argentina
 * @package Rad
 * @subpackage DataGateway
 * @author Martin Alejandro Santangelo
 */
class Rad_DataGateway_Controller_Action extends Zend_Controller_Action
{

    protected $_model  = null;
    protected $_module = null;
    protected $_modelClass;
    protected $_modelName;
    protected $_modelsAcl;

    /**
     * Inicializa el Data Gateway
     */
    public function init()
    {
        //Todo; Ver si se puede hacer antes de levantar estos recursos
        $this->_helper->viewRenderer->setNoRender(true);

        $this->_modelName = $this->getRequest()->getParam('model');
        $this->_module = $this->getRequest()->getParam('m');

        // Si no se me especifica el modulo, tomo el modulo al que pertenece el Datagateway
        if (!$this->_module) {
            $this->_module = $this->getRequest()->getModuleName();
        }

        if (!$this->_modelName) {
            throw new Rad_DataGateway_Controller_Action_Exception("Falta el Parametro modelo");
        }

        $prefix = (($this->_module == 'default') ? '' : ucfirst($this->_module) . '_');

        $this->_modelClass = $prefix . "Model_DbTable_" . $this->_modelName;
        $db = Zend_Registry::get('db');
        $this->_modelsAcl = new Rad_ModelAcl($db);
        parent::init();
    }

    /**
     * Instancia el _modelo
     *
     * @param bool $withAutoJoins
     * @return bool
     */
    protected function constructModel($withAutoJoins = false)
    {
        if ($this->_model == null) {
            $this->_model = new $this->_modelClass(array(), $withAutoJoins);
            return true;
        } else {
            return false;
        }
    }

    public function createrowAction()
    {
        if (!$this->_modelsAcl->allowInsert($this->_modelClass)) {
            throw new Rad_DataGateway_Controller_Action_NotAllowedException("Ud. no tiene permiso para realizar esta operación");
        }
        $this->constructModel();
        $row = $this->_model->createRow();
        $row->Id = 0;
        $msg->success = true;
        $msg->rows[] = $row->toArray();
        $msg->count = 1;

        $this->_sendJsonResponse($msg);
    }

    /**
     * Arma el array de orden segun los parametros enviados
     * @return array
     */
    protected function _buildSort($select)
    {
        $sort = array();

        $rq   = $this->getRequest();

        // Parametros enviados
        $groupBy = $rq->getParam("groupBy");
        $sortBy  = $rq->getParam("sort");
        $dir     = $rq->getParam("dir");

        // $_modelInfo = $this->_model->getMetadataWithJoins();
        // $fieldMetaData = $_modelInfo[$sortBy];
        // Rad_Log::debug($fieldMetaData);
        // return '';



        $modelDefaultGroup = $this->_model->getGridGroupField();

        // Si se agrupa ordenamos por el campo agrupado!
        if ($groupBy) {
            $direction = $this->_model->getGridGroupFieldOrderDirection();
            if (!$direction) $direction = 'ASC';
            $sort[] = $groupBy . ' '.$direction;
        } else if ($modelDefaultGroup) {
            $sort[] = $modelDefaultGroup . ' ' .$this->_model->getGridGroupFieldOrderDirection();
        }

        //viene un parametro de orden
        if ($sortBy) {
            // Obetenemos los metadatos del modelo
            $_modelInfo = $this->_model->getMetadataWithJoins();
            // Multi orden?
            if (strpos($sortBy,',')) {
                $fields = explode(',', $sortBy);
                $dirs   = explode(',', $dir);

                foreach($fields as $k => $field) {
                    if (!$field) continue;

                    if ($dirs[$k] && !in_array(strtolower($dirs[$k]), array('asc', 'desc'))) {
                        throw new Rad_DataGateway_Controller_Action_Exception("La dirección del orden solo puede ser Asc o Desc");
                    }

                    $fieldMetaData = $_modelInfo[$field];

                    if (!$fieldMetaData) throw new Rad_DataGateway_Controller_Action_Exception("El campo $field no pertenece al modelo");

                    // si es un combo filtro por el campo descripcion
                    if (isset($fieldMetaData['COMBO_SOURCE'])) {
                        // busco los metadatos del campo q muestro en realidad en el combo
                        $joinedMeta = $_modelInfo[$field.'_cdisplay'];
                        $field = $joinedMeta['TABLE_ALIAS'].".".$joinedMeta['COLUMN_NAME'];

                    } else if (isset($fieldMetaData['JOINED_FIELD'])) { // Si es un campo joineado se ordena por el campo de la tabla relacionada

                        $field = $fieldMetaData['TABLE_ALIAS'].".".$fieldMetaData['COLUMN_NAME'];

                    } else if (!$fieldMetaData['CALCULATED_FIELD']) {

                        $field = $fieldMetaData['TABLE_NAME'].".".$field;

                    }

                    $sort[] = $field . " " . ($dirs[$k]);
                }

            } else {
                if ($dir && !in_array(strtolower($dir), array('asc', 'desc'))) {
                    throw new Rad_DataGateway_Controller_Action_Exception("La dirección del orden solo puede ser Asc o Desc");
                }
                $fieldMetaData = $_modelInfo[$sortBy];

                //if (!$fieldMetaData) throw new Rad_DataGateway_Controller_Action_Exception("El campo $sortBy no pertenece al modelo");
                if (!$fieldMetaData) throw new Rad_DataGateway_Controller_Action_Exception("El campo $sortBy no pertenece al modelo");

                // si es un combo filtro por el campo descripcion
                if (isset($fieldMetaData['COMBO_SOURCE'])) {
                    // busco los metadatos del campo q muestro en realidad en el combo
                    $joinedMeta = $_modelInfo[$sortBy.'_cdisplay'];
                    $sortBy = $joinedMeta['TABLE_ALIAS'].".".$joinedMeta['COLUMN_NAME'];

                } else if (isset($fieldMetaData['JOINED_FIELD'])) { // Si es un campo joineado se ordena por el campo de la tabla relacionada

                    $sortBy = $fieldMetaData['TABLE_ALIAS'].".".$fieldMetaData['COLUMN_NAME'];

                } else if (!$fieldMetaData['CALCULATED_FIELD']) {

                    $sortBy = $fieldMetaData['TABLE_NAME'].".".$sortBy;

                }

                $sort[] = $sortBy . " " . (($dir) ? $dir : "ASC");
            }
        } else {
            $sort = array_merge( $sort, $this->_model->getSort());
        }

        // if (empty($sort)) {
        //     $sort = $this->_model->getSort();
        // }

        $select->order($sort);
    }

    protected function _sendJsonResponse($data)
    {
        // $json = $this->getHelper('Json');
        // $json->suppressExit = true;
        // Zend_Wildfire_Channel_HttpHeaders::getInstance()->flush(); // fix para q no rompa el envio a firebug
        // $json->getResponse()->sendResponse();
        // $json->sendJson($data, array('enableJsonExprFinder' => true));

        // no puedo enviar la cabecera  json/javascript porque el formulario al tener fielUpload: true no funciona
        Zend_Wildfire_Channel_HttpHeaders::getInstance()->flush(); // fix para q no rompa el envio a firebug
        $data = Zend_Json::encode($data, null, array('enableJsonExprFinder' => true));
        echo $data;
    }

    /**
     * Obtiene un registro en formatoJson
     */
    public function getAction()
    {
        if (!$this->_modelsAcl->allowView($this->_modelClass)) {
            throw new Rad_DataGateway_Controller_Action_NotAllowedException("Ud. no tiene permiso para realizar esta operación");
        }

        $this->constructModel(true);
        $id = $this->getRequest()->getParam("id");
        if ($id) {
            $rows = $this->_model->find($id);
            if (count($rows)) {
                $msg->data = $rows->current()->toArray();
                $msg->success = true;
            } else {
                $msg->success = false;
                $msg->msg = "No se encontro el registro";
            }
        } else {
            $msg->success = false;
            $msg->msg = "Falta el parametro requerido";
        }

        $this->_sendJsonResponse($msg);
    }

    protected function _getSelect($start = null, $limit = null)
    {
        $select = $this->_model->select();

        if ($limit || $start) {
            $select->limit($limit, $start);
        }

        return $select;
    }

    /**
     * Lista los datos del _modelo filtrando si se pasaron filtros por POST
     */
    public function listAction()
    {
        $this->constructModel(true);

        $r = $this->getRequest();

        $fetch = $r->getParam("fetch");
        $start = $r->getParam("start");
        $limit = $r->getParam("limit");

        // Obtenemos el select del modelo ya con los limit seteados
        $select = $this->_getSelect($start, $limit);
        // Agregamos el orden al select
        $this->_buildSort($select);
        // Agregamos el Where al select
        $this->_buildSelectWhere($select);
        // Traigo la consulta
        $return = $this->fetchRecordListWithCount($select, $fetch);
        $return['success'] = true;

        $this->_sendJsonResponse($return);
    }

    /**
     * Lista los datos del _modelo filtrando si se pasaron filtros por POST Agregando los datos relacionados especificados en
     * $_POST['nested'][0]['model'] = 'modulo_modelo';
     * $_POST['nested'][0]['variable'] = 'nested';
     * $_POST['nested'][0]['rule'] = 'relacion1'; // opcional
     */
    public function listnestedAction()
    {
        $this->constructModel(true);

        $r = $this->getRequest();

        $fetch  = $r->getParam("fetch");
        $start  = $r->getParam("start");
        $limit  = $r->getParam("limit");
        $nested = $r->getParam("nested");

        //limitamos los registros ya que traemos los relacionados y puede ser muy pesado
        if (!$limit) {
            $limit = 50;
        }

        // Obtenemos el select del modelo ya con los limit seteados
        $select = $this->_getSelect($start, $limit);
        // Agregamos el orden al select
        $this->_buildSort($select);
        // Agregamos el Where al select
        $this->_buildSelectWhere($select);
        // Traigo la consulta
        $return = $this->fetchRecordListWithCount($select, $fetch);

        foreach ($nested as $rel) {
            $model = $this->_getNestedModel($rel);

            $this->_tempNestedModel = new $model(array(), true);

            if (!$rel['variable']) {
                throw new Rad_DataGateway_Controller_Action_Exception('no se especifico la variable de retorno para la tabla relacionada');
            }
            foreach ($return['rows'] as $key => $row) {
                $return['rows'][$key][$rel['variable']] = $this->_getNestedData($rel, $model, $row);
            }
        }

        $return['success'] = true;

        $this->_sendJsonResponse($return);
    }

    private function _getNestedModel($rel)
    {
        $model = explode('_', $rel['model']);

        if (count($model) == 2) {
            $model = $model[0] . '_Model_DbTable_' . $model[1];
        } else {
            $model = 'Model_DbTable_' . $model[0];
        }
        return $model;
    }

    private function _getNestedData($rel, $model, $row)
    {
        $refMap = $this->_tempNestedModel->getReference(get_class($this->_model), @$rel['rule']);

        if (!$refMap) {
            throw new Rad_DataGateway_Controller_Action_Exception('No se encuentra relacion con el modelo ' . $model);
        }

        $return = $this->_tempNestedModel->fetchAll(
            $refMap[Zend_Db_Table_Abstract::COLUMNS][0] . " = " . $row[$refMap[Zend_Db_Table_Abstract::REF_COLUMNS][0]],
            $rel['order'],
            $rel['limit'],
            $rel['offset']
        )->toArray();

        return $return;
    }

    /**
     * Construye el where de la busqueda segun los parametros enviados por POST
     */
    protected function _buildSelectWhere($select)
    {
        $request = $this->getRequest();

        $where = null;

        if ($request->isPost()) {
            $_modelInfo = $this->_model->getMetadataWithJoins();
            $filters = $request->getParam("filter");
            $wheref = $this->_setWhereFromFilter($select, $filters, $_modelInfo);
            /**
             * Filtros pasados por parametro al dataStore de la grilla
             * Uso una variable aparte ya que el plugin de filtro para la grilla limpia la variable filter
             */
            $pfilters = $request->getParam("pfilter");
            $wherep = $this->_setWhereFromFilter($select, $pfilters, $_modelInfo, false);
            // if ($wheref || $wherep) {
                // $where = array_merge($wheref, $wherep);
            // }

            // foreach ($where as $key => $value) {
                // $select->where($key, $value);
            // }
        }
    }

    /**
     * Arma las condiciones del where a partir de los filtros pasados (Ver formato de filtros)
     *
     * @param Rad_Db_Select $select
     * @param array         $filters
     * @param array         $_modelInfo
     * @param bool          $searchInCombo
     */
    protected function _setWhereFromFilter($select, $filters, $_modelInfo, $searchInCombo = true)
    {
        $where = array();

        if (is_array($filters)) {
            foreach ($filters as $k => $filter) {
                $fieldMetaData = $_modelInfo[$filter['field']];

                if (!$fieldMetaData) {
                    continue;
                }
                // Si es un campo joineado
                if ($fieldMetaData['JOINED_FIELD']) {
                    $fieldName = $fieldMetaData['COLUMN_NAME'];
                    $tableName = $fieldMetaData['TABLE_ALIAS'];
                } else {
                    // Si es un combo se compara contra el campo de la tabla relacionada
                    if ($fieldMetaData['COMBO_SOURCE']) {
                        if ($searchInCombo) {
                            // busco los metadatos del campo q muestro en realidad en el combo
                            $joinedMeta = $_modelInfo[$filter['field'].'_cdisplay'];
                            $fieldName  = $joinedMeta['COLUMN_NAME'];
                            $tableName = $joinedMeta['TABLE_ALIAS'];
                        } else {
                            $fieldName = $filter['field'];
                            $tableName = $fieldMetaData['TABLE_NAME'];
                        }
                    } else  {
                        $fieldName = $filter['field'];
                        $tableName = $fieldMetaData['TABLE_NAME'];
                    }

                }



                $wItem     = array();

                if ($fieldMetaData['CALCULATED_FIELD']) {
                    $wItem[0] = $fieldName;

                } else {
                    $wItem[0]  = $this->_model->getAdapter()->quoteIdentifier($tableName . "." . $fieldName);
                }

                switch ($filter['data']['type']) {
                    case 'numeric':
                    case 'date':
                        switch ($filter['data']['comparison']) {
                            case 'lt':
                                $wItem[0] .= " <= ?";
                                break;
                            case 'gt':
                                $wItem[0] .= " >= ?";
                                break;
                            case 'eq':
                                $wItem[0] .= " = ?";
                                break;
                            case 'ne':
                                $wItem[0] .= " <> ?";
                                break;
                            default:
                                $wItem[0] .= " = ?";
                                break;
                        }
                        break;
                    case 'string':
                        $wItem[0] .= " like ?";
                        $filter['data']['value'] = "%" . $filter['data']['value'] . "%";
                        break;
                    case 'list':
                        $data = explode(',', $filter['data']['value']);
                        $wItem[0] .= ' in (' . implode(',', $data) . ')';
                        break;
                    case 'boolean':
                        $wItem[0] .= " = ?";
                        if ($filter['data']['value'] == 'true') $filter['data']['value'] = 1;
                        else $filter['data']['value'] = '0';

                        break;
                    default:
                        // $where[$k] .= " = ?";
                        break;
                }
                if ($filter['data']['type'] != 'list') {
                    //$where[$k] .= " " . $this->_model->getAdapter()->quote($filter['data']['value']);
                    $wItem[1] = $filter['data']['value'];
                } else {
                    $wItem[1] = $data;
                }

                if ($fieldMetaData['CALCULATED_FIELD']) {

                    $select->having($wItem[0],$wItem[1]);
                } else {
                    //throw new Exception($wItem[0].', '.$wItem[1]);
                    $select->where($wItem[0], $wItem[1]);
                }

            }
        }
    }

    protected function _catchDbTableException($e)
    {
        // Acomodo los errores por campo en el formato de Ext.Form
        foreach ($e->getFieldsErrors() as $field => $errors) {
            foreach ($errors as $error) {
                $fieldsErrors[$field] .= $error;
            }
        }
        $msg = new stdClass();

        $msg->success = false;
        $msg->errors  = $fieldsErrors;
        $msg->msg     = $e->getMessage();

        if (APPLICATION_ENV == 'development') {
            $msg->line = $e->getLine();
            $msg->file = $e->getFile();
        }

        return $msg;
    }

    /**
     * Guarda varios registros recibidos dentro de la propiedad rows en json
     * o retorna los errores en el formato soportado por Ext.Form
     */
    public function saverowsAction()
    {
        $request = $this->getRequest();
        if (!$request->isPost()) {
            $msg->success = false;
            $msg->errors = array();
            $msg->msg = 'Peticion erronea';
        } else {
            try {
                $this->constructModel(true);
                $adpter = $this->_model->getAdapter();

                $adpter->beginTransaction();

                $rows = json_decode($request->rows, true);
                if (!$rows)
                    throw new Rad_DataGateway_Controller_Action_Exception('no se paso el parametro rows');
                if (isset($rows[0])) {
                    foreach ($rows as $datos) {
                        $row = $this->_saveRecord($datos, $this->_model);
                        $msg->rows[] = $row->toArray();
                    }
                } else {
                    $row = $this->_saveRecord($rows, $this->_model);
                    $msg->rows[] = $row->toArray();
                }

                $adpter->commit();

                $msg->success = true;

            } catch (Rad_Db_Table_Exception $e) {

                $adpter->rollback();
                $msg = $this->_catchDbTableException($e);

            } catch (Exception $e) {

                $adpter->rollback();
                throw $e;
            }
        }
        $this->_sendJsonResponse($msg);
    }

    /**
     * Guarda un registro
     * o retorna los errores en el formato soportado por Ext.Form
     */
    public function saveAction()
    {
        $request = $this->getRequest();

        $msg = new stdClass();

        if (!$request->isPost()) {

            $msg->success = false;
            $msg->errors = array();
            $msg->msg = 'Peticion erronea';

        } else {

            try {
                $this->constructModel(true);

                $adpter = $this->_model->getAdapter();
                $datos = $request->getPost();

                $this->_model->getAdapter()->beginTransaction();

                $row = $this->_saveRecord($datos, $this->_model);

                // almancemos los archivos anexados
                $row = $this->_storeAttachedFiles($row);

                $msg->record = $row->toArray();

                $adpter->commit();


                $msg->success = true;

            } catch (Rad_Db_Table_Exception $e) {

                $adpter->rollback();

                $msg = $this->_catchDbTableException($e);
            } catch (Exception $e) {

                $adpter->rollback();

                throw $e;
            }
        }
        $this->_sendJsonResponse($msg);
    }

    /**
     * Almacena los archivos anexados al modelo si fueron enviados por el formulario
     */
    protected function _storeAttachedFiles($row)
    {
        // Valido contra los validadores configurados en el modelo
        $validator = new Rad_Db_Table_AttachedFileValidate($this->_model);

        $provider  = Service_AttachedFileProvider::getProvider();

        foreach ($_FILES as $field => $val) {
            if (!trim($val['tmp_name'])) continue;
            // valido
            $validator->isValid($field, $val['tmp_name']);

            $extension = end(explode('.',$val['name']));

            // guardo el archivo
            $provider->saveFile($row, $field, $val['tmp_name'], $extension);
        }
        return $row;
    }

    /**
     * Borra los archivos anexados al modelo
     */
    protected function _deleteAttachedFiles($row)
    {
        $fieldAttachedFiles = $this->_model->getAttachedFiles();

        $provider  = Service_AttachedFileProvider::getProvider();

        foreach ($fieldAttachedFiles as $field => $value) {
            $provider->deleteFile($row, $field, false);
        }
    }

    /**
     * Crea o actualiza un registro en el _modelo dado
     * @param $datos
     * @param $_model
     * @return array con datos guardados
     */
    protected function _saveRecord($datos, $_model)
    {
        // Valores de clave primaria
        $primary = $_model->getPrimaryKeys();
        foreach ($primary as $p) {
            $value = $datos[$p];
            // Si la Clave no tiene valor, la sacamos para que la base ponga el autoincrement si esta configurado
            if (!$value) {
                unset($datos[$p]);
            } else {
                $primaryValues[] = $value;
            }
        }
        // Buscamos el registro
        $rowset = null;
        if (!empty($primaryValues) && $primary != null) {
            $rowset = $_model->find($primaryValues);
        }
        if (count($rowset)) { // Existe?
            if (!$this->_modelsAcl->allowUpdate($this->_modelClass)) {
                throw new Rad_DataGateway_Controller_Action_NotAllowedException("Ud. no tiene permiso para realizar esta operación");
            }
            $row = $rowset->current();
            $row->setReadOnly(false);
            $row->setFromArray($datos);
            $inserting = false;
        } else { // No existe?, lo creamos
            if (!$this->_modelsAcl->allowInsert($this->_modelClass)) {
                throw new Rad_DataGateway_Controller_Action_NotAllowedException("Ud. no tiene permiso para realizar esta operación");
            }
            $row = $_model->createRow();
            $row->setFromArray($datos);
            $inserting = true;
        }

        // Vemos si vienen registros relacionados, si es asi guardamos todos dentro de una transaccion

        $regRel = array_diff_key($datos, $row->toArray());

        if (!empty($regRel)) {
            $id = $row->save();
            $datos[$p] = $id;

            foreach ($regRel as $_modelClass => $data) {
                // Si no es un _modelo saltearlo, TODO:  ver como filtrar regRel para que queden solo los _modelos
                if (substr($_modelClass, 0, 6) != "_model_")
                    continue;

                $childData = Zend_Json::decode($data);

                if (!empty($childData)) {

                    $child_model = new $_modelClass(array(), false);

                    foreach ($childData as $rowData) {
                        //if ($inserting) {
                        $references = $child_model->getReference(get_class($this->_model));
                        $rowData[$references['columns'][0]] = $datos[$references['refColumns'][0]];
                        //}
                        $this->_saveRecord($rowData, $child_model);
                    }
                }
            }
        } else {
            $id = $row->save();
        }

        $newRow = $_model->find($id)->current();

        if (!$newRow) {
            throw new Rad_DataGateway_Controller_Action_Exception("No se encontro el registro<br>Posiblemente un insert no retorno el id");
        }

        return $newRow;
    }

    /**
     * Borrar uno o mas registros del _modelo
     *
     */
    public function deleteAction()
    {
        //TODO: Ver el tema del multi primary key
        try {
            if (!$this->_modelsAcl->allowDelete($this->_modelClass)) {
                throw new Rad_DataGateway_Controller_Action_NotAllowedException("Ud. no tiene permiso para realizar esta operación");
            }
            $request = $this->getRequest();
            $data = $request->getPost();
            $this->constructModel();
            $primary = $this->_model->getPrimaryKeys();
            if (is_array($primary))
                $primary = $primary[1];

            $where = $this->_model->getAdapter()->quoteInto($primary . ' IN(?)', $data['id'], 'INTEGER');

            // tiene archivos anexos el modelo?
            $fieldsAttached = $this->_model->getAttachedFiles();
            $hasAttached = !empty($fieldsAttached);

            if ($where) {
                // si tiene anexos primero saco los rows
                if ($hasAttached) $rows = $this->_model->fetchAll($where);

                $this->_model->delete($where);

                // borro los archivos anexos de tenerlos
                if ($hasAttached) {
                    foreach ($rows as $row) {
                        $this->_deleteAttachedFiles($row);
                    }
                }
            } else {
                throw new Rad_DataGateway_Controller_Action_Exception("Parametro Where vacio!");
            }
            $msg->success = true;

        } catch (Rad_Db_Table_Exception $e) {
            $msg->success = false;
            $msg->msg     = $e->getMessage();
        }

        $this->_sendJsonResponse($msg);
    }

    /**
     * Borrar un registro del _modelo
     *
     */
    public function deleterowAction()
    {
        try {
            if (!$this->_modelsAcl->allowDelete($this->_modelClass)) {
                throw new Rad_DataGateway_Controller_Action_NotAllowedException("Ud. no tiene permiso para realizar esta operación");
            }
            $request = $this->getRequest();
            $data    = $request->getPost();
            $ids     = json_decode($data['rows']);

            $this->constructModel();

            $where = $this->_model->getAdapter()->quoteInto("Id in (?)", $ids, 'INTEGER');

            // tiene archivos anexos el modelo?
            $fieldsAttached = $this->_model->getAttachedFiles();
            $hasAttached = !empty($fieldsAttached);

            if ($where) {
                // si tiene anexos primero saco los rows
                if ($hasAttached) $rows = $this->_model->fetchAll($where);

                $this->_model->delete($where);

                // borro los archivos anexos de tenerlos
                if ($hasAttached) {
                    foreach ($rows as $row) {
                        $this->_deleteAttachedFiles($row);
                    }
                }
            } else {
                throw new Rad_DataGateway_Controller_Action_Exception("Parametro Where vacio!");
            }

            $msg->success = true;

        } catch (Rad_Db_Table_Exception $e) {
            $msg->success = false;
            $msg->msg = $e->getMessage();
        }

        $this->_sendJsonResponse($msg);
    }

    /**
     * Este Action actualiza un campo en la base
     *
     */
    public function savefieldAction()
    {
        $this->constructModel(true);

        // Permisos?
        if (!$this->_modelsAcl->allowUpdate($this->_modelClass)) {
            throw new Rad_DataGateway_Controller_Action_NotAllowedException("Ud. no tiene permiso para realizar esta operación");
        }

        $node  = $this->getRequest()->getParam("node");
        $field = $this->getRequest()->getParam("field");
        $value = $this->getRequest()->getParam("value");

        try {

            $rowset = $this->_model->find($node);
            if (!count($rowset))
                throw new Rad_Exception("El registro que intento modificar no existe");
            $row = $rowset->current();
            $row->setReadOnly(false);
            $row->$field = $value;
            $row->save();
            $msg->success = true;
            $msg->record = $this->_model->find($node)->current()->toArray();

        } catch (Rad_Db_Table_Exception $e) {

            $erromsg = "<b>" . $e->getMessage() . "</b><br>";
            foreach ($e->getFieldsErrors() as $field => $errors) {
                foreach ($errors as $error) {
                    $erromsg .= "$field: $error<br>";
                }
            }
            $msg->success = false;
            $msg->msg = $erromsg;

            if (APPLICATION_ENV == 'development') {
                $msg->line = $e->getLine();
                $msg->file = $e->getFile();
            }
        }

        $this->_sendJsonResponse($msg);
    }


    /**
     * Retorna un array con los registros devueltos por el fetch ($fetch)
     *
     * @param int|null $start
     * @param int|null $limit
     * @param string|null $sort
     * @param array|null $where
     * @param string|null $fetch
     */
    public function fetchRecordList(Rad_Db_Table_Select $select, $fetch = null)
    {
        if (!$this->_modelsAcl->allowView($this->_modelClass)) {
            throw new Rad_DataGateway_Controller_Action_NotAllowedException("Ud. no tiene permiso para realizar esta operación");
        }

        //$this->constructModel();

        if ($fetch) {
            $fetch = "fetch$fetch";
            if (!method_exists($this->_model, $fetch)) {
                throw new Zend_Exception("El metodo $fetch no existe en el _modelo " . get_class($this->_model));
            }
            $return = $this->_model->$fetch($select)->toArray();
        } else {
            $return = $this->_model->fetchAll($select)->toArray();
        }
        return $return;
    }


    /**
     * Retorna un array con los registros devueltos por el fetch y la cantidad de registros
     *
     * @param int|null $start
     * @param int|null $limit
     * @param string|null $sort
     * @param array|null $where
     * @param string|null $fetch
     */
    public function fetchRecordListWithCount(Rad_Db_Table_Select $select, $fetch = null)
    {
        $return          = array();
        $return['rows']  = $this->fetchRecordList($select, $fetch);
        $return['count'] = $this->fetchCount($where);
        return $return;
    }

    /**
     * Retorna la cantidad de registros dado el $where
     * @param $where
     * @return int
     */
    //TODO: Hacerla compatible con todas las bases. (El problema son los fetch particulares de los _modelos)
    public function fetchCount($where)
    {
        /* Implementacion anterior FALLA CON LOS FETCH PARTICULARES DE LOS _modelOS

          $select = $this->_model->select();
          $select->setIntegrityCheck(false);

          foreach ($where as $key => $val) {
          $select->where($val);
          }

          $select->from($this->_model->getName(), new Zend_Db_Expr('COUNT(*) AS cantidad'));
          $autoJoins = $this->_model->getAutoJoins();
          if (! empty($autoJoins)) {
          foreach ($autoJoins as $join) {
          $select->joinLeft($join[0], $join[1], array());
          }
          }
          $ret = $select->query()->fetchAll();
         */

        $cantidad = $this->_model->getAdapter()->fetchOne("select FOUND_ROWS()");
        //$cantidad = $db->query("select FOUND_ROWS()")->fetchOne();
        return (int) $cantidad; //$ret[0]['cantidad'];
    }

    public function indexAction()
    {
        throw new Exception('no tengo q estar aca');
    }
}
