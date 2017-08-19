<?php
/**
 * @package     Aplicacion
 * @subpackage  Facturacion
 * @class       Facturacion_Model_DbTable_ComprobantesDeExportaciones
 * @extends     Rad_Db_Table
 * @copyright   SmartSoftware Argentina
 */
class Facturacion_Model_DbTable_ComprobantesDeExportaciones extends Rad_Db_Table
{
    protected $_name = 'ComprobantesDeExportaciones';

    protected $_referenceMap = array(
        'PaisesCuit' => array(
            'columns'           => 'CuitPaisDestino',
            'refTableClass'     => 'Base_Model_DbTable_PaisesCuit',
            'refJoinColumns'    => array('Pais'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'PaisesCuit',
            'refColumns'        => 'Id',
            'comboPageSize'     => '10'
        ),
        'Paises' => array(
            'columns'           => 'PaisDestino',
            'refTableClass'     => 'Base_Model_DbTable_Paises',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'Paises',
            'refColumns'        => 'Id',
            'comboPageSize'     => '10'
        ),
        'Idiomas' => array(
            'columns'           => 'Idioma',
            'refTableClass'     => 'Base_Model_DbTable_Idiomas',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'Idiomas',
            'refColumns'        => 'Id'
        ),
        'Comprobantes' => array(
            'columns'           => 'Comprobante',
            'refTableClass'     => 'Facturacion_Model_DbTable_Comprobantes',
            'refTable'          => 'Comprobantes',
            'refColumns'        => 'Id',
        ),
        'AfipIncoterms' => array(
            'columns'           => 'Incoterm',
            'refTableClass'     => 'Afip_Model_DbTable_AfipIncoterms',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'AfipIncoterms',
            'refColumns'        => 'Id',
        ),
        'AfipConceptosIncluidos' => array(
            'columns'           => 'ConceptoIncluido',
            'refTableClass'     => 'Afip_Model_DbTable_AfipConceptosIncluidos',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'AfipConceptosIncluidos',
            'refColumns'        => 'Id'
        )
    );

    protected $_dependentTables = array();

    public function init()
    {
        parent::init();
        if ($this->_fetchWithAutoJoins) {
            $j = $this->getJoiner();
            $j->with('PaisesCuit')
                ->joinRef('AfipCuitPaises', array(
                    'Cuit'
                ));
        }
    }


    public function update($data, $where)
    {
        $regEditar = $this->fetchAll($where);

        $modelFV = new Facturacion_Model_DbTable_FacturasVentas;

        foreach($regEditar as $reg){
            $modelFV->salirSi_estaCerrado($reg->Comprobante);
        }

        parent::update($data, $where);
    }
}