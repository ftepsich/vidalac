<?php
/**
 * 
 */
class UtilesController extends Zend_Controller_Action
{
    public function init ()
    {
        /* Initialize action controller here */
        ini_set("display_errors", 1);
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            $this->_helper->redirector('index', 'auth');
        }
    }
    
    protected function test1Action()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        
        $cajas = new Contable_Model_DbTable_CajasMovimientos();
        $c = $cajas->createRow();
        echo "<pre>";
        print_r($c->toArray());
         $db = $cajas->getAdapter();
         $db->beginTransaction();
         $c->save();
//        print_r($c->_modifiedFields);
         
    }
    
    
    public function fegettipostributosAction()
    {
        require_once 'FactElect/wsfev1.php';
        $this->_helper->viewRenderer->setNoRender(true);

        echo '<pre>';
        print_r(FactElect_Wsfev1::FECompConsultar(
                    6,
                    39,
                    6
                ));
    }

    public function xmlAction ()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        
        $report = new Rad_BirtEngine();
        $report->setParameter('Id', 1, 'Int');
        $xml = simplexml_load_file(APPLICATION_PATH . '/../birt/Reports/ListadoDeChequesPropios.rptdesign');

        //$xml = new SimpleXMLElement($content);
        echo '<pre>';
        $dataSources = htmlentities($xml->{'data-sources'}->asXML());
        print $dataSources;

        $tag_start = preg_quote('property name="odaURL"', '/');
        echo PHP_EOL.'START: '.$tag_start;
        $tag_end = preg_quote('property', '/');
        echo PHP_EOL.'START: '.$tag_end;

        echo PHP_EOL."/<$tag_start>(.*?)<\/$tag_end>/";
        echo PHP_EOL.'--------------------------------------------------------'.PHP_EOL;
        preg_match_all("/<$tag_start>(.*?)<\/$tag_end>/", $dataSources, $results);
        //"/<tag>(.*?)<\/tag>/"
        var_dump($results);

        //Rad_Log::debug($xml);
        //$report->renderFromString((string) $xml, $formato);
        //$report->sendStream();
    }

    public function testAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);

        $stock = new Almacenes_Model_Stock();
        echo  $stock->getStockProducto(53,5)."<br>";
        echo $stock->getStock(4);
    }
    
    public function recalcularctacteylibroivaAction()
    {
        set_time_limit(0);
        ignore_user_abort();
        $this->_helper->viewRenderer->setNoRender(true);

        $cuenta = new Contable_Model_DbTable_CuentasCorrientes();
        $libro  = new Contable_Model_DbTable_LibrosIVADetalles();

        // Facturas y notas entrada
        $modelo = new Facturacion_Model_DbTable_FacturasCompras();

        $db = $modelo->getAdapter();
        $db->query('truncate table CuentasCorrientes;');
        $db->query('truncate table LibrosIVADetalles;');

        $rowset = $modelo->fetchAll('Comprobantes.Anulado = 0 And Comprobantes.Cerrado = 1 AND Comprobantes.TipoDeComprobante in (19, 20, 21, 22, 23, 33, 34, 35, 36, 41, 42, 43, 44,47,49,50)');

        foreach ($rowset as $comprobante) {
            echo "Cerrando $comprobante->Id Tipo $comprobante->TipoDeComprobante<br>";
            //ob_flush();
            $cuenta->asentarComprobante($comprobante);
            $libro->asentarLibroIVA($comprobante);
        }

        // Facturas y notas salida
        $modelo = new Facturacion_Model_DbTable_FacturasVentas();

        $rowset = $modelo->fetchAll('Comprobantes.Anulado = 0 And Comprobantes.Cerrado = 1 AND Comprobantes.TipoDeComprobante in (24, 25, 26, 27, 28, 29, 30, 31, 32, 37, 38, 39, 40)');

        foreach ($rowset as $comprobante) {
            echo "Cerrando $comprobante->Id $comprobante->TipoDeComprobante<br>";
            $cuenta->asentarComprobante($comprobante);
            $libro->asentarLibroIVA($comprobante);
        }


        // Facturas y notas salida Anuladas
        // Las nuestras deben estar todas en el libro de iva, las anuladas iran con valor 0
        $rowset = $modelo->fetchAll('Comprobantes.Anulado = 1 And Comprobantes.Cerrado = 1 AND Comprobantes.TipoDeComprobante in (24, 25, 26, 27, 28, 29, 30, 31, 32, 37, 38, 39, 40)');
        foreach ($rowset as $comprobante) {
            echo "Cerrando $comprobante->Id $comprobante->TipoDeComprobante<br>";
            $libro->asentarLibroIVA($comprobante);
            $libro->quitarComprobante($comprobante); //---> pone los valores en 0 para las nuestas anuladas
        }
		
        // Pagos
        $modelo = new Facturacion_Model_DbTable_OrdenesDePagos();

        $rowset = $modelo->fetchAll('Comprobantes.Anulado = 0 And Comprobantes.Cerrado = 1 AND Comprobantes.TipoDeComprobante = 7');

        foreach ($rowset as $comprobante) {
            echo "Cerrando $comprobante->Id $comprobante->TipoDeComprobante<br>";
			echo "-----Ini CC P: <br>";
            $cuenta->asentarComprobante($comprobante);
			echo "-----Ini lib iva P: <br>";
            $libro->asentarLibroIVA($comprobante);
        }

        // Cobros
        $modelo = new Facturacion_Model_DbTable_Recibos();

        $rowset = $modelo->fetchAll('Comprobantes.Anulado = 0 And Comprobantes.Cerrado = 1 AND Comprobantes.TipoDeComprobante in(5,6,8,9)');

        foreach ($rowset as $comprobante) {
            echo "Cerrando $comprobante->Id $comprobante->TipoDeComprobante<br>";
			echo "-----Ini CC C: <br>";
            $cuenta->asentarComprobante($comprobante);
			echo "-----Ini lib iva C: <br>";
            $libro->asentarLibroIVA($comprobante);
        }
        echo 'ok!';
    }
    
    public function getcontrollersAction ()
    {
        set_time_limit(0);
        ignore_user_abort();
        $this->_helper->viewRenderer->setNoRender(true);
        
        $front = $this->getFrontController();
        $acl = array();

        echo '<pre>';
        foreach ($front->getControllerDirectory() as $module => $path) {
            if ($module == 'Window') continue;
            foreach (scandir($path) as $file) {
                if ($file[0] !== '.') {
                    if (!is_file($path . DIRECTORY_SEPARATOR . $file)) continue;
                    if ($file == 'IndexController.php') continue;
                    
                    include_once $path . DIRECTORY_SEPARATOR . $file;
                    foreach (get_declared_classes() as $class) {
                        $controller = substr($class, 0, strpos($class, "Controller"));
                        $actions = array();
                        foreach (get_class_methods($class) as $action) {
                            if (strstr($action, "Action") !== false) {
                               $actions[] = $action;
                            }
                        }
                    }
                    $acl[$module][$controller] = $actions;
                }
            }
        }
        print_r($acl);
    }
    
    public function getmodelsAction ()
    {
        set_time_limit(0);
        ignore_user_abort();
        $this->_helper->viewRenderer->setNoRender(true);
        
        $front = $this->getFrontController();
        $acl = array();
        
        echo '<pre>';
        $classes = array();
        
        foreach ($front->getControllerDirectory() as $module => $path) {
            if ($module == 'Window') continue;
            echo '================================'.PHP_EOL.$module.PHP_EOL.'================================';
            $path = substr($path, 0, strlen($path)-12);
            $path = $path . DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'DbTable';
            foreach (@scandir($path) as $file) {
                if (strpos($file, ".php") !== false) {
                    $classes = array_merge(file_get_php_classes($path.DIRECTORY_SEPARATOR.$file), $classes);
                }
            }
        }
        sort($classes);
        
        foreach ($classes as $class) {
            echo $class.PHP_EOL;
            $modelos = new Model_DbTable_Modelos();
            $row = $modelos->fetchAll("Descripcion like '%".$class."%'");
            if (!count($row)) {
                $modelos->insert(array(
                        'Descripcion' => $class
                    )
                );
            }
        }
    }
    
    public function reflectionAction ()
    {
        set_time_limit(0);
        ignore_user_abort();
        $this->_helper->viewRenderer->setNoRender(true);  
    }
    
    
}

function file_get_php_classes ($filepath)
{
    $php_code = file_get_contents($filepath);
    $classes = get_php_classes($php_code);
    return $classes;
}

function get_php_classes ($php_code)
{
    $classes = array();
    $tokens = token_get_all($php_code);
    $count = count($tokens);
    for ($i = 2; $i < $count; $i++) {
        if ($tokens[$i - 2][0] == T_CLASS && $tokens[$i - 1][0] == T_WHITESPACE && $tokens[$i][0] == T_STRING) {
            $class_name = $tokens[$i][1];
            $classes[] = $class_name;
        }
    }
    return $classes;
}