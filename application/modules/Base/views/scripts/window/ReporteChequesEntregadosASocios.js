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


        var cheque_estado = Ext.ComponentMgr.create({
            fieldLabel: 'Cheques Estado',
            xtype: 'combo',
            width: '300px',
            displayField: 'Descripcion',
            name: 'chequesestados',
            valueField: 'Id',
            allowBlank: true,
            msgTarget: 'under',
            triggerAction: 'all',
            store: new Ext.data.JsonStore({
                id: 0,
                url: "datagateway/combolist/fetch/RetiradoPorSocio/model/ChequesEstados/m/Base",
                storeId: "ChequesEstadosStore"
            })
        });

        var cheques =Ext.ComponentMgr.create({
            fieldLabel: 'Cheques retirados',
            xtype: 'superboxselect',
            anchor: '90%',
            tpl: '<tpl for="."><div class="x-combo-list-item">Nº {Numero} {BancoSucursal_cdisplay} de {Persona_cdisplay}</div></tpl>',
            displayFieldTpl: 'Nº {Numero} {BancoSucursal_cdisplay} de {Persona_cdisplay}',
            name: 'cheques',
            valueField: 'Id',
            allowBlank: true,
            msgTarget: 'under',
            triggerAction: 'all',
            store: new Ext.data.JsonStore({
                id: 0,
                url: "datagateway/combolist/fetch/EntregadosASocios/model/Cheques/m/Base",
                storeId: "ChequesStore"
            })
        });

        var form = Ext.ComponentMgr.create(
            {
                xtype: 'form',
                url : '/Base/ReporteChequesEntregadosASocios/verreporte',
                layout: 'form',
                border: false,
                bodyStyle: 'padding:10px',
                defaults: {
                    border: false
                },
                items: [
                    cheque_estado,
                    {
                        id: 'Socio-Id',
                        xtype: 'textfield',
                        name:'socio',
                        fieldLabel: 'Socio que retira',
                        width: '300px',
                        maxLength: 50
                    },
                    {
                        id: 'obs-Id',
                        xtype: 'textarea',
                        name:'observaciones',
                        fieldLabel: 'Observaciones',
                        width: '300px',
                        maxLength: 800
                    },
                    {
                       xtype: 'xdatefield',
                       ref: '../fecha',
                       format: 'd/m/Y',
                       dateFormat:'Y-m-d',
                       name: 'fecha',
                       fieldLabel : 'Fecha de retiro'
                    },
                    cheques
                ],
                buttons:[
                    {
                        text:  'Ver Reporte',
                        handler: function () {
                            values = this.ownerCt.ownerCt.getForm().getValues();
                            var  params = '';

                            if (values.fecha == 'undefined') {
                                Ext.Msg.alert('Atencion', 'Debe completar la fecha');
                                return;
                            } else {
                                params += '/fecha/'+values.fecha;
                            }

                            if (values.socio != '') {
                                params += '/socio/'+values.socio;
                            }

                            if (values.observaciones != '') {
                                params += '/observaciones/'+values.observaciones;
                            }

                            if (values.cheques != '') {
                                params += '/cheques/';
                                params += (values.cheques instanceof Array) ? values.cheques.join(',') : values.cheques;
                            }

                            app.publish('/desktop/modules/js/commonApps/showUrl.js', {
                                action: 'launch',
                                url: '/Base/ReporteChequesEntregadosASocios/verreporte'+params,
                                width: 900,
                                height: 500,
                                title: 'Reporte Cheques entregados a Socios'
                            });
                        }
                    }
                ]
            }
        );

        cheque_estado.on('select',function(t,d){
            cheques.store.baseParams['ChequeEstado'] = d.id;
            cheques.store.baseParams['TieneRecibo'] = 0;
            cheques.reset();
        });

        // ocultar campos
        var reglas = {
            12: ['socio'],
            13: ['socio'],
        };        

        Rad.autoHideFields(form, cheque_estado, reglas);        

        return form;
    }

});

new Apps.<?=$this->name?>();
