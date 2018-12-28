Ext.ns( 'Apps' );

Apps.<?=$this->name?> = Ext.extend(RadDesktop.Module, {
    autoStart: true,
    title: '<?=$this->title?>',
    appChannel: '/desktop/modules<?=$this->url()?>',
    requires: [
        '/js/viz/spacetree.js'
    ],

    eventlaunch: function(ev)
    {
        if (!ev.id) {
            this.publish(
            '/desktop/notify',{
                title: 'Error',
                iconCls: 'x-icon-error',
                html: 'No se envio el id del Mmi'
            });
        } else {
            this.MmiId = ev.id;
            this.createWindow();

        }
    },
    /**
     * Inicializador del modulo
     */
    startup: function() {

    },

    initTree: function ()
    {
        idMmi = this.MmiId;
        that = this;
        var st = this.st;

        Rad.callRemoteJsonAction({
            url: '/Almacenes/Trazabilidad/traza/',
            params: {
                id: idMmi,
                tipo: 2
            },
            success: function (data) {

                st.loadJSON(data);

                //compute node positions and layout
                st.compute();
                //optional: make a translation of the tree
                //Tree.Geometry.translate(st.tree, new Complex(+200, +200), "startPos");
                //Emulate a click on the root node.
                st.onClick(st.root);
                node = st.graph.getNode(st.root);
                that.mostrarDetalle(node);
            }
        });

        Ext.getCmp('gridmovimientosmmitrazabilidad').store.load({params:{id:idMmi}});
    },

    mostrarDetalle: function(node){
        var gridInfo = Ext.getCmp('gridmovimientosmmitrazabilidad');
        var el = gridInfo.getGridEl();
        if (node.data.tipo == '2') {
            el.unmask();
            gridInfo.store.load({params:{id:node.id}});
            gridInfo.setTitle('Movimientos '+node.data.Identificador);
        } else {
            el.mask('Seleccione un Mmi');
        }
        var detailEl = Ext.get('trazabilidad-titulo');
        console.log(node.data.tipo);
        detailEl.hide();
        switch(node.data.tipo){
            case '2':
                tituloTemplateMmi.overwrite(detailEl, node.data);
            break;
            case '1':
                tituloTemplateRemito.overwrite(detailEl, node.data);
            break;
            case '3':
                tituloTemplateOrdenP.overwrite(detailEl, node.data);
            break;
            case '4':
                tituloTemplateGrupo.overwrite(detailEl, node.data);
            break;

        }

        detailEl.slideIn('l', {
            stopFx:true,
            duration:.2
        });

    },

    _initTree: function (){
        var cont = Ext.getCmp('spaceTreeTrazabilidad').getEl().dom.childNodes[0].childNodes[0].id;

        tituloTemplateMmi = new Ext.XTemplate(
            '<div class="panelDeDetalles">',
                '<tpl for=".">',
                    '<h3>Mmi: {Identificador}</h3>',
                    '<div class="detalle">',
                        '<b>Articulo:</b>',
                        '<span>{Descripcion}</span>',
                        '<b>Fecha de Ingreso:</b>',
                        '<span>{FechaIngreso}</span>',
                        '<b>Fecha de Vencimiento:</b>',
                        '<span>{FechaVencimiento}</span>',
                    '</div>',
                '</tpl>',
                '<img src="images/Forward.png" onclick="app.publish(\'/desktop/modules/Window/abm/index/m/Almacenes/model/Mmis\',{action:\'find\', value: {Id}})" style="position:absolute;right:3px;bottom:3px;left:auto;top:auto;padding:5px;cursor:pointer">',
            '</div>'
        );
        tituloTemplateGrupo = new Ext.XTemplate(
            '<div class="panelDeDetalles">',
                '<tpl for=".">',
                    '<h3>Grupo</h3>',
                    '<div class="detalle">',
                        '<b>Articulo:</b>',
                        '<span>{Grupo}</span>',
                        '<b>Cantidad Mmis:</b>',
                        '<span>{Cantidad}</span>',
                    '</div>',
                '</tpl>',
                '<img src="images/Forward.png" onclick="app.publish(\'/desktop/modules/Window/abm/index/m/Almacenes/model/Mmis\',{action:\'find\', value: {Id}})" style="position:absolute;right:3px;bottom:3px;left:auto;top:auto;padding:5px;cursor:pointer">',
            '</div>'
        );
        tituloTemplateRemito = new Ext.XTemplate(
            '<div class="panelDeDetalles">',
                '<tpl for=".">',
                    '<h3>Remito: {Numero}</h3>',
                    '<div class="detalle">',
                        '<b>Razon Social:</b>',
                        '<span>{RazonSocial}</span>',
                        '<b>Fecha de Carga:</b>',
                        '<span>{FechaEmision}</span>',
                        '<b>Fecha de Entrega:</b>',
                        '<span>{FechaEntrega}</span>',
                    '</div>',
                '</tpl>',
                '<img src="images/Forward.png" onclick="app.publish(\'/desktop/modules/Almacenes/remitosDeIngresos\',{action:\'find\', value: {Id}})" style="position:absolute;right:3px;bottom:3px;left:auto;top:auto;padding:5px;cursor:pointer">',
            '</div>'
        );

        tituloTemplateOrdenP = new Ext.XTemplate(
            '<div class="panelDeDetalles">',
                '<tpl for=".">',
                    '<h3>Orden de Produccion: {Id}</h3>',
                    '<div class="detalle">',
                        '<b>Articulo:</b>',
                        '<span>{Articulo}</span>',
                        '<b>Cantidad:</b>',
                        '<span>{Cantidad}</span>',
                        '<b>Estado:</b>',
                        '<span>{Estado}</span>',
                        '<b>Fecha:</b>',
                        '<span>{FechaOrdenDeProduccion}</span>',
                    '</div>',
                '</tpl>',
                '<img src="images/Forward.png" onclick="app.publish(\'/desktop/modules/Produccion/ordenesDeProducciones\',{action:\'find\', value: {Id}})" style="position:absolute;right:3px;bottom:3px;left:auto;top:auto;padding:5px;cursor:pointer">',
            '</div>'
        );

        // Inicializo el tree
        var labelType, useGradients, nativeTextSupport, animate;

        (function() {
        var ua = navigator.userAgent,
            iStuff = ua.match(/iPhone/i) || ua.match(/iPad/i),
            typeOfCanvas = typeof HTMLCanvasElement,
            nativeCanvasSupport = (typeOfCanvas == 'object' || typeOfCanvas == 'function'),
            textSupport = nativeCanvasSupport
                && (typeof document.createElement('canvas').getContext('2d').fillText == 'function');
        //I'm setting this based on the fact that ExCanvas provides text support for IE
        //and that as of today iPhone/iPad current text support is lame
        labelType = (!nativeCanvasSupport || (textSupport && !iStuff))? 'Native' : 'HTML';
        nativeTextSupport = labelType == 'Native';
        useGradients = nativeCanvasSupport;
        animate = !(iStuff || !nativeCanvasSupport);
        })();


        //Implement a node rendering function called 'nodeline' that plots a straight line
        //when contracting or expanding a subtree.
        $jit.ST.Plot.NodeTypes.implement({
            'nodeline': {
            'render': function(node, canvas, animating) {
                    if(animating === 'expand' || animating === 'contract') {
                    var pos = node.pos.getc(true), nconfig = this.node, data = node.data;
                    var width  = nconfig.width, height = nconfig.height;
                    var algnPos = this.getAlignedPos(pos, width, height);
                    var ctx = canvas.getCtx(), ort = this.config.orientation;
                    ctx.beginPath();
                    if(ort == 'left' || ort == 'right') {
                        ctx.moveTo(algnPos.x, algnPos.y + height / 2);
                        ctx.lineTo(algnPos.x + width, algnPos.y + height / 2);
                    } else {
                        ctx.moveTo(algnPos.x + width / 2, algnPos.y);
                        ctx.lineTo(algnPos.x + width / 2, algnPos.y + height);
                    }
                    ctx.stroke();
                }
            }
            }

        });
        //end

        that = this;
        //init Spacetree
        //Create a new ST instance
        this.st = new $jit.ST({
            //id of viz container element
            injectInto: cont,
            duration: 380,
            //set animation transition type
            transition: $jit.Trans.Quart.easeInOut,
            //set distance between node and its children
            levelDistance: 80,
            //
            //multitree: true,
            //set max levels to show. Useful when used with
            //the request method for requesting trees of specific depth
            levelsToShow: 2,

            constrained:false,
            //set node and edge styles
            //set overridable=true for styling individual
            //nodes or edges
            Navigation: {
                enable:  true,
                panning: true
            },
            Node: {
                height: 46,
                width: 60,
                //use a custom
                //node rendering function
                type: 'none',
                color:'#999',
                lineWidth: 2,
                align:"center",
                overridable: true,
                CanvasStyles: {
                    shadowColor: '#efefef',
                    shadowBlur: 4
                }
            },

            Edge: {
                type: 'bezier',
                lineWidth: 2,
                color:'#017DC5',
                overridable: true
            },

            onBeforeCompute: function(node){
                //Log.write("loading " + node.name);
            },

            onAfterCompute: function(){
                //Log.write("done");
            },

            //This method is called on DOM label creation.
            //Use this method to add event handlers and styles to
            //your node.
            onCreateLabel: function(label, node) {
                label.id = node.id;
                if (node.data.tipo == 4) {
                    label.innerHTML = '<img src="../images/palets/grouppalet.png" /></br><span>'+node.data.Descripcion+'</span>'; // node.name
                }else if (node.data.tipo == 2) {
                    label.innerHTML = '<img src="../images/palets/palet.png" /></br><span>'+node.data.Identificador+'</span>'; // node.name
                }else if (node.data.tipo == 1) {
                    label.innerHTML = '<img src="../images/truckyellow.png" />';
                } else {
                    label.innerHTML = '<img src="../images/fabrica_40.png" />';
                }

                label.onclick = function(){
                    that.st.onClick(node.id);
                    that.mostrarDetalle(node);
                };

                //set label styles
                var style = label.style;
                style.width             = 60 + 'px';
                style.height            = 42 + 'px';
                style.cursor            = 'pointer';
                style.color             = '#333';
                //style.backgroundColor = '#1a1a1a';
                style.fontSize          = '0.8em';
                style.textAlign         = 'center';
                //style.textDecoration  = 'underline';
                style.paddingTop        = '3px';
                style.clear             = 'left'
            },

            //This method is called right before plotting
            //a node. It's useful for changing an individual node
            //style properties before plotting it.
            //The data properties prefixed with a dollar
            //sign will override the global node style properties.
            onBeforePlotNode: function(node){
                //add some color to the nodes in the path between the
                //root node and the selected node.
                if (node.selected) {
                    node.data.$color = "#017DC5";
                    node.data.$type='ellipse';
                }
                else {
                    delete node.data.$color;
                    node.data.$type='none';
                }
            },

            //This method is called right before plotting
            //an edge. It's useful for changing an individual edge
            //style properties before plotting it.
            //Edge data proprties prefixed with a dollar sign will
            //override the Edge global style properties.
            onBeforePlotLine: function(adj){
                if (adj.nodeFrom.selected && adj.nodeTo.selected) {
                    adj.data.$color = "#017DC5";
                    adj.data.$lineWidth = 4;
                }
                else {
                    delete adj.data.$color;
                    delete adj.data.$lineWidth;
                }
            }
        });

    },



    createWindow: function()
    {
        var win = app.desktop.getWindow(this.id+'-win');
        if ( !win ) {
            win = this.create();
            win.show();
            t = this;
            (function() {
                t._initTree();
                t.initTree();
            }).defer(500);

        } else {
            win.show();
            this.initTree();
        }


    },




    create: function()
    {
        defaultWinCfg = {
            id: this.id+'-win',
            title: this.title,
            iconCls: 'icon-grid',
            border:  false,
            shim: false,
            animCollapse: false,
            layout: 'fit',
            maximized:true,

            width: 1050,
            height:500,
            items: [
                this.renderWindowContent()
            ]
        };
        w = app.desktop.createWindow(defaultWinCfg);

        return w;
    },

    renderWindowContent: function ()
    {
        movimientosGrid  = Ext.create({
            'title'      : 'Movimientos',
            'loadAuto'   : false,
            'xtype'      : "radgridpanel",
            'id'         : 'gridmovimientosmmitrazabilidad',
            flex         :  1,
            'layout':'fit',
            'border':  true,
            'filters'    : true,
            'url'        : '/Almacenes/Trazabilidad/trazammi',
            'forceFit'   : true,
            'stateful'   : false,
            'withPaginator': true
        });

        return {
           layout: 'border',
           border:  false,

           items:[{
                region : 'center',
                margins: '2 2 2 2',
                id: 'spaceTreeTrazabilidad',

                layout: 'fit',
                html:'<div id="trazabilidad-titulo" style="display:none"></div>',
                //border:  false,
                bodyStyle: {

                    background: '#eee',
                    position:   'relative',
                    margin:     'auto',
                    overflow:   'hidden'
                }
            },{
                layout: 'vbox',
                region : 'east',
                'margins': '2 2 2 0',
                border: false,
                width:350,
                items:[
                    movimientosGrid,

                    new Ext.chart.LineChart({
                        border: false,
                        'margins': '2 2 2 2',
                        flex:1,
                        store: movimientosGrid.store,
                        //url:'../ext-3.0-rc1/resources/charts.swf',
                        xField: 'Fecha',

                        xAxis: new Ext.chart.TimeAxis({
                              displayName: 'Fecha',
                              labelRenderer : Ext.util.Format.dateRenderer('H:m d/M'),

                              majorTimeUnit : "hour"
                          }),
                        series: [{
                                type: 'line',
                                displayName: 'Cantidad',
                                yField: 'Cantidad',
                                style: {
                                    size: 7

                                }
                            }],
    //                    tipRenderer : function(chart, record, index, series) {
    //                        return series.displayName+'\nPeriodo: ' + record.data.Mes + '/' + record.data.Anio + "\n" +
    //                               'Total: ' + Ext.util.Format.usMoney(record.get(series.yField));
    //                    },
                        extraStyle: {
                            padding: 10,
                            animationEnabled: true,
                            legend: {
                                display: 'bottom'
                            },
                            xAxis: {
                                color: 0x3366cc,
                                showLabels: true,
                                majorGridLines: {size: 1, color: 0xdddddd},
                                minorGridLines: {size: 1, color: 0xdddddd}

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
            }
            ]
        };
    },

});

new Apps.<?=$this->name?>();