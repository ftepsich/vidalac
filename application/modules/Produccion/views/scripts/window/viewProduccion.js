Ext.ns( 'Apps' );

Apps.<?=$this->name?> = Ext.extend(RadDesktop.Module, {

    title: '<?=$this->title?>',
    appChannel: '/desktop/modules<?=$this->url()?>',
    requires: [
        '/direct/Produccion/Producciones?javascript'
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
            this.createGrid();
            
            win = this.create();
            
            this.initEstadisticaPorTurno()
            // Si destruimos la ventana padre tenemos q destruir la hija tambien
            win.on('destroy',function(){
                this.abmWindow.destroy();
            },this);
        }
        win.show();
    },

    /**
     * Crea la ventana del Abm
     */
    createEditorWindow: function () {
//        if (this.abmWindow) return;
        this.abmWindow = app.desktop.createWindow({
            autoHideOnSubmit: false,
            width  : 1000,
            height : 520,
            animateTarget : null,
            maximized: true,
            closeAction : 'hide',
            modal: true,
            border : false,
            layout : 'fit',
            ishidden : true,
            title  : 'Produccion',
            plain  : true,
            items  : this.renderViewWindow()
       });
    },
   

    /**
     * Creamos la grilla principal
     */
    createGrid: function () {
        this.grid = Ext.ComponentMgr.create(<?=$this->grid?>);
        this.grid.getTopToolbar().addButton([
            {
                text:	'Ver Orden',
                icon:	'images/printer.png',
                cls	:	'x-btn-text-icon',
                scope:  this.grid,
                handler: function () {
                    selected = this.getSelectionModel().getSelected();
                    if (selected) {
                        this.publish('/desktop/modules/Window/birtreporter', {
                            action: 'launch',
                            template: 'OrdenDeProduccion',
                            id: selected.id,
                            output: 'html',
                            width:  900,
                            height: 800
                        });
                    } else {
                        Ext.Msg.alert('Atencion', 'Seleccione un registro para ver el reporte');
                    }
                }
            },
            { xtype:'tbseparator' },
            {
                text:	'Ver Actividad',
                icon:	'images/bullet_wrench.png',                
                cls	:'x-btn-text-icon',
                scope:  this,
                handler:	function () {
                    selected = this.grid.getSelectionModel().getSelected();
                    if (selected) {
                        this.startProduction(selected);
                    }
                }
            }
            
        ]);
    },
    
    startProduction: function(record) {
        this.createEditorWindow();
        this.OrdenDeProduccion = record;
        var rt1 = {};
        rt1['pfilter[0][field]']       = 'OrdenDeProduccion';
        rt1['pfilter[0][data][value]'] = this.OrdenDeProduccion.data.Id;
        rt1['pfilter[0][data][type]']  = 'numeric';
        rt1['pfilter[0][data][comparison]'] = 'eq';
        
        
        
        this.turnosGrid.store.baseParams = rt1;
        this.turnosGrid.store.load();
        
        this.ordenesProduccionMmiGrid.store.baseParams = rt1;
        this.ordenesProduccionMmiGrid.store.load();

        this.gridLog.store.baseParams = rt1;
        this.gridLog.store.load();

        this.produccionesMmis.store.baseParams = rt1;
        this.produccionesMmis.store.load();
        
        this.actualizarGrafico();
        
        this.gridProduccionPorTurno.store.baseParams.id = this.OrdenDeProduccion.data.Id;
        this.gridProduccionPorTurno.store.load();
        
        this.showDetails(null,this.detailsTemplate);
        
        this.abmWindow.show();
        
    },
    
    initEstadisticaPorTurno: function () {
        store = new Ext.data.JsonStore({
            // store configs
            autoDestroy: true,
            autoLoad: false,
            url: '/Produccion/viewProduccion/getproduccionporturno/',
            remoteSort: false,
            sortInfo: {
                field: 'Produccion',
                direction: 'ASC'
            },
            storeId: 'myStore',

            // reader configs
            idProperty: 'id',
            root: 'rows',
            totalProperty: 'count',
            fields: [{
                name: 'Id'
            },{
                name: 'Numero'
            }, {
                name: 'Produccion'
            }, {
                name: 'Cantidad',
                type: 'float',
               
            }, {
                name: 'Comienzo',
                type: 'date',
                dateFormat: 'Y-m-d H:i:s'
            }, {
                name: 'Final',
                type: 'date',
                dateFormat: 'Y-m-d H:i:s'
            }]
        });

        this.gridProduccionPorTurno = new Ext.grid.GridPanel({
            flex:1,
            store: store,
            border: false,
            plugins: [
                new Ext.ux.grid.GridSummary()
            ],
            viewConfig: {
                forceFit: true
            },
            region: 'center',
//            title: 'Produccion Por Turno',
            columns: [
                 {
                    header   : 'Turno',
                    width    : 20,
                    dataIndex: 'Numero',
                    sortable : false
                },
                {
                    header   : 'Comienzo',
                    width   : 80,
                    dataIndex: 'Comienzo',
                    sortable : false,
                    xtype: 'datecolumn',
                    format: 'd/m/Y H:i:s'
                },{
                    header   : 'Final',
                    width    : 80,
                    sortable : false,
                    dataIndex: 'Final',
                    xtype: 'datecolumn',
                    format: 'd/m/Y H:i:s'
                },{
                    header   : 'Cantidad',
                    dataIndex: 'Cantidad',
                    align: 'right',
                    width    : 70,
                    summaryType: 'sum',
                    sortable : false
                }
            ]
        });
    },
    
    renderViewWindow: function() {
        this.puestosDeTrabajo = new Ext.data.JsonStore({
            // store configs
            autoLoad: false,
            autoDestroy: true,
            url: '/default/datagateway/list/model/LineasDeProduccionesPersonas/m/Produccion/order/Actividad'
        }); 
        
        this.puestosDeTrabajo.on('load', function() {
            var actividad, actividadRow, rowIndex;
            
            this.puestosDeTrabajo.data.each(function(item, idx, number){
                if (actividad != item.data.Actividad) {
                    actividad  = item.data.Actividad;
                    actividadRow = this.actividadesGrid.store.getById(actividad);
                    rowIndex     = this.actividadesGrid.store.indexOf(actividadRow);
                }
                htmlRow = this.actividadesGrid.getView().getRow(rowIndex);
                body = Ext.get(htmlRow).child('.actividades-target');
                body.update(item.data.Persona_cdisplay+', Dni: '+item.data.PersonasDni+'<br>'+body.dom.innerHTML);
   
            }, this);
        },this);
        
        // creo todas las grillas
        this.actividadesGrid = Ext.ComponentMgr.create(<?=$this->actividadesGrid?>);
        
        this.ordenesProduccionMmiGrid = Ext.ComponentMgr.create(<?=$this->ordenesProduccionMmiGrid?>);
        this.gridLog = Ext.ComponentMgr.create(<?=$this->gridLog?>);
        this.produccionesMmis = Ext.ComponentMgr.create(<?=$this->produccionesMmis?>);
        this.turnosGrid = Ext.ComponentMgr.create(<?=$this->turnosGrid?>);
        
        this.turnosGrid.getSelectionModel().on('rowselect', function (t,i,row){
            this.Produccion = row.data.Id;
            this.actividadesGrid.store.load();
        },this);
        
        
        this.actividadesGrid.store.on('load', function() {
            var rt = {};
            rt['pfilter[0][field]'] 	  = 'Produccion';
            rt['pfilter[0][data][value]'] = this.Produccion;
            rt['pfilter[0][data][type]']  = 'numeric';
            rt['pfilter[0][data][comparison]'] = 'eq';
            this.puestosDeTrabajo.baseParams = rt;
            this.puestosDeTrabajo.load();
        }, this);
        
        this.graficoData = [
            ['Terminado',0],
            ['Falta',100],
            
        ];
        // Store para el grafico
        this.gstore = new Ext.data.ArrayStore({
            // store configs
            autoDestroy: true,
            storeId: 'myStore',
            data: this.graficoData, 
            idIndex: 0,  
            fields: [
               'desc',
               {name: 'cuanto', type: 'float'}
            ]
        });
                
        // Template de Detalles Del MMi
        this.detailsTemplate = new Ext.XTemplate(
                '<div class="panelDeDetalles">',
                        '<tpl for=".">',
                                '<h3>{Mmi_cdisplay}</h3>',
                                '<div class="detalle">',
                                '<b>Producto:</b>',
                                '<span>{ProductoDescripcion}</span>',
                                '<b>Quedan:</b>',
                                '<span>{[Ext.util.Format.number(values.CantidadEnUnidadDeMedida,"0.00")]} {UnidadDeMedidaPDescripcion}</span>',
                                '<b>De:</b>',
                                '<span>{[Ext.util.Format.number(values.CantidadEnUnidadDeMedidaO,"0.00")]} {UnidadDeMedidaPDescripcion}</span></div>',
                                
                        '</tpl>',
                '</div>'
        );
        this.detailsTemplate.compile();
        
         // Template de Detalles Del Log de movimientos MMi
        this.detailsLogMmiTemplate = new Ext.XTemplate(
                '<div class="panelDeDetalles" >',
                        '<tpl for=".">',
                                '<h3>{MmisIdentificador}</h3>',
                                '<div class="detalle">',
                                '<b>Articulo:</b>',
                                '<span style="font-size:110%">{ArticuloDescripcion}</span>',
                                '<b>Producto:</b>',
                                '<span style="font-size:110%">{ProductoDescripcion}</span>',
                                '<b>Movimiento:</b>',
                                '<span style="font-size:110%">{[Ext.util.Format.number(values.CantidadEnUnidadDeMedida,"0.00")]} {UnidadDeMedidaPDescripcion}</span>',
                                '<b>Movimiento Articulo:</b>',
                                '<span style="font-size:110%">{[Ext.util.Format.number(values.Cantidad,"0.000")]}</b> {DescPackaging}</span></div>',
                                
                        '</tpl>',
                '</div>'
        );
        this.detailsTemplate.compile();
        
        // Mostramos los detalles para La grilla de Mmis de materia prima
        this.ordenesProduccionMmiGrid.getSelectionModel().on('rowselect', function (t,  rowIndex, r ) {
            this.showDetails(r,this.detailsTemplate);
        }
        ,this);
        this.ordenesProduccionMmiGrid.getSelectionModel().on('rowdeselect', function (t,  rowIndex, r )  {
            this.showDetails(null,this.detailsTemplate);
        }
        ,this);
        
        // Mostramos los detalles para La grilla de Mmis movimientos
        this.gridLog.getSelectionModel().on('rowselect', function (t,  rowIndex, r ) {
            this.showDetails(r,this.detailsLogMmiTemplate);
        }
        ,this);
        this.gridLog.getSelectionModel().on('rowdeselect', function (t,  rowIndex, r )  {
            this.showDetails(null,this.detailsLogMmiTemplate);
        }
        ,this);
        
        return {
            layout: 'border',
            items: [{
                xtype: 'tabpanel',
                activeTab: 0,
                region:'center',
                items: [
                    this.renderResumen(),
                    {
                        layout: 'border',
                        title: 'Operarios',
                        items: [
                            {
                                title: 'Turnos',
                                region: 'west',
                                layout:'fit',
                                width: 400,
                                margins: '0 5 0 0',
                                items: this.turnosGrid 
                            },
                            this.actividadesGrid
                        ]
                    },
                    this.ordenesProduccionMmiGrid,
                    this.gridLog,
                    this.produccionesMmis
                ]
                
            },{
                 region: 'east',
                 width: 270,
                 layout: 'vbox',
                 layoutConfig: {
                    align : 'stretch',
                    pack  : 'start',
                 },
                 items: [
                    {
                        id:'produccionPanelDetalleDeMmi',
                        flex: 1,
                        bodyStyle: 'background-color:white;border-bottom:1px;',
                        border: false
                    },
                    {
                        xtype: 'piechart',
                        flex:1,
                        dataField: 'cuanto',
                        categoryField: 'desc',
                        store: this.gstore,
                        series: [{
                            style: { colors: ['#1FC071', '#B2000F'] }
                        }],
                        extraStyle:
                        {
                            legend:
                            {
                                display: 'bottom',
                                padding: 5,
                                font:
                                {
                                    family: 'Tahoma',
                                    size: 13
                                }
                            }
                        }

                    }
                 ]
            }]
        };
        
    },
    
    renderResumen: function(){
        return {
            title: 'Resumen',
            layout:'fit',
            border:false,
            items: [{
                border:false,   
                layout: 'hbox',
                layoutConfig : {
                    type : 'hbox',
                    align : 'stretch',
                    pack : 'start'
                },
                defaults : {
                    flex : 1
                },
                items: [
                    {
                        border:true,
                        margins: '0 5 0 0',
                        layout: 'vbox',
                        title: 'Producción Por Turno',
                        layoutConfig : {
                            type : 'vbox',
                            align : 'stretch',
                            pack : 'start'
                        },
                        defaults : {
                            flex : 1
                        },
                        items: [
                            this.gridProduccionPorTurno,
                            new Ext.chart.ColumnChart({
                                store: this.gridProduccionPorTurno.store,
                                //url:'../ext-3.0-rc1/resources/charts.swf',
                                series: [{
                                    type: 'column',
                                    displayName: 'Cantidad',
                                    yField: 'Cantidad',
                                    xField: 'Numero'

                                }],
            //                    tipRenderer : function(chart, record, index, series) {
            //                        return series.displayName+'\nPeriodo: ' + record.data.Mes + '/' + record.data.Anio + "\n" +
            //                               'Total: ' + Ext.util.Format.usMoney(record.get(series.yField));
            //                    },
                                extraStyle: {
                                    padding: 10,
                                    animationEnabled: true,
                                    legend:{
                                        display:'bottom'
                                    },
                                    xAxis: {
                                        color: 0x3366cc,
                                        majorGridLines: {size: 1, color: 0xdddddd},

                                    },
                                    yAxis: {
                                        color: 0x3366cc,
                                        majorTicks: {color: 0x3366cc, length: 4},
                                        minorTicks: {color: 0x3366cc, length: 2},
                                        majorGridLines: {size: 1, color: 0xdddddd}

                                    }
                                }

                            })
                        ]
                    },
                    {
                        border:true,
                        title: 'Utilizado'
                    }
                ]
            }]
            
        };
    },
    
    showDetails : function(selected, template){
       
        var detailEl = Ext.getCmp('produccionPanelDetalleDeMmi').body;
        if (selected){
            detailEl.hide();
            template.overwrite(detailEl, selected.data);
            detailEl.slideIn('l', {stopFx:true,duration:.2});
        }else{
            detailEl.update('');
        }
    },
    


   
    
    actualizarGrafico: function () {
         Models.Produccion_Model_ProduccionesMapper.getTotalProducido(this.OrdenDeProduccion.data.Id,function(result, e) {
            if (e.status) {
                var falta = this.OrdenDeProduccion.data.Cantidad - result;
                if (falta < 0) falta = 0;
                this.graficoData = [
                    ['Terminado',result],
                    ['Falta',falta]
                ];
                this.gstore.loadData(this.graficoData);
            }
        }, this);
    },

    /**
     * Crea la ventana del modulo
     */
    create: function() {
        return app.desktop.createWindow({
            id: this.id+'-win',
            title: this.title,
            width: 1000,
            height:500,
            border: false,

            shim:   false,
            animCollapse: false,
            layout: 'fit',
            items: [
                this.grid
            ]
        });
    }
});

new Apps.<?=$this->name?>();
