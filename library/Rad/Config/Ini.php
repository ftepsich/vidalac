<?php
/**
 * Agrego soporte para includes al Zend_Config_Ini
 *
 * @author Martin A. Santangelo
 */
class Rad_Config_Ini extends Zend_Config_Ini
{
    protected $_filename;
    protected $_directory;

    public function __construct($filename, $section = null, $options = false)
    {
        $this->_filename = $filename;
        $this->_directory = dirname($filename);
        $options = true;
        parent::__construct($filename, $section, $options);
    }

    protected function _processKey($config, $key, $value)
    {
        if ($key === '@include') {
            $reader  = new self($this->_directory . '/' . $value);

            $config  = array_replace_recursive($config, $reader->toArray());
        } else {
            $config = parent::_processKey($config, $key, $value);
        }
        return $config;
    }

    /**
     * Load the ini file and preprocess the section separator (':' in the
     * section name (that is used for section extension) so that the resultant
     * array has the correct section names and the extension information is
     * stored in a sub-key called ';extends'. We use ';extends' as this can
     * never be a valid key name in an INI file that has been loaded using
     * parse_ini_file().
     *
     * @param string $filename
     * @throws Zend_Config_Exception
     * @return array
     */
    protected function _loadIniFile($filename)
    {
        $loaded = $this->_parseIniFile($filename);
        $iniArray = array();
        foreach ($loaded as $key => $data)
        {
            if ($key === '@include') {
                $reader  = new self($this->_directory . '/' . $data);
                $iniArray  = array_replace_recursive($iniArray, $reader->toArray());
                continue;
            }

            $pieces = explode($this->_sectionSeparator, $key);
            $thisSection = trim($pieces[0]);
            switch (count($pieces)) {
                case 1:
                    $iniArray[$thisSection] = $data;
                    break;

                case 2:
                    $extendedSection = trim($pieces[1]);
                    $iniArray[$thisSection] = array_merge(array(';extends'=>$extendedSection), $data);
                    break;

                default:
                    /**
                     * @see Zend_Config_Exception
                     */
                    require_once 'Zend/Config/Exception.php';
                    throw new Zend_Config_Exception("Section '$thisSection' may not extend multiple sections in $filename");
            }
        }

        return $iniArray;
    }
}