Ext.ns( 'Apps' );

Apps.<?=$this->name?> = Ext.extend(RadDesktop.Module, {
    autoStart: true,
    title: '<?=$this->title?>',
    appChannel: '/desktop/modules<?=$this->url()?>',
    requires:[
        '/css/comboBusqueda.css',
        '/css/consolaliq.css'
    ],

    eventlaunch: function(ev) {
        this.createWindow();
    },

    createWindow: function() {
        var win = app.desktop.getWindow(this.id+'-win');
        if ( !win ) {
            this.createGrid();
            win = this.create();

        }
        win.show();
    },

    createGrid: function(){
        var Concepto = Ext.data.Record.create([
           {name: 'tipo', type: 'string'},
           {name: 'concepto'},
           {name: 'codigo', type: 'string'},
           {name: 'detalle', type: 'string'},           
           {name: 'monto', type: 'float'}
        ]);

        var store = new Ext.data.GroupingStore({
            groupField: 'tipo',
            reader: new Ext.data.ArrayReader({
                idIndex: 1
            },Concepto)
        });

        this.gridConceptos = new Ext.grid.GridPanel({
            store: store,
            view: new Ext.grid.GroupingView({
                // forceFit: true,
                showGroupName: false,
                enableNoGroups: false,
                enableGroupingMenu: false,
                hideGroupedColumn: true
            }),
            plugins: [
                new Ext.ux.grid.GridSummary(),
                new Ext.ux.grid.GroupSummary()
            ],
            multiSelect: true,
            emptyText: 'No hay datos calculados',
            reserveScrollOffset: true,
            autoExpandColumn: 'testliqconcepto',
            columns: [{
                    header: 'Tipo',
                    width: 1,
                    dataIndex: 'tipo'
                },{
                    id:'testliqconcepto',
                    header: 'Concepto',
                    width: 120,
                    dataIndex: 'concepto'
                },{
                    header: 'Código',
                    width: 50,
                    dataIndex: 'codigo',
                    align: 'right',
                },{
                    header: 'Detalle',
                    width: 50,
                    dataIndex: 'detalle',
                    hidden: true
                },{
                    header: 'Monto',
                    renderer: Ext.util.Format.usMoney,
                    width: 90,
                    summaryType: 'sum',
                    summaryRenderer: Ext.util.Format.usMoney,
                    align: 'right',
                    dataIndex: 'monto'
                }],


        });

        this.PanelbuscadorEmpledo =  new Ext.Panel({
            bodyStyle: 'background-color:#d6d6d6;padding:5px',
            html:'<div style="float:right;padding:5px"><img src="/images/general/calc.png"/></div><div>'+
            '<h2 style="margin-bottom:5px;font-size:16px;color:#669b00">Seleccionar Empleado y Servicio</h2>'+
            '<input type="text" size="20" name="testLiqservicio" id="testLiqservicio" /></div>'
        });
        // Custom rendering Template
        var resultTplServ = new Ext.XTemplate(
            '<tpl for="."><div class="search-item">',
                '<h3><span>Alta {FechaAlta:date("j M, Y")}<br />Baja {FechaBaja:date("j M, Y")}</span>{Persona_cdisplay}</h3>',
                '{Empresa_cdisplay} - {Convenio_cdisplay}',
            '</div></tpl>'
        );


        var dsServicios=new Ext.data.JsonStore ({
            url: '/datagateway/combolist/model/Servicios/m/Rrhh/sort/Persona_cdisplay/dir/asc',
            autoLoad: true,
            baseParams:{
                search:'Persona_cdisplay'
            },
            root: 'rows',
            idProperty: 'Id',
            totalProperty: 'count',
        });

        this.PanelbuscadorEmpledo.on('afterrender', function() {
            this.searchServicio = new Ext.form.ComboBox({
                store: dsServicios,
                displayField:'Persona_cdisplay',
                valueField: 'Id',
                typeAhead: false,
                loadingText: 'Buscando...',
                width: 470,
                pageSize:10,
                minChars: 3,
                // hideTrigger:true,
                // triggerConfig: {tag: "img", src: '', cls: "x-form-trigger " + this.triggerClass}
                triggerClass:'x-form-search-trigger',
                tpl: resultTplServ,
                applyTo: 'testLiqservicio',
                itemSelector: 'div.search-item',
            });
        },this);
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
            width: 900,
            height:570,
            items: [
                this.renderWindowContent()
            ]
        };
        return app.desktop.createWindow(defaultWinCfg);
    },
    filtrarCeros: function() {
        this.gridConceptos.store.filterBy(function(r,id){
            return r.data.monto != 0;
        });
    },

    renderConsole: function () {
        this.btnOcultarCeros = new Ext.Button({
            icon: 'images/table_row_insert.png',
            text: 'Ocultar 0',
            tooltip: 'Ocultar conceptos con valor 0',
            enableToggle: true,
            toggleHandler: function(b,s) {
                if (s) {
                    this.filtrarCeros();
                } else {
                    this.gridConceptos.store.clearFilter();
                }
            },
            scope: this
        });
        return {
            layout: 'fit',
            border : true,
            margins: '0 5 0 0',
            flex: 3,
            tbar: [{
                    tooltip: 'Ejecutar (F10)',
                    icon:'images/lightning_go.png',
                    text: 'Calcular',
                    scope: this,
                    handler: function() {
                        this.ejecutar();
                    }
                },'-',{
                    tooltip: 'Limpiar consola',
                    icon:'images/page_delete.png',

                    handler: function () {
                        this.console.update('');
                    },
                    scope: this
                },
                '-',
                this.comboPeriodos,
                {
                    xtype: 'tbfill'
                },
                this.btnOcultarCeros
            ],
            items : [this.console]
        }
    },


    renderWindowContent: function () {


        // this.console = new Ext.form.TextArea({
        //     style      :"color: #E6E1DC; background-color: #3d3d3d; background-image: -moz-linear-gradient(left, #3D3D3D, #333); background-image: -ms-linear-gradient(left, #3D3D3D, #333); background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#3D3D3D), to(#333)); background-image: -webkit-linear-gradient(left, #3D3D3D, #333); background-image: -o-linear-gradient(left, #3D3D3D, #333); background-image: linear-gradient(left, #3D3D3D, #333); background-repeat: repeat-x; border-right: 1px solid #4d4d4d; bext-shadow: 0px 1px 1px #4d4d4d; bolor: #222;font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', 'Consolas', 'source-code-pro', monospace;font-size: 12px;line-height: normal;",
        //     fieldLabel : 'consola',
        //     anchor     : '100%',
        //     height     : '100%',
        //     name       : 'codigo'
        // });
        this.console = new Ext.Panel({
            autoScroll: true,
            bodyCssClass: 'consolaliq',
            bodyStyle      :"padding: 5px;color: #E6E1DC; background-color: #3d3d3d; background-image: -moz-linear-gradient(left, #3D3D3D, #333); background-image: -ms-linear-gradient(left, #3D3D3D, #333); background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#3D3D3D), to(#333)); background-image: -webkit-linear-gradient(left, #3D3D3D, #333); background-image: -o-linear-gradient(left, #3D3D3D, #333); background-image: linear-gradient(left, #3D3D3D, #333); background-repeat: repeat-x; border-right: 1px solid #4d4d4d; bext-shadow: 0px 1px 1px #4d4d4d; bolor: #222;font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', 'Consolas', 'source-code-pro', monospace;font-size: 11px;line-height: normal;"
        });

        this.comboPeriodos = new Ext.ux.form.ComboBox({
            displayFieldTpl: '{TipoDeLiquidacionPeriodo_cdisplay} {[Ext.util.Format.date(Date.parseDate(values.FechaDesde, "Y-m-d"),"d-m-Y")]} - {[Ext.util.Format.date(Date.parseDate(values.FechaHasta,"Y-m-d"),"d-m-Y")]}',
            tpl: '<tpl for="."><div class="x-combo-list-item">{TipoDeLiquidacionPeriodo_cdisplay} {[Ext.util.Format.date(Date.parseDate(values.FechaDesde,"Y-m-d"),"d-m-Y")]} - {[Ext.util.Format.date(Date.parseDate(values.FechaHasta,"Y-m-d"),"d-m-Y")]}</div></tpl>',
            selectOnFocus: true,
            forceSelection: true,
            forceReload: true,
            loadingText: 'Cargando...',
            emptyText: 'Seleccione un Período',
            lazyRender: true,
            triggerAction: 'all',
            valueField: 'Id',
            autoLoad: false,
            store: new Ext.data.JsonStore ({
                url: '/datagateway/combolist/model/LiquidacionesPeriodos/m/Liquidacion',
                autoLoad: true,
                root: 'rows',
                idProperty: 'Id',
                // storeId: 'DepositoStore32sd',
                totalProperty: 'count',
                fields: [ 'Id', 'Anio', 'TipoDeLiquidacionPeriodo','TipoDeLiquidacionPeriodo_cdisplay', {name:'FechaDesde'}, {name:'FechaHasta'}, 'Valor']
            })
        });

        this.comboPeriodos.on('select', function(e,r){
            this.periodo = r.data.Id;
        },this);


        return {
            layout : 'border',
            bodyStyle:'background: #D6D6D6',
            border : false,
            items : [{
                region : 'north',
                layout: 'fit',
                height : 82,
                split: false,
                items: [
                    this.PanelbuscadorEmpledo
                ]
            },{
                region : 'center',
                layout: 'fit',
                items: [{
                    layout: {
                        type: 'hbox',
                        pack: 'start',
                        align: 'stretch'
                    },
                    items: [
                        this.renderConsole(),
                        new Ext.Panel({
                            title: 'Liquidación',
                            layout: 'fit',
                            flex:2,
                            items:[this.gridConceptos]
                        })
                    ]
                }

                ]
            }]
        };
    },

    log: function(text)
    {
        //cont = this.console.getValue();
        this.console.update(text);
    },

    ejecutar: function() {
        var serv = this.searchServicio.getValue();
        if (!serv || !this.periodo) {
            app.publish('/desktop/showMsg', {
                title: 'Atención',
                msg: 'Seleccione primero un servicio y un período',
                renderTo: 'x-desktop',
                modal: true,
                icon: Ext.Msg.ERROR,
                buttons: Ext.Msg.OK
            });
            return;
        }

        var cfg = {
            url:'Liquidacion/Testliquidador/test/',
            method: 'POST',
            params: {
                periodo: this.periodo,
                servicio: serv
            },
            scope: this,
            success: function(result, request) {
                this.gridConceptos.store.loadData(result.conceptos);
                if(this.btnOcultarCeros.pressed) this.filtrarCeros();
                this.log(result.log);

            }
        };

        Rad.callRemoteJsonAction (cfg);
    }
});

new Apps.<?=$this->name?>();