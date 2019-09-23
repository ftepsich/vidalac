<?php
require_once 'Rad/Db/Table.php';
/**
 * Contable_Model_DbTable_PeriodosLiquidacionSinIVA
 *
 *  Periodos de Liquidación para Comprobantes Sin IVA
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Contable
 * @class Contable_Model_DbTable_PeriodosLiquidacionSinIVA
 * @extends Rad_Db_Table
 */
class Contable_Model_DbTable_PeriodosLiquidacionSinIVA extends Rad_Db_Table
{
    protected $_name = 'PeriodosLiquidacionSinIVA';

    protected $_referenceMap = array(
        'Meses' => array(
            'columns'           => 'Mes',
            'refTableClass'     => 'Base_Model_DbTable_Meses',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'Meses',
            'refColumns'        => 'Id'
        ),
        'Bimestres' => array(
            'columns'           => 'Bimestre',
            'refTableClass'     => 'Base_Model_DbTable_Bimestres',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'Bimestres',
            'refColumns'        => 'Id'
        ),
        'Trimestres' => array(
            'columns'           => 'Trimestre',
            'refTableClass'     => 'Base_Model_DbTable_Trimestres',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'Trimestres',
            'refColumns'        => 'Id'
        )
    );

    /**
     *  Insert
     *
     * @param array $data 
     */
    public function insert($data)
    {
        // Armo la descripcion del Periodo de Liquidacion dependiendo del mes, bimestre, trimestre y año
        if ($data['Mes'] != 0) {
           $data['Descripcion']= $data['Anio'].'-'.str_pad($data['Mes'],2, "0", STR_PAD_LEFT);
           $data['Bimestre']  = 0;
           $data['Trimestre'] = 0;
        } elseif ($data['Bimestre'] != 0) {
           $data['Descripcion']= $data['Anio'].'-B'.$data['Bimestre'];
           $data['Trimestre'] = 0;
        } elseif ($data['Trimestre'] != 0) {
           $data['Descripcion']= $data['Anio'].'-T'.$data['Trimestre'];
        } else {
           $data['Descripcion']= $data['Anio'].'-'.str_pad($data['Mes'],2, "0", STR_PAD_LEFT);
        }

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
                $bimestre= ($data['Bimestre']) ? $data['Bimestre'] : $row['Bimestre'];
                $trimestre= ($data['Trimestre']) ? $data['Trimestre'] : $row['Trimestre'];
                $anio = ($data['Anio']) ? $data['Anio'] : $row['Anio'];
                if ($mes != 0) {
                   $data['Descripcion'] = $anio.'-'.str_pad($mes,2, "0", STR_PAD_LEFT);
                   $data['Bimestre']  = 0;
                   $data['Trimestre'] = 0;
                } elseif ($bimestre != 0) {
                   $data['Descripcion'] = $anio.'-B'.$bimestre;
                   $data['Trimestre'] = 0;
                } elseif ($trimestre!= 0) {
                   $data['Descripcion'] = $anio.'-T'.$trimestre;
                } else {
                   $data['Descripcion'] = $anio.'-'.str_pad($mes,2, "0", STR_PAD_LEFT);
                }
                parent::update($data, $where);
            }
            $this->_db->commit();
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }

    }

}
