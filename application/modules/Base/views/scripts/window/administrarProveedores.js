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
         // Proveedores -> Generales -> Direcciones
         this.gridProveedoresDirecciones = Ext.ComponentMgr.create(<?=$this->gridProveedoresDirecciones?>);
         this.gridProveedoresDirecciones.getTopToolbar().addButton([{xtype:'tbseparator'}, this.renderMenuMaps()]);

         // Proveedores -> Generales -> Telefonos
        this.gridProveedoresTelefonos = Ext.ComponentMgr.create(<?=$this->gridProveedoresTelefonos?>);
      
        // Proveedores -> Generales -> Emails
        this.gridProveedoresEmails = Ext.ComponentMgr.create(<?=$this->gridProveedoresEmails?>);

        // Proveedores -> Impositivo -> Ingresos Brutos
        this.gridProveedoresIngresosBrutos = Ext.ComponentMgr.create(<?=$this->gridProveedoresIngresosBrutos?>);
        this.gridProveedoresIngresosBrutos.store.on(
            'load',
            function() {
                var proveedor = this.grid.getSelectionModel().getSelected();
                if ( proveedor.data.IBProximosVencimientosCM05 > 0 ) {
                     this.publish('/desktop/showMsg/',{
                                  title: 'Atencion',
                                  msg: 'El formulario CM05 de Ingresos Brutos del Proveedor <br> se encuentra vencido o próximo a vencer.',
                                  buttons: Ext.Msg.OK,
                                  icon:    Ext.Msg.WARNING
                                  });
                }
            },
            this
        );


        // Proveedores -> Impositivo -> Conceptos Impositivos
        this.gridProveedoresConceptosImpositivos = Ext.ComponentMgr.create(<?=$this->gridProveedoresConceptosImpositivos?>);

        // Proveedores -> Impositivo -> Valores Conceptos Impositivos
        this.gridProveedoresValoresConceptosImpositivos = Ext.ComponentMgr.create(<?=$this->gridProveedoresValoresConceptosImpositivos?>);

        // Proveedores -> Cuentas Bancarias
        this.gridProveedoresCuentasBancarias = Ext.ComponentMgr.create(<?=$this->gridProveedoresCuentasBancarias?>);
        
        // Proveedores -> Precios -> Modalidades de Pagos
        this.gridProveedoresModalidadesDePagos = Ext.ComponentMgr.create(<?=$this->gridProveedoresModalidadesDePagos?>);
        
        // Proveedores -> Precios -> Registros de Precios
        this.gridProveedoresRegistrosDePrecios = Ext.ComponentMgr.create(<?=$this->gridProveedoresRegistrosDePrecios?>);
        this.gridProveedoresPreciosInformados  = Ext.ComponentMgr.create(<?=$this->gridProveedoresPreciosInformados?>);
        
        // Proveedores -> Cuenta Corriente
        this.gridProveedoresCuentasCorrientes  = Ext.ComponentMgr.create(<?=$this->gridProveedoresCuentasCorrientes?>);
        this.gridProveedoresCuentasCorrientes.store.on(
            'load',
            function() {
                this.gridProveedoresCuentasCorrientesSaldo.store.reload();
            },
            this
        );
        this.gridProveedoresCuentasCorrientesSaldo = Ext.ComponentMgr.create(<?=$this->gridProveedoresCuentasCorrientesSaldo?>);

        // Proveedores -> Cuenta Corriente Como Cliente
        this.gridProveedoresCuentasCorrientesComoCliente = Ext.ComponentMgr.create(<?=$this->gridProveedoresCuentasCorrientesComoCliente?>);
        this.gridProveedoresCuentasCorrientesComoCliente.store.on(
            'load',
            function() {
                this.gridProveedoresCuentasCorrientesComoClienteSaldo.store.reload();
            },
            this
        );
        this.gridProveedoresCuentasCorrientesComoClienteSaldo = Ext.ComponentMgr.create(<?=$this->gridProveedoresCuentasCorrientesComoClienteSaldo?>);

        // Proveedores -> Cuenta Corriente Como Proveedor
        this.gridProveedoresCuentasCorrientesComoProveedor = Ext.ComponentMgr.create(<?=$this->gridProveedoresCuentasCorrientesComoProveedor?>);
        this.gridProveedoresCuentasCorrientesComoProveedor.store.on(
            'load',
            function() {
                this.gridProveedoresCuentasCorrientesComoProveedorSaldo.store.reload();
            },
            this
        );
        this.gridProveedoresCuentasCorrientesComoProveedorSaldo = Ext.ComponentMgr.create(<?=$this->gridProveedoresCuentasCorrientesComoProveedorSaldo?>);

        this.gridProveedoresConceptosImpositivos.on(
            'saverelation',
            function(status) {
                if(status) {
                    this.gridProveedoresValoresConceptosImpositivos.store.reload();
                }
            },
            this
        );

    
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
                    items: [ this.gridProveedoresDirecciones, this.gridProveedoresTelefonos, this.gridProveedoresEmails]
                },
                {
                    xtype : 'tabpanel',
                    deferredRender : false,
                    title:'Impositivo',
                    activeTab: 0,
                    items: [ this.gridProveedoresIngresosBrutos, this.gridProveedoresConceptosImpositivos, this.gridProveedoresValoresConceptosImpositivos ]
                },

                this.gridProveedoresCuentasBancarias,
                {
                    xtype : 'tabpanel',
                    deferredRender : false,
                    title:'Precios',
                    activeTab: 0,
                    items: [ this.gridProveedoresModalidadesDePagos, this.gridProveedoresRegistrosDePrecios, this.gridProveedoresPreciosInformados ]
                },

                {
                    title: 'Cuenta Cte',
                    layout: 'border',
                    items: [ this.gridProveedoresCuentasCorrientes, this.gridProveedoresCuentasCorrientesSaldo ]
                },
                {
                    layout: 'border',
                    title: 'CC Cliente',
                    defaults: { layout: 'fit' },
                    items: [
                        {
                            region: 'north',
                            height: 380,
                            items: this.gridProveedoresCuentasCorrientesComoCliente
                        },
                        {
                            region: 'center',
                            items: this.gridProveedoresCuentasCorrientesComoClienteSaldo
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
                            items: this.gridProveedoresCuentasCorrientesComoProveedor
                        },
                        {
                            region: 'center',
                            items: this.gridProveedoresCuentasCorrientesComoProveedorSaldo
                        }
                    ]
                },                               
            ]
        }
    },
    
    /*
     *  Renderiza el menu de Google Maps
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
