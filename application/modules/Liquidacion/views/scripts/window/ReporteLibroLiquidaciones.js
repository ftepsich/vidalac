Ext.ns( 'Apps' );

Apps.<?=$this->name?> = Ext.extend(RadDesktop.Module, {
    autoStart: true,
    title: '<?=$this->title?>',
    appChannel: '/desktop/modules<?=$this->url()?>',

    eventlaunch: function(ev)
    {
        this.createWindow();
    },

    createWindow: function()
    {
        var win = app.desktop.getWindow(this.id+'-win');
        if ( !win ) {
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
            resizable:false,
            animCollapse: false,
            layout: 'fit',
            width: 600,
            height:350,

            items: [
                this.renderWindowContent()
            ]
        };

        return app.desktop.createWindow(defaultWinCfg);
    },






    renderWindowContent: function ()
    {


        var storeReportes = new Ext.data.JsonStore({
                fields: ['id', 'reporte'],
                data :  [{"id":"1","reporte":"Libro de Liquidacion"}
                        ,{"id":"2","reporte":"Exportador informe 931 v34, 35 y 36"}
                        ,{"id":"3","reporte":"Recibo de Sueldo"}
                        ,{"id":"4","reporte":"Exportador informe 931 v36.5 y 37"}
                        ,{"id":"5","reporte":"Exportador informe SICORE v8"}
                        ,{"id":"6","reporte":"Totales por CUIT"}
                        ,{"id":"7","reporte":"Libro de Liquidacion (para Pagos)"}
                        ,{"id":"8","reporte":"Totales por Codigo"}
                        ,{"id":"9","reporte":"Libro de Liquidacion (individual)"}
                        ,{"id":"10","reporte":"Libro de Liquidacion (normal y especial)"}
                        ],
                sortInfo: {
                    field: 'reporte',
                    direction: 'ASC' // or 'DESC' (case sensitive for local sorting)
                }
            });


        this.listaReportes = new Ext.list.ListView({
            fieldLabel: 'Repo',
            id:   'mSelect',
            name: 'mSelect',
            store: storeReportes,
            multiSelect: false,
            anchor: '95%',
            emptyText: 'Nada que mostrar',
            reserveScrollOffset: true,
            hideHeaders: true,
            columns: [{
                dataIndex: 'reporte', align: 'left'
            }]
        });


        return {
            xtype: 'form',
            url : '/Liquidacion/ReporteLibroLiquidaciones/verreporte',
            layout: 'form',
            border: false,
            bodyStyle: 'padding:10px',
            defaults: {
                border: false
            },
            items: [
                // this.listaReportes,
                {
                    xtype: 'xcombo',
                    store: new Ext.data.ArrayStore({
                        fields: ['desc', 'id'],
                        data : [
                            ['Libro de Liquidacion', '1'],
                            ['Libro de Liquidacion (individual)', '9'],
                            ['Libro de Liquidacion (normal y especial)', '10'],
                            ['Libro de Liquidacion (para Pagos)', '7'],
                            ['Exportador informe 931 v34, 35 y 36', '2'],
                            ['Exportador informe 931 v36.5 y 37', '4'],
                            ['Exportador informe SICORE v8', '5'],
                            ['Recibo de Sueldo', '3'],
                            ['Totales por CUIT', '6'],
                            ['Totales por Codigo', '8']
                        ]
                    }),
                    value: 1,
                    anchor: '95%',
                    alowBlank: false,
                    displayField:'desc',
                    valueField: 'id',
                    typeAhead: true,
                    fieldLabel: 'Modelo',
                    name: 'Modelo',
                    mode: 'local',
                    forceSelection: true,
                    triggerAction: 'all',
                    selectOnFocus:true
                },
                {
                    xtype: 'xcombo',
                    displayField: 'LiquidacionPeriodo_cdisplay',
                    autoLoad: true,
                    anchor: '95%',
                    forceReload: true,
                    hiddenName: "Liquidacion",
                    loadingText: "Cargando...",
                    lazyRender: true,
                    store: new Ext.data.JsonStore({ "id":0,
                        "url":"datagateway\/combolist\/model\/Liquidaciones/m\/Liquidacion\/sort\/LiquidacionPeriodo_cdisplay\/dir\/desc",
                        "storeId":"LiquidaiconesStore"}),
                    typeAhead: true,
                    valueField: "Id",
                    pageSize: 20,
                    editable: true,
                    autocomplete: false,
                    allowBlank: false,
                    allowNegative: false,
                    fieldLabel: "Liquidacion",
                    name: "Liquidacion",
                    displayFieldTpl: "{LiquidacionPeriodo_cdisplay}",
                    forceSelection: true,
                    triggerAction: 'all',
                    selectOnFocus: true,
                    tpl:"<tpl for=\".\"><div class=x-combo-list-item><h3>{Empresa_cdisplay}<\/h3>{LiquidacionPeriodo_cdisplay} | {TipoDeLiquidacion_cdisplay}<\/div><\/tpl>"
                },{
                    xtype: 'xdatefield',
                    fieldLabel: 'Fecha Emision (para SICORE)',
                    format: 'd/m/Y',
                    name: 'FechaEmision'
                },
                {
                    xtype: 'radiogroup',
                    fieldLabel: 'Formato',
                    width: 150,
                    items: [
                        { boxLabel: 'PDF', name: 'formato', inputValue: 'pdf', checked: true },
                        { boxLabel: 'Excel', name: 'formato', inputValue: 'xls' },
                        { boxLabel: 'Html', name: 'formato', inputValue: 'html' }
                    ]
                }
            ],
            buttons:[
                {
                    text:  'Ver Reporte',
                    handler: function () {
                        values = this.ownerCt.ownerCt.getForm().getValues();
                        var  params = '';

                        if (values.Liquidacion != '') {
                            params += '/Liquidacion/'+values.Liquidacion;
                        }

                        if (values.Modelo != '') {
                            params += '/Modelo/'+values.Modelo;
                        }

                        if (values.formato != '') {
                            params += '/formato/'+values.formato;
                        }

                        // Contolo la fecha solo para el SICORE
                        if (values.Modelo == 5) {
                            if (values.FechaEmision == 'undefined') {
                                Ext.Msg.alert('Atencion', 'Debe completar la fecha de emisión.');
                                return;
                            } else {
                                params += '/Fecha/'+values.FechaEmision;
                            }
                        }

                        app.publish('/desktop/modules/js/commonApps/showUrl.js', {
                            action: 'launch',
                            url: '/Liquidacion/ReporteLibroLiquidaciones/verreporte'+params,
                            width: 900,
                            height: 500,
                            title: 'Reportes de Liquidaciones'
                        });
                    }
                }
            ]
        };
    }

});

new Apps.<?=$this->name?>();
