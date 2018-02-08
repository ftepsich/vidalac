Ext.ns( 'Apps' );

Apps.<?=$this->name?> = Ext.extend(RadDesktop.Module, {
    autoStart: true,
    title: '<?=$this->title?>',
    appChannel: '/desktop/modules<?=$this->url()?>',

    eventfind: function (ev) {
        this.createWindow();
        this.grid.store.load({
            params: this.grid.buildFilter(0, 'Id', ev.value),
            callback: function() {
                this.getSelectionModel().selectFirstRow();
            },
            scope: this.grid
        });
    },

    eventsearch: function (ev) {
        this.createWindow();
        var p = this.grid.builFilter(0, ev.field, ev.value);
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
                        params: [ {
                                    name:   'tipoPersona',
                                    value:  'Cliente',
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

    createSecGrids: function ()
    {
        this.gridTelefonos              = Ext.ComponentMgr.create(<?=$this->gridTelefonos?>);
        this.gridDirecciones            = Ext.ComponentMgr.create(<?=$this->gridDirecciones?>);
        this.gridEmails                 = Ext.ComponentMgr.create(<?=$this->gridEmails?>);
        this.gridActividades            = Ext.ComponentMgr.create(<?=$this->gridActividades?>);
        this.gridCuentasBancarias       = Ext.ComponentMgr.create(<?=$this->gridCuentasBancarias?>);
        this.gridZonasDeVentas          = Ext.ComponentMgr.create(<?=$this->gridZonasDeVentas?>);
        this.gridConceptosImpositivos   = Ext.ComponentMgr.create(<?=$this->gridConceptosImpositivos?>);
        this.gridConceptosImpositivosE  = Ext.ComponentMgr.create(<?=$this->gridConceptosImpositivosE?>);
        this.gridCtaCte                 = Ext.ComponentMgr.create(<?=$this->gridCtaCte?>);
        this.gridCtaCte.store.on(
            'load',
            function() {
                this.gridCtaCte_Saldo.store.reload();
            },
            this
        );
        this.gridCtaCte_Saldo           = Ext.ComponentMgr.create(<?=$this->gridCtaCte_Saldo?>);

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


        this.gridModalidadesDePago      = Ext.ComponentMgr.create(<?=$this->gridModalidadesDePago?>);
        this.gridConceptosImpositivos.on(
            'saverelation',
            function(status) {
                if(status) {
                    this.gridConceptosImpositivosE.store.reload();
                }
            },
            this
        );

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
                {
                    xtype : 'tabpanel',
                    deferredRender : false,
                    title: 'Impositivo',
                    enableTabScroll: true,
                    activeTab: 0,
                    items: [ this.gridConceptosImpositivos, this.gridConceptosImpositivosE, this.gridActividades ]
                },
                this.gridCuentasBancarias,
                {
                    layout: 'border',
                    title: 'Cuenta Corriente',
                    defaults: { layout: 'fit' },
                    items: [
                        {
                            region: 'north',
                            height: 380,
                            items: this.gridCtaCte
                        },
                        {
                            region: 'center',
                            items: this.gridCtaCte_Saldo
                        }
                    ]
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
                this.gridZonasDeVentas,
                this.gridModalidadesDePago

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
