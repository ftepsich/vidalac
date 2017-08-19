<?php
require_once 'Rad/Db/Table.php';
/**
 * Contable_Model_DbTable_LibrosIVA
 *
 * Libros de Iva
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Contable
 * @class Contable_Model_DbTable_LibrosIVA
 * @extends Rad_Db_Table
 */
class Contable_Model_DbTable_LibrosIVA extends Rad_Db_Table
{
    protected $_name = 'LibrosIVA';

    protected $_referenceMap = array(
        'Meses' => array(
            'columns'           => 'Mes',
            'refTableClass'     => 'Base_Model_DbTable_Meses',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'Meses',
            'refColumns'        => 'Id'
        )
    );

    /**
     * Validadores
     *
     * Mes      -> valor unico con el anio
     *
     */
    public function init()     {
        $this ->_validators = array(
            'Mes'=> array(
                array(
                    'Db_NoRecordExists',
                    'LibrosIVA',
                    'Mes',
                    'Anio = {Anio} AND Id <> {Id}'
                ),
                'messages' => array('El Libro de IVA de ese Mes ya existe.')
            )
        );
        parent::init();
    }

    /**
     *  Insert
     *
     * @param array $data   Valores que se insertaran
     */
    public function insert($data)
    {
        // Armo la descripcion del Libro de IVA dependiendo del mes y año
        $data['Descripcion']= $data['Anio'].'-'.str_pad($data['Mes'],2, "0", STR_PAD_LEFT);

        // inserto
        return parent::insert($data);
    }

    public function update($data, $where)
    {
        $this->_db->beginTransaction();
        try {
            $reg = $this->fetchAll($where);

            foreach ($reg as $row){
                $mes = ($data['Mes']) ? $data['Mes'] : $row['Mes'];
                $anio = ($data['Anio']) ? $data['Anio'] : $row['Anio'];

                $data['Descripcion'] = $anio.'-'.str_pad($mes, 2, '0', STR_PAD_LEFT);

                parent::update($data, $where);
            }

            $this->_db->commit();
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }

    }

    /**
     * Selecciona el libro de iva al cual debe asignarse un comprobante
     *
     * @param string $fechaEmision      fecha de emision del comprobante
     *
     * @return int
     */
    public function crearLibroIVA ($mes, $anio)
    {
        $R_LIVA         = $this->fetchRow('1=1', array('Anio desc','Mes desc'));
        $ultimaFecha    = $R_LIVA->Anio . '-' . str_pad($R_LIVA->Mes, 2, '0', STR_PAD_LEFT) . '-01';
        $nuevaFecha     = new DateTime($anio . '-' . str_pad($mes, 2, '0', STR_PAD_LEFT) . '-01');

        $date           = new DateTime($ultimaFecha);

        $date->modify('+1 month');

        while ($date <= $nuevaFecha) {
            $mes        = $date->format('m');
            $anio       = $date->format('Y');
            $Renglon    = array(
                'Descripcion'   => $anio . '-' . str_pad($mes, 2, '0', STR_PAD_LEFT),
                'Mes'           => str_pad($mes, 2, '0', STR_PAD_LEFT),
                'Anio'          => $anio,
                'Cerrado'       => 0
            );
            $id         = $this->insert($Renglon);

            $date->modify('+1 month');
        }

        return $id;
    }

    /**
     * Retorna el contienido de un libro de iva con el formato pedido por AFIP
     *
     * @param int $idLibro      identificador del Libro de IVA
     * @param int $tipoDeLibro  1: Compra 2: Venta
     *
     * @return array
     */
    public function exportadorAFIP ($idLibro, $tipoDeLibro) {

        switch ($tipoDeLibro) {
            case 1:
                $sql = "call AFIP_exportador_LibroIVA_Compra($idLibro, $tipoDeLibro)";
                break;
            case 2:
                $sql = "call AFIP_exportador_LibroIVA_Venta($idLibro, $tipoDeLibro)";
                break;
            default:
                throw new Rad_Db_Table_Exception('Error. Tipo de libro inexistente.');
                break;
        }
        $R = $this->_db->fetchAll($sql);
        if (!count($R)) {
            throw new Rad_Db_Table_Exception('No se encuentra el libro de IVA a exportar o no tiene registros.');
        }
        return $R;
    }
    
    /*
	Aca iria el metodo . Lo que hice fue una readaptacion de lo de alicuotas, como para tener una referencia
    public function siagerRetencionPercepcion($idLibro,$idTipoDeLibro){
        switch ($idTipoDeLibro) {
            case 1:
                $sql = "call SP_SiagerPercepcion($idLibro, $idTipoDeLibro)";
                break;
            case 2:
                $sql = "call SP_SiagerRetencion($idLibro, $idTipoDeLibro)";
                break;
            throw new Rad_Db_Table_Exception('No existe el libro seleccionado.');
                break;
        }
        $reporte = $this->_db->fetchAll($sql);
        return $reporte;
    }
*/
    /**
     * Retorna el contienido de un libro de iva con el formato pedido por AFIP
     * ---------
     * @param int $idLibro      identificador del Libro de IVA
     * @param int $tipoDeLibro  1: Compra 2: Venta
     * @param int $parte        1: Cabecera 2: Detalle
     * @param int $forma        0: ancho fijo, 1: separado por como (0 es la que se debe mandar)
     *
     * @return array
     */
    public function exportadorAFIPres3685 ($idLibro, $tipoDeLibro, $parte, $forma) {

        $txtTipo    = ($tipoDeLibro == 1) ? "Compra" : "Venta";
        $txtParte   = ($parte == 1) ? "" : "_Detalle";
        $sql        = "call AFIP_exportador_LibroIVA_".$txtTipo."_res3685".$txtParte."($idLibro, $forma)";

        $R = $this->_db->fetchAll($sql);
        if (!count($R)) throw new Rad_Db_Table_Exception('No se encuentra el libro de IVA a exportar o no tiene registros.');
        return $R;
    }

    /**
     * Verifica si el libro de IVA esta cerrado
     *
     * @param int $idLibro      identificador del comprobante a verificar
     *
     * @return boolean
     */
    public function estaCerrado ($idLibro)
    {
        $R = $this->find($idLibro)->current();
        if (!$R) throw new Rad_Db_Table_Exception('No se encuentra el libro de IVA.');
        return ($R->Cerrado) ? true : false;
    }

    /**
     * Sale si el Libro esta cerrado
     *
     * @param int $idLibro  identificador del libro a verificar
     *
     */
    public function salirSi_estaCerrado ($idLibro)
    {
        if ($this->estaCerrado($idLibro)) {
            throw new Rad_Db_Table_Exception('El comprobante se encuentra registrado en un libro de iva '.
                'cerrado o intenta asignarse a un libro de iva cerrado y no puede modificarse.');
        }
        return $this;
    }


    public function fetchAbiertos ($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = 'Cerrado = 0';
        $where = $this->_addCondition($where, $condicion);
        return parent:: fetchAll ($where , $order , $count , $offset );
    }
}
