Ext.ns( 'Apps' );

Apps.<?=$this->name?> = Ext.extend(RadDesktop.Module, {
    title: '<?=$this->title?>',
    appChannel: '/desktop/modules<?=$this->url()?>',
    requires: [
        '/direct/Facturacion/OrdenesDePagos?javascript',
        '/direct/Base/Proveedores?javascript'
    ],

    eventfind: function (ev) {
        this.createWindow();
        var p = this.grid.buildFilter(0, 'Id',ev.value);
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
        // Formulario del generador de cheques
        this.formGenCheques = Ext.ComponentMgr.create(<?=$this->formGenCheques?>);
        this.formGenCheques.on(
            'actioncomplete',
            function(){
                this.winGeneradorCheques.hide();
                this.formGenCheques.getForm().reset();
                this.gridCheques.store.reload();
            },
            this
        );

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
            width  : 900,
            height : 500,
            border : false,
            layout : 'fit',
            ishidden : true,
            title  : 'Ordenes de Pago',
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

        // si la ventana se esconde volvemos al primer paso
        this.grid.abmWindow.on('hide', function() {
                this.wizard.setActiveItem(0);
                id = this.form.getForm().findField('Id').getValue();
                if (id != 0) {
                    // Actualizo la grilla
                    Rad.callRemoteJsonAction ({
                        url: '/datagateway/get/model/OrdenesDePagos/m/Facturacion',
                        params: {id: id},
                        scope: this,
                        success: function(response) {
                            this.form.updateGridStoreRecord(response.data);
                        }
                    });
                }
            }, this
        );
        this.grid.abmWindow.on('show', function(){this.setDropTargets();},this);
    },

    /**
     * Agrega la logica adicional para los campos del formulario
     */
    addExtraListeners: function() {
        var form = this.form.getForm();
        var grid = this.grid;
        // Campo Persona (Proveedor)
        form.findField('Persona').on('select', function (combo, record, index) {
            Models.Base_Model_ProveedoresMapper.getIBItems(record.data.Id, function(result, e) {
                if (e.status) {
                    if ( result == 0 ) {
                        Ext.Msg.show({
                            title : 'Atención',
                            msg : 'El Proveedor no tiene situación impositiva cargada. Desea Continuar ?',
                            width : 400,
                            closable : false,
                            buttons : Ext.Msg.YESNO,
                            multiline : false,
                            fn : function(btn) { 
                              if (btn == 'no') {
                                form.findField('Persona').reset(); 
                                grid.abmWindow.closeAbm(); 
                              }
                            },
                            icon : Ext.Msg.WARNING
                        });
                        return;
                    }
                }
            }, this);
        }, this);
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
                this.renderWizardItem('Ingresar datos de la Orden de Pagos:','',this.form),
                this.renderWizardItem('Seleccionar las Facturas que se pagaran:','',this.renderPaso2()),
                this.renderWizardItem('Conceptos impositivos:','',this.gridCI),
                this.renderWizardItem('Seleccionar formas de pagos:','',this.renderPaso3()),
                this.renderWizardItem('Finalizar Orden de Pago:','',this.renderPaso4()),
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
                        form 		= this.form.getForm();
                        id   		= form.findField('Id').getValue();
                        persona  	= form.findField('Persona').getValue();
                        detailGrid 	= {remotefield: 'Comprobante',localfield: 'Id'};

                        this.gridAFC.setPermanentFilter(0,'Persona',persona);
                        this.gridAFC.loadAsDetailGrid(detailGrid, id);
                        break;
                    case 2:
                        detailGrid 	= {remotefield: 'ComprobantePadre',localfield: 'Id'};
                        form 		= this.form.getForm();
                        id   		= form.findField('Id').getValue();

                        this.gridCI.parentForm = this.form;	// le seteamos el formulario padre para que saque los valores automaticamente
                        this.gridCI.loadAsDetailGrid(detailGrid, id);

                        break;
                    case 3:

                        detailGrid = {remotefield:'Comprobante',localfield:'Id'};

                        // cargo grilla de pagos
                        var IdOrdenDePago 	= this.form.getForm().findField('Id');
                        var IdProveedor 	= this.form.getForm().findField('Persona');
                        this.gridOPD.loadAsDetailGrid(detailGrid, IdOrdenDePago.getValue());

                        // cargo grilla cheques
                        this.gridCheques.setPermanentFilter(0,'TipoDeEmisorDeCheque',1); // Mostramos solo los cheques propios por defecto
                        this.gridCheques.store.load();
                        detailGrid = {remotefield:'Persona',localfield:'Id'};
                        // cargo grilla transacciones bancarias
                        this.gridTranB.loadAsDetailGrid(detailGrid, IdProveedor.getValue());
                        // cargo grilla de pagos con Tarjeta bancarias
                        this.gridCuponesTarjetas.loadAsDetailGrid(detailGrid, IdProveedor.getValue());
                        Models.Facturacion_Model_OrdenesDePagosMapper.getTotal(IdOrdenDePago.getValue(), function(result, e) {
                            if (e.status) {
                                this.montoAPagar = parseFloat(result).toFixed(2);
                                Ext.select('#totalOrdenPago').update(this.montoAPagar);
                                this.refreshSaldo();
                            }
                        }, this);

                        break;
                    case 4:
                        Ext.getCmp('impresionODePagoHtml').setSrc('/Window/birtreporter/report?template=Comp_OrdenDePago_Ver&output=html&id='+ this.form.getForm().findField('Id').getValue());
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
                    this.gridAFC.saveRelation();
                    return false;
                    break;
                case 2:
                    // Controlo que no hayan conceptos con monto menor o igual a 0 (cero)
                    var IdOrden = this.form.getForm().findField('Id');
                    Models.Facturacion_Model_OrdenesDePagosMapper.getControlTotalConcepto(
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

    showGeneradorDeCheques: function() {
        if (!this.winGeneradorCheques) {
            this.winGeneradorCheques = app.desktop.createWindow({
                title: 'Generar Cheques',
                width: 900,
                height:370,
                border: false,
                shim: false,
                resizable: false,
                animCollapse: false,
                closeAction: 'hide',
                layout: 'fit',
                items: [
                    this.formGenCheques
                ]
            });
        }

        this.formGenCheques.getForm().reset();
        var field   = this.formGenCheques.getForm().findField('Persona');
        var persona = this.form.getForm().findField('Persona');
        field.setValue(persona.getValue());

        this.winGeneradorCheques.show();
    },

    setDropTargets: function() {
        // seteo la papelera
        this.dropPepelera = new Ext.dd.DropTarget(
            'papeleraOrdenesDePago', {
                ddGroup : 'pagos',
                scope   : this,
                notifyDrop : function(dd, e, data) {
                    this.scope.gridOPD.deleteRows();
                }
            }
        );

        // seteo los drop de pagos
        this.dropPagos = new Ext.dd.DropTarget(this.gridOPD.getView().scroller.dom, {
            ddGroup: 'grillassistema',
            groups: { cheques: true, facturasVentas: true },
            scope: this,
            notifyDrop: function(dd, e, data) {
                var IdOrdenDePago = this.scope.form.getForm().findField('Id');
                var ids = [];
                var action = null;

                switch (data.grid.id) {
                    case this.scope.gridCheques.id:
                        action = 'agregarcheque';
                        break;
                    case this.scope.gridTranB.id:
                        action = 'agregartransaccion';
                        break;
                    case this.scope.gridCuponesTarjetas.id:
                        action = 'agregarcupontarjeta';
                        break;
                }
                if (action != null) {
                    for(var i = 0, len = data.selections.length; i < len; i++) {
                        ids[i] = data.selections[i].data['Id'];
                    }
                    Rad.callRemoteJsonAction ({
                        url: '/Facturacion/ordenesdepagos/'+action,
                        params: {idOrdenDePago: IdOrdenDePago.getValue(), 'ids[]': ids},
                        scope: this.scope,
                        success: function(response) {
                            this.gridOPD.store.reload();

                        }
                    });
                }
            }
        });
    },

    /**
     * Creamos las grillas secundarias y su logica
     */
    createSecondaryGrids: function () {
        // Factura Compras a Pagar
        this.gridAFC = Ext.ComponentMgr.create(<?=$this->gridAFC?>);
        this.gridAFC.on(
        'saverelation',
        function(status) {
            if(status) {
                var IdOrdenDePago = this.form.getForm().findField('Id');
                Rad.callRemoteJsonAction({
                    url: '/Facturacion/ordenesdepagos/paso2',
                    method: 'POST',
                    params: {idOrdenDePago: IdOrdenDePago.getValue()},
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
        this.gridCheques = Ext.ComponentMgr.create(<?=$this->gridCheques?>);
        this.gridCheques.parentModule = this; // Le guardo una referencia para poder llamar directamente desde el toolbar algunas funciones de este modulo
        // Transferencias
        this.gridTranB   = Ext.ComponentMgr.create(<?=$this->gridTranB?>);
        this.gridTranB.__suscribeToModelEvent('TransferenciasSalientes');
        // Tarjetas de Credito
        this.gridCuponesTarjetas   = Ext.ComponentMgr.create(<?=$this->gridCuponesTarjetas?>);
        this.gridCuponesTarjetas.__suscribeToModelEvent('TarjetasDeCreditoCuponesSalientes');
        // Cantidades de Facturas Compras
        this.gridComprobantesArt = Ext.ComponentMgr.create(<?=$this->gridComprobantesArt?>);
        // Ordenes de Pagos Detalls (pagos)
        this.gridOPD 	 = Ext.ComponentMgr.create(<?=$this->gridOPD?>);

        this.gridOPD.store.on({
            add:    { scope: this, fn: this.refreshSaldo },
            remove: { scope: this, fn: this.refreshSaldo },
            load:   { scope: this, fn: function () {
                        this.refreshSaldo();
                        this.refreshGrillasPagos();
                    }
            }
        });
        this.gridOPD.on({
            afterdeleterows: { scope: this, fn: this.refreshGrillasPagos }
        });
    },

    renderTipoPagos: function () {
        return {
            xtype: 'tabpanel',
            activeItem: 0,
            deferredRender : false,
            items: [{
                    title:'Efectivo',
                    bodyStyle:'padding:25px',
                    layout:'form',
                    items:[
                        {
                            xtype:'xcombo',
                            id : 'cajaPagoDetalleEfectivo',
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
                        {
                            id: 'montoPagoDetalleEfectivo',
                            xtype:'numberfield',
                            fieldLabel:'Monto',
                            width: 80
                        },
                        {
                            xtype:'button', text:'Agregar',
                            handler: function() {
                                var IdOrdenDePago = this.form.getForm().findField('Id');

                                var montoValue = Ext.getCmp('montoPagoDetalleEfectivo').getValue();
                                var cajaValue  = Ext.getCmp('cajaPagoDetalleEfectivo').getValue();

                                if (montoValue > 0 && cajaValue > 0) {
                                    Rad.callRemoteJsonAction({
                                        url: '/Facturacion/ordenesdepagos/agregarefectivo',
                                        method: 'POST',
                                        params: {
                                            idOrdenDePago: IdOrdenDePago.getValue(),
                                            monto: montoValue,
                                            caja: cajaValue
                                        },
                                        success: function (response) {
                                            //this.agregarPagosDeRespuesta(response.pagos)
                                            //this.gridOPD.getGridEl().unmask();
                                            this.gridOPD.store.reload();
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
                this.gridTranB,
                this.gridCuponesTarjetas,
            ]
        };
    },

    renderPaso2: function () {
        return {
            xtype  : 'panel',
            layout : 'border',
            border: false,
            items  : [{
                    region : 'center',
                    layout: 'fit',
                    margins: '2 2 2 2',
                    border: false,
                    items : [
                        this.gridAFC
                    ]
                },{
                    region : 'south',
                    title  : 'Articulos',
                    layout : 'fit',
                    height  : 110,
                    margins: '2 2 2 2',
                    border: false,
                    split  : false,
                    items  : [
                        this.gridComprobantesArt
                    ]
                }]
        }
    },


    renderPaso3: function () {
        return {
            xtype  : 'panel',
            layout : 'border',
            defaults: {border: false},
            items  : [{
                    region : 'center',
                    title : 'Pagos',
                    margins: '2 2 2 2',
                    layout: 'fit',
                    items : [{
                            items: [
                                this.gridOPD,
                                {
                                    height: 40,
                                    border: false,
                                    html:'<span id=\'factCompraPagosSaldo\' style=\'FONT-SIZE:13pt;padding-left:5px;\'>Saldo: <span id=\'saldoOrdenPago\'>0.00</span> - De: <span id=\'totalOrdenPago\'>0.00</span></span><img id=\'papeleraOrdenesDePago\' qtip=\'Arrastre los pagos aqui para borrarlos\' src=\'/images/papelera.png\' style=\'float:right;\'>'
                                }
                            ]
                        }]
                },{
                    region : 'west',
                    title  : 'Medios de pago',
                    layout : 'fit',
                    margins: '2 2 2 2',
                    width  : 520,
                    split  : false,
                    items  : [
                        this.renderTipoPagos()
                    ]
                }]
        }
    },

    renderPaso4: function () {
        return {
            layout: 'fit',
            border: false,
            items:  [{
                    xtype		: 'iframepanel',
                    id 			: 'impresionODePagoHtml',
                    bodyStyle	: 'background-color:white;'
                }],
            buttons : [{
                text:       'Imprimir y cerrar Orden de Pago',
                scope: this,
                handler: function() {
                    var IdOrdenDePago = this.form.getForm().findField('Id');
                    var PersonaOrdenDePago = this.form.getForm().findField('Persona');
                    Models.Base_Model_ProveedoresMapper.getIBProximosVencimientosCM05(PersonaOrdenDePago.getValue(), function(result, e) {
                        if (e.status) {
                            if ( result > 0 ) {
                                Ext.Msg.confirm('Atencion','El formulario CM05 de Ingresos Brutos del Proveedor se encuentra vencido. Continuar ?',function(btn) {
                                        if (btn == 'yes') {
                                            Rad.callRemoteJsonAction({
                                                url: '/Facturacion/ordenesdepagos/pagarordendepago',
                                                method: 'POST',
                                                scope:  this,
                                                params: {idOrdenDePago: IdOrdenDePago.getValue() },
                                                success: function (response) {
                                                    this.publish('/desktop/modules/Window/birtreporter', {
                                                    action: 'launch',
                                                    template: 'Comp_OrdenDePago_Ver',
                                                    id: IdOrdenDePago.getValue(),
                                                    width:  645,
                                                    height: 400
                                                    });
                                                    this.grid.abmWindow.closeAbm();
                                                }
                                            });
                                        }
                                 }, this);
                            } else {
                                Rad.callRemoteJsonAction({
                                    url: '/Facturacion/ordenesdepagos/pagarordendepago',
                                    method: 'POST',
                                    scope:  this,
                                    params: {idOrdenDePago: IdOrdenDePago.getValue() },
                                    success: function (response) {
                                        this.publish('/desktop/modules/Window/birtreporter', {
                                        action: 'launch',
                                        template: 'Comp_OrdenDePago_Ver',
                                        id: IdOrdenDePago.getValue(),
                                        width:  645,
                                        height: 400
                                        });
                                        this.grid.abmWindow.closeAbm();
                                    }
                                });
                            }
                        }

                    }, this);
                    
                }
            }]

        };
    },

    refreshGrillasPagos: function () {
        this.gridCheques.store.reload();
        this.gridTranB.store.reload();
        this.gridCuponesTarjetas.store.reload();
    },

    refreshSaldo: function () {
        var montoPagado = parseFloat(this.gridOPD.plugins[0].totales.PrecioUnitario).toFixed(2);
        saldo = (this.montoAPagar - montoPagado).toFixed(2);
        Ext.select('#saldoOrdenPago').update(saldo);
    },

    /**
     * Creamos la grilla principal y le agrego botones al toolbar
     */
    createGrid: function () {
        this.grid = Ext.ComponentMgr.create(<?=$this->grid?>);

        this.grid.getTopToolbar().addButton([
            this.anular(),
            {xtype:'tbseparator'},
                {
                text: 'Imprimir Retenciones',
                icon: 'images/printer.png',
                cls: 'x-btn-text-icon',
                scope: this.grid,
                handler: function () {
                    selected = this.getSelectionModel().getSelected();
                    if (selected) {

                       if (selected.get('Cerrado')=='0') {
                          Ext.Msg.show({ title: 'Atencion', 
                                         msg: 'Este registro aun no esta cerrado.',
                                         modal: true,
                                         icon: Ext.Msg.WARNING,
                                         buttons: Ext.Msg.OK
                                      });
                          return;
                        }

                        Models.Facturacion_Model_OrdenesDePagosMapper.getTotalRetenciones(
                        selected.id,
                        function(result, e) { if(e.status) {
                                                if ( parseFloat(result).toFixed(2) > 0 ) {

                                                    this.publish('/desktop/modules/Window/birtreporter', {
                                                        action: 'launch',
                                                        template: 'Retenciones',
                                                        id: selected.id,
                                                        output: 'pdf',
                                                        width:  600,
                                                        height: 800
                                                    });

                                                } else { 

                                                    Ext.Msg.show({ title: 'Atencion',
                                                     msg: 'Este registro NO presenta retenciones.',
                                                     modal: true,
                                                     icon: Ext.Msg.WARNING,
                                                     buttons: Ext.Msg.OK
                                                    });
                                                }
                                            }
                        },
                        this
                    );
                        
                    } else {
                        Ext.Msg.alert('Atencion', 'Seleccione un registro para ver el reporte');
                    }
                }
            },
            {xtype: 'tbfill'},
            {
                icon:	'images/wrench.png',
                cls:	'x-btn-text-icon',
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

    anular: function ()
    {
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
                if (selected.get('Cerrado')=='0') {
                    Ext.Msg.show({
                        title: 'Atencion',
                        msg: 'Este registro aun no esta cerrado.',
                        modal: true,
                        icon: Ext.Msg.WARNING,
                        buttons: Ext.Msg.OK
                    });
                    return;
                }

                if (Ext.Msg.confirm('Atencion','Está seguro que desea anular el comprobante seleccionado?',function(btn) {
                    if (btn == 'yes') {
                        var id = selected.get('Id');
                        this.form.record = selected;

                        Models.Facturacion_Model_OrdenesDePagosMapper.anular(id, Ext.emtpyFn, this);
                        Models.Facturacion_Model_OrdenesDePagosMapper.get(id, function(result, e) {
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
            height:500,
            border: false,
            shim:   false,
            animCollapse: false,
            layout: 'fit',
            items: [
                this.grid
            ]
        });
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
                            Models.Facturacion_Model_OrdenesDePagosMapper.cambiarImputacionIva(id,value, function(result, e) {
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