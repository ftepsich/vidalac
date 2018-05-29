<?php
/**
 * @package     Aplicacion
 * @subpackage  Liquidacion
 * @class       Liquidacion_Model_DbTable_Descuentos * @extends     Rad_Db_Table
 * @copyright   SmartSoftware Argentina
 */
class Liquidacion_Model_DbTable_Descuentos extends Rad_Db_Table
{
    protected $_name = 'Descuentos';

    protected $_sort = array('Fecha');    

    protected $_referenceMap = array(   
        'Servicios' => array(
            'columns'           => 'Servicio',
            'refTableClass'     => 'Rrhh_Model_DbTable_Servicios',
            'refJoinColumns'    => array('Id'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'Servicios',
            'refColumns'        => 'Id',
            'comboPageSize'     => '10'
        ),
        'Personas' => array(
            'columns'           => 'Persona',
            'refTableClass'     => 'Base_Model_DbTable_Personas',
            'refJoinColumns'    => array('RazonSocial'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'Personas',
            'refColumns'        => 'Id',
            'comboPageSize'     => '10'
        ),
        'DescuentosTipos' => array(
            'columns'           => 'Tipo',
            'refTableClass'     => 'Liquidacion_Model_DbTable_DescuentosTipos',
            'refJoinColumns'    => array('Descripcion'),
            'comboBox'          => true,
            'comboSource'       => 'datagateway/combolist',
            'refTable'          => 'DescuentosTipos',
            'refColumns'        => 'Id',
            'comboPageSize'     => '10'
        )
    );

    protected $_dependentTables = array('Liquidacion_Model_DbTable_DescuentosDetalles');

    public function init()
    {
        parent::init();

        if ($this->_fetchWithAutoJoins) {
            $j = $this->getJoiner();
            $j->with('Servicios')
                ->joinRef('Personas', array(
                    'RazonSocial'
                ))
                ->joinRef('Empresas', array(
                    'Descripcion' => 'TRIM({remote}.Descripcion)'
                ));
        }
    }

    /**
     * Inserta un registro y lleva la persona del servicio
     *
     * @param array $data
     * @return mixed
     */
    public function insert($data) {

        $this->_db->beginTransaction();
        try {

            if (!$data['Numero']) {
                throw new Rad_Db_Table_Exception("Debe ingresar el numero");
            } 

            if (!$data['MontoTotal']) {
                throw new Rad_Db_Table_Exception("Debe ingresar el monto Total");
            }            

            if (!$data['MontoCuota'] && !$data['CantidadCuota'] && !$data['PorcentajeCuota']) {
                throw new Rad_Db_Table_Exception("Debe ingresar el monto de la cuota, porcentaje o cantidad de Cuotas.");
            }            


            $id = parent::insert($data);

            if($id){

                if($data['MontoCuota'] || $data['CantidadCuota']){
                    if ($data['CantidadCuota']) {
                        $cantidadCuota = $data['CantidadCuota'];
                        $monto = $data['MontoTotal']/$data['CantidadCuota'];
                    } else {
                        $monto = $data['MontoCuota'];
                        if(($data['MontoTotal']%$data['MontoCuota'])>0){
                            $cantidadCuota = floor($data['MontoTotal']/$data['MontoCuota']) + 1;
                        } else {
                            $cantidadCuota = $data['MontoTotal']/$data['MontoCuota'];
                        }
                    }

                    $montoAcumulado = $data['MontoTotal'];

                    for ($i=0; $i < $cantidadCuota; $i++) { 
                        if($monto > $montoAcumulado){
                            $monto = $montoAcumulado;
                            $montoAcumulado -= $monto;
                        } else {
                            $montoAcumulado -= $monto;
                        }

                        $dataDD = array(
                            'Descuento'             => $id,
                            'Cuota'                 => $i+1,
                            'MontoCuota'            => $monto,
                            'Intereses'             => $data['Intereses']
                        );
                        $M_DD = new Liquidacion_Model_DbTable_DescuentosDetalles;

                        $idDD = $M_DD->insert($dataDD);
                    }
                }
            }
            $this->_db->commit();
            return $id;
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }    
}