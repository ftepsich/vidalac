Ext.ns( 'Apps' );

Apps.<?=$this->name?> = Ext.extend(RadDesktop.Module, {
    autoStart: true,
    title: '<?=$this->title?>',
    appChannel: '/desktop/modules<?=$this->url()?>',

    eventfind: function (ev) {
        this.createWindow();
        this.grid.store.load({
            params: this.grid.buildFilter(0, 'Id', ev.value),
            callback: function() {
                this.getSelectionModel().selectFirstRow();
            },
            scope: this.grid
        });
    },

    eventsearch: function (ev) {
        this.createWindow();
        var p = this.grid.builFilter(0, ev.field, ev.value);
        this.grid.store.load({params:p});
    },

    eventlaunch: function(ev) {
        this.createWindow();
        this.grid.store.load();
    },

    createWindow: function() {
        var win = app.desktop.getWindow(this.id+'-win');
        if ( !win ) {
            this.grid =  Ext.ComponentMgr.create(<?=$this->grid?>);
            win = this.create();
        }
        win.show();
    },



    create: function() {
        this.createSecGrids();
        defaultWinCfg = {
            id: this.id+'-win',
            title: this.title,
            iconCls: 'icon-grid',
            border:  false,
            shim: false,
            animCollapse: false,
            layout: 'fit',
            width: 1100,
            height:500,
            items: [
                this.renderWindowContent()
            ]
        };
        return app.desktop.createWindow(defaultWinCfg);
    },

    renderWindowContent: function () {
        return {
            layout : 'border',
            bodyStyle:'background:rgb(214, 214, 214)',
            border : false,
            items : [{
                    region : 'west',
                    layout: 'fit',
                    width : 360,
                    split: true,
                    items: [
                        this.grid
                    ]
                },{
                    region : 'center',
                    layout: 'fit',
                    items: [
                        this.renderTabs()
                    ]
                }]
        }
    },
    createSecGrids: function() {
        this.gridVCL_DetalleGenerico    = Ext.ComponentMgr.create(<?=$this->gridVCL_DetalleGenerico?>);
        this.gridVCL_DetalleConvenio    = Ext.ComponentMgr.create(<?=$this->gridVCL_DetalleConvenio?>);
        this.gridVCL_DetalleEmpresa     = Ext.ComponentMgr.create(<?=$this->gridVCL_DetalleEmpresa?>);
        this.gridVCL_DetalleCategoria   = Ext.ComponentMgr.create(<?=$this->gridVCL_DetalleCategoria?>);
        this.gridVCL_DetalleGrupo       = Ext.ComponentMgr.create(<?=$this->gridVCL_DetalleGrupo?>);
        this.gridVCL_DetallePuesto      = Ext.ComponentMgr.create(<?=$this->gridVCL_DetallePuesto?>);
        this.gridTipoLiquidaciones      = Ext.ComponentMgr.create(<?=$this->gridTipoLiquidaciones?>);

        this.gridVCL_DetalleGenerico.abmForm.reloadGridOnClose = true;  
        this.gridVCL_DetalleConvenio.abmForm.reloadGridOnClose = true;  
        this.gridVCL_DetalleEmpresa.abmForm.reloadGridOnClose = true;  
        this.gridVCL_DetalleCategoria.abmForm.reloadGridOnClose = true;  
        this.gridVCL_DetalleGrupo.abmForm.reloadGridOnClose = true;  
        this.gridVCL_DetallePuesto.abmForm.reloadGridOnClose = true;          
    },

    renderTabs: function ()
    {
        return {
            xtype: 'tabpanel',
            deferredRender : false,
            activeTab : 0,
            enableTabScroll: true,
            items: [
                this.gridTipoLiquidaciones,
                this.gridVCL_DetalleGenerico,
                this.gridVCL_DetalleConvenio,
                this.gridVCL_DetalleEmpresa,
                this.gridVCL_DetalleCategoria,
                this.gridVCL_DetalleGrupo,
                this.gridVCL_DetallePuesto
            ]
        }
    }


});

new Apps.<?=$this->name?>();