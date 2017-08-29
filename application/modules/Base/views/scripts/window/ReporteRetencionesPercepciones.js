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
            url : '/Base/ReporteRetencionesPercepciones/verreporte',
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
                                ['SIAGER Retenciones', '1'],
                                ['SIAGER Percepciones', '2'],
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
                    hiddenName: "periodo",
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
                    fieldLabel: "Periodo",
                    name: "periodo",
                    displayFieldTpl: "{Descripcion}",
                    forceSelection: true,
                    triggerAction: 'all',
                    selectOnFocus: true
                },
            ],
            buttons:[
                {
                    text:  'Ver Reporte',
                    handler: function () {
                       values = this.ownerCt.ownerCt.getForm().getValues();
                       var  params = '';

                       if (values.modelo) {
                           params += '/modelo/'+values.modelo;
                       } else {
                           Ext.Msg.alert('Atencion', 'Debe seleccionar un modelo de reporte');
                           return;
                       }

                       if (values.periodo) {
                           params += '/libro/'+values.periodo;
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
                           url: '/Base/ReportePercepcionesRetenciones/verreporte'+params,
                           width: 900,
                           height: 500,
                           title: 'Reporte Retenciones y Retenciones'
                       });
                    }
                }
            ]
        };
    }

});

new Apps.<?=$this->name?>();
