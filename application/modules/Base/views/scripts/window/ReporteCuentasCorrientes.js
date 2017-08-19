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

        this.tipoPersona = new Ext.ux.form.ComboBox({
            displayField:'desc',
            valueField: 'id',
            typeAhead: true,
            fieldLabel: 'Tipo',
            name: 'tipoPersona',
            //anchor : '100%',
            value:1,
            mode: 'local',
            forceSelection: true,
            triggerAction: 'all',
            selectOnFocus: true,
            store: new Ext.data.ArrayStore({
                fields: ['desc', 'id'],
                data : [
                    ['Clientes', '1'],
                    ['Proveedores', '2']
                ]
            })
        });

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
                    
        this.modeloReporte = new Ext.ux.form.ComboBox({
            value: 1,
            alowBlank: false,
            displayField:'desc',
            valueField: 'id',
            typeAhead: true,
            fieldLabel: 'Modelo',
            name: 'modeloReporte',
            mode: 'local',
            forceSelection: true,
            triggerAction: 'all',
            selectOnFocus:true,
            store: new Ext.data.ArrayStore({
                fields: ['desc', 'id'],
                data : [
                        ['Reporte de Saldo', '1'],
                        ['Reporte completo con cheques pendientes', '2'],
                        ['Reporte de composición de saldos', '3']
                ]
            })
        });

        this.ocultarCeros = new Ext.ux.form.ComboBox({
            value: 1,
            alowBlank: false,
            displayField:'desc',
            valueField: 'id',
            typeAhead: true,
            fieldLabel: 'Ocultar Ceros',
            name: 'ocultarCeros',
            mode: 'local',
            forceSelection: true,
            triggerAction: 'all',
            selectOnFocus:true,
            store: new Ext.data.ArrayStore({
                fields: ['desc', 'id'],
                data : [
                        ['Si', '1'],
                        ['No', '2']
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
            width: 500,
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
            url : '/Base/ReporteCuentasCorrientes/verreporte',
            layout: 'form',
            border: false,
            bodyStyle: 'padding:10px',
            defaults: {
                border: false
            },
            items: [
                this.tipoPersona,
                this.modeloReporte,
                this.cabecera,           

                {
                   xtype: 'xdatefield',
                   ref: '../fecha',
                   format: 'd/m/Y',
                   dateFormat:'Y-m-d',
                   name: 'fecha',
                   fieldLabel : 'Fecha'
                },
                {
                    ref: '../persona',
                    "xtype":"xcombo",
                    "width":320,
                    "displayField":"RazonSocial",
                    "autoLoad":true,
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
                this.ocultarCeros,               
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
                            Ext.Msg.alert('Atencion', 'Debe completar la fecha');
                            return;
                        } else {
                            if (values.fecha != 'undefined') {
                                params += '/fecha/'+values.fecha;
                            }
                        }
                        
                        if (values.tipoPersona) {
                            params += '/tipo/'+values.tipoPersona;
                        } else {
                            Ext.Msg.alert('Atencion', 'Debe seleccionar si ve los Clientes o Proveedores');
                            return;
                        }
                        
                        if (values.Persona != '') {
                            params += '/persona/'+values.Persona;
                        }

                        if (values.modeloReporte) {
                            params += '/modelo/'+values.modeloReporte;
                        }

                        if (values.cabecera != '') {
                            params += '/cabecera/'+values.cabecera;
                        }
                        if (values.ocultarCeros != '') {
                            params += '/ocultarCeros/'+values.ocultarCeros;
                        }

                        if (values.formato != '') {
                            params += '/formato/'+values.formato;
                        }

                        app.publish('/desktop/modules/js/commonApps/showUrl.js', {
                            action: 'launch',
                            url: '/Base/ReporteCuentasCorrientes/verreporte'+params,
                            width: 900,
                            height: 500,
                            title: 'Reporte de Cuentas Corrientes'
                        });
                    }
                }
            ]
        };
    }
	
});

new Apps.<?=$this->name?>();
