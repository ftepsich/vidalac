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
         // Clientes -> Generales -> Direcciones
        this.gridClientesDirecciones            = Ext.ComponentMgr.create(<?=$this->gridClientesDirecciones?>);
        this.gridClientesDirecciones.getTopToolbar().addButton([{xtype:'tbseparator'}, this.renderMenuMaps()]);

        // Clientes -> Generales -> Telefonos
        this.gridClientesTelefonos              = Ext.ComponentMgr.create(<?=$this->gridClientesTelefonos?>);

       // Clientes -> Generales -> Emails
        this.gridClientesEmails                 = Ext.ComponentMgr.create(<?=$this->gridClientesEmails?>);

        // Clientes -> Impositivo -> Ingresos Brutos
        this.gridClientesIngresosBrutos = Ext.ComponentMgr.create(<?=$this->gridClientesIngresosBrutos?>);

        // Clientes -> Impositivo -> Conceptos Impositivos
        this.gridClientesConceptosImpositivos   = Ext.ComponentMgr.create(<?=$this->gridClientesConceptosImpositivos?>);
        this.gridClientesIngresosBrutos.store.on(
            'load',
            function() {
                var cliente = this.grid.getSelectionModel().getSelected();
                if ( cliente.data.IBProximosVencimientosCM05 > 0 ) {
                     this.publish('/desktop/showMsg/',{
                                  title: 'Atencion',
                                  msg: 'El formulario CM05 de Ingresos Brutos del Cliente <br> se encuentra vencido o próximo a vencer.',
                                  buttons: Ext.Msg.OK,
                                  icon:    Ext.Msg.WARNING                                                                
                                  });
                }
            },
            this
        );

         // Clientes -> Impositivo -> Valores Conceptos Impositivos
        this.gridClientesValoresConceptosImpositivos  = Ext.ComponentMgr.create(<?=$this->gridClientesValoresConceptosImpositivos?>);

        // Clientes -> Cuentas Bancarias
        this.gridClientesCuentasBancarias       = Ext.ComponentMgr.create(<?=$this->gridClientesCuentasBancarias?>);

        // Clientes -> Cuenta Corriente
        this.gridClientesCuentasCorrientes                 = Ext.ComponentMgr.create(<?=$this->gridClientesCuentasCorrientes?>);
        this.gridClientesCuentasCorrientes.store.on(
            'load',
            function() {
                this.gridClientesCuentasCorrientesSaldo.store.reload();
            },
            this
        );
        this.gridClientesCuentasCorrientesSaldo           = Ext.ComponentMgr.create(<?=$this->gridClientesCuentasCorrientesSaldo?>);
        
        // Clientes -> Cuenta Corriente Como Cliente
        this.gridClientesCuentasCorrientesComoCliente                 = Ext.ComponentMgr.create(<?=$this->gridClientesCuentasCorrientesComoCliente?>);
        this.gridClientesCuentasCorrientesComoCliente.store.on(
            'load',
            function() {
                this.gridClientesCuentasCorrientesComoClienteSaldo.store.reload();
            },
            this
        );
        this.gridClientesCuentasCorrientesComoClienteSaldo           = Ext.ComponentMgr.create(<?=$this->gridClientesCuentasCorrientesComoClienteSaldo?>);


        // Clientes -> Cuenta Corriente Como Proveedor
        this.gridClientesCuentasCorrientesComoProveedor                 = Ext.ComponentMgr.create(<?=$this->gridClientesCuentasCorrientesComoProveedor?>);
        this.gridClientesCuentasCorrientesComoProveedor.store.on(
            'load',
            function() {
                this.gridClientesCuentasCorrientesComoProveedorSaldo.store.reload();
            },
            this
        );
        this.gridClientesCuentasCorrientesComoProveedorSaldo           = Ext.ComponentMgr.create(<?=$this->gridClientesCuentasCorrientesComoProveedorSaldo?>);

        // Clientes -> Zonas de Ventas
        this.gridZonasDeVentasClientes          = Ext.ComponentMgr.create(<?=$this->gridZonasDeVentasClientes?>);
        
        // Clientes -> Modalidad de Pago Clientes
        this.gridModalidadesDePagoClientes      = Ext.ComponentMgr.create(<?=$this->gridModalidadesDePagoClientes?>);

        this.gridClientesConceptosImpositivos.on(
            'saverelation',
            function(status) {
                if(status) {
                    this.gridClientesValoresConceptosImpositivos.store.reload();
                }
            },
            this
        );

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
                    items: [ this.gridClientesDirecciones, this.gridClientesTelefonos, this.gridClientesEmails ]
                },
                {
                    xtype : 'tabpanel',
                    deferredRender : false,
                    title: 'Impositivo',
                    enableTabScroll: true,
                    activeTab: 0,
                    items: [ this.gridClientesIngresosBrutos,this.gridClientesConceptosImpositivos, this.gridClientesValoresConceptosImpositivos ]
                },
                this.gridClientesCuentasBancarias,
                {
                    layout: 'border',
                    title: 'Cuenta Corriente',
                    defaults: { layout: 'fit' },
                    items: [
                        {
                            region: 'north',
                            height: 380,
                            items: this.gridClientesCuentasCorrientes
                        },
                        {
                            region: 'center',
                            items: this.gridClientesCuentasCorrientesSaldo
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
                            items: this.gridClientesCuentasCorrientesComoCliente
                        },
                        {
                            region: 'center',
                            items: this.gridClientesCuentasCorrientesComoClienteSaldo
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
                            items: this.gridClientesCuentasCorrientesComoProveedor
                        },
                        {
                            region: 'center',
                            items: this.gridClientesCuentasCorrientesComoProveedorSaldo
                        }
                    ]
                },                                
                this.gridZonasDeVentasClientes,

                this.gridModalidadesDePagoClientes

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
