Ext.ns( 'Apps' );

Apps.<?=$this->name?> = Ext.extend(RadDesktop.Module, {
    autoStart: true,
    title: '<?=$this->title?>',
    appChannel: '/desktop/modules<?=$this->url()?>',
    requires: [
        '/direct/Rrhh/Convenios?javascript'
    ],

    eventfind: function (ev) {
        this.createWindow();
        var p = this.grid.buildFilter(0, 'Id', ev.value);
        this.grid.store.load({params:p});
    },

    eventsearch: function (ev) {
        this.createWindow();
        var p = this.grid.buildFilter(0, ev.field, ev.value);
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
            layout : 'border',
            bodyStyle:'background:rgb(214, 214, 214)',
            border : false,
            items : [{
                region : 'west',
                layout: 'fit',
                width : 320,
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

    createSecGrids: function () {
        this.gridServicios                          = Ext.ComponentMgr.create(<?php echo $this->gridServicios ?>);
        this.gridServiciosSituacionesDeRevistas     = Ext.ComponentMgr.create(<?php echo $this->gridServiciosSituacionesDeRevistas?>);
        this.gridGanancias                          = Ext.ComponentMgr.create(<?php echo $this->gridGanancias ?>);
        this.gridCtasBancarias                      = Ext.ComponentMgr.create(<?php echo $this->gridCtasBancarias ?>);
        this.gridAreasDeTrabajos                    = Ext.ComponentMgr.create(<?php echo $this->gridAreasDeTrabajos ?>);
        this.gridHsExtras                           = Ext.ComponentMgr.create(<?php echo $this->gridHsExtras ?>);
        this.gridHsTrabajadas                       = Ext.ComponentMgr.create(<?php echo $this->gridHsTrabajadas ?>);
        this.gridFeriadosTrabajados                 = Ext.ComponentMgr.create(<?php echo $this->gridFeriadosTrabajados ?>);
        this.gridZonas                              = Ext.ComponentMgr.create(<?php echo $this->gridZonas ?>);

        this.gridServiciosSituacionesDeRevistas.abmForm.reloadGridOnClose = true;

        this.gridCaracteristicasServicios = new Rad.ParameterTable({
            title: 'Caracteristicas',
            model: 'Servicios',
            module: 'Rrhh',
            masterGrid: this.gridServicios,
            caracteristicas: <?=$this->caracteristicasServicios?>
        });
        // en el row select de Servicios enganchamos el handler de la grilla de caracteristicas
        this.gridServicios.getSelectionModel().on('rowselect',  this.gridCaracteristicasServicios.getRowSelectHandler());

        var combo;
        combo = this.gridServiciosSituacionesDeRevistas.abmForm.getForm().findField('ConvenioLicencia');
        combo.on('beforequery',function(e){
            var servicio = this.gridServicios.getSelectionModel().getSelected();

            combo.store.baseParams.Convenio = servicio.data.Convenio;
        },this);
    },

    renderTabs: function () {
        return {
            xtype: 'tabpanel',
            deferredRender : false,
            activeTab : 0,
            enableTabScroll: true,
            items: [
                {
                    layout: 'border',
                    title: 'Servicios del agente',
                    defaults: { layout: 'fit' },
                    items: [
                        {
                            region: 'north',
                            height: 210,
                            layout: 'fit',
                            split: true,
                            border: false,
                            items: this.gridServicios
                        },
                        {
                            region: 'center',
                            layout: 'fit',
                            border: false,
                            items: [
                                {
                                    xtype: 'tabpanel',
                                    activeTab: 0,
                                    enableTabScroll: true,
                                    deferredRender: false,
                                    items: [
                                        this.gridServiciosSituacionesDeRevistas,
                                        this.gridHsExtras,
                                        this.gridFeriadosTrabajados,
                                        this.gridCaracteristicasServicios,
                                        this.gridHsTrabajadas
                                    ]
                                }
                            ]
                        }
                    ]
                },
                this.gridCtasBancarias,
                this.gridAreasDeTrabajos,
                this.gridGanancias,
                this.gridZonas
            ]
        }
    }
});

new Apps.<?=$this->name?>();