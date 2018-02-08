Ext.ns( 'ERP' );
ERP.depositoResaltadorMenu = Ext.extend(Ext.menu.Item, {
    color: 'rojo',
    deposito: null,
     /**
     * Inicializa el componente
     */
    initComponent: function() {
        this.menu = {        // <-- submenu by nested config object
            items: [{
                text: 'Articulo',
                scope: this,
                iconCls: 'x-btn-text-icon',
                icon:    'images/modulos/Articulos16.png',
                handler: function() {
                    win = app.desktop.createWindow({
                        layout      : 'form',
                        width       : 570,
                        height      : 100,
                        minimizable: false,
                        collapsible: false,
                        constrain:   false,
                        maximizable: false,
                        title       : 'Resaltar articulos',
                        plain       : true,
                        modal		: true,
                        border	 	: false,
                        constrain	: true,
                        bodyStyle   : 'padding:15px',
                        items       : {
                            xtype: 'xcombo',
                            //width: 400,
                            iconCls: 'no-icon',
                            tpl : RadTemplates.articuloAlmacenes,
                            anchor: '98%',
                            minChars: 3,
                            displayField: 'Descripcion',
                            autocomplete: true,
                            selectOnFocus: true,
                            pageSize: 20,
                            forceSelection: true,
                            forceReload: true,
                            loadingText: 'Cargando...',
                            lazyRender: true,
                            triggerAction: 'all',
                            fieldLabel: 'Articulo',
                            searchField: 'Descripcion',
                            editable: true,
                            typeAhead: false,
                            valueField: 'Id',
                            autoLoad: false,
                            allowBlank: true,
                            //name: '',
                            store:
                                new Ext.data.JsonStore ({
                                    url: 'datagateway/combolist/model/ArticulosGenericos/m/Base',
                                    autoLoad: true,
                                    root: 'rows',
                                    idProperty: 'Id',
                                    storeId: 'ArticulosFiltrosAlmacenes',
                                    totalProperty: 'count'
                                }),
                             listeners:{
                                 scope: this,
                                 'select': function (combo) {
                                    
                                    //Ext.getCmp('celdas').filtros.porArticulo(Ext.getCmp('filterField').getValue());
                                    this.deposito.resaltar(combo.getValue(),'esArticulo','mmi-filtro'+this.color,'',this.color);
                                    win.close();
                                }
                            }
                        }
                    });
                    win.show();
                }
            },{
                text: 'Creados Desde',
                scope: this,
                iconCls: 'x-btn-text-icon',
                icon:    'images/modulos/Depositos16.png',
                menu: new Ext.menu.DateMenu({
                    scope: this,
                    listeners : {
                        select : {
                            fn:function(datepicker, date){
                                this.deposito.resaltar(date,'esCreadoDespues','mmi-filtro'+this.color,'',this.color);
                            },
                            scope: this
                        }
                    }
                })
            },{
                text: 'Vencidos',
                scope: this,
                iconCls: 'x-btn-text-icon',
                icon:    'images/modulos/Depositos16.png',
                menu: new Ext.menu.DateMenu({
                    scope: this,
                    listeners : {
                        select : {
                            fn:function(datepicker, date){
                                this.deposito.resaltar(date,'esVencidosDespues','mmi-filtro'+this.color,'',this.color);
                            },
                            scope: this
                        }
                    }
                })
            },{
                text: 'Quitar Resaltado',
                scope: this,
                iconCls: 'x-btn-text-icon',
                icon:    'images/cancel.png',
                handler: function () {
                    this.deposito.quitarResaltado(this.color);
                }
            }
            ]
        };

        ERP.depositoResaltadorMenu.superclass.initComponent.call(this);
    }
});