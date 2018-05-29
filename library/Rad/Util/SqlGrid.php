<?php

/**
 * Rad_Util_SqlGrid
 *
 * Crea una tabla temporal dado un SQL y genera los metadatos para las grillas extjs autogrid
 *
 * @package     Rad
 * @subpackage 	Util
 * @class       Rad_Util_SqlGrid
 * @author      Martin A. Santangelo
 * @copyright   SmartSoftware Argentina 2012
 */
class Rad_Util_SqlGrid
{
    public static function fetch($sql, $limit = null, $offset = null) {
        // Pedimos el conecto de la DB
        $db = Zend_Registry::get('db');
        
        $sql = 'CREATE TEMPORARY TABLE temporalsqlgrid as '.$sql;
        
        $db->query($sql);
        
        $count = $db->fetchOne('select FOUND_ROWS()');
        
        $data = new stdClass();
        
        $data->rows  = $db->fetchAll(self::generateSql($limit, $offset));
        $data->count = $count;
        $data->success         = true;
        
        $data = self::generateMetaData($data);
        return $data;
    }
    
    protected static function generateSql($limit, $offset)
    {
        if ($limit) $sqlad = " limit $offset, $limit";
        return 'SELECT * from temporalsqlgrid'.$sqlad;
    }
    
    protected static function generateMetaData($data)
    {
        $table = new Rad_Db_Table(array('name'=>'temporalsqlgrid'));
        $modelMetadata = $table->getMetadataWithJoins();
        
        foreach ($modelMetadata as $field => $metaData) {
            $data->metaData->fields[] = Rad_DbFieldToExtMapper::getMetaDataFromNoDbField($field, $metaData, false, true);
        }
        
        $data = self::addCommonMetadata($data);
        return $data;
    }
    
    /**
     *  Agrega la configuracion comun para los stores
     *
     */
    protected static function addCommonMetadata($data)
    {

//        $groupField = $this->_model->getGridGroupField();

//        $this->_metadata->groupField      = $groupField;

        $data->metaData->root            = "rows";
//        $this->_metadata->idProperty      = $primary[1];
        $data->metaData->messageProperty = 'msg';
        $data->metaData->totalProperty   = "count";
        $data->metaData->successProperty = "success";

        $data->metaData->start           = ($_POST['start']) ? $_POST['start'] : 0;
        $data->metaData->limit           = ($_POST['limit']) ? $_POST['limit'] : 20;
        
        return $data;
    }
}