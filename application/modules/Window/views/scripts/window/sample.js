Ext.ns( 'Apps' );

Apps.<?=$this->name?> = Ext.extend(RadDesktop.Module, {
	autoStart: true,
	title: <?=$this->title?>,
    /**
	* Aqui se agregan los js de los que depende este modulo
	* estos se cargaran automaticamente antes de ejecutarse
	*/
	requires: [
        '/js/debug.js'			
    ],
	/**
	* Siempre debe terminar sin / por ejemplo /desktop/modules/window/mimodulo
	*/
	appChannel: '/desktop/modules/window/abm',
    init: function() {
        
    },

	/**
	* Esta funcion se ejecuta cuando el modulo termina de cargar correctamente, incluyendo sus dependencias
	*/
    startup: function() {
       
    },
	
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

    createWindow: function() {
        var win = app.desktop.getWindow(this.id+'-win');
        if ( !win )
            win = this.create();
        win.show();
    },

    create: function() {
        return app.desktop.createWindow({
            id: this.id+'-win',
            title: this.title,
            width: 300,
            height: 250,
            iconCls: 'icon-grid',
            shim: false,
            animCollapse: false,
            layout: 'fit',
            items: [
                {
                    html: 'Sample App'
                }
            ]
        });
    }

});

new Apps.<?=$this->name?>();
