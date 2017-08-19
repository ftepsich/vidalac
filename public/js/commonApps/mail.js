Ext.ns( 'Apps' );

Apps.mail = Ext.extend(RadDesktop.Module, {
    autoStart: true,
    title: 'Enviar Mail',
    appChannel: '/desktop/modules/js/commonApps/mail.js',

    eventlaunch: function (ev) {
        this.datos = ev;
        this.createWindow();
    },

    createWindow: function() {
        this.form =  new Ext.form.FormPanel({

            labelWidth: 55, // label settings here cascade unless overridden
            url: this.datos.url || '/mail/send',
            frame:true,
            waitTitle: 'Espere por favor',
            bodyStyle:'padding:5px 5px 0',
            defaultType: 'textfield',
            items: [
                this.getDestinoField(),
               
            {

                fieldLabel: 'Asunto',
                name: 'Asunto',
                anchor:'100%',
                value: this.datos.asunto,
                allowBlank: false
            },{
                xtype: 'htmleditor',
                name: 'Cuerpo',
                anchor:'100%',
                height: 350,
                autoScroll :true,
                value: this.datos.cuerpo,
                allowBlank: false
            }
            ]
        });
        if (this.datos.baseParams != undefined) {
             this.form.getForm().baseParams = this.datos.baseParams;
        }
        win = this.create();
        win.show();
    },

    getDestinoField: function() {

        if (this.datos.Persona != undefined){
            return {
                "xtype":"xcombo",
                anchor:'100%',
                vtype:'email',
                "displayField":"Email",
                "autoLoad":false,
                "selectOnFocus":true,
                "forceSelection":true,
                "forceReload":true,
                "hiddenName":"Destino",
                "loadingText":"Cargando...",
                "lazyRender":true,
                "store":new Ext.data.JsonStore ({"id":0,"url":"datagateway\/combolist\/model\/Emails\/m\/Base\/search\/Email/Persona/"+this.datos.Persona,"baseParams":{"0":null,"sort":"Email"}}),
                "typeAhead":true,
                "valueField":"Email",
                "autocomplete":true,
                "allowBlank":false,
                "allowNegative":false,
                "fieldLabel":"Destino",
                "name":"Destino"
            };
        } else {
            return {
                fieldLabel: 'Destino',
                name: 'Destino',
                vtype:'email',
                anchor:'100%',
                value: this.datos.destino,
                allowBlank: false
            };
        }

    },
	
    create: function() {
        var idw =this.id+'mail-win'+app.id();
        return app.desktop.createWindow({
            id: idw,
            title: this.title,
            width: 750,
            height: 500,
            iconCls: 'icon-grid',
            border:  false,
            shim: false,
            animCollapse: false,
            layout: 'fit',
            items: [
            this.form
            ],
            buttons: [{
                text: 'Enviar',
                handler: function (){
                    this.form.getForm().submit({
                        scope:this,
                        waitMsg:'Enviando...',
                        success: function(form, action) {
                            app.publish( '/desktop/notify',{
                                title: 'Mail',
                                icon: 'images/page_white_go.png',
                                html: 'Mail enviado correctamente'
                            });
                            Ext.getCmp(idw).close();
                        },
                        failure: function(form, action) {
                            switch (action.failureType) {
                                case Ext.form.Action.CLIENT_INVALID:
                                    Ext.Msg.alert('Error', 'Faltan datos requeridos');
                                    break;
                                case Ext.form.Action.CONNECT_FAILURE:
                                    Ext.Msg.alert('Error', 'Fallo de comunicacion ajax');
                                    break;
                                case Ext.form.Action.SERVER_INVALID:
                                    Ext.Msg.alert('Error', action.result.msg);
                            }

                        }
                    });
                },
                scope: this
            },{
                text: 'Cancelar',
                handler: function (){
                    Ext.getCmp(idw).close();
                },
                scope: this
            }]

        });
    }
});

new Apps.mail();