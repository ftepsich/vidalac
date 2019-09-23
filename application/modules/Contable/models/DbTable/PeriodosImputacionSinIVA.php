<?php
require_once 'Rad/Db/Table.php';
/**
 * Contable_Model_DbTable_PeriodosImputacionSinIVA
 *
 * Periodos de Imputación para Comprobantes Sin IVA
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Contable
 * @class Contable_Model_DbTable_PeriodosImputacionSinIVA
 * @extends Rad_Db_Table
 */
class Contable_Model_DbTable_PeriodosImputacionSinIVA extends Rad_Db_Table
{
    protected $_name = 'PeriodosImputacionSinIVA';

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
     * Mes -> valor unico con el anio
     *
     */
    public function init()     {
        $this ->_validators = array(
            'Mes'=> array(
                array(
                    'Db_NoRecordExists',
                    'PeriodosImputacionSinIVA',
                    'Mes',
                    'Anio = {Anio} AND Id <> {Id}'
                ),
                'messages' => array('El Periodo de Imputación de ese Mes ya existe.')
            )
        );
        parent::init();
    }

    /**
     *  Insert
     *
     * @param array $data 
     */
    public function insert($data)
    {
        // Armo la descripcion del Periodo de Imputacion dependiendo del mes y año
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
     * Selecciona el Periodo De Imputación al cual debe asignarse un comprobante
     *
     * @param string $fechaEmision      fecha de emision del comprobante
     *
     * @return int
     */
    public function crearPeriodo ($mes, $anio)
    {
        $R_PERIODO      = $this->fetchRow('1=1', array('Anio desc','Mes desc'));
        $ultimaFecha    = $R_PERIODO->Anio . '-' . str_pad($R_PERIODO->Mes, 2, '0', STR_PAD_LEFT) . '-01';
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
            $id = $this->insert($Renglon);

            $date->modify('+1 month');
        }
        return $id;
    }

    /**
     * Verifica si el Periodo de Imputación esta cerrado
     *
     * @param int $idPeriodo      identificador del comprobante a verificar
     *
     * @return boolean
     */
    public function estaCerrado ($idPeriodo)
    {
        $R = $this->find($idPeriodo)->current();
        if (!$R) throw new Rad_Db_Table_Exception('No se encuentra el Periodo de Imputación.');
        return ($R->Cerrado) ? true : false;
    }

    /**
     * Sale si el Periodo de Imputación esta cerrado
     *
     * @param int $idPeriodo  identificador del peridodo a verificar
     *
     */
    public function salirSi_estaCerrado ($idPeriodo)
    {
        if ($this->estaCerrado($idPeriodo)) {
            throw new Rad_Db_Table_Exception('El comprobante se encuentra registrado en un periodo de imputación.'.
                'cerrado o intenta asignarse a un Periodo de Imputación cerrado y no puede modificarse.');
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
