<?php
require_once 'Rad/Db/Table.php';

/**
 * Ubicaciones
 *
 * @package     Aplicacion
 * @subpackage  Almacenes
 * @class       Almacenes_Model_DbTable_Ubicaciones
 * @extends     Rad_Db_Table
 */
class Almacenes_Model_DbTable_Ubicaciones extends Rad_Db_Table
{
    protected $_name = "Ubicaciones";

    /**
     * Validadores
     *
     * Almacen        -> no vacio
     *
     */
    protected $_validators = array(
        'Almacen' => array(
            'NotEmpty',
            'allowEmpty'=>false,
            'messages' => array('No se asocio correctamente el almacen a la ubicacion correspondiente.')
        )
    );

    protected $_referenceMap = array(
        'Almacenes' => array(
            'columns' => 'Almacen',
            'refTableClass' => 'Almacenes_Model_DbTable_Almacenes',
            'refJoinColumns' => array("Descripcion"),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'Almacenes',
            'refColumns' => 'Id'
        ),
    );

    protected function ifNull($dato,$reemplazar) {
        if ($dato !== null) return $dato;
        else return $reemplazar;
    }

    public function getUbicacionesAlmacen($almacen)
    {
        $ubicaciones = $this->fetchAll("Almacen = $almacen", array('Fila asc','Profundidad asc','Altura asc'));
        $ret  = array();
        foreach ($ubicaciones as $ubic) {
            $ret[$ubic->Fila][$ubic->Profundidad][$ubic->Altura] = $ubic->Descripcion;
        }
        return $ret;
    }

    public function crearUbicaciones ($regAlmacen) {

        $R_A = $regAlmacen;

        $stmt = $this->_db->prepare('INSERT INTO Ubicaciones (Almacen, Fila, Profundidad, Altura, Descripcion) VALUES (?, ?, ?, ?, ?)');

        $existentes = $this->getUbicacionesAlmacen($R_A->Id);

        for (       $fila           = 1;   $fila           <= $R_A->RackCantFila;          $fila++)
            for (   $profundidad    = 1;   $profundidad    <= $R_A->RackCantProfundidad;   $profundidad++)
                for ($altura        = 1;   $altura         <= $R_A->RackCantAltura;        $altura++) {
                    if (!isset($existentes[$fila][$profundidad][$altura])) {
                        Rad_Log::debug('creando '.$fila.'-'.$profundidad.'-'.$altura);
                        $data = array(
                            $R_A->Id,
                            $fila,
                            $profundidad,
                            $altura,
                            $this->_describirUbicacion($R_A,$fila,$profundidad,$altura)
                        );
                        //Rad_Log::debug($data);
                        $stmt->execute($data);
                    } else {
                        Rad_Log::debug('ignorando '.$fila.'-'.$profundidad.'-'.$altura);
                    }
                }
    }

    protected function _describirUbicacion ($regAlmacen,$fila,$prof,$altura) {

        $R_A = $regAlmacen;

        $DescFila           = $this->ifNull($R_A->DescFila,'');
        $DescAltura         = $this->ifNull($R_A->DescAltura,'');
        $DescProfundidad    = $this->ifNull($R_A->DescProfundidad,'');
        $separador          = $this->ifNull($R_A->Separador,'');

        // TODO: Como no esta el al cargador de almacenes en el jason la opcion OcultarDescSiUno
        // hago que nunca muestre la dimencion si tiene solo uno

        if (!$R_A->OcultarDescSiUno) {
            // Fila
            if ($R_A->RackCantFila > 1) {
                $inc    = $this->_describirUbicacionParcial($R_A->IncrementoFila,$R_A->CompletaCerosFila,$fila);
                $d      = $DescFila . $inc;
                $sep    = $separador;
            }
            // Altura
            if ($R_A->RackCantAltura > 1) {
                $inc    = $this->_describirUbicacionParcial($R_A->IncrementoAltura,$R_A->CompletaCerosAltura,$altura);
                $d      = $d . $sep . $DescAltura . $inc;
                $sep    = $separador;
            }
            // Profundidad
            if ($R_A->RackCantProfundidad > 1) {
                $inc    = $this->_describirUbicacionParcial($R_A->IncrementoProfundidad,$R_A->CompletaCerosProfundidad,$prof);
                $d      = $d . $sep . $DescProfundidad . $inc;
            }
        } else {
            // Fila
            $inc    = $this->_describirUbicacionParcial($R_A->IncrementoFila,$R_A->CompletaCerosFila,$fila);
            $d      = $DescFila . $inc;
            // Altura
            $inc    = $this->_describirUbicacionParcial($R_A->IncrementoAltura,$R_A->CompletaCerosAltura,$altura);
            $d      = $d . $sep . $DescAltura . $inc;
            // Profundidad
            $inc    = $this->_describirUbicacionParcial($R_A->IncrementoProfundidad,$R_A->CompletaCerosProfundidad,$prof);
            $d      = $d . $sep . $DescProfundidad . $inc;
        }
        return $d;
    }

    protected function _describirUbicacionParcial ($tipoIncremento,$CompletaCeros,$dimension) {
        if ($tipoIncremento == 1) {
            // Numerico
            if ($CompletaCeros == 1) {
                $inc = str_pad($dimension,2,"0",STR_PAD_LEFT);
            } else {
                $inc = $dimension;
            }
        } else {
            // Caracter
            $inc = chr($dimension + 64);
        }
        return $inc;
    }

}