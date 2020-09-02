Ext.ns( 'Apps' );

Apps.<?=$this->name?> = Ext.extend(RadDesktop.Module, {
    title: '<?=$this->title?>',
    appChannel: '/desktop/modules<?=$this->url()?>',
    requires: [
        '/direct/Facturacion/Recibos?javascript',
        '/direct/Facturacion/RecibosDetalles?javascript'
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

    eventpagada: function (ev) {
        this.createWindow();
        this.grid.store.load({params:{factura: ev.value}});
    },

    eventlaunch: function(ev) {
        this.createWindow();
        this.grid.store.load();
    },

    createWindow: function() {
        var win = app.desktop.getWindow(this.id+'-win');
        if ( !win ) {
            this.createGrid();
            this.createEditorWindow();
            win = this.create();
	}
        win.show();
    },

    /**
     * Crea la ventana del Abm
     */
    createEditorWindow: function () {
        // Forulario principal
        this.form   = Ext.ComponentMgr.create(<?=$this->form?>);
        // despues del submit exitoso se pasa al paso 1 del wizard
        this.form.on(
            'actioncomplete',
            function() {
                this.wizard.setActiveItem(1);
            },
            this
        );

        this.createWizard();
        this.grid.abmWindow = app.desktop.createWindow({
                autoHideOnSubmit: false,
                width  : 900,
                height : 600,
                border : false,
                layout : 'fit',
                ishidden : true,
                title  : 'Recibos',
                plain  : true,
                items  : this.wizard,
                form   : this.form,
                grid   : this.grid,
                getForm: function() {
                    return this.form;
                }
            },
            Rad.ABMWindow
        );

        this.grid.abmWindow.on('show', function(){
            this.setDropTargets();
            var Monto = this.form.getForm().findField('Monto');
            var selected = this.grid.getSelectionModel().getSelected();
            if (selected.get('Monto') == null) {
                if (selected.get('Id') == null) Monto.enable();
                else Monto.disable();
            } else {
                Monto.disable();
            }
        },this);

        // si la ventana se esconde volvemos al primer paso
        this.grid.abmWindow.on('hide', function(){
                this.wizard.setActiveItem(0);
                id = this.form.getForm().findField('Id').getValue();
                if (id != 0) {
                    // Actualizo la grilla
                    Rad.callRemoteJsonAction ({
                        url: '/datagateway/get/model/Recibos/m/Facturacion',
                        params: {id: id},
                        scope: this,
                        success: function(response) {
                            this.form.updateGridStoreRecord(response.data);
                        }
                    });
                }
            }, this
        );
    },

    /**
     * Creo el wizard del abm
     */
    createWizard: function() {
        this.createSecondaryGrids();

        // Creamos el Obj wizard
        this.wizard = new Rad.Wizard({
            border: false,
            defaults: {border:false},
            items: [
                this.renderWizardItem('Ingresar datos del Recibo:','',this.form),
                this.renderWizardItem('Seleccionar los comprobantes a cancelar:','',this.renderPaso2()),
                this.renderWizardItem('Conceptos impositivos:','',this.gridCI),
                this.renderWizardItem('Seleccionar formas de cobros:','',this.renderPaso3()),
                this.renderWizardItem('Finalizar Recibo:','',this.renderPaso4()),
            ]
        });
        // Logica del Wizard
        this.wizard.on(
        'activate',
        function (i) {
            switch(i) {
                case 0:

                    break;
                case 1:	// Si activo el paso 1 cargo la grilla Facturas compras
                    form = this.form.getForm();
                    id   = form.findField('Id').getValue();
                    persona  = form.findField('Persona').getValue();
                    detailGrid = {remotefield: 'Comprobante',localfield: 'Id'};

                    this.gridAFV.setPermanentFilter(0,'Persona',persona);
                    this.gridAFV.loadAsDetailGrid(detailGrid, id);
                    break;
                case 2:
                    detailGrid = {remotefield: 'ComprobantePadre',localfield: 'Id'};
                    form = this.form.getForm();
                    id   = form.findField('Id').getValue();

                    this.gridCI.parentForm = this.form;	// le seteamos el formulario padre para que saque los valores automaticamente
                    this.gridCI.loadAsDetailGrid(detailGrid, id);
                    break;
                case 3:
                    detailGrid = {remotefield:'Comprobante',localfield:'Id'};

                    // cargo grilla de cobros
                    var IdRecibo 	= this.form.getForm().findField('Id');
                    var IdCliente 	= this.form.getForm().findField('Persona');
                    this.gridRD.loadAsDetailGrid(detailGrid, IdRecibo.getValue());

                    // // cargo grilla cheques
                    // this.gridCheques.store.load();
                    // detailGrid = {remotefield:'Persona',localfield:'Id'};
                    // // cargo grilla transacciones bancarias

                    Models.Facturacion_Model_RecibosMapper.getTotal(IdRecibo.getValue(), function(result, e) {
                        if (e.status) {
                            this.montoACobrar = parseFloat(result).toFixed(2);
                            this.refreshSaldo();
                        }
                    }, this);

                    break;
                case 4:
                    Ext.getCmp('impresionReciboHtml').setSrc('/Window/birtreporter/report?template=Comp_Recibo_Ver&output=html&id='+ this.form.getForm().findField('Id').getValue());
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
                    break;
                case 1:
                    this.gridAFV.saveRelation();
                    return false;
                    break;
                case 2:
                    // Controlo que no hayan conceptos con monto menor o igual a 0 (cero)
                    var IdOrden = this.form.getForm().findField('Id');
                    Models.Facturacion_Model_RecibosMapper.getControlTotalConcepto(
                        IdOrden.getValue(),
                        function(result, e) { if(e.status) this.wizard.setActiveItem(3);},
                        this
                    );
                    return false;
                    break;
            }
        }, this
    );

        this.wizard.on('finish', function(i) {
            this.grid.abmWindow.closeAbm();
        },this);
    },

    setDropTargets: function() {
        // seteo los drop de cobros
        this.dropCobros = new Ext.dd.DropTarget(this.gridRD.getView().scroller.dom, {
            ddGroup : 'recibospagos',
            groups :{cheques: true},
            scope: this,
            notifyDrop : function(dd, e, data) {
                var IdRecibo = this.scope.form.record.data.Id;
                var ids = [];
                var action = null;

                switch (data.grid.id)
                {
                    case this.scope.gridCheques.id:
                        action = 'agregarCheques';
                        break;
                    case this.scope.gridTranB.id:
                        action = 'agregarTransaccionBancaria';
                        break;
                }

                if (action != null) {
                    for(var i = 0, len = data.selections.length; i < len; i++) {
                        ids[i] = data.selections[i].data['Id'];
                    }
                    Models.Facturacion_Model_RecibosDetallesMapper[action](IdRecibo, ids, function (result, e) {
                        if (e.status) {
                            this.gridRD.store.reload();
                            if (action == 'agregarCheques') this.gridCheques.store.reload();
                            if (action == 'agregarTransaccionBancaria') this.gridTranB.store.reload();
                        }
                    }, this.scope);
                }
            }
        });
    },
    /*
    agragarCobrosDeRespuesta: function (cobros) {
            Ext.each(cobros, function (cobro) {
                    r = new this.gridRD.store.recordType(cobro);
                    this.gridRD.getGridEl().unmask();
                    this.gridRD.store.insert(0, r);
                    this.gridRD.store.commitChanges();
                    r.id = cobro.Id;
                    r.phantom = false;
            },this);
    },
     */

    /**
     * Creamos las grillas secundarias y su logica
     */
    createSecondaryGrids: function () {
        // Factura Ventas a Cobrar
        this.gridAFV = Ext.ComponentMgr.create(<?=$this->gridAFV?>);
        this.gridAFV.on(
        'saverelation',
        function(status) {
            if(status) {
                var IdRecibo = this.form.getForm().findField('Id');
                Rad.callRemoteJsonAction({
                    url: '/Facturacion/recibos/paso2',
                    method: 'POST',
                    params: {idRecibo: IdRecibo.getValue()},
                    success: function (response) {
                        this.wizard.setActiveItem(2);
                    },
                    scope: this
                });
            }
        },
        this
    );
        // Conceptos Impositivos
        this.gridCI 	 = Ext.ComponentMgr.create(<?=$this->gridCI?>);
        // Cheques
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
            "ddGroup" : 'recibospagos',
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
        this.gridCheques.__suscribeToModelEvent('ChequesDeTerceros');
        // Transferencias
        this.gridTranB   = Ext.ComponentMgr.create({
            "xtype": "radgridpanel",
            "filters": true,
            "url": "/default/datagateway",
            "model": "TransaccionesBancarias",
            "module": "Base",
            "forceFit": true,
            "stateful": false,
            "iniSection": "entradaPersona",
            "loadAuto": false,
            "ddGroup" : 'recibospagos',
            "listeners": {
                "render": function() {
                    this.__suscribeToModelEvent('TransferenciasEntrantes');
                    this.__suscribeToModelEvent('DepositosEntrantes');
                }
            },
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
            "ddText": "{0} Transaccion(es) seleccionada(s)",
            "fetch": "NoUtilizadoDeEntrada",
            buttons:[{
                text:     'Transferencias',
                iconCls:  'add',
                handler:  function() {
                    this.publish('/desktop/modules/Window/abm/index/m/Base/model/TransferenciasEntrantes', { action: 'launch' });
                },
                iconAlign: 'bottom',
                scope:    this,
            },{
                text:     'Depositos',
                iconCls:  'add',
                iconAlign: 'bottom',
                handler:  function() {
                    this.publish('/desktop/modules/Window/abm/index/m/Base/model/DepositosEntrantes', { action: 'launch' });
                },
                scope:    this,
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
        this.gridTranB.__suscribeToModelEvent('TransferenciasEntrantes');
        // Recibos Detalls (cobros)
        this.gridRD 	 = Ext.ComponentMgr.create(<?=$this->gridRD?>);
        this.gridRD.store.on({
            add:    { scope: this, fn: this.refreshSaldo },
            remove: { scope: this, fn: this.refreshSaldo },
            load:   { scope: this, fn: this.refreshSaldo }
        });
    },

    renderTipoCobros: function () {
        return {
            xtype: 'tabpanel',
            activeItem: 0,
            deferredRender : false,
            items: [{
                    title:'Contado',
                    bodyStyle:'padding:5px 5px 0',
                    layout:'form',
                    items:[
                        {
                            xtype:'xcombo',
                            id : 'recibocajaCobrosEfectivo',
                            displayField:'Descripcion',
                            width: 80,
                            autocomplete:true,
                            selectOnFocus:true,
                            forceSelection:true,
                            forceReload:true,
                            hiddenName:'Caja',
                            loadingText:'Cargando...',
                            lazyRender: false,
                            store: new Ext.data.JsonStore (
                            {
                                id:0,
                                url:'datagateway\/combolist\/model\/Cajas/m\/Contable'
                            }),
                            typeAhead:true,
                            valueField:'Id',
                            autoLoad:true,
                            allowBlank:false,
                            allowNegative:false,
                            fieldLabel:'Caja',
                            name:'Caja'
                        },
                        {xtype:'numberfield', fieldLabel:'Monto', id: 'recibomontoCobroDetalleEfectivo'},
                        {xtype:'button', text:'Agregar',
                            handler: function() {
                                var IdRecibo   = this.form.getForm().findField('Id');
                                var cajaValue  = Ext.getCmp('recibocajaCobrosEfectivo').getValue();
                                var monto      = Ext.getCmp('recibomontoCobroDetalleEfectivo');
                                var montoValue = monto.getValue();

                                if (montoValue > 0 && cajaValue > 0) {
                                    Rad.callRemoteJsonAction({
                                        url: '/Facturacion/recibos/agregarefectivo',
                                        method: 'POST',
                                        params: {idRecibo: IdRecibo.getValue(), 'monto': montoValue, caja: cajaValue},
                                        success: function (response) {
                                            this.gridRD.store.reload();
                                            //this.agragarCobrosDeRespuesta(response.cobros)
                                            //this.gridRD.getGridEl().unmask();
                                        },
                                        scope: this
                                    });
                                } else {
                                    app.publish('/desktop/showError', 'Debe especificar la caja y el monto');
                                }
                            },
                            scope: this
                        }
                    ]
                },
                this.gridCheques,
                this.gridTranB
            ]
        };
    },

    renderPaso2: function () {
        return this.gridAFV;
    },


    renderPaso3: function () {
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
                            text: 'Banco',
                            icon: 'images/32/bankTransfer.png',
                            scale: 'large',
                            iconAlign: 'bottom',
                            handler: function(btn, e) {
                                pagosCard.setTitle(btn.text);
                                var IdRecibo    = this.form.getForm().findField('Id');
                                var IdCliente   = this.form.getForm().findField('Persona');
                                this.gridTranB.loadAsDetailGrid(detailGrid, IdCliente.getValue());
                                pagosCard.getLayout().setActiveItem(4);

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
                                id: 'recibosPagoTarjeta',
                                xtype: 'numberfield',
                                anchor: '100%',
                                fieldLabel: 'Monto',
                                allowNegative: false,
                            }, {
                                id: 'recibosNumeroTarjeta',
                                xtype: 'numberfield',
                                anchor: '100%',
                                fieldLabel: 'Numero de Tarjeta',
                                allowNegative: false,
                            },
                            {
                                xtype:"xcombo",
                                id: 'recibosTipoTarjeta',
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
                                id: 'recibosCuotas',
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
                                var monto   = Ext.getCmp('recibosPagoTarjeta').getValue();
                                var tarjeta = Ext.getCmp('recibosNumeroTarjeta').getValue();
                                var tipo    = Ext.getCmp('recibosTipoTarjeta').getValue();
                                var ctas    = Ext.getCmp('recibosCuotas').getValue();
                                Models.Facturacion_Model_RecibosDetallesMapper.agregarTarjeta(this.form.record.data.Id, monto, ctas, tarjeta, tipo, function (result, e) {
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
                                id: 'recibosPagoEfectivo',
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
                                var monto = Ext.getCmp('recibosPagoEfectivo').getValue();

                                if (!monto) {
                                    window.app.publish('/desktop/showError', 'Ingrese un monto.');
                                    return;
                                }

                                Models.Facturacion_Model_RecibosDetallesMapper.agregarEfectivo(this.form.record.data.Id, monto, function (result, e) {
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
                    this.gridCheques,
                    this.gridTranB
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
                            html:'<span id="reciboCobroTotal">$ 0.0</span>'
                        },
                        {
                            title:'Saldo',
                            height:80,
                            bodyStyle:'text-align: center;vertical-align: middle;line-height: 55px;font-size:32px;font-family: arial;padding-right: 10px;',
                            html:'<span id="reciboCobroSaldo">$ 0.0</span>'
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
            items: [{
                    xtype: 'iframepanel',
                    id: 'impresionReciboHtml',
                    bodyStyle: 'background-color:white;'
                }],
            buttons : [{
                    text: 'Imprimir y cerrar Recibo',
                    scope: this,
                    handler: function() {
                        var IdRecibo 	= this.form.getForm().findField('Id');
                        Rad.callRemoteJsonAction({
                            url: '/Facturacion/recibos/cobrarrecibos',
                            method: 'POST',
                            scope:  this,
                            params: {idRecibo: IdRecibo.getValue() },
                            success: function (response) {
                                this.publish('/desktop/modules/Window/birtreporter', {
                                    action: 'launch',
                                    template: 'Comp_Recibo_PreImpreso',
                                    id: IdRecibo.getValue(),
                                    width:  645,
                                    height: 400
                                });
                                this.grid.abmWindow.closeAbm();
                            }
                        });
                    }
                }]
        };
    },

    refreshSaldo: function () {
        //var montoCobrado = parseFloat(this.gridOPD.plugins[0].totales.PrecioUnitario).toFixed(2);
        var montoCobrado = parseFloat(this.gridRD.plugins[0].totales.PrecioUnitario).toFixed(2);
        saldo = (this.montoACobrar - montoCobrado).toFixed(2);
        Ext.select('#reciboCobroSaldo').update('$ ' + saldo);
        Ext.select('#reciboCobroTotal').update('$ ' + this.montoACobrar);
    },

    /**
     * Creamos la grilla principal y le agrego botones al toolbar
     */
    createGrid: function () {
        this.grid = Ext.ComponentMgr.create(<?=$this->grid?>);

        this.grid.getTopToolbar().addButton([
            {
                text: 'Imprimir',
                icon: 'images/printer.png',
                cls: 'x-btn-text-icon',
                scope: this.grid,
                handler: function () {
                    selected = this.getSelectionModel().getSelected();
                    if (selected) {
                        if (selected.data.Cerrado) {
                            this.publish('/desktop/modules/Window/birtreporter', {
                                action: 'launch',
                                template: 'Comp_Recibo_PreImpreso',
                                id: selected.data.Id,
                                output: 'pdf',
                                width:  600,
                                height: 570
                            });
                        } else {
                            window.app.publish('/desktop/showError', 'Operacion no permitida, el comprobante no esta cerrado.');
                        }
                    } else {
                        window.app.publish('/desktop/showWarning', 'Seleccione un comprobante');
                    }
                }
            },
            {
                text: 'Imprimir Retenciones',
                icon: 'images/printer.png',
                cls: 'x-btn-text-icon',
                scope: this.grid,
                handler: function () {
                    selected = this.getSelectionModel().getSelected();
                    if (selected) {
                        if (selected.data.Cerrado) {
                            this.publish('/desktop/modules/Window/birtreporter', {
                                action: 'launch',
                                template: 'Retenciones',
                                id: selected.data.Id,
                                output: 'pdf',
                                width:  600,
                                height: 570
                            });
                        } else {
                            window.app.publish('/desktop/showError', 'Operacion no permitida, el comprobante no esta cerrado.');
                        }
                    } else {
                        window.app.publish('/desktop/showWarning', 'Seleccione un comprobante');
                    }
                }
            },
            { xtype:'tbseparator' },
            this.anular(),
            {xtype: 'tbfill'},
            {
                icon:   'images/wrench.png',
                cls:    'x-btn-text-icon',
                menu:[
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
                    }
               ]
            }

        ]);
    },

     cambiarImputacionIva: function (id,row) {
        if (!this.windowCambiarImputacionIVA) {
            this.comboLibrosIVA = new Ext.ux.form.ComboBox({
                "width":120,
                "minChars":3,
                "displayField":"Descripcion",
                "autoLoad":false,
                "autoSelect":true,
                "selectOnFocus":true,
                "forceSelection":true,
                "forceReload":true,
                "hiddenName":"LibroIVA",
                "loadingText":"Cargando...",
                "lazyRender":true,
                "searchField":"Descripcion",
                "store":new Ext.data.JsonStore(
                    {
                        "id":0,
                        "url":"datagateway\/combolist\/fetch\/Abiertos\/model\/LibrosIVA\/m\/Contable",
                        "storeId":"LibroIVAStore"
                    }
                ),
                "typeAhead":false,
                "valueField":"Id",
                "autocomplete":true,
                "allowBlank":true,
                "allowNegative":false,
                "fieldLabel":"Libro de IVA",
                "name":"LibroIVA"
            });

            this.windowCambiarImputacionIVA = app.desktop.createWindow({
                width  : 300,
                height : 100,
                border : false,
                bodyStyle:'background:white',
                layout : 'form',
                closeAction: 'hide',
                maximizable:false,
                minimizable:false,

                modal: true,
                ishidden : true,
                title  : 'Cambiar Imputacion Libro IVA',
                plain  : true,
                items  : [this.comboLibrosIVA],
                buttons: [
                    {
                        text:'Imputar',
                        scope:this,
                        handler: function() {
                            var value = this.comboLibrosIVA.getValue();
                            if (!value) return;
                            Models.Facturacion_Model_RecibosMapper.cambiarImputacionIva(id,value, function(result, e) {
                                if (e.status) {
                                    this.windowCambiarImputacionIVA.hide();
                                }
                            }, this);
                        }
                    }
                ]
            });

        }

//        this.comboLibrosIVA.setValue(sel.data.LibroIVA);

        this.windowCambiarImputacionIVA.show();


    },

    anular: function ()
    {
        return {
            text: 'Anular',
            iconCls: 'x-btn-text-icon',
            icon: 'images/cancel.png',
            handler: function() {
                selected = this.grid.getSelectionModel().getSelected();
                if (!selected) {
                    window.app.publish('/desktop/showWarning', 'Seleccione un comprobante.');
                    return;
                }
                if (selected.get('Cerrado')=='0') {
                    window.app.publish('/desktop/showWarning', 'El comprobante aun esta abierto.');
                    return;
                }

                if (Ext.Msg.confirm('Atencion','¿Está seguro que desea anular el comprobante seleccionado?',function(btn) {
                    if (btn == 'yes') {
                        var id = selected.get('Id');
                        this.form.record = selected;

                        Models.Facturacion_Model_RecibosMapper.anular(id, Ext.emtpyFn, this);
                        Models.Facturacion_Model_RecibosMapper.get(id, function(result, e) {
                            if (e.status) {
                                this.form.updateGridStoreRecord(result);
                            }
                        }, this);
                    }
                }, this));
            },
            scope: this
        }
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
            shim:   false,
            animCollapse: false,
            layout: 'fit',
            items: [
                this.grid
            ]
        });
    },

    /**
     * Da formato a los items del wizard
     */
    renderWizardItem: function (titulo, subtitulo, contenido) {
        return {
            layout: 'fit',
            border : false,
            frame : false,
            items:  {
                layout : 'border',
                border : false,
                items : [{
                        region : 'north',
                        layout: 'fit',
                        border: false,
                        height : 50,
                        items : {
                            layout: 'fit',
                            border: false,
                            html: '<img style=\'float:right;\' src=\'/images/268498431.png\'><Font style=\'COLOR:#336699;FONT-FAMILY:Arial;FONT-SIZE:16pt;text-align:center;PADDING-TOP:50px;\'>'+titulo+'</font><br><Font style=\'COLOR:#336699;FONT-FAMILY:Arial;FONT-SIZE:10pt;text-align:center;padding-left:5px;\'>'+subtitulo+'</font>'
                        }
                    },{
                        region : 'center',
                        layout: 'fit',
                        border: false,
                        items : [{
                                layout: 'fit',
                                border: false,
                                items: contenido
                            }]
                    }]
            }
        }
    }
});

new Apps.<?=$this->name?>();
