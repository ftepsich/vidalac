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
        this.prov = new Ext.ux.form.ComboBox({
          "width":300,
          "displayField":"Descripcion",
          "autoLoad":true,
          "selectOnFocus":true,
          "forceSelection":true,
          "forceReload":true,
          "hiddenName":"Provincia",
          "loadingText":"Cargando...",
          "lazyRender":true,
          "store":new Ext.data.JsonStore({"id":0,"url":"datagateway\/combolist\/model\/Provincias/m\/Base\/search\/Descripcion","storeId":"BancoSucursalStore"}),
          "typeAhead":true,
          "valueField":"Id",
          "pageSize":20,
          "editable":true,
          "autocomplete":true,
          "allowBlank":true,
          "allowNegative":false,
          "fieldLabel":"Provincia",
          "name":"Provincia"
        });

        this.loc = new Ext.ux.form.ComboBox({
          "width":300,
          "displayField":"Descripcion",
          "autoLoad":true,
          "selectOnFocus":true,
          "forceSelection":true,
          "forceReload":true,
          "hiddenName":"Localidad",
          "loadingText":"Cargando...",
          "lazyRender":true,
          "hidden":true,
          "store":new Ext.data.JsonStore({"id":0,"url":"datagateway\/combolist\/model\/Localidades/m\/Base\/search\/Descripcion","storeId":"locStore"}),
          "typeAhead":true,
          "valueField":"Id",
          "pageSize":20,
          "editable":true,
          "autocomplete":true,
          "allowBlank":true,
          "allowNegative":false,
          "fieldLabel":"Localidad",
          "name":"Localidad"
        });

        this.inscripcion = new Ext.ux.form.ComboBox(                {
          //"xtype":"xcombo",
          //anchor:'100%',
          "displayField":"Descripcion",
          "autoLoad":true,
          "selectOnFocus":true,
          "forceSelection":true,
          "forceReload":true,
          "hiddenName":"Inscripcion",
          "loadingText":"Cargando...",
          "lazyRender":true,
          "store":new Ext.data.JsonStore({"id":0,"url":"datagateway\/combolist\/model\/ModalidadesIVA/m\/Base\/search\/Descripcion","storeId":"BancoSucursalStore"}),
          "typeAhead":true,
          "valueField":"Id",
          "pageSize":20,
          "editable":true,
          "autocomplete":true,
          "allowBlank":false,
          "allowNegative":false,
          "fieldLabel":"Inscripcion",
          "name":"Inscripcion"
        });
        
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
                this.listadoClientes()
            ]
        };
        return app.desktop.createWindow(defaultWinCfg);
    },
	
	listadoClientes: function() {
        return {
            xtype: 'form',
            url : '/Base/ReporteClientes/verreporte',
//            layout: 'form',
            border: false,
            bodyStyle: 'padding:10px',
            defaults: {
                border: false
            },
            items: [
                this.tipoPersona,
                this.inscripcion,
                {
                    xtype:'fieldset',
                    title: 'Fecha de Alta',
                    border: true,
                    layout: 'form',
                    items :[               
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
                              }
                            ]
                },
                {    
                    xtype:'fieldset',
                    title: 'Lugar',
                    border: true,
                    layout: 'anchor',
                    items :[
                        {
                            xtype: 'radiogroup',
                            fieldLabel: 'Formato',
                            listeners: {
                                'change': {
                                    'fn': function (rg, c){
                                        if (c.boxLabel == 'Provincia') {
                                            this.prov.show();
                                            this.loc.hide();
                                            this.loc.clearValue();
                                        } else {
                                            this.loc.show();
                                            this.prov.hide();
                                            this.prov.clearValue();
                                        }
                                    },
                                    'scope':this
                                }
                            },
                            width: 150,
                            items: [
                                { boxLabel: 'Provincia', name: 'locprov', inputValue: 'Provincia', checked: true },
                                { boxLabel: 'Localidad', name: 'locprov', inputValue: 'Localidad' }
                            ]
                        },
                        this.prov,
                        this.loc
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
                       if (values.desde != 'undefined') params += '/desde/'+values.desde;
                       if (values.hasta != 'undefined') params += '/hasta/'+values.hasta;
                       if (values.Inscripcion != 'undefined') params += '/inscripcion/'+values.Inscripcion;
                       if (values.locprov == 'Provincia'){
                          if (values.Provincia != '') params += '/provincia/'+values.Provincia;
                       }
                       if (values.locprov == 'Localidad'){
                          if (values.Localidad != '') params += '/localidad/'+values.Localidad;
                       }
                       if (values.tipoPersona != '') params += '/tipopersona/'+values.tipoPersona;
                       if (values.formato != '') params += '/formato/'+values.formato;

                       app.publish('/desktop/modules/js/commonApps/showUrl.js', {
                           action: 'launch',
                           url: '/Base/ReporteClientes/verreporte'+params,
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
