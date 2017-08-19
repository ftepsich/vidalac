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
            url : '/Contable/ReportePercepcionesyRetenciones/verreporte',
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
                                ['Detallado', '1'],
                                ['Agrupado', '2']
                        ]
                    }),
                    value: 1,
                    alowBlank: false,
                    displayField:'desc',
                    valueField: 'id',
                    typeAhead: true,
                    fieldLabel: 'Reporte',
                    name: 'reporte',
                    mode: 'local',
                    forceSelection: true,
                    triggerAction: 'all',
                    selectOnFocus:true
                },
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
                {
                    fieldLabel: 'Libro IVA',
                    xtype: 'superboxselect',
                    anchor: '90%',
                    displayField: 'Descripcion',
                    name: 'libroiva',
                    valueField: 'Id',
                    allowBlank: true,
                    msgTarget: 'under',
                    triggerAction: 'all',
                    store: new Ext.data.JsonStore({
                        id: 0,
                        url: "datagateway/combolist/model/LibrosIVA/m/Contable",
                        storeId: "LibroIVAStore"
                    })
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

                        if (values.desde == 'undefined' || !values.hasta == 'undefined') {
                            Ext.Msg.alert('Atencion', 'Debe completar las fechas');
                            return;
                        } else {
                            params += '/desde/'+values.desde;
                            if (values.hasta != 'undefined') {
                                params += '/hasta/'+values.hasta;
                            }
                        }
                       
                        if (values.reporte != '') {
                            params += '/reporte/'+values.reporte;
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

                        if (values.libroiva != '') {
                            params += '/libroiva/';
                            params += (values.libroiva instanceof Array) ? values.libroiva.join(',') : values.libroiva;
                        }

                        app.publish('/desktop/modules/js/commonApps/showUrl.js', {
                            action: 'launch',
                            url: '/Contable/ReportePercepcionesyRetenciones/verreporte'+params,
                            width: 900,
                            height: 500,
                            title: 'Reporte Percepciones y Retenciones'
                        });
                    }
                }
            ]
        };
    }
	
});

new Apps.<?=$this->name?>();
