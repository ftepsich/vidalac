Ext.ns( 'Apps' );

Apps.<?=$this->name?> = Ext.extend(RadDesktop.Module, {
    autoStart: true,
    title: '<?=$this->title?>',
    //appChannel: '/desktop/modules<?=$_SERVER['REQUEST_URI']?>',
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
            win = this.create();
        }
        win.show();
    },

    create: function() {
        defaultWinCfg = {
            id: this.id+'-win',
            title: this.title,
            iconCls: 'icon-grid',
            border:  false,
            shim: false,
            animCollapse: false,
            layout: 'fit',
            items: [
                this.grid
            ]
        };
        defaultWinCfg = Ext.apply(defaultWinCfg, <?=$this->AbmWinCfg?>);
        return app.desktop.createWindow(defaultWinCfg);
    }
});

new Apps.<?=$this->name?>();
