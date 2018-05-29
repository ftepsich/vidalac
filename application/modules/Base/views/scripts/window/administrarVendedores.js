Ext.ns( 'Apps' );

Apps.<?=$this->name?> = Ext.extend(RadDesktop.Module, {
    autoStart: true,
    title: '<?=$this->title?>',
    appChannel: '/desktop/modules<?=$this->url()?>',
	
    eventfind: function (ev) {
        this.createWindow();
        var p = this.grid.buildFilter(0, 'Id', ev.value);
        this.grid.store.load({params:p});
    },
	
    eventsearch: function (ev) {
        this.createWindow();
        var p = this.grid.buildFilter(0, ev.field, ev.value);
        this.grid.store.load({params:p});
    },
	
    eventlaunch: function(ev)
    {
        this.createWindow();
    },

    createWindow: function()
    {
        var win = app.desktop.getWindow(this.id+'-win');
        if ( !win ) {
            this.grid =  Ext.ComponentMgr.create(<?=$this->grid?>);
            win = this.create();
        }
        win.show();
    },
	
    create: function()
    {
        this.createSecGrids();
        defaultWinCfg = {
            id: this.id+'-win',
            title: this.title,
            iconCls: 'icon-grid',
            border:  false,
            shim: false,
            animCollapse: false,
            layout: 'fit',
            width: 1000,
            height:500,
            items: [
                this.renderWindowContent()
            ]
        };
        return app.desktop.createWindow(defaultWinCfg);
    },
	
    renderWindowContent: function ()
    {
        return {
            layout : 'border',
            border : false,
            items : [{
                    region : 'west',
                    layout: 'fit',
                    width : 320,
                    split: true,
                    items: [
                        this.grid
                    ]
                },{
                    region : 'center',
                    layout: 'fit',
                    items: [
                        this.renderTabs()
                    ]
                }]
        }
    },
	
    createSecGrids: function ()
    {
        this.gridTelefonos              = Ext.ComponentMgr.create(<?php echo $this->gridTelefonos ?>);
        this.gridDirecciones            = Ext.ComponentMgr.create(<?php echo $this->gridDirecciones ?>);
	    this.gridEmails                 = Ext.ComponentMgr.create(<?php echo $this->gridEmails ?>);
        this.gridCtasBancarias          = Ext.ComponentMgr.create(<?php echo $this->gridCtasBancarias ?>);
		this.gridZonasPorVendedores     = Ext.ComponentMgr.create(<?php echo $this->gridZonasPorVendedores ?>);

        
        this.gridDirecciones.getTopToolbar().addButton([{xtype:'tbseparator'}, this.renderMenuMaps()]);
    },
	
    renderTabs: function ()
    {
        return {
            xtype: 'tabpanel',
            deferredRender : false,
            activeTab : 0,
            enableTabScroll: true,
            items: [
                {
                    xtype: 'tabpanel',
                    deferredRender: false,
                    title: 'Generales',
                    enableTabScroll: true,
                    activeTab: 0,
                    items: [ this.gridDirecciones, this.gridTelefonos, this.gridEmails ]
                },
                this.gridCtasBancarias,
				this.gridZonasPorVendedores
				
            ]
        }
    },
	
    /*
     *	Renderiza el menu de Google Maps
     */
    renderMenuMaps: function ()
    {
        return {
            text: 'Mapas',
            iconCls: 'x-btn-text-icon',
            icon: 'images/map.png',
            menu: [
                {
                    text:    'Buscar',
                    iconCls: 'x-btn-text-icon',
                    icon: 'images/map_magnify.png',
                    handler: function() {
                        selected = this.gridDirecciones.getSelectionModel().getSelected();
                        if (!selected) {
                            Ext.Msg.show({
                                title: 'Atencion',
                                msg: 'Seleccione una direccion',
                                modal: true,
                                icon: Ext.Msg.WARNING,
                                buttons: Ext.Msg.OK
                            });
                            return;
                        }
                        if (!selected.get('DireccionGoogleMaps')) {
                            Ext.Msg.show({
                                title: 'Atencion',
                                msg: 'Este cliente no tiene dirección cargada.',
                                modal: true,
                                icon: Ext.Msg.WARNING,
                                buttons: Ext.Msg.OK
                            });
                            return;
                        }
						
                        this.publish('/desktop/modules/js/commonApps/gmaps.js',
                            { action: 'searchAddress', address: selected.get('DireccionGoogleMaps') });
                    },
                    scope:   this
                },
                {
                    text:    'Camino',
                    iconCls: 'x-btn-text-icon',
                    icon: 'images/map_go.png',
                    handler: function() {
                        selected = this.gridDirecciones.getSelectionModel().getSelected();
                        if (!selected) {
                            Ext.Msg.show ({
                                title: 'Atencion',
                                msg: 'Seleccione una direccion',
                                modal: true,
                                icon: Ext.Msg.WARNING,
                                buttons: Ext.Msg.OK
                            });
                            return;
                        }
                        if (!selected.get('DireccionGoogleMaps')) {
                            Ext.Msg.show ({
                                title: 'Atencion',
                                msg: 'Este cliente no tiene dirección cargada.',
                                modal: true,
                                icon: Ext.Msg.WARNING,
                                buttons: Ext.Msg.OK
                            });
                            return;
                        }
						
                        this.publish('/desktop/modules/js/commonApps/gmaps.js',
                            { action: 'searchPath', srcAddress: 'Saavedra 5580, Santa Fe, Santa Fe, Argentina', dstAddress: selected.get('DireccionGoogleMaps') });
                    },
                    scope:   this,
                }
            ]
        }
    }
});

new Apps.<?=$this->name?>();