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

        this.cabecera = new Ext.ux.form.ComboBox({
            displayField:'desc',
            valueField: 'id',
            typeAhead: true,
            fieldLabel: 'Encabezado',
            name: 'cabecera',
            //anchor : '100%',
            value:1,
            mode: 'local',
            forceSelection: true,
            triggerAction: 'all',
            selectOnFocus: true,
            store: new Ext.data.ArrayStore({
                fields: ['desc', 'id'],
                data : [
                    ['Interno (simple)', '1'],
                    ['Completo (detallado)', '2']
                ]
            })
        });

        this.orden = new Ext.ux.form.ComboBox({
            displayField:'desc',
            valueField: 'id',
            typeAhead: true,
            fieldLabel: 'Ordenado por',
            name: 'orden',
            //anchor : '100%',
            value:3,
            width:320,
            mode: 'local',
            forceSelection: true,
            triggerAction: 'all',
            selectOnFocus: true,
            store: new Ext.data.ArrayStore({
                fields: ['desc', 'id'],
                data : [
                    ['Razon Social y Nro Comprobante', '1'],
                    ['Razon Social y Fecha', '2'],
                    ['Fecha, Razon Social y Nro Comprobante', '3'],
                    ['Fecha y Nro Comprobante', '4']
                ]
            })
        });

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
        return {
            xtype: 'form',
            url : '/Facturacion/ReporteComprobantesCobros/verreporte',
            layout: 'form',
            border: false,
            bodyStyle: 'padding:10px',
            defaults: {
                border: false
            },
            items: [
                this.cabecera,
                {
                   xtype: 'xdatefield',
                   ref: '../desde',
                   format: 'd/m/Y',
                   dateFormat:'Y-m-d',
                   name: 'desde',
                   fieldLabel : 'Desde'
                },
                {
                   xtype: 'xdatefield',
                   ref: '../hasta',
                   format: 'd/m/Y',
                   dateFormat:'Y-m-d',
                   name: 'hasta',
                   fieldLabel : 'Hasta'
                },
                {
                    ref: '../persona',
                    "xtype":"xcombo",
                    "width":320,
                    "displayField":"RazonSocial",
                    "autoLoad":false,
                    "selectOnFocus":true,
                    "forceSelection":true,
                    "forceReload":true,
                    "hiddenName":"Persona",
                    "loadingText":"Cargando...",
                    "lazyRender":true,
                    "store":new Ext.data.JsonStore({"id":0,"url":"datagateway\/combolist\/model\/Personas\/m\/Base\/search\/RazonSocial","storeId":"PersonaStore"}),
                    "typeAhead":true,
                    "valueField":"Id",
                    "pageSize":20,
                    "editable":true,
                    "autocomplete":true,
                    "allowNegative":false,
                    "fieldLabel":"Cliente / Prov.",
                    "name":"Persona",
                    "displayFieldTpl":"{RazonSocial}",
                    "tpl":"<tpl for=\".\"><div class=x-combo-list-item><h3>{RazonSocial}<\/h3>{Denominacion}<\/div><\/tpl>",
                    "link":"\/Base\/administrarClientes",
                    "descriptionPanel":{"tpl":"\n        <h1>Informacion<\/h1>\n        <b>Cuit:<\/b> {Cuit}<br>\n        <b>Inscripcion IVA:<\/b> {ModalidadIva_cdisplay}<br>\n        <b>Inscripcion Gan.:<\/b> {ModalidadGanancia_cdisplay}<br>\n        <b>Localidad:<\/b> {Localidad_cdisplay}"}
                },
                this.orden,
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

                        if (values.desde == 'undefined' || !values.hasta == 'undefined') {
                            Ext.Msg.alert('Atencion', 'Debe completar las fechas');
                            return;
                        } else {
                            params += '/desde/'+values.desde;
                            if (values.hasta != 'undefined') {
                                params += '/hasta/'+values.hasta;
                            }
                        }

                        if (values.Persona != '') {
                            params += '/persona/'+values.Persona;
                        }

                        if (values.cabecera != '') {
                            params += '/cabecera/'+values.cabecera;
                        }

                        if (values.formato != '') {
                            params += '/formato/'+values.formato;
                        }

                        if (values.orden != '') {
                            params += '/orden/'+values.orden;
                        }

                        app.publish('/desktop/modules/js/commonApps/showUrl.js', {
                            action: 'launch',
                            url: '/Facturacion/ReporteComprobantesCobros/verreporte'+params,
                            width: 900,
                            height: 500,
                            title: 'Reporte de Recibos'
                        });
                    }
                }
            ]
        };
    }
	
});

new Apps.<?=$this->name?>();
