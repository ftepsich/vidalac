<?php

class Base_Model_ClientesMapper extends Rad_Mapper
{
    protected $_class = 'Base_Model_DbTable_Clientes';

    public function getIBProximosVencimientosCM05($persona)
    {
        $db = Zend_Registry::get('db');
        return $db->fetchOne("SELECT COUNT(Id) FROM personasingresosbrutos WHERE Persona = $persona AND FechaVencimientoCM05 IS NOT NULL AND FechaVencimientoCM05 < CURDATE()");
    }
    
    public function getIBItems($persona)
    {
        $db = Zend_Registry::get('db');
        return $db->fetchOne("SELECT COUNT(Id) FROM personasingresosbrutos WHERE Persona = $persona AND FechaBaja IS NULL");
    }
}

