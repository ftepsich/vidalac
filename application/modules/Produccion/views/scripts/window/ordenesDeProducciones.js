Ext.ns( 'Apps' );

Apps.<?=$this->name?> = Ext.extend(RadDesktop.Module, {
    title: '<?=$this->title?>',
    appChannel: '/desktop/modules<?=$this->url()?>',
    requires: [
        '/direct/Produccion/OrdenesDeProducciones?javascript',
        '/js/ux/grid/ProgressColumn.js',
        '/js/ux/grid/css/ProgressColumn.css',
        '/js/ux/Ext.ux.chartsFilter.js',
        '/js/erp/requerimientosMaterialesPanel.js'
    ],

    /**
     * Filtra la grilla por el id enviado
     */
    eventfind: function (ev) {
        this.createWindow();
        var p = this.grid.buildFilter(0,'Id', ev.value);
        this.grid.store.load({params:p});
    },

    eventsearch: function (ev) {
        this.createWindow();
        var p = this.grid.buildFilter(0,ev.field, ev.value);
        this.grid.store.load({params:p});
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
            width  : 1000,
            height : 520,
            border : false,
            layout : 'fit',
            ishidden : true,
            title  : 'Ordenes de Producciones',
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
        this.grid.abmWindow.on('hide', function(){
            this.wizard.setActiveItem(0);
            id = this.form.getForm().findField('Id').getValue();
            if (id != 0) {
                // Actualizo la grilla
                Models.Produccion_Model_OrdenesDeProduccionesMapper.get(id, function(result, e) {
                    if (e.status) {
                        this.form.updateGridStoreRecord(result);
                    }
                }, this);
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
                this.renderWizardItem('Ingresar los datos de la Orden de Produccion:','',this.form),

                this.renderWizardItem('Requerimientos','',this.renderPaso2()),

                this.renderWizardItem('Pedido de Materia Prima:','',this.renderPaso3()),
                this.renderWizardItem('Finalizar Orden de Produccion:','',this.renderPaso4())
            ]
        });

        // Logica del Wizard
        this.wizard.on(
            'activate',
            function (i) {
                switch(i) {
                    case 1:

                        form = this.form.getForm();
                        ido   = form.findField('Id').getValue();
                        this.panelRequerimientos.loadRequerimientos(ido);
                        break
                    case 2:

                        detailGrid = {remotefield: 'OrdenDeProduccion', localfield: 'Id'};
                        form = this.form.getForm();
                        id   = form.findField('Id').getValue();
                        this.gridDetalles.parentForm = this.form;
                        this.gridDetalles.loadAsDetailGrid(detailGrid, id);
                        break;
                    case 3:
                        Ext.getCmp('impresionOrdenesDeProduccionHtml').setSrc('/Window/birtreporter/report?template=OrdenDeProduccion&output=html&id='+ this.form.getForm().findField('Id').getValue());
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

                    // case 1:
                    //     Models.Produccion_Model_OrdenesDeProduccionesMapper.generarPedidoMateriales(id,'N', function(result, e) {
                    //         if (e.status) {
                    //             this.wizard.setActiveItem(2);
                    //         }
                    //     }, this);
                    //     return false
                    //     break;
                }
            }, this
        );

        this.wizard.on('finish', function(i) {
            this.grid.abmWindow.closeAbm();
        },this);
    },


    /**
     * Creamos las grillas secundarias y su logica
     */
    createSecondaryGrids: function () {

        this.gridDetalles = Ext.ComponentMgr.create(<?=$this->gridDetalles?>);
        this.gridDetalles.abmForm.reloadGridOnClose = true;
        this.gridDetalles.getTopToolbar().addButton([{
            text:    'Regenerar',
            icon: '/images/calculator_edit.png',
            handler: function() {
                 var id = this.form.getForm().findField('Id').getValue();
                 Models.Produccion_Model_OrdenesDeProduccionesMapper.generarPedidoMateriales(id,'S', function(result, e) {
                    if (e.status) {
                         Ext.getCmp('loggeneracionpedidoDeMat').update('<pre>'+result+'</pre>');
                         this.gridDetalles.store.reload();
                    }
                 }, this);
            },
            scope:   this
        }]);

    },

    /**
     * Creamos la grilla principal
     */
    createGrid: function () {
        this.grid = Ext.ComponentMgr.create(<?=$this->grid?>);

        this.grid.onBeforeCreateColumns = function(columns)
        {
            columns.push(
                new Ext.ux.ProgressColumn({
                    header: '% Terminado',
                    width: 105,
                    dataIndex: 'MmisTerminado',
                    divisor: 'Cantidad',
                    align: 'center',
                    getBarClass: function(fraction) {
                        return (fraction > 0.99) ? 'high' : (fraction > 0.50) ? 'medium' : 'low';
                    },
                    renderer: function(value, meta, record, rowIndex, colIndex, store, pct) {
                        return Ext.util.Format.number(pct, "0.00%");
                    }
                })
            );
        }

        this.grid.getTopToolbar().addButton([
            {
                text:   'Ver',
                icon:   'images/printer.png',
                cls :   'x-btn-text-icon',
                scope:  this.grid,
                handler:    function () {
                    selected = this.getSelectionModel().getSelected();
                    if (selected) {
                        this.publish('/desktop/modules/Window/birtreporter', {
                            action: 'launch',
                            template: 'OrdenDeProduccion',
                            id: selected.id,
                            output: 'html',
                            width:  900,
                            height: 800
                        });
                    } else {
                        Ext.Msg.alert('Atencion', 'Seleccione un registro para ver el reporte');
                    }
                }
            },
            { xtype:'tbseparator' },
            this.cancelar(),
            { xtype:'tbseparator' },
            {
                text: 'Enviar Mail',
                iconCls: 'x-btn-text-icon',
                icon: 'images/email_attach.png',
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
                    app.publish('/desktop/modules/js/commonApps/mail.js', {
                        action: 'launch',
                        asunto : 'Orden de Produccion',
                        Persona: selected.get('Persona'),
                        cuerpo : 'Se adjunta Orden de produccion.',
                        url : '/Window/birtreporter/mailreport',
                        baseParams: {
                            id: selected.data.Id,
                            template: 'OrdenDeProduccion'
                        }
                    });

                },
                scope: this
            }
        ]);
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
                        height : 28,
                        items : {
                            layout: 'fit',
                            bodyStyle:'padding: 3px',
                            border: false,
                            html: '<Font style=\'COLOR:#336699;FONT-FAMILY:Arial;FONT-SIZE:16pt;text-align:center;PADDING-TOP:50px;\'>'+titulo+'</font><br><Font style=\'COLOR:#336699;FONT-FAMILY:Arial;FONT-SIZE:10pt;text-align:center;padding-left:5px;\'>'+subtitulo+'</font>'
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
    },

    renderPaso4: function () {
        return {
            layout: 'fit',
            border: false,
            items:  [{
                    xtype: 'iframepanel',
                    id: 'impresionOrdenesDeProduccionHtml',
                    bodyStyle: 'background-color:white;'
                }],
            buttons : [{
                    text:   'Cerrar Orden de Produccion',
                    scope: this,
                    handler: function() {
                        var id = this.form.getForm().findField('Id').getValue();
                        Models.Produccion_Model_OrdenesDeProduccionesMapper.cerrar(id, function(result, e) {
                            if (e.status) {
                                this.grid.abmWindow.closeAbm();
                            }
                        }, this);

                    }
                }]
        };
    },
    renderPaso2: function () {
        this.panelRequerimientos = new Rad.RequerimientosMaterialesPanel();

        return this.panelRequerimientos;
    },

    renderPaso3: function () {
        return {
            xtype  : 'panel',
            layout : 'border',
            border : false,
            items  : [{
                    xtype : 'panel',
                    region : "center",
                    layout : "fit",
                    border : false,
                    items: [this.gridDetalles]
                },{
                    xtype : 'panel',
                    region : "south",
                    height: 200,
                    id: 'loggeneracionpedidoDeMat'
                }]
        }
    },


    // renderPaso3: function () {
    //     return {
    //         layout: 'fit',
    //         border: false,
    //         items:  [this.gridDetalles]
    //     };
    // },

    cancelar: function ()
    {
        return {
            text: 'Cancelar',
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

                if (Ext.Msg.confirm('Atencion','¿Está seguro que desea cancelar la orden de produccion seleccionada?', function(btn) {
                    if (btn == 'yes') {
                        var id = selected.get('Id');
                        this.form.record = selected;

                        Models.Produccion_Model_OrdenesDeProduccionesMapper.cancelar(id, Ext.emtpyFn, this);
                        Models.Produccion_Model_OrdenesDeProduccionesMapper.get(id, function(result, e) {
                            if (e.status) {
                                this.form.updateGridStoreRecord(result);
                            }
                        }, this);
                    }
                }, this));

            },
            scope: this
        }
    }

});

new Apps.<?=$this->name?>();
