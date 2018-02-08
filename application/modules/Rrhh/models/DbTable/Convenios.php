<?php
class Rrhh_Model_DbTable_Convenios extends Rad_Db_Table
{
    protected $_name = 'Convenios';

    protected $_sort = array('Descripcion ASC');

    protected $_referenceMap    = array(
            );

    protected $_dependentTables = array(	'Rrhh_Model_DbTable_CategoriasGrupos',
    										'Liquidacion_Model_DbTable_VariablesDetallesAbstractas',
    										'Rrhh_Model_DbTable_ConveniosCategorias',
    										'Rrhh_Model_DbTable_ConveniosLicencias',
    										'Rrhh_Model_DbTable_Servicios'
    										);

    public function init() {
        $this->_validators = array(
            'Descripcion' => array(
                array(  'Db_NoRecordExists',
                        'Convenios',
                        'Descripcion',
                        array(  'field' => 'Id',
                                'value' => "{Id}"
                        )
                ),
                'messages' => array('El valor que intenta ingresar se encuentra repetido1111.')
            )
        );
        parent::init();
    }

    /**
     * Borra los registros indicados
     *
     * @param array $where
     *
    */
    public function delete($where)
    {

        try {
            $this->_db->beginTransaction();

            throw new Rad_Db_Table_Exception("No se puede eliminar un convenio.");

            $this->_db->commit();
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    /**
     * Genera un convenio nuevo igual al seleccionado con las licencias y formulas salvo los detalles de categoria
     * 
     * @param int       $idConvenio
     * @param varchar   $nombre
     */   
    public function generarClonConvenio($nombre, $idConvenio)
    {   
        if (!$idConvenio) throw new Rad_Db_Table_Exception("El Convenio no existe");

        try {
            $this->_db->beginTransaction();

            $model_ConveniosCategorias          = new Rrhh_Model_DbTable_ConveniosCategorias;
            $model_ConveniosLicencias           = new Rrhh_Model_DbTable_ConveniosLicencias;
            $model_ConceptosPorConvenios        = new Liquidacion_Model_DbTable_VariablesDetalles_ConceptosLiquidacionesDetallesConvenios;
            $model_ConceptosPorCategorias       = new Liquidacion_Model_DbTable_VariablesDetalles_ConceptosLiquidacionesDetallesCategorias;

            //throw new Rad_Db_Table_Exception("Llego  ".$nombre."  -  : ".$idConvenio);
            Rad_Log::debug('llegoooooooooooooooooooooooooooooooo');
            //ingreso el convenio nuevo
            $dataConvenio = array(
                'Descripcion'           => $nombre
            );            
            $id = $this->insert($dataConvenio);
            Rad_Log::debug('pasooooooooooooooooooooooooooooo');
            //recorro las licencias del convenio
            $row_ConveniosLicencias = $model_ConveniosLicencias->fetchAll("Convenio = ".$idConvenio);    

            if ($row_ConveniosLicencias){

                foreach ($row_ConveniosLicencias as $row_Licencia) {                       
                    //inserto la misma formula para el convenio seleccionado 
                    $dataConvenioLicencia = array(
                        'Descripcion'        => $row_Licencia->Descripcion,
                        'Convenio'           => $id,
                        'SituacionDeRevista' => $row_Licencia->SituacionDeRevista,
                        'Detalle'            => $row_Licencia->Detalle
                    );
                    $idConvenioLicencia = $model_ConveniosLicencias->insert($dataConvenioLicencia); 
                }                
            }

            //recorro las categorias del convenio
            $row_ConveniosCategorias = $model_ConveniosCategorias->fetchAll("Convenio = ".$idConvenio);    

            if ($row_ConveniosCategorias){

                foreach ($row_ConveniosCategorias as $rowCategoria) {

                    //inserto el nuevo registro con los valores iguales al convenio seleccionado 
                    $dataConvenioCategoria = array(
                        'Descripcion'           => $rowCategoria->Descripcion,
                        'Codigo'                => $rowCategoria->Codigo,
                        'Convenio'              => $id,
                        'CategoriaGrupo'        => $rowCategoria->CategoriaGrupo,  
                        'Detalle'               => $rowCategoria->Detalle
                    );
                    $idConvenioCategoria = $model_ConveniosCategorias->insert($dataConvenioCategoria); 

                    //recorro los conceptos que tienen formulas por categorias 
                    $row_ConceptosPorCategorias = $model_ConceptosPorCategorias->fetchAll("ConvenioCategoria = ".$rowCategoria->Id);    

                    if ($row_ConceptosPorCategorias) {

                        foreach ($row_ConceptosPorCategorias as $rowConceptoXCat) {                        
                            //inserto la misma formula para la categoria del convenio seleccionado 
                            $dataConceptoPorCategoria = array(
                                'Variable'           => $rowConceptoXCat->Variable,
                                'Descripcion'        => $rowConceptoXCat->Descripcion,
                                'Convenio'           => $id,
                                'Empresa'            => $rowConceptoXCat->Empresa,
                                'ConvenioCategoria'  => $idConvenioCategoria,
                                'GrupoDePersona'     => $rowConceptoXCat->GrupoDePersona,
                                'Servicio'           => $rowConceptoXCat->Servicio,
                                'FechaDesde'         => $rowConceptoXCat->FechaDesde,
                                'FechaHasta'         => $rowConceptoXCat->FechaHasta,
                                'Formula'            => $rowConceptoXCat->Formula, 
                                'FormulaDetalle'     => $rowConceptoXCat->FormulaDetalle,
                                'Selector'           => $rowConceptoXCat->Selector,                                                         
                                'Obseraciones'       => $rowConceptoXCat->Obseraciones,
                                'VariableJerarquia'  => $rowConceptoXCat->VariableJerarquia,
                                'FechaBaja'          => $rowConceptoXCat->FechaBaja,
                                'Historico'          => $rowConceptoXCat->Historico
                            );
                            $idConceptoXCat = $model_ConceptosPorCategorias->insert($dataConceptoPorCategoria); 
                        }
                    }
                }
            }

            //recorro los conceptos con formulas asociadas al convenio seleccionado
            $row_ConceptosPorConvenios = $model_ConceptosPorConvenios->fetchAll("Convenio = ".$idConvenio." and ConvenioCategoria is null");    

            if ($row_ConceptosPorConvenios){

                foreach ($row_ConceptosPorConvenios as $rowConceptoXConv) {                        
                    //inserto la misma formula para el convenio seleccionado 
                    $dataConceptoPorConvenio = array(
                        'Variable'           => $rowConceptoXConv->Variable,
                        'Descripcion'        => $rowConceptoXConv->Descripcion,
                        'Convenio'           => $id,
                        'Empresa'            => $rowConceptoXConv->Empresa,
                        'ConvenioCategoria'  => $rowConceptoXConv->ConvenioCategoria,
                        'GrupoDePersona'     => $rowConceptoXConv->GrupoDePersona,
                        'Servicio'           => $rowConceptoXConv->Servicio,
                        'FechaDesde'         => $rowConceptoXConv->FechaDesde,
                        'FechaHasta'         => $rowConceptoXConv->FechaHasta,
                        'Formula'            => $rowConceptoXConv->Formula, 
                        'FormulaDetalle'     => $rowConceptoXConv->FormulaDetalle,
                        'Selector'           => $rowConceptoXConv->Selector,                                                         
                        'Obseraciones'       => $rowConceptoXConv->Obseraciones,
                        'VariableJerarquia'  => $rowConceptoXConv->VariableJerarquia,
                        'FechaBaja'          => $rowConceptoXConv->FechaBaja,
                        'Historico'          => $rowConceptoXConv->Historico
                    );
                    $idConceptoXConv = $model_ConceptosPorConvenios->insert($dataConceptoPorConvenio); 
                }                
            }

            $this->_db->commit();
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }


    /**
     * Genera para todo un convenio todos los detalles de las categorias con un nuevo periodo y valor del basico
     *
     * @param $fecha                fecha del inicio del nuevo periodo
     * @param $idConvenio           Id convenio
     * @param $valorBasico          valor nuevo del basico
     * @param $valorNoRemunerativo  valor nuevo no remunerativo
     * @param $fechaDesde           fecha del inicio del periodo donde tomo en cuenta el monto a incrementar
     * @param $fechaHasta           fecha del fin del periodo donde tomo en cuenta el monto a incrementar
     * @return none
    */    
    public function generarDetallesConvenio($fecha, $idConvenio, $valorBasico, $valorBasicoP, $valorNoRemunerativo, $valorNoRemunerativoP,$fechaDesde,$fechaHasta)
    {   
        if (!$idConvenio) throw new Rad_Db_Table_Exception("El Convenio no existe");
        // Rad_Log::debug("Basico");
        // Rad_Log::debug($valorBasico);
        // Rad_Log::debug("Basico %");
        // Rad_Log::debug($valorBasicoP);
        // Rad_Log::debug("No Rem");
        // Rad_Log::debug($valorNoRemunerativo);
        // Rad_Log::debug("No Rem %");
        // Rad_Log::debug($valorNoRemunerativoP);

        try {
            $this->_db->beginTransaction();

            //le resto un dia para la fecha de cierre
            $FechaCerrar = new DateTime($fecha);
            $FechaCerrar->sub(new DateInterval('P1D'));
            //Rad_Log::debug($FechaCerrar->format('Y-m-d'));

            $model_ConveniosCategorias          = new Rrhh_Model_DbTable_ConveniosCategorias;
            $model_ConveniosCategoriasDetalles  = new Rrhh_Model_DbTable_ConveniosCategoriasDetalles;        

            //recorro las categorias del convenio
            $row_ConveniosCategorias = $model_ConveniosCategorias->fetchAll("Convenio = ".$idConvenio);    

            if (!$row_ConveniosCategorias) throw new Rad_Db_Table_Exception("El Convenio no tiene categoria");

            if (!$fechaHasta) $fechaHasta = '2099-12-31';

            foreach ($row_ConveniosCategorias as $rowCategoria) {
                //saco el valor del detalle en ese periodo
                $row_monto = $model_ConveniosCategoriasDetalles->fetchRow("ConvenioCategoria = ".$rowCategoria->Id." and FechaDesde = '".substr($fechaDesde, 0, 10)."' and ifnull(FechaHasta,'2099-12-31') ='".substr($fechaHasta, 0, 10)."'");

                //recorro los detalles de las categorias
                $row_ConveniosCategoriasDetalles = $model_ConveniosCategoriasDetalles->fetchAll("ConvenioCategoria = ".$rowCategoria->Id." and FechaDesde < '".$fecha."' and ifnull(FechaHasta,'2099-12-31') >'".$fecha."'");

                if (!$row_ConveniosCategoriasDetalles) throw new Rad_Db_Table_Exception("Las Categorias no tienen detalles");

                foreach ($row_ConveniosCategoriasDetalles as $rowCategoriaDetalle) {
                    // Cierro al dia anterior 
                    //Rad_Log::debug($rowCategoriaDetalle);
                    $rowCategoriaDetalle->FechaHasta  = $FechaCerrar->format('Y-m-d');
                    //Rad_Log::debug($FechaCerrar->format('Y-m-d'));                    
                    $rowCategoriaDetalle->save();

                    //calculos los valores del basico y no remunerativo dependiendo si viene en pocentaje o valor fijo
                    $valorBasico            = ($valorBasico) ? $valorBasico:0;
                    $ValorNoRemunerativo    = ($ValorNoRemunerativo) ? $ValorNoRemunerativo:0;                   

                    if($valorBasicoP && $row_monto->Valor > 0){ 
                        $valorB = $row_monto->Valor + (($row_monto->Valor * $valorBasicoP)/100);
                    } else if($valorBasico && $row_monto->Valor > 0){
                        $valorB = $row_monto->Valor + $valorBasico;                 
                    } else {
                        $valorB = 0;
                    }

                    if($valorNoRemunerativoP && $row_monto->ValorNoRemunerativo > 0){
                        $valorNR = $row_monto->ValorNoRemunerativo + (($row_monto->ValorNoRemunerativo * $valorNoRemunerativoP)/100);
                    } else if($valorNoRemunerativo && $row_monto->ValorNoRemunerativo > 0){
                        $valorNR = $row_monto->ValorNoRemunerativo + $valorNoRemunerativo;                    
                    } else {
                        $valorNR = 0;
                    }

                    //inserto el nuevo registro con los valores actualizados desde la fecha 
                    $dataCCD = array(
                        'ConvenioCategoria'     => $rowCategoriaDetalle->ConvenioCategoria,
                        'Valor'                 => $valorB,
                        'ValorNoRemunerativo'   => $valorNR,
                        'FechaDesde'            => $fecha
                    );
                    $idCCD = $model_ConveniosCategoriasDetalles->insert($dataCCD);
                }
            }
            $this->_db->commit();
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    /**
     * Busca la Situacion de revista asociada al Convenio Licencia
     * 
     * @param int      $convenioLicencia
     */
    public function getSituacionDeRevistaLicencia($convenioLicencia)
    {   
        $model_ConveniosLicencias          = new Rrhh_Model_DbTable_ConveniosLicencias;
        $model_SituacionesDeRevistas       = new Rrhh_Model_DbTable_SituacionesDeRevistas;


        $row_ConveniosLicencias     = $model_ConveniosLicencias->fetchRow("Id = ".$convenioLicencia);        
        $row_SituacionesDeRevistas  = $model_SituacionesDeRevistas->fetchRow("Id = ".$row_ConveniosLicencias->SituacionDeRevista);


        return $row_SituacionesDeRevistas->Descripcion;
    }

}