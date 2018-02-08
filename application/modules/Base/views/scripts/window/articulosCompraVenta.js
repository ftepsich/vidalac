Ext.ns( 'Apps' );

Apps.<?=$this->name?> = Ext.extend(RadDesktop.Module, {
    autoStart: true,
    title: '<?=$this->title?>',
    appChannel: '/desktop/modules<?=$this->url()?>',
    requires: <?= ($this->requires) ? $this->requires : 'null' ?>,

    /**
     * Filtra la grilla por el id enviado
     */
    eventfind: function (ev) {
        this.createWindow();
        var p = this.grid.buildFilter(0,'Id',ev.value);
        this.grid.store.load({params:p});
    },

    eventsearch: function (ev) {
        this.createWindow();
        var p = this.grid.buildFilter(0,ev.field,ev.value);
        this.grid.store.load({params:p});
    },

    /**
     * utilizado para enviar un parametro adicional al load
     */
    eventcustom: function (ev) {
        this.createWindow();
        var p = {custom: ev.value}
        this.grid.store.load({params:p});
    },

    eventlaunch: function(ev) {
        this.createWindow();
        this.grid.store.load();
    },

    createWindow: function() {
        var win = app.desktop.getWindow(this.id+'-win');
        if (!win) {
            this.grid = Ext.ComponentMgr.create(<?=$this->grid?>);
            this.gridPrecios = Ext.ComponentMgr.create(<?=$this->gridPrecios?>);
            this.gridPrecios.store.baseParams.fetch = 'HistoricoCompra';
            win = this.create();
        }
        win.show();
    },

    create: function() {
        defaultWinCfg = {
            id: this.id+'-win',
            width: 880,
            height: 600,
            title: this.title,
            iconCls: 'icon-grid',
            border:  false,
            shim: false,
            animCollapse: false,
            layout: 'fit',
            items: [{
                layout: {
                    type: 'vbox',
                    pack: 'start',
                    align: 'stretch'
                },
                items:[
                    this.grid,
                    {
                        tbar:[
                        {
                            toggleGroup: 'artPreciosToggle',
                            text: 'Compra',
                            pressed: true,
                            scope: this,
                            toggleHandler: function(b,s) {
                                this.gridPrecios.store.baseParams.fetch = 'HistoricoCompra';
                                this.gridPrecios.store.load();
                            }
                        },{
                            toggleGroup: 'artPreciosToggle',
                            text: 'Venta',
                            scope: this,
                            toggleHandler: function(b,s) {
                                this.gridPrecios.store.baseParams.fetch = 'HistoricoVenta';
                                this.gridPrecios.store.load();
                            }
                        }
                        ],
                        flex:1,
                        layout: 'fit',
                        items: [this.gridPrecios]
                    }
                ]
            }]
        };
        return app.desktop.createWindow(defaultWinCfg);
    }
});

new Apps.<?=$this->name?>();
