Ext.ns( 'Apps' );

Apps.<?=$this->name?> = Ext.extend(RadDesktop.Module, {
    autoStart: true,
    title: '<?=$this->title?>',
    appChannel: '/desktop/modules<?=$_SERVER['REQUEST_URI']?>',

    itemsSeleccionados: false, // Indica si se seleccionaron palets para el modo despachar o enviar a produccion

    requires: [
        '/direct/Almacenes/Almacenes?javascript',
        '/direct/Produccion/OrdenesDeProducciones?javascript'
    ],
    eventlaunch: function(ev) {
        this.createWindow();
    },

    /**
     * Manejador de modos de la ventana principal
     */
    modoVentana: {
        modo: 'ninguno',
        deposito: null,

        panelAcciones: function(title,accion,show) {
            var pa = Ext.getCmp('almacenes_panel_acciones');
            //pa.setTitle(title);
            // Pongo la pantalla de la accion correspondiente
            pa.get(0).layout.setActiveItem(accion);
            // Pongo la primera pantalla de seleccion de remitos
            pa.get(0).get(accion).layout.setActiveItem(0);
            if (show) pa.show(); else pa.hide();
            pa.ownerCt.doLayout();
        },

        getBotones: function(t) {
            this.botonMoverPredeposito   = Ext.getCmp('botonAlmacenesMoverAPredeposito');
            this.botonAlmacenesDespachar = Ext.getCmp('botonAlmacenesDespachar');
            this.deposito = t.deposito;
            this.scope = t;
        },

        modoNinguno: function () {
            this.modo = 'ninguno';
            this.scope.itemsSeleccionados = false;
            this.panelAcciones('',0, false);
            this.deposito.getTopToolbar().get(3).hide();
            this.botonAlmacenesDespachar.disable();
            this.botonMoverPredeposito.disable();
            statusb = this.deposito.getBottomToolbar();
            statusb.updateSeleccion(' ');
            statusb.updateTotal(' ');
            this.deposito.deposito.asignando = null;
            this.deposito.showTemporal();
            this.deposito.deposito.filtros.quitar();

            // quitamos filtros y resaltados de la grilla mmis
            this.deposito.gridMmis.getView().resaltarNada();
            this.deposito.gridMmis.clearPermanentFilter(1);
            this.deposito.gridMmis.store.reload();

            // quito selecciones
            this.scope.gridRemitos.getSelectionModel().clearSelections();
            this.scope.gridRemitosEntrada.getSelectionModel().clearSelections();
            this.scope.gridODProducciones.getSelectionModel().clearSelections();
            this.scope.gridODProduccionesDetalles.getSelectionModel().clearSelections();
            this.scope.gridRemitosArticulosEntrada.getSelectionModel().clearSelections();
            this.scope.gridRemitosArticulos.getSelectionModel().clearSelections();

        },

        modoEnviarAProducion: function() {
            this.itemsSeleccionados = false;
            this.scope.gridODProducciones.store.reload();
            this.deposito.getTopToolbar().get(3).show();
            this.modo = 'produccion';
            this.botonMoverPredeposito.setTooltip('Mover a Interdeposito').enable();
            this.botonMoverPredeposito.moviendo = 'AInterdeposito';
            this.panelAcciones('Enviar a Producción',2, true);
            this.deposito.hideTemporal();
        },

        /**
         * Establece el modo de despacho de mercaderia
         */
        modoDespachar: function() {
            this.itemsSeleccionados = false;
            this.scope.gridRemitos.store.reload();
            this.deposito.getTopToolbar().get(3).show();
            this.modo = 'despachar';
            this.deposito.deposito.filtros.quitar();
            this.botonMoverPredeposito.setTooltip('Mover a Predeposito').enable();
            this.botonMoverPredeposito.moviendo = 'APredeposito';
            this.botonAlmacenesDespachar.enable();
            var view = this.deposito.deposito;
            view.refresh();
            view.asignando = false;
            statusb = this.deposito.getBottomToolbar();
            statusb.updateSeleccion(' ');
            statusb.updateTotal(' ');
            this.panelAcciones('Despachar Mercadería',0, true);
            this.deposito.hideTemporal();
        },


        /**
         * Establece el modo de despacho de mercaderia
         */
        modoRecibir: function() {
            this.itemsSeleccionados = false;
            this.scope.gridRemitosEntrada.store.reload();
            if (this.deposito.modoVista == 2) {
                this.deposito.setModoVista(1);
            }

            this.modo = 'recibir';
            this.deposito.getTopToolbar().get(3).hide();
            this.deposito.deposito.filtros.quitar();

            this.botonMoverPredeposito.disable();
            this.botonMoverPredeposito.moviendo = false;

            this.botonAlmacenesDespachar.disable();
            var view = this.deposito.deposito;
            view.refresh();
            view.asignando = false;
            statusb = this.deposito.getBottomToolbar();
            statusb.updateSeleccion(' ');
            statusb.updateTotal(' ');
            this.panelAcciones('Recibir Mercadería',1, true);
            this.deposito.hideTemporal();
        }
    },

    createWindow: function() {

        var win = app.desktop.getWindow(this.id+'-win');
        if ( !win ) {
            this.deposito = new ERP.depositoPanel({
                scope: this,
                partidor: true,
                mover: true,
                onDropDeposito: function (source, e, data) {
                    if (this.scope.gridRemitosArticulosEntrada.id == source.grid.id) {
                        this.scope.paletizar(data);
                    }
                }
            });

            this.agregarBotones();

            this.gridRemitos        = Ext.ComponentMgr.create(<?=$this->gridRemitos?>);
            this.gridRemitosEntrada = Ext.ComponentMgr.create(<?=$this->gridRemitosEntrada?>);

            this.gridRemitosArticulos        = Ext.ComponentMgr.create(<?=$this->gridRemitosArticulos?>);
            this.gridRemitosArticulosEntrada = Ext.ComponentMgr.create(<?=$this->gridRemitosArticulosEntrada?>);

            this.gridODProducciones         = Ext.ComponentMgr.create(<?=$this->gridODProducciones?>);
            this.gridODProduccionesDetalles = Ext.ComponentMgr.create(<?=$this->gridODProduccionesDetalles?>);

            this.gridRemitosArticulosEntrada.onBeforeCreateColumns = function (columns) {
                columns.push({
                    xtype: 'actioncolumn',
                    header: '',
                    menuDisabled: true,
                    width: 45,
                    items: [
                        {
                            icon   : 'images/printer.png',                // Use a URL in the icon config
                            tooltip: 'Imprimir identificadores',
                            handler: function(grid, rowIndex, colIndex) {
                                var rec = grid.store.getAt(rowIndex);
                                if (rec.data.CantidadPaletizada > 0) {
                                    app.publish('/desktop/modules/js/commonApps/showUrl.js', {
                                    action: 'launch',
                                    url: '/Window/BirtReporter/report/template/Identificador_Palets_Entrantes_xRemitoDetalle/id/' + rec.data.Id,
                                    width: 900,
                                    height: 500,
                                    title: 'Mmi'
                                });
                                } else {
                                    app.publish('/desktop/showError','Debe paletizar primero.');
                                }
                            }
                        }
                    ]
                });
            };
            // Eventos Row select
            /*
            this.gridODProducciones.getSelectionModel().on('rowselect', function (i, rowIndex, r) {
                this.modoVentana.modoEnviarAProducion();
            }, this);*/

            // On Selection de Ordenes de Prod Detalles
            this.gridODProduccionesDetalles.getSelectionModel().on('rowselect', function (i, rowIndex, r) {
                var value = this.deposito.comboAlmacenes.getValue();
                this.itemsSeleccionados = true;
                if (value) {
                    var index = this.deposito.comboAlmacenes.getStore().findExact('Id', value);
                    var record = this.deposito.comboAlmacenes.getStore().getAt(index);

                    // existe registro y no es un interdeposito
                    // if (record && record.data.TipoDeAlmacen != 3) {
                        this.deposito.deposito.filtros.porOrdenDeProduccionDetalleTemporalYArticuloVersion(r.data.Id, r.data.ArticuloVersion);
                        this.deposito.deposito.refresh();
                        var bb = this.deposito.getBottomToolbar();
                        bb.updateSeleccion('Seleccionados: 0');
                    // } else {
                    //     // this.deposito.deposito.filtros.filtrarTodo();
                    // }

                }


                this.deposito.gridMmis.setPermanentFilter(1, 'ArticuloVersion', r.data.ArticuloVersion);
                this.deposito.gridMmis.getView().resaltarTemporalesProduccion(r.data.Id);
                this.deposito.gridMmis.store.load();
                //bb.updateTotal('de ' + (r.data.Cantidad - r.data.CantidadAsignada));
            }, this);

            // On select de Remitos Articulos de Salida
            this.gridRemitosArticulos.getSelectionModel().on('rowselect', function (i, rowIndex, r) {
                this.itemsSeleccionados = true;
                this.deposito.deposito.filtros.porArticuloYRemitoArticuloSalida(r.data.Articulo, r.data.Id);
                this.deposito.deposito.refresh();
                var bb = this.deposito.getBottomToolbar();
                bb.updateSeleccion('Seleccionados: 0');
                bb.updateTotal('de ' + (r.data.Cantidad - r.data.MmiCantAsociadaSalida));

                //Resaltamos Los asignados en la grilla
                this.deposito.gridMmis.getView().resaltarRemitoArticuloSalida(r.data.Id);

                // filtro la grilla de mmis
                this.deposito.gridMmis.setPermanentFilter(1, 'Articulo', r.data.Articulo);
                this.deposito.gridMmis.store.load();
            }, this);

            this.gridRemitosArticulosEntrada.getSelectionModel().on('rowselect', function (i, rowIndex, r) {
                this.deposito.deposito.filtros.porArticuloYRemitoArticulo(r.data.Articulo, r.data.Id);
                this.deposito.deposito.refresh();

                //Resaltamos Los asignados en la grilla
                this.deposito.gridMmis.getView().resaltarRemitoArticuloEntrada(r.data.Id);

                // filtro la grilla de mmis
                this.deposito.gridMmis.setPermanentFilter(1, 'Articulo', r.data.Articulo);
                this.deposito.gridMmis.store.load();
            }, this);

            win = this.create();
            win.on('hide', function() {
                this.deposito.deposito.tip.hide();
            },this)
        }
        win.show();
        this.modoVentana.getBotones(this);
    },

    paletizar: function(data) {
        this.datos = data.selections[0].data;

        // Creo la ventana de generacion de palets
        if (this.datos.Cantidad == this.datos.CantidadPaletizada) {
            window.app.desktop.showMsg({
                title: 'Atencion',
                msg: 'Este item ya esta completamente paletizado',
                modal: true,
                manager: window.app.desktop.getManager(),
                icon: Ext.Msg.WARNING,
                buttons: Ext.Msg.OK
            });
            return;
        }
        if (!this.paletizador) {

            var paletForm;

            paletForm = Ext.ComponentMgr.create({
                xtype: 'radform',

                bodyStyle:'padding:5px;',
                border: false,
                items:[
                    { xtype:'hidden', name:'Articulo', ref: '../Articulo'},
                    {
                        layout: 'column',
                        border: false,
                        defaults: { layout: 'form', border: false },
                        items: [
                            {
                                columnWidth: .50, items: [

                                    { xtype:'numberfield', fieldLabel:'Cant. a Paletizar', ref: '../../../cantidad',width:'90%', allowBlank: false, allowNegative:false,value:this.datos.Cantidad },
                                    { xtype:'numberfield', fieldLabel:'Cant. por Palet',ref: '../../../cantidadPorPalet', width:'90%', allowBlank: false, allowNegative:false },
                                    {
                                        xtype: 'xcombo',
                                        ref: '../../../TipoPalet',
                                        //width: '80%',
                                        displayField: 'Descripcion',
                                        autocomplete: true,
                                        selectOnFocus: true,
                                        forceSelection: true,
                                        forceReload: true,
                                        hiddenName: 'TipoPalet',
                                        loadingText: 'Cargando...',
                                        lazyRender: false,
                                        store: new Ext.data.JsonStore ({
                                            id: 0,
                                            url: 'datagateway\/combolist\/model\/TiposDePalets\/m\/Almacenes',
                                            storeId: 'MmiTipoStore'
                                        }),
                                        typeAhead: true,
                                        valueField: 'Id',
                                        autoLoad: true,
                                        allowBlank: false,
                                        allowNegative: false,
                                        fieldLabel: 'Tipo Palet',
                                        name: 'Tipo_Palet'
                                    }

                                    ,{
                                        xtype: 'xcombo',
                                        ref: '../../../ArticuloVersion',
                                        //width: '80%',
                                        displayField: 'Descripcion',
                                        autocomplete: true,
                                        selectOnFocus: true,
                                        forceSelection: true,
                                        forceReload: true,
                                        hiddenName: 'ArticuloVersion',
                                        loadingText: 'Cargando...',
                                        lazyRender: true,
                                        store: new Ext.data.JsonStore ({
                                            id: 0,
                                            url: 'datagateway\/combolist\/model\/ArticulosVersiones\/m\/Base\/fqfield\/Articulo',
                                            storeId: 'ArticuloVersionStore'
                                        }),
                                        typeAhead: true,
                                        valueField: 'Id',
                                        filterFrom: {
                                            Articulo: 'Articulo'
                                        },
                                        mustFilter: true,
                                        autoLoad: false,
                                        allowBlank: false,
                                        allowNegative: false,
                                        fieldLabel: 'Version Articulo',
                                        name: 'ArticuloVersion'
                                    }

                                ]
                            },{
                                columnWidth: .50, items: [ {
                                    xtype:'fieldset',
                                    title: 'Lote',
                                    autoHeight:true,
                                    items: [

                                            { xtype:'textfield',  fieldLabel:'Numero', ref: '../../../../Lote', width:'90%', allowBlank: true },
                                            { xtype:'xdatefield', fieldLabel:'Elaboracion', ref: '../../../../Elaboracion', width:'90%',dateFormat: 'Y-m-d H:i:s', allowBlank: true },
                                            { xtype:'xdatefield', fieldLabel:'Vencimiento', ref: '../../../../Vencimiento', width:'90%',dateFormat: 'Y-m-d H:i:s', allowBlank: true }
                                         ]
                                    }
                                ]
                            }
                        ]
                    }]
            });


            this.paletizador = new app.desktop.createWindow({
                layout: 'fit',
                defaults: {border:false},
                width: 670,
                height: 280,
                border: false,
                closeAction: 'hide',
                // bodyStyle: 'padding: 5px;',
                //plain: true,
                modal: true,
                title: 'Crear Mmi',
                autoDestroy: true,
                items: [
                    paletForm
                ],
                buttons: [{
                        text: 'Crear',
                        scope: this,
                        handler: function() {
                            if (!this.paletizador.items.items[0].form.isValid())
                                return app.publish('/desktop/showError', 'No se completaron todos los campos requeridos');

                            var almCmp = this.deposito.comboAlmacenes;

                            var LoteCmp        = this.paletizador.Lote;
                            var ElaboracionCmp = this.paletizador.Elaboracion;
                            var VencimientoCmp = this.paletizador.Vencimiento;

                            var cantidadCmp  = this.paletizador.cantidad;
                            var cantidadPCmp = this.paletizador.cantidadPorPalet;
                            var tipoCmp      = this.paletizador.TipoPalet;

                            this.datos.cantidadMaxima     = cantidadPCmp.getValue();
                            this.datos.cantidadAPaletizar = cantidadCmp.getValue();
                            this.datos.TipoPalet          = tipoCmp.getValue();
                            this.datos.Almacen            = almCmp.getValue();

                            this.datos.Lote            = LoteCmp.getValue();
                            this.datos.Elaboracion     = ElaboracionCmp.getValue();
                            this.datos.Vencimiento     = VencimientoCmp.getValue();
                            // this.datos.ArticuloVersion = null;
                            this.datos.ArticuloVersion = this.paletizador.ArticuloVersion.getValue();

                            Models.Almacenes_Model_AlmacenesMapper.paletizarRemitoArticulo(
                                this.datos.Id,
                                this.datos.Almacen,
                                this.datos.cantidadAPaletizar,
                                this.datos.cantidadMaxima,
                                this.datos.TipoPalet,
                                this.datos.Lote,
                                this.datos.Vencimiento,
                                this.datos.Elaboracion,
                                this.datos.ArticuloVersion,
                                function (result, e){
                                    if (e.status) {
                                        this.deposito.deposito.store.reload();
                                        this.gridRemitosArticulosEntrada.store.reload();
                                        this.paletizador.hide();
                                    }
                                },
                                this
                            );
                        }
                    },{
                        text: 'Cancelar',
                        handler: function(){
                            this.paletizador.hide();
                        },
                        scope: this
                    }]
            });
        }

        this.paletizador.Articulo.setValue(this.datos.Articulo);

        //this.paletizador.ArticuloVersion.setValue(1);

        //por defecto la cantidad es el total del palet, si tiene algo paletizado se lo desuento
        this.paletizador.cantidad.setValue(this.datos.Cantidad-this.datos.CantidadPaletizada);

        // Mostramos la ventana
        this.paletizador.show();
    },

    agregarBotones: function(){

        this.deposito.getTopToolbar().add([
            {
                xtype: 'buttongroup',
                title: 'Acción',
                columns: 4,
                defaults: {
                    scale: 'large'
                },
                items: [{
                        iconCls:  'x-btn-text-icon',
                        icon: 'images/palets/mmi32agregar.png',
                        scope: this,
                        toggleGroup: 'accionesgroup',
                        enableToggle: true,
                        tooltip: 'Recibir (Paletizar)' ,
                        toggleHandler: function(b,s) {
                            if (s == false) {
                                this.modoVentana.modoNinguno();
                                return;
                            }
                            this.modoVentana.modoRecibir();
                        }
                    },{
                        iconCls:  'x-btn-text-icon',
                        icon: 'images/32/asignarpalet.png',
                        scope: this,
                        enableToggle: true,
                        toggleGroup: 'accionesgroup',
                        tooltip: 'Despachar' ,
                        toggleHandler: function(b,s) {
                            if (s == false) {
                                this.modoVentana.modoNinguno();
                                return;
                            }
                            this.modoVentana.modoDespachar();
                        }
                    },{
                        iconCls:  'x-btn-text-icon',
                        icon: 'images/32/movermmi.png',
                        toggleGroup: 'accionesgroup',
                        enableToggle: true,
                        scope: this,
                        tooltip: 'Producción' ,
                        toggleHandler: function(b,s) {
                            if (s == false) {
                                this.modoVentana.modoNinguno();
                                return;
                            }
                            this.modoVentana.modoEnviarAProducion();
                        }
                }]

            },
            {
                xtype: 'buttongroup',
                title: 'Despachar',
                hidden: true,
                columns: 4,
                defaults: {
                    scale: 'large'
                },
                items: [
                    {
                        iconCls:  'x-btn-text-icon',
                        id:       'botonAlmacenesAsignarMmis',
                        icon:     'images/32/asignarpalet.png',
                        disabled: true,
                        scope: this,
                        tooltip: 'Asignar',
                        handler: function() {
                            var selected;
                            var recordsIdJson = [];
                            var dvView = this.deposito.deposito;

                            // segun este en modo grafico o grilla opero de manera diferente
                            if (this.deposito.modoVista == 1) {

                                selected = dvView.getSelectedRecords();
                                for (var x = 0; x < selected.length ; x++) {
                                    recordsIdJson[x] = selected[x].data.Mmi.Id;
                                }
                            } else {
                                selected = this.deposito.gridMmis.getSelectionModel().getSelections();
                                for (var x = 0; x < selected.length ; x++) {
                                    recordsIdJson[x] = selected[x].data.Id;
                                }
                            }

                            var gridMmis = this.deposito.gridMmis;

                            switch (this.modoVentana.modo) {
                                // ASIGNANDO MMIS A REMITOS
                                case 'despachar':
                                    var raGrid = this.gridRemitosArticulos;
                                    var row = raGrid.selModel.getSelections();

                                    Models.Almacenes_Model_AlmacenesMapper.asignarARemito(
                                        recordsIdJson,
                                        row[0].get('Id'),
                                        function (result, e) {
                                            if (e.status) {
                                                if (this.deposito.modoVista == 1) dvView.store.reload();
                                                raGrid.store.reload();
                                                gridMmis.store.reload();
                                            }
                                        },
                                        this
                                    );
                                    break;

                                    // ASIGNANDO MMIS A ORDENES DE PRODUCCION (DETALLE)
                                case 'produccion':
                                    var odp = this.gridODProducciones.selModel.getSelections();
                                    var odpDetalle = this.gridODProduccionesDetalles.selModel.getSelections();
                                    if (!odpDetalle) {
                                        Ext.Msg.alert('Atencion', 'Debe seleccionar un articulo de la Orden de Produccion');
                                        return;
                                    }

                                    Models.Produccion_Model_OrdenesDeProduccionesMapper.asignarOrdenDeProduccionDetalleMmi_Temporal(
                                        odp[0].get('Id'),
                                        odpDetalle[0].get('Id'),
                                        recordsIdJson,
                                        function (result, e) {
                                            if (e.status) {
                                                if (this.deposito.modoVista == 1) dvView.store.reload();
                                                this.gridODProduccionesDetalles.store.reload();
                                                gridMmis.store.reload();
                                            }
                                        },
                                        this
                                    );
                                    break;
                            }
                        }
                    },{
                        iconCls:  'x-btn-text-icon',
                        id:       'botonAlmacenesDesasignarMmis',
                        icon:     'images/32/desasignarpalet.png',
                        disabled: true,
                        scope: this,
                        tooltip: 'Desasignar',
                        handler: function() {
                            var selected;
                            var recordsIdJson = [];
                            var dvView = this.deposito.deposito;

                            // segun este en modo grafico o grilla opero de manera diferente
                            if (this.deposito.modoVista == 1) {

                                selected = dvView.getSelectedRecords();
                                for (var x = 0; x < selected.length ; x++) {
                                    recordsIdJson[x] = selected[x].data.Mmi.Id;
                                }
                            } else {
                                selected = this.deposito.gridMmis.getSelectionModel().getSelections();
                                for (var x = 0; x < selected.length ; x++) {
                                    recordsIdJson[x] = selected[x].data.Id;
                                }
                            }

                            var gridMmis = this.deposito.gridMmis;

                            switch (this.modoVentana.modo) {
                                // DESASIGNANDO MMIS A REMITOS
                                case 'despachar':
                                    var raGrid = this.gridRemitosArticulos;
                                    var row = raGrid.selModel.getSelections();


                                    Models.Almacenes_Model_AlmacenesMapper.desasignarARemito(
                                        recordsIdJson,
                                        function (result, e){
                                            if (e.status) {
                                                if (this.deposito.modoVista == 1) dvView.store.reload();
                                                raGrid.store.reload();
                                                gridMmis.store.reload();
                                            }
                                        },
                                        this
                                    );
                                    break;

                                // DESASIGNANDO MMIS A ORDENES DE PRODUCCION (DETALLE)
                                case 'produccion':
                                    var odp = this.gridODProducciones.selModel.getSelections();
                                    var odpDetalle = this.gridODProduccionesDetalles.selModel.getSelections();
                                    if (!odpDetalle) {
                                        Ext.Msg.alert('Atencion', 'Debe seleccionar un articulo de la Orden de Produccion');
                                        return;
                                    }

                                    Models.Produccion_Model_OrdenesDeProduccionesMapper.desasignarOrdenDeProduccionDetalleMmi_Temporal(
                                        odp[0].get('Id'),
                                        odpDetalle[0].get('Id'),
                                        recordsIdJson,
                                        function (result, e) {
                                            if (e.status) {
                                                if (this.deposito.modoVista == 1) dvView.store.reload();
                                                this.gridODProduccionesDetalles.store.reload();
                                                gridMmis.store.reload();
                                            }
                                        },
                                        this
                                    );
                                    break;
                            }
                        }
                    },{
                        iconCls:  'x-btn-text-icon',
                        id:       'botonAlmacenesMoverAPredeposito',
                        icon:     'images/32/movermmi.png',
                        disabled:  true,
                        //text: 'Mover a Predeposito',
                        tooltip : 'Mover a Predepósito',
                        scope: this,
                        handler : function() {

                            switch (Ext.getCmp('botonAlmacenesMoverAPredeposito').moviendo) {
                                case 'APredeposito':
                                    var value = this.deposito.comboAlmacenes.getValue();
                                    if (value) {
                                        var index = this.deposito.comboAlmacenes.getStore().findExact('Id', value);
                                        var record = this.deposito.comboAlmacenes.getStore().getAt(index);
                                        if (record.data.TipoDeAlmacen != 2) {
                                            app.publish('/desktop/showError', 'Debe estar situado en un predepósito');
                                            return;
                                        }
                                    } else {
                                        app.publish('/desktop/showError', 'Debe estar situado en un predepósito');
                                        return;
                                    }
                                    var dvView = this.deposito.deposito;
                                    window.app.desktop.showMsg({
                                        title: 'Alerta',
                                        msg: '¿Desea mover todos los Mmis asignados a este remito al predepósito?',
                                        renderTo: 'x-desktop',
                                        fn: function (Estado) {
                                            if (Estado == 'yes') {
                                                sel = this.gridRemitos.getSelectionModel().getSelected();
                                                if (!sel) return;

                                                Models.Almacenes_Model_AlmacenesMapper.moverMmisAPredeposito(
                                                    sel.data.Id,
                                                    value,
                                                    function (result, e) {
                                                        if (e.status)
                                                            dvView.store.reload();
                                                    },
                                                    this
                                                );
                                            }
                                        },
                                        scope: this,
                                        modal: true,
                                        icon: Ext.Msg.ALERT,
                                        buttons: Ext.Msg.YESNO
                                    });
                                    break;

                                case 'AInterdeposito':
                                    var dvView = this.deposito.deposito;
                                    window.app.desktop.showMsg({
                                        title: 'Alerta',
                                        msg: '¿Desea mover todos los Mmis asignados a esta orden al interdeposito?',
                                        renderTo: 'x-desktop',
                                        fn: function (Estado) {
                                            if (Estado == 'yes') {
                                                sel = this.gridODProducciones.getSelectionModel().getSelected();
                                                if (!sel) return;
                                                Models.Produccion_Model_OrdenesDeProduccionesMapper.moverOrdenDeProduccionAInterdeposito(
                                                    sel.data.Id,
                                                    function (result, e) {
                                                        if (e.status) {
                                                            dvView.store.reload();
                                                        }
                                                    },
                                                    this
                                                );
                                            }
                                        },
                                        scope: this,
                                        modal: true,
                                        icon: Ext.Msg.ALERT,
                                        buttons: Ext.Msg.YESNO
                                    });
                                    break;
                            }
                        }
                    },{
                        iconCls:  'x-btn-text-icon',
                        id:       'botonAlmacenesDespachar',
                        icon:     'images/32/despachar.png',
                        disabled:  true,
                        //tooltip : 'Despachar Remito',
                        tooltip : 'Despachar',
                        scope: this,
                        handler : function() {
                            var remitosGrid = this.gridRemitos;
                            var dvView = this.deposito.deposito;
                            window.app.desktop.showMsg({
                                title: 'Alerta',
                                msg: 'Desea despachar este remito?',
                                renderTo: 'x-desktop',
                                fn: function (Estado) {
                                    if (Estado == 'yes') {
                                        sel = remitosGrid.getSelectionModel().getSelected();
                                        if (!sel) return;
                                        Rad.callRemoteJsonAction ({
                                            params: {
                                                'id':sel.data.Id,
                                            },
                                            url: '/Almacenes/Almacenes/despacharremito',
                                            scope: this,
                                            success: function(response) {
                                                dvView.store.reload();
                                                remitosGrid.store.reload();
                                            }
                                        });
                                    }
                                },
                                scope: this,
                                modal: true,
                                icon: Ext.Msg.ALERT,
                                buttons: Ext.Msg.YESNO
                            });


                        }
                    }
                ]
            },{
                xtype: 'buttongroup',
                title: 'Utiles',
                columns: 4,
                defaults: {
                    scale: 'large'
                },
                items: [
                    {
                        iconCls:  'x-btn-text-icon',
                        icon: 'images/32/print.png',
                        tooltip: 'Imprimir Movimientos',
                        scope: this,
                        handler: function() {
                            this.publish('/desktop/modules/Window/list/index/m/Almacenes/model/MmisMovimientos/fetch/MmisMovimientosDelDia/section/impresion', { action: 'launch' });
                            //alert("tranquilo esta en proceso");
                        },
                    },{
                        iconCls:  'x-btn-text-icon',
                        icon: 'images/32/alacarte.png',
                        tooltip: 'Reporte Stock',
                        scope: this,
                        handler: function() {
                            this.publish('/desktop/modules/Almacenes/ReporteDeStock',{ action: 'launch' });
                        }
                    },
                    {
                        iconCls: 'x-btn-text-icon',
                        icon:    'images/32/tools.png',
                        menu: [{
                                iconCls:  'x-btn-text-icon',
                                icon: 'images/shape_square_add.png',
                                disabled: true,
                                scope: this,
                                ref: '../../../buttonCambiarCantidad',
                                text: 'Cantidad' ,
                                handler: function() {
                                    nodos = this.deposito.deposito.getSelectedNodes();
                                    selItem = this.deposito.deposito.store.getAt(nodos[0].viewIndex);

                                    Ext.Msg.prompt('Modificar Palet '+selItem.data.Mmi.Identificador, '<br>Este palet contiene: <i>'+selItem.data.Mmi.CantidadActual+' de '+selItem.data.A.Descripcion+'</i><br><br><b>Cantidad Actual a asignar:</b>', function(btn, cantidad){
                                        if (btn == 'ok') {
                                            this.deposito.deposito.cambiarCantidadPalet(selItem.data.Mmi.Id, cantidad);
                                        }
                                    }, this);
                                }
                            },{
                                iconCls:  'x-btn-text-icon',
                                icon:     'images/delete.png',
                                disabled:  true,
                                scope: this,
                                ref: '../../../buttonBorrarPalet',
                                text: 'Borrar',
                                tooltip : 'Borrar Mmi',
                                handler : function() {

                                    nodos = this.deposito.deposito.getSelectedNodes();
                                    selItem = this.deposito.deposito.store.getAt(nodos[0].viewIndex);

                                    Ext.Msg.confirm('Eliminar Palet '+selItem.data.Mmi.Identificador, '<br>Este palet contiene: <i>'+selItem.data.Mmi.CantidadActual+' de '+selItem.data.A.Descripcion+'</i><br><br><b><div style="text-align:center">¿Esta seguro que desea eliminar este Palet?</div>', function(btn) {
                                        if (btn == 'yes') {
                                            this.deposito.deposito.eliminarPalet(selItem.data.Mmi.Id);
                                        }
                                    }, this);
                                }
                            },{
                                iconCls:  'x-btn-text-icon',
                                icon: 'images/shape_ungroup.png',
                                disabled: true,
                                scope: this,
                                ref: '../../../buttonPartirPalet',
                                text: 'Partir',
                                handler: function() {
                                    nodos = this.deposito.deposito.getSelectedNodes();
                                    selItem = this.deposito.deposito.store.getAt(nodos[0].viewIndex);

                                    Ext.Msg.prompt('Partir Palet '+selItem.data.Mmi.Identificador, '<br>Este palet contiene: <i>'+selItem.data.Mmi.CantidadActual+' de '+selItem.data.A.Descripcion+'</i><br><br><b>Cantidad a Separar:</b>', function(btn, cantidad){
                                        if (btn == 'ok') {
                                            this.deposito.deposito.partirPalet(selItem.data.Mmi.Id, cantidad);
                                        }
                                    }, this);
                                }
                            },{
                                iconCls:  'x-btn-text-icon',
                                icon: 'images/shape_move_back.png',
                                disabled: true,
                                scope: this,
                                ref: '../../../buttonCambiarArticulo',
                                text: 'Articulo',
                                handler: function() {
                                    nodos = this.deposito.deposito.getSelectedNodes();
                                    selItem = this.deposito.deposito.store.getAt(nodos[0].viewIndex);
                                    // creo la ventana para elegir el articulo que va a tomar el mmi
                                    var artForm;
                                    var comboarticuloversion = Ext.create({
                                        xtype: 'xcombo',
                                        width: 520,
                                        displayField: 'Descripcion',
                                        autocomplete: true,
                                        selectOnFocus: true,
                                        forceSelection: true,
                                        forceReload: true,
                                        hiddenName: 'ArticuloVersion',
                                        loadingText: 'Cargando...',
                                        lazyRender: true,
                                        store: new Ext.data.JsonStore ({
                                            id: 0,
                                            storeId: 'ArticuloVersionCombo',
                                            root: 'rows',
                                            totalProperty: 'count',
                                            url: '/Almacenes/Almacenes/filtrosubarticulo',
                                            baseParams: {
                                                'id':selItem.data.Mmi.Id
                                            },
                                            fields:['Id','Descripcion'],
                                        }),
                                        typeAhead: true,
                                        valueField: 'Id',
                                        mustFilter: true,
                                        autoLoad: false,
                                        allowBlank: false,
                                        allowNegative: false,
                                        fieldLabel: 'SubArticulo',
                                        name: 'ArticuloVersion'
                                    });

                                    artForm = Ext.ComponentMgr.create({
                                        xtype: 'radform',

                                        bodyStyle:'padding:5px;',
                                        border: false,
                                        items:[
                                            { xtype:'hidden', name:'Articulo', ref: '../Articulo'},
                                            {
                                                layout: 'form',
                                                border: false,
                                                items: [comboarticuloversion]
                                            }]
                                    });

                                    this.artMmi = new app.desktop.createWindow({
                                        layout: 'fit',
                                        defaults: {border:false},
                                        width: 670,
                                        height: 120,
                                        border: false,
                                        closeAction: 'hide',
                                        // bodyStyle: 'padding: 5px;',
                                        //plain: true,
                                        modal: true,
                                        title: 'Cambiar Articulo a Mmi',
                                        autoDestroy: true,
                                        items: [
                                            artForm
                                        ],
                                        buttons: [{
                                            text: 'Guardar',
                                            scope: this,
                                            handler: function() {
                                                var articuloversion = comboarticuloversion.getValue();
                                                this.deposito.deposito.cambiarArticuloPalet(selItem.data.Mmi.Id, articuloversion);
                                                this.artMmi.hide();
                                            }
                                        },{
                                            text: 'Cancelar',
                                            handler: function(){
                                                this.artMmi.hide();
                                            },
                                            scope: this
                                        }]
                                    });

                                    // Mostramos la ventana
                                    this.artMmi.show();
                                }
                            }
                        ]
                    }

                ]
            }
    ]);

    this.deposito.deposito.on('selectionchange',  function(i, selections) {

        if (selections.length > 0 ) {
            selItem = this.deposito.deposito.store.getAt(selections[0].viewIndex);

            // Esta ocupado ?
            if (selItem.data.Mmi == "" || (selItem.data.Mmi && selItem.data.Mmi.Id > 0)) {
                if (!selItem.data.Mmi.RemitoArticuloSalida && selections.length == 1) {
                    this.deposito.topToolbar.buttonPartirPalet.enable();
                    this.deposito.topToolbar.buttonCambiarCantidad.enable();
                    this.deposito.topToolbar.buttonBorrarPalet.enable();
                    this.deposito.topToolbar.buttonCambiarArticulo.enable();
                } else {
                    this.deposito.topToolbar.buttonPartirPalet.disable();
                    this.deposito.topToolbar.buttonCambiarCantidad.disable();
                    this.deposito.topToolbar.buttonBorrarPalet.disable();
                    this.deposito.topToolbar.buttonCambiarArticulo.disable()
                }
            } else {
                this.deposito.topToolbar.buttonPartirPalet.disable();
                this.deposito.topToolbar.buttonCambiarCantidad.disable();
                this.deposito.topToolbar.buttonCambiarArticulo.disable();
                this.deposito.topToolbar.buttonBorrarPalet.disable();
            }
        } else {
            this.deposito.topToolbar.buttonPartirPalet.disable();
            this.deposito.topToolbar.buttonCambiarCantidad.disable();
            this.deposito.topToolbar.buttonCambiarArticulo.disable();
            this.deposito.topToolbar.buttonBorrarPalet.disable();
        }

        if (this.itemsSeleccionados == false) return;

        var modo = this.modoVentana.modo;
        var botonAsignar = Ext.getCmp('botonAlmacenesAsignarMmis');
        var botonDesasignar = Ext.getCmp('botonAlmacenesDesasignarMmis');

        if (selections.length > 0) {

            // Esta ocupado ?
            if (selItem.data.Mmi == "" || (selItem.data.Mmi && selItem.data.Mmi.Id > 0)) {
                if (modo) {
                    if ((modo == 'despachar' && !selItem.data.Mmi.RemitoArticuloSalida) ||
                      (modo == 'produccion' && !selItem.data.AsignadoODPDetalleTemporal)) {
                        botonAsignar.enable();
                        botonDesasignar.disable();
                    } else {
                        botonAsignar.disable();
                        botonDesasignar.enable();
                    }
                } else {
                    botonAsignar.disable();
                    botonDesasignar.disable();
                }
            } else {
                botonAsignar.disable();
                botonDesasignar.enable();
            }
        } else {
            botonAsignar.disable();
            botonDesasignar.disable();
        }
        // Si se esta asignando mmis a remitos mostramos el total de seleccionados
        if (selections.length > 0 && modo == 'despachar') {
            statusb =this.deposito.getBottomToolbar();
            if (statusb)
                statusb.updateSeleccion('Seleccionados: '+this.sumarSeleccionados(selections));
        }
    }, this);

    this.deposito.gridMmis.getSelectionModel().on('selectionchange', function (rs) {
        var modo = this.modoVentana.modo;
        var botonAsignar = Ext.getCmp('botonAlmacenesAsignarMmis');
        var botonDesasignar = Ext.getCmp('botonAlmacenesDesasignarMmis');

        if (rs.getCount() == 1) {
            this.deposito.topToolbar.buttonPartirPalet.enable();
            this.deposito.topToolbar.buttonCambiarCantidad.enable();
            this.deposito.topToolbar.buttonCambiarArticulo.enable();
            this.deposito.topToolbar.buttonBorrarPalet.enable();

        } else {
            this.deposito.topToolbar.buttonPartirPalet.disable();
            this.deposito.topToolbar.buttonCambiarCantidad.disable();
            this.deposito.topToolbar.buttonCambiarArticulo.disable();
            this.deposito.topToolbar.buttonBorrarPalet.disable();
        }

        if (this.itemsSeleccionados == false) return;

        if (rs.getCount() > 0) {
            if (modo) {
                var s = rs.getSelections();
                var puedeDespachar = true;
                var puedeDesasignar = true;

                if (modo == 'despachar') {

                    for (var i = 0; i < s.length; i++) {
                        element = s[i];
                        if (element.data.RemitoArticuloSalida != '') {
                            puedeDespachar  = false;
                        } else {
                            puedeDesasignar = false;
                        }
                    }

                } else if (modo == 'produccion') {
                    for (var i = 0; i < s.length; i++) {
                        element = s[i];
                        if (element.data.AsignadoODPDetalleTemporal != '') {
                            puedeDespachar  = false;
                        } else {
                            puedeDesasignar = false;
                        }
                    }

                }

                if (puedeDespachar) {
                    botonAsignar.enable();
                } else {
                    botonAsignar.disable();
                }
                if (puedeDesasignar) {
                    botonDesasignar.enable();
                } else {
                    botonDesasignar.disable();
                }
            }

        }
    },this);
},

create: function() {
    defaultWinCfg = {
        id: this.id+'-win',
        width: 1000,
        height: 500,
        maximized: true,
        title: this.title,
        iconCls: 'icon-grid',
        border:  false,
        bodyStyle: 'background: #fff',
        shim: false,
        animCollapse: false,
        layout: 'fit',
        items: [
            {
                xtype : 'panel',
                border:  false,
                layout : "border",
                items : [
                    {
                        region : "center",
                        layout: 'fit',
                        margins: '3 3 3 3',
                        border:  false,
                        items : [
                            this.deposito
                        ]
                    },
                    {
                        region : "south",
                        margins: '3 3 3 3',
                        layout: 'fit',
                        height : 270,
                        border:  false,
                        title:'',
                        //split : true,
                        id:'almacenes_panel_acciones',
                        margins: '3 3 3 3',
                        hidden: true,
                        animCollapse: false,
                        items : [
                            {
                                xtype: 'panel',
                                layout:'card',
                                deferredRender: false,
                                border:  true,
                                activeItem : 0,

                                items:[
                                    {
                                        layout:	'card',
                                        border: false,
                                        defaults: { border: false },
                                        activeItem:0,
                                        title: 'Despachar Mercadería',
                                        items:	[
                                            {
                                                layout:	'fit',
                                                items:	this.gridRemitos,
                                                buttons: [{
                                                    icon: 'images/arrow_right.png',
                                                    text:'Siguiente',
                                                    handler: function(b,e) {
                                                        if (this.gridRemitos.getSelectionModel().getCount() != 1) {
                                                            app.publish('/desktop/showWarning', 'Seleccione primero un remito');
                                                            return;
                                                        }
                                                        b.ownerCt.ownerCt.ownerCt.layout.setActiveItem(1);
                                                    },
                                                    scope:this
                                                }]
                                            },{
                                                layout:	'fit',
                                                items:	this.gridRemitosArticulos,
                                                buttons: [{
                                                    icon: 'images/arrow_left.png',
                                                    text:'Atras',
                                                    handler: function(b,e) {
                                                        var botonAsignar = Ext.getCmp('botonAlmacenesAsignarMmis');
                                                        var botonDesasignar = Ext.getCmp('botonAlmacenesDesasignarMmis');
                                                        botonAsignar.disable();
                                                        botonDesasignar.disable();
                                                        this.gridRemitosArticulos.getSelectionModel().clearSelections();
                                                        b.ownerCt.ownerCt.ownerCt.layout.setActiveItem(0);
                                                        this.deposito.deposito.filtros.quitar();
                                                        //Resaltamos Los asignados en la grilla
                                                        this.deposito.gridMmis.getView().resaltarNada();
                                                        this.itemsSeleccionados = false;
                                                    },
                                                    scope:this
                                                }]
                                            }
                                        ]
                                    },{
                                        layout:	'card',
                                        activeItem:0,
                                        border: false,
                                        defaults: { border: false },
                                        title: 'Recibir Mercadería',
                                        items:	[
                                            {
                                                layout:	'fit',
                                                items:	this.gridRemitosEntrada,
                                                buttons: [{
                                                    icon: 'images/arrow_right.png',
                                                    text:'Siguiente',
                                                    handler: function(b,e) {
                                                        if (this.gridRemitosEntrada.getSelectionModel().getCount() != 1) {
                                                            app.publish('/desktop/showWarning', 'Seleccione primero un remito');
                                                            return;
                                                        }
                                                        b.ownerCt.ownerCt.ownerCt.layout.setActiveItem(1);
                                                    },
                                                    scope:this
                                                }]
                                            },{
                                                layout:	'fit',
                                                items:	this.gridRemitosArticulosEntrada,
                                                buttons: [{
                                                    icon: 'images/arrow_left.png',
                                                    text:'Atras',
                                                    handler: function(b,e) {
                                                        var botonAsignar = Ext.getCmp('botonAlmacenesAsignarMmis');
                                                        var botonDesasignar = Ext.getCmp('botonAlmacenesDesasignarMmis');
                                                        botonAsignar.disable();
                                                        botonDesasignar.disable();
                                                        this.gridRemitosArticulosEntrada.getSelectionModel().clearSelections();
                                                        b.ownerCt.ownerCt.ownerCt.layout.setActiveItem(0);
                                                        this.deposito.deposito.filtros.quitar();
                                                        this.itemsSeleccionados = false;
                                                    },
                                                    scope:this
                                                }]
                                            }
                                        ]
                                    },{
                                        layout:	'card',
                                        border: false,
                                        defaults: { border: false },
                                        activeItem:0,
                                        title: 'Despachar Mercadería a Producción',
                                        items:	[
                                            {
                                                layout:	'fit',
                                                items:	this.gridODProducciones,
                                                buttons: [{
                                                    icon: 'images/arrow_right.png',
                                                    text:'Siguiente',
                                                    handler: function(b,e) {
                                                        if (this.gridODProducciones.getSelectionModel().getCount() != 1) {
                                                            app.publish('/desktop/showWarning', 'Seleccione primero un remito');
                                                            return;
                                                        }
                                                        b.ownerCt.ownerCt.ownerCt.layout.setActiveItem(1);
                                                    },
                                                    scope:this
                                                }]
                                            },{
                                                layout:	'fit',
                                                items:	this.gridODProduccionesDetalles,
                                                buttons: [{
                                                    icon: 'images/arrow_left.png',
                                                    text:'Atras',
                                                    handler: function(b,e) {
                                                        var botonAsignar = Ext.getCmp('botonAlmacenesAsignarMmis');
                                                        var botonDesasignar = Ext.getCmp('botonAlmacenesDesasignarMmis');
                                                        botonAsignar.disable();
                                                        botonDesasignar.disable();
                                                        this.gridODProduccionesDetalles.getSelectionModel().clearSelections();
                                                        b.ownerCt.ownerCt.ownerCt.layout.setActiveItem(0);
                                                        this.deposito.deposito.filtros.quitar();
                                                        this.itemsSeleccionados = false;
                                                    },
                                                    scope:this
                                                }]
                                            }
                                        ]
                                    }]
                            }
                        ]
                    }
                ]
            }
        ]
    };
    return app.desktop.createWindow(defaultWinCfg);
}
});

new Apps.<?=$this->name?>();
