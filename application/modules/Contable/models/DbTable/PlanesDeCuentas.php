<?php

require_once 'Rad/Db/Table.php';

class Contable_Model_DbTable_PlanesDeCuentas extends Rad_Db_Table
{

    protected $_name = 'PlanesDeCuentas';
    
    protected $_referenceMap = array(
        'PlanesDeCuentasGrupos' => array(
            'columns' => 'Grupo',
            'refTableClass' => 'Contable_Model_DbTable_PlanesDeCuentasGrupos',
            'refJoinColumns' => array('Descripcion'),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'PlanesDeCuentasGrupos',
            'refColumns' => 'Id',
        ),
        'TreePlanesDeCuentas' => array(
            'columns' => 'Padre',
            'refTableClass' => 'Contable_Model_DbTable_PlanesDeCuentas',
            'refJoinColumns' => array('Descripcion'),
            //'comboBox'          => false,
            //'comboSource'       => 'datagateway/combolist',
            'refTable' => 'PlanesDeCuentas',
            'refColumns' => 'Id'
        )
    );
    protected $_dependentTables = array(
        'Base_Model_DbTable_Articulos',
        'Base_Model_DbTable_ConceptosImpositivos',
        'Contable_Model_DbTable_PlanesDeCuentas',
        'Facturacion_Model_DbTable_FacturasComprasArticulos'
    );

    public function fetchPlanCuentaImputable ($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion  = "PlanesDeCuentas.Imputable = 1";
        $where      = $this->_addCondition($where, $condicion);
        return parent:: fetchAll($where, $order, $count, $offset);
    }

    public function fetchCuentaImpositivaActivo ($where = null, $order = null, $count = null, $offset = null)
    {
        $cfg = Rad_Cfg::get();
        $RamaPadre = $cfg->PlanesDeCuentas->RamaPadreImpuestosActivos->Like;

        $condicion  = "PlanesDeCuentas.Imputable = 1 and PlanesDeCuentas.Jerarquia like '$RamaPadre%'";
        $where      = $this->_addCondition($where, $condicion);
        return parent:: fetchAll($where, $order, $count, $offset);
    }    

    public function fetchArticulo ($where = null, $order = null, $count = null, $offset = null)
    {
        $cfg = Rad_Cfg::get();
        $RamaPadre = $cfg->PlanesDeCuentas->RamaMercaderia->Like;

        $condicion  = "PlanesDeCuentas.Imputable = 1 and PlanesDeCuentas.Jerarquia like '$RamaPadre%'";
        $where      = $this->_addCondition($where, $condicion);
        return parent:: fetchAll($where, $order, $count, $offset);
    }    

    public function fetchActivoRetenciones ($where = null, $order = null, $count = null, $offset = null)
    {
        $cfg = Rad_Cfg::get();
        $RamaPadre = $cfg->PlanesDeCuentas->RamaPadreActivoRetenciones->Like;

        $condicion  = "PlanesDeCuentas.Imputable = 1 and PlanesDeCuentas.Jerarquia like '$RamaPadre%'";
        $where      = $this->_addCondition($where, $condicion);
        return parent:: fetchAll($where, $order, $count, $offset);
    }

    public function fetchActivoPercepciones ($where = null, $order = null, $count = null, $offset = null)
    {
        $cfg = Rad_Cfg::get();
        $RamaPadre = $cfg->PlanesDeCuentas->RamaPadreActivoPercepciones->Like;

        $condicion  = "PlanesDeCuentas.Imputable = 1 and PlanesDeCuentas.Jerarquia like '$RamaPadre%'";
        $where      = $this->_addCondition($where, $condicion);
        return parent:: fetchAll($where, $order, $count, $offset);
    }
    
    public function fetchCuentaImpositivaPasivo ($where = null, $order = null, $count = null, $offset = null)
    {
        $cfg = Rad_Cfg::get();
        $RamaPadre = $cfg->PlanesDeCuentas->RamaPadreImpuestosPasivos->Like;

        $condicion  = "PlanesDeCuentas.Imputable = 1 and PlanesDeCuentas.Jerarquia like '$RamaPadre%'";
        $where      = $this->_addCondition($where, $condicion);
        return parent:: fetchAll($where, $order, $count, $offset);
    } 

    public function fetchPasivoRetenciones ($where = null, $order = null, $count = null, $offset = null)
    {
        $cfg = Rad_Cfg::get();
        $RamaPadre = $cfg->PlanesDeCuentas->RamaPadrePasivoRetenciones->Like;

        $condicion  = "PlanesDeCuentas.Imputable = 1 and PlanesDeCuentas.Jerarquia like '$RamaPadre%'";
        $where      = $this->_addCondition($where, $condicion);
        return parent:: fetchAll($where, $order, $count, $offset);
    }

    public function fetchPasivoPercepciones ($where = null, $order = null, $count = null, $offset = null)
    {
        $cfg = Rad_Cfg::get();
        $RamaPadre = $cfg->PlanesDeCuentas->RamaPadrePasivoPercepciones->Like;

        $condicion  = "PlanesDeCuentas.Imputable = 1 and PlanesDeCuentas.Jerarquia like '$RamaPadre%'";
        $where      = $this->_addCondition($where, $condicion);
        return parent:: fetchAll($where, $order, $count, $offset);
    }

    public function fetchPerdidas ($where = null, $order = null, $count = null, $offset = null)
    {
        $cfg = Rad_Cfg::get();
        $RamaPadre = $cfg->PlanesDeCuentas->RamaPerdidas->Like;
        $condicion  = "PlanesDeCuentas.Imputable = 1 and PlanesDeCuentas.Jerarquia like '$RamaPadre%'";
        $where      = $this->_addCondition($where, $condicion);
        return parent:: fetchAll($where, $order, $count, $offset);
    }
    
    public function fetchCajas ($where = null, $order = null, $count = null, $offset = null)
    {
        $cfg = Rad_Cfg::get();
        $RamaPadre = $cfg->PlanesDeCuentas->RamaPadreCajas->Like;
        $condicion  = "PlanesDeCuentas.Imputable = 1 and PlanesDeCuentas.Jerarquia like '$RamaPadre%'";
        $where      = $this->_addCondition($where, $condicion);
        return parent:: fetchAll($where, $order, $count, $offset);
    }    
    
    public function fetchBancos ($where = null, $order = null, $count = null, $offset = null)
    {
        $cfg = Rad_Cfg::get();
        $RamaPadre = $cfg->PlanesDeCuentas->RamaPadreBancos->Like;
        $condicion  = "PlanesDeCuentas.Imputable = 1 and PlanesDeCuentas.Jerarquia like '$RamaPadre%'";
        $where      = $this->_addCondition($where, $condicion);
        return parent:: fetchAll($where, $order, $count, $offset);
    }      
    
    public function insert ($data)
    {
        $data['Jerarquia'] = $this->_generarJerarquia($data['Padre']);
        return parent::insert($data);
    }

//    public function update ($data, $where)
//    {
//        if ($data['Padre']) {
//            Rad_Log::debug('Busco hijos principales de '.$where[0]);
//            unset($data['Jerarquia']);
//            $rows = $this->fetchAll($where);
//            Rad_Log::debug('Itero sobre encontrados');
//            Rad_Log::debug('Encontrados: '.count($rows));
//            foreach ($rows as $row) {
//                Rad_Log::debug('Encontrado: '.$row->Id . ' -- '.$row->Jerarquia);
//                if ($row->Padre != $data['Padre']) {
//                    Rad_Log::debug('Cambia padre');
//                    $row->Jerarquia = $this->_generarJerarquia($data['Padre']);
//                    Rad_Log::debug('Grabo jerarquia nueva');
//                    $this->_db->update($this->getName(), $data, 'Id ='.$row->Id);
//                    $this->_updatearJerarquiaHijos ($row, $data['Padre']);
//                }
//            }
//            Rad_Log::debug('Fin Itero sobre hijos');
//        }
//        return parent::update($data, $where);
//    }
//
//    /**
//     *
//     * @param Zend_Db_Table_Row $row
//     * @param int $padre
//     */
//    protected function _updatearJerarquiaHijos ($row, $padre)
//    {
//        Rad_Log::debug('Veo hijos de '.$row->Id);
//        $hijos = $this->fetchAll(
//            $this->select()->where('Padre = ?', $row->Id)
//                           ->order('Jerarquia ASC')
//        );
//        Rad_Log::debug('Itero sobre subhijos');
//        Rad_Log::debug('Tiene subhijos: '.count($hijos));
//        foreach ($hijos as $hijo) {
//            Rad_Log::debug('SubHijo: '.$hijo->Id . ' -- '.$hijo->Jerarquia);
//            $hijo->Jerarquia = $this->_generarJerarquia($row->Id);
//            Rad_Log::debug('Grabo subjerarquia nueva');
//            $hijo->save();
//            $this->_updatearJerarquiaHijos($hijo, $row->Id);
//            Rad_Log::debug($hijo->Id . ' --- ' . $hijo->Jerarquia);
//        }
//    }

    /**
     * Devuelve la jerarquia de un nodo de acuerdo a su padre
     *
     * @param int $padre
     * @return string
     */
    protected function _generarJerarquia ($padre)
    {
        $where = 'Padre ';
        $where .= ($padre) ? '=' . $this->_db->quote($padre, 'INTEGER') : 'IS NULL';
        
        $rowLast = $this->fetchRow(
            $this->select()->where($where)
                           ->order('Jerarquia DESC')
        );

        // El padre tiene otros hijos, se agrega uno mas
        if ($rowLast) {
            $jerarquia = array_reverse(explode('.', $rowLast->Jerarquia));
            foreach ($jerarquia as $k => &$j) {
                if (($j != 0) || ($k == count($jerarquia))) {
                    $j++;
                    break;
                }
            }
            $jerarquia = array_reverse($jerarquia);
        // No tiene otros hijos, se agrega un nuevo nivel
        } else {
            $rowJPadre = $this->find((int) $padre)->current();
            $jerarquia = explode('.', $rowJPadre->Jerarquia);
            foreach ($jerarquia as $k => &$j) {
                if (($j == 0) || ($k == count($jerarquia))) {
                    $j++;
                    break;
                }
            }
        }

        // Formatea
        foreach ($jerarquia as $k => &$v) {
            if ($k) {
                $v = sprintf('%02d', $v);
            }
        }

        return implode('.', $jerarquia);
    }

}