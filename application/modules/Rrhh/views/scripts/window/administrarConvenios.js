Ext.ns( 'Apps' );

Apps.<?=$this->name?> = Ext.extend(RadDesktop.Module, {
    autoStart: true,
    title: '<?=$this->title?>',
    appChannel: '/desktop/modules<?=$this->url()?>',

    requires: [
        '/direct/Rrhh/Convenios?javascript',
        '/direct/Rrhh/LiquidacionesTablas?javascript'
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
        this.createBotton();
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

    /**
     * Le agrego botones al toolbar
     */
    createBotton: function () {

        var bottonClonConvenios =  {
            text: 'Clonar Convenio',
            icon: 'images/date_add.png',
            cls: 'x-btn-text-icon',
            scope: this,
            handler: function () {
                sel = this.grid.getSelectionModel().getSelected();

//                selCatDet = this.gridCategoriasDetalles.getSelectionModel().getSelected();

                if (!sel) {
                    app.publish('/desktop/showWarning', 'Debe Seleccionar un convenio');
                    return;
                }

//                if (!selCatDet) {
//                    app.publish('/desktop/showWarning', 'Debe Seleccionar una Periodo Base (Categoria Valores)');
//                    return;
//                }

                var id = sel.data.Id;

                // creo la ventana para elegir el nuevo periodo con el nuevo valor
                var clonConvenioForm;

                clonConvenioForm = Ext.ComponentMgr.create({
                    xtype: 'fieldset',
                    bodyStyle:'padding:5px;',
                    autoHeight: true,
                    border: false,
                    items:[
                        {
                            layout: 'form',
                            autoHeight: true,
                            border: false,
                            items: [
                                { xtype:'textfield', fieldLabel:'Nombre del Convenio', ref:'../../nombre', width:'90%', allowBlank: true }
                            ]
                        }]
                });

                if (!this.nuevoClonConvenio) {
                    this.nuevoClonConvenio = new app.desktop.createWindow({
                        layout: 'fit',
                        defaults: {border:false},
                        width: 550,
                        height: 120,
                        border: false,
                        closeAction: 'hide',
                        // bodyStyle: 'padding: 5px;',
                        //plain: true,
                        modal: true,
                        title: 'Nuevo Convenio',
                        autoDestroy: true,
                        items: [
                            clonConvenioForm
                        ],
                        buttons: [{
                            text: 'Guardar',
                            scope: this,
                            handler: function() {
                                var Nombre  = this.nuevoClonConvenio.nombre;
                                Models.Rrhh_Model_ConveniosMapper.generarClonConvenio(Nombre.getValue(), id, function(result,e){
                                    if (e.status) {
                                        Ext.MessageBox.hide();
                                        this.grid.store.reload();
                                    }
                                });

                                this.nuevoClonConvenio.hide();

                            }
                        },{
                            text: 'Cancelar',
                            handler: function(){
                            this.nuevoClonConvenio.hide();
                            },
                        scope: this
                        }]
                    });
                }
                // Mostramos la ventana
                this.nuevoClonConvenio.show();
            }
        };




        var bottonConvenios =  {
            text: 'Nuevo Ajuste',
            icon: 'images/date_add.png',
            cls: 'x-btn-text-icon',
            scope: this,
            handler: function () {
                sel = this.grid.getSelectionModel().getSelected();

                selCatDet = this.gridCategoriasDetalles.getSelectionModel().getSelected();

                if (!sel) {
                    app.publish('/desktop/showWarning', 'Debe Seleccionar un convenio');
                    return;
                }

                if (!selCatDet) {
                    app.publish('/desktop/showWarning', 'Debe Seleccionar una Periodo Base (Categoria Valores)');
                    return;
                }

                var id = sel.data.Id;

                // creo la ventana para elegir el nuevo periodo con el nuevo valor
                var periodoForm;

                periodoForm = Ext.ComponentMgr.create({
                    xtype: 'fieldset',
                    bodyStyle:'padding:5px;',
                    autoHeight: true,
                    border: false,
                    items:[
                        {
                            layout: 'form',
                            autoHeight: true,
                            border: false,
                            items: [
                                { xtype:'xdatefield', fieldLabel:'Inicio del Periodo', ref:'../../fecha', width:'90%',dateFormat: 'Y-m-d', allowBlank: true },
                                { xtype:'numberfield', fieldLabel:'Incremento Basico Fijo', ref:'../../valorb', width:'80%', allowBlank: false, allowNegative:false },
                                { xtype:'numberfield', fieldLabel:'Incremento Basico %', ref:'../../valorbp', width:'80%', allowBlank: false, allowNegative:false },
                                { xtype:'numberfield', fieldLabel:'Incremento No Rem. Fijo', ref:'../../valornr', width:'80%', allowBlank: false, allowNegative:false },
                                { xtype:'numberfield', fieldLabel:'Incremento No Rem. %', ref:'../../valornrp', width:'80%', allowBlank: false, allowNegative:false }
                            ]
                        }]
                });

                if (!this.nuevoPeriodo) {
                    this.nuevoPeriodo = new app.desktop.createWindow({
                        layout: 'fit',
                        defaults: {border:false},
                        width: 280,
                        height: 260,
                        border: false,
                        closeAction: 'hide',
                        // bodyStyle: 'padding: 5px;',
                        //plain: true,
                        modal: true,
                        title: 'Nuevo Convenio',
                        autoDestroy: true,
                        items: [
                            periodoForm
                        ],
                        buttons: [{
                            text: 'Guardar',
                            scope: this,
                            handler: function() {
                                var Fecha           = this.nuevoPeriodo.fecha;
                                var FechaPeriodo    = Fecha.getValue().format("Y-m-d");
                                var ValorB          = this.nuevoPeriodo.valorb;
                                var ValorBP          = this.nuevoPeriodo.valorbp;
                                var ValorNR         = this.nuevoPeriodo.valornr;
                                var ValorNRP         = this.nuevoPeriodo.valornrp;

                                Models.Rrhh_Model_ConveniosMapper.generarDetallesConvenio(FechaPeriodo, id, ValorB.getValue(),ValorBP.getValue(), ValorNR.getValue(),ValorNRP.getValue(), selCatDet.data.FechaDesde, selCatDet.data.FechaHasta, function(result,e){
                                    if (e.status) {
                                        Ext.MessageBox.hide();
                                        //this.gridCategorias.store.reload();
                                    }
                                });

                                this.nuevoPeriodo.hide();

                            }
                        },{
                            text: 'Cancelar',
                            handler: function(){
                            this.nuevoPeriodo.hide();
                            },
                        scope: this
                        }]
                    });
                }
                // Mostramos la ventana
                this.nuevoPeriodo.show();
            }
        };

        var funcionBottonTablas = function () {
            sel = this.getSelectionModel().getSelected();

            if (!sel) alert("Debe Seleccionar un registro de la tabla");

            var id = sel.data.Id;

            // creo la ventana para elegir el nuevo periodo con el nuevo valor
            var periodoTForm;

            periodoTForm = Ext.ComponentMgr.create({
                xtype: 'fieldset',
                bodyStyle:'padding:5px;',
                autoHeight: true,
                border: false,
                items:[
                    {
                        layout: 'form',
                        autoHeight: true,
                        border: false,
                        items: [
                            { xtype:'xdatefield', fieldLabel:'Inicio del Periodo', ref:'../../fecha', width:'90%',dateFormat: 'Y-m-d', allowBlank: true },
                            { xtype:'numberfield', fieldLabel:'Incremento', ref:'../../valor', width:'80%', allowBlank: false, allowNegative:false },
                            { xtype:'checkbox', fieldLabel:'Porcentaje', ref:'../../porcentaje', width:'90%'}
                        ]
                    }]
            });

            this.nuevoPeriodoT = new app.desktop.createWindow({
                layout: 'fit',
                defaults: {border:false},
                width: 280,
                height: 180,
                border: false,
                closeAction: 'hide',
                // bodyStyle: 'padding: 5px;',
                //plain: true,
                modal: true,
                title: 'Crear detalles con un nuevo Periodo',
                autoDestroy: true,
                items: [
                    periodoTForm
                ],
                buttons: [{
                    text: 'Guardar',
                    scope: this,
                    handler: function() {
                        var Fecha           = this.nuevoPeriodoT.fecha;
                        var FechaPeriodo    = Fecha.getValue().format("Y-m-d");
                        var Valor           = this.nuevoPeriodoT.valor;
                        var Porcentaje      = this.nuevoPeriodoT.porcentaje;

                        Models.Rrhh_Model_LiquidacionesTablasMapper.generarDetallesTablas(FechaPeriodo, id, Valor.getValue(), Porcentaje.getValue(), function(result,e){
                            if (e.status) {
                                Ext.MessageBox.hide();
                                //this.gridCategorias.store.reload();
                            }
                        });

                        this.nuevoPeriodoT.hide();
                    }
                },{
                    text: 'Cancelar',
                    handler: function(){
                        this.nuevoPeriodoT.hide();
                    },
                    scope: this
                }]
            });

            // Mostramos la ventana
            this.nuevoPeriodoT.show();
        }      

        var bottonTablasRangos =  {
            text: 'Nuevo Detalle',
                icon: 'images/date_add.png',
                cls: 'x-btn-text-icon',
                scope: this.gridTablasRangos,
                handler: funcionBottonTablas
        };

        var bottonTablasEscalares =  {
            text: 'Nuevo Detalle',
                icon: 'images/date_add.png',
                cls: 'x-btn-text-icon',
                scope: this.gridTablasEscalares,
                handler: funcionBottonTablas
        };

        var bottonTablasGrupos =  {
            text: 'Nuevo Detalle',
                icon: 'images/date_add.png',
                cls: 'x-btn-text-icon',
                scope: this.gridTablasGrupos,
                handler: funcionBottonTablas
        };

        this.grid.getTopToolbar().addButton([
            {
                xtype:'tbseparator'
            },
            bottonClonConvenios
        ]);

        this.gridCategorias.getTopToolbar().addButton([
            {
                xtype:'tbseparator'
            },
            bottonConvenios
        ]);
        this.gridTablasRangos.getTopToolbar().addButton([
            {
                xtype:'tbseparator'
            },
            bottonTablasRangos
        ]);
        this.gridTablasEscalares.getTopToolbar().addButton([
            {
                xtype:'tbseparator'
            },
            bottonTablasEscalares
        ]);
        this.gridTablasGrupos.getTopToolbar().addButton([
            {
                xtype:'tbseparator'
            },
            bottonTablasGrupos
        ]);

    },

    renderWindowContent: function () {
        return {
            layout : 'border',
            bodyStyle:'background: #D6D6D6',
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
        this.gridCategorias                 = Ext.ComponentMgr.create(<?php echo $this->gridCategorias ?>);
        this.gridCategoriasDetalles         = Ext.ComponentMgr.create(<?php echo $this->gridCategoriasDetalles ?>);
        this.gridLicencias                  = Ext.ComponentMgr.create(<?php echo $this->gridLicencias ?>);
        this.gridTablasRangos               = Ext.ComponentMgr.create(<?php echo $this->gridTablasRangos ?>);
        this.gridTablasEscalares            = Ext.ComponentMgr.create(<?php echo $this->gridTablasEscalares ?>);
        this.gridTablasGrupos               = Ext.ComponentMgr.create(<?php echo $this->gridTablasGrupos ?>);
        this.gridTablasRangosDetalles       = Ext.ComponentMgr.create(<?php echo $this->gridTablasRangosDetalles ?>);
        this.gridTablasEscalaresDetalles    = Ext.ComponentMgr.create(<?php echo $this->gridTablasEscalaresDetalles ?>);
        this.gridTablasGruposDetalles       = Ext.ComponentMgr.create(<?php echo $this->gridTablasGruposDetalles ?>);
    },

    renderTabs: function () {
        return {
            xtype: 'tabpanel',
            deferredRender : false,
            activeTab : 0,
            enableTabScroll: true,
            defaults: { bodyStyle:'background: #D6D6D6'},
            items: [
                {
                    layout: 'border',
                    title: 'Categorias',
                    defaults: { layout: 'fit' },
                    items: [
                        {
                            region: 'north',
                            height: 280,
                            layout: 'fit',
                            split: true,
                            border: false,
                            items: this.gridCategorias
                        },
                        {
                            region: 'center',
                            layout: 'fit',
                            border: false,
                            items: [
                                this.gridCategoriasDetalles
                            ]
                        }
                    ]
                },
                {
                    layout: 'border',
                    title: 'Tablas por Rango',
                    defaults: { layout: 'fit' },
                    items: [
                        {
                            region: 'north',
                            height: 280,
                            layout: 'fit',
                            split: true,
                            border: false,
                            items: this.gridTablasRangos
                        },
                        {
                            region: 'center',
                            layout: 'fit',
                            border: false,
                            items: [
                                this.gridTablasRangosDetalles
                            ]
                        }
                    ]
                },
                {
                    layout: 'border',
                    title: 'Tablas Escalares',
                    defaults: { layout: 'fit' },
                    items: [
                        {
                            region: 'north',
                            height: 280,
                            layout: 'fit',
                            split: true,
                            border: false,
                            items: this.gridTablasEscalares
                        },
                        {
                            region: 'center',
                            layout: 'fit',
                            border: false,
                            items: [
                                this.gridTablasEscalaresDetalles
                            ]
                        }
                    ]
                },
                {
                    layout: 'border',
                    title: 'Tablas Categorias',
                    defaults: { layout: 'fit' },
                    items: [
                        {
                            region: 'north',
                            height: 280,
                            layout: 'fit',
                            split: true,
                            border: false,
                            items: this.gridTablasGrupos
                        },
                        {
                            region: 'center',
                            layout: 'fit',
                            border: false,
                            items: [
                                this.gridTablasGruposDetalles
                            ]
                        }
                    ]
                },
                this.gridLicencias
            ]
        }
    }
});

new Apps.<?=$this->name?>();