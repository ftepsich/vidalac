<?php
/**
 * DatagatewayController
 * 
 * @author Martin A. Santangelo
 * @version 0.5
 *  
 */
//require_once 'Rad/AutoGridGateway/Controller/Action.php';
require_once 'Rad/DataGateway/Controller/Action.php';
/**
 * Esta clase sirve de interface de acceso a todos los modelos
 * Provee automaticamente la MetaData necesaria para Ext.AutoGrid
 *
 */
class TreeDataGatewayController extends Rad_TreeDataGateway_Controller_Action
{
    public function init ()
    {
        parent::init();
		/*
		// Si el sistema no esta en modo desarrollo activamos el cacheo de metadatos de las grillas.
        if (APPLICATION_ENV != 'development') {
            $bootstrap = $this->getInvokeArg('bootstrap');
            $cache = $bootstrap->getResource('fastCache');
            if ($cache) {
                Rad_AutoGridGateway_ModelMetadata::setCache($cache);
            }
        }
		*/
    }
}

