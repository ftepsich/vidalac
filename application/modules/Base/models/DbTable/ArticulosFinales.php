<?php

class Base_Model_DbTable_ArticulosFinales extends Base_Model_DbTable_ArticulosGenericos
{
    protected $_calculatedFields = array(
        'Stock' => "CASE Articulos.TipoDeControlDeStock WHEN 1 THEN fStockArticuloEsInsumo(Articulos.Id) WHEN 2 THEN fStockArticuloFechaXCantidad(Articulos.Id, now()) END"
    );

    protected $_permanentValues = array(
        'Tipo'           => 1,
        'EsMateriaPrima' => 0,
        'EsFinal'        => 1
    );

    public function insert($data)
    {
        $data['EsFinal'] = 1;
        $data['Tipo'] = 1;

        return parent::insert($data);
    }
}
