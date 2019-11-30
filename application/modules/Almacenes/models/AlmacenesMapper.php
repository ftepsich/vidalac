
<?php
/**
 * Almacenes_Model_Almacenes_Exception
 *
 * @author Martin A. Santangelo
 * @class Almacenes_Model_Almacenes_Exception
 * @package Aplicacion
 * @subpackage Almacenes
 */
class Almacenes_Model_Almacenes_Exception extends Rad_Exception
{
    
}

/**
 * Almacenes_Model_Almacenes
 *
 * Esta clase provee el manejo de almacenes.
 * Esta clase no esta ligada a Model_Db_Table_Almacenes, sino que provee un
 * manejo de mas alto nivel sobre los almacenes
 * 
 * @author Martin A. Santangelo
 * @class Almacenes_Model_Almacenes
 * @package Aplicacion
 * @subpackage Almacenes
 */
class Almacenes_Model_AlmacenesMapper extends Rad_Mapper
{
    protected $_class = 'Almacenes_Model_DbTable_Almacenes';
    
    /**
     * Mueve un Mmi de una ubicacion (o Mmi directo) a otra (o a Almacen no rackeable)
     * 
     * @param int $almacenOrigen
     * @param int $almacenDestino
     * @param array $items 
     */
    public function moverMmis ($almacenOrigen, $almacenDestino, $items, $deposito = null)
    {
        $this->_model->moverMmis($almacenOrigen, $almacenDestino, $items, $deposito);
    }
    
    /**
     * 	Genera los mmis de un remitoArticulo dado. Teniendo en cuenta el almacen destino, la cantidad y el maximo por palet.
     *
     * 	@param Int $remitoArticulo
     * 	@param Int $almacenDestino
     * 	@param Int $cantidad
     * 	@param Int $cantidadPorMmi
     * 	@param Int $tipoPalet
     * 	@param String $loteNumero
     * 	@param String(date) $loteVencimiento
     * 	@param String(date) $loteElaboracion
     *  @return Array Ids de los mmis creados
     */
    public function paletizarRemitoArticulo ($remitoArticulo, $almacenDestino, $cantidad, $cantidadPorMmi, $tipoPalet, $loteNumero = null, $loteVencimiento = null, $loteElaboracion = null, $articuloVersion)
    {

        $remitosArticulos   = new Almacenes_Model_DbTable_RemitosArticulosDeEntradas(array(), true);
        $remitoArticulo     = $remitosArticulos->find($remitoArticulo)->current();

        if (!$remitoArticulo)  throw new Almacenes_Model_Almacenes_Exception('No se encontro el articulo del remito');

        $remito     = $remitoArticulo->findParentRow('Almacenes_Model_DbTable_RemitosDeEntradas');

        $articulos  = new Base_Model_DbTable_Articulos();
        $articulo   = $articulos->find($remitoArticulo->Articulo)->current();

        // No se encontro el articulo?
        if (!$articulo) throw new Almacenes_Model_Almacenes_Exception('No se encontro el articulo');

        // Recupero el ArticuloVersion
        $M_AV               = new Base_Model_DbTable_ArticulosVersiones();
        $R_AV               = $M_AV->fetchRow('Articulo = '.$remitoArticulo->Articulo);       
        $articuloVersion    = $R_AV->Id;

        // El articulo tiene configurado Otro tipo de manejo de Stock
        if ($articulo->TipoDeControlDeStock != 1) throw new Almacenes_Model_Almacenes_Exception('Este Articulo no lleva stock por almacenes');

        $lotes      = new Almacenes_Model_DbTable_Lotes();

        $almacenes  = new Almacenes_Model_DbTable_Almacenes();

        $almacen    = $almacenes->find($almacenDestino)->current();

        if (!$almacen) {
            throw new Almacenes_Model_Almacenes_Exception('No se encontro el almacen');
        } else if ($almacen->TipoDeAlmacen != 2) {
            throw new Almacenes_Model_Almacenes_Exception('Solo se pueden generar palets en los predepositos');
        }

        $mmis       = new Almacenes_Model_DbTable_Mmis();

        if (!(is_numeric($cantidadPorMmi) && $cantidadPorMmi > 0 )) {
            throw new Almacenes_Model_Almacenes_Exception('La cantidad debe ser un numero y ser mayor que 0');
        }

        $cantidadRemito = $remitoArticulo->Cantidad - $remitoArticulo->CantidadPaletizada;

        // La cantidad pendiente de paletizado es menor q lo que le digo q paletice?
        if ($cantidad) {
            if ($cantidadRemito < $cantidad) {
                throw new Almacenes_Model_Almacenes_Exception('La cantidad a paletizar es mayor que la cantidad disponible');
            }
            $cantidadRemito = $cantidad;
        }

        $return = array();       // TODO: Esto para que esta ??? no se ocupa (PK)
        $db = $mmis->getAdapter();
        $db->beginTransaction();

        // Si requiere lote verificamos que se hayan enviado los datos
        $requiereLote = false;

        if ($articulo->RequiereLote) {
            if (!$loteNumero || !$loteVencimiento || !$loteElaboracion) {
                throw new Almacenes_Model_Almacenes_Exception("Este Articulo requiere lote, por favor ingrese la informacion necesaria.");
            }
            $requiereLote = true;
        } else {
            if ($loteNumero || $loteVencimiento || $loteElaboracion) {
                if (!$loteNumero || !$loteVencimiento || !$loteElaboracion) {
                    throw new Almacenes_Model_Almacenes_Exception("No ingreso la informacion completa para el lote.");
                }
                $requiereLote = true;
            }
        }

        // Le pido al usuario que confirme la ejecucion
        $cantidadPaletsEnteros = floor($cantidadRemito / $cantidadPorMmi);
        $cantidadPaletfinal    = $cantidadRemito % $cantidadPorMmi;
        $fechaVencFormateada   = date('d/m/Y', strtotime(str_replace('T', '', $loteVencimiento)));
        $fechaElabFormateada   = date('d/m/Y', strtotime(str_replace('T', '', $loteElaboracion)));

        if ($cantidadPaletsEnteros == 0) {
            $confTxt = "Paletizara <b>$cantidadRemito</b> unidades en <b>1</b> palet de <b>$cantidadPaletfinal</b>";
        } else {
            $confTxt = "Paletizara <b>$cantidadRemito</b> unidades en <b>$cantidadPaletsEnteros</b> palet de <b>$cantidadPorMmi</b>";
            if ($cantidadPaletfinal) {
                $confTxt .= " y <b>1</b> palet de <b>$cantidadPaletfinal</b>";
            }
        }
        
        $confTxt .= "<hr>Lote: <b>$loteNumero</b><br>Vencimiento: <b>$fechaVencFormateada</b><br>Elaboracion: <b>$fechaElabFormateada</b><br><br>Desea continuar?";

        if (Rad_Confirm::confirm( $confTxt, _FILE_._LINE_, array('includeCancel' => false)) == 'yes') {
            
        }

        // terminadas las validaciones ejecuto...

        if ($requiereLote) {
            $where = 'Numero = ' . $db->quote($loteNumero);
            if ($remito->Persona) {
                $where .= ' AND Persona = ' . $remito->Persona;
            }
            $existeLote = $lotes->fetchRow($where);
            
            if (count($existeLote)) {
                if (strtotime($existeLote->FechaElaboracion) != strtotime($loteElaboracion))
                    throw new Rad_Exception('El lote ya existe pero no coinciden las fechas de Elaboracion.<br>'.
                        'Lote existente: ' . date('d/m/Y', strtotime($existeLote->FechaElaboracion)));
                if (strtotime($existeLote->FechaVencimiento) != strtotime($loteVencimiento))
                    throw new Rad_Exception('El lote ya existe pero no coinciden las fechas de Vencimiento.<br>'.
                        'Lote existente: ' . date('d/m/Y', strtotime($existeLote->FechaVencimiento)));
                $loteId = $existeLote->Id;
            } else {
                $lote = $lotes->createRow();
                $lote->Numero   = $loteNumero;
                $lote->Articulo = $articulo->Id;
                $lote->Cantidad = 0;
                $lote->Persona  = $remito->Persona;
                $lote->FechaElaboracion = substr($loteElaboracion, 0, 10);
                $lote->FechaVencimiento = substr($loteVencimiento, 0, 10);

                $loteId = $lote->save();
            }
        }

        while ($cantidadRemito) {
            if ($cantidadRemito >= $cantidadPorMmi) {
                $cantidad = $cantidadPorMmi;
                $cantidadRemito -= $cantidadPorMmi;
            } else {
                $cantidad = $cantidadRemito;
                $cantidadRemito = 0;
            }

            $mmi = $mmis->createRow();
            
            $mmi->Almacen                  = $almacenDestino;
            $mmi->Deposito                 = $almacen->Deposito;
            $mmi->Articulo                 = $articulo->Id;
            $mmi->ArticuloVersion          = $articuloVersion;
            $mmi->CantidadActual           = $cantidad;
            $mmi->CantidadOriginal         = $cantidad;
            $mmi->UnidadDeMedida           = $articulo->UnidadDeMedida;
            $mmi->RemitoArticulo           = $remitoArticulo->Id;
            $mmi->Descripcion              = $articulo->Descripcion;
            $mmi->FechaIngreso             = date("Y-m-d H:i:s");
            $mmi->HabilitadoParaProduccion = 0;

            if ($loteId) {
                $mmi->Lote = $loteId;
            }

            $mmi->MmiTipo = 1;
            $mmi->Ubicacion = null;
            $mmi->TipoDePalet = $tipoPalet;
                    
            $id[] = $mmi->save();

        }
        $db->commit();
        return $id;
    }

    /**
     * Asigna uno o mas Mmis a un remito
     */
    public function asignarARemito ($id, $remitoArticulo)
    {
        if ((int) $remitoArticulo != $remitoArticulo) {
            throw new Almacenes_Model_Almacenes_Exception('El paramentro remitoArticulo es erroneo');
        }
        if (!is_array($id)) {
            throw new Almacenes_Model_Almacenes_Exception('No se enviaron los parametros requeridos');
        }

        $M_Mmis = new Almacenes_Model_DbTable_Mmis();
        $M_Mmis->asignarMmiRemitoArticulo($id, $remitoArticulo);
    }

    /**
     * Desasigna uno o mas Mmis a un remito
     */
    public function desasignarARemito ($ArrayIds)
    {
        if (!is_array($ArrayIds)) throw new Almacenes_Model_Almacenes_Exception('No se enviaron los parametros requeridos');        

        $M_Mmis = new Almacenes_Model_DbTable_Mmis();
        $db = $M_Mmis->getAdapter();
        $db->beginTransaction();

        $mmis = $M_Mmis->find($ArrayIds);

        foreach ($mmis as $mmi) {
            $data['RemitoArticuloSalida'] = null;
            $M_Mmis->update($data,"Id=".$mmi->Id);
            //$mmi->RemitoArticuloSalida = null;
            //$mmi->save();
        }
        
        $db->commit();
    }

    /**
     * Mueve uno mas Mmis a un predeposito
     */
    public function moverMmisAPredeposito ($idRemito, $predeposito)
    {
        if (!$idRemito) throw new Almacenes_Model_Almacenes_Exception('No se enviaron los parametros requeridos');

        $M_Mmis = new Almacenes_Model_DbTable_Mmis();
        $M_Mmis->moverPredepositoRemito($idRemito, $predeposito);
    }

}
