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
                    store: new Ext.data.ArrayStore({
                        fields: ['desc', 'id'],
                        data : [
                               
                                ['General', '1'],
                                ['Detallado', '2'],
                                ['Por Grupo', '3'],
                      
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
                   xtype: 'xdatefield',
                   fieldLabel: 'Fecha Desde',
                   format: 'd/m/Y',
                   dateFormat:'Y-m-d',
                   name: 'fechaDesde',
                   forceSelection: true,
                   anchor: '45%'
                },
                {
                   xtype: 'xdatefield',
                   fieldLabel : 'Fecha Hasta',
                   format: 'd/m/Y',
                   dateFormat:'Y-m-d',
                   name: 'fechaHasta',
                   forceSelection: true,
                   anchor: '45%'
                },
                {
                    xtype: 'xcombo',
                    fieldLabel: 'Libro IVA Desde',                 
                    anchor: '45%',
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
                    anchor: '45%',
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
                    typeAhead: true,
                    xtype: 'xcombo',
                    fieldLabel: 'Grupo',                 
                    anchor: '96%',
                    displayField: 'Descripcion',
                    name: 'plandecuentaGrupo',
                    valueField: 'Id',
                    selectOnFocus: true,
                    forceSelection: true,
                    forceReload: true,
                    hiddenName: "plandecuentaGrupo",
                    loadingText: "Cargando...",
                    msgTarget: 'under',
                    triggerAction: 'all',
                    store: new Ext.data.JsonStore({
                        id: 0,
                        url:"datagateway\/combolist\/model\/PlanesDeCuentasGrupos/m\/Contable\/search\/Descripcion\/sort\/Id\/dir\/desc",
                        storeId: "PlanDeCuentaGrupoStore"
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
                        if (values.modelo) {
                            params += '/modelo/'+values.modelo;
                        } else {
                            Ext.Msg.alert('Atencion', 'Debe seleccionar un modelo de reporte');
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
                            title: 'Reporte Plan de Cuenta Mercadería'
                        });
                    }
                }
            ]
        };
    }
	
});

new Apps.<?=$this->name?>();
