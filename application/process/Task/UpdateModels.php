<?php
require 'Abstract.php';
require_once 'FactElect/wsfev1.php';



class Task_UpdateModels extends Task_Abstract
{
    public function run() 
    {
        $front = Zend_Controller_Front::getInstance();

        $c = new Rad_Util_Colors;

        $acl = array();
        
        $classes = array();

        echo PHP_EOL.$c("Módulos:",'light_blue',null).PHP_EOL;
        
        foreach ($front->getControllerDirectory() as $module => $path) {
            
            if ($module == 'Window') continue;
            
            echo ' - '.$module.PHP_EOL;
            
            $path = substr($path, 0, strlen($path)-12);
            $path = $path . DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'DbTable';

            $this->_getClases($path,$classes);
        }
        sort($classes);

        echo PHP_EOL.$c("Modelos:",'light_blue',null).PHP_EOL;
        
        foreach ($classes as $class) {
            echo $class;
            $modelos = new Model_DbTable_Modelos();
            $row = $modelos->fetchAll("Descripcion like '%".$class."%'");
            if (!count($row)) {
                echo $c(" insertado",'light_blue',null);
                $modelos->insert(array(
                        'Descripcion' => $class
                    )
                );
            } else {
                echo $c(" existe",'light_green',null);
            }
            echo PHP_EOL;
        }
    }

    protected function _getClases ($path, &$classes)
    {
        $dir = @scandir($path); 

        // no existe el directorio, continuo
        if ($dir === false) return;

        foreach ($dir as $file) {
            if (strpos($file, ".php") !== false) {
                $classes = array_merge($this->file_get_php_classes($path.DIRECTORY_SEPARATOR.$file), $classes);
            } else {
                if(is_dir($path.DIRECTORY_SEPARATOR.$file) && $file != "." && $file != ".."){
                    $this->_getClases($path.DIRECTORY_SEPARATOR.$file, $classes);
                }
            }
        }

    }    


    protected function file_get_php_classes ($filepath)
    {
        $php_code = file_get_contents($filepath);
        $classes = $this->get_php_classes($php_code);
        return $classes;
    }

    protected function get_php_classes ($php_code)
    {
        $classes = array();
        $tokens = token_get_all($php_code);
        $count = count($tokens);
        for ($i = 2; $i < $count; $i++) {
            if ($tokens[$i - 2][0] == T_CLASS && $tokens[$i - 1][0] == T_WHITESPACE && $tokens[$i][0] == T_STRING) {
                $class_name = $tokens[$i][1];
                $classes[] = $class_name;
            }
        }
        return $classes;
    }

    /**
     * es llamado en caso de pasar -h o de no pasar los parametros requeridos
     */
    public function printHelp()
    {
        echo "Updatea los modelos en la DB";
    }
}