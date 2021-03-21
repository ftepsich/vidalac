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
            url : '/Contable/ReporteRetenciones/verreporte',
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
                                ['Rep. de ret. de ganancias realizadas', '1'],
                                ['Rep. retenciones IVA', '2'],
                                ['Rep. retenciones SUSS', '3'],
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
                    xtype:          'compositefield',
                    fieldLabel:     'Fecha',
                    items: [
                        {xtype: 'displayfield', value: 'Desde:'},{name : 'fechaDesde', xtype: 'xdatefield',format: 'd/m/Y',  dateFormat:'Y-m-d'},
                        {xtype: 'displayfield', value: 'Hasta:'},{name : 'fechaHasta', xtype: 'xdatefield',format: 'd/m/Y',  dateFormat:'Y-m-d'},
                    ]
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

                        values.fechaDesde    = values.fechaDesde.replace(/undefined/gi,"");
                        values.fechaHasta    = values.fechaHasta.replace(/undefined/gi,"");
                        values.idProveedor   = values.idProveedor.replace(/undefined/gi,"");

                        if(values.fechaDesde == '' && values.fechaHasta == '' && values.libroIvaDesde == '' && values.libroIvaHasta == '') {
                          Ext.Msg.alert('Atencion', 'Debe seleccionar un rango de fechas Desde/Hasta o un periodo Libro IVA Desde/Hasta');
                          return;
                        }
                        if (values.fechaDesde != '' && values.fechaDesde) {
                            params += '/fechadesde/'+values.fechaDesde;
                        }
 
                        if (values.fechaHasta != '' && values.fechaHasta) {
                            params += '/fechahasta/'+values.fechaHasta;
                        }


                        if (values.idProveedor !== '' && values.idProveedor) {
                           params += '/proveedor/'+values.idProveedor;
                        } else {
                           params += '/proveedor/0';
                        }

                        app.publish('/desktop/modules/js/commonApps/showUrl.js', {
                            action: 'launch',
                            url: '/Contable/ReporteRetenciones/verreporte'+params,
                            width: 900,
                            height: 500,
                            title: 'Reporte Retenciones'
                        });
                    }
                }
            ]
        };
    }
	
});

new Apps.<?=$this->name?>();
