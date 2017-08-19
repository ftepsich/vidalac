<?php
/**
 * DatagatewayController
 *
 *
 * @copyright SmartSoftware Argentina
 * @package Rad
 * @subpackage Desktop
 * @author Martin Alejandro Santangelo
 */

require_once 'Rad/GridDataGateway/Controller/Action.php';

/**
 * DatagatewayController
 *
 * Esta clase sirve de interface de acceso a todos los modelos
 * Provee automaticamente la MetaData necesaria para las grillas Extjs
 *
 * @copyright SmartSoftware Argentina
 * @package Rad
 * @subpackage Desktop
 * @author Martin Alejandro Santangelo
 */
class DatagatewayController extends Rad_GridDataGateway_Controller_Action
{
    public function init ()
    {
        parent::init();
		// Si el sistema no esta en modo desarrollo activamos el cacheo de metadatos de las grillas.
        if (APPLICATION_ENV != 'development') {
            $bootstrap = $this->getInvokeArg('bootstrap');
            $cache = $bootstrap->getResource('fastCache');
            if ($cache) {
                Rad_GridDataGateway_ModelMetadata::setCache($cache);
            }
        }
    }
}

