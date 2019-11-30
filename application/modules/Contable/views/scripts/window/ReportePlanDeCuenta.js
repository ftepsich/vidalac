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
            url : '/Contable/ReportePlanDeCuenta/verreporte',
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
                               
                                ['General por Cuenta', '1'],
                                ['Detallado por Cuenta', '2'],
                                ['General por Grupo', '3'],
                                ['Detallado por Grupo', '4'],
                      
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
                    typeAhead: true,
                    xtype: 'xcombo',
                    fieldLabel: 'Cuenta',                 
                    anchor: '96%',
                    displayField: 'Descripcion',
                    name: 'cuenta',
                    valueField: 'Id',
                    selectOnFocus: true,
                    forceSelection: true,
                    forceReload: true,
                    hiddenName: "cuenta",
                    loadingText: "Cargando...",
                    msgTarget: 'under',
                    triggerAction: 'all',
                    store: new Ext.data.JsonStore({
                        id: 0,
                        url:"datagateway\/combolist\/model\/PlanesDeCuentas/m\/Contable\/search\/Descripcion\/sort\/Descripcion\/dir\/asc",
                        storeId: "PlanDeCuentaStore"
                    }),
                editable:true,
                autocomplete:true

                },
                {
                    typeAhead: true,
                    xtype: 'xcombo',
                    fieldLabel: 'Grupo',                 
                    anchor: '96%',
                    displayField: 'Descripcion',
                    name: 'grupo',
                    valueField: 'Id',
                    selectOnFocus: true,
                    forceSelection: true,
                    forceReload: true,
                    hiddenName: "grupo",
                    loadingText: "Cargando...",
                    msgTarget: 'under',
                    triggerAction: 'all',
                    store: new Ext.data.JsonStore({
                        id: 0,
                        url:"datagateway\/combolist\/model\/PlanesDeCuentasGrupos/m\/Contable\/search\/Descripcion\/sort\/Descripcion\/dir\/asc",
                        storeId: "PlanDeCuentaGrupoStore"
                    })
                },
                {
                    xtype: 'checkbox',
                    fieldLabel: 'Incluir Período Iva 00',
                    name: 'periodoiva00'
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

                        values.modelo        = values.modelo.replace(/undefined/gi,"");
                        values.fechaDesde    = values.fechaDesde.replace(/undefined/gi,"");
                        values.fechaHasta    = values.fechaHasta.replace(/undefined/gi,"");
                        values.libroIvaDesde = values.libroIvaDesde.replace(/undefined/gi,"");
                        values.libroIvaHasta = values.libroIvaHasta.replace(/undefined/gi,"");
                        values.cuenta        = values.cuenta.replace(/undefined/gi,"");
                        values.grupo         = values.grupo.replace(/undefined/gi,"");

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
                            Ext.Msg.alert('Atencion', 'Debe seleccionar un Modelo de reporte');
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

                        if (values.cuenta) {
                            params += '/cuenta/'+values.cuenta;
                        } else {
                            if (values.modelo == 2) {
                               Ext.Msg.alert('Atencion', 'Debe seleccionar una Cuenta');
                               return;
                            }
                        }

                        if (values.grupo) {
                            params += '/grupo/'+values.grupo;
                        } else {
                            if (values.modelo == 3 || values.modelo == 4) {
                               Ext.Msg.alert('Atencion', 'Debe seleccionar una Grupo');
                               return;
                            }

                        }
 
                        if (values.formato) {
                            params += '/formato/'+values.formato;
                        } else {
                            Ext.Msg.alert('Atencion', 'Debe seleccionar un Formato de Salida');
                            return;
                        }
                        
                        if (values.periodoiva00) {
                            params += '/periodoiva00/1';
                        }

                        app.publish('/desktop/modules/js/commonApps/showUrl.js', {
                            action: 'launch',
                            url: '/Contable/ReportePlanDeCuenta/verreporte'+params,
                            width: 900,
                            height: 500,
                            title: 'Reporte Plan de Cuenta'
                        });
                    }
                }
            ]
        };
    }
	
});

new Apps.<?=$this->name?>();
