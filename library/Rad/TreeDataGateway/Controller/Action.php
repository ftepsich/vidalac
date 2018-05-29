<?php

/**
 * Tree DataGateway
 *
 * @class Rad_TreeDataGateway_Controller_Action
 * @extends Rad_DataGateway_Controller_Action
 */
require_once 'Rad/DbFieldToExtMapper.php';
require_once 'Rad/CustomFunctions.php';
require_once 'Rad/DataGateway/Controller/Action.php';

class Rad_TreeDataGateway_Exception extends Zend_Exception
{

}

/**
 * Esta clase sirve de gateway de acceso a todos los modelos
 * Provee automaticamente la MetaData necesaria para Ext.AutoGrid y la generacion de formularios
 *
 * @author Martin Alejandro Santangelo
 * @version 1.0
 */
class Rad_TreeDataGateway_Controller_Action extends Rad_DataGateway_Controller_Action
{
    /**
     * Seccion del ini
     * @var string
     * TODO: No se esta usando para nada... VER!
     *
     */
    protected $_iniSection;
    /**
     * Instancia del generador de metadatos
     * @var Rad_AutoGridGateway_ModelMetadata
     *
     * TODO: No se esta usando para nada... VER!
     */
    protected $_metaGenerator;

    /**
     * Inicializa el Data Gateway
     *
     */
    public function init ()
    {
        parent::init();
        $this->_iniSection = $this->getRequest()->getParam('section');
        if (!$this->_iniSection) {
            $this->_iniSection = 'default';
        }
    }

    /**
     * Instancia el modelo
     *
     * @param bool $withAutoJoins
     * @return bool
     */
    protected function constructModel ($withAutoJoins = false)
    {
        parent::constructModel($withAutoJoins);

        // Lo mismo de la declaracion de la propiedad
        $this->_metaGenerator = new Rad_GridDataGateway_ModelMetadata($this->_model, $this->_iniSection);
    }

    protected function _getUrl ()
    {
        $req = $this->getRequest();
        return $this->view->url(
                array(
                    'controller' => $req->getControllerName(),
                    'module' => $req->getModuleName()
                ),
                '',
                true
        );
    }

    /**
     * Retorna la metadata para el Tree
     * Soporta filtros.. ?
     *
     */
    public function listAction ()
    {
        $this->constructModel(true);

        $this->_ref = $this->getRequest()->getParam('ref');
        $this->_refMap = $this->_model->getReference(get_class($this->_model), $this->_ref);

        if (!$this->_ref || !$this->_refMap)
            throw new Rad_TreeDataGateway_Exception('No se definio la relacion del arbol');

        $fetch = $this->getRequest()->getParam('fetch');

        // Obtenemos el select del modelo ya con los limit seteados
        $select = $this->_getSelect($start, $limit);
        // Agregamos el orden al select
        $this->_buildSort($select);
        // Agregamos el Where al select
        $this->_buildSelectWhere($select);

        $this->_sendJsonResponse($this->fetchChildrens($select, $fetch));
    }

    /**
     * Devuelve los hijos de un nodo, o un boolean si tiene o no hijos
     */
    function fetchChildrens ($select, $fetch = null, $count = false)
    {
        $displayField = $this->_refMap['refJoinColumns'][0];
        $parentField = $this->_refMap['columns'][0];

        if ($fetch) {
            $fetchMethod = "fetch$fetch";
            $childrens = $this->_model->$fetchMethod($select);
        } else {
            $childrens = $this->_model->fetchAll($select);
        }

        if (!$count) {
            $return = array();
            foreach ($childrens as $row) {
                $select2 = $this->_getSelect();
                $this->_setWhereFromFilter($select2, $row->Id);

                $return[] = array(
                    'id' => $row->Id,
                    'text' => $row->$displayField,
                    'data' => $row->toArray(),
                    'leaf' => !(bool) $this->fetchChildrens($select2)
                );
            }
            return $return;
        } else {
            return (bool) count($childrens);
        }
    }

    /**
     * Construye el where de la busqueda segun los parametros enviados por POST
     */
    protected function _buildSelectWhere (Rad_Db_Table_Select $select)
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $filters = $request->getParam('filter');
            // TODO ver los metadatos porque puede servir para algo
            //$_modelInfo = $this->_model->getMetadataWithJoins();
            //Rad_Log::debug($this->_model);
            $this->_setWhereFromFilter($select, $filters/* , $_modelInfo */);
        }
    }

    /**
     * Arma las condiciones del where a partir de los filtros pasados (Ver formato de filtros)
     *
     * @param array $filters
     * @param array $_modelInfo
     * @param bool $searchInCombo
     * @return string|null
     */
    protected function _setWhereFromFilter ($select, $node/* , $_modelInfo */)
    {
        if ($node === 'root') {
            $select->where($this->_model->getName() . '.' . $this->_refMap['columns'][0] . ' IS NULL');
        } else {
            $select->where($this->_model->getName() . '.' . $this->_refMap['columns'][0] . ' = ?', $node);
        }
    }

    /**
     * Borrar un registro del modelo
     *
     */
    public function deleterowAction ()
    {
        try {
            if (!$this->_modelsAcl->allowDelete($this->_modelName)) {
                throw new Rad_TreeDataGateway_Controller_Action_Exception("Ud. no tiene permiso para realizar esta operación");
            }
            $request = $this->getRequest();
            $data = $request->getPost();
            $ids = json_decode($data['rows']);
            $this->_ref = $data['ref'];

            $this->constructModel();
            $where = $this->_model->getAdapter()->quoteInto("Id in (?)", $ids, 'INTEGER');

            if ($where) {
                $this->recursiveDelete($where);
            } else {
                throw new Rad_TreeDataGateway_Controller_Action_Exception("Parametro Where vacio!");
            }
            $msg->success = true;
        } catch (Rad_Db_Table_Exception $e) {
            $msg->success = false;
            $msg->msg = $e->getMessage();
        }
        $this->_sendJsonResponse($msg);
    }

    /**
     * Mueve un nodo
     */
    public function moverowAction ()
    {
        try {
            /*
             * // TODO: checkear permisos correspondientes
             *
            if (!$this->_modelsAcl->allowDelete($this->_modelName)) {
                throw new Rad_TreeDataGateway_Controller_Action_Exception("Ud. no tiene permiso para realizar esta operación");
            }
            */
            $request = $this->getRequest();
            $data = $request->getPost();
            $node = $data['rows'];
            $to = $data['to'];
            $this->_ref = $data['ref'];

            $this->constructModel();
            $where = $this->_model->getAdapter()->quoteInto("Id in (?)", $to, 'INTEGER');

            if ($where) {
                $nodeRow = $this->_model->find($node)->current();
                $parentField = $this->_model->getParentField();
                $nodeRow->$parentField = $to;
                $nodeRow->save();
            } else {
                throw new Rad_TreeDataGateway_Controller_Action_Exception("Parametro Where vacio!");
            }
            $msg->success = true;
        } catch (Rad_Db_Table_Exception $e) {
            $msg->success = false;
            $msg->msg = $e->getMessage();
        }
        $this->_sendJsonResponse($msg);
    }

    /**
     * Borra recursivamente un nodo
     */
    protected function recursiveDelete ($where)
    {
        $rows = $this->_model->fetchAll($where);
        foreach ($rows as $row) {
            $dependents = $row->findDependentRowset($this->_modelClass, $this->_ref);
            if (!$dependents) {
                $row->delete();
            } else {
                $this->recursiveDelete('Padre = '.$row->Id);
                $row->delete();
            }
        }
    }

    /**
     * Obtiene el path de un nodo para el arbol
     */
    public function getnodepathAction ()
    {
        $request = $this->getRequest();
        $data = $request->getPost();
        $this->_ref = $data['ref'];
        $nodeId = $data['from'];

        $this->constructModel();

        $parentField = $this->_refMap['columns'][0];
        $root = false;
        $path = array($nodeId);
        while (!$root) {
            $node = $this->_model->fetchAll(
                $this->_model->select()->where('PlanesDeCuentas.Id = ?', $nodeId)
            );
            if ($node->$parentField) {
                $path[] = $node->$parentField;
                $nodeId = $node->$parentField;
            } else {
                $path[] = 'root';
                $root = true;
            }
        }
    }

    /**
     * Este Action Guarda los metadatos del AutoGrid si se seteo un adaptador
     *
     */
    public function savemetadataAction ()
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

}
