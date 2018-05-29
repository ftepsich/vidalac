<?php

/**
 * Base_Model_DbTable_Direcciones
 *
 * Direcciones de Personas
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Base
 * @author Martin Alejandro Santangelo
 * @class Base_Model_DbTable_Direcciones
 * @extends Rad_Db_Table
 */
class Base_Model_DbTable_Direcciones extends Rad_Db_Table
{

    protected $_name = 'Direcciones';
    
    /**
     * Validadores
     *
     * Direccion    -> no vacio
     * Persona      -> no vacio
     *
     */
    protected $_validators = array(
        'Direccion' => array(
            'NotEmpty',
            'allowEmpty'=>false,
            'messages' => array('Falta ingresar la Direccion.')
        ),
        'Localidad' => array(
            'NotEmpty',
            'allowEmpty'=>false,
            'messages' => array('Falta ingresar la Localidad.')
        ),        
        'Persona' => array(
            'NotEmpty',
            'allowEmpty'=>false,
            'messages' => array('No se asocio correctamente la direccion a la persona correspondiente.')
        )
    );
    
    
    protected $_referenceMap = array(
        'Personas' => array(
            'columns' => 'Persona',
            'refTableClass' => 'Base_Model_DbTable_Personas',
            'refTable' => 'Personas',
            'refColumns' => 'Id',
        ),
        'Localidades' => array(
            'columns' => 'Localidad',
            'refTableClass' => 'Base_Model_DbTable_Localidades',
            'refJoinColumns' => array("Descripcion", "CodigoPostal"),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'Localidades',
            'refColumns' => 'Id',
            'comboPageSize' => 10
        ),
        'TiposDeDirecciones' => array(
            'columns' => 'TipoDeDireccion',
            'refTableClass' => 'Base_Model_DbTable_TiposDeDirecciones',
            'refJoinColumns' => array("Descripcion"),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'TiposDeDirecciones',
            'refColumns' => 'Id',
            'comboPageSize' => 10
        )
    );

    public function init ()
    {
        parent::init();

        // $this->addAutoJoin(
        //         'Provincias',
        //         'Localidades.Provincia = Provincias.Id',
        //         array(
        //             'Provincia' => 'Provincias.Descripcion'
        //         )
        // );

        // Con el Joiner nuevo
        if ($this->_fetchWithAutoJoins) {

            $j = $this->getJoiner();

            $j->with('Localidades')->joinRef('Provincias',array('Descripcion'));
        }


    }

    private function makeDescripcion ($data)
    {

        $LO = new Base_Model_DbTable_Localidades(array(), false);
        $TipoLO = $LO->find($data['Localidad'])->current();
        $descLO = ($TipoLO) ? ', ' . ($TipoLO->Descripcion) : '';

        $Pro = new Base_Model_DbTable_Provincias(array(), false);
        $ArtPro = $Pro->find($TipoLO->Provincia)->current();
        $descPro = ($ArtPro) ? ', ' . ($ArtPro->Descripcion) : '';

        $Apais = new Base_Model_DbTable_Paises(array(), false);
        $Apais = $Apais->find($ArtPro->Pais)->current();
        $descApais = ($Apais) ? ', ' . ($Apais->Descripcion) : '';

        $data['DireccionGoogleMaps'] = $data['Direccion'] . $descLO . $descPro . $descApais;

        return $data;
    }

    public static function getTextDireccion($direccion)
    {
        $desc = $direccion->Direccion;

        $localidad = $direccion->findParentRow('Base_Model_DbTable_Localidades');

        if ($localidad) {
            $desc .= ', '.$localidad->Descripcion;
            $provincia = $localidad->findParentRow('Base_Model_DbTable_Provincias');
            if ($provincia) {
                $desc .= ' '.$provincia->Descripcion;
                $pais = $provincia->findParentRow('Base_Model_DbTable_Paises');
                if ($pais) {
                    $desc .= ' '.$pais->Descripcion;
                }
            }
        }
        return $desc;
    }

    public function insert ($data)
    {
        $data = $this->makeDescripcion($data);
        return parent::insert($data);
    }

    public function update ($data, $where)
    {
        $data = $this->makeDescripcion($data);
        return parent::update($data, $where);
    }

    public function fetchDepositos ($where = null, $order = null, $count = null, $offset = null)
    {
        $condition = 'Direcciones.TipoDeDireccion = 2';
        $where = $this->_addCondition($where, $condition);
        return self::fetchAll($where, $order, $count, $offset);
    }

}