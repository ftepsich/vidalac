<?php
require_once 'Rad/Db/Table.php';

/**
 * Base_Model_DbTable_GeneradorDeCheques
 *
 * Generador automatico de cheques
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Base
 * @class Base_Model_DbTable_GeneradorDeCheques
 * @extends Rad_Db_Table
 */
class Base_Model_DbTable_GeneradorDeCheques extends Rad_Db_Table
{

    protected $_name = 'GeneradorDeCheques';

    protected $_defaultSource = self::DEFAULT_CLASS;
    
    protected $_referenceMap = array(
        'OrdenesDePagos' => array(
            'columns' => 'OrdenDePago',
            'refTableClass' => 'Facturacion_Model_DbTable_OrdenesDePagos',
            'refJoinColumns' => array("Numero"),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'OrdenesDePagos',
            'refColumns' => 'Id',
            'comboPageSize' => 20
        ),
        'Proveedores' => array(
            'columns' => 'Persona',
            'refTableClass' => 'Base_Model_DbTable_Proveedores',
            'refJoinColumns' => array("Denominacion"),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'Proveedores',
            'refColumns' => 'Id',
            'comboPageSize' => 20
        ),
        'Chequeras' => array(
            'columns' => 'Chequera',
            'refTableClass' => 'Base_Model_DbTable_Chequeras',
            'refJoinColumns' => array("NumeroDeChequera"),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'Chequeras',
            'refColumns' => 'Id',
            'comboPageSize' => 20
        )
    );

    public function init ()
    {
        parent:: init();
        $this->_defaultValues = array(
            'FechaGeneracion' => date('Y-m-d'),
            'FechaPrimerPago' => date('Y-m-d'),
            'Cruzado' => '1',
            'CantidadDeCheques' => '1',
            'NoALaOrden' => '0'
        );

        if ($this->_fetchWithAutoJoins) {
            $j = $this->getJoiner();
            $j->with('Chequeras')
                  ->joinRef('CuentasBancarias', array(
                        'BancoSucursal' => '{remote}.BancoSucursal'
                    ))
                  ->with('CuentasBancarias')
                      ->joinRef('BancosSucursales', array(
                        'Descripcion' => '{remote}.Descripcion'
                    ));
        }
    }

    public function insert ($data)
    {
        try {
            $this->_db->beginTransaction();

            // Si es automatico verificar continuidad y cantidad,
            // en los manuales solo cantidad. Es una propiedad de la chequera.
            $this->_SalirSi_noHayCheques($data["Chequera"], $data["CantidadDeCheques"]);

            // inserto el registro
            $idGenerador = parent::insert($data);

            // Para el primer caso es la fecha de primer pago.
            $fechaCheque = $data["FechaPrimerPago"];
            //Rad_Log::debug($data);
            // Calculo el monto de cada cheque
            $MontoCheque = $data["MontoTotal"] / $data["CantidadDeCheques"];

            $M_Ch = new Base_Model_DbTable_Cheques(array(), false);
            $M_BChe = new Base_Model_DbTable_Chequeras(array(), false);
            $R_BChe = $M_BChe->find($data['Chequera'])->current();

            $M_CB = new Base_Model_DbTable_CuentasBancarias(array(), false);
            $R_CB = $M_CB->find($R_BChe->CuentaBancaria)->current();


            if (!$R_CB) {
                throw new Rad_Db_Table_Exception("No Tiene Sucursal Asociada");
            }
            $M_P = new Base_Model_DbTable_Proveedores(array(), false);

            // ahora completo los cheques
            for ($x = 0; $x < $data["CantidadDeCheques"]; $x++) {

                // Busco el proximo cheque a updatear
                $idCheque = $this->_seleccionarProximoCheque($data["Chequera"]);

                // Recalculo la fecha segun los bloqueos que existan
                $fechaCheque = $this->_seleccionarProximaFecha($fechaCheque, $MontoCheque);
                
                // Controlo que la fecha de emision sea menor a la del primer pago.
                 $this->verificaFechaEmisionMenorFechaPrimerPago($data["FechaGeneracion"],$fechaCheque);

                // Si no me indica a quien pagarlo uso la Razon Social
                if (!$data["AlaOrden"]) {
                    $R_P = $M_P->fetchRow("Id = " . $data["Persona"]);
                    if (!$R_P) {
                        throw new Rad_Db_Table_Exception("No se encuentra la Persona.");
                    } else {
                        $data["AlaOrden"] = $R_P->RazonSocial;
                    }
                }

                $dataCheque = array(
                    'ChequeEstado' => 6,
                    'Persona' => $data["Persona"],
                    'BancoSucursal' => $R_CB->BancoSucursal,
                    'FechaDeEmision' => $data["FechaGeneracion"],
                    'FechaDeVencimiento' => $fechaCheque,
                    'Monto' => $MontoCheque,
                    'PagueseA' => $data["AlaOrden"],
                    'Cruzado' => $data["Cruzado"],
                    'NoALaOrden' => $data["NoALaOrden"],
                    'Generador' => $idGenerador
                );

                $M_Ch->update($dataCheque, "Id=" . $idCheque);

                // Calculo la fecha hipotetica del proximo cheque segun la distancia
                $fechaCheque = date('Y-m-d', strtotime("+" . $data["DistanciaEnDias"] . " days", strtotime($fechaCheque)));
            }
            $this->_db->commit();
            return $idGenerador;
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    // =====================================================================================================================
    public function update ($data, $where)
    {
        throw new Rad_Db_Table_Exception("El generador de Cheques no debe modificarse.");
    }

    // =====================================================================================================================
    public function delete ($where)
    {
        throw new Rad_Db_Table_Exception("El generador de Cheques no debe eliminarse.");
    }

    // =====================================================================================================================
    
    /**
     * Verifica si la fecha de emision es menor a la fecha del primer pago 
     *
     * @param date $FechaGeneracion 
     * @param date $FechaCheque 
     * 
     * @return boolean
     */
    public function verificaFechaEmisionMenorFechaPrimerPago($FechaGeneracion,$FechaCheque)
    {
        if ($FechaGeneracion && $FechaCheque) {
           if ($FechaGeneracion >= $FechaCheque) {
             throw new Rad_Db_Table_Exception("La fecha de emision debe ser menor a la fecha del primer pago.");
            } 
        }     
        return true;
    }    

    // =====================================================================================================================
    protected function _SalirSi_noHayCheques ($idChequera, $cantUsar)
    {

        $sql = "select 	count(*) as cantDisponibles
                from    Cheques
                where   Chequera = $idChequera
                and     ChequeEstado = 1";

        $cantDisponibles = $this->_db->fetchOne($sql);

        if ($cantDisponibles < $cantUsar) {
            // error la chequera no tiene tantos cheques disponibles
            throw new Rad_Db_Table_Exception("Las chequera seleccionada no tiene tantos cheques sin usar. Actualmente quedan $cantDisponibles.");
        }
    }
	
    // =====================================================================================================================
    public function recuperarProximoNumeroCheque ($idChequera)
    {

        $sql = "select 	Numero
                from    Cheques
                where   Chequera = $idChequera
                and     ChequeEstado = 1
                order by Numero asc limit 1";

        $numero = $this->_db->fetchOne($sql);

        if ($numero) {
            return $numero;
        } else {
            throw new Rad_Db_Table_Exception("La Chequera se encuentra completamente utilizada.");
        }
    }

    // =====================================================================================================================
    protected function _seleccionarProximoCheque ($idChequera)
    {

        $sql = "select 	Id
                from    Cheques
                where   Chequera = $idChequera
                and     ChequeEstado = 1
                order by Numero asc limit 1";

        $idCheque = $this->_db->fetchOne($sql);

        if ($idCheque) {
            return $idCheque;
        } else {
            throw new Rad_Db_Table_Exception("Error inesperado. No se localiza el proximo cheque a usar.");
        }
    }

    // =====================================================================================================================
    protected function _seleccionarProximaFecha ($fechaCheque, $MontoCheque)
    {

        // Verifico que no caiga un sabado o domingo, en dicho caso adelanto los dias.
        // Representación numérica ISO-8601 del día de la semana
        // 1 (para lunes) hasta 7 (para domingo)

        $diaSemana = date('N', strtotime($fechaCheque));
        //Rad_Log::debug("$fechaCheque -- $diaSemana");
        if ($diaSemana == 6) {
            // Es un sabado
            $fechaCheque = date('Y-m-d', strtotime("+2 days", strtotime($fechaCheque)));
            $fechaCheque = $this->_seleccionarProximaFecha($fechaCheque, $MontoCheque);
        } else {
            // No es sabado
            if ($diaSemana == 7) {
                // Es un Domingo
                $fechaCheque = date('Y-m-d', strtotime("+1 days", strtotime($fechaCheque)));
                $fechaCheque = $this->_seleccionarProximaFecha($fechaCheque, $MontoCheque);
            } else {

                // No es fin de semana asi que sigo y reviso los bloqueos
                // Busco los bloqueos que existen a la fecha que deberia hacerse el cheque
                $sql = "select 	FechaInicio, FechaFin, ifnull(MontoMaximo,0) MontoMaximo
						from	ChequesBloqueos
						where	FechaInicio <= '$fechaCheque'
						and		FechaFin 	>= '$fechaCheque'
						order by FechaFin desc";

                $R = $this->_db->fetchAll($sql);

                if (!empty($R)) {

                    // Recorro cada registro hasta que falle, la primera que falle tomo la
                    // fecha de fin y debo llamar de nuevo a esta funcion para ver si no cae
                    // en otro periodo bloqueado

                    if ($R["MontoMaximo"] > 0) {
                        // Verifico cuanto hay en cheques en ese periodo

                        $sql = "select 	sum(Monto) as Monto
                                from    Cheques
                                where   TipoDeEmisorDeCheque = 1
                                and     FechaDeVencimiento >= '" . $R["FechaInicio"] . "'
                                and     FechaDeVencimiento >= '" . $R["FechaFin"] . "'
                                order by FechaFin desc";

                        $MontoAcumulado = $this->_db->fetchOne($sql);

                        if ($R["MontoMaximo"] <= $MontoAcumulado + $MontoCheque) {
                            $fechaCheque = date('Y-m-d', strtotime("+1 days", strtotime($R["FechaFin"])));
                            $fechaCheque = $this->_seleccionarProximaFecha($fechaCheque, $MontoCheque);
                        }
                    } else {
                        $fechaCheque = date('Y-m-d', strtotime("+1 days", strtotime($R["FechaFin"])));
                        $fechaCheque = $this->_seleccionarProximaFecha($fechaCheque, $MontoCheque);
                    }
                }
            }
        }

        return $fechaCheque;
    }

}