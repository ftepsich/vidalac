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
            this.grid = Ext.ComponentMgr.create(<?=$this->grid?>);
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
            width: 1000,
            height:500,
            items: [
                this.renderWindowContent()
            ]
        };
        return app.desktop.createWindow(defaultWinCfg);
    },
	
    renderWindowContent: function ()
    {
        return {
            xtype: 'panel',
            layout: 'border',
            border: false,
            defaults: {
                border: false
            },
            items: [
                {
                    region: 'center',
                    layout: 'fit',
                    items: [
                        this.grid
                    ]
                },
                {
                    region: 'east',
                    collapsible: true,
                    title: 'Caracteristicas',
                    layout: 'fit',
                    width: 300,
                    items: [
                        this.gridProductosCaracteristicas
                    ]
                }
            ]
        }
    },
	
    createSecGrids: function ()
    {
        this.gridProductosCaracteristicas = Ext.ComponentMgr.create(<?php echo $this->gridProductosCaracteristicas ?>);
    }
	
});

new Apps.<?=$this->name?>();