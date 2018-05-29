Ext.ux.WallpaperChooserPanel = Ext.extend(Ext.Panel, {
    callback: Ext.emptyFn,

    constructor: function (config) {

        this.thumbTemplate = new Ext.XTemplate(
            '<tpl for=".">',
                '<div class="WallpaperChooser-thumb-wrap" id="{name}">',
                '<div class="WallpaperChooser-thumb"><img src="{thumb}" title="{name}" width=80 ></div>',
                '<span>{name}</span></div>',
            '</tpl>'
        );
        this.thumbTemplate.compile();

        this.detailsTemplate = new Ext.XTemplate(
            '<div class="details">',
                '<tpl for=".">',
                    '<img src="{thumb}"><div class="details-info">',
                    '<b>Imagen:</b>',
                    '<span>{name}</span>',
                '</tpl>',
            '</div>'
        );
        this.detailsTemplate.compile();

        this.showDetails = function(t, sel){
            var selNode = this.view.getSelectedRecords();
            var detailEl = Ext.getCmp('img-detail-panel').body;
            if(selNode && selNode.length > 0){
                selNode = selNode[0];
                detailEl.hide();
                this.detailsTemplate.overwrite(detailEl, selNode.data);
                detailEl.slideIn('l', {stopFx:true,duration:.2});
            }else{
                detailEl.update('');
            }
        }

        this.doCallback = function(){
            var selNode = this.view.getSelectedRecords()[0];
            var callback = this.callback;
            if(selNode && callback) {
                callback(selNode.data);
            }
        }

        this.store = new Ext.data.JsonStore({
            url: config.url,
            root: 'files',
            fields: [
                'name', 'image', 'thumb'
            ],
            listeners: {
                'load': {fn:function(){ this.view.select(0); }, scope:this, single:true}
            }
        });
        this.store.load();


        this.view = new Ext.DataView({
            tpl: this.thumbTemplate,
            singleSelect: true,
            overClass:'x-view-over',
            itemSelector: 'div.WallpaperChooser-thumb-wrap',
            emptyText : '<div style="padding:10px;">No hay Fondos cargados</div>',
            store: this.store,
            listeners: {
                'selectionchange': {fn:this.showDetails, scope:this, buffer:100},
                'dblclick'       : {fn:this.doCallback, scope:this},
                'loadexception'  : {fn:this.onLoadException, scope:this},
                'beforeselect'   : {fn:function(view){
                    return view.store.getRange().length > 0;
                }}
            }
        });

        Ext.ux.WallpaperChooserPanel.superclass.constructor.call(this, Ext.applyIf(config, {
            title: 'Fondos de Pantalla',
            id: 'img-chooser-dlg',
            
            layout: 'fit',
            border: false,
            items:[{
                layout: 'border',
                tbar: [
                    {
                        text: 'Centrar Fondo',
                        icon: '/images/application.png',
                        handler: function() {
                            app.desktop.setWallpaperPosition('center');
                            Models.Model_UsuariosConfiguracionesEscritoriosMapper.guardarFondoPosicion('center', function(result, e) {
                                if (e.status) {
                                    this.publish( '/desktop/notify',{
                                        title: 'Configuración',
                                        iconCls: 'x-icon-information',
                                        html: 'La posición del Fondo se guardo correctamente'
                                    });
                                }
                            }, this);
                        }
                    
                    },{
                        text: 'Repetir Fondo',
                        icon: '/images/application_tile_vertical.png',
                        handler: function() {
                            app.desktop.setWallpaperPosition('tile');
                            Models.Model_UsuariosConfiguracionesEscritoriosMapper.guardarFondoPosicion('tile', function(result, e) {
                                if (e.status) {
                                    this.publish( '/desktop/notify',{
                                        title: 'Configuración',
                                        iconCls: 'x-icon-information',
                                        html: 'La posición del Fondo se guardo correctamente'
                                    });
                                }
                            }, this);
                        }
                            
                    },{
                        text: 'Quitar Fondo',
                        icon: '/images/delete.png',
                        handler: function() {
                            app.desktop.setWallpaper();
                            Models.Model_UsuariosConfiguracionesEscritoriosMapper.quitarFondo(function(result, e) {
                                if (e.status) {
                                    this.publish( '/desktop/notify',{
                                        title: 'Configuración',
                                        iconCls: 'x-icon-information',
                                        html: 'La posición del Fondo se guardo correctamente'
                                    });
                                }
                            }, this);
                        }
                            
                    },{
                        text: 'Color de Fondo',
                        icon:'/images/color_swatch.png',
                        menu: {
                            xtype : 'colormenu',
                            handler: function(p,c) {
                                app.desktop.setBackgroundColor(c);
                                Models.Model_UsuariosConfiguracionesEscritoriosMapper.guardarColor(c, function(result, e) {
                                    if (e.status) {
                                        this.publish( '/desktop/notify',{
                                            title: 'Configuración',
                                            iconCls: 'x-icon-information',
                                            html: 'El color se guardo correctamente'
                                        });
                                    }
                                }, this);
                            }
                        }
                    }
                ],
                items:[{
                    margins: '2 2 2 2',
                    id: 'img-chooser-view',
                    region: 'center',
                    autoScroll: true,
                    items: this.view,
                    
                },{
                    id: 'img-detail-panel',
                    margins: '2 2 2 0',
                    region: 'east',
                    split: true,
                    width: 250,
                    minWidth: 150,
                    maxWidth: 300
                }]
            }]
            
        }));
    },
    

    onLoadException : function(v,o){
        this.view.getEl().update('<div style="padding:10px;">Error cargando imagenes.</div>');
    }
});

String.prototype.ellipse = function(maxLength){
    if(this.length > maxLength){
        return this.substr(0, maxLength-3) + '...';
    }
    return this;
};