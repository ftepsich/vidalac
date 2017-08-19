Ext.ns( 'Apps' );

Apps.<?=$this->name?> = Ext.extend(RadDesktop.Module, {
    autoStart: true,
    title: '<?=$this->title?>',
    appChannel: '/desktop/modules<?=$this->url()?>',

    requires: [
        '/direct/Facturacion/FacturaElectronica?javascript',
        '/direct/Facturacion/ImpresoraFiscal?javascript'
    ],

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
    },

    createWindow: function() {
        var win = app.desktop.getWindow(this.id+'-win');
        if ( !win ) {
            this.grid = Ext.ComponentMgr.create(<?=$this->grid?>);
            this.grid.flex=1;

            var sm = this.grid.getSelectionModel();
            sm.on('rowselect', function(t, i, r){
                switch(r.data.Adaptador) {
                    case '1': // preimpreso
                        this.card.getLayout().setActiveItem(0);
                    break;

                    case '2': // Factura Electronica
                        this.card.getLayout().setActiveItem(1);
                        this.fex = false;
                    break;
                        this.card.getLayout().setActiveItem(0);
                    case '3': // Nulo
                    break;

                    case '4': // Imp. Fiscal
                        this.card.getLayout().setActiveItem(2);
                    break;

                    case '5': // Factura Electronica Exportacion
                        this.card.getLayout().setActiveItem(3);
                        this.fex = true;
                    break;
                }
            },this);

            win = this.create();
        }
        win.show();
    },

    create: function()
    {
        defaultWinCfg = {
            id: this.id+'-win',
            title: this.title,
            iconCls: 'icon-grid',
            border:  false,
            shim: false,
            animCollapse: false,
            layout: 'fit',
            width: 700,
            height:450,
            items: [
                this.renderWindowContent()
            ]
        };
        return app.desktop.createWindow(defaultWinCfg);
    },

    renderWindowContent: function ()
    {
        return {
            border: false,
            layout: {
                type: 'hbox',
                align: 'stretch'
            },
            items: [
                this.grid,
                this.createTools()
            ]
        };
    },

    showWait: function(msg) {
         // Mensaje de Espera
         return app.desktop.showMsg({
            progressText: 'Espere por favor...',
            msg: msg,
            modal: true,
            closable: false,
            width: 300,
            wait: true,
            waitConfig: {interval:200}
        });
    },

    createTools: function(){
        this.card = new Ext.Panel({
            layout: 'card',
            title: 'Herramientas',
            activeItem: 0,
            width:230,
            layoutConfig: {
                padding: '10 10'
            },
            defaults: {
                bodyStyle: 'padding:10px'
            },
            items:[
                {
                    bodyStyle: 'text-align:center;border:1px solid silver;display: table-cell;vertical-align:middle',
                    html: 'Seleccione un Punto de Venta'
                },
                { // Menu factura electronica
                    defaults: {
                        bodyStyle:'padding:5px;'
                    },
                    layout: {
                        type: 'table',
                        columns: 2,
                        tableAttrs: {
                            style: {
                                width: '100%',
                                'text-align': 'center'
                            }
                        }
                    },
                    items: [{
                        xtype: 'button',
                        width: 100,
                        text: 'Estado Servicio',
                        icon: 'images/32/cash.png',
                        scale: 'large',
                        iconAlign: 'bottom',
                        scope: this,
                        handler: function() {
                            var wait = this.showWait('Verificando estado de Servidores Afip');
                            if (this.fex) {
                                Models.Facturacion_Model_FacturaElectronicaMapper.FEXDummy(
                                    function (result, e) {
                                    wait.hide();
                                    if (e.status) {
                                        var msgs = '';
                                        for (var property in result) {
                                            msgs += '<h1>' + property + ': ' + result[property]+'</h1><br>';
                                        }
                                        app.publish('/desktop/showMsg',{title: 'Verificación de Estado', msg: msgs, modal: true, width: 250});
                                    }
                                }, this);
                            } else {
                                Models.Facturacion_Model_FacturaElectronicaMapper.FEDummy(
                                    function (result, e) {
                                        wait.hide();
                                        if (e.status) {
                                            var msgs = '';
                                            for (var property in result) {
                                                msgs += '<h1>' + property + ': ' + result[property]+'</h1><br>';
                                            }
                                        app.publish('/desktop/showMsg',{title: 'Verificación de Estado', msg: msgs, modal: true, width: 250});
                                    }
                                }, this);
                            }
                        }
                    },{
                        xtype: 'button',
                        width: 100,
                        text: 'Consultar',
                        icon: 'images/32/cash.png',
                        scale: 'large',
                        iconAlign: 'bottom',
                        handler: function() {

                        }
                    }]
                },{ // Menu imp fiscal
                    defaults: {
                            bodyStyle:'padding:5px;'
                        },
                        layout: {
                            type: 'table',
                            columns: 2,
                            tableAttrs: {
                            style: {
                            width: '100%',
                            'text-align': 'center'
                            }
                        }
                    },
                    items: [{
                        xtype: 'button',
                        width: 100,
                        text: 'Estado',
                        icon: 'images/32/cash.png',
                        scale: 'large',
                        iconAlign: 'bottom',
                        scope: this,
                        handler: function() {
                            // Mensaje de Espera
                            var wait = this.showWait('Verificando estado de Impresora');

                            var s = this.grid.getSelectionModel().getSelected();

                            Models.Facturacion_Model_ImpresoraFiscalMapper.estado(s.data.Numero,
                            function (result, e) {
                                if (e.status) {
                                    wait.hide();
                                    app.publish('/desktop/showMsg',{title: 'Estado', msg: 'La impresora se encuentra lista', modal: true, width: 250});
                                }
                            }, this);
                        }
                    },{
                        xtype: 'button',
                        width: 100,
                        text: 'Cierre Z',
                        icon: 'images/32/cash.png',
                        scale: 'large',
                        scope: this,
                        iconAlign: 'bottom',
                        handler: function() {
                            var wait = this.showWait('Verificando estado de Impresora');

                            var s = this.grid.getSelectionModel().getSelected();

                            Models.Facturacion_Model_ImpresoraFiscalMapper.cierreDiario(s.data.Numero,
                                function (result, e) {
                                    if (e.status) {
                                        wait.hide();
                                        app.publish('/desktop/showMsg',{title: 'Cierre Diario', msg: 'Cierre efectuado correctamente.', modal: true, width: 250});
                                    }
                                },
                            this);
                        }
                    }]
                },
            ]
        });


        return this.card;
    }
});

new Apps.<?=$this->name?>();