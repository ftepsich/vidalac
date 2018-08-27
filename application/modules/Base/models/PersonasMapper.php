<?php

class Base_Model_PersonasMapper extends Rad_Mapper
{
    protected $_class = 'Base_Model_DbTable_Personas';

    public function getBloqueado($id)
    {
        $row = $this->_model->fetchRow("Id = $id");
        if ($row) {
            return $row->Bloqueado;
        }
        return 1;
    }

}