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
            width: 450,
            height:200,
            items: [
                this.renderWindowContent()
            ]
        };
        return app.desktop.createWindow(defaultWinCfg);
    },

    renderWindowContent: function ()
    {
        return {
            xtype: 'form',
            url : '/Contable/ReporteLibroIva/verreporte',
            layout: 'form',
            border: false,
            bodyStyle: 'padding:10px',
            defaults: {
                border: false
            },
            items: [
                {
                    xtype: 'xcombo',
                    store: new Ext.data.ArrayStore({
                        fields: ['desc', 'id'],
                        data : [
                                ['Compra', '1'],
                                ['Venta', '2']
                        ]
                    }),
                    value: 2,
                    anchor: '95%',
                    alowBlank: false,
                    displayField:'desc',
                    valueField: 'id',
                    typeAhead: true,
                    fieldLabel: 'Tipo',
                    name: 'tipo',
                    mode: 'local',
                    forceSelection: true,
                    triggerAction: 'all',
                    selectOnFocus:true
                },
                {
                    xtype: 'xcombo',
                    store: new Ext.data.ArrayStore({
                        fields: ['desc', 'id'],
                        data : [
                                ['Clásico', '1'],
                                ['Con Jurisdicción', '2'],
                                ['Con Provincia de la Dirección', '14'],
                                ['Con Percepciones y Retenciones', '5'],
                                ['Ret. y Percep. Ingresos Brutos Sufridas', '11'],                                
                                ['Exportador solo Percepciones y Retenciones', '12'],
                                ['Exportador Libro IVA Detallado', '13'],                                
                                ['Con Plan de Cuenta', '10'],
                                ['Exportador AFIP (pre 3685)', '3'],
                                ['Exportador AFIP res 3685 Cabecera', '6'],
                                ['Exportador AFIP res 3685 Alicuotas', '7'],
                                //['Test Exp. AFIP res 3685 Cabecera (separado por coma)', '8'],
                                //['Test Exp. AFIP res 3685 Detalle  (separado por coma)', '9'],
                                //['Test Exportador AFIP (pre 3685) -- para control con valores negativos', '4']
                        ]
                    }),
                    value: 1,
                    anchor: '95%',
                    alowBlank: false,
                    displayField:'desc',
                    valueField: 'id',
                    typeAhead: true,
                    fieldLabel: 'Modelo',
                    name: 'modelo',
                    mode: 'local',
                    forceSelection: true,
                    triggerAction: 'all',
                    selectOnFocus:true
                },
                {
                    xtype: 'xcombo',
                    displayField: 'Descripcion',
                    autoLoad: true,
                    anchor: '95%',
                    selectOnFocus: true,
                    forceSelection: true,
                    forceReload: true,
                    hiddenName: "libroIva",
                    loadingText: "Cargando...",
                    lazyRender: true,
                    store: new Ext.data.JsonStore({ "id":0,
                                                    "url":"datagateway\/combolist\/model\/LibrosIVA/m\/Contable\/search\/Descripcion\/sort\/Id\/dir\/desc",
                                                    "storeId":"BancoSucursalStore"}),
                    typeAhead: true,
                    valueField: "Id",
                    pageSize: 20,
                    editable: true,
                    autocomplete: false,
                    allowBlank: false,
                    allowNegative: false,
                    fieldLabel: "Libro",
                    name: "libroIva",
                    displayFieldTpl: "{Descripcion}",
                    forceSelection: true,
                    triggerAction: 'all',
                    selectOnFocus: true
                },
                {
                    xtype: 'radiogroup',
                    fieldLabel: 'Formato',
                    width: 150,
                    items: [
                        { boxLabel: 'PDF', name: 'formato', inputValue: 'pdf', checked: true },
                        { boxLabel: 'Excel', name: 'formato', inputValue: 'xls' }
                    ]
                }
            ],
            buttons:[
                {
                    text:  'Ver Reporte',
                    handler: function () {
                       values = this.ownerCt.ownerCt.getForm().getValues();
                       var  params = '';

                       if (values.tipo) {
                           params += '/tipo/'+values.tipo;
                       } else {
                           Ext.Msg.alert('Atencion', 'Debe seleccionar un Tipo de Libro');
                           return;
                       }

                       if (values.modelo) {
                           params += '/modelo/'+values.modelo;
                       } else {
                           Ext.Msg.alert('Atencion', 'Debe seleccionar un modelo de reporte');
                           return;
                       }

                       if (values.libroIva) {
                           params += '/libro/'+values.libroIva;
                       } else {
                           Ext.Msg.alert('Atencion', 'Debe seleccionar un Periodo');
                           return;
                       }

                       if (values.formato) {
                           params += '/formato/'+values.formato;
                       } else {
                           Ext.Msg.alert('Atencion', 'Debe seleccionar un formato de salida');
                           return;
                       }

                       app.publish('/desktop/modules/js/commonApps/showUrl.js', {
                           action: 'launch',
                           url: '/Contable/ReporteLibroIva/verreporte'+params,
                           width: 900,
                           height: 500,
                           title: 'Libro Iva'
                       });
                    }
                }
            ]
        };
    }

});

new Apps.<?=$this->name?>();