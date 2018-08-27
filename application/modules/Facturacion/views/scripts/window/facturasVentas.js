Ext.ns( 'Apps' );

Apps.<?=$this->name?> = Ext.extend(RadDesktop.Module, {
    title: '<?=$this->title?>',
    appChannel: '/desktop/modules<?=$this->url()?>',
    requires: [
      '/direct/Contable/LibrosIVA?javascript',
        '/direct/Facturacion/FacturasVentas?javascript',
        '/direct/Base/Personas?javascript',
        '/direct/Base/Clientes?javascript'
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
        }
        win.show();
    },

    /**
     * Crea la ventana del Abm
     */
    createEditorWindow: function () {
        // Formulario principal
        this.form       = Ext.ComponentMgr.create(<?=$this->form?>);
        this.formExport = Ext.ComponentMgr.create(<?=$this->formExport?>);

        // despues del submit exitoso se pasa al paso 1 del wizard
        this.form.on(
            'actioncomplete',
            function() {
                var tipo = this.form.getForm().findField('TipoDeComprobante').getValue();
                var compExp = [27,59,61];
                // si es un comprobante de exportacion vamos al paso 1 y cargamos el comprobanteDeExportacion
                if (compExp.indexOf(parseInt(tipo)) != -1) {
                    Models.Facturacion_Model_FacturasVentasMapper.getComprobanteExportacion(this.form.record.data.Id, function (result, e) {
                        if (e.status) {
                            this.formExport.loadRecord(new Ext.data.Record(result, result.Id));
                            this.wizard.setActiveItem(1);
                        }
                    }, this);
                    return;
                }

                if (tipo < 29)
                    this.wizard.setActiveItem(2);
                else
                    this.wizard.setActiveItem(3);
            },
            this
        );

        this.formExport.on(
            'actioncomplete',
            function() {
                var tipo = this.form.getForm().findField('TipoDeComprobante').getValue();
                if (tipo == 27)
                    this.wizard.setActiveItem(2);
                else
                    this.wizard.setActiveItem(3);
            },
            this
        );

        this.addExtraListeners();

        this.createWizard();

        this.grid.abmWindow = app.desktop.createWindow({
            autoHideOnSubmit: false,
            width: 1000,
            height: 540,
            border: false,
            layout: 'fit',
            ishidden: true,
            title: 'Emisión de Comprobante',
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
                Models.Facturacion_Model_FacturasVentasMapper.get(id, function (result, e) {
                    if (e.status) {
                        this.form.updateGridStoreRecord(result);
                    }
                }, this);
            }
        }, this);

        this.grid.abmWindow.on('show', function () {
            this.checkTipoCmp();
            this.updateEstadoDeCuentaPorCliente();
        }, this);
    },

    renderExportForm: function() {
        return {
            xtype: 'panel',
            bodyStyle: 'padding: 25px;',
            layout: 'border',
            items: [{
                    layout: 'fit',
                    region: 'north',
                    height: 130,
                    items: [this.formExport]
                },
                this.gridPermisos
            ]
        };
    },

    /**
     * Agrega la logica adicional para los campos del formulario
     */
    addExtraListeners: function() {
        var form = this.form.getForm();
        var grid = this.grid;
        // Campo Persona (Cliente)
        form.findField('Persona').on('select', function (combo, record, index) {
            Models.Base_Model_PersonasMapper.getBloqueado(record.data.Id, function(result, e) {
                if (e.status) {
                    if ( result == 1 ) {
                        Ext.Msg.show({
                            title : 'Atencion',
                            msg : 'El Cliente seleccionado se encuentra BLOQUEADO.<br><br> No puede utilizarse para la operación que intenta realizar.',
                            width : 400,
                            closable : false,
                            buttons : Ext.Msg.OK,
                            multiline : false,
                            fn : function() { form.findField('Persona').reset(); grid.abmWindow.closeAbm(); },
                            icon : Ext.Msg.WARNING
                        });
                        return;
                    }
                }
            }, this);
            Models.Base_Model_ClientesMapper.getIBItems(record.data.Id, function(result, e) {
                if (e.status) {
                    if ( result == 0 ) {
                        Ext.Msg.show({
                            title : 'Atención',
                            msg : 'El Cliente no tiene situación impositiva cargada. Desde Continuar ?',
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

            this.updateEstadoDeCuentaPorCliente(record.data.Id);
            // Setea el tipo de comprobante segun la modalidad de iva y el tipo de comprobante
            var form = this.form.getForm();
            var combo = form.findField('TipoDeComprobante');
            var tipo = combo.getValue();
            if (!tipo) return;

            var compExp = [27,59,61];
            // si es un comprobante de exportacion
            if (compExp.indexOf(parseInt(tipo)) != -1) {
                return;
            }

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
			Models.Facturacion_Model_FacturasVentasMapper.recuperarProximoNumero(punto, tipo, function(result, e) {
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

        Models.Facturacion_Model_FacturasVentasMapper.getEstadoDeCuentaPorCliente(idCliente, function (result, e) {
            if (e.status) {
                var template = Ext.getCmp('FacturasVentasWizard_DetalleCuentasTemplate');
                var detailEl = template.body;
                template.overwrite(detailEl, result);
                detailEl.slideIn('l', { stopFx: true, duration: .2 });
            }
        }, this);
    },

    checkTipoCmp: function () {
        var combo = this.form.getForm().findField('Persona');
        var tipo = this.form.getForm().findField('TipoDeComprobante');
        var ComprobanteRelacionado = this.form.getForm().findField('ComprobanteRelacionado');
        if (tipo.getValue() < 29) {
            ComprobanteRelacionado.disable();
            ComprobanteRelacionado.setValue(null);
            combo.store.baseParams.EsCliente = 1;
        } else {
            ComprobanteRelacionado.enable();
            delete combo.store.baseParams.EsCliente;
        }
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
                this.renderWizardItem('Ingresar datos Exportación', '', this.renderExportForm()),
                this.renderWizardItem('Seleccionar los remitos asociados', '', this.renderPaso1()),
                this.renderWizardItem('Agregar art&iacute;culos y completar datos', '', this.gridArticulos),
                this.renderWizardItem('Ingresar los conceptos impositivos', '', this.gridCI),
                this.renderWizardItem('Finalizar comprobante', '', this.renderPaso4())
            ]
        });

        // Logica del Wizard
        this.wizard.on(
            'activate',
            function (i) {
                switch (i) {
                    // si activo el paso 1 cargo la grilla de Permisos de Exportación
                    case 1:
                        var id   		= this.form.record.data.Id;
                        var detailGrid 	= {remotefield: 'Comprobante', localfield: 'Id'};
                        this.gridPermisos.parentForm = this.form;
                        this.gridPermisos.loadAsDetailGrid(detailGrid, id);
                        break;
                    // si activo el paso 2 cargo la grilla Remitos
                    case 2:
                        var form 		= this.form.getForm();
                        var id   		= form.findField('Id').getValue();
                        var cliente  	= form.findField('Persona').getValue();
                        var detailGrid 	= {remotefield: 'ComprobantePadre', localfield: 'Id'};
                        this.gridRemito.setPermanentFilter(0, 'Persona', cliente);
                        this.gridRemito.loadAsDetailGrid(detailGrid, id);
                        break;
                    // cargamos la grilla de articulos para el paso 3
                    case 3:
                        var form 		= this.form.getForm();
                        var id   		= form.findField('Id').getValue();
                        var detailGrid 	= {remotefield: 'Comprobante', localfield: 'Id'};
                        this.gridArticulos.parentForm = this.form;
                        this.gridArticulos.loadAsDetailGrid(detailGrid, id);
                        break;
                    // cargamos la grilla de conceptos para el paso 4
                    case 4:
                        var form 		= this.form.getForm();
                        var id   		= form.findField('Id').getValue();
                        var detailGrid 	= {remotefield: 'ComprobantePadre', localfield: 'Id'};
                        this.gridCI.parentForm = this.form;
                        this.gridCI.loadAsDetailGrid(detailGrid, id);
                        break;
                    // vista previa de reporte
                    case 5:
                        var id 			= this.form.getForm().findField('Id').getValue();
						//var urlReporte 	= '/Window/birtreporter/report?template=ComprobanteFactura&output=html&id=' + id;
                        var urlReporte 	= '/Window/birtreporter/report?template=Comp_FacturaEmitida_Ver&output=html&id=' + id;
                        Ext.getCmp('impresionFacturaVentaHtml').setSrc(urlReporte);
                        break;
                }
            },
            this
        );

        this.wizard.on('prev', function (i) {
            switch(i) {
                case 2:
                    var tipo = this.form.getForm().findField('TipoDeComprobante');
                    if (tipo.getValue() > 28) {
                        this.wizard.setActiveItem(0);
                        return false;
                    }
            }
        }, this);

        this.wizard.on('next', function (i) {
            switch(i) {
                case 0:
                    this.form.submit();
                    return false;
                case 1:
                    this.formExport.submit();
                    return false;
                case 2:
                    this.gridRemito.saveRelation();
                    return false;
                case 3:
                    // Inserta los conceptos desde el controlador
                    var id = this.form.getForm().findField('Id').getValue();
                    Models.Facturacion_Model_FacturasVentasMapper.insertarConceptosDesdeControlador(id, function (result, e) {
                        if (e.status)
                            this.wizard.setActiveItem(4);
                    }, this);
                    return false;
                    break;
                 case 4:
                    // Controlo que no hayan conceptos con monto menor o igual a 0 (cero)
                    var idFact = this.form.getForm().findField('Id');
                    Models.Facturacion_Model_FacturasVentasMapper.getControlTotalConcepto(idFact.getValue(), function(result, e) {
                        if (e.status)
                            this.wizard.setActiveItem(5);
                    }, this);
                    return false;
                    break;
            }
        }, this);

        this.wizard.on('finish', function (i) {
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
            function (status) {
                if(status)
                    this.wizard.setActiveItem(3);
            },
            this
        );
        this.gridRemitoArt = Ext.ComponentMgr.create(<?=$this->gridRemitoArt?>);


        // Grilla  Articulos de la factura
        this.gridArticulos = Ext.ComponentMgr.create(<?=$this->gridArticulos?>);
        this.gridArticulos.onAbmWindowShow = function() {
            var panel = Ext.getCmp('FacturasVentasArticulosForm');
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
        this.gridArticulos.abmForm.reloadGridOnClose = true;

        // Grilla Permisos de Embarques (Exportacion)
        this.gridPermisos  = Ext.ComponentMgr.create(<?=$this->gridPermisos?>);

        // Grilla Conceptos imp
        this.gridCI = Ext.ComponentMgr.create(<?=$this->gridCI?>);
    },

    renderPaso1: function () {
        return {
            xtype: 'panel',
            layout: 'border',
            border: false,
            items: [
                {
                    region: 'center',
                    title: 'Articulos',
                    layout: 'fit',
                    border: false,
                    margins: '2 2 2 2',
                    items: this.gridRemitoArt
                },
                {
                    region: 'north',
                    layout: 'fit',
                    height : 200,
                    border: false,
                    margins: '2 2 2 2',
                    split: false,
                    items: this.gridRemito
                }
            ]
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
                        var IdComprobante = this.form.getForm().findField('Id').getValue();
                        var IdPersonaComprobante = this.form.getForm().findField('Persona');
                        Models.Base_Model_ClientesMapper.getIBProximosVencimientosCM05(IdPersonaComprobante.getValue(), function(result, e) {
                            if (e.status) {
                                if ( result > 0 ) {
                                    Ext.Msg.confirm('Atencion','El formulario CM05 de Ingresos Brutos del Cliente se encuentra vencido. Continuar ?',function(btn) {
                                           if (btn == 'yes') {
                                                app.publish('/desktop/wait', 'Cerrando el comprobante');
                                                Models.Facturacion_Model_FacturasVentasMapper.cerrar(IdComprobante, function (result, e) {
                                                    if (e.status) {
                                                        Ext.MessageBox.hide();
                                                        this.grid.abmWindow.closeAbm();
                                                    }
                                                }, this);
                                           }
                                    }, this);
                                } else {
                                    app.publish('/desktop/wait', 'Cerrando el comprobante');
                                    Models.Facturacion_Model_FacturasVentasMapper.cerrar(IdComprobante, function (result, e) {
                                        if (e.status) {
                                            Ext.MessageBox.hide();
                                            this.grid.abmWindow.closeAbm();
                                        }
                                    }, this);
                                }
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
                                        url: '/Facturacion/FacturasVentas/cambiartipo',
                                        scope: this,
                                        success: function (response) {
                                            this.store.reload();
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
                                    Models.Facturacion_Model_FacturasVentasMapper.compensarFacturasConNotas(id, function (result, e) {
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
                            Models.Facturacion_Model_FacturasVentasMapper.cambiarImputacionIva(id,value, function (result, e) {
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
            height: 500,
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
                if (selected.get('Cerrado') == '0') {
                    Ext.Msg.show({
                        title: 'Atención',
                        msg: 'Este registro aun no esta cerrado.',
                        modal: true,
                        icon: Ext.Msg.WARNING,
                        buttons: Ext.Msg.OK
                    });
                    return;
                }

                if (Ext.Msg.confirm('Atención','¿Está seguro que desea anular el comprobante seleccionado?', function (btn) {
                    if (btn == 'yes') {
                        var id = selected.get('Id');
                        this.form.record = selected;

                        Models.Facturacion_Model_FacturasVentasMapper.anular(id, Ext.emtpyFn, this);
                        Models.Facturacion_Model_FacturasVentasMapper.get(id, function(result, e) {
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