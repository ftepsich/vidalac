<?php

namespace Rad\Util;
/**
 * Exportador de archivos en formato de ancho fijo o con separadores
 *
 * @author Martin A. Santangelo
 * @copyright SmartSoftware SRL
 */
class FileExport
{
    const MODE_SEPARATOR = 1;
    const MODE_FIXED     = 2;

    protected $_file;
    protected $_mode;
    protected $_content;

    /**
     * Formato con el que se guardara el archivo
     * Ejemplo:
     *  MODE_SEPARATOR:
     *  array(
     *      'columna1' => array(
     *          'format' => function($val){return $val*-1} // OPCIONAL
     *       )
     *  )
     *  MODE_FIXED:
     *  array(
     *      'columna1' => array(
     *          'width'  => 10,     // 10 caracteres de ancho
     *          'align'  => 'left', // alineacion (left o right)
     *          'fill'   => '0',    // caracteres con los que rellena
     *          'format' => function($val){return $val*-1} // OPCIONAL callback pre formateo
     *      )
     *  )
     * @var array
     */
    protected $_lineFormat = array();

    protected $_separator = ';';
    protected $_lineEnd   = "\n";

    public function __construct($mode = self::MODE_SEPARATOR)
    {
        $this->_mode = $mode;
    }

    public function setLineFormat($format)
    {
        $this->_lineFormat = $format;
    }

    public function setLineEnd($le)
    {
        $this->_lineEnd = $le;
    }

    public function setSeparator($sep)
    {
        $this->_separator = $sep;
    }

    public function getContent()
    {
        return $this->_content;
    }

    public function clearContent()
    {
        $this->_content = '';
    }

    protected function openFile($file)
    {
        $this->_file = fopen($file, 'w');
        return $this->_file;
    }

    public function addLine($line)
    {
        $formatedLine = $this->_formatLine($line).$this->_lineEnd;

        if ($this->_file) {
            return fwrite($this->_file, $formatedLine);
        } else {
            $this->_content .= $formatedLine;
            return true;
        }
    }

    public function addAll($ar)
    {
        foreach ($ar as $value) {
            $this->addLine($value);
        }
    }

    protected function _formatLine($line)
    {
        $formatedLine = array();

        if ($this->_mode == self::MODE_SEPARATOR) {
            foreach ($line as $key => $value) {
                if ($this->_lineFormat[$key]) {

                    $f = @$this->_lineFormat[$key]['format'];
                    // si tiene formatedor lo usamos
                    if (is_callable($f)) $formatedLine[] = $f($value);
                    else $formatedLine[] = $value;
                } else {
                    $formatedLine[] = $value;
                }
            }
            return implode($this->_separator, $formatedLine);
        }

        if ($this->_mode == self::MODE_FIXED) {
            foreach ($line as $key => $value) {
                if ($this->_lineFormat[$key]) {
                    $format = $this->_lineFormat[$key];

                    $f = @$format['format'];
                    // si tiene formatedor lo usamos
                    if (is_callable($f)) {
                        $value = $f($value);
                    }
                    // cortamos al ancho fijo
                    $value = substr($value, 0, $format['width']);
                    // alineo y relleno

                    if($format['align'] == 'left'){
                        $value = str_pad($value, $format['width'], $format['fill']);
                    } else {
                        $value = str_pad($value, $format['width'], $format['fill'], STR_PAD_LEFT);
                    }
                    $formatedLine[] = $value;
                } else {
                    throw new Exception('Formato no especificado para columna '.$key);
                }
            }
            return implode('', $formatedLine);
        }
    }

    protected function closeFile()
    {
        if($this->_file) fclose($this->_file);
        $this->_file = null;
    }
}
