<?php

class Contable_Model_LibrosIVAMapper extends Rad_Mapper
{
    protected $_class = 'Contable_Model_DbTable_LibrosIVA';

    public function getFechaDesdeUltimoLibroIVA()
    {
        $db = Zend_Registry::get('db');
        return $db->fetchOne("SELECT MAX(STR_TO_DATE(CONCAT(Descripcion,'-01'), '%Y-%m-%d')) FROM LibrosIVA");
    }

    public function getFechaHastaUltimoLibroIVA()
    {
        $db = Zend_Registry::get('db');
        return $db->fetchOne("SELECT LAST_DAY(MAX(STR_TO_DATE(CONCAT(Descripcion,'-01'), '%Y-%m-%d'))) FROM LibrosIVA");
    }
    
}
