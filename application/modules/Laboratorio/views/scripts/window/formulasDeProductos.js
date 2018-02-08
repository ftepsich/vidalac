Ext.ns( 'Apps' );

Apps.<?=$this->name?> = Ext.extend(RadDesktop.Module, {
	autoStart: true,
	title: '<?=$this->title?>',
	appChannel: '/desktop/modules<?=$this->url()?>',
	
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
			this.grid =  Ext.ComponentMgr.create(<?=$this->grid?>);
            
			this.gridFormulas =  Ext.ComponentMgr.create(<?=$this->gridFormulas?>);
            win = this.create();
		}
        win.show();
    },
	
    create: function() {
        return app.desktop.createWindow({
            width: 900,
            height: 500,
            id: this.id+'-win',
            title: this.title,
            iconCls: 'icon-grid',
            border:  false,
            shim: false,
            animCollapse: false,
            layout: 'fit',
            items: [{
                xtype : 'panel',
                layout : "border",
                items : [
                    {
                        region : "north",
                        title : "Productos",
                        layout: 'fit',
                        height : 250,
                        split : true,
                        items : [
                            this.grid
                        ]
                    },{
                        xtype : 'panel',
                        region : "center",
                        layout : "border",
                        items: [{
                            region : "west",
                            layout: 'fit',
                            width: 650,
                            items : [
                                this.gridFormulas
                            ]
                        },{
                            region: 'center',
                            // layout: 'fit',
                            xtype: 'piechart',
                            dataField: 'Cantidad',
                            categoryField: 'Insumo_cdisplay',
                            store: this.gridFormulas.store
                        }]
                    }]
            }]
        });
    }
});

new Apps.<?=$this->name?>();
