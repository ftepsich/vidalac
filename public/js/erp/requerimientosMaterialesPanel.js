/**
 * Rad Framework
 * Copyright(c) 2013 SmartSoftware
 * @author Martín A. Santangelo
 */

Ext.ns( 'Rad' );

/**
 * Rad.RequerimientosMaterialesPanel
 */
Rad.RequerimientosMaterialesPanel = Ext.extend(Ext.Panel, {
    border : false,

    loadRequerimientos: function(idOrdenProd) {
        this.gridProductos.store.load({params:{id:idOrdenProd}});
    },

    initComponent: function() {

        var store = new Ext.data.GroupingStore({
            url: '/Produccion/ordenesDeProducciones/getrequerimientosproductos',
            reader: new Ext.data.JsonReader({
                idProperty: 'Id',
                messageProperty: 'msg',
                root: 'rows',
                totalProperty: 'count',
                fields:[
                    {name:'ArticuloId', type:'int'},
                    {name:'ArticuloDesc'},
                    {name:'Cantidad', type:'float'},
                    {name:'CantidadTotal',type:'float'},
                    {name:'UnidadDeMedidaId', type:'int'},
                    {name:'UnidadDeMedida', type:'int'},
                    {name:'UnidadDeMedidaR', type:'string'},
                    {name:'TipoDeUnidad', type:'int'},
                    {name:'GrupoId', type:'int'},
                    {name:'Grupo'},
                    {name:'SubGrupoId', type:'int'},
                    {name:'SubGrupo'},
                    {name:'MateriaPrima', type:'int'},
                    {name:'EsContenedor', type:'int'},
                    {name:'TieneFormula', type:'int'},
                    {name:'TipoDeRelacionArticulo', type:'int'}
                ]
            }),
            groupField: 'TipoDeRelacionArticulo',
        });

        this.gridProductos = new Ext.grid.GridPanel({
            store: store,
            view: new Ext.grid.GroupingView({
                groupTextTpl: '{[values.rs[0].data["TipoDeRelacionArticulo"] == 1 ? "Fórmula" : "Packaging"]}',
                showGroupName: false,
                enableNoGroups: false,
                enableGroupingMenu: false,
                hideGroupedColumn: true
            }),
            multiSelect: true,
            emptyText: 'No hay datos calculados',
            reserveScrollOffset: true,
            autoExpandColumn: 'ordenprodcolumnProducto',
            columns: [{
                    header: 'Es Formula',
                    width: 120,
                    dataIndex: 'TipoDeRelacionArticulo',
                    align: 'right',
                },{
                    id: 'ordenprodcolumnProducto',
                    header: 'Producto',
                    width: 120,
                    dataIndex: 'ArticuloDesc'
                },{
                    header: 'Cantidad x U',
                    width: 120,
                    dataIndex: 'Cantidad',
                    align: 'right',
                },{
                    header: 'Cantidad Total',
                    width: 120,
                    dataIndex: 'CantidadTotal',
                    align: 'right',
                },{
                    header: 'Unidad',
                    width: 90,
                    align: 'left',
                    dataIndex: 'UnidadDeMedidaR'
            }]


        });

        this.items = {
            xtype : 'panel',
            region : "center",
            layout : "border",
            border : false,
            items: [{

                    layout: 'fit',
                    border: true,
                    region: 'center',
                    margins: '2 3 2 2',
                    items : [
                        this.gridProductos
                    ]
                },
                {
                    title: 'Formula',
                    region : "east",
                    margins: '2 2 2 2',
                    layout: {
                        type:'vbox',
                        align:'stretch'
                    },
                    width: 200,
                    split: true,
                    items:[
                        {
                            xtype: 'piechart',
                            dataField: 'Cantidad',
                            categoryField: 'ArticuloDesc',
                            store: this.gridProductos.store,
                            flex: true,
                            //filtro antes de graficar
                            refresh: Rad.chartsFilter(function(data, rs) {
                                return data.TipoDeRelacionArticulo == 1;
                            })


                        },

                    ]

                }]
        };

        Rad.RequerimientosMaterialesPanel.superclass.initComponent.call(this);
        this.layout = 'border';
    }

});

