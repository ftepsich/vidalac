<?php
require_once 'RAD/Window/Controler/Action.php';
require_once 'PHPExt/Javascript.php';

class Window_MapController extends Rad_Window_Controler_Action
{
    protected function initWindow ()
    {
        $this->getRequest()->getParam("direccion");
        
        $this->windowsObj->items = PhpExt_Javascript::variable("
        {
                    xtype: 'gmappanel',
                    zoomLevel: 16,
                    gmapType: 'map',
                    id: 'my_map',
                    mapConfOpts: ['enableScrollWheelZoom','enableDoubleClickZoom','enableDragging'],
                    mapControls: ['GSmallMapControl','GMapTypeControl','NonExistantControl'],
                    setCenter: {
                    	geoCodeAddr: 'Saavedra 5550, Santa fe, Santa fe, Argentina',
						//lat: -31.727280,
                        //lng: -60.532855,
                    },
                    markers: [{
                        //lat: -31.727280,
                        //lng: -60.532855,
                        geoCodeAddr: 'Saavedra 5550, Santa fe, Santa fe, Argentina',
                        marker: {title: 'Vidalac'},
                        listeners: {
                            click: function(e){
                                Ext.Msg.alert('Vidalac', 'Empresa Lactea');
                            }
                        }
                    }]
                }
        
        ");		
        
        $this->title = "Mapa";
        $this->windowsObj->setWidth(900);		
        $this->windowsObj->setMinWidth(800);		
        $this->windowsObj->setHeight(500);
        $this->windowsObj->setMinHeight(500);
        $this->windowsObj->setBorder(false);
        $this->windowsObj->layout = "fit";
        $this->windowsObj->stateId = PhpExt_Javascript::variable("id");
    }

}

