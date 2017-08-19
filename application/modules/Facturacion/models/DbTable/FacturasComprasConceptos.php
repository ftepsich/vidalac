<?php
class Facturacion_Model_DbTable_FacturasComprasConceptos extends Facturacion_Model_DbTable_ComprobantesImpositivos
{
    public function init() 
    {   /*
        $this ->_validators = array(
            'ConceptoImpositivo'=> array(
                array(
                    'Db_NoRecordExists',
                    'Comprobantes',
                    'ConceptoImpositivo',
                    "Numero = '{Numero}' AND Id <> {Id} "
                ),
            'messages' => array('El Numero del Concepto ya existe en la Factura de Compra')
            )
        );
        */
        $this->_referenceMap['ConceptosImpositivos']['comboSource'] = 'datagateway/combolist/fetch/ParaCompras';
        parent::init();
    }

}
