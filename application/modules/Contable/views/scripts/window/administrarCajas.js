Ext.ns( 'Apps' );

Apps.<?=$this->name?> = Ext.extend(RadDesktop.Module, {
	autoStart: true,
	title: '<?=$this->title?>',
	appChannel: '/desktop/modules<?=$this->url()?>',
    requires: [
        '/direct/Contable/CajasMovimientos?javascript'
    ],

	eventfind: function(ev) {
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
            this.grid = Ext.ComponentMgr.create(<?=$this->grid?>);
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
            border : false,
            frame: false,
            items : [{
                region : 'west',
                layout: 'fit',
                width : 320,
                split: true,
                border: false,
                frame: false,
                items: this.grid
            },{
                region : 'center',
                layout: 'fit',
                width : 320,
                split: true,
                border: false,
                frame: false,
                items: this.gridCajasMovimientos
            }]
        }
    },

    _getAbmWindowConfig: function (abm, grid, title) {
        var config = {
            layout      : 'fit',
            width       : 540,
            height      : 210,
            title       : title,
            plain       : true,
            modal       : true,
            bodyStyle   : 'padding:0px;margin:0px;',
            border      : false,
            constrain   : true,
            items       : abm,
            id          : this.id+'_formwindow'+title,
            grid        : grid
        }
        return config;
    },

    createRow: function(abmForm, model, parentStore) {
        Rad.callRemoteJsonAction({
            url:     '/default/datagateway/createrow/model/'+model,
            scope:   this,
            success: function(response) {
                var record = new parentStore.reader.recordType(response.rows[0]);
                record.store = parentStore;
                abmForm.loadRecord(record);
            }
        });
    },

    _createWindowMovimientos: function(abmWindow, abmForm, module, model, title) {
        // creo la ventana del abm si no existe
        if (!this[abmWindow]) {
            this[abmWindow] = app.desktop.createWindow(
                this._getAbmWindowConfig(
                    this.gridCajasMovimientos[abmForm],
                    this.gridCajasMovimientos,
                    title
                ), Rad.ABMWindow);

                this[abmWindow].onCloseWindow = function() {
                    // this.grid.reloadChildGrids(selected);
                    this.grid.addingRow = false;
                    this.grid.disableButtons(false);
                    this.grid.store.reload();
                    this.grid.onAbmWindowHide();
                },


            // le seteo la grilla de CajasMovimientos
            this[abmWindow].grid = this.gridCajasMovimientos;
        }
        // creo el row y lo cargo al form
        this.createRow(
            this.gridCajasMovimientos[abmForm],
            model+'/m/'+module,
            this.gridCajasMovimientos.store
        );

        // var el = this.getGridEl();
        // el.mask();
        this[abmWindow].show();
    },

    createSecGrids: function() {
        this.gridCajasMovimientos = Ext.ComponentMgr.create(<?= $this->gridCajasMovimientos ?>);
        var tbar = this.gridCajasMovimientos.getTopToolbar();
        tbar.addButton([
            {
                text: 'Entrada',
                iconCls: 'add',
                disabled: true,
                scope: this,
                handler: function() {
                    this._createWindowMovimientos('abmWindowEntradas', 'abmFormEntradas',
                        'Contable', 'CajasMovimientosDeEntradas', 'Entrada');
                }
            },
            {
                text: 'Salida',
                iconCls: 'add',
                disabled: true,
                scope: this,
                handler: function() {
                    this._createWindowMovimientos('abmWindowSalidas', 'abmFormSalidas',
                        'Contable', 'CajasMovimientosDeSalidas', 'Salida');
                }
            },
            this.botonMovimientoEntreCajas(),
            { xtype: 'tbfill' },
            { xtype: 'tbseparator' },
            {
                text: 'Entradas',
                icon: 'images/arrow_in.png',
                id: 'botonFiltrosEntradas',
                enableToggle: true,
                pressed: true,
                handler: this.filtrosMovimientos,
                scope: this
            },
            { xtype: 'tbspacer' },
            {
                text: 'Salidas',
                icon: 'images/arrow_out.png',
                id: 'botonFiltrosSalidas',
                enableToggle: true,
                pressed: true,
                handler: this.filtrosMovimientos,
                scope: this
            }
        ]);

    },

    filtrosMovimientos: function() {
        var verEntradas = Ext.getCmp('botonFiltrosEntradas').pressed;
        var verSalidas = Ext.getCmp('botonFiltrosSalidas').pressed;

        var url = '/default/datagateway/list/model/CajasMovimientos/m/Contable';
        var fetch;
        if (verEntradas && !verSalidas)
            fetch = 'DeEntrada';
        else if (verSalidas && !verEntradas)
            fetch = 'DeSalida';

        if (verEntradas || verSalidas) {
            if (fetch)
                url += '/fetch/'+fetch;
            this.gridCajasMovimientos.store.proxy.api.read.url = url;
            this.gridCajasMovimientos.store.proxy.conn.api.read.url = url;
            this.gridCajasMovimientos.store.reload();
        } else {
            this.gridCajasMovimientos.store.removeAll(true);
            this.gridCajasMovimientos.view.refresh();
            var el = this.gridCajasMovimientos.getGridEl();
            el.mask('No se muestran datos...', 'x-mask');
        }
    },

    botonMovimientoEntreCajas: function() {
        return {
            text: 'Movimiento entre cajas',
            icon: 'images/arrow_switch.png',
            scope: this,
            handler: function() {
                var win = app.desktop.createWindow({
                    id: this.id+'-awin',
                    title: 'Movimiento entre Cajas',
                    width: 500,
                    height: 230,
                    store: this.grid.store,
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
                            hiddenName: 'CajaOrigen',
                            loadingText: 'Cargando...',
                            lazyRender: true,
                            searchField: 'Descripcion',
                            store: new Ext.data.JsonStore({
                                url: 'datagateway/combolist/m/Contable/model/Cajas',
                                storeId: 'CajaOrigenStore'
                            }),
                            typeAhead: false,
                            valueField: 'Id',
                            pageSize: 10,
                            editable: true,
                            autocomplete: true,
                            allowBlank: false,
                            fieldLabel: 'Caja Origen',
                            name: 'CajaOrigen',
                            scope: this
                        },
                        {
                            xtype: 'xcombo',
                            anchor: '100%',
                            minChars: 3,
                            displayField: 'Descripcion',
                            autoLoad: false,
                            autoSelect: true,
                            selectOnFocus: true,
                            forceSelection: true,
                            forceReload: true,
                            hiddenName: 'CajaDestino',
                            loadingText: 'Cargando...',
                            lazyRender: true,
                            searchField: 'Descripcion',
                            store: new Ext.data.JsonStore({
                                url: 'datagateway/combolist/m/Contable/model/Cajas',
                                storeId: 'CajaDestinoStore'
                            }),
                            typeAhead: false,
                            valueField: 'Id',
                            pageSize: 10,
                            editable: true,
                            autocomplete: true,
                            allowBlank: false,
                            fieldLabel: 'Caja Destino',
                            name: 'CajaDestino',
                            scope: this
                        },
                        {
                            xtype: 'textfield',
                            fieldLabel: 'Descripcion',
                            name: 'Descripcion',
                            allowBlank: false,
                            anchor: '100%'
                        },
                        {
                            xtype: 'numberfield',
                            fieldLabel: 'Monto',
                            name: 'Monto',
                            allowBlank: false,
                            allowNegative: false
                        },
                        {
                            xtype: 'datefield',
                            fieldLabel: 'Fecha',
                            name: 'Fecha',
                            allowBlank: false
                        }]
                    },
                    buttons: [{
                        text: 'Aceptar',
                        handler: function() {
                            var win = this.findParentByType('window');
                            var form = win.items.first().getForm();
                            if (form.isValid()) {
                                var cajaOrigen = form.findField('CajaOrigen');
                                var cajaDestino = form.findField('CajaDestino');
                                var descripcion = form.findField('Descripcion').getValue();
                                var monto = form.findField('Monto').getValue();
                                var fecha = form.findField('Fecha').getValue().format('Y-m-d');
                                if (cajaOrigen.getValue() == cajaDestino.getValue()) {
                                    Ext.Msg.alert('Atencion', 'La Caja Origen debe ser distinta a la Caja Destino');
                                    return false;
                                }
                                Models.Contable_Model_CajasMovimientosMapper.movimientosEntreCajas(cajaOrigen.getValue(), cajaDestino.getValue(), descripcion, monto, fecha, function(result, e) {
                                    if (e.status) {
                                        win.store.load();
                                        win.close();
                                    }
                                }, this);
                            } else {
                                Ext.Msg.alert('Atencion', 'Debe completar todos los campos requeridos');
                            }
                        }
                    }]
                });
                win.show();
            }
        }
    }

});

new Apps.<?=$this->name?>()