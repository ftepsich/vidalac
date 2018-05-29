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

    eventlaunch: function(ev) {
        this.createWindow();
    },

    createWindow: function() {
        var win = app.desktop.getWindow(this.id+'-win');
        if ( !win ) {
            this.grid = Ext.ComponentMgr.create(<?=$this->grid?>);
            this.grid.flex=1;
            win = this.create();
        }
        win.show();
    },

    create: function()
    {
        defaultWinCfg = {
            id: this.id+'-win',
            title: this.title,
            iconCls: 'icon-grid',
            border:  false,
            shim: false,
            animCollapse: false,
            layout: 'fit',
            width: 1050,
            height:600,
            items: [
                this.renderWindowContent()
            ]
        };
        return app.desktop.createWindow(defaultWinCfg);
    },

    renderWindowContent: function ()
    {
         var gridStockHistorico = Ext.create({
            title: 'Historico',
            loadAuto: false,
            xtype: 'radgridpanel',
            id: 'gridhistorico30dias',
            flex: .30,
            layout: 'fit',
            border: true,
            margins: '2 0 0 0',
            filters: true,
            url: 'Almacenes/ControlStock/stockhistorico',
            forceFit: true,
            stateful: false,
            withPaginator: false
        });
        
        var gridStockFuturo = Ext.create({
            title: 'Futuro',
            loadAuto: false,
            xtype: 'radgridpanel',
            id: 'gridfuturo30dias',
            flex: .70,
            layout: 'fit',
            border: true,
            margins: '2 0 0 2',
            filters: true,
            url: '/Almacenes/ControlStock/stockfuturo',
            forceFit: true,
            stateful: false,
            withPaginator: false
        });
        
        return {
            layout: 'border',
            border: false,
            defaults: { layout: 'fit', border: false },
            items: [
                {
                    region: 'center',
                    border:  false,
                    margins: '2 2 2 2',
                    items: [
                        {
                            border:false,
                            layout: {
                                type: 'vbox',
                                align: 'stretch'
                            },
                            items: [
                                this.grid,
                                {
                                    border: false,
                                    flex:1,
                                    layout: {
                                        type: 'hbox',
                                        align: 'stretch'
                                    },
                                    items: [gridStockHistorico, gridStockFuturo]
                                }
                            ]
                        }
                    ]
                },
                {
                    region: 'east',
                    border: false,
                    margins: '2 2 2 0',
                    width: 400,
                    items: [
                            {
                                layout: {
                                    type: 'vbox',
                                    align: 'stretch'
                                },
                                border: true,
                                items: [
                                    new Ext.chart.LineChart({
                                        title: 'Historico',
                                        margins: '2 2 2 2',
                                        flex: 1,
                                        title: 'Historico',
                                        store: gridStockHistorico.store,
                                        xField: 'fecha',  
                                        xAxis: new Ext.chart.TimeAxis({
                                            displayName: 'Fecha',
                                            labelRenderer : Ext.util.Format.dateRenderer('d/M'),
                                            majorTimeUnit : 'day'
                                        }),
                                        series: [
                                            {
                                                type: 'line',
                                                displayName: 'Stock',
                                                yField: 'Stock',
                                                style: {
                                                    size: 6,
                                                }
                                            }
                                        ],
                                        extraStyle: {
                                            padding: 10,
                                            animationEnabled: true,
                                            xAxis: {
                                                color: 0x3366cc,
                                                showLabels: true,
                                                majorGridLines: {size: 1, color: 0xdddddd},
                                                minorGridLines: {size: 1, color: 0xdddddd}
                                            },
                                            yAxis: {
                                                color: 0x3366cc,
                                                majorTicks: {color: 0x3366cc, length: 4},
                                                minorTicks: {color: 0x3366cc, length: 2},
                                                majorGridLines: {size: 1, color: 0xdddddd}
                                            }
                                        }
                                    }),
                                    new Ext.chart.LineChart({
                                        title: 'Futuro',
                                        margins: '2 2 2 2',
                                        flex: 1,
                                        store: gridStockFuturo.store,
                                        xField: 'fecha',
                                        xAxis: new Ext.chart.TimeAxis({
                                            displayName: 'Fecha',
                                            labelRenderer : Ext.util.Format.dateRenderer('d/M'),
                                            majorTimeUnit : "day"
                                        }),
                                        series: [
                                            {
                                                type: 'line',
                                                displayName: 'Pedido',
                                                yField: 'Pedido',
                                                style: {
                                                    size: 6
                                                }
                                            }, {
                                                type: 'line',
                                                displayName: 'Utilizado',
                                                yField: 'Utilizado',
                                                xField: 'fecha',
                                                style: {
                                                    size: 6
                                                }
                                            }, {
                                                type: 'line',
                                                displayName: 'Producido',
                                                yField: 'Producido',
                                                xField: 'fecha',
                                                style: {
                                                    size: 6
                                                }
                                            }, {
                                                type: 'line',
                                                displayName: 'Total',
                                                yField: 'total',
                                                xField: 'fecha',
                                                style: {
                                                    size: 6
                                                }
                                            }
                                        ],
                                        extraStyle: {
                                            padding: 10,
                                            animationEnabled: true,
                                            legend: {
                                                display: 'bottom'
                                            },
                                            xAxis: {
                                                color: 0x3366cc,
                                                showLabels: true,
                                                majorGridLines: {size: 1, color: 0xdddddd},
                                                minorGridLines: {size: 1, color: 0xdddddd}

                                            },
                                            yAxis: {
                                                color: 0x3366cc,
                                                majorTicks: {color: 0x3366cc, length: 4},
                                                minorTicks: {color: 0x3366cc, length: 2},
                                                majorGridLines: {size: 1, color: 0xdddddd}
                                            }
                                        }
                                    })
                            ]
                        }
                    ]
                }
            ]
        };
    }
});

new Apps.<?=$this->name?>();