<?php
class Rrhh_Model_DbTable_LiquidacionesTablasDetalles extends Rad_Db_Table
{
    protected $_name = 'LiquidacionesTablasDetalles';
    protected $_sort = array('InicioRango asc');

    protected $_referenceMap    = array(
            );

    protected $_dependentTables = array();

    /**
     * Validadores
     *
     * FechaHasta    -> mayor a fechadesde
     *
     */
    protected $_validators = array(
        'FinRango'=> array(
            array( 'GreaterThan',
                    '{InicioRango}'
            ),
            'messages' => array('El fin de rango no puede ser menor que el inicio de rango.')
        )
    );
}