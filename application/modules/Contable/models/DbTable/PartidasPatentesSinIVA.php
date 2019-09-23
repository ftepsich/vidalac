<?php
require_once 'Rad/Db/Table.php';
/**
 * Contable_Model_DbTable_PartidasPatentesSinIVA
 *
 * Partidas y Patentes para Comprobantes Sin IVA
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Contable
 * @class Contable_Model_DbTable_PartidasPatentesSinIVA
 * @extends Rad_Db_Table
 */
class Contable_Model_DbTable_PartidasPatentesSinIVA extends Rad_Db_Table
{
    protected $_name = 'PartidasPatentesSinIVA';

    public function init()
    {
        $this->_validators = array(
            'Identificador' => array(
                array('Db_NoRecordExists',
                    'PartidasPatentesSinIVA',
                    'Identificador',
                    'Identificador = \'{Identificador}\' AND Id <> {Id}'
                    ),
                'messages' => array('El identificador ya existe.')
            )
        );

        parent::init();
    }

    public function insert($data)
    {
        return parent::insert($data);
    }

    public function update($data, $where)
    {
        $this->_db->beginTransaction();
        try {
            $reg = $this->fetchAll($where);
            foreach ($reg as $row){
                $identificador = ($data['Identificador']) ? $data['Identificador'] : $row['Identificador'];
                $descripcion = ($data['Descripcion']) ? $data['Descripcion'] : $row['Descripcion'];
                parent::update($data, $where);
            }
            $this->_db->commit();
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }

    }

}
