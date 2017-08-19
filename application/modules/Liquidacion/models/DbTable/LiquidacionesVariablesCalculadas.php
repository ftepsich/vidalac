<?php
class Liquidacion_Model_DbTable_LiquidacionesVariablesCalculadas extends Rad_Db_Table
{
    protected $_name            = 'LiquidacionesVariablesCalculadas';
    protected $_sort            = array('Nombre asc');

    protected $_referenceMap    = array(
        
	    'LiquidacionesRecibos' => array(
            'columns'           => 'LiquidacionRecibo',
            'refTableClass'     => 'Liquidacion_Model_DbTable_LiquidacionesRecibos',
            'refJoinColumns'    => array('Id'),
            'comboBox'			=> true,
            'comboSource'		=> 'datagateway/combolist',
            'refTable'			=> 'LiquidacionesRecibos',
            'refColumns'        => 'Id',
        ),
	    'VariablesDetalles' => array(
            'columns'           => 'VariableDetalle',
            'refTableClass'     => 'Liquidacion_Model_DbTable_VariablesDetalles_ConceptosLiquidacionesDetalles',
            'refJoinColumns'    => array('Formula'),
            'comboBox'			=> true,
            'comboSource'		=> 'datagateway/combolist',
            'refTable'			=> 'VariablesDtalles',
            'refColumns'        => 'Id',
        )    );

    protected $_dependentTables = array();	

    public function getMontoHsExtra($idRecibo) {

        $sql    = " SELECT  Sum(LVC.Valor) as Monto
                    FROM    LiquidacionesVariablesCalculadas LVC
                    INNER JOIN VariablesDtalles VD on VD.Id = LVC.VariableDetalle
                    WHERE   LVC.LiquidacionRecibo = $idRecibo
                    AND     VD.Variable in (40,84,370,371)";
        $R      = $this->fetchOne($sql);
        if (!$R) return 0;
        return $R;
    }

    public function getMontoPlusHsExtraDiasInhabiles($idRecibo) {

        $R = $R2 = $R50 = $R100 = 0;

        $sql    = " SELECT  LVC.Valor as Monto
                    FROM    LiquidacionesVariablesCalculadas LVC
                    INNER JOIN VariablesDtalles VD on VD.Id = LVC.VariableDetalle
                    WHERE   LVC.LiquidacionRecibo = $idRecibo
                    AND     VD.Variable in (370)";
        $R100   = $this->fetchOne($sql);
        if ($R100)  $R = $R100/2;

        $sql    = " SELECT  LVC.Valor as Monto
                    FROM    LiquidacionesVariablesCalculadas LVC
                    INNER JOIN VariablesDtalles VD on VD.Id = LVC.VariableDetalle
                    WHERE   LVC.LiquidacionRecibo = $idRecibo
                    AND     VD.Variable in (371)";
        $R50    = $this->fetchOne($sql);
        if ($R50) $R2 = $R50/3;

        return $R+$R2;        
    }
}