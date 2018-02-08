Ext.ns( 'Apps' );

Apps.<?=$this->name?> = Ext.extend(RadDesktop.Module, {
	autoStart: true,
	title: '<?=$this->title?>',
	appChannel: '/desktop/modules<?=$_SERVER['REQUEST_URI']?>',
    init: function() {
        
    },

    // Esta funcion se ejecuta cuando el modulo termina de cargar correctamente, incluyendo sus dependencias
    startup: function() {
       
    },
	
	<?=$this->customJsFunctions?>
	 
	eventfind: function(ev) {
		this.createWindow();
    },
    eventlaunch: function(ev) {
		this.createWindow();
    },

    createWindow: function() {
        var win = app.desktop.getWindow('<?=$this->name?>-win');
        if ( !win )
            win = this.create();
        win.show();
    },
	
	
    create: function() {
        return app.desktop.createWindow(<?=$this->contenido?>);
    }
});

new Apps.<?=$this->name?>();