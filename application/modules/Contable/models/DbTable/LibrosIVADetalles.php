<?php
/**
 * Contable_Model_DbTable_LibrosIVADetalles
 *
 * Libros de Iva Detalles
 *
 * @copyright SmartSoftware Argentina
 * @package Aplicacion
 * @subpackage Contable
 * @class Contable_Model_DbTable_LibrosIVADetalles
 * @extends Rad_Db_Table
 */
class Contable_Model_DbTable_LibrosIVADetalles extends Rad_Db_Table
{

    protected $_sort = array("TipoDeLibro", "LibroIVA", "Comprobantes.Punto", "Comprobantes.Numero");
    protected $_name = 'LibrosIVADetalles';


    protected $_referenceMap = array(
        'Personas' => array(
            'columns' => 'Persona',
            'refTableClass' => 'Base_Model_DbTable_Personas',
            'refJoinColumns' => array('RazonSocial'),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'Personas',
            'refColumns' => 'Id',
        ),
        'Comprobantes' => array(
            'columns' => 'Comprobante',
            'refTableClass' => 'Facturacion_Model_DbTable_Comprobantes',
            'refJoinColumns' => array('Id','Numero', 'Punto'),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'Comprobantes',
            'refColumns' => 'Id'
        ),
        'LibroIVA' => array(
            'columns' => 'LibroIVA',
            'refTableClass' => 'Contable_Model_DbTable_LibrosIVA',
            'refJoinColumns' => array('Descripcion'),
            'comboBox' => true,
            'comboSource' => 'datagateway/combolist',
            'refTable' => 'LibrosIVA',
            'refColumns' => 'Id'
        )
    );

    protected $_calculatedFields = array(
        'NumeroCompleto' => "fNumeroCompleto(Comprobantes.Id,'C') COLLATE utf8_general_ci"
    );

    protected $_dependentTables = array();

    /**
     * Borra un el detalle del libro de iva de un comprobante
     * En el caso que el Libro de iva este cerrado ya no puedo borrarlo.
     * Solo se pueden borrar aquellos que sean comprobantes de entrada,
     * los que nosotros generamos no se pueden borrar, se anulan y en el
     * detalle se ponen los valores en 0.
     *
     * @param array $where 	Registros que se deben eliminar
     */
    public function delete ($where)
    {
        try {
            $this->_db->beginTransaction();
            $reg = $this->fetchAll($where);

            foreach ($reg as $R_LID) {
                $this->salirSi_estaCerrado($R_LID->LibroIVA);
                parent::delete('Id =' . $R_LID->Id);
            }
            $this->_db->commit();
            return true;
        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    /**
     * Asienta en el libro de iva un comprobante.
     * En el caso de existir previamente lo borra
     *
     * @param array $where 	Registros que se deben eliminar
     */
    public function asentarLibroIVA ($row, $grupo=null)
    {   // Rad_Log::debug("CI");
        // Rad_Log::debug($row);
        $M_CI = new Base_Model_DbTable_ConceptosImpositivos;
        $M_TC = new Facturacion_Model_DbTable_TiposDeComprobantes;
        $M_C  = new Facturacion_Model_DbTable_Comprobantes;

        $R_TC = $M_TC->find($row->TipoDeComprobante)->current();
        if (!$R_TC) throw new Rad_Db_Table_Exception("Error al intentar asentar en el Libro de IVA. No se encuentra el tipo de comprobante.");

        $Multiplicador = 0;

        // Credito en Cuenta Corriente / Debito en Cuenta Corriente / Retencion IB Entre Rios (R) / Percepción IB Entre Rí­os (R)
        if ($row->TipoDeComprobante == 65 || $row->TipoDeComprobante == 66 || $row->ConceptoImpositivo == 41 || $row->ConceptoImpositivo == 42) {
            // Comprobante que no se inserta en el libro de IVA, debe mejorarse y crear una columna para estos casos y su correspondiente filtro
            return true;
        } else {

            // Si son comprobantes de cobro o pago asiento los comprobantes hijos (retenciones)
            if (($R_TC->Grupo == 9) || ($R_TC->Grupo == 11)) {
                // 27/06/2016 TipoDeComprobante => 65  son los Creditos en Cuenta Corriente y 66 son los Debitos en Cuenta Corriente... no se asientan
                $R_C = $M_C->fetchAll("ComprobantePadre = $row->Id and TipoDeComprobante not in (65,66)");
                if (count($R_C)) {
                    foreach ($R_C as $row1) {
                        $this->asentarLibroIVA($row1, $R_TC->Grupo);
                    }
                }
            } else {

                if ($grupo) {
                    $R_TC->Grupo = $grupo;
                    $condicion = " and C.Id	= ";
                } else {
                    $condicion = " and C.ComprobantePadre = ";
                }

                // 1:FC, 6:FV, 7:NCE, 8:NCR, 9:OP, 11:RC, 12:NDE, 13:NDR
                switch ($R_TC->Grupo) {
                    case 1: case 6: case 9: case 11: case 13: case 12: case 14: case 15: case 16: $Multiplicador = 1;
                        break;
                    case 7: case 8: $Multiplicador = -1;
                        break;
                }

                switch ($R_TC->Grupo) {
                    /* 1 = Compra -- 2 = Venta */
                    case 1: case 8: case 9: case 11: case 13: case 14: case 15: case 16: $tipoDeLibro = 1;
                        break;
                    case 6: case 7: case 12: $tipoDeLibro = 2;
                        break;
                }

                if ($Multiplicador) {

                    $idComprobante      = $row->Id;
                    $persona            = $row->Persona;
                    $libroIVA           = $row->LibroIVA;

                    $idConcepto_ImpInt  = $M_CI->impInterno;

                    $AmbitoNacional     = $M_CI->AmbitoNacional;
                    $AmbitoProvincial   = $M_CI->AmbitoProvincial;
                    $AmbitoMunicipal    = $M_CI->AmbitoMunicipal;

                    $sql = "select ifnull(sum(C.Monto),0) as Monto
                            from    Comprobantes C, ConceptosImpositivos CI, EntesRecaudadores ER
                            where   C.ConceptoImpositivo    = CI.Id
                            and     CI.EnteRecaudador       = ER.Id
                            and     ER.Ambito               = $AmbitoNacional
                            and     CI.esPercepcion         = 1
                            and     CI.TipoDeConcepto       = 1
                            $condicion $idComprobante";

                    $R1 = $this->_db->fetchRow($sql);
                    $Imp_MT_PerIVA = 0;
                    if ($R1['Monto']) {
                        $Imp_MT_PerIVA = $R1['Monto'];
                    }

                    $sql = "select ifnull(sum(C.Monto),0) as Monto
                            from    Comprobantes C, ConceptosImpositivos CI, EntesRecaudadores ER
                            where   C.ConceptoImpositivo    = CI.Id
                            and     CI.EnteRecaudador       = ER.Id
                            and     ER.Ambito               = $AmbitoNacional
                            and     CI.esPercepcion         = 1
                            and     CI.TipoDeConcepto       not in (1,2,3,5)
                            $condicion $idComprobante";

                    $R2 = $this->_db->fetchRow($sql);
                    $Imp_MT_PerNac = 0;
                    if ($R2['Monto']) {
                        $Imp_MT_PerNac = $R2['Monto'];
                    }

                    $sql = "select ifnull(sum(C.Monto),0) as Monto
                            from    Comprobantes C, ConceptosImpositivos CI, EntesRecaudadores ER
                            where   C.ConceptoImpositivo    = CI.Id
                            and     CI.EnteRecaudador       = ER.Id
                            and     ER.Ambito               = $AmbitoProvincial
                            and		CI.TipoDeConcepto		<> 3
                            and     CI.esPercepcion         = 1
                            $condicion $idComprobante";

                    $R3 = $this->_db->fetchRow($sql);
                    $Imp_MT_PerProv = 0;
                    if ($R3['Monto']) {
                        $Imp_MT_PerProv = $R3['Monto'];
                    }

                    $sql = "select ifnull(sum(C.Monto),0) as Monto
                            from    Comprobantes C, ConceptosImpositivos CI, EntesRecaudadores ER
                            where   C.ConceptoImpositivo    = CI.Id
                            and     CI.EnteRecaudador       = ER.Id
                            and     ER.Ambito               = $AmbitoMunicipal
                            and     CI.esPercepcion         = 1
                            $condicion $idComprobante";

                    $R4 = $this->_db->fetchRow($sql);
                    $Imp_MT_PerMuni = 0;
                    if ($R4['Monto']) {
                        $Imp_MT_PerMuni = $R4['Monto'];
                    }

                    $sql = "select ifnull(sum(C.Monto),0) as Monto
                            from    Comprobantes C, ConceptosImpositivos CI, EntesRecaudadores ER
                            where   C.ConceptoImpositivo    = CI.Id
                            and     CI.EnteRecaudador       = ER.Id
                            and     ER.Ambito               = $AmbitoNacional
                            and     CI.esRetencion          = 1
                            and     CI.TipoDeConcepto       not in (1,2,3,5)
                            $condicion $idComprobante";

                    $R5 = $this->_db->fetchRow($sql);
                    $Imp_MT_RetNac = 0;
                    if ($R5['Monto']) {
                        $Imp_MT_RetNac = $R5['Monto'];
                    }

                    $sql = "select ifnull(sum(C.Monto),0) as Monto
                            from    Comprobantes C, ConceptosImpositivos CI, EntesRecaudadores ER
                            where   C.ConceptoImpositivo    = CI.Id
                            and     CI.EnteRecaudador       = ER.Id
                            and     ER.Ambito               = $AmbitoProvincial
                            and	    CI.TipoDeConcepto   	<> 3
                            and     CI.esRetencion          = 1
                            $condicion $idComprobante";

                    $R6 = $this->_db->fetchRow($sql);
                    $Imp_MT_RetProv = 0;
                    if ($R6['Monto']) {
                        $Imp_MT_RetProv = $R6['Monto'];
                    }

                    $sql = "select ifnull(sum(C.Monto),0) as Monto
                            from    Comprobantes C, ConceptosImpositivos CI, EntesRecaudadores ER
                            where   C.ConceptoImpositivo    = CI.Id
                            and     CI.EnteRecaudador       = ER.Id
                            and     ER.Ambito               = $AmbitoMunicipal
                            and     CI.esRetencion          = 1
                            $condicion $idComprobante";

                    $R7 = $this->_db->fetchRow($sql);
                    $Imp_MT_RetMuni = 0;
                    if ($R7['Monto']) {
                        $Imp_MT_RetMuni = $R7['Monto'];
                    }

                    $sql = "select ifnull(sum(C.Monto),0) as Monto
                            from    Comprobantes C, ConceptosImpositivos CI, EntesRecaudadores ER
                            where   C.ConceptoImpositivo    = CI.Id
                            and     CI.EnteRecaudador       = ER.Id
                            and     ER.Ambito               = $AmbitoNacional
                            and     CI.esRetencion          = 1
                            and     CI.TipoDeConcepto       = 1
                            $condicion $idComprobante";

                    $R8 = $this->_db->fetchRow($sql);
                    $Imp_MT_RetIVA = 0;
                    if ($R8['Monto']) {
                        $Imp_MT_RetIVA = $R8['Monto'];
                    }

                    $sql = "select ifnull(sum(C.Monto),0) as Monto
                            from    Comprobantes C, ConceptosImpositivos CI, EntesRecaudadores ER
                            where   C.ConceptoImpositivo    = CI.Id
                            and     CI.EnteRecaudador       = ER.Id
                            and     ER.Ambito               = $AmbitoNacional
                            and     CI.esPercepcion         = 1
                            and     CI.TipoDeConcepto       = 2
                            $condicion $idComprobante";

                    $R9 = $this->_db->fetchRow($sql);
                    $Imp_MT_PerGan = 0;
                    if ($R9['Monto']) {
                        $Imp_MT_PerGan = $R9['Monto'];
                    }

                    $sql = "select ifnull(sum(C.Monto),0) as Monto
                            from    Comprobantes C, ConceptosImpositivos CI, EntesRecaudadores ER
                            where   C.ConceptoImpositivo    = CI.Id
                            and     CI.EnteRecaudador       = ER.Id
                            and     ER.Ambito               = $AmbitoNacional
                            and     CI.esPercepcion         = 1
                            and     CI.TipoDeConcepto       = 5
                            $condicion $idComprobante";

                    $R10 = $this->_db->fetchRow($sql);
                    $Imp_MT_PerSuss = 0;
                    if ($R10['Monto']) {
                        $Imp_MT_PerSuss = $R10['Monto'];
                    }

                    $sql = "select ifnull(sum(C.Monto),0) as Monto
                            from    Comprobantes C, ConceptosImpositivos CI, EntesRecaudadores ER
                            where   C.ConceptoImpositivo    = CI.Id
                            and     CI.EnteRecaudador       = ER.Id
                            and     ER.Ambito               = $AmbitoProvincial
                            and     CI.esPercepcion         = 1
                            and     CI.TipoDeConcepto       = 3
                            $condicion $idComprobante";

                    $R11 = $this->_db->fetchRow($sql);
                    $Imp_MT_PerIB = 0;
                    if ($R11['Monto']) {
                        $Imp_MT_PerIB = $R11['Monto'];
                    }


                    $sql = "select ifnull(sum(C.Monto),0) as Monto
                            from    Comprobantes C, ConceptosImpositivos CI, EntesRecaudadores ER
                            where   C.ConceptoImpositivo    = CI.Id
                            and     CI.EnteRecaudador       = ER.Id
                            and     ER.Ambito               = $AmbitoNacional
                            and     CI.esRetencion          = 1
                            and     CI.TipoDeConcepto       = 2
                            $condicion $idComprobante";

                    $R12 = $this->_db->fetchRow($sql);
                    $Imp_MT_RetGan = 0;
                    if ($R12['Monto']) {
                        $Imp_MT_RetGan = $R12['Monto'];
                    }

                    $sql = "select ifnull(sum(C.Monto),0) as Monto
                            from    Comprobantes C, ConceptosImpositivos CI, EntesRecaudadores ER
                            where   C.ConceptoImpositivo    = CI.Id
                            and     CI.EnteRecaudador       = ER.Id
                            and     ER.Ambito               = $AmbitoNacional
                            and     CI.esRetencion          = 1
                            and     CI.TipoDeConcepto       = 5
                            $condicion $idComprobante";

                    $R13 = $this->_db->fetchRow($sql);
                    $Imp_MT_RetSuss = 0;
                    if ($R13['Monto']) {
                        $Imp_MT_RetSuss = $R13['Monto'];
                    }

                    $sql = "select ifnull(sum(C.Monto),0) as Monto
                            from    Comprobantes C, ConceptosImpositivos CI, EntesRecaudadores ER
                            where   C.ConceptoImpositivo    = CI.Id
                            and     CI.EnteRecaudador       = ER.Id
                            and     ER.Ambito               = $AmbitoProvincial
                            and     CI.esRetencion          = 1
                            and     CI.TipoDeConcepto       = 3
                            $condicion $idComprobante";

                    $R14 = $this->_db->fetchRow($sql);
                    $Imp_MT_RetIB = 0;
                    if ($R14['Monto']) {
                        $Imp_MT_RetIB = $R14['Monto'];
                    }

                    $sql = "select ifnull(sum(C.Monto),0) as Monto
                            from    Comprobantes C, ConceptosImpositivos CI, EntesRecaudadores ER
                            where   C.ConceptoImpositivo    = CI.Id
                            and     CI.EnteRecaudador       = ER.Id
                            and     CI.TipoDeConcepto       = 6
                            $condicion $idComprobante";

                    $R15 = $this->_db->fetchRow($sql);
                    $Imp_MT_Internos = 0;
                    if ($R15['Monto']) {
                        $Imp_MT_Internos = $R15['Monto'];
                    }



                    /*
                    Para IVA si son Comprobantes 'B' debemos calcular en funcion de los valores de comprobnates detalles
                    En el caso que sean A, E, o M solo sumo los comprobantes impositivos hijos
                    */

                    //Rad_Log::debug($row->getTable());

                    $conceptosIva = $row->getTable()->afip_RecuperarTotalesIVA($idComprobante);
                    
                    $Imp_NG_0250 = $conceptosIva['0250']['MontoImponible'];
                    $Imp_NG_0500 = $conceptosIva['0500']['MontoImponible'];
                    $Imp_NG_1050 = $conceptosIva['1050']['MontoImponible'];
                    $Imp_NG_2100 = $conceptosIva['2100']['MontoImponible'];
                    $Imp_NG_2700 = $conceptosIva['2700']['MontoImponible'];

                    if ($R_TC->TipoDeLetra == 2) {
                        // Tipo B --------------------------------

                        $Imp_MT_IVA0250 = $conceptosIva['0250']['Monto'];
                        $Imp_MT_IVA0500 = $conceptosIva['0500']['Monto'];                        
                        $Imp_MT_IVA1050 = $conceptosIva['1050']['Monto'];
                        $Imp_MT_IVA2100 = $conceptosIva['2100']['Monto'];
                        $Imp_MT_IVA2700 = $conceptosIva['2700']['Monto'];
                    } else {

                        // Tipo A, etc

                        // IVA 21 --------------
                        $sql = "    select  ifnull(sum(C.Monto),0) as Monto
                                    from    Comprobantes C
                                            inner join ConceptosImpositivos CI on C.ConceptoImpositivo = CI.Id
                                    where   C.ComprobantePadre = $idComprobante
                                    and     CI.Id = ". $M_CI->iva21;

                        $R2100 = $this->_db->fetchRow($sql);
                        $Imp_MT_IVA2100 = 0;
                        if ($R2100['Monto']) {
                            $Imp_MT_IVA2100 = $R2100['Monto'];
                        }

                        // IVA 27 --------------
                        $sql = "    select  ifnull(sum(C.Monto),0) as Monto
                                    from    Comprobantes C
                                            inner join ConceptosImpositivos CI on C.ConceptoImpositivo = CI.Id
                                    where   C.ComprobantePadre = $idComprobante
                                    and     CI.Id = ". $M_CI->iva27;

                        $R2700 = $this->_db->fetchRow($sql);
                        $Imp_MT_IVA2700 = 0;
                        if ($R2700['Monto']) {
                            $Imp_MT_IVA2700 = $R2700['Monto'];
                        }

                        // IVA 10.5 --------------
                        $sql = "    select  ifnull(sum(C.Monto),0) as Monto
                                    from    Comprobantes C
                                            inner join ConceptosImpositivos CI on C.ConceptoImpositivo = CI.Id
                                    where   C.ComprobantePadre = $idComprobante
                                    and     CI.Id = ". $M_CI->iva105;

                        $R1050 = $this->_db->fetchRow($sql);
                        $Imp_MT_IVA1050 = 0;
                        if ($R1050['Monto']) {
                            $Imp_MT_IVA1050 = $R1050['Monto'];
                        }
                        
                        // IVA 5 --------------
                        $sql = "    select  ifnull(sum(C.Monto),0) as Monto
                                    from    Comprobantes C
                                            inner join ConceptosImpositivos CI on C.ConceptoImpositivo = CI.Id
                                    where   C.ComprobantePadre = $idComprobante
                                    and     CI.Id = ". $M_CI->iva05;

                        $R0500 = $this->_db->fetchRow($sql);
                        $Imp_MT_IVA0500 = 0;
                        if ($R0500['Monto']) {
                            $Imp_MT_IVA0500 = $R0500['Monto'];
                        }

                        // IVA 2.5 --------------
                        $sql = "    select  ifnull(sum(C.Monto),0) as Monto
                                    from    Comprobantes C
                                            inner join ConceptosImpositivos CI on C.ConceptoImpositivo = CI.Id
                                    where   C.ComprobantePadre = $idComprobante
                                    and     CI.Id = ". $M_CI->iva025;

                        $R0250 = $this->_db->fetchRow($sql);
                        $Imp_MT_IVA0250 = 0;
                        if ($R0250['Monto']) {
                            $Imp_MT_IVA0250 = $R0250['Monto'];
                        }
                    }

                    $Imp_NG_ExentosyNoGravados =    $row->getTable()->recuperarMontoImponibleFacturacion($M_CI->iva0, $idComprobante) +
                                                    $row->getTable()->recuperarMontoImponibleFacturacion($M_CI->ivaNoGravado, $idComprobante) +
                                                    $row->getTable()->recuperarMontoImponibleFacturacion($M_CI->ivaExcento, $idComprobante);

                    $Imp_MT_Comprobante = $row->getTable()->recuperarMontoTotal($idComprobante);

                    // En el libro de IVA
                    if ($R_TC->Grupo == 9 || $R_TC->Grupo == 11) {
                        $Imp_MT_Comprobante = $row->Monto;
                    }


                    // Para las facturas y notas C solo tienen el valor excento
                    // 1:FC, 6:FV, 7:NCE, 8:NCR, 12:NDE, 13:NDR --- 9:OP, 11:RC,
                    $comp = array(1,6,7,8,12,13);
                    if ($R_TC->TipoDeLetra == 3 && in_array($R_TC->Grupo,$comp)) {
                        
                        $data = array(
                            "Persona"                                       => $persona,
                            "Comprobante"                                   => $idComprobante,
                            "LibroIVA"                                      => $libroIVA,
                            "TipoDeLibro"                                   => $tipoDeLibro,
                            "ImporteNetoGravado105"                         => 0,
                            "ImporteNetoGravado210"                         => 0,
                            "ImporteNetoGravado270"                         => 0,
                            "ImporteNetoGravado5"                           => 0,
                            "ImporteNetoGravado25"                          => 0,
                            "ImporteIVA105"                                 => 0,
                            "ImporteIVA210"                                 => 0,
                            "ImporteIVA270"                                 => 0,
                            "ImporteIVA5"                                   => 0,
                            "ImporteIVA25"                                  => 0,                            
                            "ImporteImpuestosInternos"                      => 0,
                            "ImporteConceptosExentosONoGravados"            =>  $Imp_MT_Comprobante * $Multiplicador,
                            "ImportePercepcionesIVA"                        => 0,
                            "ImportePercepcionesGanancias"                  => 0,
                            "ImportePercepcionesSuss"                       => 0,
                            "ImportePercepcionesIB"                         => 0,
                            "ImporteOtrasPercepcionesImpuestosNacionales"   => 0,
                            "ImporteOtrasPercepcionesImpuestosProvinciales" => 0,
                            "ImportePercepcionesTasaMunicipales"            => 0,
                            "ImporteRetencionesIVA"                         => 0,
                            "ImporteRetencionesGanancias"                   => 0,
                            "ImporteRetencionesSuss"                        => 0,
                            "ImporteRetencionesIB"                          => 0,
                            "ImporteOtrasRetencionesImpuestosNacionales"    => 0,
                            "ImporteOtrasRetencionesImpuestosProvinciales"  => 0,
                            "ImporteRetencionesTasaMunicipales"             => 0,
                            "ImporteTotalComprobante"                       =>  $Imp_MT_Comprobante * $Multiplicador
                        );

                    } else {

                        $data = array(
                            "Persona"                                       => $persona,
                            "Comprobante"                                   => $idComprobante,
                            "LibroIVA"                                      => $libroIVA,
                            "TipoDeLibro"                                   => $tipoDeLibro,
                            "ImporteNetoGravado105"                         => $Imp_NG_1050 * $Multiplicador,
                            "ImporteNetoGravado210"                         => $Imp_NG_2100 * $Multiplicador,
                            "ImporteNetoGravado270"                         => $Imp_NG_2700 * $Multiplicador,
                            "ImporteNetoGravado5"                           => $Imp_NG_0500 * $Multiplicador,
                            "ImporteNetoGravado25"                          => $Imp_NG_0250 * $Multiplicador,
                            "ImporteIVA105"                                 => $Imp_MT_IVA1050 * $Multiplicador,
                            "ImporteIVA210"                                 => $Imp_MT_IVA2100 * $Multiplicador,
                            "ImporteIVA270"                                 => $Imp_MT_IVA2700 * $Multiplicador,
                            "ImporteIVA5"                                   => $Imp_MT_IVA0500 * $Multiplicador,
                            "ImporteIVA25"                                  => $Imp_MT_IVA0250 * $Multiplicador,                                                        
                            "ImporteImpuestosInternos"                      => $Imp_MT_Internos * $Multiplicador,
                            "ImporteConceptosExentosONoGravados"            => $Imp_NG_ExentosyNoGravados * $Multiplicador,
                            "ImportePercepcionesIVA"                        => $Imp_MT_PerIVA * $Multiplicador,
                            "ImportePercepcionesGanancias"                  => $Imp_MT_PerGan * $Multiplicador,
                            "ImportePercepcionesSuss"                       => $Imp_MT_PerSuss * $Multiplicador,
                            "ImportePercepcionesIB"                         => $Imp_MT_PerIB * $Multiplicador,
                            "ImporteOtrasPercepcionesImpuestosNacionales"   => $Imp_MT_PerNac * $Multiplicador,
                            "ImporteOtrasPercepcionesImpuestosProvinciales" => $Imp_MT_PerProv * $Multiplicador,
                            "ImportePercepcionesTasaMunicipales"            => $Imp_MT_PerMuni * $Multiplicador,
                            "ImporteRetencionesIVA"                         => $Imp_MT_RetIVA * $Multiplicador,
                            "ImporteRetencionesGanancias"                   => $Imp_MT_RetGan * $Multiplicador,
                            "ImporteRetencionesSuss"                        => $Imp_MT_RetSuss * $Multiplicador,
                            "ImporteRetencionesIB"                          => $Imp_MT_RetIB * $Multiplicador,
                            "ImporteOtrasRetencionesImpuestosNacionales"    => $Imp_MT_RetNac * $Multiplicador,
                            "ImporteOtrasRetencionesImpuestosProvinciales"  => $Imp_MT_RetProv * $Multiplicador,
                            "ImporteRetencionesTasaMunicipales"             => $Imp_MT_RetMuni * $Multiplicador,
                            "ImporteTotalComprobante"                       => $Imp_MT_Comprobante * $Multiplicador
                        );
                    }

                    $this->delete("Comprobante = $idComprobante");
                    $id = $this->insert($data);
                }
            }
        }
    }


    /**
     * Verifica si el libro de IVA esta cerrado
     *
     * @param int $idLibro      identificador del comprobante a verificar
     *
     * @return boolean
     */
    public function estaCerrado ($idLibro)
    {
        $M_LibroIVA = new Contable_Model_DbTable_LibrosIVA();
        $R = $M_LibroIVA->find($idLibro)->current();
        if (!$R) {
            throw new Rad_Db_Table_Exception('No se encuentra el libro de IVA.');
        }

        if ($R->Cerrado) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Sale si el Libro esta cerrado
     *
     * @param int $idLibro  identificador del libro a verificar
     *
     */
    public function salirSi_estaCerrado ($idLibro)
    {
        if ($this->estaCerrado($idLibro)) {
            throw new Rad_Db_Table_Exception("El comprobante se encuentra registrado en un libro de iva cerrado y no puede modificarse. Debe realizar los comprobantes cancelatorios que indica la ley.");
        }
        return $this;
    }

    /**
     * 	Quita un comprobante del libro de iva
     *  El registro no se elimina, sino que se ponen todos los valores en 0
     *
     *  @param Zend_Db_Table_Row $row El Row del comprobante que se quiere quitar de la cuenta corriente
     */
    public function quitarComprobante ($row)
    {

        if ($row->LibroIVA) {
            $this->salirSi_estaCerrado($row->LibroIVA);

            // Recupero el tipo de comprobante
            $M_TC = new Facturacion_Model_DbTable_TiposDeComprobantes();
            $R_TC = $M_TC->find($row->TipoDeComprobante)->current();

            $comprobantesEntrantes = array(1,8,11,13);

            if (!$R_TC) {
                throw new Rad_Db_Table_Exception('No se encuentra el Comprobante al intentar modificar el Libro de IVA.');
            } else {
                if (in_Array($R_TC->Grupo,$comprobantesEntrantes)) {
                    $this->delete("Comprobante =" . $row->Id);
                } else {
                    $data = array(
                        "ImporteNetoGravado105"                         => 0,
                        "ImporteNetoGravado210"                         => 0,
                        "ImporteNetoGravado270"                         => 0,
                        "ImporteNetoGravado5"                           => 0,
                        "ImporteNetoGravado25"                          => 0,                        
                        "ImporteIVA105"                                 => 0,
                        "ImporteIVA210"                                 => 0,
                        "ImporteIVA270"                                 => 0,
                        "ImporteIVA5"                                   => 0,
                        "ImporteIVA25"                                  => 0,
                        "ImporteImpuestosInternos"                      => 0,
                        "ImporteConceptosExentosONoGravados"            => 0,
                        "ImportePercepcionesIVA"                        => 0,
                        "ImportePercepcionesGanancias"                  => 0,
                        "ImportePercepcionesSuss"                       => 0,
                        "ImportePercepcionesIB"                         => 0,
                        "ImporteOtrasPercepcionesImpuestosNacionales"   => 0,
                        "ImporteOtrasPercepcionesImpuestosProvinciales" => 0,
                        "ImportePercepcionesTasaMunicipales"            => 0,
                        "ImporteTotalComprobante"                       => 0,
                        "ImporteRetencionesIVA"                         => 0,
                        "ImporteRetencionesGanancias"                   => 0,
                        "ImporteRetencionesSuss"                        => 0,
                        "ImporteRetencionesIB"                          => 0,
                        "ImporteRetencionesTasaMunicipales"             => 0,
                        "ImporteOtrasRetencionesImpuestosNacionales"    => 0,
                        "ImporteOtrasRetencionesImpuestosProvinciales"  => 0
                    );

                    $this->update($data, "Comprobante =" . $row->Id);
                }
            }
        }
    }
    public function fetchEsParaVenta($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = "LibrosIVADetalles.TipoDeLibro = 2";
        $where = $this->_addCondition($where, $condicion);
        return parent:: fetchAll($where, $order, $count, $offset);
    }
    public function fetchEsParaCompra($where = null, $order = null, $count = null, $offset = null)
    {
        $condicion = "LibrosIVADetalles.TipoDeLibro = 1";
        $where = $this->_addCondition($where, $condicion);
        return parent:: fetchAll($where, $order, $count, $offset);
    }

}
