<?php

/**
 * Base_Model_DbTable_ChequesConciliacionBancaria
 *
 * Permite colocar la fecha de cobro de los cheques que se entregaron a los proveedores
 * Se realiza como control de los extractos bancarios
 *
 * @class Base_Model_DbTable_ChequesConciliacionBancaria
 * @extends Base_Model_DbTable_Cheques
 */
class Base_Model_DbTable_ChequesConciliacionBancaria extends Base_Model_DbTable_Cheques
{

    protected $_sort = array('Impreso','Numero Asc');
        
    protected $permanentValues = array(
        'TipoDeEmisorDeCheque' => 1
    );
    
    public function init ()
    {
        unset($this->_referenceMap['Clientes']);
        $config = Rad_Cfg::get();
		
        parent::init();
    }

    public function insert ($data) {
       throw new Rad_Db_Table_Exception('No se pueden agregar registros en esta ventana, utilice la aplicacion correcta para la operacion que intenta realiza.');
    }

    public function delete ($where) {
       throw new Rad_Db_Table_Exception('No se pueden eliminar registros en esta ventana, utilice la aplicacion correcta para la operacion que intenta realiza.');
    }

    public function update ($data, $where) {
       throw new Rad_Db_Table_Exception('Funcionalidad en desarrollo !');
       /*
        // Si es automatico ver si ya esta impreso
        unset($data['Persona']);

        $reg = $this->fetchAll($where);

        foreach ($reg as $row) {
            if ($row['ChequeEstado'] != 6 && $row['ChequeEstado'] != 1 && $row['Impreso']) {
                throw new Rad_Db_Table_Exception('Solo puede modificar cheques vacios, disponibles y no impreso');
            }

            if (!$data['Monto'] || !$data['FechaDeEmision'] || !$data['PagueseA']) {
                throw new Rad_Db_Table_Exception("Deben completarse 'Monto', 'Fecha de Emision' y 'Paguese A'");
            }
            //$data['ChequeEstado'] = 6;
            $data['MontoEnLetras'] = Rad_CustomFunctions::num2letras(round($data['Monto'], 2, PHP_ROUND_HALF_UP));

            return Rad_Db_Table::update($data, $where);
        }
        */
    }

    /**
     * Se utiliza para la conciliacion bancaria para filtrar la seleccion  
     */
    public function fetchAll($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = "Cheques.ChequeEstado = 3 and Cheques.TipoDeEmisorDeCheque = 1 and Cheques.FechaDeCobro is null";
        $where = $this->_addCondition($where, $condicion);
        return parent::fetchAll($where, $order, $count, $offset);
    }
}