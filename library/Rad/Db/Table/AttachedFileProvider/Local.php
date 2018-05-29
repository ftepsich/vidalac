<?php
require_once 'Rad/Db/Table/AttachedFileProvider.php';

/**
 * Adaptador de archivos anexados para almacenamiento local
 *
 * @package Rad
 * @subpackage Db_Table
 * @author Martin A. Santangelo
 */
class Rad_Db_Table_AttachedFileProvider_Local extends Rad_Db_Table_AttachedFileProvider
{
    /**
     * Carpeta donde se almacenaran los archivos
     * @var string
     */
    protected $_folder;

    /**
     * setea el directorio de almacenamiento
     *
     * @param string $f directorio
     */
    public function setFolder($f)
    {
        $this->_folder = $f;
    }

    /**
     * retorna el path del archivo anexado al campo
     *
     * @param Rad_Db_Table_Row $row    Instancia del registro
     */
    protected function _getFilePath($row, $field)
    {
        // Verifico que exista el registro
        if (!$row) {
            $this->_throw("No se paso el registro del cual intenta obtener un archivo.");
        }

        return $this->_folder. '/' . $row->$field;
    }

    /**
     * retorna el nombre del archivo anexado
     *
     * @param Rad_Db_Table_Row $row     Instancia del registro
     * @param string           $field   Nombre del campo
     * @param string           $model   Modelo
     */
    protected function _getFileName($row, $field, $model, $extension)
    {
        return $model . '-' . $this->_getFileId($row, $field, $extension);
    }

    /**
     * retorna el nombre del archivo anexado
     *
     * @param string           $row     Instancia del registro
     * @param string           $model   modelo
     * @param int              $width   Ancho
     * @param int              $height  lto
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
     */
    protected function _saveFileFromString($row, $field, $string, $extension)
    {
        $anterior = $this->_getFilePath($row, $field);

        // si exite uno previo lo borro
        $this->_deleteFile($row->$field);

        $table = $row->getTable();
        $model = get_class($table);

        $nombre = $this->_getFileName($row, $field, $model, $extension);

        if (!file_put_contents($file, $this->_folder . '/' . $nombre, $string)){
            $this->_throw('No se pudo escribir el archivo anexo');
        }

        return $nombre;
    }

    /**
     * Obtiene un archivo anexado dado el modelo y el identificador $id (valor almacenado en el campo)
     *
     * @param int    $id     Identificador del archivo
     * @param string $model  Modelo
     */
    public function _readFile($id, $model)
    {
        $archivo = $this->_folder . '/' . $model . '-' .  $id;

        $this->_isReadable($archivo);

        return file_get_contents($archivo);
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
        $table = $row->getTable();
        $model = get_class($table);

        // Si hay uno anterior lo borro
        if ($row->$field) {
            $this->_deleteFile($row->$field, $model);
        }

        // Obtengo el nombre del archivo
        $nombre = $this->_getFileName($row, $field, $model, $extension);
        $id     = $this->_getFileId($row, $field, $extension);

        // Intento moverlo
        if (!copy($file, $this->_folder . '/' . $nombre)) {
            $this->_throw('No se pudo mover el archivo anexado');
        }

        unlink($file);

        return $id;
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
     * @param string           $field  nombre del campo
     * @param int              $width  Ancho
     * @param int              $height Alto
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
     * @param string           $field  nombre del campo
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