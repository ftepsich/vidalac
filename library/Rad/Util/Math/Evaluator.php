<?php

namespace Rad\Util\Math;


use Rad\Util\Math\Expresion\Parser;
use Rad\Util\Math\Expresion\Func;
use Rad\Util\Math\Expresion\Operand;
use Rad\Util\Math\Expresion\Token;

class Evaluator_Exception extends \Rad_Exception {

}

/**
 * Evaluador de Expresiones
 *
 * Soporta valores boleanos, numericos, variables y funciones
 *
 * Basado en https://github.com/SymDevStudio/MathExecutor/blob/master/NXP
 *
 * @package     Rad
 * @subpackage  Util
 * @copyright   SmartSoftware Argentina
 * @author Martin Alejandro Santangelo
 */
class Evaluator
{

    private $operators = array();

    private $functions = array();

    private $variables = array();

    /**
     * @var \SplStack
     */
    private $stack;

    /**
     * @var \SplQueue
     */
    private $queue;

    /**
     * Callback manejador de variables sin valor
     * @var
     */
    protected $_variableHandler;


    /**
     * Base math operators
     */
    public function __construct()
    {
        $this->addOperator(new Operand('!', 1, Operand::RIGHT_ASSOCIATED, Operand::UNARY,  function ($op1) { return !$op1; }));
        $this->addOperator(new Operand('&', 10, Operand::LEFT_ASSOCIATED, Operand::BINARY, function ($op1, $op2) { return $op1&&$op2; }));
        $this->addOperator(new Operand('|', 10, Operand::LEFT_ASSOCIATED, Operand::BINARY, function ($op1, $op2) { return $op1||$op2; }));
        $this->addOperator(new Operand('<', 20, Operand::LEFT_ASSOCIATED, Operand::BINARY, function ($op1, $op2) { return $op1<$op2;  }));
        $this->addOperator(new Operand('>', 20, Operand::LEFT_ASSOCIATED, Operand::BINARY, function ($op1, $op2) { return $op1>$op2;  }));
        $this->addOperator(new Operand('=', 20, Operand::LEFT_ASSOCIATED, Operand::BINARY, function ($op1, $op2) { return $op1==$op2;  }));
        $this->addOperator(new Operand('~', 20, Operand::LEFT_ASSOCIATED, Operand::BINARY, function ($op1, $op2) { return $op1!=$op2; }));

        $this->addOperator(new Operand('+', 30, Operand::LEFT_ASSOCIATED, Operand::BINARY, function ($op1, $op2) { return $op1+$op2; }));
        $this->addOperator(new Operand('-', 30, Operand::LEFT_ASSOCIATED, Operand::BINARY, function ($op1, $op2) { return $op1-$op2; }));
        $this->addOperator(new Operand('*', 40, Operand::LEFT_ASSOCIATED, Operand::BINARY, function ($op1, $op2) { return $op1*$op2; }));
        $this->addOperator(new Operand('/', 40, Operand::LEFT_ASSOCIATED, Operand::BINARY, function ($op1, $op2) { return $op1/$op2; }));
        $this->addOperator(new Operand('^', 50, Operand::LEFT_ASSOCIATED, Operand::BINARY, function ($op1, $op2) { return pow($op1,$op2); }));

        /* comentado por que no lo usamos en el liquidor para ver si mejora la performance !
        $this->addFunction(new Func('sin',  function ($arg) { return sin($arg); }));
        $this->addFunction(new Func('cos',  function ($arg) { return cos($arg); }));
        $this->addFunction(new Func('tn',   function ($arg) { return tan($arg); }));
        $this->addFunction(new Func('asin', function ($arg) { return asin($arg); }));
        $this->addFunction(new Func('acos', function ($arg) { return acos($arg); }));
        $this->addFunction(new Func('atn',  function ($arg) { return atan($arg); }));
        */

        /* PK 2015-07-28 valor absoluto */
        /*
        $this->addFunction(
            new Func('abs',  
                function ($arg) { 
                    // $arg = func_get_args();
                    // return str_replace('-','',$arg); 
                    return abs($arg); 
                }
            )
        );
        */

        $this->addFunction(
            new Func('si',
                function ($c, $t, $e){
                    if ($c) return $t; else return $e;
                }
            )
        );

        $this->addFunction(
            new Func('caso',
                function(){
                    /*
                        ejemplo de caso: caso(vairableAcomparar,siValor1,entonces1,siValor2,entonces2,siValor3,entonces3,...)
                        los va tomando de a pares valor, respuesta
                    */
                    $arg = func_get_args();
                    $c = array_shift($arg);
                    for($i=0;$i<count($arg);$i+=2){
                        if ($c == $arg[$i]) return $arg[$i+1];
                    }
                    // por si sale si enganchar ninguno de los casos anteriores, puede pasar !!!
                    return 0;
                }
            )
        );

        $this->addFunction(
            new Func('encaso',
                function(){
                    /*
                        ejemplo de encaso: caso(sicond1,entonces1,sicond2,entonces2,sicond3,entonces3,...)
                        los va tomando de a pares valor, respuesta
                    */
                    $arg = func_get_args();
                    for($i=0;$i<count($arg);$i+=2){
                        if ($arg[$i]) return $arg[$i+1];
                    }
                    // por si sale si enganchar ninguno de los casos anteriores
                    return 0;
                }
            )
        );

        $this->addFunction(
            new Func('max',
                function () {
                    $args = func_get_args();
                    $res  = array_pop($args);
                    foreach($args as $a) {
                        if ($res < $a) {
                            $res = $a;
                        }
                    }
                    return $res;
                }
            )
        );

        $this->addFunction(
            new Func('min',
                function () {
                    $args = func_get_args();
                    $res = array_pop($args);
                    foreach($args as $a) {
                        if ($res > $a) {
                            $res = $a;
                        }
                    }
                    return $res;
                }
            )
        );

        $this->addFunction(
            new Func('sum',
                function () {
                    $args = func_get_args();
                    return array_sum($args);
                }
            )
        );

        $this->addFunction(
            new Func(
                'prom',
                function () {
                    $args = func_get_args();
                    return array_sum($args)/count($args);
                }
            )
        );


        $this->addFunction(
            new Func(
                'redondeo',
                function ($val, $precision = 0) {
                    return round($val, $precision);
                }
            )
        );

    }


    public function setVariableHandler($f)
    {
        $this->_variableHandler = $f;
    }

     /**
     * Add operator to executor
     * @param Operand $operator
     */
    public function addOperator(Operand $operator)
    {
        $this->operators[$operator->getSymbol()] = $operator;
    }

    /**
     * Add function to executor
     * @param Func $function
     */
    public function addFunction(Func $function)
    {
        $this->functions[$function->getName()] = $function->getCallback();
    }

    /**
     * Add variable to executor
     * @param $variable
     * @param $value
     * @throws Evaluator_Exception
     */
    public function setVar($variable, $value)
    {
        if (!is_numeric($value) && !is_bool($value)) {
            throw new Evaluator_Exception("Variable value must be a number");
        }
        $this->variables[$variable] = $value;
    }

    /**
     * retorna un array con todas las variables
     */
    public function getVars()
    {
        return $this->variables;
    }

    /**
     * setea un array con todas las variables
     */
    public function setVars($vars)
    {
    	return $this->variables = $vars;
    }

    /**
     * Retorna una variable
     */
    public function getVar($variable)
    {
        return @$this->variables[$variable];
    }

    /**
     * Execute expression
     * @param $expression
     * @param $var        Nombre de la variable a donde quiero que guarde el resultado
     * @return int|float
     */
    public function execute($expression, $var=null)
    {
        $reversePolishNotation = $this->convertToReversePolishNotation($expression);


        $result = $this->calculateReversePolishNotation($reversePolishNotation);


        if ($var) $this->setVar($var, $result);

        return $result;
    }

    protected function _handleVariable($var)
    {
        if (is_callable($this->_variableHandler)) {
            return call_user_func($this->_variableHandler, $var, $this);
        }
    }

    /**
     * Convert expression from normal expression form to RPN
     * @param $expression
     * @return \SplQueue
     * @throws Evaluator_Exception
     */
    private function convertToReversePolishNotation($expression)
    {
        $this->stack = new \SplStack();
        $this->queue = new \SplQueue();

        $tokenParser = new Parser();

        $input = $tokenParser->tokenize($expression);

        $this->categorizeTokens($input);

        while (!$this->stack->isEmpty()) {
            $token = $this->stack->pop();

            // if ($token->getType() != Parser::TOKEN_OPERATOR) {
            //     throw new Evaluator_Exception('Apertura de parentesis sin cerrar');
            // }
            $this->queue->push($token);
        }
        return $this->queue;
    }

    /**
     * @param SplQueue $tokenList
     * @throws Evaluator_Exception
     */
    private function categorizeTokens($tokenList)
    {
        $count = $tokenList->count();

        $inFunction = 0;

        foreach ($tokenList as $pos => $token) {
            switch ($token->getType()) {
                case Parser::TOKEN_FUNC:
                    if (!array_key_exists($token->getValue(), $this->functions)) {
                        throw new Evaluator_Exception("Funcion desconocida: '{$token->getValue()}'");
                    }
                    $inFunction++;
                    $this->stack->push($token);
                    break;
                case Parser::TOKEN_NUMBER :
                    if ($inFunction) {
                        $token->setType(Parser::TOKEN_PARAM_NUMBER);
                    }
                    $this->queue->push($token);

                    break;

                case Parser::TOKEN_STRING:
                    // primero me fijo si el token siguiente no es un parentesis por que sino es una funcion

                    // es una variable ?
                    $type = ($inFunction)?Parser::TOKEN_PARAM_NUMBER:Parser::TOKEN_NUMBER;
                    if (array_key_exists($token->getValue(), $this->variables)) {
                        $this->queue->push(new Token($type, $this->variables[$token->getValue()]));
                    } else {
                        $var = null;

                        $var = $this->_handleVariable($token->getValue());

                        if ($var !== null) {
                            $this->variables[$token->getValue()] = $var;
                            $this->queue->push(new Token($type, $var));
                        } else {
                            throw new Evaluator_Exception('Variable '.$token->getValue().' desconocida');
                        }
                    }
                    break;

                case Parser::TOKEN_LEFT_BRACKET:
                    if($inFunction) $inFunction++;

                    $bracketcount++;

                    $this->stack->push($token);
                    break;

                case Parser::TOKEN_COMMA:
                    while (!$this->stack->isEmpty() && ($this->stack->top()->getType() != Parser::TOKEN_LEFT_BRACKET) && ($previousToken = $this->stack->pop()) ) {
                            $this->queue->push($previousToken);
                            //if ($this->stack->isEmpty()) throw new Evaluator_Exception('Expresión incorrecta');
                    }
                    break;

                case Parser::TOKEN_RIGHT_BRACKET:

                    if ($inFunction) $inFunction--;

                    $bracketcount--;

                    while (!$this->stack->isEmpty() && ($previousToken = $this->stack->pop()) && ($previousToken->getType() != Parser::TOKEN_LEFT_BRACKET)) {
                    // while (!$this->stack->isEmpty() && ($this->stack->top()->getType() != Parser::TOKEN_LEFT_BRACKET) && ($previousToken = $this->stack->pop()) ) {
                            $this->queue->push($previousToken);
                            //if ($this->stack->isEmpty()) throw new Evaluator_Exception('Expresión incorrecta');
                    }
                    if (!$this->stack->isEmpty() && ($this->stack->top()->getType() == Parser::TOKEN_FUNC)) {
                        $this->queue->push( $this->stack->pop());
                    }


                    break;

                case Parser::TOKEN_OPERATOR:
                    $this->proceedOperator($token);
                    $this->stack->push($token);
                    break;

                default:
                    throw new Evaluator_Exception('Error analizando la expresión: Expresión desconocida');
            }
        }

        if ($bracketcount > 0) throw new Evaluator_Exception('No cerro un parentesis');
        if ($bracketcount < 0) throw new Evaluator_Exception('Sobra un cierre de parentesis');
    }

    /**
     * @param $token
     * @throws Evaluator_Exception
     */
    private function proceedOperator($token)
    {
        if (!array_key_exists($token->getValue(), $this->operators)) {
            throw new Evaluator_Exception('Operatodor '.$token->getValue().' desconocido');
        }
        /** @var Operand $operator */
        $operator = $this->operators[$token->getValue()];
        while (!$this->stack->isEmpty()) {
            $top = $this->stack->top();
            if ($top->getType() == Parser::TOKEN_OPERATOR) {
                $priority = $this->operators[$top->getValue()]->getPriority();
                if ( $operator->getAssociation() == Operand::RIGHT_ASSOCIATED) {
                    if (($priority > $operator->getPriority())) {
                        $this->queue->push($this->stack->pop());
                    } else {
                        return;
                    }
                } else {
                    if (($priority >= $operator->getPriority())) {
                        $this->queue->push($this->stack->pop());
                    } else {
                        return;
                    }
                }
            } elseif ($top->getType() == Parser::TOKEN_STRING) {
                $this->queue->push($this->stack->pop());
            } else {
                return;
            }
        }
    }

     /**
     * @param \SplQueue $expression
     * @return mixed
     * @throws Evaluator_Exception
     */
    private function calculateReversePolishNotation(\SplQueue $expression)
    {
        $this->stack = new \SplStack();

        /** @val Token $token */
        foreach ($expression as $token) {

            switch ($token->getType()) {
                case Parser::TOKEN_NUMBER :
                case Parser::TOKEN_PARAM_NUMBER :
                    $this->stack->push($token);
                    break;
                case Parser::TOKEN_OPERATOR:
                    /** @var Operand $operator */
                    $operator = $this->operators[$token->getValue()];

                    $pop = $this->stack->pop();

                    if ($operator->getType() == Operand::BINARY) {
                        $arg2 = $pop->getValue();
                        $arg1 = $this->stack->pop()->getValue();
                    } else {
                        $arg2 = null;
                        $arg1 = $pop->getValue();
                    }
                    $callback = $operator->getCallback();

                    $this->stack->push(new Token($pop->getType(), ($callback($arg1, $arg2))));

                    break;
                case Parser::TOKEN_FUNC:
                    /** @var Func $function */

                    $callback = $this->functions[$token->getValue()];
                    $arg = array();

                    // $arg[] = $this->stack->pop()->getValue();


                    while ($this->stack->count() && $this->stack->top()->getType() == Parser::TOKEN_PARAM_NUMBER) {
                        $arg[] = $this->stack->pop()->getValue();
                    }
                    $arg = array_reverse($arg);
                    $this->stack->push(new Token(Parser::TOKEN_NUMBER, (call_user_func_array($callback, $arg))));
                    break;
                default:
                    throw new Evaluator_Exception('Expresión desconocida');
            }
        }
        $result = $this->stack->pop()->getValue();
        if (!$this->stack->isEmpty()) {
            throw new Evaluator_Exception('Expresión incorrecta');
        }

        return $result;
    }
}