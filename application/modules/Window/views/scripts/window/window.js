Ext.ns( 'Apps' );

Apps.<?=$this->name?> = Ext.extend(RadDesktop.Module, {
	autoStart: true,
	title: '<?=$this->title?>',
	//Siempre debe terminar sin / por ejemplo /desktop/modules/window/mimodulo
	appChannel: '/desktop/modules<?=$this->url()?>',
    init: function() {
        
    },

	// Esta funcion se ejecuta cuando el modulo termina de cargar correctamente, incluyendo sus dependencias
    startup: function() {
       
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
        return app.desktop.createWindow({
            id: this.id+'-win',
            title: this.title,
            width: 500,
            height: 400,
            iconCls: 'icon-grid',
			border:  false,
            shim: false,
            animCollapse: false,
            layout: 'fit',
            items: [
				this.grid
            ]
        });
    }
});

new Apps.<?=$this->name?>();