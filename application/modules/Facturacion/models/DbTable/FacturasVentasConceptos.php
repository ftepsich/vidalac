<?php
class Facturacion_Model_DbTable_FacturasVentasConceptos extends Facturacion_Model_DbTable_ComprobantesImpositivos
{

    public function init() {
        $this->_referenceMap['ConceptosImpositivos']['comboSource'] = 'datagateway/combolist/fetch/ParaVentas';
        $this ->_validators = array(
            'ConceptoImpositivoPorcentaje'=> array(
                array( 'GreaterThan',0),
                'messages' => array('La cantidad no puede ser 0 (cero)')
            ),
            'Monto'=> array(
                array('GreaterThan',0),
                'messages' => array('El precio no puede ser menor a 0')
            )
        );
        parent::init();
    }

    public function insert($data)
    {
        $this->_db->beginTransaction();
        try {
            $this->salirSi_estaCerrado($data['ComprobantePadre']);

            // controlo que el concepto no este asignado ya
            $this->salirSi_estaElConceptoAsignado($data['ComprobantePadre'], $data['ConceptoImpositivo']);

            $id = parent::insert($data);

            $this->_db->commit();
            return $id;
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }


}
