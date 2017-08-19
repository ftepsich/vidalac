/**
 * Rad Framework
 * Copyright(c) 2010 SmartSoftware
 * @autor: Martin A. Santangelo
 */
Ext.ns('Rad');

/**
 * Parchesor para el paginado de las grillas
 *
 * Con esto permito que al hacer un grid.store.load(p) con los parametros que se nos canten
 * automaticamente se agreguen los parametros de paginacion de no estar los mismos
 */
Ext.override(Ext.PagingToolbar,{
    beforeLoad : function(t, options){
        if (this.rendered && this.refresh){
            this.refresh.disable();
        }
        if (options.params.start == undefined) {
            options.params.start = this.cursor;
            options.params.limit = this.pageSize;
        }
    }
});

/**
 *  Sobreescribo el metodo load de los stores asi envian los parametros de peticion configuracion automaticamente
 */
Ext.override(Ext.data.Store, {
    iniSection: null,

    load : function(options){
        options = Ext.apply({}, options);
        this.storeOptions(options);
        if(this.sortInfo && this.remoteSort){
            var pn = this.paramNames;
            options.params = Ext.apply({}, options.params);
            options.params[pn.sort] = this.sortInfo.field;
            options.params[pn.dir] = this.sortInfo.direction;
        }

        // Si el store no esta configurado enviamos el parametro reconfigure
        if (!options.params) options.params = {};
        options.params.section     = this.iniSection;

        if (!this.fields) {
            options.params.reconfigure = true;
        } else {
            if (options.params) {
                delete options.params.reconfigure;
            }
        }

//        if (this.paginator && options.params && options.params.start == undefined) {
//            options.params = Ext.apply(options.params, this.paginator);
//        }
        try {
            return this.execute('read', null, options); // <-- null represents rs.  No rs for load actions.
        } catch(e) {
            this.handleException(e);
            return false;
        }
    }
});

//############### RAD Grid ############################################################
Rad.GridPanel = function(config) {
    // call parent constructor
    Rad.GridPanel.superclass.constructor.call(this, config);
}

Rad.EditorGridPanel = function(config) {
    // call parent constructor
    Rad.EditorGridPanel.superclass.constructor.call(this, config);
}

/**
 *  Funcionalidad comun de todas las metagrids
 */
var metagrid = {

    /**
     * Botones de la barra de herramientas
     */
    topButtons: {
        add: true,
        del: true,
        edit: true
    },
    enableDragDrop : true,
    ddGroup : 'grillassistema',
    model: null,                     // Nombe del modelo al que apunta
    module: null,                        // Nombe del modelo al que apunta
    reloadOnModelUpdate: true,       // Recargar en caso de que el modelo se modifique en otra ventana
    initPreview: true,
    loadAuto: true,                  // Ejecuta el load del store al inicio
    configured: false,                   // indica si la grilla configuro (primera peticion)
    withPaginator: true,
    addingRow: false,                    // Indica si se esta agregando una nueva fila
    autoSave:  false,
    detailGrid: null,            // Si se pasa como parametro {id: 'gridid',remotefield:'idComprobantes', localfield: 'tipoCoprobante'}
    parentGrid: null,            // Apunta a la grilla padre de tener
    parentForm: null,            // Apunta al formulario padre de tener
    parentRemoteField: null,
    parentLocalField: null,
    iniSection: 'default',               // Indica que seccion se tomara del Ini del modelo

    /**
     * Habilitar ordenamiento por varias columnas
     */
    multisort: false,

    /**
     * Botones de la barra de herramientas
     */
    loadMask: {
        msg: 'Cargando ...'
    },

    // se llama antes de crear el column model, aqui se pueden agregar nuevas columnas o alterar las existentes
    //onBeforeCreateColumns: function(columns) {}


    /**
     * Agrega las barras de tareas a la grilla
     * Esta funcion debe ser sobreescrita
     */
    buildToolbar: function() {
    },

    /**
     * Crea los objetos necesarios para el multiorden
     */
    createMultiSorter: function() {
        var reorderer = new Ext.ux.ToolbarReorderer();
        var droppable = new Ext.ux.ToolbarDroppable({
            scope:this,

            /**
             * Creates the new toolbar item from the drop event
             */
            createItem: function(data) {
                var column = this.getColumnFromDragDrop(data);

                return this.scope.createSorterButton({
                    text    : column.header,
                    sortData: {
                        field: column.dataIndex,
                        direction: "ASC"
                    }
                });
            },

            /**
             * Custom canDrop implementation which returns true if a column can be added to the toolbar
             * @param {Object} data Arbitrary data from the drag source
             * @return {Boolean} True if the drop is allowed
             */
            canDrop: function(dragSource, event, data) {

                var sorters = dragSource.grid.getSorters(),
                    column  = this.getColumnFromDragDrop(data);

                if (column.multisortable == false) return false;

                for (var i=0; i < sorters.length; i++) {

                    if (sorters[i].field == column.dataIndex) {
                        return false;
                    }
                }
                return true;
            },

            afterLayout: function () {
                this.scope.doSort();
            },

            /**
             * Helper function used to find the column that was dragged
             * @param {Object} data Arbitrary data from
             */
            getColumnFromDragDrop: function(data) {
                var index    = data.header.cellIndex,
                    colModel = this.scope.colModel,
                    column   = colModel.getColumnById(colModel.getColumnId(index));

                return column;
            }
        });

        //toolbar de orden
        this.multisortbar = new Ext.Toolbar({
            renderTo: this.tbar,
            plugins : [reorderer, droppable],
            listeners : {
                reordered: {
                    fn:function(button) {
                        this.changeSortDirection(button, false);
                    },
                    scope: this
                },
                afterrender: {
                    fn:function(button) {
                       var dragProxy = this.getView().columnDrag,
                       ddGroup   = dragProxy.ddGroup;
                       droppable.addDDGroup(ddGroup);
                    },
                    scope: this
                }
            },
            items: [
                {
                    icon:'images/delete.png',
                    title: 'Quitar Orden',
                    handler: function (){
                        this.clearSort();
                    },
                    scope:this
                },
                new Ext.form.Label({
                    html: 'Orden:'
                })
            ]
        });


    },

    /**
     * Callback handler used when a sorter button is clicked or reordered
     * @param {Ext.Button} button The button that was clicked
     * @param {Boolean} changeDirection True to change direction (default). Set to false for reorder
     * operations as we wish to preserve ordering there
     */
    changeSortDirection: function(button, changeDirection) {
        var sortData = button.sortData,
            iconCls  = button.iconCls;

        if (sortData != undefined) {
            if (changeDirection !== false) {
                button.sortData.direction = button.sortData.direction.toggle("ASC", "DESC");
                button.setIconClass(iconCls.toggle("sort-asc", "sort-desc"));
            }

            this.store.clearFilter();
            this.doSort();
        }
    },

    /**
     * Returns an array of sortData from the sorter buttons
     * @return {Array} Ordered sort data from each of the sorter buttons
     */
    getSorters: function() {
        var sorters = [];

        Ext.each(this.multisortbar.findByType('button'), function(button) {
            if (button.multisortbutton) {
                sorters.push(button.sortData);
            }
        }, this);

        return sorters;
    },

    /**
     * Convenience function for creating Toolbar Buttons that are tied to sorters
     * @param {Object} config Optional config object
     * @return {Ext.Button} The new Button object
     */
    createSorterButton: function(config) {
        config = config || {};

        Ext.applyIf(config, {
            multisortbutton: true, // lo agrego para saber q es un boton de orden
            listeners: {
                click:{
                    fn: function(button, e) {
                        this.changeSortDirection(button, true);

                    },
                    scope: this
                }

            },
            iconCls: 'sort-' + config.sortData.direction.toLowerCase(),
            reorderable: true
        });

        return new Ext.Button(config);
    },

    clearSort: function() {
        var sorters = this.getSorters();
        Ext.each(this.multisortbar.findByType('button'), function(button) {
            if (button.multisortbutton) {
                button.destroy();
            }
            this.doSort();
        }, this);

    },

    /**
     * Tells the store to sort itself according to our sort data
     *
     * Uso los parametros base para setear el orden porque sino si se setean filtros particulares y se recarga el combo se pierde el orden.
     */
    doSort: function() {
        var sorters = this.getSorters();
//        var lastop = this.store.lastOptions;
        var s = '', f = '';

        delete this.store.baseParams.sort;
        delete this.store.baseParams.dir;

        Ext.each(sorters, function(item) {

            f = f.concat(item.field,','); // Array of fields, respective
            s = s.concat(item.direction,','); // Array of directions
        });

        Ext.apply(this.store.baseParams, {sort: f, dir: s});

        this.store.reload();
    },

    updateRecorFromJson: function(record, values) {
        record.fields.each(function(f) {
            var v = values[f.name];
            if (v != null) {
                record.set(f.name, f.convert(v));  // Indispensable llamar a la funcion convert, ya que algunos datos como las fechas el store no las guarda como texto sino co
            } else {
                record.set(f.name, null);
            }
        }, this);
    },

    /**
     * @cfg {Boolean} true to mask the grid if there's no data to make
     * it even more obvious that the grid is empty.  This will apply a
     * mask to the grid's body with a message in the middle if there
     * are zero rows - quite hard for the user to miss.
     */
    maskEmpty: true,
    /**
     * key number for new records (will be adjusted by new records)
     */
    newKey: -1,
//    paging: {
//        perPage: this.pageSize || 25
//    },
    /**
     * @cfg {String} primaryKey The database table primary key.
     */
    primaryKey: 'Id',
    stripeRows: true,
    trackMouseOver: true,

    storeUpdatePublish: function (action, data){
        e = {
            obj:    this,
            action: action,
            e:      data
        };
        this.publish( '/rad/model/'+this.model+'/',e);
    },

    onModelUpdate: function() {},

    __suscribeToModelEvent: function (model) {
        this.subscribe( '/rad/model/'+model+'/', function(config, channel) {

            // si el evento lo levanto la misma grilla lo ignoramos
            if (this == config.obj) return;
            if (this.reloadOnModelUpdate) {
                if (this.store.getTotalCount()) this.store.reload();
            }

            this.onModelUpdate(config);

        },this);
    },

    /**
     * Construye el store de la grilla
     */
    buildStore: function () {
        this.writer = new Ext.data.JsonWriter({
            encode: true,
            returnJson: true,
            writeAllFields: false
        });

        this.store = new Ext.data.GroupingStore({
//            paginator: this.paging,
            autoLoad : this.loadAuto,
            autoSave : this.autoSave,
            pruneModifiedRecords: true,
            autoDestroy: true,
            iniSection: this.iniSection,
            reader: new Ext.data.JsonReader({
                idProperty: 'Id',
                messageProperty: 'msg'
            }),
            proxy : this.proxy,
            writer : this.writer,
            remoteSort :  true,
            groupOnSort: false,
            // GroupingView: true,
            listeners: this.store.listeners
        }); // mask the grid if there is no data if so configured
    },

    /**
     * Inicializa la grilla
     */
    initComponent: function(config) {
        this.addEvents('afterdeleterows', 'aftersavefield');
        //this.on('afteredit',this.saveField,this);

        this.buildToolbar();

        if (this.tbar) {
            this.addReportButton();

            // Multiorden
            if (this.multisort) {

                this.on('render',function(){
                    this.createMultiSorter();
                    this.syncSize();
                },this);

            }
        }

        Ext.applyIf(this, {
            plugins: [],
            pagingPlugins: [],
            // customize view config
            viewConfig: {
                emptyText: 'No hay Datos',
                forceFit: true
            }
        });
        if (this.filters) {
            this.filters = new Ext.ux.grid.GridFilters({
                filters: []
            });
            this.plugins.push(this.filters);
            this.pagingPlugins.push(this.filters);
        }
        if (!this.store) this.store = {};
        if (!this.store.listeners) this.store.listeners = {};

        this.store.listeners.metachange = {
            fn: this.onMetaChange,
            scope: this
        }
        var postUrl = '';

        if (this.module){
            postUrl = '/m/'+this.module;
        }

        if (this.fetch) {
            postUrl = postUrl+'/fetch/'+this.fetch;
        }

        this.proxy = new Ext.data.HttpProxy({
            api: {
                read    : this.url+'/list/model/'+this.model+postUrl,
                create  : this.url+'/createrow/model/'+this.model+postUrl,
                update  : this.url+'/saverows/model/'+this.model+postUrl,
                destroy : this.url+'/deleterow/model/'+this.model+postUrl
            }
        });

        this.buildStore();

        if (this.model) {
            this.__suscribeToModelEvent(this.model);
            this.on('afterdeleterows',function(r) {
                this.storeUpdatePublish('remove',r);
            }, this);
            this.store.on('update',function (s,r,a) {
                if (a == Ext.data.Record.COMMIT)
                    this.storeUpdatePublish('update',r);
            }, this);
        }

        if (this.maskEmpty) {
            this.store.on('load', function() {
                var el = this.getGridEl();
                // Si tiene hijos los enmascaramos
                if (this.detailGrid != null) {
                    //this.detailGrid.parentGrid = this;
                    this.maskDetailGrids();
                }
                if (this.store.getTotalCount() == 0 && typeof el == 'object') {
                    if (this.store.baseParams['filter[0][data][type]'] == undefined) {
                        //el.mask('No hay Datos', 'x-mask');
                    }
                } else {
                    el.unmask();
                }
            },
            this);
        }
        //Create Paging Toolbar
        if (this.withPaginator) {
            this.pagingToolbar = new Ext.PagingToolbar({
                store: this.store,
                //pageSize: this.options.pageSize,//makes this global for all who need it
                pageFit: true,
                pageSize: this.pageSize || 25,
                beforePageText :'Pág',
                //default is 20
                plugins: this.pagingPlugins,
                displayInfo: true,
                //default is false (to not show displayMsg)
                displayMsg: '{0} - {1} de {2}',
                //display message when no records found
                emptyMsg: "No hay Datos para mostrar"
            }); //Add a bottom bar
            this.bbar = this.pagingToolbar;
        }
        /*
         * JSONReader provides metachange functionality which allows you to create
         * dynamic records natively
         * It does not allow you to create the grid's column model dynamically.
         */
        if (this.columns && (this.columns instanceof Array)) {
            this.colModel = new Ext.grid.ColumnModel(this.columns);
            delete this.columns;
        } // Create a empty colModel if none given
        if (!this.colModel) {
            this.colModel = new Ext.grid.ColumnModel([]);
        }
        /**
         * defaultSortable : Boolean
         * Default sortable of columns which have no sortable specified
         * (defaults to false)
         * Instead of specifying sorting permission by individual columns
         * can just specify for entire grid
         */
        this.colModel.defaultSortable = true;
        Rad.GridPanel.superclass.initComponent.call(this);

        /**
         * Se asigna aca, pq si es despues de la llamada al constructor del padre
         * me toma el this como el objeto metagrid y lo comparte para todas las grillas
         */
        this.store.baseParams = this.baseParams || {};

        // Tiene configurada una grilla detalle? entonces la enganchamos al evento rowselect
        if (this.detailGrid != null) {
            //this.detailGrid.parentGrid = this;
            var sm = this.getSelectionModel();
            sm.on('beforerowselect', function (grid, idx,k, record)
            {
                this.reloadChildGrids(record);
            }, this);

//            sm.on('rowdeselect', function (t, i, r) {
//                this.maskDetailGrids();
//            },this);
        }
    },
    /**
     * Enmascara las grillas hijas
     */
    maskDetailGrids: function() {
        Ext.each(this.detailGrid, function(detailGrid) {
            if (detailGrid instanceof Rad.GridPanel) {
                var gridD =  detailGrid;
            } else {
                var gridD =  Ext.getCmp(detailGrid.id);
            }
            if (!gridD) {
                alert("No se encontro la grilla hija "+ detailGrid.id +"  para "+ this.id);
            }
            // Si tiene datos los borro
            if(gridD.store.getCount()) gridD.store.loadData({rows:[],count:0},false);
            gridD.getGridEl().parent().mask('Seleccione el dato relacionado...', 'x-mask');
        },this);
    },

    addReportButton: function() {
        if ( this.report && typeof this.report != "undefined") {
            Ext.each(this.report, function(r) {
                if (r.separator) {
                    this.tbar.push('-');
                }

                if (r.menu) {
                    Ext.each(r.menu, function(rc) {
                        Ext.applyIf(rc, {
                            text:       'Reporte',
                            icon:       'images/printer.png',
                            cls:        'x-btn-text-icon',
                            ref:        '../reportButton',
                            handler:    (rc.file) ? this.reportWindow : Ext.EmpyFn,
                            scope:      this,
                        });

                    },this);
                } else {
                    Ext.applyIf(r, {
                        text:       'Reporte',
                        icon:       'images/printer.png',
                        cls:        'x-btn-text-icon',
                        ref:        '../reportButton',
                        handler:    (r.file) ? this.reportWindow : Ext.EmpyFn,
                        scope:      this,
                        disabled:   !this.loadAuto
                    });
                }

                this.tbar.push(r);
            }, this);
        }
    },

    reportWindow: function (r) {
        if (typeof r.requireSelected != 'undefined' && r.requireSelected == false) {
            var selected = { id: -1 };
        } else {
            var selected = this.getSelectionModel().getSelected();
        }
        if (selected) {
            this.publish('/desktop/modules/Window/birtreporter', {
                action:     'launch',
                template:   r.file,
                id:         selected.id,
                params:     r.params,
                output:     r.output || 'pdf',
                width:      (r.window && r.window.width) || null,
                height:     (r.window && r.window.height) || null
            });
        } else {
            this.publish('/desktop/showMsg/',{
                title: 'Atencion',
                msg: 'Seleccione un registro para ver el reporte'
            });
        }
    },

    //recibe el registro seleccionado actualmente
    reloadChildGrids: function(record) {
        if (!this.detailGrid) return;
        Ext.each(this.detailGrid, function(detailGrid) {
            var gridD =  Ext.getCmp(detailGrid.id);
            if (gridD != undefined) {
                gridD.parentGrid = this;
                value = record.get(detailGrid.localfield)
                gridD.loadAsDetailGrid(detailGrid, value);
            }
        },this);
    },

    /**
     * Borra los registros selecionados
     */
    deleteRows: function() {
        if (this.stopEditing) this.stopEditing();
        else if (this.editor.stopEditing) this.editor.stopEditing();
        records = this.getSelectionModel().getSelections();
        this.store.remove(records);
        this.store.save();
        this.fireEvent('afterdeleterows', records);
    },

    /**
     * Crea la configuracion para un filtro
     */
    buildFilter: function(i, field, value, type, cmp ) {
        if (!cmp)  cmp  = 'eq';
        if (!type) type = 'numeric';

        var rt = {};
        rt['pfilter['+i+'][field]']            = field;
        rt['pfilter['+i+'][data][value]']      = value;
        rt['pfilter['+i+'][data][type]']       = type;
        rt['pfilter['+i+'][data][comparison]'] = cmp;

        return rt;
    },

    setPermanentFilter: function(i, field, value, type, cmp) {
        if (i > this.permanentFilterCount) this.permanentFilterCount = i+1;
        if (!this.store.baseParams) this.store.baseParams ={};

        Ext.apply(
            this.store.baseParams,
            this.buildFilter(i, field, value, type, cmp )
        );
    },

    clearPermanentFilter: function (i) {
        delete this.store.baseParams['pfilter['+i+'][field]'];
        delete this.store.baseParams['pfilter['+i+'][data][value]'];
        delete this.store.baseParams['pfilter['+i+'][data][type]'];
        delete this.store.baseParams['pfilter['+i+'][data][comparison]'];
    },

    /**
     * Agrega un filtro permanente a la grilla
     */
    addPermanentFilter: function(field, value, type, cmp) {
        if (!this.permanentFilterCount) this.permanentFilterCount = 0;
        if (!this.store.baseParams) this.store.baseParams ={};
        Ext.apply(
            this.store.baseParams,
            this.buildFilter(this.permanentFilterCount, field, value, type, cmp)
        );
        this.permanentFilterCount++;
    },

    /**
     * Carga la grilla como hija de otra filtrando por el parametro correspondiente
     */
    loadAsDetailGrid: function (detailConfig, value)
    {
        if (!value) value = 0;
        this.parentLocalField  = detailConfig.remotefield;
        this.parentRemoteField = detailConfig.localfield;
        this.setPermanentFilter(0,detailConfig.remotefield,value);
        this.store.load();
    },


    /**
     * Configure the reader using the server supplied meta data.
     * This grid is observing the store's metachange event (which will be triggered
     * when the metaData property is detected in the returned json data object).
     * This method is specified as the handler for the that metachange event.
     * This method interrogates the metaData property of the json packet (passed
     * to this method as the 2nd argument ).  The local meta property also contains
     * other user-defined properties needed:
     *     fields
     *     defaultSortable
     *     id
     *     root
     *     start
     *     limit
     *     sortinfo.field
     *     sortinfo.direction
     *     successProperty
     *     totalProperty
     * @param {Object} store
     * @param {Object} meta The reader's meta property that exposes the JSON metadata
     */
    onMetaChange: function(store, meta) { // avoid loading meta on store reload
        //delete(store.lastOptions.params.meta);
        var columns = [],
        editor,
        plugins,
        storeCfg,
        l,
        convert; // set primary Key

        this.primaryKey = meta.idProperty;

        Ext.each(meta.fields, function(col) { // if plugin specified
            if (col.plugin !== undefined) {
                columns.push(eval(col.plugin));
                return;
            } // if header property is not specified do not add to column model
            if (col.header == undefined ) {
                return;
            } // if not specified assign dataIndex = name
            if (typeof col.dataIndex == "undefined") {
                col.dataIndex = col.name;
            } //if using gridFilters extension

            if (typeof col.renderer == "string") { // if specified Ext.util or a function will eval to get that function
                if (col.renderer.indexOf("Ext") < 0 && col.renderer.indexOf("function") < 0) { //                    col.renderer = this.setRenderer(col.renderer);
                    col.renderer = this[col.renderer].createDelegate(this);
                } else {
                    col.renderer = eval(col.renderer);
                }
            }

            // if listeners specified in meta data
            l = col.listeners;
            if (typeof l == "object") {
                for (var e in l) {
                    if (typeof e == "string") {
                        for (var c in l[e]) {
                            if (typeof c == "string") {
                                l[e][c] = eval(l[e][c]);
                            }
                        }
                    }
                }
            }

            // Si esta activado el multiordenamiento no usamos el sortable en las columnas
            if (this.multisort) {
                if (col.sortable) {
                    col.sortable = false;
                    col.multisortable = true;
                } else {
                    col.multisortable = false;
                }
            }

            // if convert specified assume it's a function and eval it
            if (col.convert) {
                col.convert = eval(col.convert);
            }

            // Editores
            editor = col.editor;
            if (editor) {
                switch (editor.xtype) {
                    case 'combo' :
                    case 'xcombo':
                        if (col.editor.store) {
                            storeCfg = col.editor.store;
                            storeCfg.config.autoDestroy = true;
                            col.editor.store = new Ext.data[storeCfg.storeType](storeCfg.config);
                        }
                        col.editor = Ext.ComponentMgr.create(editor, 'textfield');
                        break;
                    case 'xdatefield':
                        //Fix: en la grilla no funciona bien el xdatafield asi q usamos el comun
                        // editor.xtype = 'datefield';
                        col.editor = Ext.ComponentMgr.create(editor, 'textfield');
                        break;
                    default:
                        col.editor = Ext.ComponentMgr.create(editor, 'textfield');
                        break;
                }
                plugins = editor.plugins;
                delete(editor.plugins); //configure any listeners specified for this column's editor
                l = editor.listeners;
                if (typeof l == "object") {
                    for (var e in l) {
                        if (typeof e == "string") {
                            for (var c in l[e]) {
                                if (typeof c == "string") {
                                    l[e][c] = eval(l[e][c]);
                                }
                            }
                        }
                    }
                }
            }
            if (plugins instanceof Array) {
                editor.plugins = [];
                Ext.each(plugins, function(plugin) {
                    plugin.name = plugin.name || col.dataIndex;
                    editor.plugins.push(Ext.ComponentMgr.create(plugin));
                });
            } // add column to colModel config array
            columns.push(col);
        },
        this); // end of columns loop

        if (this.onBeforeCreateColumns != undefined)
        {
            this.onBeforeCreateColumns(columns);
        }

        var cm = new Ext.grid.ColumnModel(columns);

        if (meta.defaultSortable != undefined) {
            cm.defaultSortable = meta.defaultSortable;
        }

        var store = this.store; // Reconfigure the grid to use a different Store and Column Model. The View
        // will be bound to the new objects and refreshed.

        this.reconfigure(store, cm);

        if ( meta.groupField != undefined && store.groupField == undefined && meta.groupField != '' && this.view instanceof Ext.grid.GroupingView ) {
           store.groupField = meta.groupField;
           store.applyGroupField();

        }

        if (this.stateful) {
            this.initState();
        }
        if (this.onAfterMetaChange != undefined)
        {
            this.onAfterMetaChange();
        }
    }
};



Ext.extend(Rad.GridPanel, Ext.grid.GridPanel,metagrid);



/**
 * Implementacion comun a las grillas de edicion
 */
CommonEditorGrid = {
    /**
     *  Obtiene el valor del padre de existir este
     */
    getParentValue: function () {
        if (this.parentGrid) {
            var selected = this.parentGrid.getSelectionModel().getSelected();
            if (!selected) {
                this.publish('/desktop/showError','Seleccione primero el dato relacionado');
                this.addingRow = false;
                return;
            }
            return selected.get(this.parentRemoteField);
        } else {
            if (this.parentForm) {                      // Si tiene padre tomamos el valor automaticamente
                field = this.parentForm.getForm().findField(this.parentRemoteField);
                if (!field) {
                    this.publish('/desktop/showError','El campo '+this.parentRemoteField+' no se encuentra');
                } else {
                    return field.getValue();
                }
            }
        }
    },

    /**
     * Crea el store de la grilla
     */
    buildStore: function () {
        this.writer = new Ext.data.JsonWriter({
            encode: true,
            returnJson: true,
            writeAllFields: false
        });

        this.store = new Ext.data.GroupingStore({
            autoLoad : this.loadAuto,
            autoSave : this.autoSave,
            pruneModifiedRecords: true,
            iniSection: this.iniSection,
            autoDestroy: true,
            reader: new Ext.data.JsonReader({
                idProperty: 'Id',
                messageProperty: 'msg'
            }),
            proxy : this.proxy,
            writer: this.writer,
            remoteSort :  true,
            // groupOnSort: true,
            remoteGroup: true,
            listeners: this.store.listeners
        }); // mask the grid if there is no data if so configured
    },

    /**
     * Borra los registros selecionados
     */
    deleteRows: function() {
        if (this.stopEditing) this.stopEditing();
        else if (this.editor.stopEditing) this.editor.stopEditing();

        this.publish('/desktop/showMsg/',{
            title:'Borrar',
            msg: '¿Está seguro que desea borrar los registros seleccionados?',
            buttons: Ext.Msg.YESNO,
            fn: function(btn) {
                if(btn == 'yes') {
                    records = this.getSelectionModel().getSelections();
                    this.store.remove(records);
                    this.fireEvent('afterdeleterows', records);
                }
            },
            scope:this,
            icon: Ext.MessageBox.QUESTION
        });
    },

    /**
     * Crea un registro haciendo una llamada por ajax al servidor
     */
    createRow: function() {
        if (this.addingRow) return;
        var u = new this.store.recordType();
        if (this.editor) {
            this.editor.stopEditing();
        }
        if(this.stopEditing){
            this.stopEditing();
        }
        var el = this.getGridEl();
        el.unmask();
        this.store.insert(0, u);
        this.addingRow = true;
    },

    onStoreWrite: function (store, action, result) {
        if (action == Ext.data.Api.actions.create){
            this.getSelectionModel().selectRow(0);
            selected = this.getSelectionModel().getSelected();
            parentValue = this.getParentValue();
            if (parentValue === false) return;
            selected.data[this.parentLocalField] = parentValue;
            this.fireEditor();
        }
    }
}

/**
 *  Grilla de edicion en linea que usa el plugin RowEditor
 *
 */

Ext.extend(Rad.EditorGridPanel, Rad.GridPanel, {
    withRowEditor: false,
    /**
     * Inicializo la grilla
     */

    initComponent: function () {
        // Si tiene q usar el row editor lo creamos
        Ext.applyIf(this, {
            plugins: []
        });
        if (this.withRowEditor) {
            this.editor = new Ext.ux.grid.RowEditor({
                saveText: 'Guardar',
                cancelText: 'Cancelar',
                commitChangesText: 'Debe guardar o cancelar los cambios',
                errorText: 'Error'
            });
            this.editor.on('canceledit', function () {
                var selected = this.grid.getSelectionModel().getSelected();
                if (this.grid.addingRow && !this.grid.deferSave) {
                    if (selected.get(selected.store.reader.meta.idProperty) == 0) {
                        /**
                     * Suspendo los eventos para q el writer no intente borrar el registro del lado del servidor
                     */
                        this.grid.store.suspendEvents();
                        this.grid.store.remove(selected);
                        this.grid.store.resumeEvents();
                        this.grid.getView().refresh();
                    } else {
                        // sino enviamos el publish de que se agrego una linea
                        this.grid.storeUpdatePublish('add',selected);
                    }
                }
                this.grid.addingRow = false;
            });
            this.plugins.push(this.editor);
        }
        Rad.EditorGridPanel.superclass.initComponent.call(this);
        this.store.on('write', this.onStoreWrite, this);
        if (!this.loadAuto) {
            this.store.on('beforeload',  this.enableButtons, this);
        }
    },

    enableButtons: function () {
        this.disableButtons(false);
    },
    disableButtons: function (disabled) {
        var toolbar = this.getTopToolbar();
        if (!toolbar) return;
        toolbar.items.each(function(item) {
            if (item.setDisabled) {
                item.setDisabled(disabled);
            }
        },this);
    },
    fireEditor: function () {
        if (this.editor) this.editor.startEditing(0);
    },
    /**
     *  Construimos el toolbar
     */
    buildToolbar: function() {
        //var id = this.getId();
        var cfg = [];

        if (this.topButtons.add) {
            cfg.push({
                text:     'Agregar',
                ref:      '../addButton',
                iconCls:  'add',
                handler:  this.createRow,
                scope:    this,
                disabled: !this.loadAuto
            });
        }
        if (this.topButtons.del) {
            cfg.push({
                text:    'Borrar',
                ref:      '../delButton',
                iconCls: 'remove',
                handler: this.deleteRows,
                scope:   this,
                disabled:!this.loadAuto
            });
        }
        if (cfg.length == 0) return;
        this.tbar = cfg;
    }
});
// agrego la funcionalidad comun a las grillas
Ext.override(Rad.EditorGridPanel, CommonEditorGrid);


/**
 *  Grilla de edicion rapida en linea
 */
Rad.FastEditorGridPanel = Ext.extend(Ext.grid.EditorGridPanel, metagrid);
Rad.FastEditorGridPanel = Ext.extend(Rad.FastEditorGridPanel, {

    saveRecords : function () {
        this.stopEditing();
        this.store.save();
    },
    /**
     *  Construimos el toolbar
     */
    buildToolbar: function() {
        this.autoSave = false;
        var id = this.getId();

        var cfg = [];
        if (this.topButtons.add) {
            cfg.push({
                text:    'Guardar',
                icon: '/images/accept.png',
                ref: '../addButton',
                handler: this.saveRecords,
                scope:   this
            });
        }

        if (this.topButtons.del) {
            cfg.push({
                text:    'Borrar',
                iconCls: 'remove',
                ref: '../delButton',
                handler: this.deleteRows,
                scope:   this
            });
        }
        if (cfg.length == 0) return;
        this.tbar = cfg;
    },
    /**
     * Inicializo la grilla
     */
    initComponent: function () {
        Rad.FastEditorGridPanel.superclass.initComponent.call(this);
        this.store.on('write', this.onStoreWrite, this);
    },

    fireEditor: function () {
        this.startEditing(0);
    }
});
// agrego la funcionalidad comun a las grillas
Ext.override(Rad.FastEditorGridPanel, CommonEditorGrid);

// ############### ABM Window ############################################################

/**
 * Esta clase solo debe ser usada como ventana de un ABMEditorGridPanel
 */
Rad.ABMWindow = Ext.extend(Ext.Window, {
    autoHideOnSubmit: true,
    grid: null,

    onCloseWindow: function() {
        var selected = this.grid.getSelectionModel().getSelected();
        if (selected) {
            // && selected.store fix para cuando se cierra la ventana padre y se esta agregando
            if (this.grid.addingRow && !this.grid.deferSave && selected.store) {
                if (selected.get(selected.store.reader.meta.idProperty) == 0) {
                    /**
                     * Suspendo los eventos para q el writer no intente borrar el registro del lado del servidor
                     */
                    this.grid.store.suspendEvents();
                    this.grid.store.remove(selected);
                    this.grid.store.resumeEvents();
                    this.grid.getView().refresh();
                    this.grid.maskDetailGrids();
                } else {
                    // sino enviamos el publish de que se agrego una linea
                    this.grid.storeUpdatePublish('add',selected);
                    this.grid.reloadChildGrids(selected);
                }
            }

        }
        this.grid.addingRow = false;
        var el = this.grid.getGridEl();
        el.unmask();
        this.grid.disableButtons(false);
        this.grid.onAbmWindowHide();
    },

    closeAbm: function() {
        this.onCloseWindow();
        this.hide();
    },

    onEsc: function(k, e){
        if (this.activeGhost) {
            this.unghost();
        }
        e.stopEvent();
        this.closeAbm();
    },

    initComponent: function () {
        this.initialConfig.closeAction  = 'hide';
        this.closeAction  = 'hide';

        Rad.ABMWindow.superclass.initComponent.call(this, arguments);

        if (this.autoHideOnSubmit) {
            this.getForm().on('actioncomplete', function() {
                this.closeAbm();
            }, this);
        }

        this.on('render', function() {
            var closeb = this.getTool('close');

            closeb.on('click',function () {
                this.onCloseWindow();
                this[this.closeAction]();
            },this);
        }, this);

        this.on('show', function() {
            this.onAbmWindowShow();
        }, this.grid);
    },

    /**
    * Para el formulario ABM comun el unico hijo es el Rad Form
    * en caso de ser una ventana personalizada debera sobreescribirse este método
    */
    getForm: function () {
        if (this.items.items[0] instanceof Rad.Form) {
            return this.items.items[0];
        } else if (this.grid.abmFormId){ //Es se mantiene por compatibilidad (BORRAR AL TERMINAR DE MIGRAR)
            return this.findById( this.grid.abmFormId );
        } else {
            this.publish('/desktop/showError', 'La ventana ABMWindow no puede retornar el formulario<br>Posiblemente falte sobreescribir el método getForm');
            return false;
        }
    }
});

// ############### ABM Grid ############################################################
Rad.ABMEditorGridPanel = function(config) {
    // call parent constructor
    Rad.ABMEditorGridPanel.superclass.constructor.call(this, config);
}
Ext.extend(Rad.ABMEditorGridPanel, Rad.EditorGridPanel, {
    abmWindow: null,
    /**
     * El contenido de la ventana abm
     * solo se usa si abmWindow es null al momento de la creacion de la ventana.
     */
    abmForm :null,
    /**
     * Configuracion De la ventana
     * Esta configuracion sobreescribe la configuracion por defecto de la ventana del ABM (Solo si abmWindow no esta definido al momento de la creacion de la ventana)
     */
    abmWindowConfig: null,
    /**
     * Titulo de la ventana abm
     */
    abmWindowTitle: null,
    /**
     * Alto y ancho de la ventanta de abm
     */
    abmWindowWidth: 750,
    abmWindowHeight: 500,
    /**
     * En caso de ser una ventana compleja, o sea si el RadForm no es el primer item de la ventana.
     * Se debera especificar en esta variable el id del objeto de este
     */
    abmFormId: null,
    abmFormAutoHidde: true,
    /**
     * Esta funcion es llamada cada vez que se esconde la ventana de edicion
     */
    onAbmWindowHide: Ext.emptyFn,
    /**
     * Esta funcion es llamada cada vez que se muestra la ventana de edicion
     */
    onAbmWindowShow: Ext.emptyFn,

    onRowDblClick: function (grid, rowIndex, e) {
		// si tiene row editor no abre el editor con doble click pq ya activa el mismo
		if (this.withRowEditor == true) return;

        var rsm = grid.getSelectionModel();

		row = grid.store.getAt(rowIndex);
		// verifico por las dudas que este seleccionada
		if (rsm.isIdSelected(row.data.Id)) {
			this.abmWindowShow();
		}
    },

    onDestroy : function(){
        if(this.abmWindow) {
            this.abmWindow.destroy();
        }
        Rad.ABMEditorGridPanel.superclass.onDestroy.call(this);
    },

    getAbmWindowConfig: function () {
        var config = {
            layout      : 'fit',
            width       : this.abmWindowWidth,
            height      : this.abmWindowHeight,
            title       : this.abmWindowTitle,
            plain       : true,
            modal       : false,
            bodyStyle   : 'padding:0px;margin:0px;',
            border      : false,
            constrain   : true,
            items       : this.abmForm,
            id          : this.id+'_formwindow',
            grid        : this
        }
        Ext.apply(config, this.abmWindowConfig);
        return config;
    },

    createEditorWindow: function() {
        this.abmWindow = app.desktop.createWindow(this.getAbmWindowConfig(), Rad.ABMWindow);
    },

    abmWindowShow: function () {
        var selected = this.getSelectionModel().getSelected();
        if (selected != undefined) {
            if (this.abmWindow == null) {
               this.createEditorWindow();
            }

            this.disableButtons(true);
            var el = this.getGridEl();
            el.mask();
            this.abmWindow.show();
            var form = this.abmWindow.getForm();
            if (form) {
                form.loadRecord(selected);
            } else {
                Ext.MessageBox.alert('Atencion','No se encontro el formulario para cargar los datos');
            }
        } else {
            Ext.MessageBox.alert('Atencion','Seleccione un registro a editar');
        }
    },

    /**
     * Crea un registro haciendo una llamada por ajax al servidor
     */

    buildToolbar: function() {
        var id = this.getId();

        var cfg = [];
        if (this.topButtons.add) {
            cfg.push({
                text:     'Agregar',
                iconCls:  'add',
                handler:  this.createRow,
                ref: '../addButton',
                scope:    this,
                disabled: !this.loadAuto
            });
        }
        if (this.topButtons.edit) {
            cfg.push({
                text:     'Editar',
                iconCls:  'x-btn-text-icon',
                ref: '../editButton',
                icon:     'images/application_form_edit.png',
                handler:  this.abmWindowShow,
                scope:    this,
                disabled: !this.loadAuto
            });
        }
        if (this.topButtons.del) {
            cfg.push('-');
            cfg.push({
                text:    'Borrar',
                ref: '../delButton',
                iconCls: 'remove',
                handler: this.deleteRows,
                scope:   this,
                disabled:!this.loadAuto
            });
        }

        if (this.topButtons.extra) {
            Ext.each(this.topButtons.extra, function(btn) {
                cfg.push(btn);
            })
        }

        if (cfg.length == 0) return;
        this.tbar = cfg;
    },

    initComponent: function () {
        this.autoSave = true;
        Rad.ABMEditorGridPanel.superclass.initComponent.call(this);
        if (!this.loadAuto) {
            this.store.on('beforeload',  this.enableButtons, this);
        }
        this.on('rowdblclick', function (grid,index,e) {
	    this.onRowDblClick(grid,index,e);
	},this);
    },

    fireEditor: function () {
        this.abmWindowShow();
    },

    enableButtons: function () {
        this.disableButtons(false);
    },

    disableButtons: function (disabled) {
        var toolbar = this.getTopToolbar();
        if (!toolbar) return;
        toolbar.items.each(function(item) {
            if (item.setDisabled) {
                item.setDisabled(disabled);
            }
        }, this);
    }
});

//############### Form ABM Grid ############################################################
/**
 * Esta grilla esta hecha para ser un campo mas del formulario Rad.Form
 */
Rad.FormABMEditorGridPanel = function(config) {
    // call parent constructor
    Rad.FormABMEditorGridPanel.superclass.constructor.call(this, config);
}
Ext.extend(Rad.FormABMEditorGridPanel, Rad.ABMEditorGridPanel, {
    // #### Agregamos la capacidad de ser un campo de un formulario
    deferSave: true,        // hace q la ventana no borre el registro al cerrarce por no tener id
    newRecord: false,       // se esta creando un registro nuevo en el formulario padre?

    getValue: function() {
        var records = this.store.getModifiedRecords();
        var data = [];
        Ext.each(records, function (item) {
            data.push(item.data);
        }, this);
        return Ext.util.JSON.encode(data);
    },

    validate: function() {
        return true;
    },

    // #### FIN
    initComponent: function () {
        this.autoSave = false;
        Rad.FormABMEditorGridPanel.superclass.initComponent.call(this);
        this.un('afteredit',this.saveField,this);
    },

    createRow: function() {
        Rad.callRemoteJsonAction({
            url:     this.url+'/createrow/model/'+this.model,
            scope:   this,
            success: function(response) {
                if (this.addingRow) return;
                var u = new this.store.recordType(response.rows[0]);
                if (this.editor) this.editor.stopEditing();
                var el = this.getGridEl();
                el.unmask();
                var parentValue = this.getParentValue();
                if (parentValue === false) return;
                u.data[this.parentLocalField] = parentValue;
                this.store.insert(0, u);
                this.addingRow = true;
                this.getSelectionModel().selectRow(0);
                this.abmWindowShow();
            }
        });
    },

    /**
     * Crea el store de la grilla
     */
    buildStore: function() {
        this.store = new Ext.data.GroupingStore({
            autoLoad : this.loadAuto,
            autoSave : this.autoSave,
            pruneModifiedRecords: true,
            iniSection: this.iniSection,
            autoDestroy: true,
            reader: new Ext.data.JsonReader({
                idProperty: 'Id',
                messageProperty: 'msg'
            }),
            proxy : this.proxy,
            remoteSort :  false,
            //groupOnSort: true,
            remoteGroup: false,
            listeners: this.store.listeners
        }); // mask the grid if there is no data if so configured
    }
});

//############### Many To Many ############################################################
Rad.ManyToManyGridPanel = function(config) {
    // call parent constructor
    Rad.ManyToManyGridPanel.superclass.constructor.call(this, config);
}

manyToManyGrid = {
    enableDragDrop : false,
    saveUrl: null,
    relationRowId: 3,
    initComponent: function() {
        this.addEvents('saverelation');
        this.autoSave = false;
        // call parent
        Rad.ManyToManyGridPanel.superclass.initComponent.apply(this);

        var postUrl;
        if (this.module) postUrl = '/m/'+this.module;
        if (this.fetch)  postUrl = postUrl+'/fetch/'+this.fetch;

        this.proxy.api.read.url = this.url+'/listmtom/model/'+this.model+postUrl;

        this.store.on ('metachange', function(cols) {
            var cm = this.getColumnModel();
            cm.config[0].init(this);
            cm.config[0].bindEvents();
        },this);
    },

    buildToolbar: function() {
        this.tbar = [
        {
            'text': 'Asignar',
            icon: '/images/accept.png',
            ref: '../addButton',
            iconCls:'x-btn-text-icon',
            handler: this.saveRelation,
            scope: this
        }
        ];
    },
    loadAsDetailGrid: function (detailConfig, value)
    {
        if (!value) value        = 0;
        this.parentLocalField    = detailConfig.remotefield;
        this.parentRemoteField   = detailConfig.localfield;
        this.relationRowId       = value;
        this.store.baseParams.Id = this.relationRowId;
        this.store.reload();
    },
    saveRelation: function ()
    {
        var changes = this.store.getModifiedRecords(), i=0;
        var param = {};
        Ext.each(changes, function (e) {
            param['changes['+i+'][id]']    = e.data.Id;
            param['changes['+i+'][state]'] = e.data.checked;
            i++;
        });
        param.Id =  this.relationRowId;
        if (i == 0) {
            this.fireEvent('saverelation',{
                status: true
            });
            this.store.reload();
            return;
        }
        Ext.apply(param, this.baseParams);

        Rad.callRemoteJsonAction({
            url:     this.saveUrl,
            scope:   this,
            params:  param,
            success: function(response){
                this.store.reload();
                this.fireEvent('saverelation',{
                    status: true
                });
                this.publish( '/desktop/notify',{
                    title: 'Info',
                    iconCls: 'x-icon-information',
                    html: 'Cambios guardados'
                });
            }
        });
    }
};

Ext.extend(Rad.ManyToManyGridPanel, Rad.GridPanel, manyToManyGrid);

//############### Form ABM Field Many To Many ############################################################
Rad.ManyToManyEditorGridPanel = function(config) {
    // call parent constructor
    Rad.ManyToManyEditorGridPanel.superclass.constructor.call(this, config);
}

Ext.extend(Rad.ManyToManyEditorGridPanel, Rad.ABMEditorGridPanel, manyToManyGrid);
//Ext.extend(Rad.FormManyToManyEditorGridPanel,{});

Ext.reg('radgridpanel', Rad.GridPanel);
// Ext.reg('radlistpanel', Rad.list.ListView);
Ext.reg('radeditorgridpanel', Rad.EditorGridPanel);
Ext.reg('radfasteditorgridpanel', Rad.FastEditorGridPanel);
Ext.reg('radabmeditorgridpanel', Rad.ABMEditorGridPanel);
Ext.reg('radformabmeditorgridpanel', Rad.FormABMEditorGridPanel);
Ext.reg('radformmanytomanygridpanel', Rad.ManyToManyGridPanel);
Ext.reg('radformmanytomanyeditorgridpanel', Rad.ManyToManyEditorGridPanel);
