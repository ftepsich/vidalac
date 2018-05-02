Ext.ns( 'Apps' );

Apps.<?=$this->name?> = Ext.extend(RadDesktop.Module, {
    title: '<?=$this->title?>',
    appChannel: '/desktop/modules<?=$this->url()?>',
    requires: [
        '/direct/Facturacion/FacturasCompras?javascript',
        '/direct/Base/Personas?javascript',
        '/direct/Contable/LibrosIVA?javascript'

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
        if ( !win ) {
            this.createGrid();
            this.createEditorWindow();
            win = this.create();
        }

        win.show();
    },

    checkTipoCmp: function() {
        var combo = this.form.getForm().findField('Persona');
        var tipo  = this.form.getForm().findField('TipoDeComprobante');
        var tipoData = tipo.store.getById(tipo.getValue());
        var ComprobanteRelacionado = this.form.getForm().findField('ComprobanteRelacionado');

        var urlFacturasCompras = '/datagateway/combolist/fetch/FacturasDeCompras/model/FacturasCompras/m/Facturacion';
        var urlAdelantoVtaFactura = '/datagateway/combolist/fetch/ParaAdelantoPorVtaDeFactura/model/Facturas/m/Facturacion';

        // Factura Compra (Ingreso Comprobantes)
        // if (tipoData.data.Grupo == 1) {
        ComprobanteRelacionado.setValue(null);
        ComprobanteRelacionado.mustFilter = true;
        combo.store.baseParams.EsProveedor = 1;
        ComprobanteRelacionado.store.proxy.api.read.url = urlFacturasCompras;
        // TGC Gastos Bancarios
        // } else if (tipoData.data.Grupo == 14) {
        //     ComprobanteRelacionado.mustFilter = false;
        //     delete combo.store.baseParams.EsProveedor;
        //     ComprobanteRelacionado.store.proxy.api.read.url = urlAdelantoVtaFactura;
        //     ComprobanteRelacionado.enable();
        // // TGC (liq de GB) Gastos Bancarios por cesion de Factura
        // } else if (tipoData.data.Grupo == 15) {
        //     ComprobanteRelacionado.mustFilter = false;
        //     delete combo.store.baseParams.EsProveedor;
        //     ComprobanteRelacionado.store.proxy.api.read.url = urlAdelantoVtaFactura;
        //     ComprobanteRelacionado.enable();
        // // TGC Liquidacion de Cheques
        // } else if (tipoData.data.Grupo == 16) {
        //     ComprobanteRelacionado.mustFilter = false;
        //     delete combo.store.baseParams.EsProveedor;
        //     ComprobanteRelacionado.disable();
        // } else {
        //     ComprobanteRelacionado.mustFilter = true;
        //     delete combo.store.baseParams.EsProveedor;
        //     ComprobanteRelacionado.store.proxy.api.read.url = urlFacturasCompras;
        //     ComprobanteRelacionado.enable();
        // }

        // Tengo que filtrar el Comprobante Relacionado por la Persona seleccionada?
        // if (ComprobanteRelacionado.mustFilter) {
        //     var url = '/datagateway/combolist/fetch/FacturasDeCompras/model/FacturasCompras/m/Facturacion'
        //     ComprobanteRelacionado.store.proxy.api.read.url = url;
        //     // ComprobanteRelacionado.enable();
        // }

    },

    /**
     * Crea la ventana del Abm
     */
    createEditorWindow: function () {
        // Formulario principal
        this.form   = Ext.ComponentMgr.create(<?=$this->form?>);

        // this.addListenerTipoComprobante(this.form);
        this.addListenerPersona(this.form);
        this.addListenerFechaEmision(this.form);
     
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
            width  : 1000,
            height : 450,
            border : false,
            layout : 'fit',
            ishidden : true,
            title  : 'Ingreso de Comprobante',
            plain  : true,
            items  : this.wizard,
            form   : this.form,
            grid   : this.grid,
            getForm: function() {
                return this.form;
            }
        }, Rad.ABMWindow);

        // si la ventana se esconde volvemos al primer paso
        this.grid.abmWindow.on('hide', function() {
            this.wizard.setActiveItem(0);
            id = this.form.getForm().findField('Id').getValue();
            if (id != 0) {
                Models.Facturacion_Model_FacturasComprasMapper.get(id, function(result, e) {
                    if (e.status) {
                        this.form.updateGridStoreRecord(result);
                    }
                }, this);
            }
        }, this);

        this.grid.abmWindow.on('show', function() {
            var form = this.form.getForm();
            var caja = form.findField('Caja');
            // Contado requiere que se seleccione una caja
            if (form.findField('CondicionDePago').getValue() == 2) {
                caja.show();
                caja.allowBlank = false;
            } else {
                caja.hide();
                caja.allowBlank = true;
            }
        }, this);

    },

    // addListenerTipoComprobante: function(form) {
    //     var tipo  = this.form.getForm().findField('TipoDeComprobante');

    //     var tmpFunc = function(store, record, op) {
    //         this.checkTipoCmp();
    //         store.un('load', tmpFunc, this);
    //         tipo.store.on('load', tmpFunc, this);
    //     }

    //     tipo.on('select', function(combo, record, index) {
    //         this.checkTipoCmp();
    //     }, this);
    // },

    addListenerFechaEmision: function(form) {

        var fechaEmision = this.form.getForm().findField('FechaEmision');
        var grid = this.grid;

        fechaEmision.on('blur', function(combo, record, index) {
          var fechaEmision = new Date(this.form.getForm().findField('FechaEmision').getValue());
          Models.Contable_Model_LibrosIVAMapper.getFechaHastaUltimoLibroIVA(function(result,e){
             if (e.status) {
                var fechaHastaLibroIVA = Date.parseDate(result,'Y-m-d');
                if ( fechaEmision > fechaHastaLibroIVA ) {
                      Ext.Msg.show({
                          title : 'Atencion',
                          msg : ' La Fecha de Emisión que intenta ingresar no es válida. <br> Es posterior al último Libro de IVA creado.',
                          width : 400,
                          closable : false,
                          buttons : Ext.Msg.OK,
                          multiline : false,
                          fn : function() { grid.abmWindow.closeAbm(); },
                          icon : Ext.Msg.WARNING
                      });
                      return;
                }
             }
          },this);
        }, this);
    },

    addListenerPersona: function(form) {
        var persona  = this.form.getForm().findField('Persona');
        var grid     = this.grid;
        // var tmpFunc = function(store, record, op) {
        //     this.checkTipoCmp();
        //     store.un('load', tmpFunc, this);
        //     persona.store.on('load', tmpFunc, this);
        // }

        persona.on('select', function(combo, record, index) {
            Models.Base_Model_PersonasMapper.getBloqueado(record.data.Id, function(result, e) {
                if (e.status) {
                    if ( result == 1 ) {
                        Ext.Msg.show({
                            title : 'Atencion',
                            msg : 'El Proveedor seleccionado se encuentra BLOQUEADO.<br><br> No puede utilizarse para la operación que intenta realizar.',
                            width : 400,
                            closable : false,
                            buttons : Ext.Msg.OK,
                            multiline : false,
                            fn : function() { persona.reset(); grid.abmWindow.closeAbm(); },
                            icon : Ext.Msg.WARNING
                        });
                        return;
                    }
                }
            }, this);
            this.form.getForm().findField('ComprobanteRelacionado').setValue(null);
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
                // Paso 0
                this.renderWizardItem('Ingresar los datos del Comprobante:','',this.form),
                // Paso 1
                this.renderWizardItem('Seleccionar los Remitos que va a facturar:','',this.renderPaso1()),
                // this.renderWizardItem('Seleccionar los Cheques vendidos:','',this.gridCheque),
                // this.renderWizardItem('Agregar artículos y completar datos:','',this.gridArticulos),
                // Paso 2
                this.renderWizardItem('Agregar artículos y completar datos:','',this.renderArticulos()),
                // Paso 3
                this.renderWizardItem('Ingresar los conceptos impositivos:','',this.gridCI),
                // Paso 4
                this.renderWizardItem('Finalizar Comprobante:','',this.renderPaso4()),
            ]
        });
        // Logica del Wizard
        this.wizard.on('activate', function (i) {
            switch (i) {
                case 1: // Si activo el paso 1 cargo la grilla Remitos
                    var form = this.form.getForm();
                    var id   = form.findField('Id').getValue();
                    var proveedor  = form.findField('Persona').getValue();
                    var detailGrid = {remotefield: 'ComprobantePadre', localfield: 'Id'};
                    this.gridRemito.setPermanentFilter(0, 'Persona', proveedor);
                    this.gridRemito.loadAsDetailGrid(detailGrid, id);
                    break;
                // case 2: // Si activo el paso 2 cargo la grilla Cheques
                //     form = this.form.getForm();
                //     id   = form.findField('Id').getValue();
                //     detailGrid = {remotefield: 'Comprobante', localfield: 'Id'};
                //     this.gridCheque.loadAsDetailGrid(detailGrid, id);
                //     break;
                case 2: // cargamos la grilla de articulos para el paso 2
                    var detailGrid = {remotefield: 'Comprobante', localfield: 'Id'};
                    var form = this.form.getForm();
                    var id   = form.findField('Id').getValue();
                    // le seteamos el formulario padre para que saque los valores automaticamente
                    this.gridArticulos.parentForm = this.form;
                    this.gridArticulos.loadAsDetailGrid(detailGrid, id);
                    break;
                case 3: //cargamos la grilla de conceptos para el paso 3
                    var detailGrid = {remotefield: 'ComprobantePadre', localfield: 'Id'};
                    var form = this.form.getForm();
                    var id   = form.findField('Id').getValue();

                    this.gridCI.parentForm = this.form;
                    this.gridCI.loadAsDetailGrid(detailGrid, id);
                    break;
                case 4:
                    Ext.getCmp('impresionFActuraCompraHtml').setSrc('/Window/birtreporter/report?template=Comp_FacturaRecibida_Ver&output=html&id='+ this.form.getForm().findField('Id').getValue());
                    break;
                // case 5:
                //     Ext.getCmp('impresionFActuraCompraHtml').setSrc('/Window/birtreporter/report?template=ComprobanteFactura&output=html&id='+ this.form.getForm().findField('Id').getValue());
                //     break;
            }
        }, this);

        // this.wizard.on('prev', function (i) {
        //     switch (i) {
        //         case 3:
        //             var tipo = this.form.getForm().findField('TipoDeComprobante');
        //             if (tipo.getValue() < 28) {
        //                 this.wizard.setActiveItem(1);
        //                 return false;
        //             } else {
        //                 if (tipo.getValue() != 50) {
        //                     this.wizard.setActiveItem(0);
        //                     return false;
        //                 }
        //             }
        //             break;
        //         case 2:
        //             this.wizard.setActiveItem(0);
        //             return false;
        //             break;
        //     }
        // }, this);

        // var i es el paso en el que estoy
        this.wizard.on('next', function (i) {

            switch (i) {
                case 0:
                    this.form.submit();
                    return false;
                    break;
                case 1:
                    this.gridRemito.saveRelation();
                    return false;
                    break;
                // case 2:
                //     this.gridCheque.saveRelation();
                //     return false;
                //     break;
                case 2:
                    var id = this.form.getForm().findField('Id').getValue();
                    Models.Facturacion_Model_FacturasComprasMapper.insertarConceptos(id, function(result, e) {
                        if (e.status) {
                            this.wizard.setActiveItem(3);
                        }
                    }, this);
                    return false;
                    break;
            }
        }, this);

        this.wizard.on('finish', function(i) {
            this.grid.abmWindow.closeAbm();
        }, this);
    },

    /**
     * Creamos las grillas secundarias y su logica
     */
    createSecondaryGrids: function () {
        // Remitos
        this.gridRemito = Ext.ComponentMgr.create(<?=$this->gridRemito?>);

        this.gridRemito.on(
            'saverelation',
            function(status) {
                if(status) {
                    this.wizard.setActiveItem(2);
                }
            },
            this
        );
        this.gridRemitoArt = Ext.ComponentMgr.create(<?=$this->gridRemitoArt?>);
        this.gridArticulosRel = Ext.ComponentMgr.create(<?=$this->gridArticulosRel?>);
        this.gridArticulosRel.on('rowdblclick', function (Grid, rowIndex, e) {
            var record = this.gridArticulosRel.store.getAt(rowIndex);
            var articulo = this.gridArticulos.getSelectionModel().getSelected();
            articulo.set('PrecioUnitario', record.data.PrecioUnitario);
            articulo.commit();
        }, this);

        // Cheques
        // this.gridCheque = Ext.ComponentMgr.create(<?=$this->gridCheque?>);
        // this.gridCheque.on('saverelation', function(status) {
        //     if(status) {
        //         this.wizard.setActiveItem(3);
        //     }
        // }, this);

        // Articulos de la factura
        this.gridArticulos = Ext.ComponentMgr.create(<?=$this->gridArticulos?>);
        this.gridArticulos.abmForm.reloadGridOnClose = true;
        this.gridArticulos.onAbmWindowShow = function() {
            // Este panel esta en FacturasComprasArticuloswizard.json
            var panel = Ext.getCmp('FacturasComprasArticulosFormpanel');
            var selected = this.getSelectionModel().getSelected();
            var tipoDeComprobante = this.parentForm.getForm().findField('TipoDeComprobante');
            // Tiene que haber otra forma de traer el record seleccionado en el combo!!!!!
            var tipoDeGrupoDeComprobante = tipoDeComprobante.store.getById(tipoDeComprobante.getValue()).data.Grupo;

            if (['14','15','16'].indexOf(tipoDeGrupoDeComprobante) !== -1) {
                panel.cambiarTipo('3');
            } else {
                switch (selected.data.ArticulosTipo) {
                    case '2':
                        panel.cambiarTipo('2');
                        break;
                    case '3':
                        panel.cambiarTipo('1');
                        break;
                    default:
                    case '1':
                        panel.cambiarTipo('0');
                        break;
                }
            }
        }

        // Conceptos imp
        this.gridCI = Ext.ComponentMgr.create(<?=$this->gridCI?>);
    },

    renderPaso1: function () {
        return {
            xtype  : 'panel',
            layout : 'border',
            border: false,
            items  : [
                {
                    region : 'center',
                    title : 'Articulos',
                    layout: 'fit',
                    border: false,
                    margins: '2 2 2 2',
                    items : [
                        this.gridRemitoArt
                    ]
                },{
                    region : 'north',
                    layout : 'fit',
                    height  : 200,
                    split  : false,
                    border: false,
                    margins: '2 2 2 2',
                    items  : [
                        this.gridRemito
                    ]
                }
            ]
        }
    },

    renderArticulos: function () {
        return {
            layout: 'border',
            border:false,
            items: [
                {
                    region: 'center',
                    border: false,
                    margins: '2 2 2 2',
                    layout: 'fit',
                    items: this.gridArticulos
                },
                {
                    region: 'south',
                    layout: 'fit',
                    border: false,
                    margins: '2 2 2 2',
                    height: 120,
                    items: this.gridArticulosRel
                }
            ]
        };
    },

    renderPaso4: function () {
        return {
            layout: 'fit',
            border: false,
            items:  [{
                    xtype: 'iframepanel',
                    id: 'impresionFActuraCompraHtml',
                    bodyStyle: 'background-color:white;'
                }],
            buttons : [
                {
                    text: 'Cerrar Comprobante',
                    scope: this,
                    handler: function() {
                        var id = this.form.getForm().findField('Id').getValue();
                        var CondicionDePago = this.form.getForm().findField('CondicionDePago').getValue();
                        var caja = 0;
                        if(CondicionDePago == '2'){
                            caja = this.form.getForm().findField('Caja').getValue();
                        }
                        Models.Facturacion_Model_FacturasComprasMapper.cerrar(id, caja, function(result, e) {
                            if (e.status) {
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
    createGrid: function() {
        this.grid = Ext.ComponentMgr.create(<?=$this->grid?>);

        this.grid.getTopToolbar().addButton([
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
                    },
                    this.botonGenerarOrdenDePago(),
                    {
                        text: 'Cambiar Tipo Comprobante',
                        tooltip :'<b>Cambia el comprobante</b><br>Entre CC Proveedor y Cliente',
                        icon: 'images/page_white_wrench.png',
                        cls: 'x-btn-text-icon',
                        scope:  this.grid,
                        handler: function () {
                            var sel = this.getSelectionModel().getSelected();
                            if (!sel) return;

                            Ext.MessageBox.confirm('Atención','Quieres cambiar el comprobante de cta. cte. (Proveedor - Cliente)<br><br><span style="color:red">Desea continuar?</span>',function(btn){
                                if (btn == 'yes') {
                                    Rad.callRemoteJsonAction({
                                        params: {
                                            'id': sel.data.Id
                                        },
                                        url: '/Facturacion/FacturasCompras/cambiartipo',
                                        scope: this,
                                        success: function (response) {
                                            this.store.reload();
                                        }
                                     });
                                }
                            }, this);
                        }
                    },
               ]
            }
        ]);
    },

    botonGenerarOrdenDePago: function() {
        return {
            text: 'Generar Orden de Pago',
            icon: 'images/arrow_switch.png',
            scope: this,
            handler: function() {
                sel = this.grid.getSelectionModel().getSelected();
                if (!sel) {
                    Ext.Msg.alert('Atencion', 'Seleccione un comprobante');
                    return;
                }
                this.win = app.desktop.createWindow({
                    id: this.id+'-generarODP',
                    title: 'Generar Orden de Pago para Comprobante',
                    width: 400,
                    height: 150,
                    grid: this.grid,
                    border: false,
                    frame: true,
                    modal: true,
                    animCollapse: false,
                    layout: 'fit',
                    items: {
                        xtype: 'radform',
                        border: false,
                        items: [{
                            xtype: 'xcombo',
                            anchor: '100%',
                            minChars: 3,
                            displayField: 'Descripcion',
                            autoLoad: false,
                            autoSelect: true,
                            selectOnFocus: true,
                            forceSelection: true,
                            forceReload: true,
                            hiddenName: 'Caja',
                            loadingText: 'Cargando...',
                            lazyRender: true,
                            searchField: 'Descripcion',
                            store: new Ext.data.JsonStore({
                                url: 'datagateway/combolist/m/Contable/model/Cajas',
                                storeId: 'CajaStore'
                            }),
                            typeAhead: false,
                            valueField: 'Id',
                            pageSize: 10,
                            editable: true,
                            autocomplete: true,
                            allowBlank: false,
                            fieldLabel: 'Caja',
                            name: 'Caja',
                            scope: this
                        }]
                    },
                    buttons: [{
                        text: 'Aceptar',
                        scope: this,
                        handler: function() {
                            //var win = this.findParentByType('window');
                            var form = this.win.items.first().getForm();
                            if (form.isValid()) {
                                var caja = form.findField('Caja');
                                var id = sel.data.Id;
                                Ext.Msg.confirm('Confirmar', '¿Desea generar una Orden de Pago de este comprobante?', function(btn) {
                                    if (btn == 'yes'){
                                        Models.Facturacion_Model_FacturasComprasMapper.generarOrdenDePagoDesdeControlador(id, caja.getValue(), function(result, e) {
                                            if (e.status) {
                                                app.publish('/desktop/notify', {title: 'Orden de Pago', html:'La Orden de Pago fue creada con exito'});
                                                this.win.hide();
                                                this.win.grid.store.reload();
                                            }
                                        }, this);
                                    }
                                }, this);
                            } else {
                                Ext.Msg.alert('Atencion', 'Debe completar todos los campos requeridos');
                            }
                        }
                    }]
                });
                this.win.show();
            }
        }
    },

    /**
     * Crea la ventana del modulo
     */
    create: function() {
        return app.desktop.createWindow({
            id: this.id+'-win',
            title:  this.title,
            width:  1000,
            height: 500,
            border: false,
            shim:   false,
            animCollapse: false,
            layout: 'fit',
            items: [
                this.grid
            ]
        });
    },

    cambiarImputacionIva: function (id, row) {
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
                store:  new Ext.data.JsonStore(
                    {
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
                width  : 300,
                height : 110,
                border : false,
                bodyStyle:'background:white',
                padding: 5,
                layout : 'form',
                closeAction: 'hide',
                maximizable: false,
                minimizable: false,
                resizable: false,
                modal: true,
                ishidden : true,
                title  : 'Cambiar Imputacion de Libro IVA',
                plain  : true,
                items  : this.comboLibrosIVA,
                buttons: [
                    {
                        text: 'Imputar',
                        scope: this,
                        handler: function() {
                            var value = this.comboLibrosIVA.getValue();
                            if (!value) return;
                            Models.Facturacion_Model_FacturasComprasMapper.cambiarImputacionIva(this.windowCambiarImputacionIVA.idComprobante, value, function(result, e) {
                                if (e.status) {
                                    this.windowCambiarImputacionIVA.hide();
                                    this.grid.store.reload();
                                }
                            }, this);
                        }
                    }
                ]
            });
        }
        this.windowCambiarImputacionIVA.idComprobante = id;
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
                items : [
                    {
                        region : 'north',
                        layout: 'fit',
                        border: false,
                        height : 50,
                        items : {
                            layout: 'fit',
                            border: false,
                            html: "<img style='float:right;' src='/images/268498431.png'><Font style='COLOR:#336699;FONT-FAMILY:Arial;FONT-SIZE:16pt;text-align:center;PADDING-TOP:50px;'>"+titulo+"</font><br><Font style='COLOR:#336699;FONT-FAMILY:Arial;FONT-SIZE:10pt;text-align:center;padding-left:5px;'>"+subtitulo+"</font>"
                        }
                    },
                    {
                        region : 'center',
                        layout: 'fit',
                        border: false,
                        items : {
                                layout: 'fit',
                                border: false,
                                items: contenido
                            }
                    }
                ]
            }
        }
    }
});

new Apps.<?=$this->name?>();
