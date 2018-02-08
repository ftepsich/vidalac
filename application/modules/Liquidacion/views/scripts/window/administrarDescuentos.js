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
            win = this.create();
        }
        win.show();
    },
	
    create: function() {
        this.createSecGrids();
        defaultWinCfg = {
            id: this.id+'-win',
            title: this.title,
            iconCls: 'icon-grid',
            border:  false,
            shim: false,
            animCollapse: false,
            layout: 'fit',
            width: 900,
            height:500,
            items: [
                this.renderWindowContent()
            ]
        };
        return app.desktop.createWindow(defaultWinCfg);
    },
	
    renderWindowContent: function () {
        return {
            layout : 'border',
            border : false,
            items : [{
                    region : 'center',
                    layout: 'fit',
                    height:200,
                    split: true,
                    items: [
                        this.grid
                    ]
                },{
                    region: 'south',
                    height:200,                    
                    layout: 'fit',
                    collapsible: true,
                    collapsed: true,                    
                    title: 'Detalles de las cuotas',
                    items: [
                        this.grid_DetallesDescuentos
                    ]
                }]
        }
    },
    createSecGrids: function() {
        this.grid_DetallesDescuentos = Ext.ComponentMgr.create(<?=$this->grid_DetallesDescuentos?>);
    }
	
});

new Apps.<?=$this->name?>();