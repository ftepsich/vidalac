<?php
class Base_Model_DbTable_ArticulosVersionesDetallesFormulas extends Base_Model_DbTable_ArticulosVersionesDetalles
{


    protected $_referenceMap    = array(
        
        'ArticulosVersiones' => array(
            'columns'           => 'ArticuloVersionPadre',
            'refTableClass'     => 'Base_Model_DbTable_ArticulosVersiones',
            //'refJoinColumns'    => array('Descripcion'),
            //'comboBox'          => true,
            //'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'ArticulosVersiones',
            'refColumns'        => 'Id',
        ),
        'ArticulosVersionesHijo' => array(
            'columns'           => 'ArticuloVersionHijo',
            'refTableClass'     => 'Base_Model_DbTable_ArticulosVersiones',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist/fetch/EsProducto',
            'refTable'          => 'ArticulosVersiones',
            'refColumns'        => 'Id',
            'comboPageSize'     => 10
        ),
        'UnidadesDeMedidas' => array(
            'columns'           => 'UnidadDeMedida',
            'refTableClass'     => 'Base_Model_DbTable_UnidadesDeMedidas',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'UnidadesDeMedidas',
            'refColumns'        => 'Id',
        ),
        'TiposDeRelacionesArticulos' => array(
            'columns'           => 'TipoDeRelacionArticulo',
            'refTableClass'     => 'Base_Model_DbTable_TiposDeRelacionesArticulos',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'TiposDeRelacionesArticulos',
            'refColumns'        => 'Id',
        )
    );

    protected $_permanentValues = array(
        'TipoDeRelacionArticulo' => 1
    );

    protected $_defaultValues = array(
        'TipoDeRelacionArticulo' => 1
    );    

    /**
     * Inserta un ArticuloVersion
     * @param array $data Datos
     */
    public function insert($data)
    {
        try 
        {
            $this->_db->beginTransaction();       

            if($data['ArticuloVersionPadre']){
                $ArticulosVersiones = new Base_Model_DbTable_ArticulosVersiones;
                $where= "Id=".$data['ArticuloVersionPadre'];
                $dataAV['TieneFormula'] = 1;
                $ArticulosVersiones->update($dataAV,$where);
            }

            $data['TipoDeRelacionArticulo'] = 1;

            $id = Rad_Db_Table::insert($data);

            $this->getCompletarArticulosVersionesRaices($id,$data['ArticuloVersionPadre']);             

            $this->_db->commit();
            return $id;
        } catch(Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }            
    }


    /**
     * Update
     *
     * @param array $data   Valores que se cambiaran
     * @param array $where  Registros que se deben modificar
     *
     */
    public function update($data, $where)
    {
        $this->_db->beginTransaction();
        try {

            Rad_Db_Table::update($data, $where);
            
            $this->_db->commit();
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

            $ArticulosVersionesDetalles = new Base_Model_DbTable_ArticulosVersionesDetalles;
            $ArticulosVersiones = new Base_Model_DbTable_ArticulosVersiones;
            $reg_avd = $this->fetchAll($where);

            // Si tiene articulos los borro
            if (count($reg_avd)) {
                foreach ($reg_avd as $row) {
                    parent::delete("Id =" . $row['Id']);

                    $reg_avdpadre = $this->fetchAll("ArticuloVersionPadre = ".$row['ArticuloVersionPadre']);
                    if(count($reg_avdpadre)==0){
                        $whereAV= "Id=".$row['ArticuloVersionPadre'];
                        $dataAV['TieneFormula'] = 0;
                        $ArticulosVersiones->update($dataAV,$whereAV);
                    }

                }

            }

            $this->_db->commit();
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }    


}
