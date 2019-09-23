<?php

class Contable_Model_PeriodosImputacionSinIVAMapper extends Rad_Mapper
{
    protected $_class = 'Contable_Model_DbTable_PeriodosImputacionSinIVA';

    public function getFechaDesdeUltimo()
    {
        $db = Zend_Registry::get('db');
        return $db->fetchOne("SELECT MAX(STR_TO_DATE(CONCAT(Descripcion,'-01'), '%Y-%m-%d')) FROM PeriodosImputacionSinIVA");
    }

    public function getFechaHastaUltimo()
    {
        $db = Zend_Registry::get('db');
        return $db->fetchOne("SELECT LAST_DAY(MAX(STR_TO_DATE(CONCAT(Descripcion,'-01'), '%Y-%m-%d'))) FROM PeriodosImputacionSinIVA");
    }
    
}
