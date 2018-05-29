<?php
/**
 * @see Zend_Config_Yaml
 */
require_once 'Zend/Config/Yaml.php';

/**
 * YAML Adapter for Zend_Config
 *
 * @package      Rad
 * @subpackage   Config
 */
class Rad_Config_Yaml extends Zend_Config_Yaml
{
    protected $_yamlDecoder = array('Rad_Config_Yaml', 'decode');

    /**
     * Very dumb YAML parser
     *
     * Until we have Zend_Yaml...
     *
     * @param  string $yaml YAML source
     * @return array Decoded data
     */
    public static function decode($yaml)
    {
        $lines = explode("\n", $yaml);
        reset($lines);
        return static::_decodeYaml(0, $lines);
    }
    /**
     * SOBRESCRIVE LA FUNCION PARA PARSEAR LOS YML YA QUE ZEND 1.11.11 NO SOPORTA "/" EN LOS NOMBRES
     * Service function to decode YAML
     *
     * @param  int $currentIndent Current indent level
     * @param  array $lines  YAML lines
     * @return array|string
     */
    protected static function _decodeYaml($currentIndent, &$lines)
    {      
        $config   = array();
        $inIndent = false;
        while (list($n, $line) = each($lines)) {
            $lineno = $n + 1;
            
            $line = rtrim(preg_replace("/#.*$/", "", $line));
            if (strlen($line) == 0) {
                continue;
            }

            $indent = strspn($line, " ");

            // line without the spaces
            $line = trim($line);
            if (strlen($line) == 0) {
                continue;
            }

            if ($indent < $currentIndent) {
                // this level is done
                prev($lines);
                return $config;
            }

            if (!$inIndent) {
                $currentIndent = $indent;
                $inIndent      = true;
            }

            if (preg_match("/([\w\/]+):\s*(.*)/", $line, $m)) {
                // key: value
                if (strlen($m[2])) {
                    // simple key: value
                    $value = rtrim(preg_replace("/#.*$/", "", $m[2]));
                    // Check for booleans and constants
                    if (preg_match('/^(t(rue)?|on|y(es)?)$/i', $value)) {
                        $value = true;
                    } elseif (preg_match('/^(f(alse)?|off|n(o)?)$/i', $value)) {
                        $value = false;
                    } elseif (!self::$_ignoreConstants) {
                        // test for constants
                        $value = self::_replaceConstants($value);
                    }
                } else {
                    // key: and then values on new lines
                    $value = self::_decodeYaml($currentIndent + 1, $lines);
                    if (is_array($value) && !count($value)) {
                        $value = "";
                    }
                }
                $config[$m[1]] = $value;
            } elseif ($line[0] == "-") {
                // item in the list:
                // - FOO
                if (strlen($line) > 2) {
                    $config[] = substr($line, 2);
                } else {
                    $config[] = self::_decodeYaml($currentIndent + 1, $lines);
                }
            } else {
                require_once 'Zend/Config/Exception.php';
                throw new Zend_Config_Exception(sprintf(
                    'Error parsing YAML at line %d - unsupported syntax: "%s"',
                    $lineno, $line
                ));
            }
        }
        return $config;
    }
}