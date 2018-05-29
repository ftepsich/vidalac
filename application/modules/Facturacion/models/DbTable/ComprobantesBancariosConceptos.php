<?php
class Facturacion_Model_DbTable_ComprobantesBancariosConceptos extends Facturacion_Model_DbTable_FacturasComprasConceptos
{
    public function init()
    {
        $this->_referenceMap['ConceptosImpositivos']['comboSource'] = 'datagateway/combolist/fetch/ParaCompras';
        parent::init();
    }

}
