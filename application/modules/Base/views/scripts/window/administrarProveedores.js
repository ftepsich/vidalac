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

    eventlaunch: function(ev) {
        this.createWindow();
        this.grid.store.load();
    },

    createWindow: function() {
        var win = app.desktop.getWindow(this.id+'-win');
        if ( !win ) {
            this.grid =  Ext.ComponentMgr.create(<?=$this->grid?>);
            this.grid.getTopToolbar().addButton({
                text: 'Listado',
                iconCls: 'x-btn-text-icon',
                icon: 'images/infGral16.png',
                handler: function() {
                    this.publish('/desktop/modules/Window/birtreporter', {
                        action: 'launch',
                        template: 'Inf_Clientes_o_Proveedores_Basico',
                        params: [{
                                    name:   'tipoPersona',
                                    value:  'Proveedor',
                                    type:   'string'
                                    }],
                        output: 'pdf',
                        width: 800,
                        height: 500
                    });
                }
            });
            win = this.create();
        }
        win.show();
    },

    create: function() {
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

    renderWindowContent: function () {
        return {
            layout : 'border',
            bodyStyle:'background:rgb(214, 214, 214)',
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
    createSecGrids: function() {
        this.gridTelefonos          = Ext.ComponentMgr.create(<?=$this->gridTelefonos?>);
        this.gridActiv              = Ext.ComponentMgr.create(<?=$this->gridActiv?>);
        this.gridCtaBan             = Ext.ComponentMgr.create(<?=$this->gridCtaBan?>);
        this.gridModPag             = Ext.ComponentMgr.create(<?=$this->gridModPag?>);
        this.gridListaPrecios       = Ext.ComponentMgr.create(<?=$this->gridListaPrecios?>);
        this.gridListaPreciosInf    = Ext.ComponentMgr.create(<?=$this->gridListaPreciosInf?>);
        this.gridCI                 = Ext.ComponentMgr.create(<?=$this->gridCI?>);
        this.gridCtaCte             = Ext.ComponentMgr.create(<?=$this->gridCtaCte?>);
        this.gridCtaCte.store.on(
            'load',
            function() {
                this.gridCtaCte_Saldo.store.reload();
            },
            this
        );
        this.gridCtaCte_Saldo       = Ext.ComponentMgr.create(<?=$this->gridCtaCte_Saldo?>);

        //CC solo cliente
        this.gridCtaCteC                 = Ext.ComponentMgr.create(<?=$this->gridCtaCteC?>);
        this.gridCtaCteC.store.on(
            'load',
            function() {
                this.gridCtaCte_SaldoC.store.reload();
            },
            this
        );
        this.gridCtaCte_SaldoC           = Ext.ComponentMgr.create(<?=$this->gridCtaCte_SaldoC?>);

        //CC solo proveedor
        this.gridCtaCteP                 = Ext.ComponentMgr.create(<?=$this->gridCtaCteP?>);
        this.gridCtaCteP.store.on(
            'load',
            function() {
                this.gridCtaCte_SaldoP.store.reload();
            },
            this
        );
        this.gridCtaCte_SaldoP           = Ext.ComponentMgr.create(<?=$this->gridCtaCte_SaldoP?>);

        this.gridCIP                = Ext.ComponentMgr.create(<?=$this->gridCIP?>);
        this.gridEmail              = Ext.ComponentMgr.create(<?=$this->gridEmail?>);
//        this.gridMarcas             = Ext.ComponentMgr.create(<?=$this->gridMarcas?>);
        this.gridDirecciones        = Ext.ComponentMgr.create(<?=$this->gridDirecciones?>);

        this.gridCI.on(
            'saverelation',
            function(status) {
                if(status) {
                    this.gridCIP.store.reload();
                }
            },
            this
        );

        this.gridDirecciones.getTopToolbar().addButton([{xtype:'tbseparator'}, this.renderMenuMaps()]);
    },

    renderTabs: function() {
        return {
            xtype: 'tabpanel',
            deferredRender : false,
            enableTabScroll: true,
            activeTab : 0,
            items: [{
                    xtype : 'tabpanel',
                    deferredRender : false,
                    title:'Generales',
                    activeTab: 0,
                    items: [ this.gridDirecciones, this.gridTelefonos, this.gridEmail]
                },
                {
                    xtype : 'tabpanel',
                    deferredRender : false,
                    title:'Impositivo',
                    activeTab: 0,
                    items: [ this.gridActiv, this.gridCI, this.gridCIP ]
                },

                this.gridCtaBan,
                {
                    xtype : 'tabpanel',
                    deferredRender : false,
                    title:'Precios',
                    activeTab: 0,
                    items: [ this.gridModPag, this.gridListaPrecios, this.gridListaPreciosInf ]
                },

                {
                    title: 'Cuenta Cte',
                    layout: 'border',
                    items: [ this.gridCtaCte, this.gridCtaCte_Saldo ]
                },
                {
                    layout: 'border',
                    title: 'CC Cliente',
                    defaults: { layout: 'fit' },
                    items: [
                        {
                            region: 'north',
                            height: 380,
                            items: this.gridCtaCteC
                        },
                        {
                            region: 'center',
                            items: this.gridCtaCte_SaldoC
                        }
                    ]
                },
                {
                    layout: 'border',
                    title: 'CC Proveedor',
                    defaults: { layout: 'fit' },
                    items: [
                        {
                            region: 'north',
                            height: 380,
                            items: this.gridCtaCteP
                        },
                        {
                            region: 'center',
                            items: this.gridCtaCte_SaldoP
                        }
                    ]
                },                               
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
                                msg: 'Este proveedor no tiene dirección cargada.',
                                modal: true,
                                icon: Ext.Msg.WARNING,
                                buttons: Ext.Msg.OK
                            });
                            return;
                        }

                        this.publish('/desktop/modules/js/commonApps/gmaps.js',
                        { action: 'searchAddress', address: selected.get('DireccionGoogleMaps') });
                    },
                    scope:   this,
                },
                {
                    text:    'Camino',
                    iconCls: 'x-btn-text-icon',
                    icon: 'images/map_go.png',
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
                                msg: 'Este proveedor no tiene dirección cargada.',
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