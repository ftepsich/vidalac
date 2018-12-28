<?php

class TrazaNodo
{
    public $name;
    public $children;
    public $id;
    public $realid;
    public $data;
    public function add($child)
    {
        $this->children[] = $child;
    }  
}

class TrazaNodoAgrupado extends TrazaNodo
{
    protected $_group;

    protected $_groupsObj = array();

    public function __construct($group)
    {
        // Rad_Log::debug($group);
        $this->_group = $group;
    }

    public function add($child)
    {
        // agrupo solo mmis
        if ($child->data['tipo'] == 2) {

            $f = $this->_group;
            // si
            $g = $child->data[$f];
            // Rad_Log::debug($child->data);
            // Rad_Log::debug($this->_group);

            if (!$this->_groupsObj[$g]) {
                // Rad_Log::debug('agregando grupo '.$g);
                $tn = new TrazaNodo();
                $tn->id   = 1000+count($this->_groupsObj);
                $tn->data = array(
                    'tipo' => '4',
                    'Descripcion' => strstr($g, ' ', true),
                    'Grupo' => $g,
                    'Cantidad'=>0
                );
                $tn->name = $g;

                $this->_groupsObj[$g] = $tn;
                parent::add($this->_groupsObj[$g]);
            }
            $this->_groupsObj[$g]->data['Cantidad']++;
            $this->_groupsObj[$g]->add($child);
        }
    }
}

/**
 * Almacenes_ControlStockController
 *
 * Control de Stock
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @author Martin A. Santangelo
 * @subpackage Almacenes
 * @class Almacenes_ControlStockController
 * @extends Rad_Window_Controller_Action
 */
class Almacenes_TrazabilidadController extends Rad_Window_Controller_Action
{
    protected $title = 'Trazabilidad';

    public function initWindow()
    {
        
    }
    
    public function trazammiAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        
        $db = Zend_Registry::get('db');
        
        $request = $this->getRequest();
        $id = $request->getParam('id');
        $id = $db->quote($id, 'INTEGER');
        
        if (!$id) {
            //throw new Rad_Exception('No se envio el articulo');
             $this->_sendJsonResponse(array('success'=>false, 'msg'=>'No se envio el id'));
             exit;
        }
        
//        $rtn = Rad_Util_SqlGrid::fetch("select DATE_ADD(fecha,INTERVAL 1 DAY)as Fecha , fStockArticuloFecha($articulo,fecha) as Stock from tUltimos30Dias order by num desc",$_POST['limit'],$_POST['start']);
        $db->query("call RecorridoMmi($id)");
        $rtn = Rad_Util_SqlGrid::fetch("SELECT Fecha, Descripcion, Cantidad FROM MmiMov_temp ORDER BY Fecha,Mmi;",$_POST['limit'],$_POST['start']);
        //$db->beginTransaction();
        $this->_sendJsonResponse($rtn);
    }
    
    public function trazaAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        
        $this->db   = Zend_Registry::get('db');
        
        $id   = $this->getRequest()->id;
        $tipo = $this->getRequest()->tipo;
        
        $id   = $this->db ->quote($id, 'INTEGER');
        $tipo = $this->db ->quote($tipo, 'INTEGER');
        
        $rtn = $this->_armarArbol($id,$tipo);
        $rtn->success = true;
        
      
        $this->_sendJsonResponse($rtn);
    }
    
    protected function _armarArbol($id, $tipo)
    {
        $rows = $this->db->fetchAll("call TrazabilidadPorMmi($id,$tipo);");
        // fix de error "command out of sync"
        //while($this->db->getConnection()->next_result()){};
        $raiz = $this->_getNodo($rows[0]['Id'],$rows[0]['Tipo']);
        
        unset($rows[0]);

        $treeArray = array();

        $treeArray[$id][$tipo] = $raiz;
        
        // armo el subarbol
        
        foreach ($rows as $node)
        {
            
            $objNode = $this->_getNodo($node['Id'],$node['Tipo']);
            
            // Lo agrego al array temporal
            $treeArray[$node['Id']][$node['Tipo']] = $objNode;
            
            $treeArray[$node['Padre']][$node['TipoPadre']]->add($objNode);
        }
        
//
//        foreach ($treeArray as $nodes) {
//            foreach($nodes as $node) {
//                if ($id != $node->realid && $node->data['tipo'] != $tipo && !empty($node->children)) {
//                    Rad_Log::debug($node->realid.", ".$node->data['tipo']);
//                    $node = $this->_armarArbol($node->realid, $node->data['tipo']);
//                }
//                
//            }
//        }
        
        return $raiz;
              
    }
    
    protected function _getNodo($id,$tipo)
    {
        //$db = Zend_Registry::get('db');
        if ($tipo == 3) {
            $nodo = new TrazaNodoAgrupado('Descripcion');
        } else {
            $nodo = new TrazaNodo();
        }
        
        $nodo->id   = $id.'-'.$tipo;
        $nodo->realid   = $id;
        $nodo->name = $id;
        $nodo->data = $this->_getNodoDetalle($id,$tipo);
        $nodo->data['tipo'] = $tipo;
        
        return $nodo;
    }
    
    protected function _getNodoDetalle($id,$tipo)
    {

        
        // segun el tipo traemos los datos adicionales
        switch($tipo) {
            case 1:
                // si es remito
                $dato = $this->db->fetchRow("Select R.Id, R.Numero, R.FechaEmision, R.FechaEntrega, P.RazonSocial from Comprobantes R left join Personas P on P.Id = R.Persona where R.Id = $id");
                break;
            case 2:
                  // si es mmi
                $dato = $this->db->fetchRow("
                    Select M.Id, M.Identificador, M.FechaIngreso, M.FechaVencimiento, M.FechaCierre, M.Descripcion, A.Descripcion
                        From Mmis M 
                        join Articulos A on A.Id = M.Articulo
                        where M.Id=$id
                ");
                break;
            case 3:
                // si es orden de produccion
                $dato = $this->db->fetchRow("
                   SELECT OP.Id, A.Descripcion as Articulo, OP.Cantidad, FechaOrdenDeProduccion , OPE.Descripcion as Estado FROM OrdenesDeProducciones OP
                    left join Articulos A on A.Id = OP.Articulo
                    left join OrdenesDeProduccionesEstados OPE on OPE.Id = OP.Estado 
                    where OP.Id=$id
               ");
                break;
                
        }
        return $dato;
    }

}