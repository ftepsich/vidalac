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
                    name: 'libroivadesde',
                    valueField: 'Id',
                    selectOnFocus: true,
                    forceSelection: true,
                    forceReload: true,
                    hiddenName: "libroIvaDesde",
                    loadingText: "Cargando...",
                    msgTarget: 'under',
                    triggerAction: 'all',
                    store: new Ext.data.JsonStore({
                        id: 0,
                        url:"datagateway\/combolist\/model\/LibrosIVA/m\/Contable\/search\/Descripcion\/sort\/Id\/dir\/desc",
                        storeId: "LibroIVAStore"
                    })
                },
                {
                    xtype: 'xcombo',
                    fieldLabel: 'Libro IVA Hasta',
                    anchor: '96%',
                    displayField: 'Descripcion',
                    name: 'libroivahasta',
                    valueField: 'Id',
                    selectOnFocus: true,
                    forceSelection: true,
                    forceReload: true,
                    hiddenName: "libroIvaHasta",
                    loadingText: "Cargando...",
                    msgTarget: 'under',
                    triggerAction: 'all',
                    store: new Ext.data.JsonStore({
                        id: 0,
                        url:"datagateway\/combolist\/model\/LibrosIVA/m\/Contable\/search\/Descripcion\/sort\/Id\/dir\/desc",
                        storeId: "LibroIVAStore"
                    })
                },
                {
                    fieldLabel: 'Proveedor',
                    ref: '../persona',
                    xtype:"xcombo",
                    anchor: '96%',
                    displayField: 'RazonSocial',
                    name: 'proveedor',
                    typeAhead:true,
                    valueField: 'Id',
                    allowBlank: true,
                    msgTarget: 'under',
                    triggerAction: 'all',
                    autoLoad:true,
                    selectOnFocus:true,
                    forceSelection:true,
                    forceReload:true,
                    hiddenName:"idProveedor",
                    loadingText:"Cargando...",
                    lazyRender:true,
                    store:new Ext.data.JsonStore({
                       id:0,
                       url:"datagateway\/combolist\/model\/Proveedores\/m\/Base\/search\/RazonSocial",
                       storeId:"ProveedoresStore"
                    }),
                pageSize:20,
                editable:true,
                autocomplete:true
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

                        if (values.libroIvaDesde != "undefined" && values.libroIvaDesde) {
                            params += '/libroivadesde/'+values.libroIvaDesde;
                        } else {
                            Ext.Msg.alert('Atencion', 'Debe seleccionar un periodo Libro IVA Desde');
                            return;
                        }
 
                        if (values.libroIvaHasta != "undefined" && values.libroIvaHasta) {
                            params += '/libroivahasta/'+values.libroIvaHasta;
                        } else {
                            Ext.Msg.alert('Atencion', 'Debe seleccionar un periodo Libro IVA Hasta');
                            return;
                        }

                        if (values.idProveedor !== "undefined" && values.idProveedor) {
                           params += '/proveedor/'+values.idProveedor;
                        } else {
                           params += '/proveedor/0';
                        }
 
                        if (values.formato) {
                            params += '/formato/'+values.formato;
                        } else {
                            Ext.Msg.alert('Atencion', 'Debe seleccionar un Formato de Salida');
                            return;
                        }

                        app.publish('/desktop/modules/js/commonApps/showUrl.js', {
                            action: 'launch',
                            url: '/Contable/ReportePlanDeCuentaMercaderia/verreporte'+params,
                            width: 900,
                            height: 500,
                            title: 'Reporte Plan de Cuenta Mercaderí­a'
                        });
                    }
                }
            ]
        };
    }
	
});

new Apps.<?=$this->name?>();
