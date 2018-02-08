<?php

/**
 * Clase base para Proveedores de archivos anexados a campos de una tabla
 *
 * @package Rad
 * @subpackage Db_Table
 * @author Martin A. Santangelo
 */
abstract class Rad_Db_Table_AttachedFileProvider
{
    /**
     * Obtiene un archivo anexado a un row
     *
     * @param Rad_Db_Table_Row $row    Instancia del registro
     * @param string           $field  nombre del campo
     */
    public function readFile($row, $field)
    {
        $id = $row->$field;

        if (!$id) {
            $this->_throw('Error al obtener el tamaño: el campo no tiene archivo cargado');
        }

        $table = $row->getTable();
        $model = get_class($table);

        Rad_PubSub::publish('Rad_Db_Table_AttachedFileProvider/Read/'.$model, $model, $id);

        return $this->_readFile($id, $model);
    }



    /**
     * Envia al cliente para descarga un archivo anexado a un row
     *
     * @param Rad_Db_Table_Row $row    Instancia del registro
     * @param string           $field  nombre del campo
     */
    public function downloadFile($row, $field)
    {
        $id = $row->$field;

        if (!$id) {
            $this->_throw('Error al descargar archivo: el campo no tiene archivo cargado');
        }

        $table = $row->getTable();
        $model = get_class($table);

        $this->downloadFileFromId($id, $model);
    }

    /**
     * Envia al cliente para descarga con las cabeceras correspondientes
     *
     * @param  int    $id     Identificador del archivo
     * @param  string $model  Modelo
     */
    public function downloadFileFromId($id, $model)
    {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename='.$id);
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . $this->getSizeFromId($id, $model));

        $this->sendFileFromId($id, $model);
    }

    /**
     * Envia al cliente el archivo
     *
     * @param Rad_Db_Table_Row $row    Instancia del registro
     * @param string           $field  nombre del campo
     */
    public function sendFile($row, $field)
    {
        $id = $row->$field;

        if (!$id) {
            $this->_throw('Error al enviar archivo: el campo no tiene archivo cargado');
        }

        $table = $row->getTable();
        $model = get_class($table);

        $this->sendFileFromId($id, $model);
    }

    /**
     * Envia al cliente el archivo
     *
     * @param  int    $id     Identificador del archivo
     * @param  string $model  Modelo
     */
    public function sendFileFromId($id, $model)
    {
        $this->_sendFile($id, $model);
        exit;
    }

    /**
     * Envia al cliente para descarga con las cabeceras correspondientes
     *
     * @param  int    $id     Identificador del archivo
     * @param  string $model  Modelo
     */
    protected abstract function _sendFile($id, $model);

    /**
     * Dispara una excepcion con el mensaje $msg
     *
     * @param string $msg    Mensaje
     */
    protected function _throw($msg)
    {
        throw new Rad_Db_Table_AttachedFileProvider_Exception($msg);
    }

    /**
     * Obtiene un archivo anexado dado el modelo y el identificador $id (valor almacenado en el campo)
     *
     * @param  int    $id     Identificador del archivo
     * @param  string $model  Modelo
     *
     * @return string Retorna el contenido del archivo
     */
    public function readFromId($id, $model)
    {
        return $this->_readFile($id, $model);
    }

    /**
     * Obtiene un archivo anexado a un row
     *
     * @param Rad_Db_Table_Row $row    Instancia del registro
     * @param string           $field  nombre del campo
     */
    public function getSize($row, $field)
    {
        $id = $row->$field;

        if (!$id) {
            $this->_throw('Error al obtener el tamaño: el campo no tiene archivo cargado');
        }

        $table = $row->getTable();
        $model = get_class($table);

        return $this->_readFile($id, $model);
    }

    /**
     * Obtiene el tamaño de archivo anexado dado el modelo y el identificador $id (valor almacenado en el campo)
     *
     * @param  int    $id     Identificador del archivo
     * @param  string $model  Modelo
     *
     * @return int    Retorna el tamaño del archivo
     */
    public function getSizeFromId($id, $model)
    {
        return $this->_getSize($id, $model);
    }

    /**
     * Obtiene el tamaño de archivo anexado dado el modelo y el identificador $id (valor almacenado en el campo)
     *
     * @param  int    $id     Identificador del archivo
     * @param  string $model  Modelo
     *
     * @return int    Retorna el tamaño del archivo
     */
    protected abstract function _getSize($id, $model);

    /**
     * Obtiene un archivo anexado dado el modelo y el identificador $id (valor almacenado en el campo)
     *
     * @param int    $id     Identificador del archivo
     * @param string $model  Modelo
     *
     * @return string Retorna el contenido del archivo
     */
    protected abstract function _readFile($id, $model);

    /**
     * Guarda un archivo anexado
     *
     * @param Rad_Db_Table_Row $row        Instancia del registro
     * @param string           $field      Nombre del campo
     * @param string           $file       Nombre del archivo local
     * @param string           $extension  Extension del archivo
     */
    public function saveFile($row, $field, $file, $extension)
    {
        $extension = strtolower($extension);
        $table     = $row->getTable();
        $model     = get_class($table);

        require_once 'Rad/Db/Table/AttachedFileValidate.php';

        $validate = new Rad_Db_Table_AttachedFileValidate($table);

        // levante una excepcion en caso de no cumplir con la validación
        $validate->isValid($field, $file);

        Rad_PubSub::publish('Rad_Db_Table_AttachedFileProvider/BeforeSave/'.$model, $model);

        $id = $this->_saveFile($row, $field, $file, $extension);

        Rad_PubSub::publish('Rad_Db_Table_AttachedFileProvider/AfterSave/'.$model, $model);

        $table->setFieldAttachedFile($field, $id, $row);
    }

    /**
     * Envia las cabeceras mimes segun la extension
     *
     * @param string  $extension  Extension del archivo
     */
    protected function _sendMimeHeader($extension)
    {
        switch ($extension) {
            case 'png':
                header("Content-Type: image/png");
                break;
            case 'jpg':
                header("Content-Type: image/jpg");
                break;
            case 'gif':
                header("Content-Type: image/gif");
                break;
            case 'bmp':
                header("Content-Type: image/bmp");
                break;
            case 'pdf':
                header("Content-Type: image/pdf");
                break;
            case 'docx':
            case 'doc':
                header("Content-type: application/msword");
                break;
            case "xls":
                header("Content-type: application/vnd.ms-excel");
                break;
        }

    }

    /**
     * Almacena el archivo y retorna su identificador
     *
     * @param  Rad_Db_Table_Row  $row        Clase modelo
     * @param  string            $field      Nombre del campo
     * @param  string            $file       Nombre del archivo local
     * @param string             $extension  Extension del archivo
     * @return string            Retorna el id del archivo guardado
     */
    protected abstract function _saveFile($row, $field, $file, $extension);

    /**
     * Borra un archivo anexado
     *
     * @param Rad_Db_Table_Row $row    Instancia del registro
     * @param string           $field  nombre del campo
     */
    public function deleteFile($row, $field, $updateField = true)
    {
        $id = $row->$field;

        Rad_PubSub::publish('Rad_Db_Table_AttachedFileProvider/BeforeDelete/'.$jsonid->modelo, $jsonid->modelo, $jsonid->id);

        $this->_deleteFile($id, get_class($row->getTable()));

        Rad_PubSub::publish('Rad_Db_Table_AttachedFileProvider/AfterDelete/'.$jsonid->modelo, $jsonid->modelo, $jsonid->id);

        if ($updateField) {
            $row->getTable()->setFieldAttachedFile($field, null, $row);
        }
    }

    /**
     * Borra un archivo anexado
     *
     * @param int    $id     Identificador del archivo
     * @param string $model  Modelo
     */
    protected abstract function _deleteFile($id, $model);

    /**
     * Obtener una miniatura
     *
     * En caso de ser imagenes deberia retorna una miniatura de la misma o de lo
     * contrario un icono representando el formato del archivo
     *
     * Usar cacheo en las implementaciones!
     *
     * @param string           $id     Identificador del archivo
     * @param string           $model  Modelo
     * @param string           $field  nombre del campo
     * @param int              $width  Ancho
     * @param int              $height Alto
     */
    public abstract function getThumbnail($id, $model, $width, $height);

    /**
     * Envia una miniatura al cliente
     *
     * En caso de ser imagenes deberia retorna una miniatura de la misma o de lo
     * contrario un icono representando el formato del archivo
     *
     * Usar cacheo en las implementaciones!
     *
     * @param string           $id     Identificador del archivo
     * @param string           $model  Modelo
     * @param string           $field  nombre del campo
     * @param int              $width  Ancho
     * @param int              $height Alto
     */
    public abstract function sendThumbnail($id, $model, $width, $height);

    /**
     * Guarda en un archivo anexado el contenido $string
     *
     * @todo Agregar validadores como en el saveFile
     * @param Rad_Db_Table_Row $row        Instancia del registro
     * @param string           $field      nombre del campo
     * @param binary           $string     contenido a guardar
     * @param string           $extension  Extension del archivo
     */
    public function saveFileFromString($row, $field, $string, $extension)
    {
        $extension = strtolower($extension);

        $table = $row->getTable();
        $model = get_class($table);

        Rad_PubSub::publish('Rad_Db_Table_AttachedFileProvider/BeforeSave/'.$model, $model);

        $id = $this->_saveFileFromString($row, $field, $file, $extension);

        Rad_PubSub::publish('Rad_Db_Table_AttachedFileProvider/AfterSave/'.$model, $model);

        $table->setFieldAttachedFile($field, $id, $row);
    }

    /**
     * Guarda en un archivo anexado el contenido $string
     *
     * @param Rad_Db_Table_Row $row        Instancia del registro
     * @param string           $field      nombre del campo
     * @param binary           $string     contenido a guardar
     * @param string           $extension  Extension del archivo
     */
    protected abstract function _saveFileFromString($row, $field, $string, $extension);
}