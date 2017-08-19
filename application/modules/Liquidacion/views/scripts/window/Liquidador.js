Ext.ns( 'Apps' );

Apps.<?=$this->name?> = Ext.extend(RadDesktop.Module, {
    autoStart: true,
    title: '<?=$this->title?>',
    appChannel: '/desktop/modules<?=$this->url()?>',
    requires: [
        '/direct/Liquidacion/Liquidar?javascript',
        '/direct/Liquidacion/LiquidacionesRecibos?javascript',
        '/direct/Jobs?javascript'
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
            this.grid =  Ext.ComponentMgr.create(<?=$this->grid?>);
            this.grid.getSelectionModel().on('rowselect', function( t, rowIndex, r){
                this.gridPersonasGanancias.setPermanentFilter(1, 'Liquidacion', r.data.Id);
            },this);
            win = this.create();
        }
        win.show();
    },

    create: function() {
        this.createSecGrids();
        defaultWinCfg = {
            id: this.id+'-win',
            title: this.title,
            maximized: true,
            iconCls: 'icon-grid',
            border:  false,
            shim: false,
            animCollapse: false,
            layout: 'fit',
            width: 1050,
            height:500,
            items: [
                this.renderWindowContent()
            ]
        };
        return app.desktop.createWindow(defaultWinCfg);
    },

    filtrarCeros: function() {
        this.gridRecibosDetalles.store.filterBy(function(r,id){
            return r.data.Monto != 0;
        });
    },

    createSecGrids: function () {
        this.btnOcultarCeros = new Ext.Button({
            icon: 'images/table_row_insert.png',
            text: 'Ocultar 0',
            tooltip: 'Ocultar conceptos con valor 0',
            enableToggle: true,
            toggleHandler: function(b, s) {
                if (s) {
                    this.filtrarCeros();
                } else {
                    this.gridRecibosDetalles.store.clearFilter();
                }
            },
            scope: this
        });

        this.gridRecibos              = Ext.ComponentMgr.create(<?php echo $this->gridRecibos ?>);
        this.gridRecibosDetalles      = Ext.ComponentMgr.create(<?php echo $this->gridRecibosDetalles ?>);
        this.gridVariablesCalculadas  = Ext.ComponentMgr.create(<?php echo $this->gridVariablesCalculadas ?>);
        this.gridPersonasGanancias    = Ext.ComponentMgr.create(<?php echo $this->gridPersonasGanancias ?>);
        this.gridCabeceraRecibos      = Ext.ComponentMgr.create(<?php echo $this->gridCabeceraRecibos ?>);

        // Agrego el boton  btnOcultarCeros
        this.gridRecibos.getBottomToolbar().addButton({xtype:'tbseparator'});
        this.gridRecibos.getBottomToolbar().addButton(this.btnOcultarCeros);

        // Al seleccionar un recibo
        this.gridRecibos.getSelectionModel().on('rowselect', function( t, rowIndex, r){
            var x = [];
            for (var i=r.data.PrimerAjuste;i<r.data.CantidadAjustes+r.data.PrimerAjuste;i++) {
              x.push(i);
            }
            this.comboAjustes.store.loadData(x);
            if (this.gridComp1.store.getCount() > 0) {
                this.gridComp1.store.loadData({rows:[],count:0},false);
                this.gridComp2.store.loadData({rows:[],count:0},false);
            }
        }, this);

        // Al cargar recibos
        this.gridRecibos.store.on('load', function(){
            if (this.gridComp1.store.getCount() > 0) {
                this.gridComp1.store.loadData({rows:[],count:0},false);
                this.gridComp2.store.loadData({rows:[],count:0},false);
            }

        }, this);

        this.gridRecibosDetalles.store.on('save', function(){
            this.gridPersonasGanancias.store.reload();
        }, this);

        this.gridRecibosDetalles.store.on('load', function(){
            if(this.btnOcultarCeros.pressed) this.filtrarCeros();
        },this);

        this.gridRecibos.onBeforeCreateColumns = function (columns) {
                columns.splice(2,0,{
                    xtype: 'actioncolumn',
                    header: '',
                    menuDisabled: true,
                    width: 25,
                    items: [
                        {
                            icon   : 'images/control_repeat_blue.png',                // Use a URL in the icon config
                            tooltip: 'Recalcular',
                            scope: this,
                            handler: function(grid, rowIndex, colIndex) {
                                var rec = grid.store.getAt(rowIndex);

                                Ext.Msg.confirm('Atención!', 'Quiere recalcular la liquidacion para  '+rec.data.Persona_cdisplay+'?', function(btn) {
                                    if (btn == 'yes') {
                                        // Mensaje de Espera
                                        var wait = app.desktop.showMsg({
                                            progressText: 'Espere por favor...',
                                            msg: 'Liquidando',
                                            modal: true,
                                            closable: false,
                                            width: 300,
                                            wait: true,
                                            waitConfig: {interval:200}
                                        });

                                        Models.Liquidacion_Model_LiquidarMapper.reliquidar(rec.data.Liquidacion, rec.data.Periodo, 1, 'SERVICIO', rec.data.Servicio, function(result,e){
                                            if (e.status) {
                                                // Ext.MessageBox.hide();
                                                var that = this;
                                                var task = {
                                                    run: function(){
                                                        Models.Model_JobsMapper.getJobStatus(result,function(r, ev){
                                                            if (ev.status) {
                                                                switch(r.status){
                                                                    case 1: // finalizado con error
                                                                        Ext.TaskMgr.stop(task);
                                                                        wait.hide();
                                                                        app.publish('/desktop/showError',r.error);
                                                                    break;
                                                                    case 2: // en espera
                                                                        wait.updateText('Esperando ejecución...');
                                                                    break;
                                                                    case 3: // terminado
                                                                        Ext.TaskMgr.stop(task);
                                                                        wait.hide();
                                                                        this.store.reload();
                                                                    break;
                                                                    case 4: // en espera
                                                                        wait.updateText('Liquidación en ejecución...');
                                                                    break;
                                                                }
                                                            } else {
                                                                this.store.reload();
                                                            }
                                                        },that);
                                                    },
                                                    interval: 1000 //1 second
                                                };
                                                Ext.TaskMgr.start(task);
                                            }
                                        },this);
                                    }
                                }, this);
                            }
                        }
                    ]
                });
                columns.push({
                    xtype: 'actioncolumn',
                    header: '',
                    menuDisabled: true,
                    width: 30,
                    items: [
                        {
                            tooltip: 'Ir a Datos Personales',
                            icon   : 'images/user.png',
                            scope : this,
                            handler: function(grid, rowIndex, colIndex) {
                                var rec = grid.store.getAt(rowIndex);
                                app.publish('/desktop/modules/Base/administrarEmpleados', {'action': 'find','value':rec.data.Persona});
                            }
                        },{
                            tooltip: 'Ir a Datos Laborales',
                            icon   : 'images/user_suit.png',
                            scope : this,
                            handler: function(grid, rowIndex, colIndex) {
                                var rec = grid.store.getAt(rowIndex);
                                app.publish('/desktop/modules/Base/administrarEmpleadosLaborales', {'action': 'find','value':rec.data.Persona});
                            }
                        }
                    ]
                });
            };

    },

    renderLiquidador: function () {
        return {
            layout: 'fit',
            border : true,
            margins: '0 5 0 0',
            flex: 3,
            tbar: [
                {
                    tooltip: 'Ejecutar Liquidación',
                    icon:'images/32/run.png',
                    // text: 'Liquidar',
                    scale: 'large',
                    scope: this,
                    handler: function() {
                        this.liquidar();
                    }
                },
                {
                    tooltip: 'Borrar liquidación',
                    icon:'images/32/cancel.png',
                    // text: 'Borrar',
                    scale: 'large',
                    scope: this,
                    handler: function() {
                        this.grid.deleteRows();
                    }
                },
                '-',
                {
                    xtype: 'buttongroup',
                    //title: 'Clipboard',
                    columns: 1,
                    items:[
                        this.comboPeriodos,
                        this.comboEmpresas,
                        this.comboTipos
                    ]
                },
                {
                    tooltip: 'Informes',
                    icon:'images/32/print.png',
                    // text: 'Liquidar',
                    scale: 'large',
                    scope: this,
                    handler: function() {
                        app.publish('/desktop/modules/Liquidacion/ReporteLibroLiquidaciones',{action:'launch'});
                    }
                }
                ],
            items : [this.grid]
        }
    },

    renderWindowContent: function () {

        this.comboPeriodos = new Ext.ux.form.ComboBox({
            selectOnFocus: true,
            forceSelection: true,
            forceReload: true,
            loadingText: 'Cargando...',
            emptyText: 'Período...',
            lazyRender: true,
            width:128,
            triggerAction: 'all',
            valueField: 'Id',
            displayField: 'Descripcion',
            autoLoad: false,
            store: new Ext.data.JsonStore ({
                //url: '/datagateway/combolist/model/LiquidacionesPeriodos/m/Liquidacion/sort/FechaDesde/dir/desc',
                url: '/datagateway/combolist/model/LiquidacionesPeriodos/m/Liquidacion/fetch/Ultimos12Meses',
                autoLoad: true,
                root: 'rows',
                idProperty: 'Id',
                // storeId: 'DepositoStore32sd',
                totalProperty: 'count',
                fields: [ 'Id', 'Anio', 'TipoDeLiquidacionPeriodo','TipoDeLiquidacionPeriodo_cdisplay', {name:'FechaDesde'}, {name:'FechaHasta'}, 'Valor', 'Descripcion']
            })
        });

        this.comboEmpresas = new Ext.ux.form.ComboBox({
            selectOnFocus: true,
            forceSelection: true,
            forceReload: true,
            loadingText: 'Cargando...',
            emptyText: 'Empresa...',
            lazyRender: true,
            width:128,
            triggerAction: 'all',
            valueField: 'Id',
            displayField: 'Descripcion',
            autoLoad: false,
            store: new Ext.data.JsonStore ({
                url: '/datagateway/combolist/model/Empresas/m/Base',
                autoLoad: true,
                root: 'rows',
                idProperty: 'Id',
                // storeId: 'DepositoStore32sd',
                totalProperty: 'count',
                fields: [ 'Id', 'Descripcion']
            })
        });

        this.comboTipos = new Ext.ux.form.ComboBox({
            selectOnFocus: true,
            forceSelection: true,
            forceReload: true,
            loadingText: 'Cargando...',
            emptyText: 'Tipo...',
            lazyRender: true,
            width:128,
            triggerAction: 'all',
            value: 1,
            valueField: 'Id',
            displayField: 'Descripcion',
            autoLoad: false,
            store: new Ext.data.JsonStore ({
                url: '/datagateway/combolist/model/TiposDeLiquidaciones/m/Liquidacion',
                autoLoad: true,
                root: 'rows',
                idProperty: 'Id',
                totalProperty: 'count',
                fields: [ 'Id', 'Descripcion']
            })
        });

        this.comboPeriodos.on('select', function(e,r){
            this.periodo = r.data.Id;
        },this);

        this.comboEmpresas.on('select', function(e,r){
            this.empresa = r.data.Id;
        },this);
        this.tipos = 1;
        this.comboTipos.on('select', function(e,r){
            this.tipos = r.data.Id;
        },this);

        this.crearComparador();

        // tab con detalles
        this.detalleTab = new Ext.TabPanel({
            activeTab: 0,
            border: true,
            enableTabScroll: true,
            deferredRender: false,
            items: [
                this.gridRecibosDetalles,
                this.gridVariablesCalculadas,
                this.comparadorRecibos,
                this.gridPersonasGanancias,
                this.gridCabeceraRecibos
            ]
        });

        return {
            layout : 'border',
            bodyStyle:'background:rgb(214, 214, 214)',
            border : false,
            items : [{
                region : 'west',
                layout: 'fit',
                title: 'Liquidaciones',
                width : 350,
                collapsible: true,
                split: true,
                items: [
                    this.renderLiquidador()
                ]
            },{
                region : 'center',
                layout: 'fit',
                border: false,
                items: [
                    {
                        layout: 'border',
                        defaults: { layout: 'fit', border: false },

                        items: [
                            {
                                region: 'north',
                                height: 240,
                                layout: 'fit',
                                collapsible: true,
                                title: 'Recibos',
                                split: true,
                                border: false,
                                items: this.gridRecibos
                            },
                            {
                                region: 'center',
                                layout: 'fit',
                                border: false,
                                items: [
                                    this.detalleTab
                                ]
                            }
                        ]
                    }
                ]
            }]
        }
    },

    crearComparador: function () {

        this.comboAjustes = new Ext.ux.form.ComboBox({
            store: [1],
            selectOnFocus: true,
            forceSelection: true,
            forceReload: true,
            triggerAction: 'all',
            emptyText:'Seleccione un ajuste',
        });

        var o = {
            'xtype'        : 'radgridpanel',
            'withPaginator': false,
            'plugins'      :[new Ext.ux.grid.GroupSummary(), new Ext.ux.grid.GridSummary()],
            'filters'      : true,
            'url'          : '/default/datagateway',
            'flex'         : 1,
            'loadAuto'     : false,
            'model'        : 'LiquidacionesRecibosDetalles',
            'module'       : 'Liquidacion',
            'forceFit'     : true,
            'stateful'     : false,
            'iniSection'   : 'reducido',
            'view'         : new Ext.grid.GroupingView({
                forceFit:true,
                hideGroupedColumn: true,
                groupTextTpl: '<span style=\'font-size:15px;\'>{text}</span>'
            })
        };
        var o1 = {
            'xtype'        : 'radgridpanel',
            'withPaginator': false,
            'plugins'      :[new Ext.ux.grid.GroupSummary(), new Ext.ux.grid.GridSummary()],
            'filters'      : true,
            'url'          : '/default/datagateway',
            'flex'         : 1,
            'loadAuto'     : false,
            'model'        : 'LiquidacionesRecibosDetalles',
            'module'       : 'Liquidacion',
            'forceFit'     : true,
            'stateful'     : false,
            'iniSection'   : 'reducido',
            'view'         : new Ext.grid.GroupingView({
                forceFit:true,
                hideGroupedColumn: true,
                groupTextTpl: '<span style=\'font-size:15px;\'>{text}</span>'
            }),

        };
        this.gridComp1 = Ext.create(o);
        this.gridComp2 = Ext.create(o1);

        this.gridComp2.on('bodyScroll', function (scrollLeft, scrollTop) {
                this.gridComp1.getView().scroller.scrollTo('top', scrollTop);
            }, this
        );

        this.gridComp1.on('bodyScroll', function (scrollLeft, scrollTop) {
                this.gridComp2.getView().scroller.scrollTo('top', scrollTop);
            }, this
        );

        this.comboAjustes.on('select',function(e, r) {
            var selected = this.gridRecibos.getSelectionModel().getSelected();
            if (!selected) {
                this.publish('/desktop/showError','Seleccione primero un recibo.');
                return;
            }

            Models.Liquidacion_Model_LiquidacionesRecibosMapper.getIdRecibosAjuste(r.data.field1, selected.data.Servicio, selected.data.Periodo, function(result, e){
                if (e.status) {
                    var detailGrid  = {remotefield: 'LiquidacionRecibo', localfield: 'Id'};

                    // solo los devengados de este periodo
                    this.gridComp1.setPermanentFilter(0, 'Periodo', selected.data.Periodo);
                    this.gridComp1.loadAsDetailGrid(detailGrid, result[0]);
                    this.gridComp2.setPermanentFilter(0, 'Periodo', selected.data.Periodo);
                    this.gridComp2.loadAsDetailGrid(detailGrid, result[1]);
                }
            }, this);
        }, this);

        this.comparadorRecibos = new Ext.Panel({
            title: 'Ajustes',
            tbar: ['Ajuste', this.comboAjustes],
            layout: {
                type: 'hbox',
                pack: 'start',
                align: 'stretch'
            },
            items:[
                this.gridComp1,
                this.gridComp2
            ]
        });
    },

    liquidar: function() {
        if (!this.periodo) {
            app.publish('/desktop/showMsg', {
                title: 'Atención',
                msg: 'Seleccione primero un período',
                renderTo: 'x-desktop',
                modal: true,
                icon: Ext.Msg.ERROR,
                buttons: Ext.Msg.OK
            });
            return;
        }
        if (!this.empresa) {
            app.publish('/desktop/showMsg', {
                title: 'Atención',
                msg: 'Seleccione primero una empresa',
                renderTo: 'x-desktop',
                modal: true,
                icon: Ext.Msg.ERROR,
                buttons: Ext.Msg.OK
            });
            return;
        }

        // Mensaje de Espera
        var wait = app.desktop.showMsg({
            progressText: 'Espere por favor...',
            msg: 'Liquidando',
            modal: true,
            closable: false,
            width: 300,
            wait: true,
            waitConfig: {interval:200}
        });

        Models.Liquidacion_Model_LiquidarMapper.liquidar(this.periodo, this.tipos, this.empresa, function(result,e){
            if (e.status) {
                // Ext.MessageBox.hide();
                var that = this
                var task = {
                    run: function(){
                        Models.Model_JobsMapper.getJobStatus(result,function(r, ev){
                            if (ev.status) {
                                switch(r.status){
                                    case 1: // finalizado con error
                                        Ext.TaskMgr.stop(task);
                                        wait.hide();
                                        app.publish('/desktop/showError',r.error);
                                    break;
                                    case 2: // en espera
                                        wait.updateText('Esperando ejecución...');
                                    break;
                                    case 3: // terminado
                                        Ext.TaskMgr.stop(task);
                                        wait.hide();
                                        that.grid.store.reload();
                                    break;
                                    case 4: // en espera
                                        wait.updateText('Liquidación en ejecución...');
                                    break;
                                }
                            } else {
                                that.grid.store.reload();
                            }
                        },that);
                    },
                    interval: 1000 //1 second
                };
                Ext.TaskMgr.start(task);
            }
        },this);
    }

});

new Apps.<?=$this->name?>();