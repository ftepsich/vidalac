Ext.ns( 'Apps' );

Apps.<?=$this->name?> = Ext.extend(RadDesktop.Module, {
	autoStart: true,
	title: '<?=$this->title?>',
	appChannel: '/desktop/modules<?=$_SERVER['REQUEST_URI']?>',

	eventfind: function (ev) {
		this.createWindow();
		var p = this.grid.buildFilter(0, 'Id', ev.value);
		this.grid.store.load({params:p});
	},

	eventsearch: function (ev) {
		this.createWindow();
		var p = this.grid.buildFilter(0, ev.field, ev.value);
		this.grid.store.load({params:p});
	},

    eventlaunch: function(ev) {
		this.createWindow();
    },

    createWindow: function() {
        var win = app.desktop.getWindow(this.id+'-win');
        if ( !win ) {
            win = this.create();
		}
        win.show();
    },

    create: function() {
		defaultWinCfg = {
            id: this.id+'-win',
            title: this.title,
            width: 500,
            height: 300,
            iconCls: 'icon-grid',
			border:  false,
            shim: false,
            animCollapse: false,
            items: [{
				xtype: 'form',
                labelWidth: 75,
                id : 'buildModelsForm',
                bodyStyle:'padding:5px 5px 0',
                items: [
                    {
                    xtype          : 'combo',
                    width          : 320,
                    triggerAction  : 'all',
                    pageSize       : 500,
                    emptyText      : 'Seleccione una tabla...',
                    hiddenName	   : 'table',
                    displayField   : 'tableName',
                    loadingText    : 'Cargando...',
                    forceSelection : true,
                    fieldLabel     : 'Tabla',
                    valueField     : 'Id',
                    store: new Ext.data.JsonStore ({
                        root    : 'rows',
                        totalProperty : 'count',
                        url     : '/Develop/buildmodels/gettables',
                        fields: [
                            {name: 'Id', type: 'string'},
                            {name: 'tableName', type: 'string'}
                        ]
                    })
                    },{
                    xtype          : 'checkbox',
                    fieldLabel     : 'Sobrescribir',
                    name		   : 'sobrescribir'
                    }
                ],


                buttons: [{
                    text: 'Generar',
                    handler: function () {
                        Ext.getCmp('buildModelsForm').getForm().submit({
                            scope: this,
                            success:  function(form, action) {
                                if (action && action.result) {
                                     window.app.desktop.showMsg({
                                        title: 'Atencion',
                                        manager: window.app.desktop.getManager(),
                                        renderTo: 'x-desktop',
                                        width: 600,
                                        msg: 'Modelo Creado',
                                        modal: true,
                                        icon: Ext.Msg.ALERT,
                                        buttons: Ext.Msg.OK
                                    });
                                }
                            },
                            failure: function(form, action) {
                                if (action && action.result) {
                                     window.app.desktop.showMsg({
                                        title: 'Error',
                                        manager: window.app.desktop.getManager(),
                                        renderTo: 'x-desktop',
                                        width: 600,
                                        msg: action.result.msg,
                                        modal: true,
                                        icon: Ext.Msg.ERROR,
                                        buttons: Ext.Msg.OK
                                    });
                                }
                            },
                            waitMsg: 'Guardando...',
                            url:'/Develop/buildmodels/buildmodel/'
                        });
                    }
                },{
                    text: 'Cancelar',
                     handler: function () {
                        parent.close();
                     }
                }]

            }
            ]
        };

        return app.desktop.createWindow(defaultWinCfg);
    }
});

new Apps.<?=$this->name?>();