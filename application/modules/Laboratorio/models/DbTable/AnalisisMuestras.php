<?php
require_once('Rad/Db/Table.php');
/**
 * Laboratorio_Model_DbTable_AnalisisMuestras
 *
 * Analisis
 *
 *
 * @package     Aplicacion
 * @subpackage  Laboratorio
 * @class       Laboratorio_Model_DbTable_Analisis
 * @extends     Rad_Db_Table
 * @copyright   SmartSoftware Argentina 2010
 */
class Laboratorio_Model_DbTable_AnalisisMuestras extends Rad_Db_Table
{
    protected $_name = "AnalisisMuestras";
    protected $_referenceMap    = array(
        'Lotes' => array(
            'columns'           => 'Lote',
            'refTableClass'     => 'Almacenes_Model_DbTable_Lotes',
            'refJoinColumns'    => array("Numero"),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'Lotes',
            'refColumns'        => 'Id'
        )
    );
    
    public function init()
    {
        
        $this -> _validators = array(
            'Identificacion'=> array(
                array(
                    'Db_NoRecordExists',
                    'AnalisisMuestras', //
                    'Identificacion',
                    'Identificacion = "{Identificacion}"'.
                    ' and Lote = {Lote}'.
                    ' and Id <> {Id}'
                )
            )
        );
        parent::init();
    }
    
    public function insert($data)
    {
        //Zend_Wildfire_Plugin_FirePhp::send($data); // para debug
        try {
            $this->_db->beginTransaction();
            // Generacion de identificador de la muestra
            $row = $this->fetchAll(null, 'Id DESC', 1, 0);
            $number = substr($row->current()->Identificacion, 3, 5);
            $year = substr($row->current()->Identificacion, 9, 2);
            
            if ( (date('y') == $year) && count($row) ) {
                if ( ($number+1) > 99999 ) {
                    $number = '00001';
                } else {
                    $number++;
                }
                $newIdentificacion = 'Mu-'. sprintf('%05d', $number) . '-' . $year;
            } else {
                $newIdentificacion = 'Mu-00001-' . date('y');
            }
            
            $data['Identificacion'] = $newIdentificacion;
                
            $id = parent::insert($data); // inserto lo qeu carge en el modelo y recupero el id
            //Zend_Wildfire_Plugin_FirePhp::send($data);
            //TODO: Ver si se puede implementar usando relaciones en vez del find
            
            /*
            // Modelos para obtener los valores
            $ModeloLotes            = new Model_DbTable_Lotes(array(),false);
            $ModeloProductos        = new Model_DbTable_Productos(array(),false);
            $ModeloAnalisisModelos  = new Model_DbTable_AnalisisModelos(array(),false);
            
            // Registros obtenidos de los modelos
            $RegistroLote       = $ModeloLotes->fetchRow("Id = ".$this->_db->quote($data['Lote']));
            $RegistroProductos  = $ModeloProductos->fetchRow("Id = ".$RegistroLote->Articulo);
            
            if (!$RegistroArticulo->AnalisisTipoModelo) {
                throw new Rad_Db_Table_Exception('Debe asignar los analisis a realizar antes de cargar la muestra.'); 
            }
            
            $RegistroAnalisis   = $ModeloAnalisisModelos->fetchAll("AnalisisTipoModelo = ".$RegistroArticulo->AnalisisTipoModelo);
            
            // Modelos donde voy a insertar tambien los valores
            $AnalisisProtocolo  = new Model_DbTable_AnalisisProtocolo(array(),false);
            
            foreach ($RegistroAnalisis as $ArrayAnalisis) {
                $RenglonArrayAnalisis = array(
                    'Analisis' => $ArrayAnalisis->Analisis,
                    'Muestra' => $id
                );
                $row = $AnalisisProtocolo->createRow($RenglonArrayAnalisis);
                $row->save();
            }
            */
            $this->_db->commit();
            return $id;
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }
    
    /**
    * Retorna un array con los Analisis y sus respectivos valores dado el id de la mmuestra
    * @return array(
    *       'valores' =>array (
    *           'color' => 'rojo'
    *        ),
    *       'campos' => array (
    *           'color' => 3    //Tipo de campo
    *       )
    *   )
    */
    public function getAnalisis($id) 
    {
        $campos = $this->_db->fetchAll("
            SELECT A.Descripcion ,A.TipoDeCampo, A.Id
            FROM  AnalisisMuestras AM
              inner join Lotes L on L.Id = AM.Lote
              inner join Articulos Ar on Ar.Id = L.Articulo
              inner join Productos P on P.Id = Ar.Producto
              inner join AnalisisTiposModelos ATM on ATM.Id = P.AnalisisTipoModelo
              inner join AnalisisModelos AMo on AMo.AnalisisTipoModelo = ATM.Id
              inner join Analisis A on AMo.Analisis = A.Id

            WHERE
                AM.Id = $id
        ");
        
        $valores = $this->_db->fetchPairs("
            SELECT C.Descripcion , AP.Valor
            FROM Analisis C
                 inner join AnalisisProtocolo AP on AP.Analisis = C.Id
            WHERE
                AP.Muestra = $id
        ");
        
        return array('valores' => $valores, 'campos' => $campos);
    }

    public function fetchControlados ($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = "Controlada = 1";
        $where = $this->_addCondition($where, $condicion);
        return parent:: fetchAll ($where , $order , $count , $offset );
    }   

    public function fetchNoControlados ($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = "Controlada = 0";
        $where = $this->_addCondition($where, $condicion);
        return parent:: fetchAll ($where , $order , $count , $offset );
    }   
    
}