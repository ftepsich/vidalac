Ext.ns( 'Apps' );

Apps.<?=$this->name?> = Ext.extend(RadDesktop.Module, {
    title: '<?=$this->title?>',
    reportAction: 'report',
   
    /**
     * Siempre debe terminar sin / por ejemplo /desktop/modules/window/mimodulo
     */
    appChannel: '/desktop/modules/Window/birtreporter',
	
    /**
     * Se ejecuta cuando recibe el evento launch
     *
     * se puede capturar otros eventos agregando
     * funciones con la nomenclatura eventMifuncion
     * por ejemplo eventSearch: function(ev){}
     */
    eventlaunch: function(ev) {
        this.createWindow(ev);
    },

    eventmulticheques: function(ev) {
        this.reportAction = 'reportcheques';
        this.createWindow(ev);
    },
    
    createWindow: function(ev) {
        win = this.create(ev);
        win.show();
    },
	
    create: function(ev) {
        //console.dir({llego:ev});
        parameters = "";

        if (ev.params) {
            
            var i = 0;

            Ext.each(ev.params, function(param) {
                parameters = parameters +'/params/'+ param.name + ','+ param.type + ','+ param.value
            });
        }

        var idURL = '';
        if (ev.id) idURL = '/id/'+ev.id;

        return app.desktop.createWindow({
            id: app.id( 'app-' )+'report-win',
            title: ev.title || this.title,
            modal: true,
            width:  ev.width  ||600,
            height: ev.height ||500,
            iconCls: 'icon-grid',
            border:  false,
            shim: false,
            animCollapse: false,
            layout: 'fit',
            items: [{
                xtype: 'box',
                autoEl: {
                    tag: 'iframe',
                    style: 'height: 100%; width: 100%',
                    src:'/Window/birtreporter/'+this.reportAction+'/template/'+ev.template+'/output/'+(ev.output || 'pdf')+
                    idURL+parameters
                }

            }]
        });
    }
});

new Apps.<?=$this->name?>();
