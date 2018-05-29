<?php

/**
 *  Funciones genericas
 *
 */
class Rad_CustomFunctions
{

    /**
     * Reemplaza en un string o array recursivamente un valor por otro
     *
     * @param type $find
     * @param type $replace
     * @param type $data
     */
    public static function recursive_array_replace ($find, $replace, &$data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                if (is_array($value)) {
                    self::recursive_array_replace($find, $replace, $data[$key]);
                } else if(!is_object($value)){
                    $data[$key] = str_replace($find, $replace, $value);
                }
            }
        } else {
            $data = str_replace($find, $replace, $data);
        }
    }

    /**
     * Convierte un objeto a array
     *
     * @param object $d
     * @return array
     */
    public static function objectToArray ($d)
    {
        if (is_object($d) && $d instanceof stdClass) {
            $d = get_object_vars($d);
        }

        if (is_array($d)) {
            return array_map(__METHOD__, $d);
        } else {
            // Return array
            return $d;
        }
    }

    /**
     * Devuelve el primer dia del mes formato en 'Y-m-d'
     *
     * @param string $date Dia en formato valido para strtotime (http://ar.php.net/manual/es/function.date.php)
     * @return string
     */
    public static function firstOfMonth ($date)
    {
        $s = strtotime($date);
        return date("Y-m-d", strtotime(date('m',$s).'/01/'.date('Y',$s).' 00:00:00'));
    }

    /**
     * Devuelve el ultimo dia del mes formato en 'Y-m-d'
     *
     * @param string $date Dia en formato valido para strtotime (http://ar.php.net/manual/es/function.date.php)
     * @return string
     */
    public static function lastOfMonth ($date)
    {
        $s = strtotime($date);
        return date("Y-m-d", strtotime('-1 second',strtotime('+1 month', strtotime(date('m', $s).'/01/'.date('Y', $s).' 00:00:00'))));
    }

    /**
     * Mezcla dos Arrays recursivamente
     *
     * @param mixed $a1
     * @param mixed $a2
     * @return array
     */
    public static function mergeConf (&$a1, &$a2)
    {
        $newArray = array();
        foreach ($a1 as $key => $v) {
            //if (!isset($a2[$key])) {
            if (!array_key_exists($key, $a2)) {
                $newArray[$key] = $v;
                continue;
            }
            if (is_array($v)) {
                if (!is_array($a2[$key])) {
                    $newArray[$key] = $a2[$key];
                    continue;
                }
                $newArray[$key] = self::mergeConf($a1[$key], $a2[$key]);
                continue;
            }
            $newArray[$key] = $a2[$key];
        }
        foreach ($a2 as $key => $v) {
            if (!array_key_exists($key, $a1)) {
                $newArray[$key] = $v;
            }
        }
        return $newArray;
    }

    /**
     * Carga la configuracion de un ini
     * Crea Zend_Json_Expr para los valores que contengan javascript
     *
     * @param string $fileNombre de archivo ini
     * @param string $iniSection
     * @param string $simple no se cargaran los JavaScripts del ini
     * @return array
     */
    public static function loadIniConfig ($file, $iniSection = null, $simple = false)
    {
        $registry = Zend_Registry::getInstance();
        if (!isset($registry['RadModelGuiConfig'][$file])) {
            if (file_exists(APPLICATION_PATH . $file)) {

                $config = new Rad_Config_Ini(APPLICATION_PATH . $file);
                $arrayConfig = $config->toArray();
                $arrayConfig = self::_fixArrayValues($arrayConfig, $simple);
                $registry['RadModelGuiConfig'][$file] = $arrayConfig;
            } else {
                throw new Zend_Exception("No existe el archivo " . APPLICATION_PATH . $file);
            }
        } else {
            $arrayConfig = $registry['RadModelGuiConfig'][$file];
        }
        if ($iniSection)
            return $arrayConfig[$iniSection];
        return $arrayConfig;
    }

    /**
     * Arregla el array cargado del ini
     *
     * @param array $array
     * @param bool $simple si es true no crea los javascript
     * @return array
     */
    protected static function _fixArrayValues ($array, $simple = false)
    {
        foreach ($array as $key => $values) {
            if (!is_array($values)) {
                if (is_numeric($values)) {
                    $array[$key] = (float) $values;
                } elseif (trim($values) == "") {
                    $array[$key] = null;
                } elseif (trim($values) == "true") {
                    $array[$key] = true;
                } elseif (trim($values) == "false") {
                    $array[$key] = false;
                } elseif (substr(trim($values), 0, 3) == "JS:") {
                    if (!$simple) {
                        $array[$key] = new Zend_Json_Expr(substr(trim($values), 3));
                    } else {
                        unset($array[$key]);
                    }
                }
            } else {
                $array[$key] = self::_fixArrayValues($values);
            }
        }
        return $array;
    }

    /**
     * Devuelve una suma de una columna de un array u objeto, o false
     * si se requiere controlar por valores null
     *
     * @param array|object|Traversable $iter Objeto array o iterable
     * @param int|string $col Indice numerico o asociativo por el cual se debe sumar
     * @param bool $panicOnNull Si se debe arrojar una excepcion al encontrar un valor null
     * @return float|int|bool Suma o false si el objeto no es iterable
     */
    static function array_sum_column ($iter, $col = 0, $panicOnNull = false)
    {
        if (is_array($iter) || ($iter instanceof Traversable)) {
            $sum = 0;
            foreach ($iter as $i) {
                if (is_array($iter)) {
                    if ($panicOnNull && is_null($i[$col]))
                        throw new Rad_Exception('Rad_CustomFunction::array_sum_column: Uno de los valores es null');
                    $sum += $i[$col];
                } else {
                    if ($panicOnNull && is_null($i->{$col}))
                        throw new Rad_Exception('Rad_CustomFunction::array_sum_column: Uno de los valores es null');
                    $sum += $i->{$col};
                }
            }
        } else {
            return false;
        }
        return $sum;
    }

    /**
     * Convierte un numero a su representacion en texto
     *
     * @deprecated
     * @param int $num Numero a convertir
     * @param bool $fem Salida en Femenino
     * @param bool $dec Salida con numeros decimales
     * @return string
     */
    static function num2letras ($num, $fem = false, $dec = true)
    {
        $matuni[2] = "dos";
        $matuni[3] = "tres";
        $matuni[4] = "cuatro";
        $matuni[5] = "cinco";
        $matuni[6] = "seis";
        $matuni[7] = "siete";
        $matuni[8] = "ocho";
        $matuni[9] = "nueve";
        $matuni[10] = "diez";
        $matuni[11] = "once";
        $matuni[12] = "doce";
        $matuni[13] = "trece";
        $matuni[14] = "catorce";
        $matuni[15] = "quince";
        $matuni[16] = "dieciseis";
        $matuni[17] = "diecisiete";
        $matuni[18] = "dieciocho";
        $matuni[19] = "diecinueve";
        $matuni[20] = "veinte";
        $matunisub[2] = "dos";
        $matunisub[3] = "tres";
        $matunisub[4] = "cuatro";
        $matunisub[5] = "quin";
        $matunisub[6] = "seis";
        $matunisub[7] = "sete";
        $matunisub[8] = "ocho";
        $matunisub[9] = "nove";
        $matdec[2] = "veint";
        $matdec[3] = "treinta";
        $matdec[4] = "cuarenta";
        $matdec[5] = "cincuenta";
        $matdec[6] = "sesenta";
        $matdec[7] = "setenta";
        $matdec[8] = "ochenta";
        $matdec[9] = "noventa";
        $matsub[3] = 'mill';
        $matsub[5] = 'bill';
        $matsub[7] = 'mill';
        $matsub[9] = 'trill';
        $matsub[11] = 'mill';
        $matsub[13] = 'bill';
        $matsub[15] = 'mill';
        $matmil[4] = 'millones';
        $matmil[6] = 'billones';
        $matmil[7] = 'de billones';
        $matmil[8] = 'millones de billones';
        $matmil[10] = 'trillones';
        $matmil[11] = 'de trillones';
        $matmil[12] = 'millones de trillones';
        $matmil[13] = 'de trillones';
        $matmil[14] = 'billones de trillones';
        $matmil[15] = 'de billones de trillones';
        $matmil[16] = 'millones de billones de trillones';
        $num = trim((string) @$num);
        if ($num[0] == '-') {
            $neg = 'menos ';
            $num = substr($num, 1);
        } else
            $neg = '';
        while ($num[0] == '0')
            $num = substr($num, 1);
        if ($num[0] < '1' or $num[0] > 9)
            $num = '0' . $num;
        $zeros = true;
        $punt = false;
        $ent = '';
        $fra = '';
        for ($c = 0; $c < strlen($num); $c++) {
            $n = $num[$c];
            if (!(strpos(".,'''", $n) === false)) {
                if ($punt)
                    break;
                else {
                    $punt = true;
                    continue;
                }
            } elseif (!(strpos('0123456789', $n) === false)) {
                if ($punt) {
                    if ($n != '0')
                        $zeros = false;
                    $fra .= $n;
                } else
                    $ent .= $n;
            } else
                break;
        }
        $ent = '     ' . $ent;
        if ($dec and $fra and !$zeros) {
            $fin = ' con ';
            if (strlen($fra) > 2) {
                $fra = substr($fra, 0, 2);
            }
            if (($fra < 10) && ($fra[0] != 0)) {
                $fin .= sprintf('%02d', $fra * 10) . '/100';
            } else {
                $fin .= sprintf('%02d', $fra)  . '/100';
            }
        } else
            $fin = '';
        if ((int) $ent === 0)
            return 'Cero ' . $fin;
        $tex = '';
        $sub = 0;
        $mils = 0;
        $neutro = false;
        while (($num = substr($ent, - 3)) != '   ') {
            $ent = substr($ent, 0, - 3);
            if (++$sub < 3 and $fem) {
                $matuni[1] = 'uno';
                $subcent = 'os';
            } else {
                $matuni[1] = $neutro ? 'un' : 'uno';
                $subcent = 'os';
            }
            $t = '';
            $n2 = substr($num, 1);
            if ($n2 == '00') {

            } elseif ($n2 < 21)
                $t = ' ' . $matuni[(int) $n2];
            elseif ($n2 < 30) {
                $n3 = $num[2];
                if ($n3 != 0)
                    $t = 'i' . $matuni[$n3];
                $n2 = $num[1];
                $t = ' ' . $matdec[$n2] . $t;
            } else {
                $n3 = $num[2];
                if ($n3 != 0)
                    $t = ' y ' . $matuni[$n3];
                $n2 = $num[1];
                $t = ' ' . $matdec[$n2] . $t;
            }
            $n = $num[0];
            if ($num == 100) {
                $t = ' cien' . $t;
            } elseif ($n == 1) {
                $t = ' ciento' . $t;
            } elseif ($n == 5) {
                $t = ' ' . $matunisub[$n] . 'ient' . $subcent . $t;
            } elseif ($n != 0) {
                $t = ' ' . $matunisub[$n] . 'cient' . $subcent . $t;
            }
            if ($sub == 1) {

            } elseif (!isset($matsub[$sub])) {
                if ($num == 1) {
                    $t = ' mil';
                } elseif ($num > 1) {
                    $t .= ' mil';
                }
            } elseif ($num == 1) {
                $t .= ' ' . $matsub[$sub] . 'ón';
            } elseif ($num > 1) {
                $t .= ' ' . $matsub[$sub] . 'ones';
            }
            if ($num == '000')
                $mils++;
            elseif ($mils != 0) {
                if (isset($matmil[$sub]))
                    $t .= ' ' . $matmil[$sub];
                $mils = 0;
            }
            $neutro = true;
            $tex = $t . $tex;
        }
        $tex = $neg . substr($tex, 1) . $fin;
        return strtoupper($tex);
    }

    /**
     * Interpreta un string de CSV en un array
     * (Implementado porque esta disponible a partir de PHP 5)
     * http://php.net/manual/es/function.str-getcsv.php
     *
     * @param string $input
     * @param string $delimiter
     * @param string $enclosure
     * @param string $escape
     * @param string $eol
     * @return array
     */
    static function str_getcsv ($input, $delimiter = ',', $enclosure = '"', $escape = '\\', $eol = '\n')
    {
        if (is_string($input) && !empty($input)) {
            $output = array();
            $tmp = preg_split("/" . $eol . "/", $input);

            if (is_array($tmp) && !empty($tmp)) {
                while (list($line_num, $line) = each($tmp)) {

                    if (preg_match("/" . $escape . $enclosure . "/", $line)) {
                        while ($strlen = strlen($line)) {
                            $pos_delimiter = strpos($line, $delimiter);
                            $pos_enclosure_start = strpos($line, $enclosure);
                            if (
                                    is_int($pos_delimiter) && is_int($pos_enclosure_start)
                                    && ($pos_enclosure_start < $pos_delimiter)
                            ) {
                                $enclosed_str = substr($line, 1);
                                $pos_enclosure_end = strpos($enclosed_str, $enclosure);
                                $enclosed_str = substr($enclosed_str, 0, $pos_enclosure_end);
                                $output[$line_num][] = $enclosed_str;
                                $offset = $pos_enclosure_end + 3;

                                // Fix Churi 21/07/2010
                            } else if ($line[0] == $delimiter) {
                                $output[$line_num][] = null;
                                $offset = 1;
                                // EO Fix
                            } else {
                                if (empty($pos_delimiter) && empty($pos_enclosure_start)) {
                                    $output[$line_num][] = substr($line, 0);
                                    $offset = strlen($line);
                                } else {
                                    $output[$line_num][] = substr($line, 0, $pos_delimiter);
                                    $offset = (
                                            !empty($pos_enclosure_start)
                                            && ($pos_enclosure_start < $pos_delimiter)
                                            ) ? $pos_enclosure_start : $pos_delimiter + 1;
                                }
                            }
                            $line = substr($line, $offset);
                        }
                    } else {
                        $line = preg_split("/" . $delimiter . "/", $line);
                        if (is_array($line) && !empty($line[0])) {
                            $output[$line_num] = $line;
                        }
                    }
                }
                return $output;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

}

