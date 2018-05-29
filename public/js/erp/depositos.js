Ext.ns( 'ERP' );

function filtrosDepositos(view) {
    this.view = view;


    this.porArticulo = function (articulo) {

        this.view.tpl.filtro = function (value) {

            return (articulo == value.Mmi.Articulo) ? 'ubicacion-resaltada' : 'ubicacion-no-resaltada';
        };
        this.view.refresh();
    };

    this.porArticuloVersion = function (articulo) {

        this.view.tpl.filtro = function (value) {
            return (articulo == value.Mmi.ArticuloVersion) ? 'ubicacion-resaltada' : 'ubicacion-no-resaltada';
        };
        this.view.refresh();
    };

    this.esArticulo = function (value, valorFiltro) {
        return (valorFiltro == value.Mmi.Articulo);
    },

    this.esCreadoDespues = function (value, valorFiltro) {
        return ( value.Mmi.FechaIngreso && valorFiltro.format('Y-m-d h:i:s') < value.Mmi.FechaIngreso);
    };

    this.esVencidosDespues = function (value, valorFiltro) {
        return ( value.Mmi.FechaIngreso && valorFiltro.format('Y-m-d h:i:s') < value.Mmi.FechaVencimiento);
    };

    this.porArticuloYRemitoArticuloSalida = function (articulo, rArticulo) {
        this.view.tpl.filtro = function (value) {
            v = (rArticulo == value.Mmi.RemitoArticuloSalida) ? ' mmi-resaltado ' : '';
            return ((articulo == value.Mmi.Articulo) && (!value.Mmi.RemitoArticuloSalida || v)) ? 'ubicacion-resaltada'+v : 'ubicacion-no-resaltada'+v;
        };
        this.view.refresh();
    };

    this.porArticuloYRemitoArticulo = function (articulo, rArticulo) {
        this.view.tpl.filtro = function (value) {
            v = (rArticulo == value.Mmi.RemitoArticulo) ? ' mmi-resaltado ' : '';
            return ((articulo == value.Mmi.Articulo) && (!value.Mmi.RemitoArticulo || v)) ? 'ubicacion-resaltada'+v : 'ubicacion-no-resaltada'+v;
        };
        this.view.refresh();
    };

    this.porTipo = function (articulo) {
        this.view.tpl.filtro = function (value) {
            return 'ubicacion-no-resaltada';
        };
        this.view.refresh();
    };

    this.filtrarTodo = function () {
        this.view.tpl.filtro = function (value) {
            return 'ubicacion-no-resaltada';
        };
        this.view.refresh();
    };

    this.porOrdenDeProduccionDetalleTemporalYArticuloVersion = function (odpDetalle, articulo) {
        var oD = odpDetalle;
        var a = articulo;
        var dview = this.view;
        this.view.tpl.filtro = function (value) {
            v = (value.AsignadoODPDetalleTemporal == oD) ? ' mmi-resaltado' : ' mmi-no-resaltado';
            console.log(dview.almacenSeleccionado.TipoDeAlmacen);
            // si es interdeposito no se resalta nada
            if (dview.almacenSeleccionado.TipoDeAlmacen == 3) return 'ubicacion-no-resaltada'+v;

            return (a == value.Mmi.ArticuloVersion) ? 'ubicacion-resaltada'+v : 'ubicacion-no-resaltada'+v;
        }
        this.view.refresh();
    };

    this.quitar = function () {
        this.view.tpl.filtro = function () {
            return 'ubicacion-resaltada';
        }
        this.view.refresh();
    }

}

/**
 * Visor de depositos del sistema
 */
ERP.depositoDataView = Ext.extend(Ext.DataView, {
    /**
     * Id del Almacen q se cargara por defecto
     */
    almacen: null,

    /**
     * array de datos del deposito selecionado para mostrar
     */
    almacenSeleccionado: null,
    /**
     * Url de donde se obtienen los palets
     */
    url: '/Almacenes/Almacenes/getcells',

    itemSelector: 'div.thumb-cell',
    selectedClass: 'thumb-cell-selected',
    loadingText: 'Cargando...',
    autoWidth: true,
    multiSelect: true,
    trackOver: true,

    isTarget: true,
    filtros: null,
//    style: 'overflow: auto',
    emptyText: '<div style="display: table;margin: 0 auto;font-size:110%;padding-top:30px;font-weight: bold;">Almacen vac&iacute;o</div>',
    plugins: new Ext.DataView.DragSelector ({dragSafe: true}),

    mmiMostrarDescripcion: 1, // 1 dentificador, 2 lote, 3 cantidad

    setMostrarEnDetalle: function (mode) {
        this.mmiMostrarDescripcion = mode;
        this.refresh();
    },


    cambiarAlmacen: function(alm){
        this.almacenSeleccionado = alm;
        this.store.baseParams = {
            almacen: alm.Id
        };
        this.almacen = alm.Id;
        this.store.reload();
    },

    /**
     * Inicializa el componente
     */
    initComponent: function() {

        this.addEvents('paletPartido');
        this.addEvents('paletModificado');
        this.addEvents('paletEliminado');

        this.filtros = new filtrosDepositos(this);

        // Template y logica de visualizacion
        this.tpl = new Ext.XTemplate (
            '<tpl for=".">',
                // Salto entre alturas
                '<tpl if="Fila == 1"><div style="clear:both"></div></tpl>',
                '<div class="thumb-cell {[this.getStateClass(values)]}" id="ubicacion{Id}" ubicacion={Id} Profundidad="{Profundidad}" Fila={Fila} Altura="{Altura}"  >',
                        '<div class=\"ubicMmis\"><div class=\"ubicDesc ubicMmiDesc\">{[this.getDescripcion(values)]}</div><div class=\"ubicDesc\">{Descripcion}</div></div>',
                '</div>',
                // Salto entre profundidades
                '<tpl if="Altura == 1 && Fila == this.getFilas()"><div style="clear:both; height:20px; width:1px"></div></tpl>',
            '</tpl>',
            {
                getDescripcion: function(data){
                    if (this.scope.mmiMostrarDescripcion == 1) return data.Mmi.Identificador;
                    if (this.scope.mmiMostrarDescripcion == 2) return data.Lote.Numero;
                    if (this.scope.mmiMostrarDescripcion == 3) return data.Mmi.CantidadActual;
                    if (this.scope.mmiMostrarDescripcion == 4) return Ext.util.Format.date(Date.parseDate(data.Lote.FechaVencimiento,"Y-m-d"),'d/m/Y');
                    if (this.scope.mmiMostrarDescripcion == 5) return Ext.util.Format.date(Date.parseDate(data.Lote.FechaElaboracion,"Y-m-d"),'d/m/Y');

                },

                // Sobreescribir este metodo para pintar las ubicaciones
                getStateClass: function (data) {
                    if (data.Mmi && data.Mmi.Id > 0) {
                        if (data.Mmi.RemitoArticuloSalida) {
                            cssClass = 'mmi-asignado ';
                        } else if (data.AsignadoODPDetalleTemporal) {
                            cssClass = 'mmi-asignado-prodadd ';
                        } else if (data.Mmi.Orden) {
                            cssClass = 'mmi-asignado-prod ';
                        } else {
                            cssClass = 'ubicacion-full ';
                        }


                    } else {
                        if (data.Existente == 1) {
                            cssClass = 'ubicacion-empty ';
                        } else {
                            cssClass = 'ubicacion-anulada ';
                        }
                    }
                    return cssClass + this.filtro(data)+ this.resaltar(data);
                },
                // Sobreescribir este metodo para setear el filtro
                filtro: function (data) {
                    return '';
                },
                // Sobreescribir este metodo para setear el filtro
                resaltar: function (data) {
                    dView = this.scope;
                    var rtn = '';
                    for (var i in this.resCfg)
                    {
                        rtn = rtn +' '+ ((dView.filtros[this.resCfg[i][1]](data, this.resCfg[i][0])) ? this.resCfg[i][2] : this.resCfg[i][3]);
                    }
                    return rtn;
                },
                resCfg: {
                },
                // TODO... complicado al pedo... ver otra forma
                getFilas: function () {
                    return parseInt(this.scope.store.reader.jsonData.cantFila);
                }
            }

        );
        this.tpl.scope = this;

        // Store
        this.store = new Ext.data.JsonStore ({
            url: this.url,
            baseParams: {almacen: this.almacen},
            autoLoad:  this.almacen != null,
            root: 'rows',
            idProperty: 'Id',
            storeId: 'viewStore',
            listeners: {
                 beforeload : function() {
                    //Ext.getCmp('admAlmacenes-statusbar').showBusy();
                 },
                 load : function() {
                    //Ext.getCmp('admAlmacenes-statusbar').clearStatus();
                 }
            },
            totalCount: 'cantFila',
            fields: [ 'Id', 'Descripcion', 'Observaciones', 'Almacen', 'Fila',
                      'Profundidad', 'Altura', 'Existente', 'Mmi', 'A', 'AsignadoODPDetalleTemporal',
                      'CE','CS','Lote' ]
        });

        // ## hack para tener scrollbar horizontal en los depositos rackeables
        this.store.on('load', function(t,r,o) {
            if (r[0] && r[0].json.CantFila) {
                this.el.setWidth(r[0].json.CantFila * 60);
            }
        },this);

        this.on('resize', function(t, adjWidth, adjHeight, rawWidth, rawHeight){
            var r = t.store.getAt(0);
            if (r && r.json.CantFila) {
                this.el.setWidth(r.json.CantFila * 60);
            }
        },this);

        // ## fin hack

        // Menu Contextual
        this.cmenu = new Ext.menu.Menu ({
            items: [
                {
                    text: 'Detalle',
                    iconCls: 'x-btn-text-icon',
                    icon: 'images/page_white_text.png',
                    scope: this,
                    handler: function() {
                        this.showNodeDetail();
                    }
                },{
                    text: 'Imprimir Identificador',
                    iconCls: 'x-btn-text-icon',
                    icon: 'images/printer.png',
                    scope: this,
                    handler: function() {
                        app.publish('/desktop/modules/js/commonApps/showUrl.js', {
                           action: 'launch',
                           url: '/Window/BirtReporter/report/template/Identificador_Palets_Entrantes/id/' + this.contextMmi,
                           width: 900,
                           height: 500,
                           title: 'Mmi'
                       });
                    }
                },{
                    text: 'Trazabilidad',
                    iconCls: 'x-btn-text-icon',
                    icon: 'images/arrow_divide.png',
                    scope: this,
                    handler: function() {
                        this.showNodeTrace();
                    }
                }
            ]
        });
        ERP.depositoDataView.superclass.initComponent.call(this);
    },
    /**
     * cambia el articulo a un Palet
     */
    cambiarArticuloPalet: function (paletId, articuloversion, callback) {
        Rad.callRemoteJsonAction ({
            url: '/Almacenes/Almacenes/cambiararticulommi',
            params: {
                id:       paletId,
                articuloversion: articuloversion
            },
            scope: this,
            success: function (response) {
                this.store.reload();
                window.app.publish('/desktop/notify', {
                    title: 'Info',
                    iconCls: 'x-icon-information',
                    html: 'Mmi Modificado'
                });
               this.fireEvent('paletModificado',this);
            },
            async: true
        });
    },
    /**
     * cambia la cantidad actual un Palet
     */
    cambiarCantidadPalet: function (paletId, cantidad, callback) {
        Rad.callRemoteJsonAction ({
            url: '/Almacenes/Almacenes/cambiarcantidadmmi',
            params: {
                id:       paletId,
                cantidad: cantidad
            },
            scope: this,
            success: function (response) {
                this.store.reload();
                window.app.publish('/desktop/notify', {
                    title: 'Info',
                    iconCls: 'x-icon-information',
                    html: 'Mmi Modificado'
                });
               this.fireEvent('paletModificado',this);
            },
            async: true
        });
    },
    /**
     * Parte un Palet
     */
    partirPalet: function (paletId, cantidad, callback) {
        Rad.callRemoteJsonAction ({
            url: '/Almacenes/Almacenes/partirmmi',
            params: {
                id:       paletId,
                cantidad: cantidad
            },
            scope: this,
            success: function (response) {
                this.store.reload();
                window.app.publish('/desktop/notify', {
                    title: 'Info',
                    iconCls: 'x-icon-information',
                    html: 'Mmi Partido'
                });
               this.fireEvent('paletPartido',this);
            },
            async: true
        });
    },
    /**
     * Parte un Palet
     */
    eliminarPalet: function (paletId, cantidad, callback) {
        Rad.callRemoteJsonAction ({
            url: '/Almacenes/Almacenes/eliminarmmi',
            params: {
                id: paletId
            },
            scope: this,
            success: function (response) {
                this.store.reload();
                window.app.publish('/desktop/notify', {
                    title: 'Info',
                    iconCls: 'x-icon-information',
                    html: 'Mmi Eliminado'
                });
               this.fireEvent('paletEliminado',this);
            },
            //async: true
        });
    },
    /**
     * Resaltar MMis
     */
    resaltar: function (param, fnFiltro, resaltada, normal,name) {
        if (!normal) normal = '';
        if (!resaltada) resaltada = '';
        this.tpl.resCfg[name]    = new Array()
        this.tpl.resCfg[name][0] = param;
        this.tpl.resCfg[name][1] = fnFiltro;
        this.tpl.resCfg[name][2] = resaltada;
        this.tpl.resCfg[name][3] = normal;
        this.refresh();
    },
    /**
     * Quitar Resaltar MMis
     */
    quitarResaltado: function(name) {
        delete(this.tpl.resCfg[name]);
        this.refresh();
    },
    //Indica si se estan asignando Mmis
    asignando: false,
    // Suma la cantidad de items de todos los palets seleccionados
    sumarSeleccionados: function (selections) {
        var total = 0;
        for (var i = 0, len = selections.length; i < len; i++) {
            selItem = this.store.getAt(selections[i].viewIndex);
            if (!selItem.data.Mmi.RemitoArticuloSalida) {
                total = total + Ext.num(selItem.data.Mmi.CantidadActual, 0);
            }
        }
        return total;
    },

    showNodeDetail: function () {
        t = this.tip;
        t.showBy(this.contextNode);

        t.update ('<div class="panelDeDetalles panelDetalleAzul">'+this.getDetalleMmiHtml(this.contextNode.viewIndex)+'</div>', false,function() {
                this.tip.syncShadow(); // fix para las sombras
            }
        );
    },

    showNodeTrace: function() {

        this.publish('/desktop/modules/Almacenes/Trazabilidad', {
            action: 'launch',
            id: this.contextMmi
        });

    },

    getDetalleMmiHtml: function(index) {
        var data =  this.store.getAt(index).data;
        if (data.Mmi.Id == null) return "<h3>Ubicación Vacía</h3>";

        var fe, fv, fi;

        if (data.Lote.FechaElaboracion) {
            fe = Ext.util.Format.date(Date.parseDate(data.Lote.FechaElaboracion,"Y-m-d"),'d/m/Y');
        }
        if (data.Lote.FechaVencimiento) {
            fv = Ext.util.Format.date(Date.parseDate(data.Lote.FechaVencimiento,"Y-m-d"),'d/m/Y');
        }
        if (data.Lote.FechaIngreso) {
            fi = Ext.util.Format.date(Date.parseDate(data.Lote.FechaIngreso,"Y-m-d h:i:s"),'d/m/Y h:i:s');
        }



        tooltip = "<h3>MMI: " + data.Mmi.Identificador + "</h3>"+
        "<div class='detalle'>"+
            "<b>Articulo:</b>"+
            "<span>" + data.A.Descripcion + "</span>"+
            "<b>Fecha de Ingreso:</b>"+
            "<span>" + (fi || '-') +"</span>"+
            "<b>Cantidad Actual / Original:</b>"+
            "<span>"+ data.Mmi.CantidadActual +' / '+data.Mmi.CantidadOriginal+ "</span>"+
            "<hr><b>Lote:</b>"+
            "<span>" + data.Lote.Numero + "</span>"+
            "<b>Elaboración - Vencimiento:</b>"+
            "<span>" + (fe || '-') + ' - ' + (fv || '-') +"</span>";

        if (data.CE && data.CE.Numero || data.CS && data.CS.Numero) {
            tooltip += '<hr>';
        }
        if (data.CE && data.CE.Numero) {
           tooltip += '<b>Ingreso</b>';
           tooltip += "<span class='sub'>Remíto: " + data.CE.Punto+ '-' + data.CE.Numero +"</span>";
        }
        if (data.CS && data.CS.Numero) {
           tooltip += '<b>Despacho</b>';
           tooltip += "<span class='sub'>Asignado a remíto: " + data.CS.Punto + '-' + data.CS.Numero + "</span>";
        }

        tooltip += "</div>";
        return tooltip;
    },

    listeners: {
        // Menu contextual
        contextmenu: function(i, index,  node, e) {
            if (node) {
                this.contextNode = node;
                dt = this.store.getAt(this.contextNode.viewIndex);
                if (dt.data.Mmi.Id) {
                    this.contextMmi = dt.data.Mmi.Id;
                    this.cmenu.showAt(e.getXY());
                }
            }
            e.stopEvent();
         },
         beforeselect: function(i, n, s) {
            var estado = (!Ext.fly(n).hasClass('ubicacion-no-resaltada'));
            return estado;
         },
         destroy: function() {
            this.tip.destroy();
         }
    },

    afterRender: function() {
        Ext.DataView.prototype.afterRender.apply(this, arguments);
        this.tip = new Ext.ToolTip ({
            autoWidth: false,
            width: 300,
            height: 300,
            dismissDelay: 0,
            trackMouse: false,
            autoHide: false,
            shadow: true,
            closable: true,
            floating: true
        });
    }
});