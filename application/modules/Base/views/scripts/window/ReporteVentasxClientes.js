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
            height:300,
            items: [
                this.renderWindowContent()
            ]
        };
        return app.desktop.createWindow(defaultWinCfg);
    },
    
    renderWindowContent: function() {
            
        mdate = new Date();
        return {
            xtype: 'form',
            url : '/Base/ReporteVentasxClientes/verreporte',
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
                    fieldLabel: 'Clientes',
                    xtype: 'superboxselect',
                    anchor: '90%',
                    displayField: 'RazonSocial',
                    name: 'cliente',
                    valueField: 'Id',
                    allowBlank: true,
                    msgTarget: 'under',
                    triggerAction: 'all',
                    store: new Ext.data.JsonStore({
                        id: 0,
                        url: "datagateway/combolist/model/Clientes/m/Base/fetch/TieneComprobantes",
                        storeId: "ClientesStore"
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
                    //die(values)
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
                        if (values.cliente != '')      params += '/cliente/'+values.cliente;
                        if (values.cabecera != '')      params += '/cabecera/'+values.cabecera;
                        if (values.formato != '') {     params += '/formato/'+values.formato;
                        }
                      
                       app.publish('/desktop/modules/js/commonApps/showUrl.js', {
                           action: 'launch',
                           url: '/Base/ReporteVentasxClientes/verreporte'+params,
                           width: 900,
                           height: 500,
                           title: 'Clientes'
                        });
                    }
                }
            ]
        };
    }
    
    
});

new Apps.<?=$this->name?>();
