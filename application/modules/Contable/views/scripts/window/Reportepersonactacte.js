Ext.ns( 'Apps' );

Apps.<?=$this->name?> = Ext.extend(RadDesktop.Module, {
    autoStart: true,
    title: '<?=$this->title?>',
    appChannel: '/desktop/modules<?=$this->url()?>',
	
    eventlaunch: function(ev)
    {
      this.idPersona = ev.id;
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
                    ['Cliente', '1'],
                    ['Proveedor', '2'],
                    ['Consolidado', '3']
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
                this.listadoClientes(this.idPersona)
            ]
        };
        return app.desktop.createWindow(defaultWinCfg);
    },
	
	listadoClientes: function(idPersona) {
        return {
            xtype: 'form',
            url : '/Contable/Reportepersonactacte/verreporte',
//            layout: 'form',
            border: false,
            bodyStyle: 'padding:10px',
            defaults: {
                border: false
            },
            items: [
                this.cabecera,
                this.tipoPersona,
                {
                    xtype:'fieldset',
                    title: 'Periodo a ver',
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
                       var  params = '/persona/'+ idPersona;

                       if (values.desde != 'undefined') params += '/desde/'+values.desde;
                       if (values.hasta != 'undefined') params += '/hasta/'+values.hasta;

                       if (values.desde != 'undefined' && values.hasta != 'undefined') {

                            if(values.desde > values.hasta){
                                Ext.Msg.alert('Atencion', 'La fecha desde es menor que la fecha hasta.');
                                return;
                            }
                        }

                       if (values.cabecera != '')       params += '/cabecera/'+values.cabecera;
                       if (values.formato != '')        params += '/formato/'+values.formato;
                       if (values.tipoPersona != '')    params += '/tipoPersona/'+values.tipoPersona;
                       
    
                       app.publish('/desktop/modules/js/commonApps/showUrl.js', {
                           action: 'launch',
                           url: '/Contable/Reportepersonactacte/verreporte'+params,
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
