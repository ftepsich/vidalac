<?php
require_once 'Rad/Db/Table.php';

/**
 * Base_Model_DbTable_Personas
 * Personas
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Base
 * @author Martin Alejandro Santangelo
 * @class Base_Model_DbTable_Personas
 * @extends Rad_Db_Table
 */
class Base_Model_DbTable_Personas extends Rad_Db_Table_SemiReferencial
{
    protected $_name = 'Personas';
    protected $_defaultSource = self::DEFAULT_CLASS;
    protected $_sort = array ('RazonSocial ASC');

    protected $_validators = array(
        'NroInscripcionIB' => array('Digits'),
        'Cuit' => array(
            array('Regex', '(\d{2}-\d{8}-\d{1})'),
            array(
                'Db_NoRecordExists',
                'Personas',
                'Cuit',
                "Id <> {Id} AND Cuit = '{Cuit}'"
            ),
            'messages' => array('Formato de Cuit Incorrecto',
                                'Ya existe ese Cuit.'
            ),
        ),
        'AntiguedadReconocida' => array(
            array('Regex', '(\d{4}-\d{2}-\d{2})'),
            'messages' => array('Formato de fecha Incorrecto')
        )
    );

    protected $_referenceMap = array(
       'ModalidadesIVA' => array(
           'columns'            => 'ModalidadIva',
           'refTableClass'      => 'Base_Model_DbTable_ModalidadesIVA',
           'refJoinColumns'     => array('Descripcion'),
           'comboBox'           => true,
           'comboSource'        => 'datagateway/combolist',
           'refTable'           => 'ModalidadesIVA',
           'refColumns'         => 'Id'
       ),
       'TiposDeInscripcionesGanancias' => array(
           'columns'            => 'ModalidadGanancia',
           'refTableClass'      => 'Base_Model_DbTable_TiposDeInscripcionesGanancias',
           'refJoinColumns'     => array('Descripcion'),
           'comboBox'           => true,
           'comboSource'        => 'datagateway/combolist',
           'refTable'           => 'TiposDeInscripcionesGanancias',
           'refColumns'         => 'Id'
       ),
       'TiposDeInscripcionesIB' => array(
           'columns'            => 'TipoInscripcionIB',
           'refTableClass'      => 'Base_Model_DbTable_TiposDeInscripcionesIB',
           'refJoinColumns'     => array('Descripcion'),
           'comboBox'           => true,
           'comboSource'        => 'datagateway/combolist',
           'refTable'           => 'TiposDeInscripcionesIB',
           'refColumns'         => 'Id'
       ),
        'Sexos' => array(
            'columns'           => 'Sexo',
            'refTableClass'     => 'Base_Model_DbTable_Sexos',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'Sexos',
            'refColumns'        => 'Id'
        )

    );

    protected $_dependentTables = array(
        'Almacenes_Model_DbTable_Lotes',
        'Base_Model_DbTable_AreasDeTrabajosPersonas',
        'Base_Model_DbTable_Cheques',
        'Base_Model_DbTable_CuentasBancarias',
        'Base_Model_DbTable_Direcciones',
        'Base_Model_DbTable_Emails',
        'Base_Model_DbTable_GeneradorDeCheques',
        'Base_Model_DbTable_PersonasActividades',
        'Base_Model_DbTable_PersonasConceptosImpositivos',
        'Base_Model_DbTable_PersonasListasDePrecios',
        'Base_Model_DbTable_PersonasListasDePreciosInformados',
        'Base_Model_DbTable_ProveedoresMarcas',
        'Base_Model_DbTable_Telefonos',
        'Base_Model_DbTable_ZonasPorPersonas',
        'Contable_Model_DbTable_CuentasCorrientes',
        'Contable_Model_DbTable_LibrosIVADetalles',
        'Facturacion_Model_DbTable_Comprobantes',
        'Produccion_Model_DbTable_OrdenesDeProducciones',
        'Facturacion_Model_DbTable_PedidosDeCotizaciones',
        'Produccion_Model_DbTable_LineasDeProduccionesPersonas'
    );


    /**
     * Inserta un registro y lleva la persona del servicio
     *
     * @param array $data
     * @return mixed
     */
    public function insert($data) {

        //throw new Rad_Db_Table_Exception(print_r($data,true));
        $this->_db->beginTransaction();
        try {
            $id = parent::insert($data);

            $this->_db->commit();
            return $id;
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    /**
     *  Update
     *
     * @param array $data   Valores que se cambiaran
     * @param array $where  Registros que se deben modificar
     *
     */
    public function update($data, $where)
    {
        try {
            $this->_db->beginTransaction();
            // no saquen el parent por que sino no anda (sarcasmo! 2014-04-01 18:39)
            parent::update($data,$where);
            //  Ya esta en los validators
           $reg = $this->fetchAll($where);

           foreach ($reg as $row) {
             if($data['Cuit']){
               $condicion = "Cuit = '".$data['Cuit']."' AND Personas.Id <> ".$row->Id;
               $Cuit = $this->fetchRow($condicion);
               if($Cuit) throw new Rad_Db_Table_Exception("Ya existe ese Cuit.");
             }
             parent::update($data,'Personas.Id ='.$row->Id);
           }
            $this->_db->commit();
            return true;
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    public function delete($where)
    {
        try {
            $this->_db->beginTransaction();
            $reg = $this->fetchAll($where);
            foreach ($reg as $R) {
                // Debo ver las tablas que usan personas y dar un mensaje amigable
                if (count($R->findDependentRowset('Base_Model_DbTable_Cheques'))) {
                    throw new Rad_Db_Table_Exception("Operación no valida, la persona tiene Cheques asociados.");
                }
                if (count($R->findDependentRowset('Base_Model_DbTable_CuentasBancarias'))) {
                    throw new Rad_Db_Table_Exception("Operación no valida, la persona tiene Cuentas Bancarias asociadas.");
                }
                parent::delete("Id =" . $R['Id']);
            }
            $this->_db->commit();
            return true;
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    public function getDescripcionEmpresa($id)
    {
        $R_P  = $this->find($id)->current();
        return $R_P->RazonSocial . ' [' . $R_P->Cuit . ']';
    }


    // Estas cuatro funciones se usan al cerrar un comprobante por si no tienen bien
    // seteadas a las personas como Clientes o Proveedores

    /**
     * Verifica si una persona es Proveedor
     * @param  int      $id Identificador de Persona
     * @return boolean
     */
    public static function esProveedor($id) {
        $db   = Zend_Registry::get("db");
        $id   = $db->quote($id , 'INTEGER');
        $sql  = "select EsProveedor From Personas Where Id = $id";
        $S    = $db->fetchRow($sql);
        if ($S['EsProveedor']) return true;
        return false;
    }

    /**
     * Verifica si una persona es Cliente
     * @param  int      $id Identificador de Persona
     * @return boolean
     */
    public static function esCliente($id) {
        $db   = Zend_Registry::get("db");
        $id   = $db->quote($id , 'INTEGER');
        $sql  = "select EsCliente From Personas Where Id = $id";
        $S    = $db->fetchRow($sql);
        if ($S['EsCliente']) return true;
        return false;
    }

    /**
     * Marca una persona como Proveedor
     * @param  int      $id Identificador de Persona
     * @return none
     */
    public static function setProveedor($id) {
        $db   = Zend_Registry::get("db");
        $id   = $db->quote($id , 'INTEGER');
        $S    = $db->update('Personas',array('EsProveedor' => 1),"Id=".$id);
    }

    /**
     * Marca una persona como Cliente
     * @param  int      $id Identificador de Persona
     * @return none
     */
    public static function setCliente($id) {
        $db   = Zend_Registry::get("db");
        $id   = $db->quote($id , 'INTEGER');
        $S    = $db->update('Personas',array('EsCliente' => 1),"Id=".$id);
    }

    public function fetchEsBanco($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = 'Personas.Id in (Select B.Persona from Bancos B)';
        $where = $this->_addCondition($where, $condicion);
        return parent::fetchAll($where, $order, $count, $offset);
    }

    public function fetchEsClienteOEsProveedor($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = 'EsCliente = 1 OR EsProveedor = 1';
        $where = $this->_addCondition($where, $condicion);
        return parent::fetchAll($where, $order, $count, $offset);
    }
}