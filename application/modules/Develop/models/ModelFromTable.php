<?php

class Develop_Model_ModelFromTable_Exception extends Zend_Exception
{

}

class Develop_Model_ModelFromTable
{

    protected $_tableMetadata;
    protected $_tableName;
    protected $_db;
    protected $_fk;
    protected $_reference;
    protected $_path;
    protected $_fieldWidthByType = array(
        'int'      => '50',
        'decimal'  => '50',
        'date'     => '55',
        'time'     => '40',
        'datetime' => '80',
        'tinyint'  => '30',
        'varchar'  => '130',
        'text'     => '200'
    );
    protected $_fieldAlignByType = array(
        'int'      => 'right',
        'decimal'  => 'right',
        'date'     => 'right',
        'time'     => 'right',
        'datetime' => 'right',
        'tinyint'  => 'right',
        'varchar'  => 'left',
        'text'     => 'left'
    );
    protected $_relationTemplate = "
    '{referenced_table_name}' => array(
        'columns'           => '{column_name}',
        'refTableClass'     => 'Model_DbTable_{referenced_table_name}',
        'refJoinColumns'    => array('{remoteTableDescription}'),
        'comboBox'          => true,
        'comboSource'       => 'datagateway/combolist',
        'refTable'          => '{referenced_table_name}',
        'refColumns'        => '{referenced_column_name}',
        'comboPageSize'     => '10'
    )
";
    protected $_fieldTemplate = '
    {COLUMN_NAME}.header = "{COLUMN_NAME}"
    {COLUMN_NAME}.editor.anchor = 95%
    {COLUMN_NAME}.editor.fieldLabel = "{COLUMN_NAME}"
    {COLUMN_NAME}.width = {FIELD_WIDTH}
    {COLUMN_NAME}.align = "{COLUMN_ALIGN}"
    ';

    protected function _renderTemplate($data, $template)
    {
        $from_array = array();
        $to_array = array();

        foreach ($data as $k => $v) {
            if ($v) {
                $from_array[] = '{' . $k . '}';
                $to_array[] = $v;
            }
        }

        return str_replace($from_array, $to_array, $template);
    }

    protected function _generateRelationString($data)
    {
        $data['remoteTableDescription'] = $this->_getRemoteTableDescField($data['referenced_table_name']);
        return $this->_renderTemplate($data, $this->_relationTemplate);
    }

    protected function _getRemoteTableDescField($tableName)
    {
        $metadata = $this->_db->describeTable($tableName);
        $fields = array_keys($metadata);
        foreach ($fields as $field) {
            if ($field == "Descripcion")
                return $field;
            if ($field == "Nombre")
                return $field;
        }
        return $field[1];
    }

    protected function _generateFieldString($data)
    {
        $data['FIELD_WIDTH'] = $this->_fieldWidthByType[$data['DATA_TYPE']];
        $data['COLUMN_ALIGN'] = $this->_fieldAlignByType[$data['DATA_TYPE']];

        return $this->_renderTemplate($data, $this->_fieldTemplate);
    }

    protected function _obtainTableMetadata($tableName)
    {
        //$table      = new Zend_Db_Table($tableName);
        //$metadata = $table->info();
        $db = $this->_db;

        //Obtenemos el nombre de la base y verificamos que sea MySQL para obtener las relaciones
        if (is_subclass_of($db, 'Zend_Db_Adapter_Mysqli') || is_subclass_of($db, 'Zend_Db_Adapter_Pdo_Mysql')) {
            $config = $db->getConfig();
            $dbname = $config['dbname'];
            $this->_reference = $db->fetchAll("
            SELECT c.table_schema,u.table_name,u.column_name,u.referenced_column_name
            FROM information_schema.table_constraints AS c
                     INNER JOIN information_schema.key_column_usage AS u
                     USING( constraint_schema, constraint_name )
                     WHERE c.constraint_type = 'FOREIGN KEY'
                     AND u.referenced_table_schema='$dbname'
                     AND u.referenced_table_name = '$tableName'
                     ORDER BY c.table_schema,u.table_name
            ");
            $this->_fk = $db->fetchAll("
            SELECT c.table_schema,u.referenced_table_name,u.column_name,u.referenced_column_name
            FROM information_schema.table_constraints AS c
                     INNER JOIN information_schema.key_column_usage AS u
                     USING( constraint_schema, constraint_name )
                     WHERE c.constraint_type = 'FOREIGN KEY'
                     AND u.referenced_table_schema='$dbname'
                     AND u.table_name = '$tableName'
                     ORDER BY c.table_schema,u.table_name
            ");
        }

        $this->_tableMetadata = $db->describeTable($tableName);
    }

    public function __construct()
    {
        $this->_db = Zend_Registry::get('db');
        $this->_path = APPLICATION_PATH . "/models/DbTable/";
    }

    public function generateModel($tableName)
    {
        //Obtenemos los metadatos de la tabla
        if (!$this->_tableMetadata) {
            $this->_obtainTableMetadata($tableName);
        }

        if ($this->modelExists($tableName)) {
            rename($this->_path . "$tableName.php", $this->_path . "$tableName.php.old");
        }
        //Armamos las referencias
        $foreign = array();
        $dependent = array();

        foreach ($this->_fk as $reference) {
            $foreign[] = $this->_generateRelationString($reference);
        }

        foreach ($this->_reference as $reference) {
            $dependent[] = "'Model_DbTable_{$reference['table_name']}'";
        }

        Zend_Loader::loadClass('Zend_View');

        $view = new Zend_View();
        $view->setScriptPath(APPLICATION_PATH . "/modules/Develop/views/scripts/");

        $view->tableName = $tableName;
        $view->references = implode(',', $foreign);
        $view->dependentTables = implode(',', $dependent);

        //$status = file_put_contents(APPLICATION_PATH."/modules/default/models/DbTable/$tableName.php" ,$view->render('model.phtml'));

        $status = file_put_contents($this->_path . "$tableName.php", "<?php\n" . $view->render('model.phtml'));
        chmod($this->_path . "$tableName.php", 0777);
        if ($status === false) {
            throw new Develop_Model_ModelFromTable_Exception("Error al escribir el archivo $tableName.php");
        }
    }

    public function modelExists($tableName)
    {
        return file_exists($this->_path . "$tableName.php");
    }

    public function iniExists($tableName)
    {
        return file_exists($this->_path . "$tableName.ini");
    }

    public function generateIni($tableName)
    {
        //Obtenemos los metadatos de la tabla
        if (!$this->_tableMetadata) {
            $this->_obtainTableMetadata($tableName);
        }

        if ($this->iniExists($tableName)) {
            rename($this->_path . "$tableName.ini", $this->_path . "$tableName.ini.old");
        }

        //Armamos las referencias
        $fields = "";
        foreach ($this->_tableMetadata as $field) {
            $fields .= $this->_generateFieldString($field);
        }

        Zend_Loader::loadClass('Zend_View');

        $view = new Zend_View();
        $view->setScriptPath(APPLICATION_PATH . "/modules/Develop/views/scripts/");

        $view->tableName = $tableName;
        $view->fields = $fields;

        //$status = file_put_contents(APPLICATION_PATH."/modules/default/models/DbTable/$tableName.php" ,$view->render('model.phtml'));
        $status = file_put_contents($this->_path . "$tableName.ini", $view->render('ini.phtml'));

        chmod($this->_path . "$tableName.ini", 0777);

        if ($status === false) {
            throw new Develop_Model_ModelFromTable_Exception("Error al escribir el archivo $tableName.php");
        }
    }

}