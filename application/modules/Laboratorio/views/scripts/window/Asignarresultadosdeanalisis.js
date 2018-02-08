Ext.ns( 'Apps' );

Apps.<?=$this->name?> = Ext.extend(RadDesktop.Module, {
	autoStart: true,
	title: '<?=$this->title?>',
    /**
	* Aqui se agregan los js de los que depende este modulo
	* estos se cargaran automaticamente antes de ejecutarse
	*/
//	requires: [
//        '/js/debug.js'
//    ],

	/**
	* Siempre debe terminar sin / por ejemplo /desktop/modules/window/mimodulo
	*/
	appChannel: '/desktop/modules<?=$_SERVER['REQUEST_URI']?>',

	
	/**
    * Se ejecuta cuando recibe el evento launch 
	* 
	* se puede capturar otros eventos agregando 
	* funciones con la nomenclatura eventMifuncion
	* por ejemplo eventSearch: function(ev){}
	*/
    eventlaunch: function(ev) {
		this.createWindow();
    },

    /**
	* Filtra la grilla por el id enviado
	*/
	eventfind: function (ev) {
		this.createWindow();
		var params = this.grid.buildFilter(0,'Id',ev.value);
		this.grid.store.load(params);
	},

    createWindow: function() {
        var win = app.desktop.getWindow(this.id+'-win');
        if ( !win ) {
            this.createPropertyGrid();
			this.form = Ext.ComponentMgr.create(<?=$this->form?>);
            this.grid = Ext.ComponentMgr.create(<?=$this->grid?>);
            this.grid.abmForm = this.form;
            win = this.create();
		}
        win.show();
    },

    createPropertyGrid: function () {
        this.propertyGrid = new Ext.grid.PropertyGrid({
			id: 'gridAnalisisValores',
			layout: 'fit',
			width:   300,

			listeners: {
				validateedit : {fn:function(e) {
					var g = this.grid;
					var selected = g.getSelectionModel().getSelected();
					if (selected) {
						Rad.callRemoteJsonAction ({
							url: '/Laboratorio/Asignarresultadosdeanalisis/saveproperty',
							params: {
								property: e.record.id,
								value:    e.value,
								id:       selected.data.Id
							},
							async: false,
							success: function() {

							},

							failure: function (r) {
								e.record.data.value = e.originalValue;
								return true;
							}
						});
					}
				}, scope: this}
			},
			customEditors: <?=$this->editors?>
		});
    },

    create: function() {
        return app.desktop.createWindow({
            id: this.id+'-win',
            title: this.title,
            width: 900,
            height: 400,
            iconCls: 'icon-grid',
            border: false,
            shim:   false,
            animCollapse: false,
            layout: 'fit',
            items: [
                {
                    xtype : 'panel',
                    layout : "border",
                    items : [
                        {
                            region : "center",
                            title : "Analisis",
                            layout: 'fit',
                            items : [
                                this.propertyGrid
                            ]
                        },
                        {
                            region : "west",
                            title : "Muestras",
                            layout: 'fit',
                            width : 400,
                            split : true,
                            items : [
                                this.grid
                            ]
                        }
                    ]
                }
            ]
        });
    }
});

new Apps.<?=$this->name?>();
