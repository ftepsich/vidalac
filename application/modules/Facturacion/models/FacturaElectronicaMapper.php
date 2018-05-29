<?php
require_once 'FactElect/Wsfev1.php';
require_once 'FactElect/Wsfex.php';

class Facturacion_Model_FacturaElectronicaMapper
{
    public function FEXDummy()
    {
        return FactElect_Wsfex::FEXDummy();
    }

    public function FEDummy()
    {
        return FactElect_Wsfev1::FEDummy();
    }
}
