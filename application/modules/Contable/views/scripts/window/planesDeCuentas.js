Ext.ns( 'Apps' );

Apps.<?=$this->name?> = Ext.extend(RadDesktop.Module, {
    autoStart: true,
    title: '<?=$this->title?>',
    appChannel: '/desktop/modules<?=$this->url()?>',

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
        this.tree.getTopToolbar().addButton([
            { xtype:'tbseparator' },
            {
                text: 'Imprimir',
                icon: 'images/printer.png',
                cls: 'x-btn-text-icon',
                scope: this.grid,
                handler: function () {
                    this.publish('/desktop/modules/Window/birtreporter', {
                        action: 'launch',
                        template: 'PlanDeCuentas',
                        //id: selected.id,
                        id: 1,
                        output: 'pdf',
                        width:  600,
                        height: 800
                    });
                }
            }
        ])
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
            width: 1000,
            height:500,
            items: [
                this.renderWindowContent()
            ]
        };
        return app.desktop.createWindow(defaultWinCfg);
    },

    renderWindowContent: function () {
        return {
            layout: 'fit',
            border: false,
            defaults: {
                border: false
            },
            items: [
                this.tree
            ]
        }
    }
});

new Apps.<?=$this->name?>();