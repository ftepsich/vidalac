Ext.ns( 'Apps' );

Apps.<?=$this->name?> = Ext.extend(RadDesktop.Module, {
    autoStart: true,
    title: '<?=$this->title?>',
    appChannel: '/desktop/modules<?=$this->url()?>',
    requires: [
        '/js/erp/articulosTree.js',
        '/css/arbolArticulo.css'
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

    eventlaunch: function (ev) {
        this.createWindow();
    },

    createWindow: function () {
        var win = app.desktop.getWindow(this.id+'-win');
        if ( !win ) {
            this.grid =  Ext.ComponentMgr.create(<?=$this->grid?>);
            win = this.create();
        }
        win.show();
    },

    create: function () {
        this.createExtraFields();
        this.createSecGrids();
        this.addExtraListeners();

        defaultWinCfg = {
            id: this.id+'-win',
            title: this.title,
            iconCls: 'icon-grid',
            border: false,
            shim: false,
            animCollapse: false,
            layout: 'fit',
            bodyStyle:'background-color:#fefefe',
            closeAction: 'hide',
            width: 1200,
            plain  : false,
            height: 600,
            maximized: true,
            items: [
                this.renderWindowContent()
            ]
        };
        return app.desktop.createWindow(defaultWinCfg);
    },

    renderWindowContent: function () {

        this.arbol = new Rad.ArticulosTreePanel({
            region: 'east',
            width: '700',
            border: true,
            margins: '2',
        });

        this.arbol.on('clickNode', function(t,av,item, e){
            this.mostrarDetalle(av);
        },this);

        return {
            layout: 'border',
            border: false,
            frame: false,
            defaults: { border: false, frame: false },
            items: [
                {
                    region: 'north',
                    height: 350,
                    layout: 'fit',
                    border: true,
                    defaults: { frame: false, border: false },
                    margins: '2',
                    items: this.grid
                },
                // El tabpanel del arbol y otra info de articulos
                this.arbol,
                {
                    region: 'center',
                    title: 'Composición',
                    layout: 'fit',
                    border: true,
                    margins: '2',
                    defaults: { frame: false, border: false },
                    split: true,
                    items: {
                        layout: 'fit',
                        items: [
                            {
                                // Abajo izquierda -> Combo AV / grilla AV Detalles
                                // title: 'av y combo',
                                layout: 'border',
                                border: false,
                                frame: false,
                                defaults: { frame: false, border: false },
                                items: [
                                    {
                                        // Combo
                                        region: 'north',
                                        layout: 'form',
                                        //frame: true,
                                        margins: '3',
                                        height: 22,
                                        items: this.comboArticulosVersiones
                                    },
                                    {
                                        // Artilos Versiones Detalles
                                        region: 'center',
                                        layout: 'fit',
                                        defaults: { frame: false, border: false },
                                        items: this.gridAVDetalles
                                        //items: this.tree
                                    }
                                ]
                            }
                        ]
                    }
                }
            ]
        }
    },

    selectedArt: function (id) {
        this.mostrarDetalle(id);
        this.articuloVerId = id;
        //mostramos el arbol
        this.arbol.load(id);
    },

    mostrarDetalle: function (id) {
        this.comboArticulosVersiones.setValue(id);
        this.gridAVDetalles.setPermanentFilter(0, 'ArticuloVersionPadre', id);
        this.gridAVDetalles.store.load();
        this.gridAVDetalles.enable();
    },

    addExtraListeners: function () {
        // Filtra el combo de Version de acuerdo al articulo seleccionado
        this.grid.getSelectionModel().on('rowselect', function(sm, rowIndex, r) {
            this.comboArticulosVersiones.clearValue();
            this.comboArticulosVersiones.store.baseParams['Articulo'] = r.id;
            this.comboArticulosVersiones.store.load({
                callback: function(r, o , s) {
                    this.selectedArt(r[0].data.Id);
                },
                scope: this
            });

            this.comboArticulosVersiones.enable();
            this.gridAVDetalles.disable();
            //this.comboArticulosVersiones.setValue(1);
        }, this);

        // Filtra el detalle de la version de acuerdo a la seleccionada
        this.comboArticulosVersiones.on('select', function(field, newValue, oldValue) {

            this.selectedArt(newValue.data.Id);

        }, this);

        // Setea el ArticuloVersionPadre (relacion) en el abm de Articulos Versiones Detalles
        this.gridAVDetalles.store.on('write', function() {
            this.gridAVDetalles.abmForm.form.findField('ArticuloVersionPadre').setValue(this.articuloVerId);
            this.arbol.load(this.articuloVerId);
        }, this);

        this.gridAVDetalles.abmForm.on('actioncomplete', function() {
            this.arbol.load(this.articuloVerId);
        }, this);
    },

    createSecGrids: function () {
        this.gridAVDetalles = Ext.ComponentMgr.create(<?=$this->articulosVersionesDetalles?>);

        // this.tree = Ext.ComponentMgr.create(<?= $this->tree ?>);
    },

    createExtraFields: function () {
        this.comboArticulosVersiones = Ext.ComponentMgr.create({
            xtype: "LinkTriggerField",
            link: "/Window/abm/index/m/Base/model/ArticulosVersiones",
            width: '130',
            minChars: 3,
            displayField: "Descripcion",
            autoLoad: false,
            disabled: true,
            autoSelect: true,
            selectOnFocus: true,
            forceSelection: true,
            forceReload: true,
            hiddenName: "ArticulosVersiones",
            loadingText: "Cargando...",
            lazyRender: true,
            searchField: "Descripcion",
            store: new Ext.data.JsonStore({
                id: 0,
                url: "datagateway/combolist/model/ArticulosVersiones/m/Base",
                storeId: "ArticulosVersionesStore"
            }),
            typeAhead: false,
            valueField: "Id",
            autocomplete: true,
            allowBlank: false,
            allowNegative: false,
            fieldLabel: "Version",
            name: "ArticulosVersiones",
            anchor: "97%",
            displayFieldTpl: "{Descripcion} (v {Version})",
            tpl: "<tpl for=\".\"> <div class=\"x-combo-list-item\"> <h3>{Descripcion}</h3><i>Version {Version}</i> </div> </tpl>",
            onBeforeCallApp: function(o) {
                return {
                    action: 'search',
                    field: 'Articulo',
                    value: this.fixedValue
                }
            }
        });
    }

});

new Apps.<?=$this->name?>();