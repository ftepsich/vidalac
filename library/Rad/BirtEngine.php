<?php
/**
 * Rad_BirtEngine
 *
 * Esta clase instancia el Engine del Birt Report usando el JavaBridge de Zend
 * Permitiendo cargar, pasarle parametros y renderizar un reporte
 *
 * @package Rad
 * @subpackage BirtEngine
 * @copyright SmartSoftware Argentina
 * @author Martin Alejandro Santangelo
 */
define ('BIRT_REPORTS_PATH', APPLICATION_PATH."/../birt/");
require_once("http://127.0.0.1:8080/JavaBridge/java/Java.inc");
//java_require('/usr/lib64/jvm/java-6-sun/jre/lib/');

/**
 * BirtEngine
 * 
 * Motor de Reportes del sistema
 *
 * @copyright SmartSoftware Argentina
 * @package Rad
 * @subpackage BirtEngine
 * @author Martin Alejandro Santangelo
 */
class Rad_BirtEngine
{
    public static $TagsFile = '/configs/reporttags.yml';

    private $_birtEngine = null;
    private $_config = null;
    private $_reportFile = null;
    private $_outputStream = null;
    private $_renderedFormat = null;
    private $_params = array();

    /**
     * Constructor de la clase
     */
    public function __construct()
    {
        $this->_createEngine();
    }

    /**
     * retorna la instancia de Java para un Formato de salida $format
     */
    function getOutputFormat($format)
    {
        $fmt = null;
        switch ($format) {
            case "pdf":
                $fmt = new java("org.eclipse.birt.report.engine.api.PDFRenderOption");
               
                $fmt->setOutputFormat("pdf");
                break;
            case "html":
                $publicPath = realpath(APPLICATION_PATH."/../public/");
                $fmt = new java("org.eclipse.birt.report.engine.api.HTMLRenderOption");
               
                $ih = new java("org.eclipse.birt.report.engine.api.HTMLServerImageHandler");

                $fmt->setImageHandler($ih);
                $fmt->setBaseImageURL('/sessionChartImages/'. session_id());
                $fmt->setImageDirectory($publicPath."/sessionChartImages/" . session_id());
                $fmt->setEnableAgentStyleEngine(true);
                $fmt->setOutputFormat("html");
                break;
            case "msword":
                $fmt = new java("org.eclipse.birt.report.engine.api.RenderOption");
                $fmt->setOutputFormat("doc");
                break;
            case "xls":
                $fmt = new java("org.eclipse.birt.report.engine.api.RenderOption");
                $fmt->setOutputFormat("xls");
                break;
            default:
                throw new Rad_BirtEngine_Exception("Rad_BirtEngine: formato '$format' desconocido");
        }
        return $fmt;
    }

    /**
     * Setea parametros al reporte
     * 
     * @param string $param Nombre del parametro
     * @param mixed  $value Valor del parametro
     * @param string $type  Tipo de dato int|string|datetime
     */
    public function setParameter($param, $value, $type = 'int')
    {
        $type = strtolower($type);

        switch ($type) {
            case 'int':
                $this->_params[$param] = new Java("java.lang.Integer", $value);
                break;
            case 'string':
                $this->_params[$param] = new Java("java.lang.String", $value);
                break;
            case 'datetime':
                // el Date de Java va en milisegundos, el timestamp de PHP en segundos
                $this->_params[$param] = new Java("java.util.Date", strtotime($value)*1000);
                break;
            default:
                throw new Rad_BirtEngine_Exception("Rad_BirtEngine: tipo $type no soportado");
        }
    }

    /**
     * Instancia el engine del birt
     */
    private function _createEngine()
    {
        ///$lc = new java("java.util.Locale", "es_AR");

        $ctx = java_context()->getServletContext();
        $this->_birtEngine = java("org.eclipse.birt.php.birtengine.BirtEngine")->getBirtEngine($ctx);
        java_context()->onShutdown(java("org.eclipse.birt.php.birtengine.BirtEngine")->getShutdownHook());
        $this->_birtEngine->getConfig()->setResourcePath(APPLICATION_PATH."/../birt/Reports");
    
        return $this;
    }

    /**
     * Cierra el Engine del Birt
     */
    public function close()
    {
        java("org.eclipse.birt.core.framework.Platform")->shutdown();
        $this->_birtEngine->destroy();
    }

    /**
     * Convierte un array de byte a binario (Usado para los archivos PDF)
     * @param array $byteArray
     * @return string
     */
    private function _byteArrayToStream($byteArray)
    {
        if (!is_array($byteArray)) {
            return null;
        }
        $buf = '';
        for ($i = 0, $size = count($byteArray); $i < $size; ++$i) {
            $buf .= chr($byteArray[$i]);
        }
        return $buf;
    }

    /**
     * Convierte un string binario a un array de byte
     * @param string $string
     * @return array
     */
    private function _streamToBytesArray($string)
    {
        //$out = new Java("java.io.ByteArrayOutputStream");
        $arr = array();
        $strlen = strlen($string);
        for ($i = 0; $i < $strlen; $i ++) {
          $val = ord(substr($string, $i, 1));
          if ($val >= 128) {
              $val = ($val) - 256;
          }
          $arr[] = $val;
        }
        return $arr;
    }

    /**
     * Carga un reporte de un archivo y lo renderiza
     * 
     * @param string $file   Nombre del archivo
     * @param string $format Formato
     * @param array  $tags   Tags a reemplazar en el reporte
     */
    public function renderFromFile($file, $format, $tags = null)
    {
        // publicamos el evento para que se puedan atachar autenticadores, validadores y modificadores de tags
        $fileName = str_replace('.rptdesign', '', basename($file));
        
        Rad_PubSub::publish('BirtEngine/Render/'.$fileName, $fileName, $this->_params, $format, $tags);

        try {
        
            $this->_renderedFormat = $format;
            $this->_compileIfNeeded($file);
            if (!$tags) {
                $report = $this->_birtEngine->openReportDesign($file);
                $this->renderReport($report, $format);
            } else {
                $this->_renderFromFileWithTags($file, $format, $tags);
            }
        } catch (Exception $e) {
            header("Content-type: text/html");
            die($e);
        }
        return $this;
    }

    /**
     * Renderiza de un archivo aplicando tags
     * 
     * @param string $file   Nombre del archivo
     * @param string $format Formato
     * @param array  $tags   Tags a reemplazar en el reporte
     */
    protected function _renderFromFileWithTags($file, $format, $tags)
    {
        $xml = file_get_contents($file);

        if ($xml === false) {
            throw new Rad_BirtEngine_Exception("Error al leer el reporte $file");
        }

        $compiled = $this->_replaceTags($tags, $xml, 1);
        $this->renderFromString($compiled, $format);
    }

    /**
     * Compila un reporte de ser necesario
     * 
     * @param string $file Nombre del archivo
     */
    protected function _compileIfNeeded($file, $overwrite = false)
    {
        $info = pathinfo($file);
        // extension correcta?
        if ($info['extension'] != 'rptdesign' && $info['extension'] != 'rptlibrary' ) {
            throw new Rad_BirtEngine_Exception('El reporte debe tener la extension .rptdesign o una libreria .rptlibrary');
        }
        // Ya existe salgo (excepto en modo desarrollo donde compilo siempre)
        if (!$overwrite && APPLICATION_ENV != 'development' && file_exists($file)) {
            return;
        }

        // nombre del archivo compilado
        $fileSource = $file.".source";
        if (!file_exists($fileSource)) {
            throw new Rad_BirtEngine_Exception("No existe el reporte $file");
        }
        // compilo
        $this->_compileFile($fileSource, $file);
    }

    /**
     * Compila un archivo si no esta compilado previamente
     * 
     * @param string $file Nombre del archivo
     */
    public function compile($file, $overwrite = false) 
    {
        $this->_compileIfNeeded($file, $overwrite);
    }

    /**
     * Compila un archivo $src en $dst
     * 
     * @param string $src archivo de origen
     * @param string $dst archivo destino
     */
    protected function _compileFile($src, $dst)
    {
        $content  = file_get_contents($src);
        $tags     = Rad_Cfg::get(self::$TagsFile);
        $compiled = $this->_replaceTags($tags->toArray(), $content);
        if (file_put_contents($dst, $compiled) === false) {
            throw new Rad_BirtEngine_Exception("Error al guardar el reporte compilado $dst");
        }
    }

    /**
     * Reemplaza los tags en el contenido $content
     * 
     * @param array  $tagsValues Tags a reemplazar en el contenido
     * @param string $content    Contenido del reporte (XML)
     */
    protected function _replaceTags($tagsValues, $content, $tagDelimiter = 2)
    {
        $tags   = array_keys($tagsValues);
        $values = array_values($tagsValues);

        $tdl = str_pad('', $tagDelimiter, '{');
        $tdr = str_pad('', $tagDelimiter, '}');

        foreach ($tags as $k => $tag) {
            $tags[$k] = $tdl.$tag.$tdr;
        }

        return str_replace($tags, $values, $content);
    }

    /**
     * Renderiza un reporte en un formato dado
     * 
     * @param ReportDesign $report instancia JAVA de un report design
     * @param string       $format formato de salida
     */
    protected function renderReport($report, $format)
    {
        $lc = new java("java.util.Locale", "es","AR");


        $task = $this->_birtEngine->createRunAndRenderTask($report);
        $task->setLocale($lc);
        // $fmt = new java("org.eclipse.birt.report.engine.api.PDFRenderOption");
        $fmt  = $this->getOutputFormat($format);

        $this->_outputStream = new java("java.io.ByteArrayOutputStream");

        $fmt->setOutputStream($this->_outputStream);
        $fmt->setOutputFormat($format);
        $task->setRenderOption($fmt);

        foreach ($this->_params as $param => $value) {
            $task->setParameterValue($param, $value);
        }

        $task->run();

        $task->close();
    }

    /**
     * Renderiza un reporte en un formato dado
     * 
     * @param string $xml    string coneniendo un report design en xml
     * @param string $format formato de salida
     */
    public function renderFromString($xml, $format)
    {
        try {
            $this->_renderedFormat = $format;

            $is = new java('java.io.ByteArrayInputStream', $xml);

            $report = $this->_birtEngine->openReportDesign($is);

            //java("java.lang.System")->setProperty('user.dir',BIRT_REPORTS_PATH.'Reports');
            
            $this->renderReport($report, $format);
        } catch (Exception $e) {
            header("Content-type: text/html");
            die($e);
        }
        return $this;
    }

    /**
     * Retorna los parametros
     */
    public function getParams()
    {
        return $this->_params;
    }

    /**
     * Obtiene el reporte renderizado
     */
    public function getStream()
    {
        return java_values($this->_outputStream->toByteArray());
    }

    /**
     * Returns the number of pages in the report
     *
     * @return int
     */
    function getNumberOfPages()
    {
        return $this->_birtEngine->getReportHTMLPageCount();
    }

    /**
     * Envia el Reporte renderizado al cliente
     * 
     * @param string $fileName Nombre del archivo
     */
    public function sendStream($fileName = 'report')
    {
        switch ($this->_renderedFormat) {
            case "pdf":
                //header('Access-Control-Allow-Origin: *');
                header("Accept-Ranges: bytes");
                header("Content-type: application/pdf");
                header("Content-Disposition: inline; filename=\"{$fileName}.pdf\"");
                //header('Expires: ' . gmdate('D, d M Y H:i:s', gmmktime() - 3600) . ' GMT');
                break;
            case "html":
                header("Content-type: text/html; charset=utf-8");
                break;
            case "msword":
                header("Content-type: application/msword");
                header("Content-Disposition: inline; filename=\"{$fileName}.doc\"");
                break;
            case "xls":
                header("Content-type: application/vnd.ms-excel");
                header("Content-Disposition: inline; filename=\"{$fileName}.xls\"");
                break;
            default:
                throw new Rad_BirtEngine_Exception("Rad_BirtEngine: formato '$format' desconocido");
        }
        echo $this->getStream();
    }
}