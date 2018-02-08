Ext.ns( 'Apps' );

Apps.gmaps = Ext.extend(RadDesktop.Module, {
	autoStart: true,
	title: 'Mapa',
	appChannel: '/desktop/modules/js/commonApps/gmaps.js',

	eventlaunch:function ()
        {
            this.createWindow();
            this.map.searchAddress('Argentina');
        },
	eventsearchAddress: function (ev) {
		this.createWindow();
		this.map.searchAddress(ev.address);
	},
	eventsearchPath: function (ev) {
		this.createWindow();
		this.map.searchPath(ev.srcAddress, ev.dstAddress);
	},
    

    createWindow: function() {
        this.map =  new Rad.Map({closable: false});
        win = this.create();
		win.show();
    },
	
    create: function() {
        return app.desktop.createWindow({
            id: this.id+'gmaps-win'+app.id(),
            title: this.title,
            width: 700,
            height: 500,
            iconCls: 'icon-grid',
			border:  false,
            shim: false,
            animCollapse: false,
            layout: 'fit',
            items: [
				this.map
            ]
        });
    }
});

new Apps.gmaps();