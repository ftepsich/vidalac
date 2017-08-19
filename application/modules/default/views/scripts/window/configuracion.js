Ext.ns( 'Apps' );

Apps.<?=$this->name?> = Ext.extend(RadDesktop.Module, {
    autoStart: true,
    title: '<?=$this->title?>',
    appChannel: '/desktop/modules<?=$_SERVER['REQUEST_URI']?>',
    requires: [
        '/direct/UsuariosConfiguracionesEscritorios?javascript',
        '/js/erp/wallpaperChooser.js',
        '/css/chooser.css'
    ],

    eventfind: function (ev) {
        this.createWindow();
        //TODO: Ver como se puede implementar esto
    },

    eventsearch: function (ev) {
        this.createWindow();
        //TODO: Ver como se puede implementar esto
    },

    eventlaunch: function (ev) {
        this.createWindow();
    },

    createWindow: function () {
        var win = app.desktop.getWindow(this.id+'-win');
        if ( !win ) {
            this.createTree();
            win = this.create();
        }
        win.show();
    },

    createTree: function () {
        this.tree = Ext.ComponentMgr.create(<?= $this->tree ?>);
        
        this.tree.on('dragdrop', function (t, node, dd, e){

            var d = node.attributes.data;
            Models.Model_UsuariosEscritorioMapper.addShortcut(d.Id, function(result, e) {
                if (e.status) {
                    app.desktop.shortcuts.reload();
                }
            }, this);
        }, this);
        
    },

    init: function (t) {
        var drop = new Ext.dd.DropTarget('x-desktop-view', {
            ddGroup : 'MenuShortcut',
            notifyDrop : function(dd, e, data) {

                return true;
            }
            
        });
    },

    create: function() {
        defaultWinCfg = {
            id: this.id+'-win',
            title: this.title,
            iconCls: 'icon-grid',
            border: false,
            shim: false,
            animCollapse: false,
            layout: 'fit',
            width: 630,
            height:500,
            items: [
                this.renderWindowContent()
            ]
        };
        return app.desktop.createWindow(defaultWinCfg);
    },

    renderWindowContent: function () {
        chooser = new Ext.ux.WallpaperChooserPanel({
            url:'/default/Configuracion/getwallpapers',
            callback: function(data) {
                Models.Model_UsuariosConfiguracionesEscritoriosMapper.guardarFondo(data.image, function(result, e) {
                    if (!e.status) {
                    
                        app.publish( '/desktop/notify',{
                            title: 'Configuración',
                            iconCls: 'x-icon-error',
                            html: 'La posición del Fondo NO se guardo correctamente'
                        });
                    }
                }, this);
                app.desktop.setWallpaper({
                    pathtofile: data.image,
                    id: data.name,
                    name: data.name
                },false);
            }
        });
        return {
            xtype: 'tabpanel',
            activeTab:0,
            items:[chooser,{
                layout: 'fit',
                title: 'Accesos Directos',
                border: false,
                defaults: {
                    border: false
                },
                items: [
                    this.tree
                ]
            },]
        };
    }
});

new Apps.<?=$this->name?>();