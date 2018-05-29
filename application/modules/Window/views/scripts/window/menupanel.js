Ext.ns( 'Apps' );

Apps.<?= $this->name ?> = Ext.extend(RadDesktop.Module, {
    autoStart: true,
    title: '<?= $this->title ?>',
    appChannel: '/desktop/modules<?= $this->url() ?>',
    
    eventlaunch: function(ev) {
        this.createWindow();
    },

    createWindow: function() {
        this.win = app.desktop.getWindow(this.id+'-win');
        if ( !this.win ) {
            this.win = this.create();
        }
        this.win.show();
    },

    create: function() {
        this.initTemplate();
        this.store = new Ext.data.JsonStore({
            autoDestroy: true,
            root: 'menu',
            idProperty: 'id',
            fields: ['id', 'modulo', 'url', 'icono', 'texto'],
            data: <?= $this->menuData ?>
        });
    
        this.lookup = {};
        var formatData = function(data) {
            this.lookup[data.modulo] = data;
            return data;
        }
        
        this.view = new Ext.DataView ({
            tpl: this.gridTemplate,
            singleSelect: true,
            overClass:'x-view-over',
            itemSelector: 'div.thumb-wrap',
            emptyText : '<div style="padding:10px;">No images match the specified filter</div>',
            store: this.store,
            listeners: {
                'selectionchange': { fn:this.doSelect, scope:this, buffer:100 },
                'dblclick'       : { fn:this.doLaunch, scope:this },
            },
            prepareData: formatData.createDelegate(this)
        });
        
        defaultWinCfg = {
            id: this.id+'-win',
            title: this.title,
            iconCls: 'icon-grid',
            width: <?= $this->width ?>,
            height: <?= $this->height ?>,
            border: false,
            bodyStyle:'overflow-y: scroll;',
            shim: false,
            animCollapse: false,
            layout: 'fit',
            items: this.view,
            bbar: { html: '&nbsp;' }
        };
        return app.desktop.createWindow(defaultWinCfg);
    },
    
    getSelectedNodeData: function() {
        var selNode = this.view.getSelectedNodes();
        if (selNode.length != 1) return;
        var selNode = selNode[0];
        var modulo = selNode.id.substr(9);
        return this.lookup[modulo];
    },
    
    doSelect: function() {
        data = this.getSelectedNodeData();
        this.win.getBottomToolbar().update( (data) ? data.texto : '&nbsp' );
    },
    
    doLaunch: function() {
        data = this.getSelectedNodeData();
        if (data)
            app.publish('/desktop/modules/' + data.url, {action:'launch'});
    },
    
    initTemplate: function() {
        this.gridTemplate = new Ext.XTemplate(
            '<tpl for=".">',

                '<div class="thumb-wrap thumb-wrap-menu-panel" id="shortcut-{modulo}">',
                '<div class="thumb"><img src="images/modulos/{icono}64.png" title="{texto}"></div>',
                '<span class="x-unselectable">{texto}</span></div>',
            '</tpl>'
        );
        this.gridTemplate.compile();
    }
});

new Apps.<?=$this->name?>();
