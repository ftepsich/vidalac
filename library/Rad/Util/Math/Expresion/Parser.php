<?php
namespace Rad\Util\Math\Expresion;


/**
 * Parseador de expresiones matematicas
 * Basado en https://github.com/SymDevStudio/MathExecutor/blob/master/NXP/Classes/TokenParser.php
 */
class Parser
{
    const DIGIT         = 1;
    const CHAR          = 2;
    const SPECIAL_CHAR  = 3;
    const LEFT_BRACKET  = 4;
    const RIGHT_BRACKET = 5;
    const SPACE         = 6;
    const COMMA         = 7;

    const TOKEN_NOTHING       = 'NOTHING';
    const TOKEN_STRING        = 'STRING';
    const TOKEN_OPERATOR      = 'OPERATOR';
    const TOKEN_LEFT_BRACKET  = 'LEFT_BRACKET';
    const TOKEN_RIGHT_BRACKET = 'RIGHT_BRACKET';
    const TOKEN_NUMBER        = 'NUMBER';
    const TOKEN_PARAM_NUMBER  = 'PARAM_NUMBER';
    const TOKEN_FUNC          = 'FUNC';
    const TOKEN_COMMA         = 'COMMA';

    const ERROR_STATE = 'ERROR_STATE';

    private $accumulator = '';

    private $state = self::TOKEN_NOTHING;

    private $queue = null;

    private $terms = array(
        self::DIGIT         => '[0-9\.]',
        self::SPECIAL_CHAR  => '[\!\$\^\*\/\|\-\+\=\~\<\>&|]',
        self::CHAR          => '[a-zA-Z\@\#\%]',
        self::LEFT_BRACKET  => '\(',
        self::RIGHT_BRACKET => '\)',
        self::SPACE         => '\s',
        self::COMMA         => '\,'
    );


    /**
     * transiciones entre los tokens
     */
    private $transitions = array(
        self::TOKEN_NOTHING => array(
            self::DIGIT         => self::TOKEN_NUMBER,
            self::CHAR          => self::TOKEN_STRING,
            self::SPECIAL_CHAR  => self::TOKEN_OPERATOR,
            self::LEFT_BRACKET  => self::TOKEN_LEFT_BRACKET,
            self::RIGHT_BRACKET => self::TOKEN_RIGHT_BRACKET,
            self::SPACE         => self::TOKEN_NOTHING,
            self::COMMA         => self::TOKEN_COMMA
        ),
        self::TOKEN_STRING => array(
            self::DIGIT         => self::TOKEN_STRING,
            self::CHAR          => self::TOKEN_STRING,
            self::SPECIAL_CHAR  => self::TOKEN_OPERATOR,
            self::LEFT_BRACKET  => self::TOKEN_LEFT_BRACKET,
            self::RIGHT_BRACKET => self::TOKEN_RIGHT_BRACKET,
            self::SPACE         => self::TOKEN_NOTHING,
            self::COMMA         => self::TOKEN_COMMA
        ),
        self::TOKEN_NUMBER => array(
            self::DIGIT         => self::TOKEN_NUMBER,
            self::CHAR          => self::ERROR_STATE,
            self::SPECIAL_CHAR  => self::TOKEN_OPERATOR,
            self::LEFT_BRACKET  => self::ERROR_STATE,
            self::RIGHT_BRACKET => self::TOKEN_RIGHT_BRACKET,
            self::SPACE         => self::TOKEN_NOTHING,
            self::COMMA         => self::TOKEN_COMMA
        ),
        self::TOKEN_OPERATOR => array(
            self::DIGIT         => self::TOKEN_NUMBER,
            self::CHAR          => self::TOKEN_STRING,
            self::SPECIAL_CHAR  => self::TOKEN_OPERATOR,
            self::LEFT_BRACKET  => self::TOKEN_LEFT_BRACKET,
            self::RIGHT_BRACKET => self::ERROR_STATE,
            self::SPACE         => self::TOKEN_NOTHING,
            self::COMMA         => self::ERROR_STATE
        ),
        self::ERROR_STATE => array(
            self::DIGIT         => self::ERROR_STATE,
            self::CHAR          => self::ERROR_STATE,
            self::SPECIAL_CHAR  => self::ERROR_STATE,
            self::LEFT_BRACKET  => self::ERROR_STATE,
            self::RIGHT_BRACKET => self::ERROR_STATE,
            self::SPACE         => self::ERROR_STATE,
            self::COMMA         => self::ERROR_STATE
        ),
        self::TOKEN_LEFT_BRACKET => array(
            self::DIGIT         => self::TOKEN_NUMBER,
            self::CHAR          => self::TOKEN_STRING,
            self::SPECIAL_CHAR  => self::TOKEN_OPERATOR,
            self::LEFT_BRACKET  => self::TOKEN_LEFT_BRACKET,
            self::RIGHT_BRACKET => self::TOKEN_RIGHT_BRACKET,
            self::SPACE         => self::TOKEN_NOTHING,
            self::COMMA         => self::ERROR_STATE
        ),
        self::TOKEN_RIGHT_BRACKET => array(
            self::DIGIT         => self::TOKEN_NUMBER,
            self::CHAR          => self::TOKEN_STRING,
            self::SPECIAL_CHAR  => self::TOKEN_OPERATOR,
            self::LEFT_BRACKET  => self::TOKEN_LEFT_BRACKET,
            self::RIGHT_BRACKET => self::TOKEN_RIGHT_BRACKET,
            self::SPACE         => self::TOKEN_NOTHING,
            self::COMMA         => self::TOKEN_COMMA
        ),
        self::TOKEN_COMMA => array(
            self::DIGIT         => self::TOKEN_NUMBER,
            self::CHAR          => self::TOKEN_STRING,
            self::SPECIAL_CHAR  => self::TOKEN_OPERATOR,
            self::LEFT_BRACKET  => self::TOKEN_LEFT_BRACKET,
            self::RIGHT_BRACKET => self::ERROR_STATE,
            self::SPACE         => self::TOKEN_NOTHING,
            self::COMMA         => self::ERROR_STATE
        )
    );



    function __construct()
    {
        $this->queue = new \SplQueue();
    }

    /**
     * Tokenize math expression
     * @param $expression
     * @return \SplQueue
     * @throws \Exception
     */
    public function tokenize($expression)
    {
        $oldState = null;
        for ($i=0; $i<strlen($expression); $i++) {
            $char        = substr($expression, $i, 1);
            $class       = $this->getSymbolType($char);
            $oldState    = $this->state;
            $this->state = $this->transitions[$this->state][$class];
            if ($this->state == self::ERROR_STATE) {
                throw new \Exception("Error en la expresion: ".substr($expression,0,$i).'<b>'.$expression[$i].'</b>'.substr($expression,$i+1));
            }
            $this->addToQueue($oldState);
            $this->accumulator .= $char;
        }
        if ($this->accumulator != '') {
            $token = new Token($this->state, $this->accumulator);
            $this->queue->push($token);
        }

        return $this->queue;
    }

    /**
     * @param $symbol
     * @return string
     * @throws \Exception
     */
    private function getSymbolType($symbol)
    {
        foreach ($this->terms as $class => $regex) {
            if (preg_match("/$regex/", $symbol)) {
                return $class;
            }
        }

        throw new \Exception("Unknown char '$symbol'");
    }

    /**
     * @param $oldState
     */
    private function addToQueue($oldState)
    {
        if ($oldState == self::TOKEN_NOTHING) {
            $this->accumulator = '';
            return;
        }
        if (($this->state != $oldState) || ($oldState == self::TOKEN_LEFT_BRACKET) || ($oldState == self::TOKEN_RIGHT_BRACKET)) {
            // echo $oldState.' -> '.$this->state.PHP_EOL;

            if ($oldState == self::TOKEN_STRING && $this->state == self::TOKEN_LEFT_BRACKET) {
                $oldState = self::TOKEN_FUNC;
            }

            $token = new Token($oldState, $this->accumulator);
            $this->queue->push($token);
            $this->accumulator = '';
        }
    }
}