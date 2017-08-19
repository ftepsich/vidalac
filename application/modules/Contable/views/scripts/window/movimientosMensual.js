Ext.ns( 'Apps' );

Apps.<?=$this->name?> = Ext.extend(RadDesktop.Module, {
    autoStart: true,
    title: '<?=$this->title?>',
    appChannel: '/desktop/modules<?=$this->url()?>',

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
            this.createStore();
            this.createGrid();
            this.createGraphs();

            win = this.create();
        }
        win.show();
    },

    createStore: function () {
        this.store = new Ext.data.JsonStore({
            autoDestroy: true,
            autoLoad: false,
            url: '/Contable/movimientosMensual/get/',
            remoteSort: false,
            idProperty: 'Id',
            root: 'rows',
            totalProperty: 'count',
            baseParams: {
                anio: <?= $this->anio ?>
            },
            fields: [{
                name: 'Id'
            }, {
                name: 'Mes'
            }, {
                name: 'Anio'
            }, {
                name: 'FacturasVenta',
                type: 'float'
            }, {
                name: 'NotasDeCreditoEmitidas',
                type: 'float'
            }, {
                name: 'NotasDeDebitoEmitidas',
                type: 'float'
            }, {
                name: 'FacturasCompra',
                type: 'float'
            }, {
                name: 'NotasDeCreditoRecibidas',
                type: 'float'
            }, {
                name: 'NotasDeDebitoRecibidas',
                type: 'float'
            }]
        });
    },

    createGrid: function () {
        this.grid = new Ext.grid.GridPanel({
            store: this.store,
            width : 250,
            viewConfig: {
                forceFit: true
            },
            tbar: [
                'A&ntilde;o&nbsp;',
                {
                    xtype: 'combo',
                    mode: 'local',
                    triggerAction: 'all',
                    forceSelection: true,
                    editable: false,
                    width: 100,
                    displayField: 'anio',
                    valueField: 'anio',
                    value: <?= $this->anio ?>,
                    store: new Ext.data.JsonStore({
                        fields: ['anio'],
                        data: <?= json_encode($this->aniosCombo) ?>
                    }),
                    listeners: {
                        'select': function(combo, record, index) {
                            this.scope.store.setBaseParam('anio', record.data.anio);
                            this.scope.store.load();
                        }
                    },
                    scope: this
                }
            ],
            columns: [
                {
                    header   : 'Año',
                    width    : 50,
                    dataIndex: 'Anio',
                    sortable : false
                },{
                    id       : 'Mes',
                    header   : 'Mes',
                    width    : 50,
                    sortable : false,
                    dataIndex: 'Mes'
                },{
                    header   : 'Fact. Emitidas',
                    dataIndex: 'FacturasVenta',
                    width    : 80,
                    sortable : false,
                    renderer : 'usMoney'

                },{
                    header   : 'NC Emitidas',
                    dataIndex: 'NotasDeCreditoEmitidas',
                    width    : 80,
                    sortable : false,
                    renderer : 'usMoney'
                },{
                    header   : 'ND Emitidas',
                    dataIndex: 'NotasDeDebitoEmitidas',
                    width    : 80,
                    sortable : false,
                    renderer : 'usMoney'
                },{
                    header   : 'Fact. Ingresadas',
                    dataIndex: 'FacturasCompra',
                    width    : 80,
                    sortable : false,
                    renderer : 'usMoney'
                },{
                    header   : 'NC Recibidas',
                    dataIndex: 'NotasDeCreditoRecibidas',
                    width    : 80,
                    sortable : false,
                    renderer : 'usMoney'
                },{
                    header   : 'ND Recibidas',
                    dataIndex: 'NotasDeDebitoRecibidas',
                    width    : 80,
                    sortable : false,
                    renderer : 'usMoney'
                }
            ]
         });
    },

    createGraphs: function() {
        this.graphFacturas = new Ext.chart.ColumnChart({
            store: this.grid.store,
            series: [{
                type: 'column',
                displayName: 'Facturas Emitidas',
                yField: 'FacturasVenta',
                xField: 'Mes'
            },{
                type: 'column',
                displayName: 'Facturas Ingresadas',
                yField: 'FacturasCompra',
                xField: 'Mes'
            }],
            yAxis: new Ext.chart.NumericAxis({
                labelRenderer: Ext.util.Format.usMoney
            }),
            tipRenderer : function(chart, record, index, series) {
                return series.displayName + '\nPeriodo: ' + record.data.Mes + '/' + record.data.Anio + '\n' +
                       'Total: ' + Ext.util.Format.usMoney(record.get(series.yField));
            },
            extraStyle: {
                padding: 3,
                animationEnabled: true,
                legend: {
                    display: 'bottom'
                },
                xAxis: {
                    color: 0x3366cc,
                    majorGridLines: {size: 1, color: 0xdddddd}
                },
                yAxis: {
                    color: 0x3366cc,
                    majorTicks: {color: 0x3366cc, length: 4},
                    minorTicks: {color: 0x3366cc, length: 2},
                    majorGridLines: {size: 1, color: 0xdddddd},

                }
            }
        });

        this.graphNotas = new Ext.chart.ColumnChart({
            store: this.grid.store,
            series: [{
                type: 'column',
                displayName: 'NC Recibidas',
                yField: 'NotasDeCreditoRecibidas',
                xField: 'Mes'
            },{
                type: 'column',
                displayName: 'ND Recibidas',
                yField: 'NotasDeDebitoRecibidas',
                xField: 'Mes'
            },{
                type: 'column',
                displayName: 'NC Emitidas',
                yField: 'NotasDeCreditoEmitidas',
                xField: 'Mes'
            },{
                type: 'column',
                displayName: 'ND Emitidas',
                yField: 'NotasDeDebitoEmitidas',
                xField: 'Mes'
            }],
            tipRenderer : function(chart, record, index, series){
                return series.displayName + '\nPeriodo: ' + record.data.Mes + '/' + record.data.Anio + '\n' +
                       'Total: ' + Ext.util.Format.usMoney(record.get(series.yField));
            },
            extraStyle: {
                padding: 3,
                animationEnabled: true,
                legend:{
                    display: 'bottom'
                },
                xAxis: {
                    color: 0x3366cc,
                    majorGridLines: {size: 1, color: 0xdddddd}
                },
                yAxis: {
                    color: 0x3366cc,
                    majorTicks: {color: 0x3366cc, length: 4},
                    minorTicks: {color: 0x3366cc, length: 2},
                    majorGridLines: {size: 1, color: 0xdddddd}
                }
            }
        });
    },

    create: function() {
        defaultWinCfg = {
            id: this.id+'-win',
            title: this.title,
            iconCls: 'icon-grid',
            border:  false,
            shim: false,
            animCollapse: false,
            layout: 'fit',
            width: 900,
            height: 550,
            items: this.renderWindowContent()
        };
        return app.desktop.createWindow(defaultWinCfg);
    },

    renderWindowContent: function () {
        return {
            layout: 'border',
            border: false,
            defaults: {
                layout: 'fit',
                border: false
            },
            items: [
                {
                    region: 'center',
                    items: this.grid
                },
                {
                    region: 'south',
                    minSize: 370,
                    height: 370,
                    split: true,
                    items: {
                        xtype: 'tabpanel',
                        activeTab: 0,
                        items: [
                            {
                                title: 'Facturas',
                                border: true,
                                items: this.graphFacturas
                            },
                            {
                                title: 'Notas',
                                border: true,
                                items: this.graphNotas
                            }
                        ]
                    }
                }
            ]
        }
    }

});

new Apps.<?=$this->name?>();