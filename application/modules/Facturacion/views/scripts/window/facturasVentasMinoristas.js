Ext.ns( 'Apps' );

Apps.<?=$this->name?> = Ext.extend(RadDesktop.Module, {
    title: '<?=$this->title?>',
    appChannel: '/desktop/modules<?=$this->url()?>',
    requires: [
        '/direct/Facturacion/TicketFacturas?javascript',
        '/direct/Facturacion/RecibosFicticiosDetalles?javascript'
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

    eventlaunch: function (ev) {
        this.createWindow();
        this.grid.store.load();
    },

    createWindow: function () {
        var win = app.desktop.getWindow(this.id+'-win');
        if (!win) {
            this.createGrid();
            this.createEditorWindow();
            win = this.create();

            // seteo los drop de cobros
            this.dropCobros = new Ext.dd.DropTarget(this.gridRD.getView().scroller.dom, {
                ddGroup : 'grillassistema',
                groups :{cheques: true},
                scope: this,
                notifyDrop : function(dd, e, data) {
                    var IdRecibo = this.scope.idComprobantePago;
                    var ids = [];
                    var action = null;

                    switch (data.grid.id)
                    {
                        case this.scope.gridCheques.id:
                            action = 'agregarCheques';
                            break;
                    }
                    if (action != null) {
                        for(var i = 0, len = data.selections.length; i < len; i++) {
                            ids[i] = data.selections[i].data['Id'];
                        }
                        Models.Facturacion_Model_RecibosFicticiosDetallesMapper[action](IdRecibo, ids, function (result, e) {
                            if (e.status) {
                                this.gridRD.store.reload();
                            }
                        }, this.scope);
                    }
                }
            });
        }
        win.show();
    },

    /**
     * Crea la ventana del Abm
     */
    createEditorWindow: function () {
        // Formulario principal
        this.form   = Ext.ComponentMgr.create(<?=$this->form?>);

        // despues del submit exitoso se pasa al paso 1 del wizard
        this.form.on(
            'actioncomplete',
            function() {
                    this.wizard.setActiveItem(1);
            },
            this
        );

        this.addExtraListeners();

        this.createWizard();

        this.grid.abmWindow = app.desktop.createWindow({
            autoHideOnSubmit: false,
            width: 1000,
            height: 600,
            border: false,
            layout: 'fit',
            ishidden: true,
            title: 'Facturación',
            plain: true,
            items: this.wizard,
            form: this.form,
            grid: this.grid,
            getForm: function () {
                return this.form;
            }
        }, Rad.ABMWindow);

        // si la ventana se esconde volvemos al primer paso
        this.grid.abmWindow.on('hide', function () {
            this.wizard.setActiveItem(0);
            id = this.form.getForm().findField('Id').getValue();
            if (id != 0) {
                Models.Facturacion_Model_TicketFacturasMapper.get(id, function (result, e) {
                    if (e.status) {
                        this.form.updateGridStoreRecord(result);
                    }
                }, this);
            }
        }, this);

        this.grid.abmWindow.on('show', function () {
            this.updateEstadoDeCuentaPorCliente();
        }, this);
    },

    /**
     * Agrega la logica adicional para los campos del formulario
     */
    addExtraListeners: function() {
        var form = this.form.getForm();

        // Campo Persona (Cliente)
        form.findField('Persona').on('select', function (combo, record, index) {
            this.updateEstadoDeCuentaPorCliente(record.data.Id);
            // Setea el tipo de comprobante segun la modalidad de iva y el tipo de comprobante
            var form = this.form.getForm();
            var combo = form.findField('TipoDeComprobante');
            var tipo = combo.getValue();
            if (!tipo) return;
            if (record.data.ModalidadIva != 3) {
                if (tipo < 29) {
                    combo.setValue(25);
                } else if (tipo <= 32 ) {
                    combo.setValue(30);
                } else if (tipo <= 40 ) {
                    combo.setValue(38);
                }
            } else {
                if (tipo < 29) {
                    combo.setValue(24);
                } else if (tipo <= 32 ) {
                    combo.setValue(29);
                } else if (tipo <= 40 ) {
                    combo.setValue(37);
                }
            }

            var punto = form.findField('Punto').getValue();
            var tipo = combo.getValue();
            //var tipo = form.findField('TipoDeComprobante').getValue();
            Models.Facturacion_Model_TicketFacturasMapper.recuperarProximoNumero(punto, tipo, function(result, e) {
                if (e.status) {
                    form.findField('Numero').setValue(result);
                }
            });
        }, this);
    },

    /**
     * Updatea el template de estado de cuenta del cliente pasado por parametro o seleccionado
     */
    updateEstadoDeCuentaPorCliente: function (idCliente) {
        if (!idCliente) {
            var idCliente = this.form.getForm().findField('Persona').getValue();
        }

        Models.Facturacion_Model_TicketFacturasMapper.getEstadoDeCuentaPorCliente(idCliente, function (result, e) {
            if (e.status) {
                var template = Ext.getCmp('TicketFacturasWizard_DetalleCuentasTemplate');
                var detailEl = template.body;
                template.overwrite(detailEl, result);
                detailEl.slideIn('l', { stopFx: true, duration: .2 });
            }
        }, this);
    },

    updateMontoAPagar: function(){
        Models.Facturacion_Model_TicketFacturasMapper.getTotal(this.form.record.data.Id, function(result, e) {
            if (e.status) {
                this.montoAPagar = parseFloat(result).toFixed(2);

                Ext.select('#fmCobroTotal').update('$ '+this.montoAPagar);

                this.refreshSaldo();
            }
        }, this);
    },
    refreshSaldo: function(){
        if (!this.montoAPagar) return;
        var montoCobrado = parseFloat(this.gridRD.plugins[0].totales.PrecioUnitario).toFixed(2);

        if (!montoCobrado) montoCobrado = 0;

        var saldo = (this.montoAPagar - montoCobrado).toFixed(2);
        Ext.select('#fmCobroSaldo').update('$ '+saldo);
    },

    /**
     * Creo el wizard del abm
     */
    createWizard: function () {
        this.createSecondaryGrids();

        // Creamos el Obj wizard
        this.wizard = new Rad.Wizard({
            border: false,
            defaults: {border:false},
            items: [
                this.renderWizardItem('Ingresar los datos del comprobante', '', this.form),
                this.renderWizardItem('Agregar art&iacute;culos y completar datos', '', this.gridArticulos),
                this.renderWizardItem('Ingresar los conceptos impositivos', '', this.gridCI),
                this.renderWizardItem('Pago', 'Ingresar los pagos del Cliente', this.renderPaso3()),
                this.renderWizardItem('Finalizar comprobante', '', this.renderPaso4())
            ]
        });

        // Logica del Wizard
        this.wizard.on(
            'activate',
            function (i) {
                switch (i) {
                    // cargamos la grilla de articulos para el paso 2
                    case 1:
                        var form        = this.form.getForm();
                        var id          = this.form.record.data.Id;
                        var detailGrid  = {remotefield: 'Comprobante', localfield: 'Id'};
                        this.gridArticulos.parentForm = this.form;
                        this.gridArticulos.loadAsDetailGrid(detailGrid, id);

                        break;
                    // cargamos la grilla de conceptos para el paso 3
                    case 2:
                        var form        = this.form.getForm();
                        var id          = this.form.record.data.Id;
                        var detailGrid  = {remotefield: 'ComprobantePadre', localfield: 'Id'};
                        this.gridCI.parentForm = this.form;
                        this.gridCI.loadAsDetailGrid(detailGrid, id);
                        break;
                    case 3:
                        detailGrid = {remotefield:'Comprobante', localfield:'Id'};
                        // cargo grilla de cobros
                        this.gridRD.loadAsDetailGrid(detailGrid, this.idComprobantePago);

                        // traigo el monto a pagar
                        break;
                    // vista previa de reporte
                    case 4:
                        var id          = this.form.getForm().findField('Id').getValue();
                        //var urlReporte    = '/Window/birtreporter/report?template=ComprobanteFactura&output=html&id=' + id;
                        var urlReporte  = '/Window/birtreporter/report?template=Comp_FacturaEmitida_Ver&output=html&id=' + id;
                        Ext.getCmp('impresionFacturaVentaHtml').setSrc(urlReporte);
                        break;
                }
            },
            this
        );

        this.wizard.on('next', function (i) {
            switch(i) {
                case 0:
                    this.form.submit();
                    return false;
                case 1:
                    // Inserta los conceptos desde el controlador
                    // var id = this.form.getForm().findField('Id').getValue();
                    var id = this.form.record.data.Id;

                    if(this.form.record.data.Cerrado != 1) {
                        Models.Facturacion_Model_TicketFacturasMapper.insertarConceptosDesdeControlador(id, function (result, e) {
                            if (e.status)
                                this.wizard.setActiveItem(2);
                        }, this);
                    } else {
                        this.wizard.setActiveItem(2);
                    }



                    return false;
                    break;
                case 2:
                    // Controlo que no hayan conceptos con monto menor o igual a 0 (cero)
                    var gotoPage;
                    var d = this.form.record.data;
                    if(d.CondicionDePago != 1 && d.TipoDeComprobante > 31)
                        gotoPage = 3;
                    else
                        gotoPage = 4;

                    var idFact = this.form.record.data.Id;

                    goPage = function() {
                        if (gotoPage == 3)
                        {
                            Models.Facturacion_Model_TicketFacturasMapper.getIdComprobantePago(idFact, function(result, e){
                                if (e.status) {
                                    this.idComprobantePago = result;
                                    this.updateMontoAPagar();
                                    this.wizard.setActiveItem(gotoPage);
                                }
                            }, this);
                        } else {
                            this.wizard.setActiveItem(gotoPage);
                        }
                    };

                    if(this.form.record.data.Cerrado != 1) {

                        Models.Facturacion_Model_TicketFacturasMapper.getControlTotalConcepto(idFact, function(result, e) {
                            if (e.status)
                                goPage.call(this);
                        }, this);
                    } else {
                        goPage.call(this);
                    }
                    return false;
                    break;
            }
        }, this);

        this.wizard.on('prev', function(i){
            if (i == 4) {
                var gotoPage;
                if(this.form.record.data.CondicionDePago != 1 && d.TipoDeComprobante > 31)
                    gotoPage = 3;
                else
                    gotoPage = 2;

                this.wizard.setActiveItem(gotoPage);
                return false;
            }

        },this);

        this.wizard.on('finish', function (i) {
            this.grid.abmWindow.closeAbm();
        }, this);
    },

    /**
     * Creamos las grillas secundarias y su logica
     */
    createSecondaryGrids: function () {

        this.gridCheques = Ext.ComponentMgr.create({
            "xtype": "radgridpanel",
            "filters": true,
            "url": "/default/datagateway",
            "model": "ChequesDeTerceros",
            "module": "Base",
            "forceFit": true,
            "stateful": false,
            "iniSection": "muyreducido",
            "loadAuto": false,

            "viewConfig": {
                forceFit: true,
                enableRowBody: true,
                showPreview: true,
                getRowClass: function (record, rowIndex, p, store) {
                    var fecha;
                    var numero;
                    if (record.data.FechaDeEmision) fecha = record.data.FechaDeEmision.dateFormat('d/m/Y');
                    else fecha = ' - ';
                    if (record.data.Numero) numero = record.data.Numero;
                    else numero = ' - ';

                    if (this.showPreview) {
                        p.body = '<p><b>Emitido:</b> ' + fecha + '<b><br>Emisor:  </b> ' + record.data.Persona_cdisplay + '</p>';
                        return 'x-grid3-row-expanded';
                    }
                    return 'x-grid3-row-collapsed';
                }
            },
            "ddText": "{0} Cheque(s) seleccionado(s)",
            "fetch": "Ingresados",
            buttons:[{
                text: 'Cargar',
                iconCls: 'add',
                iconAlign: 'bottom',
                handler: function () {
                    this.publish('/desktop/modules/Window/abm/index/model/ChequesDeTerceros/m/Base', {
                        action: 'launch'
                    });
                },
                scope: this
            },{
                xtype: 'button',
                text: 'Volver',
                icon: 'images/arrow_right.png',
                iconAlign: 'bottom',
                handler: function() {
                    this.pagosCard .resetTitle();
                    this.pagosCard .getLayout().setActiveItem(0);
                },
                scope: this
            }]
        });

        // Articulos de la factura
        this.gridArticulos = Ext.ComponentMgr.create(<?=$this->gridArticulos?>);
        this.gridArticulos.onAbmWindowShow = function() {
            var panel = Ext.getCmp('TicketFacturasArticulosForm');
            var selected = this.getSelectionModel().getSelected();
            if (selected != undefined) {

                switch (selected.data.ArticulosTipo) {
                    case 1:
                        panel.cambiarTipo('0');
                        break;
                    case 2:
                        panel.cambiarTipo('2');
                        break;
                    case 3:
                        panel.cambiarTipo('1');
                        break;
                    default:
                        panel.cambiarTipo('0');
                }
            } else {
                panel.cambiarTipo('0');
            }
        }

        //this.gridArticulos.abmForm.reloadGridOnClose = true;

        // Conceptos imp
        this.gridCI = Ext.ComponentMgr.create(<?=$this->gridCI?>);

        // Recibos Ficticios Detalls (cobros)
        this.gridRD = Ext.ComponentMgr.create(<?=$this->gridRD?>);

        this.gridRD.store.on({
            add:    { scope: this, fn: this.refreshSaldo },
            remove: { scope: this, fn: this.refreshSaldo },
            load:   { scope: this, fn: this.refreshSaldo }
        });

    },

    /**
     * Pagos
     */
    renderPaso3: function() {
        var pagosCard;

        pagosCard = new Ext.Panel({

                layout: 'card',
                title: 'Medios de Pago',
                activeItem: 0,
                flex: 1.2,
                resetTitle: function () {
                    this.setTitle('Medios de Pago');
                },
                items:[
                    { // Menu modalidades de pago
                        layout: {
                            type: 'table',
                            columns: 2,

                            tableAttrs: {
                                cellspacing:6,
                                style: {
                                    width: '100%',
                                    'text-align': 'center'
                                }
                            }
                        },
                        items: [{
                            xtype: 'button',
                            width: 120,
                            text: 'Efectivo',
                            icon: 'images/32/cash.png',
                            scale: 'large',
                            iconAlign: 'bottom',
                            handler: function(btn, e) {
                                pagosCard.setTitle(btn.text);
                                pagosCard.getLayout().setActiveItem(2);
                            }
                        },{
                            xtype: 'button',
                            width: 120,
                            layout : 'fit',
                            text: 'Tarjeta',
                            icon: 'images/32/credit_cards_48.png',
                            scale: 'large',
                            iconAlign: 'bottom',
                            handler: function(btn, e) {
                                pagosCard.setTitle(btn.text);
                                pagosCard.getLayout().setActiveItem(1);
                            }
                        },{
                            xtype: 'button',
                            width: 120,
                            layout : 'fit',
                            text: 'Cheques',
                            icon: 'images/32/check-icon.png',
                            scale: 'large',
                            iconAlign: 'bottom',
                            handler: function(btn, e) {
                                pagosCard.setTitle(btn.text);
                                this.gridCheques.store.load();
                                pagosCard.getLayout().setActiveItem(3);

                            },
                            scope: this
                        },{
                            xtype: 'button',
                            width: 120,
                            layout : 'fit',
                            text: 'Borrar',
                            icon: 'images/32/cancel.png',
                            scale: 'large',
                            iconAlign: 'bottom',
                            handler: function(btn, e) {
                                this.publish('/desktop/showMsg/',{
                                    title:'Borrar',
                                    msg: '¿Está seguro que desea borrar los pagos seleccionados?',
                                    buttons: Ext.Msg.YESNO,
                                    fn: function(btn) {
                                        if(btn == 'yes') {
                                            records = this.gridRD.getSelectionModel().getSelections();
                                            this.gridRD.store.remove(records);
                                            this.gridRD.fireEvent('afterdeleterows', records);
                                        }
                                    },
                                    scope:this,
                                    icon: Ext.MessageBox.QUESTION
                                });
                            },
                            scope: this
                        }]
                    },{ // Pagos Tarjeta
                        bodyStyle: 'padding:10px',
                        layout:'form',
                        layoutConfig:{
                            labelAlign: 'top'
                        },
                        items:[
                            {
                                id: 'facturaVMinoristaPagoTarjeta',
                                xtype: 'numberfield',
                                anchor: '100%',
                                fieldLabel: 'Monto',
                                allowNegative: false,
                            }, {
                                id: 'facturaVMinoristaNumeroTarjeta',
                                xtype: 'numberfield',
                                anchor: '100%',
                                fieldLabel: 'Numero de Tarjeta',
                                allowNegative: false,
                            },
                            {
                                xtype:"xcombo",
                                id: 'facturaVMinoristaTipoTarjeta',
                                anchor:'100%',
                                minChars:3,
                                displayField:"Descripcion",
                                autoSelect:true,
                                selectOnFocus:true,
                                forceSelection:true,
                                forceReload:true,
                                loadingText:"Cargando...",
                                lazyRender:false,
                                searchField:"Descripcion",
                                typeAhead:false,
                                valueField:"Id",
                                store: new Ext.data.JsonStore({
                                    id: 0,
                                    root: 'rows',
                                    totalProperty: 'count',
                                    "url":     "datagateway\/combolist\/model\/TarjetasDeCreditoMarcas\/m\/Facturacion\/fetch\/Activas",
                                    fields: ['Id', 'Descripcion']
                                }),
                                pageSize:"10",
                                editable:true,
                                autocomplete:true,
                                allowBlank:true,
                                fieldLabel:"Tipo"
                            }, {
                                id: 'facturaVMinoristaCuotas',
                                xtype: 'numberfield',
                                decimalPrecision: 0,
                                maxValue: 36,
                                minValue:1,
                                anchor: '100%',
                                fieldLabel: 'Cuotas',
                                value: 1,
                                allowNegative: false,
                            },
                        ],
                        buttons:[{
                            xtype: 'button',
                            text: 'Cobrar',
                            icon: 'images/32/cash.png',
                            scale: 'large',
                            iconAlign: 'bottom',
                            scope: this,
                            handler: function() {
                                var monto   = Ext.getCmp('facturaVMinoristaPagoTarjeta').getValue();
                                var tarjeta = Ext.getCmp('facturaVMinoristaNumeroTarjeta').getValue();
                                var tipo    = Ext.getCmp('facturaVMinoristaTipoTarjeta').getValue();
                                var ctas    = Ext.getCmp('facturaVMinoristaCuotas').getValue();
                                Models.Facturacion_Model_RecibosFicticiosDetallesMapper.agregarTarjeta(this.idComprobantePago, monto, ctas, tarjeta, tipo, function (result, e) {
                                    if (e.status) {
                                        this.gridRD.store.reload();
                                        pagosCard.resetTitle();
                                        pagosCard.getLayout().setActiveItem(0);
                                    }
                                }, this);
                            }
                        },{
                            xtype: 'button',
                            text: 'Cancelar',
                            icon: 'images/32/cancel.png',
                            scale: 'large',
                            iconAlign: 'bottom',
                            handler: function() {
                                pagosCard.resetTitle();
                                pagosCard.getLayout().setActiveItem(0);
                            }
                        }]
                    },{ // Pagos Efectivo
                        bodyStyle: 'padding:10px',
                        layout:'form',
                        layoutConfig:{
                            labelAlign: 'top'
                        },
                        items:[
                            {
                                id: 'facturaVMinoristaPagoEfectivo',
                                xtype: 'numberfield',
                                anchor: '100%',
                                fieldLabel: 'Monto',
                                allowNegative: false,
                            }
                        ],
                        buttons:[{
                            xtype: 'button',
                            text: 'Cobrar',
                            icon: 'images/32/cash.png',
                            scale: 'large',
                            iconAlign: 'bottom',
                            scope: this,
                            handler: function() {
                                var monto = Ext.getCmp('facturaVMinoristaPagoEfectivo').getValue();

                                if (!monto) {
                                    window.app.publish('/desktop/showError', 'Ingrese un monto.');
                                    return;
                                }

                                Models.Facturacion_Model_RecibosFicticiosDetallesMapper.agregarEfectivo(this.idComprobantePago, monto, function (result, e) {
                                    if (e.status) {
                                        this.gridRD.store.reload();
                                        pagosCard.resetTitle();
                                        pagosCard.getLayout().setActiveItem(0);
                                    }
                                }, this);
                            }
                        },{
                            xtype: 'button',
                            text: 'Cancelar',
                            icon: 'images/32/cancel.png',
                            scale: 'large',
                            iconAlign: 'bottom',
                            handler: function() {
                                pagosCard.resetTitle();
                                pagosCard.getLayout().setActiveItem(0);
                            }
                        }]
                    },
                    this.gridCheques
                ]
        });

        this.pagosCard = pagosCard;

        return {
            layout: {
                type: 'hbox',
                pack: 'start',
                align: 'stretch'
            },

            items:[
                this.gridRD,
                {
                    width: 270,
                    layout: {
                        type: 'vbox',
                        pack: 'start',
                        align: 'stretch'
                    },
                    items: [
                        pagosCard,
                        {
                            title:'Total',
                            height:80,
                            bodyStyle:'text-align: center;vertical-align: middle;line-height: 55px;font-size:32px;font-family: arial;padding-right: 10px;',
                            html:'<span id="fmCobroTotal">$ 0.0</span>'
                        },
                        {
                            title:'Saldo',
                            height:80,
                            bodyStyle:'text-align: center;vertical-align: middle;line-height: 55px;font-size:32px;font-family: arial;padding-right: 10px;',
                            html:'<span id="fmCobroSaldo">$ 0.0</span>'
                        }
                    ]
                }

            ],
        }
    },

    renderPaso4: function () {
        return {
            layout: 'fit',
            border: false,
            items:  [
                {
                    xtype: 'iframepanel',
                    id: 'impresionFacturaVentaHtml',
                    bodyStyle: 'background-color:white;'
                }
            ],
            buttons : [
                {
                    text: 'Cerrar Comprobante',
                    scope: this,
                    handler: function() {
                        var id = this.form.getForm().findField('Id').getValue();
                        app.publish('/desktop/wait', 'Cerrando el comprobante');
                        Models.Facturacion_Model_TicketFacturasMapper.cerrar(id, function (result, e) {
                            if (e.status) {
                                Ext.MessageBox.hide();
                                this.grid.abmWindow.closeAbm();
                            }
                        }, this);
                    }
                }
            ]
        };
    },

    /**
     * Creamos la grilla principal y le agrego botones al toolbar
     */
    createGrid: function () {
        this.grid = Ext.ComponentMgr.create(<?=$this->grid?>);
        this.grid.getTopToolbar().addButton([
            this.anular(),
            {
                xtype:'tbseparator'
            },
            {
                 text: 'Ver',
                 icon: 'images/eye.png',
                 cls: 'x-btn-text-icon',
                 scope: this.grid,
                 handler: function () {
                     sel = this.getSelectionModel().getSelected();
                     if (!sel) return;
                     param = 'id/'+sel.data.Id;
                     app.publish('/desktop/modules/js/commonApps/showUrl.js', {
                        action: 'launch',
                        url: '/Facturacion/FacturasVentas/verfactura/'+param,
                        width: 900,
                        height: 500,
                        title: 'Factura'
                    });
                 }
            },
            {
                xtype: 'tbfill'
            },
            {
                icon: 'images/wrench.png',
                cls: 'x-btn-text-icon',
                menu: [
                    {
                        text: 'Refiscalizar',
                        tooltip :'<b>Refiscaliza el comprobante</b><br>Imprime nuevamente o lo informa a la AFIP de ser electrónico',
                        icon: 'images/printer.png',
                        cls: 'x-btn-text-icon',
                        scope:  this.grid,
                        handler: function () {
                            Ext.MessageBox.confirm('Cuidado','Esta a punto de reimprimir un comprobante cerrado.<br>Es posible que este comprobante haya sido impreso anteriormente.<br><br><span style="color:red">Desea continuar?</span>',function(btn){
                                if (btn == 'yes') {
                                    sel = this.getSelectionModel().getSelected();
                                    if (!sel) return;
                                    Rad.callRemoteJsonAction({
                                        params: {
                                            'id': sel.data.Id
                                        },
                                        url: '/Facturacion/FacturasVentas/refiscalizar',
                                        scope: this,
                                        success: function (response) {
                                        }
                                     });
                                }
                            }, this);
                        }
                    },
                    {
                        text:   'Reimputar IVA',
                        tooltip :'Cambiar Inputacion al Libro IVA',
                        icon:   'images/book_open.png',
                        cls:    'x-btn-text-icon',
                        scope:  this,
                        handler: function () {
                            sel = this.grid.getSelectionModel().getSelected();
                            if (!sel) {
                                window.app.publish('/desktop/showWarning', 'Seleccione un comprobante');
                                return;
                            } else {
                                if (!sel.data.Cerrado) {
                                    window.app.publish('/desktop/showError', 'Operacion no permitida, el comprobante no esta cerrado.');
                                    return;
                                }
                            };
                            this.cambiarImputacionIva(sel.data.Id, sel);
                        }
                    },
                    {
                        text: 'Generar Nota compensatoria',
                        tooltip :'Genera una Nota compensatoria',
                        icon: 'images/page_copy.png',
                        cls: 'x-btn-text-icon',
                        scope: this,
                        handler: function () {
                            sel = this.grid.getSelectionModel().getSelected();
                            if (!sel) return;
                            var id = sel.data.Id;
                            Ext.Msg.confirm('Confirmar', 'Quiere generar una nota para compensar este comprobante?', function (btn) {
                                if (btn == 'yes'){
                                    Models.Facturacion_Model_TicketFacturasMapper.compensarFacturasConNotas(id, function (result, e) {
                                        if (e.status) {
                                            app.publish('/desktop/notify', {title: 'Nota de Credito', html:'La Nota de credito fue creada con exito'});
                                            this.grid.store.reload();
                                        }
                                    }, this);
                                }
                            });
                        }
                    }
                ]
            }
        ]);
    },

    cambiarImputacionIva: function (id,row) {
        if (!this.windowCambiarImputacionIVA) {
            this.comboLibrosIVA = new Ext.ux.form.ComboBox({
                width: 120,
                minChars: 3,
                displayField: "Descripcion",
                autoLoad: false,
                autoSelect: true,
                selectOnFocus: true,
                forceSelection: true,
                forceReload: true,
                hiddenName: "LibroIVA",
                loadingText: "Cargando...",
                lazyRender: true,
                searchField: "Descripcion",
                store: new Ext.data.JsonStore({
                        id: 0,
                        url: "datagateway\/combolist\/fetch\/Abiertos\/model\/LibrosIVA\/m\/Contable",
                        storeId: "LibroIVAStore"
                    }
                ),
                typeAhead: false,
                valueField: "Id",
                autocomplete: true,
                allowBlank: true,
                allowNegative: false,
                fieldLabel: "Libro de IVA",
                name: "LibroIVA"
            });

            this.windowCambiarImputacionIVA = app.desktop.createWindow({
                width: 300,
                height: 100,
                border: false,
                bodyStyle: 'background:white',
                layout: 'form',
                closeAction: 'hide',
                maximizable: false,
                minimizable: false,
                modal: true,
                ishidden: true,
                title: 'Cambiar Imputacion Libro IVA',
                plain: true,
                items: this.comboLibrosIVA,
                buttons: [
                    {
                        text: 'Imputar',
                        scope: this,
                        handler: function () {
                            var value = this.comboLibrosIVA.getValue();
                            if (!value) return;
                            Models.Facturacion_Model_TicketFacturasMapper.cambiarImputacionIva(id,value, function (result, e) {
                                if (e.status)
                                    this.windowCambiarImputacionIVA.hide();
                            }, this);
                        }
                    }
                ]
            });
        }

        this.windowCambiarImputacionIVA.show();
    },

    /**
     * Crea la ventana del modulo
     */
    create: function() {
        return app.desktop.createWindow({
            id: this.id+'-win',
            title: this.title,
            width: 1000,
            height: 600,
            border: false,
            shim: false,
            animCollapse: false,
            layout: 'fit',
            items: this.grid
        });
    },

    /**
     * Da formato a los items del wizard
     */
    renderWizardItem: function (titulo, subtitulo, contenido) {
        return {
            layout: 'fit',
            border: false,
            frame: false,
            items:  {
                layout: 'border',
                border: false,
                items: [
                    {
                        region: 'north',
                        layout: 'fit',
                        border: false,
                        height: 50,
                        items: {
                            layout: 'fit',
                            border: false,
                            html: "<img style='float:right;' src='/images/268498431.png'><Font style='COLOR:#336699;FONT-FAMILY:Arial;FONT-SIZE:16pt;text-align:center;PADDING-TOP:50px;'>"+titulo+"</font><br><Font style='COLOR:#336699;FONT-FAMILY:Arial;FONT-SIZE:10pt;text-align:center;padding-left:5px;'>"+subtitulo+"</font>"
                        }
                    },
                    {
                        region: 'center',
                        layout: 'fit',
                        border: false,
                        items: {
                            layout: 'fit',
                            border: false,
                            items: contenido
                        }
                    }
                ]
            }
        }
    },

    anular: function () {
        return {
            text: 'Anular',
            iconCls: 'x-btn-text-icon',
            icon: 'images/cancel.png',
            handler: function() {
                selected = this.grid.getSelectionModel().getSelected();
                if (!selected) {
                    Ext.Msg.show({
                        title: 'Atencion',
                        msg: 'Seleccione un Registro',
                        modal: true,
                        icon: Ext.Msg.WARNING,
                        buttons: Ext.Msg.OK
                    });
                    return;
                }
                if (Ext.Msg.confirm('Atencion','¿Está seguro que desea anular el comprobante seleccionado?', function (btn) {
                    if (btn == 'yes') {
                        var id = selected.get('Id');
                        this.form.record = selected;

                        Models.Facturacion_Model_TicketFacturasMapper.anular(id, Ext.emtpyFn, this);
                        Models.Facturacion_Model_TicketFacturasMapper.get(id, function(result, e) {
                            if (e.status)
                                this.form.updateGridStoreRecord(result);
                        }, this);
                    }
                }, this));
            },
            scope: this
        }
    }
});

new Apps.<?=$this->name?>();