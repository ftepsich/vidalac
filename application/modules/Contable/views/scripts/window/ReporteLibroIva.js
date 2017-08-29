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
                                ['Clasico', '1'],
                                ['Con Jurisdiccion', '2'],
                                ['Con Provincia de la Direccion', '16'],
                                ['Con Percepciones y Retenciones', '5'],
                                ['Ret. y Percep. Ingresos Brutos sufridas', '13'],                                
                                ['Exportador solo Percepciones y Retenciones', '14'],
                                ['Exportador Libro IVA detallado', '15'],                                
                                ['Con Plan de Cuenta', '12'],
                                ['Exportador AFIP (pre 3685)', '3'],
                                ['Exportador AFIP res 3685 Cabecera', '6'],
                                ['Exportador AFIP res 3685 Alicuotas', '7'],
                               // ['Test Exp. AFIP res 3685 Cabecera (separado por coma)', '8'],
                               // ['Test Exportador AFIP (pre 3685) -- para control con valores negativos', '4']
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