<?php
require 'Abstract.php';

class Task_RegenerarRegistroDePrecios extends Task_Abstract
{
    public function run() {

        $M = new Base_Model_DbTable_PersonasRegistrosDePrecios();
        $M->regenerarPrecioArticulo();
    
        echo "Proceso finalizado correctamente.\n";
    }

    /**
     * es llamado en caso de pasar -h o de no pasar los parametros requeridos
     */
    public function printHelp()
    {
        echo "Regenera los valores de la tabla de registro de precios para las FC y FV (no toca las informadas)\n";
    }
}