Ext.ns( 'Apps' );

Apps.<?=$this->name?> = Ext.extend(RadDesktop.Module, {
    title: '<?=$this->title?>',
    appChannel: '/desktop/modules<?=$this->url()?>',
    requires: [
        '/direct/Facturacion/ComprobantesSinIVA?javascript',
        '/direct/Base/Personas?javascript',
        '/direct/Contable/PeriodosImputacionSinIVA?javascript'
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
            height : 600,
            border : false,
            layout : 'fit',
            ishidden : true,
            title  : 'Ingreso de Comprobante Sin IVA',
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
                Models.Facturacion_Model_ComprobantesSinIVAMapper.get(id, function(result, e) {
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

    addListenerFechaEmision: function(form) {

        var fechaEmision = this.form.getForm().findField('FechaEmision');
        var grid = this.grid;

        fechaEmision.on('blur', function(combo, record, index) {
          var fechaEmision = new Date(this.form.getForm().findField('FechaEmision').getValue());
          Models.Contable_Model_PeriodosImputacionSinIVAMapper.getFechaHastaUltimo(function(result,e){
             if (e.status) {
                var fechaHastaPeriodo = Date.parseDate(result,'Y-m-d');
                if ( fechaEmision > fechaHastaPeriodo ) {
                      Ext.Msg.show({
                          title : 'Atencion',
                          msg : ' La Fecha de Emisión que intenta ingresar no es válida. <br> Es posterior al último periodo de imputación creado.',
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
                this.renderWizardItem('Agregar artículos y completar datos:','',this.renderArticulos()),
                // Paso 2
                this.renderWizardItem('Finalizar Comprobante:','',this.renderCerrar()),
            ]
        });
        // Logica del Wizard
        this.wizard.on('activate', function (i) {
            switch (i) {
                case 1: // cargamos la grilla de articulos para el paso 2
                    var detailGrid = {remotefield: 'Comprobante', localfield: 'Id'};
                    var form = this.form.getForm();
                    var id   = form.findField('Id').getValue();
                    // le seteamos el formulario padre para que saque los valores automaticamente
                    this.gridArticulos.parentForm = this.form;
                    this.gridArticulos.loadAsDetailGrid(detailGrid, id);
                    break;
                case 2:
                    Ext.getCmp('impresionComprobanteSinIVAHtml').setSrc('/Window/birtreporter/report?template=Comp_SinIVAIngresado_Ver&output=html&id='+ this.form.getForm().findField('Id').getValue());
                    break;
            }
        }, this);

        this.wizard.on('next', function (i) {

            switch (i) {
                case 0:
                    this.form.submit();
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
        // Articulos del comprobante
        this.gridArticulos = Ext.ComponentMgr.create(<?=$this->gridArticulos?>);
        this.gridArticulos.abmForm.reloadGridOnClose = true;
        this.gridArticulos.onAbmWindowShow = function() {
            // Este panel esta en ComprobantesSinIVAArticuloswizard.json
            var panel = Ext.getCmp('ComprobantesSinIVAArticulosFormpanel');
            var selected = this.getSelectionModel().getSelected();
            var tipoDeComprobante = this.parentForm.getForm().findField('TipoDeComprobante');
            // Tipo de Articulo Varios 
            panel.cambiarTipo('2');
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
                }
            ]
        };
    },

    renderCerrar: function () {
        return {
            layout: 'fit',
            border: false,
            items:  [{
                    xtype: 'iframepanel',
                    id: 'impresionComprobanteSinIVAHtml',
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
                        Models.Facturacion_Model_ComprobantesSinIVAMapper.cerrar(id, caja, function(result, e) {
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
    },

    /**
     * Crea la ventana del modulo
     */
    create: function() {
        return app.desktop.createWindow({
            id: this.id+'-win',
            title:  this.title,
            width:  1000,
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
