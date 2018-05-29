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
            win = this.create();
		}
        win.show();
    },
	
    create: function() {
		defaultWinCfg = {
            id: this.id+'-win',
            title: this.title,
            iconCls: 'icon-grid',
			width:	<?=$this->width?>,
			height:	<?=$this->height?>,
			border:  false,
            shim: false,
            animCollapse: false,
            layout: 'fit',
            items:[<?=$this->shortcuts?>]
        };
        return app.desktop.createWindow(defaultWinCfg);
    }
});

new Apps.<?=$this->name?>();