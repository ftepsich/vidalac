<?php
class Rrhh_Model_DbTable_PersonasAfiliaciones extends Rad_Db_Table
{
    protected $_name = 'PersonasAfiliaciones';

    protected $_referenceMap    = array(

	    'Personas' => array(
            'columns'           => 'Persona',
            'refTableClass'     => 'Base_Model_DbTable_Empleados',
            'refJoinColumns'    => array('RazonSocial'),
            'comboBox'			=> true,
            'comboSource'		=> 'datagateway/combolist',
            'refTable'			=> 'Personas',
            'refColumns'        => 'Id',
            'comboPageSize'     => 20
        ),
	    'Organismos' => array(
            'columns'           => 'Organismo',
            'refTableClass'     => 'Rrhh_Model_DbTable_Organismos',
            'refJoinColumns'    => array('Descripcion','TipoDeOrganismo'),
            'comboBox'			=> true,
            'comboSource'		=> 'datagateway/combolist/fetch/Activo',
            'refTable'			=> 'Organismos',
            'refColumns'        => 'Id',
            'comboPageSize'     => 20
        )    );

    protected $_dependentTables = array('Rrhh_Model_DbTable_PersonasAfiliacionesAdherentes');

    /**
     * Validadores
     *
     * FechaBaja    -> mayor a fecha alta
     *
     */
    protected $_validators = array(
        'FechaBaja'=> array(
            array( 'GreaterThan',
                    '{FechaAlta}'
            ),
            'messages' => array('La fecha de baja no puede ser menor e igual que la fecha de alta.')
        )
    );


    /**
     * Devuelve verdadero o falso si la persona esta o no afiliado a una Obra social
     *
     * @param row       $servicio     Servicio a liquidar
     * @param object    $periodo      periodo a liquidar
     * @return boolean
    */
    public function tieneObraSocial($servicio, $periodo)
    {
        $tipoOrganismo = 1;
        return $this->esAfiliado($servicio, $periodo, $tipoOrganismo);
    }

    /**
     * Devuelve verdadero o falso si la persona esta o no afiliado a un Sindicato
     *
     * @param row       $servicio     Servicio a liquidar
     * @param object    $periodo      periodo a liquidar
     * @return boolean
    */
    public function tieneSindicato($servicio, $periodo)
    {
        $tipoOrganismo = 2;
        return $this->esAfiliado($servicio, $periodo, $tipoOrganismo);
    }

    /**
     * Devuelve verdadero o falso si la persona esta o no afiliado a un organismo especifico o a un tipo de organismo
     *
     * @param row       $servicio       Servicio a liquidar
     * @param object    $periodo        periodo a liquidar
     * @param int       $tipoOrganismo  identificador de TiposDeOrganismos
     * @param int       $organismo      identificador de Organismos
     * @return boolean
    */
    public function esAfiliado($servicio, $periodo, $tipoOrganismo = null, $organismo = null)
    {
        // reviso que venga algun parametro
        if (!$tipoOrganismo && !$organismo) throw new Rad_Db_Table_Exception('No se indico el Organismo o tipo de Organismo para verificar la afiliacion.');

        $where  = ($organismo) ? " Organismo = $organismo " : " Organismo in (SELECT Id FROM Organismos WHERE TipoDeOrganismo = $tipoOrganismo) " ;
        $where  .= " AND Persona = $servicio->Persona";
        $where  .= " AND FechaAlta <= '".$periodo->getHasta()->format('Y-m-d')."'";
        $where  .= " AND ifnull(FechaBaja,'2199-01-01') > '".$periodo->getDesde()->format('Y-m-d')."'";

        //Rad_Log::debug($where);

        $r = $this->fetchRow($where);

        if(count($r)) {
            return  true;
        } else {
            return  false;
        }
    }



}