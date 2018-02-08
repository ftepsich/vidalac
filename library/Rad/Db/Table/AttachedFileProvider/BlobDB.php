<?php
require_once 'Rad/Db/Table/AttachedFileProvider.php';

/**
 * Adaptador de archivos anexados para almacenamiento en base de datos
 *
 * @package Rad
 * @subpackage Db_Table
 * @author Martin A. Santangelo
 */
class Rad_Db_Table_AttachedFileProvider_BlobDB extends Rad_Db_Table_AttachedFileProvider
{
    /**
     * Tabla donde se almacenaran los archivos
     * @var string
     */
    protected $_table;

    /**
     * setea Tabla de almacenamiento
     *
     * @param string $f tabla
     */
    public function setTable($f)
    {
        $this->_table = $f;
    }

    /**
     * Retorna el nombre del archivo anexado
     *
     * @param string           $id      identificador
     * @param string           $model   modelo
     * @param int              $width   Ancho
     * @param int              $height  lto
     * @return string
     */
    protected function _getFileThumbName($id, $model, $width, $height)
    {
        return $model . '-' . $id . '-' .$width. '-' .$height;
    }

    /**
     * retorna el id del archivo anexado
     *
     * @param Rad_Db_Table_Row $row        Instancia del registro
     * @param string           $field      nombre del campo
     * @param string           $extension  extension del archivo
     * @return string
     */
    protected function _getFileId($row, $field, $extension)
    {
        return  $field . '-' .$row->Id . '.' . $extension;
    }

    /**
     * Guarda en un archivo anexado el contenido $string
     *
     * @param Rad_Db_Table_Row $row        Instancia del registro
     * @param string           $field      nombre del campo
     * @param binary           $string     contenido a guardar
     * @param string           $extension  Extension del archivo
     * @return string
     */
    protected function _saveFileFromString($row, $field, $string, $extension)
    {
        $model = $row->getTable();

        $modelRow = $this->_getModelRow($model);
        $archivo  = $this->_getFileRow($row->Id, $modelRow, $field);

        $db = Zend_Registry::get('db');

        // sino lo creamos
        if (!$archivo) {
            $archivo = $archivo->getTable()->createRow();
            $archivo->Modelo   = $modelRow->Id;
            $archivo->IdModelo = $row->Id;
            $archivo->Campo    = $field;
        }

        // guardamos el adjunto
        $archivo->Archivo = $db->quote($string);
        $archivo->save();

        return $archivo->Id;
    }

    protected function _getModelRow($model)
    {
        $models = new Model_DbTable_Modelos;
        $modelRow = $models->fetchRow("Descripcion = '".get_class($model)."'");

        if (!$modelRow) {
            $this->_throw('El modelo '.get_class($model).' no se encuentra en la tabla de Moldelos del sistema');
        }

        return $modelRow;
    }

    protected function _getFileRow($id, $modelRow, $field)
    {
        $archivos = new Model_DbTable_ArchivosAdjuntos;
        // Buscamos si existe
        return $archivos->fetchRow("Modelo = $modelRow->Id AND IdModelo = $id AND Campo = '$field'");
    }


    /**
     * Obtiene un archivo anexado dado el modelo y el identificador $id (valor almacenado en el campo)
     *
     * @param int    $id     Identificador del archivo
     * @param string $model  Modelo
     * @return string
     */
    public function _readFile($id, $model)
    {
        $modelRow = $this->_getModelRow($model);
        $archivo  = $this->_getFileRow($id, $modelRow);

        if ($archivo) return $archivo->Archivo;
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
    protected function _saveFile($row, $field, $file, $extension)
    {
        $model = $row->getTable();

        $modelRow = $this->_getModelRow($model);
        $archivo  = $this->_getFileRow($row->Id, $modelRow, $field);

        $db = Zend_Registry::get('db');

        // sino lo creamos
        if (!$archivo) {
            $archivo = $archivo->getTable()->createRow();
            $archivo->Modelo   = $modelRow->Id;
            $archivo->IdModelo = $row->Id;
            $archivo->Campo    = $field;
        }

        // guardamos el adjunto
        $archivo->Archivo = $db->quote(file_get_contents($file));
        $archivo->save();

        // Borro el archivo temporal subido
        unlink($file);

        return $archivo->Id;
    }


    /**
     * Borra un archivo anexado
     *
     * @param int    $id     Identificador del archivo
     * @param string $model  Modelo
     */
    protected  function _deleteFile($id, $model)
    {
        $archivo = $this->_folder . '/' . $model . '-' .  $id;

        if (file_exists($archivo)) {
            // Borramos el archivo
            if (!unlink($archivo)) {
                $this->_throw("El Archivo $archivo no se pudo borrar");
            }

            // borramos las miniaturas tambien
            if ($gestor = opendir($this->_folder . '/thumbs')) {
                while (false !== ($entrada = readdir($gestor))) {
                    // empieza con el nombre del archivo?
                    if (strpos($entrada, $model . '-' .  $id) === 0) {
                        unlink($this->_folder . '/thumbs/' . $entrada);
                    }
                }
                closedir($gestor);
            }
        }
    }

    /**
     * Obtiene el tamaño de archivo anexado dado el modelo y el identificador $id (valor almacenado en el campo)
     *
     * @param  int    $id     Identificador del archivo
     * @param  string $model  Modelo
     *
     * @return int    Retorna el tamaño del archivo
     */
    protected function _getSize($id, $model)
    {
        $archivo = $this->_folder . '/' . $model . '-' .  $id;

        $this->_isReadable($archivo);

        return filesize($archivo);
    }

    /**
     * Envia al cliente para descarga con las cabeceras correspondientes
     *
     * @param  int    $id     Identificador del archivo
     * @param  string $model  Modelo
     */
    protected function _sendFile($id, $model)
    {
        $archivo = $this->_folder . '/' . $model . '-' .  $id;

        $this->_isReadable($archivo);

        $extension = end(explode('.', $id));

        $this->_sendMimeHeader($extension);

        ob_clean();
        // flush();
        readfile($archivo);
        ob_end_flush();
    }

    /**
     * Levanta una excepcion si el archivo no existe o no es legible
     *
     * @param string $file Archivo
     */
    protected function _isReadable($file)
    {
        if (!file_exists($file)) {
            $this->_throw("El Archivo $archivo no existe");
        }

        if (!Zend_Loader::isReadable($file)) {
            $this->_throw('El sistema no tiene permiso para leer el archivo '.$archivo);
        }
    }

    /**
     * Obtener una miniatura
     *
     * En caso de ser imagenes deberia retorna una miniatura de la misma o de lo
     * contrario un icono representando el formato del archivo
     *
     *
     * @param string           $id     Identificador del archivo
     * @param string           $model  Modelo
     * @param int              $width  Ancho
     * @param int              $height Alto
     * @return string
     */
    public function getThumbnail($id, $model, $width, $height)
    {
        $file = $this->_generateThumnail($id, $model, $width, $height);

        return file_get_contents($file);
    }

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
     * @param int              $width  Ancho
     * @param int              $height Alto
     * @return string
     */
    public function sendThumbnail($id, $model, $width, $height)
    {
        $file = $this->_generateThumnail($id, $model, $width, $height);
        ob_clean();
        header("Content-Type: image/jpeg");
        readfile($file);
        ob_end_flush();
        exit;
    }

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
     * @param int              $width  Ancho
     * @param int              $height Alto
     *
     * @return string          Nombre del Archivo de la miniatura
     */
    protected function _generateThumnail($id, $model, $width, $height)
    {
        $name = $this->_getFileThumbName($id, $model, $width, $height);

        $archivo = $this->_folder . '/thumbs/' . $name. '.jpg';

        if (!file_exists($archivo)){
            $original = $this->_folder . '/' . $model . '-' .  $id;

            $imagine = new Imagine\Gd\Imagine();

            $mode    = Imagine\Image\ImageInterface::THUMBNAIL_INSET;
            $size    = new Imagine\Image\Box($width, $height);

            $imagine->open($original)
                ->thumbnail($size, $mode)
                ->save($archivo);
        }

        return $archivo;
    }
}