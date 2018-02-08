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
            anchor: '50%',
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
            anchor: '50%',
            mode: 'local',
            forceSelection: true,
            triggerAction: 'all',
            selectOnFocus:true,
            store: new Ext.data.ArrayStore({
                fields: ['desc', 'id'],
                data : [
                        ['General', '1'],
                        ['Por Deposito', '2'],
                        ['Detallando MMI', '3'],
                        ['Por Grupo de Articulos', '4'],
                        ['Valorizado', '5'],
                        ['Por Cantidad', '6']
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
            width: 800,
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
            url : '/Almacenes/ReporteDeStock/verreporte',
            layout: 'form',
            border: false,
            bodyStyle: 'padding:10px',
            defaults: {
                border: false
            },
            items: [
                // this.tipoPersona,
                this.modeloReporte,
                this.cabecera,
                {
                    ref: '../articulo',
                    "xtype":"xcombo",
                    //"width":320,
                    "anchor": '90%',
                    "displayField":"Descripcion",
                    "autoLoad":false,
                    "selectOnFocus":true,
                    "forceSelection":true,
                    "forceReload":true,
                    "hiddenName":"Articulo",
                    "loadingText":"Cargando...",
                    "lazyRender":true,
                    "store":new Ext.data.JsonStore({"id":0,"url":"datagateway\/combolist\/model\/ArticulosGenericos\/m\/Base\/search\/Descripcion","storeId":"ArticuloStore"}),
                    "typeAhead":true,
                    "valueField":"Id",
                    "pageSize":20,
                    "editable":true,
                    "autocomplete":true,
                    "allowNegative":false,
                    "fieldLabel":"Articulos",
                    "name":"Articulo",
                    "displayFieldTpl":"{Codigo} {Descripcion}",
                    "tpl":"<tpl for=\".\"><div class=x-combo-list-item><h3>[{Codigo}] {Descripcion}<\/h3><\/div><\/tpl>"
                },
                {
                    ref:            '../deposito',
                    xtype:"xcombo",
                    //width:320,
                    anchor: '90%',
                    displayField:"Descripcion",
                    autoLoad:false,
                    selectOnFocus:true,
                    forceSelection:true,
                    forceReload:true,
                    hiddenName:"Deposito",
                    loadingText:"Cargando...",
                    lazyRender:true,
                    store:new Ext.data.JsonStore({"id":0,"url":"datagateway\/combolist\/model\/DepositosPropios\/m\/Base\/TipoDeDireccion\/2","storeId":"DepositosStore"}),
                    typeAhead:true,
                    valueField:"Id",
                    pageSize:20,
                    editable:true,
                    autocomplete:true,
                    allowNegative:false,
                    fieldLabel:"Deposito.",
                    name:"Deposito",
                    displayFieldTpl:"{TipoDeDireccion_cdisplay}: {Localidad_cdisplay} - {Direccion}",
                    tpl:"<tpl for=\".\"><div class=x-combo-list-item><h3>{TipoDeDireccion_cdisplay}: {Localidad_cdisplay}<\/h3>{Direccion}<\/div><\/tpl>"
                    //link:"\/Base\/administrarClientes",
                    //descriptionPanel:{"tpl":"\n        <h1>Informacion<\/h1>\n        <b>Cuit:<\/b> {Cuit}<br>\n        <b>Inscripcion IVA:<\/b> {ModalidadIva_cdisplay}<br>\n        <b>Inscripcion Gan.:<\/b> {ModalidadGanancia_cdisplay}<br>\n        <b>Localidad:<\/b> {Localidad_cdisplay}"}
                },
                {
                    xtype: 'radiogroup',
                    fieldLabel: 'Orden',
                    width: 150,
                    items: [
                        { boxLabel: 'Nombre', name: 'orden', inputValue: 1, checked: true },
                        { boxLabel: 'Codigo', name: 'orden', inputValue: 2 }
                    ]
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
                        /*
                        if (values.fecha != 'undefined') {
                            params += '/fecha/'+values.fecha;
                        }

                        if (values.Persona != '') {
                            params += '/persona/'+values.Persona;
                        }
                        */
                        if (values.Articulo != '') {
                            params += '/articulo/'+values.Articulo;
                        }

                        if (values.Deposito != '') {
                            params += '/deposito/'+values.Deposito;
                        }

                        if (values.modeloReporte) {
                            params += '/modelo/'+values.modeloReporte;
                        }

                        if (values.cabecera != '') {
                            params += '/cabecera/'+values.cabecera;
                        }

                        if (values.orden != '') {
                            params += '/orden/'+values.orden;
                        }

                        if (values.formato != '') {
                            params += '/formato/'+values.formato;
                        }

                        app.publish('/desktop/modules/js/commonApps/showUrl.js', {
                            action: 'launch',
                            url: '/Almacenes/ReporteDeStock/verreporte'+params,
                            width: 900,
                            height: 500,
                            title: 'Reporte de Stock'
                        });
                    }
                }
            ]
        };
    }

});

new Apps.<?=$this->name?>();

