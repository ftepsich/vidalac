<?php

/**
 * Rad_GridDataGateway_Controller_Action
 *
 * @copyright SmartSoftware Argentina
 * @package Rad
 * @subpackage DataGateway
 * @author Martin Alejandro Santangelo
 */
require_once 'Rad/DbFieldToExtMapper.php';
require_once 'Rad/CustomFunctions.php';
require_once 'Rad/DataGateway/Controller/Action.php';

/**
 * Rad_GridDataGateway_Controller_Action
 *
 * Esta clase sirve de gateway de acceso a todos los modelos
 * Provee automaticamente la MetaData necesaria para Ext.AutoGrid y la generacion de formularios
 *
 * @copyright SmartSoftware Argentina
 * @package Rad
 * @subpackage DataGateway
 * @author Martin Alejandro Santangelo
 */
class Rad_GridDataGateway_Controller_Action extends Rad_DataGateway_Controller_Action
{
    // const CARACTERISTICAS = array(
    //     'CAMPO_ENTERO'     => 1,
    //     'CAMPO_DECIMAL'    => 2,
    //     'CAMPO_FECHA'      => 3,
    //     'CAMPO_TEXTO'      => 4,
    //     'CAMPO_LISTA'      => 5,
    //     'CAMPO_BOLEANO'    => 6,
    //     'CAMPO_FECHAYHORA' => 7
    // );


    /**
     * Seccion del ini
     * @var string
     */
    protected $_iniSection;
    /**
     * instancia del generador de metadatos
     * @var Rad_GridDataGateway_ModelMetadata
     */
    protected $_metaGenerator;

    /**
     * Inicializa el Data Gateway
     *
     */
    public function init()
    {
        parent::init();
        $this->_iniSection = $this->getRequest()->getParam('section');
        if (!$this->_iniSection)
            $this->_iniSection = 'default';
    }

    /**
     * Instancia el modelo
     *
     * @param bool $withAutoJoins
     * @return bool
     */
    protected function constructModel($withAutoJoins = false)
    {
        parent::constructModel($withAutoJoins);

        $this->_metaGenerator = new Rad_GridDataGateway_ModelMetadata($this->_model, $this->_iniSection);
    }

    protected function _getUrl()
    {
        $req = $this->getRequest();
        return $this->view->url(
                array(
                    'controller' => $req->getControllerName(),
                    'module' => $req->getModuleName()
                ),
                "",
                true
        );
    }

    /**
     * Esta accion responde las peticiones de los combos del sistema,
     * y recibe un valor para filtrar los valores de ser un combo paginado.
     *
     */
    public function combolistAction()
    {
        $request = $this->getRequest();
        $query = $request->getParam("query");
        $reconfigure = $request->getParam("reconfigure");
        $id = $request->getParam("Id");
        // Agregamos el filtro de ser encesario
        $this->constructModel(true);
        $idRow = null;
        $recordNumber = 0;
        // Es una busqueda?
        if ($query) {
            $field = $this->getRequest()->getParam("search");
            $filter['field'] = $field;
            $filter['data']['type']  = 'string';         // TODO: No siempre es string implementar!
            $filter['data']['value'] = $query;
            $_POST['filter'][] = $filter;
        } else {
            // Si no es la primera peticion y mando los metadatos

            if ($reconfigure) {
                $this->_editableGrid = false;       // No enviar metadatos de los editores, no son necesarios en un combo
                $return['metaData'] = $this->_metaGenerator->getMetadata(false, false);
            }
            /*
             * Si se manda id tenemos q agregar este registro a la respuesta para que el combo tenga la descripcion del valor actual
             */
            if ($id) {
                $prim = $this->_model->getPrimaryKeys();
                if (is_array($prim))
                    $prim = $prim[1];
                $filter['field'] = $this->_model->getName() . '.' . $prim;
                $filter['data']['type'] = 'numeric';
                $filter['data']['value'] = $id;
                $filter['data']['comparison'] = 'ne';
                $_POST['filter'][] = $filter;
                $idRow = $this->_model->find($id)->toArray();
                $recordNumber = count($idRow);
            }
        }
        // Filtros adicionales
        $this->_setComboAditionalFilters();
        $fetch = $this->getRequest()->getParam("fetch");

        // Obtenemos el select del modelo ya con los limit seteados
        $select = $this->_getSelect(@$_POST['start'], @$_POST['limit']);
        // Agregamos el orden al select
        $this->_buildSort($select);
        // Agregamos el Where al select
        $this->_buildSelectWhere($select);
        // Traigo la consulta
        // $return = $this->fetchRecordListWithCount($select, $fetch);
        //
        if ($idRow) $select->where($this->_model->getName() . '.'. $prim.' <> '.$id);

        $return['rows']  = $this->fetchRecordList($select, $fetch);
        $return['count'] = $this->fetchCount($where);

        if ($idRow) {
            array_unshift($return['rows'], $idRow[0]);
            // $return['count'] = $return['count'] + 1;
        }

        $return['success'] = true;
        $this->_sendJsonResponse($return);
    }

    protected function _setComboAditionalFilters()
    {
        //TODO: Re hacer esta cagada
        foreach ($this->getRequest()->getParams() as $key => $value) {
            // Ignoro palabras reservadas
            if ($key == 'module' || $key == 'fetch' ||
                    $key == 'search' || $key == 'model' ||
                    $key == 'action' || $key == 'controller' ||
                    $key == 'pfilter' || $key == 'filter' ||
                    $key == 'Id' || $key == 'query' || $key == 'm' ||
                    $key == 'start' || $key == 'limit' || $key == 'section' ||
                    $key == 'sort' || $key == 'dir'
                    || $key == 'reconfigure')
                continue;
            $filter['field'] = $key;
            $filter['data']['type'] = 'numeric';
            $filter['data']['value'] = $value;
            $filter['data']['comparison'] = 'eq';
            $_POST['pfilter'][] = $filter;     //usamos pfilter para q filtre por el campo local y no el relacional en caso de estar relacionado
        }
    }

    /**
     * Action para listado de grillas con tilde Many to Many
     */
    public function listmtomAction()
    {
        $this->constructModel(true);

        $r = $this->getRequest();

        $start        = $r->getParam("start");
        $limit        = $r->getParam("limit");
        $fetch        = $r->getParam("fetch");
        $middleTable  = $r->getParam("intersectionModel");    //Tabla que establece la relacion entre las otras dos
        $reconfigure  = $r->getParam("reconfigure");
        $localTable   = $r->getParam("matchModel");
        $middleModulo = $r->getParam("intersectionModule");
        $localModulo  = $r->getParam("matchModule");
        $Id           = $r->getParam("Id");             //Id del row seleccionado de la tabla en la que estamos parados

        // Obtenemos el select del modelo ya con los limit seteados
        $select = $this->_getSelect($start, $limit);
        // Agregamos el orden al select
        $this->_buildSort($select);
        // Agregamos el Where al select
        $this->_buildSelectWhere($select);

        $prefix = (($middleModulo == 'default') ? '' : ucfirst($middleModulo) . '_');
        $middleTableClass = $prefix . "Model_DbTable_" . $middleTable;

        $prefix = (($localModulo == 'default') ? '' : ucfirst($localModulo) . '_');
        $localTableClass  = $prefix . "Model_DbTable_" . $localTable;

        //$refMap = $this->_model->getManyToManyRelationMap($middleTableClass, $localTableClass);
        $middleModel = new  $middleTableClass;

        $localRef = $middleModel->getReference($localTableClass);
        $remRef   = $middleModel->getReference(get_class($this->_model));

        if (!$localRef) {
            throw new Rad_GridDataGateway_Exception("No se encontro referencia de $middleModel a $localTableClass");
        }

        $j = $this->_model->getJoiner();

        $j->joinDep(
            $middleModel,
            array(
                'checked' => "if({remote}.".$localRef[Zend_Db_Table_Abstract::COLUMNS][0]." = $Id,1 , 0 ) as checked"
            ),
            null,
            "{remote}.".$localRef[Zend_Db_Table_Abstract::COLUMNS][0]." = $Id",
            false // no agregar grupo automaticamente a la consulta
        );

        $return = $this->fetchRecordListWithCount($select, $fetch);
        $return['success'] = true;
        //TODO: Ver si se puede arreglar desde el Javascript mas adelante
        if ($reconfigure) {
            //Si es para un combo o si esta marcado como no editable no se envia editor
            $return['metaData'] = $this->_metaGenerator->getMetadata();

            array_pop($return['metaData']->fields);
            //$lastField['plugin'] = new Zend_Json_Expr("new Ext.grid.CheckColumn({editable: true, type:'int',header: '',width: 10,dataIndex: 'checked', onlyDiferentValues: true})");

            array_unshift($return['metaData']->fields, array(
                    'type'      => 'int',
                    'xtype'     => 'checkcolumn',
                    'width'     => 10,
                    'align'     => 'right',
                    'name'      => 'checked',
                    'groupable' => false,
                    'header'    => '',
                    'plugin'    => new Zend_Json_Expr("new Ext.grid.CheckColumn({editable: true, type:'int',header: '',width: 10,dataIndex: 'checked', onlyDiferentValues: true})"),
                    'dataIndex' => 'checked'
                )
            );
        }

        $this->_sendJsonResponse($return);
    }

    /**
     * Guarda la relacion
     */
    public function savemanytomanyAction()
    {
        $request = $this->getRequest();
        $intersectionModel = $request->getParam("intersectionModel");    //Tabla que establece la relacion entre las otras dos
        $intersectionModule = $request->getParam("intersectionModule");    //Tabla que establece la relacion entre las otras dos
        $matchModel = $request->getParam("matchModel");
        $matchModule = $request->getParam("matchModule");

        try {
            $this->constructModel();
            $adpter = $this->_model->getAdapter();

            $prefix = (($intersectionModule == 'default') ? '' : ucfirst($intersectionModule) . '_');
            $intersectionModelClass = $prefix . "Model_DbTable_" . $intersectionModel;
            $prefix = (($matchModule == 'default') ? '' : ucfirst($matchModule) . '_');
            $matchModelClass = $prefix . "Model_DbTable_" . $matchModel;

            $refMap = $this->_model->getManyToManyRelationMap($intersectionModelClass, $matchModelClass);

            $intersectionTable = new $intersectionModelClass(array(), false);
            $datos = $request->getPost();

            $adpter->beginTransaction();

            foreach ($datos['changes'] as $change) {
                if ($change['state'] == "false") {
                    $intersectionTable->delete(
                            $adpter->quoteIdentifier($refMap['match'][Zend_Db_Table_Abstract::COLUMNS][0]) . " = " . $adpter->quote($datos['Id']) .
                            " and " .
                            $adpter->quoteIdentifier($refMap['caller'][Zend_Db_Table_Abstract::COLUMNS][0]) . " = " . $adpter->quote($change['id'])
                    );
                } else {
                    $intersectionTable->insert(
                            array(
                                $refMap['match'] [Zend_Db_Table_Abstract::COLUMNS][0] => $datos['Id'],
                                $refMap['caller'][Zend_Db_Table_Abstract::COLUMNS][0] => $change['id']
                            )
                    );
                }
            }

            // Publico...
            Rad_PubSub::publish('DG_POST_SAVE_M2M_'.get_class($intersectionTable), $datos);

            $adpter->commit();
            $msg->success = true;
        } catch (Exception $e) {
            $adpter->rollback();
            $msg->success = false;
            $msg->msg = $e->getMessage();
        }

        $this->_sendJsonResponse($msg);
    }

    /**
     * Retorna la metadata para el autoGrid
     * y los datos del modelo
     * Soporta filtros
     *
     */
    public function listAction()
    {
        $this->constructModel(true);
        $fetch = $this->getRequest()->getParam("fetch");
        $reconfigure = $this->getRequest()->getParam("reconfigure");

        $start = $this->getRequest()->getParam("start");
        $limit = $this->getRequest()->getParam("limit");

        // Obtenemos el select del modelo ya con los limit seteados
        $select = $this->_getSelect($start, $limit);
        // Agregamos el orden al select
        $this->_buildSort($select);
        // Agregamos el Where al select
        $this->_buildSelectWhere($select);
        // Traigo la consulta
        $return = $this->fetchRecordListWithCount($select, $fetch);

        if ($reconfigure) {
            $return['metaData'] = $this->_metaGenerator->getMetadata();
        }

        $extraMetadata = Rad_PubSub::publish('Rad_GridDataGateway_ListPostProcess/'.$this->_modelClass.'/'.$this->_iniSection, $return, $start, $limit, $sort, $select, $fetch);

        if (is_array($extraMetadata)) $return = Rad_CustomFunctions::mergeConf($return, $extraMetadata);

        $return['success'] = true;

        $this->_sendJsonResponse($return);
    }

    /**
     * Este Action Guarda los metadatos del AutoGrid si se seteo un adaptador
     *
     */
    public function savemetadataAction()
    {
        $this->getResponse()->setHeader('Content-Type', 'text/javascript');
        if ($this->_metaDataCache) {
            try {
                $this->_metaDataCache->save($this->getRequest()->getParam('fields'));
                $this->getResponse()->setBody("{'success':true}");
            } catch (Exception $e) {
                $this->getResponse()->setBody("{'success':false,'msg':'$e->getMessage()'}");
            }
        }
    }

    /**
     * Retorna una miniatura
     */
    public function getthumbnailfileAction()
    {
        // verificamos permisos
        if (!$this->_modelsAcl->allowView($this->_modelClass)) {
            throw new Rad_DataGateway_Controller_Action_NotAllowedException("Ud. no tiene permiso para realizar esta operación");
        }

        $request = $this->getRequest();

        $ext     = $request->ext;
        $id      = $request->id . '.' .$ext;
        $field   = $request->field;
        $size    = $request->size;

        $iniConfig = Rad_GridDataGateway_ModelMetadata::getModelClassIni($this->_modelClass, $this->_iniSection.'Thumbnails');

        $provider  = Service_AttachedFileProvider::getProvider();

        $width  = $iniConfig[$field][$size]['width'];
        $height = $iniConfig[$field][$size]['height'];

        if (!$width || !$height) throw new Rad_GridDataGateway_Exception("No se encuentra configurado el tamaño $size de la miniatura del campo $field en el modelo $this->_modelClass");

        $provider->sendThumbnail($id, $this->_modelClass, $width ,$height);
    }

    /**
     * Retorna una miniatura
     */
    public function downloadfileAction()
    {
        // verificamos permisos
        if (!$this->_modelsAcl->allowView($this->_modelClass)) {
            throw new Rad_DataGateway_Controller_Action_NotAllowedException("Ud. no tiene permiso para realizar esta operación");
        }

        $request = $this->getRequest();

        $ext     = $request->ext;
        $id      = $request->id . '.' .$ext;

        $field   = $request->field;

        $provider  = Service_AttachedFileProvider::getProvider();

        $provider->downloadFileFromId($id, $this->_modelClass);
    }

    public function getattachedfileAction()
    {
        // verificamos permisos
        if (!$this->_modelsAcl->allowView($this->_modelClass)) {
            throw new Rad_DataGateway_Controller_Action_NotAllowedException("Ud. no tiene permiso para realizar esta operación");
        }

        $request = $this->getRequest();

        $ext     = $request->ext;
        $id      = $request->id . '.' .$ext;

        $provider = Service_AttachedFileProvider::getProvider();

        $provider->sendFileFromId($id, $this->_modelClass);
    }

    /**
     * Retorna una lista con los valores para una caracteristica
     */
    public function getcaracteristicaslistaAction()
    {
        if (!$this->_modelsAcl->allowView($this->_modelClass)) {
            throw new Rad_DataGateway_Controller_Action_NotAllowedException("Ud. no tiene permiso para realizar esta operación");
        }

        $request = $this->getRequest();

        if (!$request->caracteristica) throw new Rad_Exception('Falta el parametro caracteristica');

        // $model = new Model_DbTable_CaracteristicasListas;

        $db = Zend_Registry::get('db');
        $caracteristica = $db->quote($request->caracteristica,'INTEGER');

        // $lista = $model->fecthAll('Caracteristica = '.$caracteristica);

        $lista = $db->fetchAll('SELECT Id as id, Valor as description FROM CaracteristicasListas WHERE Caracteristica = '.$caracteristica);


        $this->_sendJsonResponse(array('success' => true, 'rows' => $lista, 'count' => count($lista)));
    }

    public function getcaracteristicasAction()
    {
        try {
            $request = $this->getRequest();
            $db = Zend_Registry::get('db');
            $id = $db->quote($request->id,'INTEGER');

            $valores = Model_DbTable_CaracteristicasValores::getCaracteristicasValores($this->_modelClass, $id);


            $caracteristicas = Model_DbTable_CaracteristicasModelos::getCaracteristicas($this->_modelClass);

            $dataf = array();
            foreach($caracteristicas as $k => $v) {
                $v['Valor'] = $valores[$v['Descripcion']];

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

    public function setcaracteristicasAction()
    {
        if (!$this->_modelsAcl->allowUpdate($this->_modelClass)) {
            throw new Rad_DataGateway_Controller_Action_NotAllowedException("Ud. no tiene permiso para realizar esta operación");
        }

        // leo el request
        $request    = $this->getRequest();
        $value      = $request->value;
        $for        = $request->id;
        $property   = $request->property;

        // creo el modelo sin joins
        $this->constructModel(false);

        try {
            $db        = Zend_Registry::get('db');

            $sujeto = $this->_model->find($for)->current();

            if (!$sujeto) throw new Rad_Exception('No se encontro el registro al que quiere cambiarle la caracteristica');

            $valores = new Model_DbTable_CaracteristicasValores;

            $caracteristicasModelos = new Model_DbTable_CaracteristicasModelos;

            $caracteristicas = Model_DbTable_CaracteristicasModelos::getCaracteristicas($this->_modelClass);

            //Existe la propiedad?
            $idCM = null;
            foreach ($caracteristicas as $campo) {
                if ($campo['Descripcion'] == $property) {
                    $idCM = $campo['IdCM'];
                    break;
                }
            }

            if (!$idCM) throw new Rad_Exception('La caracteristica no existe para este modelo');


            //$value      = $db->quote($value);
            $for       = $db->quote($for,'INTEGER');
            $qproperty = $db->quote($property);

            $row = $valores->fetchRow("CaracteristicaModelo = $idCM AND IdModelo = $for");
            if (!$row) $row = $valores->createRow();

            //si es una Fecha acomodamos el valor
            if ($campo['TipoDeCampo'] == 3 || $campo['TipoDeCampo'] == 7) {
                $value = str_replace('T',' ', $value);
            }

            $row->IdModelo = $for;
            $row->Valor    = $value;
            $row->FechaAlta = date('Y-m-d');
            $row->CaracteristicaModelo = $idCM;
            $row->save();

            echo "{success: true}";
        } catch (Exception $e) {
            echo "{success: false, msg: '".addslashes($e->getMessage()) ."'}";
        }
    }
}
