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
            url : '/Facturacion/ReporteIngresoDeComprobantesAlContado/verreporte',
            layout: 'form',
            border: false,
            bodyStyle: 'padding:10px',
            defaults: {
                border: false
            },
            items: [
                {
                    xtype:          'compositefield',
                    fieldLabel:     'Fecha',
                    items: [
                        {xtype: 'displayfield', value: 'Desde:'},{name : 'fechaDesde', xtype: 'xdatefield',format: 'd/m/Y',  dateFormat:'Y-m-d'},
                        {xtype: 'displayfield', value: 'Hasta:'},{name : 'fechaHasta', xtype: 'xdatefield',format: 'd/m/Y',  dateFormat:'Y-m-d'},
                    ]
                },    
                {
                    xtype:          'compositefield',
                    fieldLabel:     'Libro Iva',
                    items: [
                        {xtype: 'displayfield', value: 'Desde:'},{displayField: 'Descripcion',name : 'libroivadesde',valueField: 'Id', xtype: 'xcombo', selectOnFocus: true, forceSelection: true, forceReload: true,
                        hiddenName: "libroIvaDesde",loadingText: "Cargando...", msgTarget: 'under',width: 116,
                        triggerAction: 'all', store: new Ext.data.JsonStore({
                            id: 0,
                            url:"datagateway\/combolist\/model\/LibrosIVA/m\/Contable\/search\/Descripcion\/sort\/Id\/dir\/desc",
                            storeId: "LibroIVAStore"
                        })},
                        {xtype: 'displayfield', value: 'Hasta:'},{ displayField: 'Descripcion',name:'libroivahasta', valueField: 'Id',xtype: 'xcombo',selectOnFocus: true, forceSelection: true, forceReload: true,
                        hiddenName: "libroIvaHasta",loadingText: "Cargando...", msgTarget: 'under',width: 116,

                        triggerAction: 'all',store: new Ext.data.JsonStore({
                            id: 0,
                            url:"datagateway\/combolist\/model\/LibrosIVA/m\/Contable\/search\/Descripcion\/sort\/Id\/dir\/desc",
                            storeId: "LibroIVAStore"
                        })},
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
            ],
            buttons:[
                {
                    text:  'Ver Reporte',
                    handler: function () {
                        values = this.ownerCt.ownerCt.getForm().getValues();
                        var  params = '';

                        values.fechaDesde    = values.fechaDesde.replace(/undefined/gi,"");
                        values.fechaHasta    = values.fechaHasta.replace(/undefined/gi,"");
                        values.libroIvaDesde = values.libroIvaDesde.replace(/undefined/gi,"");
                        values.libroIvaHasta = values.libroIvaHasta.replace(/undefined/gi,"");
                        values.idProveedor   = values.idProveedor.replace(/undefined/gi,"");

                        if(values.fechaDesde == '' && values.fechaHasta == '' && values.libroIvaDesde == '' && values.libroIvaHasta == '') {
                          Ext.Msg.alert('Atencion', 'Debe seleccionar un rango de fechas Desde/Hasta o un periodo Libro IVA Desde/Hasta');
                          return;
                        }
                  
                        if((values.fechaDesde == '' && values.fechaHasta != '') || (values.fechaDesde != '' && values.fechaHasta == '')){
                          Ext.Msg.alert('Atencion', 'Debe seleccionar un rango de fechas Desde/Hasta');
                          return;
                        }
                
                        if((values.libroIvaDesde == '' && values.libroIvaHasta != '') || (values.libroIvaDesde != '' && values.libroIvaHasta == '')){
                          Ext.Msg.alert('Atencion', 'Debe seleccionar un periodo Libro IVA Desde/Hasta');
                          return;
                        }
                    
 
                        if (values.fechaDesde != '' && values.fechaDesde) {
                            params += '/fechadesde/'+values.fechaDesde;
                        }
 
                        if (values.fechaHasta != '' && values.fechaHasta) {
                            params += '/fechahasta/'+values.fechaHasta;
                        }

                        if (values.libroIvaDesde != '' && values.libroIvaDesde) {
                            params += '/libroivadesde/'+values.libroIvaDesde;
                        }
 
                        if (values.libroIvaHasta != '' && values.libroIvaHasta) {
                            params += '/libroivahasta/'+values.libroIvaHasta;
                        }

                        if (values.idProveedor !== '' && values.idProveedor) {
                           params += '/proveedor/'+values.idProveedor;
                        } else {
                           params += '/proveedor/0';
                        }

                        app.publish('/desktop/modules/js/commonApps/showUrl.js', {
                            action: 'launch',
                            url: '/Facturacion/ReporteIngresoDeComprobantesAlContado/verreporte'+params,
                            width: 900,
                            height: 500,
                            title: 'Reporte Ingreso de Comprobantes al Contado'
                        });
                    }
                }
            ]
        };
    }
	
});

new Apps.<?=$this->name?>();
