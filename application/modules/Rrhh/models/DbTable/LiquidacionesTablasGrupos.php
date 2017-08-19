<?php
class Rrhh_Model_DbTable_LiquidacionesTablasGrupos extends Rrhh_Model_DbTable_LiquidacionesTablas
{

    protected $_permanentValues = array(
        'TipoDeLiquidacionTabla' => 2
    );

    protected $_defaultValues = array(
        'TipoDeLiquidacionTabla' => 2
    );

    public function init() {
        $this->_validators = array(
            'Grupo' => array(
                'NotEmpty',
                'allowEmpty'=>false,
                'messages' => array('Falta seleccionar una opcion en el Grupo.')
            )
        );

        parent::init();
    }


	/**
     * Insert
     *
     * @param array $data
     * @return mixed
     */
    public function insert($data) {
        try {
            $this->_db->beginTransaction();

            if($data['Grupo']){

                $mliquidacionesTablasDetalles = new Rrhh_Model_DbTable_LiquidacionesTablasGruposDetalles;  
                $id = parent::insert($data);      

                if($data['Grupo'] == 1){
                    $mcategorias = new Rrhh_Model_DbTable_ConveniosCategorias;
                } else {
                    $mcategorias = new Rrhh_Model_DbTable_CategoriasGrupos;              
                }

                $rcategoria = $mcategorias->fetchAll("Convenio = ".$data['Convenio']);

                if($rcategoria){

                    foreach ($rcategoria as $row) {
                        $detalle = array(
                            'LiquidacionTabla'          => $id,
                            'Descripcion'               => $row->Descripcion,
                            'InicioRango'               => $row->Id,
                            'Valor'                     => 0
                        );

                        $mliquidacionesTablasDetalles->insert($detalle);
                    }  

                }  else {
                    throw new Rad_Db_Table_Exception("El convenio no posee un grupo de categoria.");
                }            
                
            } else {
                throw new Rad_Db_Table_Exception("Debe elegir un grupo.");
            }


            $this->_db->commit();

            return $id;
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    /**
     * Borra los registros indicados
     *
     * @param array $where
     *
     */
    public function delete($where)
    {

        try {
            $this->_db->beginTransaction();

            $mLiquidacionTablaDetalle = new Rrhh_Model_DbTable_LiquidacionesTablasGruposDetalles;

            $reg = $this->fetchAll($where);

            if (count($reg)) {
                foreach ($reg as $row) {
                    //deja eliminar el detalle del grupo seleccionado
                    $mLiquidacionTablaDetalle->delete("LiquidacionTabla = $row->Id");

                    parent::delete('Id ='.$row->Id);
                }
            }
            $this->_db->commit();
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }   


    public function fetchAll($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = "LiquidacionesTablas.Grupo is not null ";
        $where = $this->_addCondition($where, $condicion);
        return parent::fetchAll($where, $order, $count, $offset);
    }  
}