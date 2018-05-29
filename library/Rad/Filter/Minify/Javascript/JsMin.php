<?php
 
/**
 * Based on JSMin by Ryan Grove and Steve Clay (in turn based on JsMin.c by Douglas Crockford
 * Copyright (c) 2002 Douglas Crockford  (www.crockford.com)
 *
 * Copyright (c) 2008 Ryan Grove <ryan@wonko.com>
 * Copyright (c) 2008 Steve Clay <steve@mrclay.org>
 * All rights reserved.
 *
 * @category   Rad
 * @package    Rad_Filter
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Rad_Filter_Minify_Javascript_JsMin implements Zend_Filter_Interface
{
    const ORD_LF            = 10;
    const ORD_SPACE         = 32;
    const ACTION_KEEP_A     = 1;
    const ACTION_DELETE_A   = 2;
    const ACTION_DELETE_A_B = 3;
 
    protected $_a           = "\n";
    protected $_b           = '';
    protected $_input       = '';
    protected $_inputIndex  = 0;
    protected $_inputLength = 0;
    protected $_lookAhead   = null;
    protected $_output      = '';
 
    /**
     * Defined by Zend_Filter_Interface
     *
     * Returns a string containing the minified Javascript.
     *
     * @param  string $value
     * @return string
     */
    public function filter($value)
    {
        //Perform minification on $value
        $value =& $this->_minify($value);
 
        //Return the minified string
        return $value;
    }
 
    /**
     * Take a string of javascript and minify it
     *
     * Also takes into account the set options when performing minification
     *
     * @param  string $javascript
     * @return string
     */
    protected function _minify($javascript){
        $this->_input       = str_replace("\r\n", "\n", $javascript);
        $this->_inputLength = strlen($this->_input);
 
        $this->_action(self::ACTION_DELETE_A_B);
 
        while ($this->_a !== null) {
            // determine next command
            $command = self::ACTION_KEEP_A; // default
            if ($this->_a === ' ') {
                if (! $this->_isAlphaNum($this->_b)) {
                    $command = self::ACTION_DELETE_A;
                }
            } elseif ($this->_a === "\n") {
                if ($this->_b === ' ') {
                    $command = self::ACTION_DELETE_A_B;
                } elseif (false === strpos('{[(+-', $this->_b)
                          && ! $this->_isAlphaNum($this->_b)) {
                    $command = self::ACTION_DELETE_A;
                }
            } elseif (! $this->_isAlphaNum($this->_a)) {
                if ($this->_b === ' '
                    || ($this->_b === "\n"
                        && (false === strpos('}])+-"\'', $this->_a)))) {
                    $command = self::ACTION_DELETE_A_B;
                }
            }
            $this->_action($command);
        }
        $this->_output = trim($this->_output);
        return $this->_output;
    }
 
    /**
     * ACTION_KEEP_A = Output A. Copy B to A. Get the next B.
     * ACTION_DELETE_A = Copy B to A. Get the next B.
     * ACTION_DELETE_A_B = Get the next B.
     */
    protected function _action($command)
    {
        switch ($command) {
            case self::ACTION_KEEP_A:
                $this->_output .= $this->_a;
                // fallthrough
            case self::ACTION_DELETE_A:
                $this->_a = $this->_b;
                if ($this->_a === "'" || $this->_a === '"') { // string literal
                    $str = $this->_a; // in case needed for exception
                    while (true) {
                        $this->_output .= $this->_a;
                        $this->_a       = $this->_get();
                        if ($this->_a === $this->_b) { // end quote
                            break;
                        }
                        if (ord($this->_a) <= self::ORD_LF) {
                            throw new Zend_Filter_Minify_Javascript_Exception(
                                'Unterminated String: ' . var_export($str, true));
                        }
                        $str .= $this->_a;
                        if ($this->_a === '\\') {
                            $this->_output .= $this->_a;
                            $this->_a       = $this->_get();
                            $str .= $this->_a;
                        }
                    }
                }
                // fallthrough
            case self::ACTION_DELETE_A_B:
                $this->_b = $this->_next();
                if ($this->_b === '/' && $this->_isRegexpLiteral()) { // RegExp literal
                    $this->_output .= $this->_a . $this->_b;
                    $pattern = '/'; // in case needed for exception
                    while (true) {
                        $this->_a = $this->_get();
                        $pattern .= $this->_a;
                        if ($this->_a === '/') { // end pattern
                            break; // while (true)
                        } elseif ($this->_a === '\\') {
                            $this->_output .= $this->_a;
                            $this->_a       = $this->_get();
                            $pattern      .= $this->_a;
                        } elseif (ord($this->_a) <= self::ORD_LF) {
                            throw new Zend_Filter_Minify_Javascript_Exception(
                                'Unterminated RegExp: '. var_export($pattern, true));
                        }
                        $this->_output .= $this->_a;
                    }
                    $this->_b = $this->_next();
                }
            // end case ACTION_DELETE_A_B
        }
    }
 
    protected function _isRegexpLiteral()
    {
        if (false !== strpos("\n{;(,=:[!&|?", $this->_a)) { // we aren't dividing
            return true;
        }
        if (' ' === $this->_a) {
            $length = strlen($this->_output);
            if ($length < 2) { // weird edge case
                return true;
            }
            // you can't divide a keyword
            if (preg_match('/(?:case|else|in|return|typeof)$/', $this->_output, $m)) {
                if ($this->_output === $m[0]) { // odd but could happen
                    return true;
                }
                // make sure it's a keyword, not end of an identifier
                $charBeforeKeyword = substr($this->_output, $length - strlen($m[0]) - 1, 1);
                if (! $this->isAlphaNum($charBeforeKeyword)) {
                    return true;
                }
            }
        }
        return false;
    }
 
    /**
     * Get next char. Convert ctrl char to space.
     */
    protected function _get()
    {
        $c = $this->_lookAhead;
        $this->_lookAhead = null;
        if ($c === null) {
            if ($this->_inputIndex < $this->_inputLength) {
                $c = $this->_input{$this->_inputIndex};
                $this->_inputIndex += 1;
            } else {
                return null;
            }
        }
        if ($c === "\r" || $c === "\n") {
            return "\n";
        }
        if (ord($c) < self::ORD_SPACE) { // control char
            return ' ';
        }
        return $c;
    }
 
    /**
     * Get next char. If is ctrl character, translate to a space or newline.
     */
    protected function _peek()
    {
        $this->_lookAhead = $this->_get();
        return $this->_lookAhead;
    }
 
    /**
     * Is $c a letter, digit, underscore, dollar sign, escape, or non-ASCII?
     */
    protected function _isAlphaNum($c)
    {
        return (preg_match('/^[0-9a-zA-Z_\\$\\\\]$/', $c) || ord($c) > 126);
    }
 
    protected function _singleLineComment()
    {
        $comment = '';
        while (true) {
            $get = $this->_get();
            $comment .= $get;
            if (ord($get) <= self::ORD_LF) { // EOL reached
                // if IE conditional comment
                if (preg_match('/^\\/@(?:cc_on|if|elif|else|end)\\b/', $comment)) {
                    return "/{$comment}";
                }
                return $get;
            }
        }
    }
 
    protected function _multipleLineComment()
    {
        $this->_get();
        $comment = '';
        while (true) {
            $get = $this->_get();
            if ($get === '*') {
                if ($this->_peek() === '/') { // end of comment reached
                    $this->_get();
                    // if comment preserved by YUI Compressor
                    if (0 === strpos($comment, '!')) {
                        return "\n/*" . substr($comment, 1) . "*/\n";
                    }
                    // if IE conditional comment
                    if (preg_match('/^@(?:cc_on|if|elif|else|end)\\b/', $comment)) {
                        return "/*{$comment}*/";
                    }
                    return ' ';
                }
            } elseif ($get === null) {
                throw new Zend_Filter_Minify_Javascript_Exception('Unterminated Comment: ' . var_export('/*' . $comment, true));
            }
            $comment .= $get;
        }
    }
 
    /**
     * Get the next character, skipping over comments.
     * Some comments may be preserved.
     */
    protected function _next()
    {
        $get = $this->_get();
        if ($get !== '/') {
            return $get;
        }
        switch ($this->_peek()) {
            case '/': return $this->_singleLineComment();
            case '*': return $this->_multipleLineComment();
            default: return $get;
        }
    }
}