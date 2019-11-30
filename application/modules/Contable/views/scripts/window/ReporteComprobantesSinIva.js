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
            url : '/Contable/ReporteComprobantesSinIva/verreporte',
            layout: 'form',
            border: false,
            bodyStyle: 'padding:10px',
            defaults: {
                border: false
            },
            items: [
                {
                    xtype:          'compositefield',
                    fieldLabel:     'Periodo Imputación',
                    items: [
                        {xtype: 'displayfield', value: 'Desde:'},{displayField: 'Descripcion',name : 'periodoimputaciondesde',valueField: 'Id', xtype: 'xcombo', selectOnFocus: true, forceSelection: true, forceReload: true,
                        hiddenName: "periodoImputacionSinIVADesde",loadingText: "Cargando...", msgTarget: 'under',width: 116,
                        triggerAction: 'all', store: new Ext.data.JsonStore({
                            id: 0,
                            url:"datagateway\/combolist\/model\/PeriodosImputacionSinIVA/m\/Contable\/search\/Descripcion\/sort\/Id\/dir\/desc",
                            storeId: "PeriodosImputacionSinIVAStore"
                        })},
                        {xtype: 'displayfield', value: 'Hasta:'},{ displayField: 'Descripcion',name:'periodoimputacionhasta', valueField: 'Id',xtype: 'xcombo',selectOnFocus: true, forceSelection: true, forceReload: true,
                        hiddenName: "periodoImputacionSinIVAHasta",loadingText: "Cargando...", msgTarget: 'under',width: 116,

                        triggerAction: 'all',store: new Ext.data.JsonStore({
                            id: 0,
                            url:"datagateway\/combolist\/model\/PeriodosImputacionSinIVA/m\/Contable\/search\/Descripcion\/sort\/Id\/dir\/desc",
                            storeId: "PeriodosImputacionSinIVAStore"
                        })},
                    ]
                }, 
                {
                    xtype: 'checkbox',
                    fieldLabel: 'Incluir Períodos 00',
                    name: 'periodoimputacion00'
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

                        values.periodoImputacionSinIVADesde = values.periodoImputacionSinIVADesde.replace(/undefined/gi,"");
                        values.periodoImputacionSinIVAHasta = values.periodoImputacionSinIVAHasta.replace(/undefined/gi,"");

                        if(values.periodoImputacionSinIVADesde == '' || values.periodoImputacionSinIVAHasta == ''){
                          Ext.Msg.alert('Atencion', 'Debe seleccionar un periodo de imputación Desde/Hasta');
                          return;
                        }

                        if (values.periodoImputacionSinIVADesde != '' && values.periodoImputacionSinIVADesde) {
                            params += '/periodoimputaciondesde/'+values.periodoImputacionSinIVADesde;
                        }
 
                        if (values.periodoImputacionSinIVAHasta != '' && values.periodoImputacionSinIVAHasta) {
                            params += '/periodoimputacionhasta/'+values.periodoImputacionSinIVAHasta;
                        }

                        if (values.periodoimputacion00) {
                            params += '/periodoimputacion00/1';
                        }

                        if (values.formato) {
                            params += '/formato/'+values.formato;
                        } else {
                            Ext.Msg.alert('Atencion', 'Debe seleccionar un Formato de Salida');
                            return;
                        }

                        app.publish('/desktop/modules/js/commonApps/showUrl.js', {
                            action: 'launch',
                            url: '/Contable/ReporteComprobantesSinIva/verreporte'+params,
                            width: 900,
                            height: 500,
                            title: 'Reporte Comprobante Sin IVA'
                        });
                    }
                }
            ]
        };
    }
	
});

new Apps.<?=$this->name?>();
