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
            this.store = new Ext.data.JsonStore({
                autoDestroy: true,
                autoLoad: true,
                url: '/Contable/resumenVentasMensual/get',
                baseParams: {
                    mes: <?= date('m') ?>,
                    anio: <?= date('Y') ?>
                },
                remoteSort: false,
                sortInfo: {
                    field: 'Descripcion',
                    direction: 'ASC'
                },
                idProperty: 'Codigo',
                root: 'rows',
                totalProperty: 'count',
                fields: [{
                    name: 'Codigo'
                }, {
                    name: 'Descripcion'
                }, {
                    name: 'Cantidad'
                }]
            });

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
            width: 900,
            height:550,
            items: [
                this.renderWindowContent()
            ]
        };
        return app.desktop.createWindow(defaultWinCfg);
    },
	
    renderWindowContent: function () {
        return {
            tbar: new Ext.Toolbar({
                layout: 'border',
                height: 85,
                frame: true,
                defaults: { border: false },
                items: [
                    {
                        xtype: 'panel',
                        region: 'west',
                        frame: true,
                        width: 300,
                        layout: 'form',
                        labelWidth: 50,
                        items: [
                            {
                                id: 'resumenVentasMensualMes-Id',
                                xtype: 'combo',
                                fieldLabel: 'Mes',
                                mode: 'local',
                                editable: false,
                                width: 160,
                                valueField: 'Mes',
                                forceSelection: false,
                                displayField: 'Descripcion',
                                triggerAction: 'all',
                                value: <?= date('m') ?>,
                                store: new Ext.data.ArrayStore({
                                    id: 0,
                                    fields: [ 'Mes', 'Descripcion' ],
                                    data: [
                                        [1, '01 - Enero'], [2, '02 - Febrero'], [3, '03 - Marzo'], [4, '04 - Abril'], [5, '05 - Mayo'], [6, '06 - Junio'],
                                        [7, '07 - Julio'], [8, '08 - Agosto'], [9, '09 - Septiembre'], [10, '10 - Octubre'], [11, '11 - Noviembre'], [12, '12 - Diciembre']
                                    ]
                                })
                            },
                            {
                                id: 'resumenVentasMensualAnio-Id',
                                xtype: 'combo',
                                fieldLabel: 'Año',
                                width: 160,
                                allowDecimals: false,
                                allowNegative: false,
                                minValue: 1900,
                                maxLength: 4,
                                value: "<?=date('Y')?>",
                                store: new Ext.data.ArrayStore({
                                    id: 0,
                                    fields: [ 'Año'],
                                    data: [
                                        [1, '2018'], [2, '2017'], [3, '2016']
                                    ]
                                })
                            }
                        ]
                    },
                    {
                        xtype: 'panel',
                        region: 'center',
                        layout: 'form',
                        frame: true,
                        height: 80,
                        items: [
                            {
                                xtype: 'button',
                                text: 'Ver Grafico',
                                icon: 'images/chart_bar.png',
                                handler: function() {
                                    var mes = Ext.getCmp('resumenVentasMensualMes-Id').getValue();
                                    var anio = Ext.getCmp('resumenVentasMensualAnio-Id').getValue();
                                    
                                    if (!mes || !anio) {
                                        Ext.Msg.alert('Advertencia', 'Debe completar el mes y año');
                                        return;
                                    }
                                    
                                    Ext.getCmp('resumenVentasMensual-panelGrid').setTitle('Periodo: '+mes+'/'+anio);
                                    
                                    this.store.baseParams.mes = mes;
                                    this.store.baseParams.anio = anio;
                                    this.store.reload();
                                },
                                scope: this
                            },
                            {
                                xtype: 'button',
                                text: 'Ver Reporte (PDF)',
                                icon: 'images/page_pdf.png',
                                handler: function() {
                                    
	                            var mes = Ext.getCmp('resumenVentasMensualMes-Id').getValue();
                                    var anio = Ext.getCmp('resumenVentasMensualAnio-Id').getValue();
                                    
                                    if (!mes || !anio) {
                                        Ext.Msg.alert('Advertencia', 'Debe completar el mes y año');
                                        return;
                                    }
                                    
                                    app.desktop.createWindow({
                                        id: this.id+'gmaps-win',
                                        title: this.title,
                                        modal: true,
                                        width: 600,
                                        height: 500,
                                        iconCls: 'icon-grid',
                                        border:  false,
                                        shim: false,
                                        animCollapse: false,
                                        layout: 'fit',
                                        items: [{
                                            xtype: 'iframepanel',
                                            defaultSrc: '/Window/birtreporter/reportventas?template=ListadoCantidadArticulosVendidos&output=pdf&mes='+mes+'&anio='+anio
                                        }]
                                    }).show();
                                },
                                scope: this
                            }, 
                            {
                                xtype: 'button',
                                text: 'Ver Reporte (EXCEL)',
                                icon: 'images/page_excel.png',
                                handler: function() {
                                    
	                            var mes = Ext.getCmp('resumenVentasMensualMes-Id').getValue();
                                    var anio = Ext.getCmp('resumenVentasMensualAnio-Id').getValue();
                                    
                                    if (!mes || !anio) {
                                        Ext.Msg.alert('Advertencia', 'Debe completar el mes y año');
                                        return;
                                    }

                                    document.location.href = '/Window/birtreporter/reportventas?template=ListadoCantidadArticulosVendidos&output=xls&mes='+mes+'&anio='+anio;         
                                    
                                },
                                scope: this
                            }
                        ]
                    }
                ]
            }),
            layout: 'fit',
            border: false,
            defaults: { border: false },
            items:
                {
                    layout: 'border',
                    defaults: {
                        border: false
                    },
                    items: [
                        {
                            region: 'center',
                            title: "<?= 'Periodo: '.date('n').'/'.date('Y')?>",
                            id: 'resumenVentasMensual-panelGrid',
                            layout: 'fit',
                            items:  new Ext.grid.GridPanel({
                                plugins: [ new Ext.ux.grid.GridSummary() ],
                                store: this.store,
                                columns: [
                                    {
                                        header   : 'Codigo',
                                        width    : 80,
                                        dataIndex: 'Codigo',
                                        sortable : false
                                    },{
                                        header   : 'Descripcion',
                                        width    : 300,
                                        sortable : false,
                                        dataIndex: 'Descripcion'
                                    },{
                                        header   : 'Cantidad',
                                        dataIndex: 'Cantidad',
                                        width    : 80,
                                        sortable : false
                                    }
                                ]
                            })
                        },
                        {
                            region: 'east',
                            title: '&nbsp;',
                            width: 420,
                            layout: 'fit',
                            items: {
                                xtype: 'piechart',
                                store: this.store,
                                dataField: 'Cantidad',
                                categoryField: 'Descripcion'
                            }
                        }
                    ]
                }
        }
    }
    
});

new Apps.<?=$this->name?>();
