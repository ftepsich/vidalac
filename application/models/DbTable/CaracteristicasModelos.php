<?php

class Model_DbTable_CaracteristicasModelos extends Rad_Db_Table
{
    protected $_name = 'CaracteristicasModelos';

    protected $_referenceMap    = array(

        'Modelos' => array(
            'columns'           => 'Modelo',
            'refTableClass'     => 'Model_DbTable_Modelos',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'Modelos',
            'refColumns'        => 'Id',
            'comboPageSize'     => '10'
        ),
        'Caracteristicas' => array(
            'columns'           => 'Caracteristica',
            'refTableClass'     => 'Model_DbTable_Caracteristicas',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'Caracteristicas',
            'refColumns'        => 'Id',
            'comboPageSize'     => '10'
        )
    );

    protected $_dependentTables = array('Model_DbTable_CaracteristicasValores');

    public static function getCaracteristicas($modelo)
    {
        $db = Zend_Registry::get('db');
        if (!is_numeric($modelo)) {
            $modelo = $db->fetchOne("SELECT Id From Modelos where Descripcion = '$modelo'");
            if (!is_numeric($modelo)) throw new Rad_Db_Table_Exception('El parametro modelo debe ser númerico o un string con la clase del modelo');
        }

        $campos = $db->fetchAll("
            SELECT  C.Descripcion, C.TipoDeCampo, C.Id, CM.Id as IdCM
            FROM    Caracteristicas C
                    inner join CaracteristicasModelos CM on C.Id = CM.Caracteristica and CM.Modelo = $modelo
        ");

        if (!$campos) $campos = array();

        return $campos;
    }
}