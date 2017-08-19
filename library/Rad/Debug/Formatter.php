<?php
/**
 * Rad_Debug_Formatter
 * 
 * Formatea los Errores en HTML para Debug
 *
 * @copyright SmartSoftware Argentina
 * @package Rad
 * @subpackage App
 * @author Martin Alejandro Santangelo
 */
class Rad_Debug_Formatter {

    static function displayError(array $error)
    {
        $e['message']."<br>Tipo: ".$e['type']."<br>Archivo: ".$e['file']."<br>Linea: ".$e['line'];
        
        $data = array();
        $data['errorMessage']    = $error['message'];
        $data['errorType']       = $error['type'];
        $data['errorCode']       = 0;
        $data['errorFilePath']   = $error['file'];
        $data['errorLineNumber'] = $error['line'];
        $data['errorLineNumberFormatted'] = self::addPadding( $error['line'] );
        $data['errorCodeBlock']  = self::generateCodeBlock( $error['line'], $error['file'] );
        
        $data['traceStack'] = array();
        return self::render($data, $extended);
    }

    static function displayTrace($bt, $title, $extended = false)
    {

        $data = array();
        $data['errorMessage']    = $title;
        $data['errorType']       = 0;
        $data['errorCode']       = 0;
        $data['errorFilePath']   = $bt[0]['file'];
        $data['errorLineNumber'] = $bt[0]['line'];
        $data['errorLineNumberFormatted'] = self::addPadding( $bt[0]['line'] );
        $data['errorCodeBlock']  = self::generateCodeBlock( $bt[0]['line'], $bt[0]['file'] );
        
        $data['traceStack'] = array();
        unset($bt[0]);
        foreach( $bt as $trace) {
            if ($extended) {
                $trace['lineNumberFormatted'] = self::addPadding( $trace['line'] );
                $trace['codeBlock'] = self::generateCodeBlock( $trace['line'], $trace['file'] );
            }
            $data['traceStack'][] = $trace;
        }


        return self::render($data, $extended);
    }

    /**
     * Suppresses the standard viewRenderer and directly outputs the formatted
     * exception (HTML, CSS and JavaScript.
     *
     * @param Exception $error La excepcion a mostrar
     * @param Zend_Controller_Action_HelperBroker $viewHelper The main view helper for the current controller. Generally accessed via $this->_helper from within the controller.
     * @param boolean $extended A flag used to dertmine if the stack trace should be extended to contain interactive source code or not. Browser performance is degraded when this feature is enabled. Default is false. 
     */
    static function displayException (Exception $error, Zend_Controller_Action_HelperBroker $viewHelper = null, $extended = false) {
        $e = new ArrayObject();
        $e['type'] = 0;
        $e['exception'] = $error; 
        return self::display ($e, $viewHelper, $extended );
    }


    /**
     * Suppresses the standard viewRenderer and directly outputs the formatted
     * exception (HTML, CSS and JavaScript.
     *
     * @param ArrayObject $error The error handler array object for the currently controller. Generally accessed via $this->_getParam('error_handler') from within the controller.
     * @param Zend_Controller_Action_HelperBroker $viewHelper The main view helper for the current controller. Generally accessed via $this->_helper from within the controller.
     * @param boolean $extended A flag used to dertmine if the stack trace should be extended to contain interactive source code or not. Browser performance is degraded when this feature is enabled. Default is false. 
     */
    static function display (ArrayObject $error, Zend_Controller_Action_HelperBroker $viewHelper = null, $extended = false) {
        if ($viewHelper) {
            $viewHelper->viewRenderer->setNoRender();
        }
                
        $data = array();
        $data['errorMessage']    = $error['exception']->getMessage();
        $data['errorType']       = $error['type'];
        $data['errorCode']       = $error['exception']->getCode();
        $data['errorFilePath']   = $error['exception']->getFile();
        $data['errorLineNumber'] = $error['exception']->getLine();
        $data['errorLineNumberFormatted'] = self::addPadding( $error['exception']->getLine() );
        $data['errorCodeBlock']  = self::generateCodeBlock( $error['exception']->getLine(), $error['exception']->getFile() );
        if ($error['exception']->sql) $data['sql'] = $error['exception']->sql;

        $data['traceStack'] = array();
        foreach( $error['exception']->getTrace() as $trace) {
            if ($extended) {
                $trace['lineNumberFormatted'] = self::addPadding( $trace['line'] );
                $trace['codeBlock'] = self::generateCodeBlock( $trace['line'], $trace['file'] );
            }
            $data['traceStack'][] = $trace;
        }

        return self::render($data, $extended);
    }

    /**
     * Handles the formatting of function arguments values for a given function
     * within a stack trace.
     * @access private
     * @param array $args An array of function arguments
     * @return string Formatted arguments ready for display in the output
     * template.
     */
    protected static function formatArgValues ($args) {
        $count=0;

        $isAssoc=$args!==array_values($args);

        foreach($args as $key => $value)
        {
            $count++;
            if($count>=5)
            {
                if($count>5)
                    unset($args[$key]);
                else
                    $args[$key]='...';
                continue;
            }

            if(is_object($value))
                $args[$key] = get_class($value);
            elseif(is_bool($value))
                $args[$key] = $value ? 'true' : 'false';
            elseif(is_string($value))
            {
                if(strlen($value)>64)
                    $args[$key] = '"'.substr($value,0,64).'..."';
                else
                    $args[$key] = '"'.$value.'"';
            }
            elseif(is_array($value))
                $args[$key] = 'array('.self::formatArgValues($value).')';
            elseif($value===null)
                $args[$key] = 'null';
            elseif(is_resource($value))
                $args[$key] = 'resource';

            if(is_string($key))
            {
                $args[$key] = '"'.$key.'" => '.$args[$key];
            }
            elseif($isAssoc)
            {
                $args[$key] = $key.' => '.$args[$key];
            }
        }
        $out = implode(", ", $args);

        return $out;
    }

    /**
     * Padds line numbers within the the source code using &amp;nbsp; to ensure
     * 5 positions.
     *
     * @access private
     * @todo This is a hacky way to go about this. Perhaps Ill revist it at
     * some point.
     * @param integer $number
     * @return string Number with appropriate padding applied
     */
    private static function addPadding ($number) {
        $formattedNumber = str_pad($number, 5, '_', STR_PAD_RIGHT);
        return str_replace('_', '&nbsp;', $formattedNumber );
    }

    /**
     * Loads the given file and highlights it via highlight_file method. It
     * then inserts formatted line numbers within the resulting and highlights
     * the error line.
     *
     * @access private
     * @param integer $errorLine The line on which the error occured
     * @param string $filePath The full file path to the source code in which 
     * the error occured
     * @return string The result of of the highlight_file function (source code
     * formatted with HTML) with line numbers spliced in and wrapped in a 
     * containing div for formatting and to provide a javascript DOM hook.
     */
    private static function generateCodeBlock ($errorLine, $filePath) {
        $lines = explode( '<br />', highlight_file($filePath, true) );
        $errorID = '';
        for($n = 0; $n < count($lines); $n++) {
            $lineNumber = $n+1;
            $paddedNumber =  self::addPadding( $lineNumber );
            $errorClass = '';
            list($errorClass, $errorID) = ($lineNumber == $errorLine) ? array('errorLine', md5( $errorLine.$filePath ) ) : array('',$errorID);
            $lines[ $n ] = "<span class=\"lineNumbers $errorClass\" id=\"$errorID\">$paddedNumber</span>".$lines[ $n ];
        }
        return "<div class=\"codeFile\" errorid=\"$errorID\">".implode("<br />\n", $lines).'</div>';
    }
    
    /**
     * Outputs formatted HTML directly to the browser. This method defines the 
     * HTML (CSS & JavaScript) template used for output using heredoc.
     *
     * @access private
     * @param array $data A hash of formatted strings ready for display 
     * @param boolean $extended A flag used to dertmine if the stack trace should be extended to contain interactive source code or not.
     */
    private static function render($data, $extended, $title = 'Fatal Error') {
        $compiledTrace = '';
        foreach($data['traceStack'] as $trace) {
            $compiledTrace .= '<li class="codeBlock">'."\n";
            if ($extended) {
                $compiledTrace .= '<div class="filePath"><a class="openLink" href="javascript://">open</a>'.$trace['file'].'</div>'."\n";
            } else {
                $compiledTrace .= '<div class="filePath"><span class="openLink">'.$trace['line'].'</span>'.$trace['file'].'</div>'."\n";
            }
            $compiledTrace .= '<div class="functionCall">'.$trace['class'].'->'.$trace['function'].'('.self::formatArgValues($trace['args']).')</div>'."\n";
            if ($extended) {
                $compiledTrace .= $trace['codeBlock']."\n";
            }
            $compiledTrace .= '</li>'."\n";
        }

        if ($data['sql']) $data['sql']="<div class='head'>SQL</div><div class='codeBlock'>{$data['sql']}</div>";

        $rtn= <<<EOT
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>{$title}: {$data["errorMessage"]}</title>
  <script src="http://www.prototypejs.org/assets/2007/11/6/prototype.js"></script>
  <script>
    function updateCodeBlocks() {
        $$('#error .codeBlock A.openLink').each(function(link){
            link.observe('click',toggleCodeFile);
        });

        $$('#error .codeBlock').each(function(element){
            element.select('DIV.codeFile').each(function(codeFile){
                $( codeFile.readAttribute('errorid') ).scrollIntoView();
            });
            $('error').scrollIntoView(true);
        });
    }

    function toggleCodeFile(event) {
        event.target.blur();        
        $(event.target.parentNode).adjacent('DIV.codeFile')[0].toggleClassName('open');
        $( $(event.target.parentNode).adjacent('DIV.codeFile')[0].readAttribute('errorid') ).scrollIntoView();
        event.target.innerHTML = (event.target.innerHTML == 'open') ? 'close' : 'open';
    }

    document.observe("dom:loaded", updateCodeBlocks);
  </script>
  <style>
    #error
        {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:10pt; color:#333;margin:0;padding:0;}
    #error .title
        {background-color:#10759C;border-bottom:1px solid #0e698b;overflow:hidden;height:38pt;text-align:right;}
    #error .title .prefix
        {display:block;font-size:60pt;font-weight:bold;margin:-20pt -8pt 0 0;padding:0;color:#0e698b;}
    #error .title .message
        {font-size:13pt;color:#FFF;position:absolute;top:5pt;left:10pt;}
    #error .type
        {color:#C8EFFA; font-size:9px; background-color:#334c60; padding: 1px 5px 3px 10px; border-top:2px solid #2e4456; border-bottom:1px solid #617789; margin-bottom:10px;}
    #error .head
        {border-bottom:1px dotted #ADBAC6;color:#0088B5;font-size:16pt;margin:15px 15px 15px 10px;}

    #error .codeBlock
        {font-family:"Courier New",Courier,monospace;font-size:8pt;font-weight:bold;color:#7B8D9A;margin:15px 15px 15px 10px;}
    #error .filePath
        {color:#334c60;background-color:#D3E0EB;padding:2pt 0 2pt 0;}
    #error .functionCall
        {color:#FFF;background-color:#10759C;padding:2pt 0 2pt 35pt;font-size:8pt;font-weight:normal;border:1px solid #D3E0EB;border-top:none;}
    #error .codeFile
        {height:11pt;overflow:hidden;background-color:#EDF7FF;border:1px solid #D3E0EB;border-top:none;}
    #error .codeBlock .open
        {height:125pt;overflow:auto;}
    #error .codeFile .lineNumbers
        {color:#7B8D9A;background-color:#D3E0EB;border:1px solid #EDF7FF;padding-left:3pt;margin-right:3pt;}
    #error .codeFile .errorLine
        {color:#FFF;background-color:#A00;}
    #error .codeBlock .openLink
        {float:left;padding:1pt 0 2pt 2pt;margin:-2pt 5pt 0 0;width:28pt;background-color:#10759C;color:#FFF;font-size:8pt;
            border-left:1px solid #D3E0EB;border-top:1px solid #D3E0EB;}
    #error .codeBlock A.openLink:hover
        {color:#10759C;background-color:#EDF7FF;}

    #error .trace
        {margin:15px 15px 15px 10px;}
    #error .trace LI
        {margin-right:0;}
  </style>

</head>
<body id="error">

<div class="title"><span class="prefix">{$title}</span> <span class="message">{$data['errorMessage']}</span></div>
<div class="type">{$data['errorType']} : <span class="code">{$data['errorCode']}</span></div>

<div class="head">{main}</div>



<div class="codeBlock">
    <div class="filePath"><a class="openLink" href="javascript://">open</a>{$data['errorFilePath']}</div>
    {$data['errorCodeBlock']}
</div>

{$data['sql']}

<div class="head">Stack trace</div>
<div class="trace">
    <ol>
    $compiledTrace
    </ol>
</div>

</body>
</html>
EOT;
    return $rtn;
    }
}
