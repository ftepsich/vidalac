Ext.ns( 'Apps' );

Apps.viewDepositos = Ext.extend(RadDesktop.Module, {
	autoStart: true,
	title: 'Depositos',
	appChannel: '/desktop/modules/js/commonApps/viewDepositos.js',

	
	eventlaunch: function (ev) {
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
        return app.desktop.createWindow({
            id: this.id+'-win',
            title: this.title,
            width: 700,
            height: 500,
            iconCls: 'icon-grid',
			border:  false,
//            shim: true,
//            animCollapse: false,
            layout: 'fit',
            items: [
				new ERP.depositoPanel({partidor:false, mover: false})
            ]
        });
    }
});

new Apps.viewDepositos();