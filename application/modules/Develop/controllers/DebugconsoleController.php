<?php

require_once 'Rad/Window/Controller/Action.php';
/**
 * @author Martin Santangelo
 */
class Develop_DebugconsoleController extends Rad_Window_Controller_Action
{

    protected $title = "Herramientas Desarrollo";

	protected function getSession() {
		$session = new Zend_Session_Namespace('Rad_PHPConsole');
		return $session;
	}

	public function initWindow()
    {
		$this->view->code = $this->getSession()->code;
	}

    /**
     * Check the syntax of some PHP code.
     * @param string $code PHP code to check.
     * @return boolean|array If false, then check was successful, otherwise an array(message,line) of errors is returned.
     */
    public function php_syntax_error($code){
        $braces=0;
        $inString=0;
        foreach (token_get_all('<?php ' . $code) as $token) {
            if (is_array($token)) {
                switch ($token[0]) {
                    case T_CURLY_OPEN:
                    case T_DOLLAR_OPEN_CURLY_BRACES:
                    case T_START_HEREDOC: ++$inString; break;
                    case T_END_HEREDOC:   --$inString; break;
                }
            } else if ($inString & 1) {
                switch ($token) {
                    case '`': case '\'':
                    case '"': --$inString; break;
                }
            } else {
                switch ($token) {
                    case '`': case '\'':
                    case '"': ++$inString; break;
                    case '{': ++$braces; break;
                    case '}':
                        if ($inString) {
                            --$inString;
                        } else {
                            --$braces;
                            if ($braces < 0) break 2;
                        }
                        break;
                }
            }
        }
        $inString = @ini_set('log_errors', false);
        $token = @ini_set('display_errors', true);
        ob_start();
        $braces || $code = "if(0){{$code}\n}";
        if (eval($code) === false) {
            if ($braces) {
                $braces = PHP_INT_MAX;
            } else {
                false !== strpos($code,CR) && $code = strtr(str_replace(CRLF,LF,$code),CR,LF);
                $braces = substr_count($code,LF);
            }
            $code = ob_get_clean();
            $code = strip_tags($code);

            if (preg_match("'syntax error, (.+) in .+ code on line (\d+)$'s", $code, $code)) {

                $code[2] = (int) $code[2];
                $code = $code[2] <= $braces
                    ? array($code[1], $code[2])
                    : array('unexpected $end' . substr($code[1], 14),  $code[2]);

            } else
                    $code = array('syntax error', 0);

        } else {
            ob_end_clean();
            $code = false;
        }
        @ini_set('display_errors', $token);
        @ini_set('log_errors', $inString);
        ob_end_clean();
        return $code;
    }

    public static function error_alert($e)
    {

        ob_clean();
        if (!headers_sent()) header('HTTP/1.1 200');

        $msg1 = $e['message']."\n  Tipo: ".$e['type']."\n  Archivo: ".$e['file']."\n  Linea: ".$e['line'];
        $a = array(
            'success' => true,
            'html'    => $msg1
        );
        echo json_encode($a);

    }



    public function executeAction()
    {

        $this->_helper->viewRenderer->setNoRender(true);
        $rq = $this->getRequest();

		// Solo en este servidor y desde la red interna
        //if ($_SERVER['SERVER_ADDR']!= '10.108.100.20' || substr($_SERVER['REMOTE_ADDR'],0,10) != '10.108.100') die("{success:false, msg:'No autorizado'}");

        $codigo = str_replace("<?php\n", '', $rq->codigo);

        //$codeStatus = $this->php_syntax_error($rq->codigo);

		$session = $this->getSession();

		$session->code = str_replace("\n", '\n', addslashes($codigo));

        if (!$codeStatus) {

            // desactivo las alertas de errores de php del manejador generico
            Rad_ErrorHandler::$ERROR_HANDLER = array(__CLASS__, 'error_alert');

            try {
                ob_start ();
                $status = @eval($codigo);
                $html = ob_get_clean();
            } catch (Exception $e) {
                $html = ob_get_clean();
                // ob_clean();
                $html .= get_class($e). " Exception!!!\n";
                $html .= $e->getMessage()."\n";
                $html .= "Linea: ".$e->getLine(). " En ".$e->getFile();
            }


        } else {

           $html = 'Error: '.$codeStatus[0]."\nEn linea: ".$codeStatus[1];

        }

        Zend_Wildfire_Channel_HttpHeaders::getInstance()->flush();

        $rtn = new StdClass();
        $rtn->html = $html;
        $rtn->success = true;

        echo Zend_Json::encode($rtn);;
    }
}