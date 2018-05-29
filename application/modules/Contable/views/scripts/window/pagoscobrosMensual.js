Ext.ns( 'Apps' );

Apps.<?=$this->name?> = Ext.extend(RadDesktop.Module, {
    autoStart: true,
    title: '<?=$this->title?>',
    appChannel: '/desktop/modules<?=$this->url()?>',
	
    eventfind: function (ev) {
        this.createWindow();
        var params = this.grid.buildFilter(0, 'Id',ev.value);
        this.grid.store.load(params);
    },
	
    eventsearch: function (ev) {
        this.createWindow();
        var params= this.grid.buildFilter(0, ev.field,ev.value);
        this.grid.store.load(params);
    },
	
    eventlaunch: function(ev) {
        this.createWindow();
    },
    
    getUltimosAños: function() {
        var d = new Date();
        var year = d.getFullYear();
        
        anios = [];
        year = parseInt(year);
        for (var i = 0;i<20;i++){
           anios[i] =  [year-i,year-i];
        }
        return anios;
    },

    createWindow: function() {
        var win = app.desktop.getWindow(this.id+'-win');
        if ( !win ) {
            var d = new Date();
            var year = d.getFullYear();
            
            var cstore = new Ext.data.ArrayStore({
                fields: ['id', 'desc'],
                data : this.getUltimosAños()
            });
            var combo = new Ext.form.ComboBox({
                store:cstore,
                value:year,
                displayField:'desc',
                typeAhead: true,
                mode: 'local',
                forceSelection: true,
                triggerAction: 'all',
                emptyText:'Año...',
                selectOnFocus:true,
            });
            
            
        
            store = new Ext.data.JsonStore({
                autoDestroy: true,
                autoLoad: false,
                url: '/Contable/pagoscobrosMensual/get/',
                remoteSort: false,
                sortInfo: {
                    field: 'Mes',
                    direction: 'ASC'
                },
                storeId: 'myStore',
                idProperty: 'id',
                root: 'rows',
                totalProperty: 'count',
                fields: [{
                    name: 'Id'
                }, {
                    name: 'Mes'
                }, {
                    name: 'Anio'
                }, {
                    name: 'MontoRecibo',
                    type: 'float',
                    rederer : 'usMoney'
                }, {
                    name: 'MontoOPago',
                    type: 'float',
                    rederer : 'usMoney'
                }]
            });
            
            //Cuando cambiamos el combo recargamos los datos
            combo.on('select', function(c,r,i) {
                store.load({params :{anio: r.data.id}});
            });
            
            this.grid = new Ext.grid.GridPanel({
                store: store,
                region: 'center',
                tbar:['Año ',combo],
                columns: [
                    {
                        header   : 'Año',
                        width    : 120,
                        dataIndex: 'Anio',
                        sortable : false
                    },{
                        id       : 'Mes',
                        header   : 'Mes',
                        width    : 40,
                        sortable : false,
                        dataIndex: 'Mes'
                    },{
                        header   : 'Cobros',
                        dataIndex: 'MontoRecibo',
                        width    : 80,
                        sortable : false,
                        renderer : 'usMoney'
                    },{
                        header   : 'Pagos',
                        dataIndex: 'MontoOPago',
                        width    : 80,
                        sortable : false,
                        renderer : 'usMoney'
                    }
                ]
             });
            store.load({'anio':year});
            win = this.create();
        }
        win.show();
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
            width: 1000,
            height:500,
            items: [
                this.renderWindowContent()
            ]
        };
        return app.desktop.createWindow(defaultWinCfg);
    },
	
    renderWindowContent: function () {
        return {
            layout: 'border',
            border: false,
            defaults: {
                border: false
            },
            items: [
                this.grid,
                {
                    region:'west',
                    layout:'fit',
                    width: 650,
                    border:true,
                    items:
                        new Ext.chart.ColumnChart({
                            store: this.grid.store,
                            //url:'../ext-3.0-rc1/resources/charts.swf',
                            series: [{
                                type: 'column',
                                displayName: 'Pagos',
                                yField: 'MontoOPago',
                                xField: 'Mes'
                            },{
                                type: 'column',
                                displayName: 'Cobros',
                                yField: 'MontoRecibo',
                                xField: 'Mes'
                               
                            }],
                            tipRenderer : function(chart, record, index, series) {
                                return series.displayName+'\nPeriodo: ' + record.data.Mes + '/' + record.data.Anio + "\n" +
                                       'Total: ' + Ext.util.Format.usMoney(record.get(series.yField));
                            },
                            extraStyle: {
                                padding: 10,
                                animationEnabled: true,
                                legend:{
                                    display:'bottom'
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

                        })
                }
            ]
        }
    }
    
});

new Apps.<?=$this->name?>();
