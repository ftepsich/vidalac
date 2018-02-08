<?php
require 'Abstract.php';

class Task_ListarClientes extends Task_Abstract
{
    public function run($id) {
        $clientes = new Base_Model_DbTable_Clientes();

        if ($id) $where = "Id = $id";

        $lista = $clientes->fetchAll($where);

        foreach ($lista as $c) {
            echo $c->RazonSocial. " ". $c->Cuit."\n";
        }
    }

    /**
     * es llamado en caso de pasar -h o de no pasar los parametros requeridos
     */
    public function printHelp()
    {
        echo "Muestra una lista de clientes, Puede pasarse un ID de cliente para ver solo uno\n";
    }
}