Ext.ns( 'Apps' );

Apps.showUrl = Ext.extend(RadDesktop.Module, {
    autoStart: true,
    appChannel: '/desktop/modules/js/commonApps/showUrl.js',
    title: 'showUrl',

    eventlaunch: function(ev) {
        this.createWindow(ev);
    },    

    createWindow: function(ev) {
        win = this.create(ev);
        win.show();
    },

    create: function(ev) {
        return app.desktop.createWindow({
            id: app.id( 'app-' )+'showUrl-win',
            title:   ev.title,
            modal:   true,
            width:   ev.width||600,
            height:  ev.height||500,
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
                    src: ev.url 
                }
            }]
        });
    }
});

new Apps.showUrl();