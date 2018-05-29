<?php
require 'Abstract.php';

define('Task_BirtLibrarysPath', APPLICATION_PATH."/../birt/Reports/librerias/" );

/**
 * Publica el cierre de una Factura
 */
class Task_BirtCompileLibrarys extends Task_Abstract
{
    public function run() {

        $birtEngine = new Rad_BirtEngine();

        // Abre la carpeta
        if ($dh = opendir(Task_BirtLibrarysPath)) { 
            // itero sobre los archivos y busco los .sources
            while (($file = readdir($dh)) !== false) { 
                
                if (is_file(Task_BirtLibrarysPath . $file) && substr($file,-7) == '.source'){
                    // compilo
                    echo "Compilando $file\n";
                    $birtEngine->compile(Task_BirtLibrarysPath . substr($file,0,-7), true);

                } 
            } 
            closedir($dh); 
        }
    }

    /**
     * es llamado en caso de pasar -h o de no pasar los parametros requeridos
     */
    public function printHelp()
    {
        echo "Compila las librerias de Birt, no requiere parametros";
    }
}

