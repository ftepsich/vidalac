<?php

class Base_Model_ChequesPropiosMapper extends Rad_Mapper
{
    protected $_class = 'Base_Model_DbTable_ChequesPropios';
    
    public function marcarNoImpreso ($idCheques)
    {
        return $this->_model->marcarNoImpreso($idCheques);
    }
    
    public function enviarAImpresoraIds($id)
    {
        return $this->_model->enviarAImpresora($id);
    }

    public function enviarAImpresoraRango($chequera, $desde, $hasta)
    {
        // Traigo todos los cheques del rango (los demas controles
        // se hacen desde el modelo)
        if (!chequera)
            throw new Rad_Exception('Debe seleccionar una chequera');
        if ($desde > $hasta)
            throw new Rad_Exception('El cheque de inicio debe ser igual o superior al de final');
        $select = $this->_model->select()
                    ->where('Chequera = ?', $chequera)
                    ->where('Numero >= ?', $desde)
                    ->where('Numero <= ?', $hasta)
                    ->order('Numero ASC');
        $cheques = $this->_model->fetchAll($select)->toArray();

        // Compruebo que esten el primer y ultimo cheque del rango
        if ($cheques[0]['Numero'] !== $desde)
            throw new Rad_Exception('No existe el cheque de inicio numero '.$desde);
        if ($cheques[count($cheques)-1]['Numero'] !== $hasta)
            throw new Rad_Exception('No existe el cheque final numero '.$hasta);

        // Los meto en un array para enviar al modelo
        $idCheques = array();
        foreach ($cheques as $ch)
            $idCheques[] = $ch['Id'];

        return $this->_model->enviarAImpresora($idCheques);
    }
}
