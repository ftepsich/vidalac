Ext.ns( 'Apps' );

Apps.<?=$this->name?> = Ext.extend(RadDesktop.Module, {
    autoStart: true,
    title: '<?=$this->title?>',
    appChannel: '/desktop/modules<?=$this->url()?>',
    init: function() {
        
    },

    // Esta funcion se ejecuta cuando el modulo termina de cargar correctamente, incluyendo sus dependencias
    startup: function() {
    },
	 
    eventfind: function(ev) {
            this.createWindow();
    },
    eventlaunch: function(ev) {
		this.createWindow();
    },

    createWindow: function() {
        var win = app.desktop.getWindow('lineadeProducciones-win');
        if ( !win )
            win = this.create();
        win.show();
    },


    createGrids: function () {
        this.grid = Ext.ComponentMgr.create(<?=$this->grid?>);
        
        this.gridConfiguracion   = Ext.ComponentMgr.create(<?=$this->gridConfiguracion?>);
        this.gridActividadesConf = Ext.ComponentMgr.create(<?=$this->gridActividadesConf?>);
        
    },
	
	
    create: function() {
        this.createGrids();
        
        return app.desktop.createWindow({
            id: 'lineadeProducciones-win',
            layout: 'fit',
            title: 'Lineas de Producción',
            width:  900,
            height: 500,
            plain  : true,
            defaults: {
                border: false
            },
            items: {
                xtype : 'panel',
                layout : "border",
                items : [
		{
			region : "center",
			title : "Lineas",
			layout: 'fit',
			items : [this.grid]
                        
		},
		{
			region : "east",
			
			layout: {
                            type:'vbox',
                            align:'stretch'
                        },
                        width : 500,
                        split : true,
			items : [this.gridActividadesConf, this.gridConfiguracion]
                }]
            }
        });
    }
});

new Apps.<?=$this->name?>();