<?php

require_once('Rad/Db/Table.php');

class Base_Model_DbTable_Chequeras extends Rad_Db_Table
{

    protected $_name = "Chequeras";
    protected $_sort = array ('NumeroDeChequera DESC');
    protected $_validators = array(
        'Cantidad' => array(
            array('GreaterThan', "0"),
            'messages' => array('Debe indicar la cantidad de cheques que trae la chequera.')
        ),
        'ChequeraTipo' => array(
            'NotEmpty',
            'allowEmpty'=>false,
            'messages' => array('Debe ingresar el tipo de chequera.')
        ),
        'CuentaBancaria' => array(
            'NotEmpty',
            'allowEmpty'=>false,
            'messages' => array('Debe ingresar el tipo la cuenta bancaria.')
        ),
        'NumeroDeChequera' => array(
            'NotEmpty',
            'allowEmpty'=>false,
            array(
                'Db_NoRecordExists',
                'Chequeras',
                'NumeroDeChequera',
                'CuentaBancaria = {CuentaBancaria} AND Serie = "{Serie}" AND NumeroDeChequera = {NumeroDeChequera} AND  Id <> {Id}'
            ),
            'messages' => array(
                'Falta ingresar el Punto de Numero.',
                'El numero de Chequera ya existe.'
            )
        ),
        'FechaDeEntrega' => array(
            'NotEmpty',
            'allowEmpty'=>false,
            'messages' => array('Debe ingresar la fecha de entrega de la chequera.')
        )
    );
    protected $_referenceMap = array(
        'CuentasBancarias' => array(
            'columns' => 'CuentaBancaria',
            'refTableClass' => 'Base_Model_DbTable_CuentasBancarias',
            'refJoinColumns' => array("Numero"),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist/fetch/EsPropia',
            'refTable' => 'CuentasBancarias',
            'refColumns' => 'Id',
            'comboPageSize' => 20),

        'ChequerasTipos' => array(
            'columns' => 'ChequeraTipo',
            'refTableClass' => 'Base_Model_DbTable_ChequerasTipos',
            'refJoinColumns' => array("Descripcion"),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'ChequerasTipos',
            'refColumns' => 'Id')

    );

    public function init ()
    {
        parent::init();

        if ($this->_fetchWithAutoJoins) {
            $j = $this->getJoiner();
            $j->with('CuentasBancarias')
              ->joinRef('TiposDeCuentas', array(
                    'Descripcion'
                ))
              ->joinRef('BancosSucursales', array(
                    'Descripcion' => 'TRIM({remote}.Descripcion)'
                ));
        }
    }

    public function insert ($data)
    {
        try {
            $this->_db->beginTransaction();
            // Inserto la chequera
            $id = parent::insert($data);

            // Debo Insertar los cheques en blanco
            $M_Ch = new Base_Model_DbTable_Cheques(array(), false);
            $M_BS = new Base_Model_DbTable_CuentasBancarias(array(), false);
            $R_BS = $M_BS->find($data['CuentaBancaria'])->current();

            if (!$R_BS) {
                throw new Rad_Db_Table_Exception("No Tiene sucursal asociada");
            }

            for ($i = 0; $i < $data['Cantidad']; $i++) {

                 $numero = $data['NumeroInicio'] + $i;

                //controlo que no exista el cheque
                $R_Ch = $M_Ch->fetchAll("Numero = $numero and BancoSucursal = $R_BS->BancoSucursal")->current();

                if ($R_Ch) {
                    throw new Rad_Db_Table_Exception("Ya existen cheques con ese numero.");
                }

                //inserto el cheque
                $Renglon = array(
                    'ChequeEstado'          => 1, //Disponible
                    'BancoSucursal'         => $R_BS->BancoSucursal,
                    'TipoDeEmisorDeCheque'  => 1, //Propio
                    'Serie'                 => $data['Serie'],
                    'Numero'                => $numero,
                    'Chequera'              => $id
                );
                $row = $M_Ch->createRow($Renglon);
                $row->save();
            }
            $this->_db->commit();
            return $id;
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    public function delete ($where)
    {
        try {
            $this->_db->beginTransaction();
            // Solo dejo borrarla si todos los cheques estan en blanco
            $M_Ch = new Base_Model_DbTable_Cheques(array(), false);
            $R_Ch = $M_Ch->fetchAll($where);

            if (count($R_Ch)) {
                foreach ($R_Ch as $reg) {
                    $this->_SalirSi_TieneChequesUsados($reg->Id);
                }
                // Si llego aca significa que no tienen cheques usados
                parent::delete($where);
            }

            $this->_db->commit();
            return true;
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }
    public function update ($data, $where)
    {
        // Si es automatico ver si ya esta impreso
        throw new Rad_Db_Table_Exception("Las chequeras no pueden ser modificadas, deben borrarse y cargarse nuevamente.");
    }

    protected function _SalirSi_TieneChequesUsados ($IdChequera)
    {
        // Veo que todos los cheques esten en estado 1 = sin usar
        $sql = "    select  count(*) as cantidad
                    from    Cheques
                    where   Generador = $IdChequera
                    and     ChequeEstado <> 1";
        $cantidad = $this->_db->fetchOne($sql);

        if ($cantidad > 0) throw new Rad_Db_Table_Exception("La chequera tiene cheques ya usados.");
    }

    public function fetchDisponibles ($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = "Disponibles <> 0";
        $where = $this->_addCondition($where, $condicion);
        return parent:: fetchAll($where, $order, $count, $offset);
    }

}