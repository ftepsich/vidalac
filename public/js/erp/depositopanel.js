/**
 * Image Drag zone
 */
ImageDragZone = function (view, config) {
    this.view = view;
    ImageDragZone.superclass.constructor.call(this, view.getEl(), config);
};

/**
 * Seleccion de zona por arrastre
 *
 */
Ext.extend(ImageDragZone, Ext.dd.DragZone, {
    // We don't want to register our image elements, so let's
    // override the default registry lookup to fetch the image
    // from the event instead
    getDragData : function(e) {

        var target = e.getTarget('.thumb-cell-selected');

        if (target) {
            var view = this.view;
            if (!view.isSelected(target)) {
                view.onClick(e);
            }
            var selNodes = view.getSelectedNodes();
            var dragData = {
                nodes: selNodes
            };
            var img = null;
            var selItem = null;
            // muevo 1 registro
            if (selNodes.length == 1) {
                selItem = view.store.getAt(selNodes[0].viewIndex);
                if ((!selItem.data.Mmi || !selItem.data.Mmi.Id) && !selItem.data.Ocupado) {
                    selNodes.remove(selNodes[0]);
                    return false;
                }
                img = document.createElement('img');
                img.src = '../images/palets/palet.png';

                dragData.ddel = img;
                dragData.sourceEl = target;
                dragData.single = true;

            } else {
                var div = document.createElement('div');
                var toMove = [];
                img = document.createElement('img');
                img.src = '../images/palets/palet.png';
                div.appendChild(img);

                for (var i = 0, len = selNodes.length; i < len; i++) {
                    selItem = view.store.getAt(selNodes[i].viewIndex);
                    if ((selItem.data.Mmi && selItem.data.Mmi.Id) || selItem.data.Ocupado) {
                        toMove.push(selNodes[i]);
                    }
                }
                var count = document.createElement('div'); // selected image count

                if (toMove.length == 0)
                    return false;

                count.innerHTML = toMove.length + ' Mmis';
                div.appendChild(count);

                dragData.ddel = div;
                sourceEl: target;
                dragData.multi = true;
                dragData.nodes = toMove;
            }
            return dragData;
        }
        return false;
    },

    // the default action is to 'highlight' after a bad drop
    // but since an image can't be highlighted, let's frame it
    afterRepair:function() {
        for(var i = 0, len = this.dragData.nodes.length; i < len; i++){
            Ext.fly(this.dragData.nodes[i]).frame('#8db2e3', 1);
        }
        this.dragging = false;
    },

    // override the default repairXY with one offset for the margins and padding
    getRepairXY : function(e) {
        if (!this.dragData.multi) {
            var xy = Ext.Element.fly(this.dragData.ddel).getXY();
            xy[0]+=3;
            xy[1]+=3;
            return xy;
        }
        return false;
    }
});

Ext.ns( 'ERP' );
/**
 * Panel Administrador de deposito
 */
ERP.depositoPanel = Ext.extend(Ext.Panel, {
    almacenId: null,
    depositoId: null,

    /**
     * Activa la funcionalidad de mover mmis
     */
    mover: true,
    activeItem: 0,
    layout: 'card',
    padding: '5',
    modoVista: 1, // Grafica
    destroy: function() {
        Ext.destroy(this.dd);
        Ext.destroy(this.depositoDragZone);
        Ext.destroy(this.depositoTempDragZone);
    },

    hideTemporal: function(){
        this.get(0).get(2).hide();
        this.doLayout();
    },
    showTemporal: function(){
        this.get(0).get(2).show();
        this.doLayout();
    },

    setPerspectiva: function(modo) {
        if (!this.comboDeposito.getValue()) {
            app.publish('/desktop/showError', 'Debe seleccionar primero el Depósito.');
            return;
        } else if (!this.comboAlmacenes.getValue()) {
            app.publish('/desktop/showError', 'Debe seleccionar primero el Almacen.');
            return;
        }
        this.perspectiva = modo;
        this.deposito.store.baseParams.perspectiva = modo;
        this.deposito.store.reload();
    },
    /**
     * Setea el modo de vista 1 grafico, 2 grilla
     */
    setModoVista: function(modo) {
        this.modoVista = modo;
        if (modo == 1) {
            this.modoGrillaBt.toggle(false);
            this.getTopToolbar().get(1).show();

            this.layout.setActiveItem(0);
            this.deposito.clearSelections();
            this.gridMmis.getSelectionModel().clearSelections();
        } else if (modo == 2) {
            this.modoGraficoBt.toggle(false);
            this.layout.setActiveItem(1);
            this.getTopToolbar().get(1).hide();
            this.deposito.clearSelections();
            this.gridMmis.getSelectionModel().clearSelections();
        }
    },
    /**
     * Inicializa el componente
     */
    initComponent: function() {

        /**
         * Combo seleccionador de deposito a mostrar
         */
        this.comboDeposito = new Ext.ux.form.ComboBox({
            displayFieldTpl: '{TipoDeDireccion_cdisplay}: {Direccion}',
            tpl: '<tpl for="."><div class="x-combo-list-item"><h3>{TipoDeDireccion_cdisplay} - {Localidad_cdisplay}</h3>{Direccion}</div></tpl>',
            selectOnFocus: true,
            forceSelection: true,
            forceReload: true,
            loadingText: 'Cargando...',
            emptyText: 'Seleccione un Depósito',
            lazyRender: true,
            triggerAction: 'all',
            valueField: 'Id',
            autoLoad: false,
            store: new Ext.data.JsonStore ({
                url: '/datagateway/combolist/model/DepositosPropios/m/Base/TipoDeDireccion/2',
                autoLoad: true,
                root: 'rows',
                idProperty: 'Id',
                storeId: 'DepositoStore32sd',
                totalProperty: 'count',
                fields: [ 'Id', 'TipoDeDireccion_cdisplay', 'Direccion','Localidad_cdisplay']
            })
        });

        /**
         * Combo seleccionador de Almacen a mostrar
         */

        this.comboAlmacenes = new Ext.ux.form.ComboBox({
            displayField: 'Descripcion',
            selectOnFocus: true,
            forceSelection: true,
            forceReload: true,
            loadingText: 'Cargando...',
            lazyRender: true,
            triggerAction: 'all',
            valueField: 'Id',
            autoLoad: false,
            store: new Ext.data.JsonStore ({
                url: '/datagateway/combolist/model/Almacenes/m/Almacenes',
                root: 'rows',
                autoLoad: false,
                idProperty: 'Id',
                storeId: 'AlmacenesStore32sd',
                totalProperty: 'count',
                fields: [ 'Id', 'Descripcion', 'TieneRack', 'TipoDeAlmacen', 'Perspectiva']
            })
        });

        /**
         * Combo seleccionador de perspectiva para mostrar el almacen
         */
        this.perspectivaButton = new Ext.Button({
            tooltip: 'Perspectiva',
            icon: 'images/32/3d-view.png',
            scale: 'large',
            menu : {
                items: [
                '<b class="menu-title">Perspectiva</b>',
                {
                    group: 'perspectiva',
                    text:'Frente',
                    // icon:'images/arrow_up.png',
                    scope:this,
                    checked: false,
                    handler: function(){
                        this.setPerspectiva(1);
                    }
                },{
                    group: 'perspectiva',
                    text:'Lateral',
                    // icon:'images/arrow_right.png',
                    scope:this,
                    checked: false,
                    handler: function(){
                        this.setPerspectiva(3);
                    }
                },{
                    group: 'perspectiva',
                    text:'Arriba',
                    // icon:'images/arrow_down.png',
                    scope:this,
                    checked: false,
                    handler: function(){
                        this.setPerspectiva(2);
                    }
                }]
            }
        });

        this.verDetalleMmiButton = new Ext.Button({
            tooltip: 'Ver en el Detalle de Mmi',
            icon: 'images/32/application_view_icons.png',
            scale: 'large',
            menu : {
                items: [
                '<b class="menu-title">Ver</b>',
                {
                    group: 'detallemmi',
                    text:'Identificador',
                    scope:this,
                    checked: true,
                    handler: function(){
                        this.deposito.setMostrarEnDetalle(1);
                    }
                },{
                    group: 'detallemmi',
                    text:'Lote',
                    // icon:'images/arrow_right.png',
                    scope:this,
                    checked: false,
                    handler: function(){
                        this.deposito.setMostrarEnDetalle(2);
                    }
                },{
                    group: 'detallemmi',
                    text:'Cantidad',
                    // icon:'images/arrow_down.png',
                    scope:this,
                    checked: false,
                    handler: function(){
                        this.deposito.setMostrarEnDetalle(3);
                    }
                },{
                    group: 'detallemmi',
                    text:'Fecha Vencimiento',
                    // icon:'images/arrow_down.png',
                    scope:this,
                    checked: false,
                    handler: function(){
                        this.deposito.setMostrarEnDetalle(4);
                    }
                },{
                    group: 'detallemmi',
                    text:'Fecha Elaboración',
                    // icon:'images/arrow_down.png',
                    scope:this,
                    checked: false,
                    handler: function(){
                        this.deposito.setMostrarEnDetalle(5);
                    }
                }]
            }
        });

        this.modoGraficoBt = new Ext.Button({
            tip:'Grafica',
            icon: 'images/32/racks.png',
            enableToggle: true,
            pressed: true,
            handler: function () {
                this.setModoVista(1);
            },
            scope: this
        });
        this.modoGrillaBt = new Ext.Button({
            icon: 'images/32/grid.png',
            enableToggle: true,
            tip:'Grilla',
            enableToggle: true,
            handler: function () {
                this.setModoVista(2);
            },
            scope: this
        });

        //grilla mmi
        this.gridMmis = Ext.ComponentMgr.create({
            xtype:      "radgridpanel",
            filters:    true,
            url:        "/default/datagateway",
            model:      "Mmis",
            module:     "Almacenes",
            forceFit:   true,
            stateful:   false,
            iniSection: "almacenes",
            border:     false,
            autoSave:   true,
            loadAuto:   false,
            view: new Ext.grid.GroupingView({
                enableNoGroups: false,
                forceFit: true,
                resaltar: {tipo:'nada'}, // ;)
                fresaltar: function(r) {

                    switch(this.resaltar.tipo)
                    {
                        case 'RemitoArticuloSalida':
                            return r.get('RemitoArticuloSalida') == this.resaltar.valor;
                            break;
                        case 'RemitoArticuloEntrada':
                            return r.get('RemitoArticulo') == this.resaltar.valor;
                            break;
                        case 'TemporalesProduccion':
                            return r.data.AsignadoODPDetalleTemporal == this.resaltar.valor;
                            break;
                        default:
                            return false;
                    }
                },
                resaltarNada: function() {
                    this.resaltar = {tipo:'nada'};
                },
                resaltarRemitoArticuloSalida: function(a) {
                    this.resaltar = {tipo:'RemitoArticuloSalida', valor: a};
                },
                resaltarRemitoArticuloIngreso: function(a) {
                    this.resaltar = {tipo:'RemitoArticuloIngreso', valor: a};
                },
                resaltarTemporalesProduccion: function(a) {
                    this.resaltar = {tipo:'TemporalesProduccion', valor: a};
                },
                getRowClass: function(record, rowIndex, p, store) {
                    if (this.fresaltar(record)) return 'x-grid3-row-blue';
                    return 'x-grid3-row';
                },
                hideGroupedColumn: true,
                groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? "MMI" : "MMI"]})'
            })
        });


        this.comboDeposito.on('select',function (c,r,i) {
            this.comboAlmacenes.store.baseParams['Deposito'] = r.data.Id;
            this.comboAlmacenes.clearValue();
            this.deposito.store.removeAll();
            this.depositoId = r.data.Id;
            if (this.depositoTemp) {
                this.depositoTemp.store.baseParams['deposito'] = this.depositoId;
                this.depositoTemp.store.reload();
            }

            this.gridMmis.setPermanentFilter(0, 'Deposito', r.data.Id);
            this.gridMmis.store.load();
        },this);

        this.comboAlmacenes.on('beforequery',function(){
            var v = this.comboDeposito.getValue();
            if(!v){
                app.publish('/desktop/showError', 'Debe seleccionar primero el Deposito.');
                return false;
            }
        },this);


        // this.comboAlmacenesPerspectivas.on('select',function (c,r,i) {
        //     this.setPerspectiva(r.data.Id);
        // },this);

        var t1 = new Ext.Toolbar.TextItem(' ');
        var t2 = new Ext.Toolbar.TextItem(' ');

        /**
         * Botton toolbar
         */
        this.bbar = new Ext.ux.StatusBar ({
            busyText: 'Cargando',
            defaultIconCls: 'default-icon',

            // values to set initially:
            text: 'Listo',
            iconCls: 'ready-icon',



            updateSeleccion: function (datos) {
                Ext.fly(t1.el).update(datos+'&nbsp;');
            },
            updateTotal: function (datos) {
                Ext.fly(t2.el).update(datos+'&nbsp;');
            },
            items: [
                '-',
                t1,
                // ' ',
                t2
            ]
        });

        this.deposito = new ERP.depositoDataView({
            almacen: this.almacenId
        });
        this.deposito.on('paletPartido',function(){
            if (this.mover) this.depositoTemp.store.reload();
        }, this);
        this.deposito.on('paletModificado',function(){
            if (this.mover) this.depositoTemp.store.reload();
        }, this);

        this.deposito.on('mouseenter', function(t,i) {

            var html = this.deposito.getDetalleMmiHtml(i);
            this.panelDetalles.update(html);
        },this);

        this.deposito.on('mouseleave', function(t,i) {
            this.panelDetalles.update('');
        },this);



        //this.setEventDeposito();

        this.panelDetalles = new Ext.Panel({
            border: true,
            //title: 'Detalle',
            region: 'east',
            width: 240,
            margins: '0 0 5 0',
            bodyCssClass: 'panelDeDetalles panelDetalleAzul',
        });

        this.items = [
            {
                layout: 'border',
                border: false,
                items:[
                    {
                        region: 'center',
                        frame: false,
                        autoScroll: true,
                        border: false,
                        xtype: 'panel',
                        activeItem: 0,
                        layout: 'fit',
                        items: [
                            this.deposito,
                        ]
                    },
                    this.panelDetalles
                ]
            },
            this.gridMmis
        ];

        // Si esta habilitado el mover mmis necesitamos mostrar el panel temporal
        if (this.mover) {
            this.agregarDepositoTemp();
        }

        // Seteo el manejador de eventos del combo
        this.comboAlmacenes.on('select',
            function (c, e, i) {
                this.deposito.cambiarAlmacen(e.data);
                this.perspectivaButton.menu.items.items[e.data.Perspectiva].setChecked(true, true);
            }, this
        );

        this.tbar = [
            {
                xtype: 'buttongroup',
                title: 'Vista',
                defaults: {
                    scale: 'large'
                },
                columns:3,
                items: [
                    this.modoGraficoBt,
                    this.modoGrillaBt,
                    this.comboDeposito
                ]
            },
            {
                xtype: 'buttongroup',
                title: 'Almacen',

                columns: 6,

                items: [
                    this.comboAlmacenes,
                    this.perspectivaButton,
                    this.verDetalleMmiButton,
                    {
                        tooltip:    'Resaltar',
                        scale: 'large',
                        iconCls: 'x-btn-text-icon',
                        icon:    'images/32/resaltarpalet.png',
                        menu: new Ext.menu.Menu ({
                            items: [
                                '<b class="menu-title">Resaltar</b>',
                                new ERP.depositoResaltadorMenu({
                                    text: 'Rojo',
                                    deposito: this.deposito
                                }),
                                new ERP.depositoResaltadorMenu({
                                    color:'azul',
                                    text: 'Azul',
                                    deposito: this.deposito
                                })
                            ]
                        })
                    },{
                        enableToggle: true,
                        scale: 'large',
                        icon:'images/32/application_view_detail.png',
                        tooltip:'Racks',
                        pressed: true,
                        toggleHandler: function (item, pressed) {
                            Ext.select("#x-desktop .thumb-cell .ubicDesc").toggle();
                        }
                    },{
                        enableToggle: true,
                        scale: 'large',
                        tooltip:'Detalles',
                        icon:'images/32/application_side_list_r32.png',
                        pressed: true,
                        toggleHandler: function (item, pressed) {
                            if (pressed) {
                                this.panelDetalles.show();
                                this.doLayout();
                            } else {
                                this.panelDetalles.hide();
                                this.doLayout();
                            }
                        },
                        scope: this
                    }
                ]
            },
        ];

        //this.comboAlmacenes.setValue(this.almacen);

        ERP.depositoPanel.superclass.initComponent.call(this);
        this.depositoTemp.on('afterrender', this.setDropTargets, this);
    },

    agregarDepositoTemp: function() {
        this.depositoTemp = new ERP.depositoDataView({
            style: "background-color:#efefef",
            url: '/Almacenes/Almacenes/gettemporal'
        });

        this.depositoTemp.on('mouseenter', function(t,i) {
            var html = this.depositoTemp.getDetalleMmiHtml(i);
            this.panelDetalles.update(html);
        },this);

        this.depositoTemp.on('mouseleave', function(t,i) {
            this.panelDetalles.update('');
        },this);

        this.items[0].items.push({
            xtype: 'panel',
            region: 'south',
            height: 80,
            border: true,

            layout: 'fit',
            items: [
                this.depositoTemp
            ]
        });
    },

    /**
     * Esta funcion es para extenderla y capturar drops sobre el deposito desde otros componentes externos.
     */
    onDropDeposito: function(source, e, data) {
        return false;
    },
    setDropTargets: function() {
        if (!this.mover) return;


        this.deposito.ddGroup = 'ubicacion';
        this.dropTarget = this.get(0).get(0).body;



        this.dd = new Ext.dd.DropTarget(this.dropTarget, {
            ddGroup: 'ubicacion',
            scope: this,
            // Permite soltar elementos en el DV
            notifyOver: function (source, e, data) {
                var comboAlmacenes = this.scope.comboAlmacenes;
                var rAlmacenDestino = comboAlmacenes.store.getAt(comboAlmacenes.store.findExact('Id', comboAlmacenes.getValue()));
                if (!rAlmacenDestino) return false;
                return this.dropAllowed;
            },

            notifyDrop: function (source, e, data) {
                var comboAlmacenes = this.scope.comboAlmacenes;
                var rAlmacenDestino = comboAlmacenes.store.getAt(comboAlmacenes.store.findExact('Id', comboAlmacenes.getValue()));
                if (!rAlmacenDestino) return false;

                // Cambia el target del drop si se solto en un elemento hijo
                switch (e.getTarget().getAttribute('class')) {
                    case 'thumb':
                        e.target = e.getTarget().parentNode.parentNode;
                        break;
                    case 'ubicMmis':
                        e.target = e.getTarget().parentNode;
                        break;
                    case 'ubicDesc':
                        e.target = e.getTarget().parentNode.parentNode;
                        break;
                }
                var itemTo  = [];
                var items = [];
                var cantMoves = 0;
                var indexTo = null;
                var i = 0;

                // de donde viene?
                switch (source.id) {

                    // Trae un MMI del panel temporal
                    case this.scope.depositoTemp.id:

                        indexTo = this.scope.deposito.store.findExact('Id', e.getTarget().getAttribute('ubicacion'));

                        var almacenOrigen = this.scope.depositoTemp.store.getAt(data.nodes[0].viewIndex).data.Almacen;

                        Ext.each(data.nodes, function (item, number, all) {
                            // item arrastrado
                            var selectedItem = this.scope.depositoTemp.store.getAt(item.viewIndex);
                            // posicion soltada
                            var itemTo = this.scope.deposito.store.getAt(indexTo + cantMoves);
                            items.push({
                                desde:  selectedItem.data.Id,
                                hacia:  (rAlmacenDestino.data.TieneRack != '0') ? itemTo.data.Id : null
                            });


                            if (indexTo != -1) cantMoves++;
                        }, this);

                        Models.Almacenes_Model_AlmacenesMapper.moverMmis(almacenOrigen, rAlmacenDestino.data.Id, items, this.scope.depositoId, function() {
                            this.scope.depositoTemp.store.reload();
                            this.scope.deposito.store.reload();
                        }, this);

                        return true;
                        break;

                    // Mueve un MMI dentro del mismo almacen
                    case (this.scope.deposito.id):

                        var almacenMov = comboAlmacenes.store.getAt(comboAlmacenes.store.findExact('Id', comboAlmacenes.getValue()));
                        if (!almacenMov.data.TieneRack)
                            return app.publish('/desktop/showError', 'No se pueden mover MMIs en el mismo almacen no rackeable');

                        indexTo = this.scope.deposito.store.findExact('Id', parseInt(e.getTarget().getAttribute('ubicacion')));
                        var ubicacionDestino;
                        var posicionInvalida = false;
                        Ext.each (data.nodes, function (item, number, all) {
                            ubicacionDestino = this.scope.deposito.store.getAt(indexTo + cantMoves);
                            if (!ubicacionDestino || posicionInvalida) {
                                posicionInvalida = true;
                                return false;
                            }
                            items.push({
                                desde:  this.scope.deposito.store.getAt(item.viewIndex).data.Id,
                                hacia:  ubicacionDestino.data.Id
                            });

                            if (indexTo != -1) cantMoves++;
                            i++;
                            return true;
                        }, this);

                        if (posicionInvalida) return false;

                        Models.Almacenes_Model_AlmacenesMapper.moverMmis(almacenMov.data.Id, almacenMov.data.Id, items, this.scope.depositoId, function() {
                            this.scope.deposito.store.reload();
                        }, this);

                        return true;
                        break;

                    default:
                        this.scope.onDropDeposito(source, e, data);
                        return false;
                        break;
                }
            } // eo notifyDrop
        }); // eo DropTarget

        /**
         * Drop target del panel temporal
         */
        this.get(0).get(2).ddGroup = 'ubicacion';

        this.ddt = new Ext.dd.DropTarget(this.get(0).get(2).body, {
            scope: this,
            ddGroup: 'ubicacion',

            // Permite soltar elementos en el DV
            notifyOver: function (source, e, data) {

                var comboAlmacenes = this.scope.comboAlmacenes;
                var rAlmacenDestino = comboAlmacenes.store.getAt(comboAlmacenes.store.findExact('Id', comboAlmacenes.getValue()));
                if (!rAlmacenDestino) return false;
                return this.dropAllowed;
            },

            notifyDrop: function (source, e, data) {
                var comboAlmacenes = this.scope.comboAlmacenes;
                var rAlmacen = comboAlmacenes.store.getAt(comboAlmacenes.store.findExact('Id', comboAlmacenes.getValue()));
                // No se pueden mover por drag&drop Mmis en Intedepositos
                // if (rAlmacen.data.TipoDeAlmacen == 3)
                //     return false;

                // Cambia el target del drop si se solto en el elemento hijo
                if (e.getTarget().getAttribute('class') == 'thumb')
                    e.target = e.getTarget().parentNode;

                // Mueve un MMI al Temporal
                if (source.id == this.scope.deposito.id) {

                    var items = [];
                    var selectedItem;
                    Ext.each (data.nodes, function (item, number, all) {
                        selectedItem = this.scope.deposito.store.getAt(item.viewIndex);
                        items.push({
                            desde: selectedItem.data.Id,
                            hacia: null
                        });
                    }, this);

                    Models.Almacenes_Model_AlmacenesMapper.moverMmis(
                        selectedItem.data.Mmi.Almacen || selectedItem.data.Almacen,
                        null,
                        items,
                        this.scope.depositoId,
                        function() {
                            this.scope.deposito.store.reload();
                            this.scope.depositoTemp.store.reload();
                        },
                        this
                    );

                    return true;
                } else {
                    return false;
                }
            } // eo notifyDrop
        }); // eo DropTarget

        this.depositoDragZone = new ImageDragZone(this.deposito, {
            containerScroll: true,
            ddGroup: 'ubicacion',
            isTarget: true
        });

        this.depositoTempDragZone = new ImageDragZone(this.depositoTemp, {
            containerScroll: true,
            ddGroup: 'ubicacion',
            isTarget: true
        });
    }
});
