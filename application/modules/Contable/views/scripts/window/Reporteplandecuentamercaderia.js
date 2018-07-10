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
            url : '/Contable/ReportePlanDeCuentaMercaderia/verreporte',
            layout: 'form',
            border: false,
            bodyStyle: 'padding:10px',
            defaults: {
                border: false
            },
            items: [

                {
                    xtype: 'xcombo',
                    fieldLabel: 'Libro IVA Desde',                 
                    anchor: '96%',
                    displayField: 'Descripcion',
                    name: 'libroiva',
                    valueField: 'Id',
                    selectOnFocus: true,
                    forceSelection: true,
                    forceReload: true,
                    hiddenName: "libroIva",
                    loadingText: "Cargando...",
                    msgTarget: 'under',
                    triggerAction: 'all',
                    store: new Ext.data.JsonStore({
                        id: 0,
                        url: "datagateway/combolist/model/LibrosIVA/m/Contable",
                        storeId: "LibroIVAStore"
                    })
                },
                {
                    xtype: 'xcombo',
                    fieldLabel: 'Libro IVA Hasta',
                    anchor: '96%',
                    displayField: 'Descripcion',
                    name: 'libroiva',
                    valueField: 'Id',
                    selectOnFocus: true,
                    forceSelection: true,
                    forceReload: true,
                    hiddenName: "libroIva",
                    loadingText: "Cargando...",
                    msgTarget: 'under',
                    triggerAction: 'all',
                    store: new Ext.data.JsonStore({
                        id: 0,
                        url: "datagateway/combolist/model/LibrosIVA/m/Contable",
                        storeId: "LibroIVAStore"
                    })
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
                    "fieldLabel":"Proveedor",
                    "name":"Persona",
                    "displayFieldTpl":"{RazonSocial}",
                    "tpl":"<tpl for=\".\"><div class=x-combo-list-item><h3>{RazonSocial}<\/h3>{Denominacion}<\/div><\/tpl>",
                    "link":"\/Base\/administrarClientes",
                    "descriptionPanel":{"tpl":"\n        <h1>Informacion<\/h1>\n        <b>Cuit:<\/b> {Cuit}<br>\n        <b>Inscripcion IVA:<\/b> {ModalidadIva_cdisplay}<br>\n        <b>Inscripcion Gan.:<\/b> {ModalidadGanancia_cdisplay}<br>\n        <b>Localidad:<\/b> {Localidad_cdisplay}"}
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
                            url: '/Contable/ReportePlanDeCuentaMercaderia/verreporte'+params,
                            width: 900,
                            height: 500,
                            title: 'Reporte Plan de Cuenta Mercadería'
                        });
                    }
                }
            ]
        };
    }
	
});

new Apps.<?=$this->name?>();
