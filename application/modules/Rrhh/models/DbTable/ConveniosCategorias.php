<?php
class Rrhh_Model_DbTable_ConveniosCategorias extends Rad_Db_Table
{
    protected $_name = 'ConveniosCategorias';

    protected $_referenceMap    = array(

        'Convenios' => array(
            'columns'           => 'Convenio',
            'refTableClass'     => 'Rrhh_Model_DbTable_Convenios',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'Convenios',
            'refColumns'        => 'Id',
            'comboPageSize'     => 20
        ),
        'CategoriasGrupos' => array(
            'columns'           => 'CategoriaGrupo',
            'refTableClass'     => 'Rrhh_Model_DbTable_CategoriasGrupos',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'CategoriasGrupos',
            'refColumns'        => 'Id',
            'comboPageSize'     => 20
        )
    );

    public function init() {
        $this->_validators = array(
            'Descripcion' => array(
                array(  'Db_NoRecordExists',
                        'ConveniosCategorias',
                        'Descripcion',
                        'Convenio = {Convenio} AND Id <> {Id}'
                ),
                'messages' => array('El valor que intenta ingresar se encuentra repetido.')
            )
        );

        parent::init();
        $this->_calculatedFields['ValorActual'] = "fValorActualCategoria(ConveniosCategorias.Id)";
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

            $reg = $this->fetchAll($where);

            if (count($reg)) {
                foreach ($reg as $row) {
                    if($this->categoriaEnUso($row->Id)) throw new Rad_Db_Table_Exception("No se puede eliminar la categoria cuando tiene detalle o relacion con servicios.");
                    parent::delete('Id ='.$row->Id);
                }
            }
            $this->_db->commit();
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    /**
     * Verifica si una categoria se utilizo en alguna tabla
     * @param  int          $idCategoria    Identificador de la Categoria
     * @return boolean
     */
    public function categoriaEnUso($idCategoria) {

        $sql = "    SELECT CCD.Id FROM ConveniosCategoriasDetalles CCD WHERE CCD.ConvenioCategoria = $idCategoria
                        UNION
                    SELECT S.Id FROM Servicios S WHERE S.ConvenioCategoria = $idCategoria
                        UNION
                    SELECT VD.Id FROM VariablesDetalles VD WHERE VD.ConvenioCategoria = $idCategoria";

        $enUso = $this->_db->fetchAll($sql);

        if (count($enUso)) {
            return true;
        } else {
            return false;
        }

    }

}