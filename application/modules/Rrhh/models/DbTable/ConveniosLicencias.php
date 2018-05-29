<?php
class Rrhh_Model_DbTable_ConveniosLicencias extends Rad_Db_Table
{
    protected $_name = 'ConveniosLicencias';

    protected $_sort = array('Descripcion asc');

    protected $_referenceMap    = array(

	    'Convenios' => array(
            'columns'           => 'Convenio',
            'refTableClass'     => 'Rrhh_Model_DbTable_Convenios',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'			=> true,
            'comboSource'		=> 'datagateway/combolist',
            'refTable'			=> 'Convenios',
            'refColumns'        => 'Id',
            'comboPageSize'     => 20
        ),
        'SituacionesDeRevistas' => array(
            'columns'           => 'SituacionDeRevista',
            'refTableClass'     => 'Rrhh_Model_DbTable_SituacionesDeRevistas',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist/fetch/Activo',
            'refTable'          => 'SituacionesDeRevistas',
            'refColumns'        => 'Id',
            'comboPageSize'     => 20
        )
        );

    protected $_dependentTables = array('Rrhh_Model_DbTable_ServiciosSituacionesDeRevistas');


    /**
     * Validadores
     *
     * Descripcion	-> valor unico
     *
     */
/*
    protected $_validators = array(
        'Descripcion' => array(
            'NotEmpty',
            array(
                'Db_NoRecordExists',
                'ConveniosLicencias',
                'Descripcion',
                'Convenio = {Convenio} AND Descripcion = {Descripcion} AND Id <> {Id}'
            ),
            'messages' => array(
                'Falta ingresar la descripcion.',
                'El valor que intenta ingresar se encuentra repetido.'
            )
	  )
    );
*/

    public function init() {
        $this->_validators = array(
            'Descripcion' => array(
                array(  'Db_NoRecordExists',
                        'ConveniosLicencias',
                        'Descripcion',
                        'Convenio = {Convenio} AND Descripcion = "{Descripcion}" AND Id <> {Id}'
                ),
                'messages' => array('El valor que intenta ingresar se encuentra repetido.')
            )
        );
        parent::init();
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

            $mServicioSituacionDeRevista = new Rrhh_Model_DbTable_ServiciosSituacionesDeRevistas;

            $reg = $this->fetchAll($where);

            if (count($reg)) {
                foreach ($reg as $row) {
                    //no se permite eliminar la liquidacion tabla si tiene detalle

                    $rServicioSituacionDeRevista = $mServicioSituacionDeRevista->fetchAll("ConvenioLicencia = $row->Id");
                    if($rServicioSituacionDeRevista){
                        throw new Rad_Db_Table_Exception("No se puede eliminar la licencia cuando esta asociada a un servicio.");
                    } else {
                        parent::delete('Id ='.$row->Id);
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
