/**
 * Rad Framework
 * Copyright(c) 2010 SmartSoftware
 */
 
Ext.ns( 'Rad' );

/**
 * Rad.TreePanel
 */
Rad.TreePanel = Ext.extend(Ext.tree.TreePanel, {
    /**
     * Modelo
     */
    model: null,
    /**
     * Modulo
     */
    module: null,
    /**
     * Referencia del modelo que usa para armar el arbol
     */
    ref: null,
    /**
     * Campo que se usa para armar el arbol
     */
    parent: null,
    /*
     * Campo que se muestra como texto
     */
    display: null,
    /**
     * Template para los nodos
     */
    tpl: null,
    /**
     * Carga automaticamente un path
     */
    autoLoadPath: null,
    
    animate: false,
    autoScroll: true,
    useArrows: false,
    enableDD: false,
    containerScroll: true,
    border: false,
    rootVisible: false,
    
    /**
     * Inicializa todos los componentes requeridos
     */
    initComponent: function() {
        
        if (Ext.isString(this.tpl))
            this.tpl = new Ext.Template(this.tpl);
        else if (this.tpl == null)
            this.tpl = '{'+this.display+'}';
        
        this.buildToolbar();
        this.buildBottombar();
        this.buildLoader();
        
        /**
         * DESHABILITADO!!!
        this.on('movenode', function(tree, node, oldParent, newParent) {

            Rad.callRemoteJsonAction({
                url: '/default/treedatagateway/moverow/model/'+this.model+'/m/'+this.module,
                params: {
                    ref: this.ref,
                    rows: node.attributes.data.Id,
                    to: newParent.attributes.data.Id,
                },
                success: function(response) {
                    // TODO: algo!!!
                    //console.info( );
                },
                scope: this
            });
        }, this);
        */
        
        Rad.TreePanel.superclass.initComponent.call(this);
    },
    
    /**
     * Nodo raiz principal
     */
    root: {
        // nodeType: 'radasync',
        draggable: false,
        editable: false,
        //uiProvider: Rad.RootTreeNodeUI,
        id: 'root'
    },
    
    /**
     * Construye la barra de herramientas
     */
    buildToolbar: function() {
        this.tbar = [
        {
            text: 'Agregar...',
            iconCls: 'add',
            menu: new Ext.menu.Menu({
                items: [
                {
                    text: 'En este nivel',
                    scope: this,
                    iconCls: 'add_down',
                    handler: function() {
                        var selectedNode = this.getSelectionModel().getSelectedNode();
                        if (selectedNode) {
                            this.addRow(selectedNode.parentNode);
                        } else {
                            Ext.Msg.alert('Atencion', 'Debe seleccionar un registro');
                        }
                    }
                },
                {
                    text: 'Dentro',
                    scope: this,
                    iconCls: 'add_down_merge',
                    handler: function() {
                        var selectedNode = this.getSelectionModel().getSelectedNode();
                        if (selectedNode) {
                            selectedNode.expand(false, false, this.addRow, this);
                        } else {
                            Ext.Msg.alert('Atencion', 'Debe seleccionar un registro');
                        }
                    }
                }
                ]
            })
        },
        {
            text: 'Editar',
            scope: this,
            iconCls: 'edit',
            handler: this.abmWindowShow
        },
        '-',
        {
            text: 'Borrar',
            scope: this,
            iconCls: 'remove',
            handler: this.deleteRows
        }
        ];
    },

    buildBottombar: function() {
        this.bbar = [
            {
                text: 'Recargar',
                iconCls: 'x-tbar-loading',
                handler: function() {
                    this.getRootNode().reload();
                },
                scope: this
            },
            '-',
            {
                text: 'Expandir todo',
                icon: '../images/arrow_branch_down.png',
                handler: function() {
                    this.asyncExpandCollapse(true);
                },
                scope: this
            },
            {
                text: 'Contraer todo',
                icon: '../images/arrow_merge.png',
                handler: function() {
                    this.asyncExpandCollapse(false);
                },
                scope: this
            }
        ]
    },
    
    /**
     * Construye el loader ajax
     */
    buildLoader: function() {
        //this.loader = new Ext.tree.TreeLoader ({
        this.loader = new Rad.TreeLoader({
            dataUrl: '/default/treedatagateway/list/model/'+this.model+'/m/'+this.module,
            nodeParameter: 'filter',
            baseParams: {
                ref: this.ref
            }
        });
    },
    
    abmWindowShow: function () {
        var selectedNode = this.getSelectionModel().getSelectedNode();
        var selected = (selectedNode) ? new Ext.data.Record(selectedNode.attributes.data) : undefined;
		
        if (selected != undefined) {
            if (this.abmWindow == null) {
                this.abmWindow = app.desktop.createWindow(this.getAbmWindowConfig(), Rad.ABMWindowTree);
            }
            this.disable();
            
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
	
    getAbmWindowConfig: function () {
        var config = {
            layout      : 'fit',
            width       : this.abmWindowWidth,
            height      : this.abmWindowHeight,
            title       : this.abmWindowTitle,
            plain       : true,
            modal		: false,
            bodyStyle   : 'padding:0px;margin:0px;',
            border	 	: false,
            constrain	: true,
            items       : this.abmForm,
            id			: this.id+'_formwindow',
            grid        : this
        }
        Ext.apply(config, this.abmWindowConfig);
        return config;
    },
	
    dropConfig: {
        appendOnly: true
    },
    
    /**
     * Agregar un nuevo nodo
     */
    addRow: function (parent) {
        var newNode = new Rad.AsyncTreeNode({
            leaf: true,
            data: {}
        });
        // seteo el campo padre que lo relaciona, o null si esta en el maximo nivel
        newNode.attributes.data[this.parent] = (parent.isRoot) ? null : (parent.attributes.data.Id);
        //console.dir({parent:parent});
        //console.dir({newNode:newNode});
        parent.appendChild(newNode);
        newNode.select();
        this.abmWindowShow();
    },

    /**
     * Borra los nodos selecionados
     */
    deleteRows: function() {
        records = this.getSelectionModel().getSelectedNode();
        if (records) {
            Ext.Msg.confirm('Borrar','&iquest;Est&aacute; seguro que desea borrar el registro y sus hijos?',
                function (response) {
                    if (response == 'yes') {
                        Rad.callRemoteJsonAction({
                            url: '/default/treedatagateway/deleterow/model/'+this.model+'/m/'+this.module,
                            params: {
                                rows: records.attributes.data.Id,
                                ref: this.ref
                            },
                            success: function(response) {
                                if (response.success == true) {
                                    records.remove();
                                }
                            },
                            scope: this
                        });
                    }
                }, this
            );
        }
    },

    /**
     * Expande todos los nodos recursivamente
     */
    asyncExpandCollapse: function(expand) {
        this.getRootNode().cascade(
            function(node) {
                if (expand)
                    node.expand(true);
                else
                    node.collapse();
            }
        );
    }
});
Rad.TreePanel.nodeTypes = {};


/**
 * Rad.TreeNodeUI
 */
Rad.TreeNodeUI = Ext.extend(Ext.tree.TreeNodeUI, {
    renderElements: function() {
        //console.log('renderElementsss');
        //console.dir({n:n});
        n.text = this.ownerTree.tpl.apply(n.attributes.data);
        Rad.TreeNodeUI.superclass.renderElements.call(this, arguments);
    }
});

/**
 * Rad.AsyncTreeNode
 */
Rad.AsyncTreeNode = Ext.extend(Ext.tree.AsyncTreeNode, {
    uiProvider: Rad.TreeNodeuI,
    render: function () {
        if (this.attributes.data)
            this.text = this.ownerTree.tpl.apply(this.attributes.data);
        Rad.AsyncTreeNode.superclass.render.call(this, arguments);
    },

    setText: function(text) {
        //console.info('settext');
        //console.log(this);
        text = this.ownerTree.tpl.apply(this.attributes.data);
        //console.info(text);
        Rad.AsyncTreeNode.superclass.setText.call(this, text);
    }
});
Rad.TreePanel.nodeTypes.radasync = Rad.AsyncTreeNode;

/**
 * Rad.TreeLoader
 */
Rad.TreeLoader = Ext.extend(Ext.tree.TreeLoader,{
    createNode : function(attr){
        if(this.baseAttrs){
            Ext.applyIf(attr, this.baseAttrs);
        }
        if(this.applyLoader !== false && !attr.loader){
            attr.loader = this;
        }
        if(Ext.isString(attr.uiProvider)){
           attr.uiProvider = this.uiProviders[attr.uiProvider] || eval(attr.uiProvider);
        }
        if(attr.nodeType){
            return new Rad.TreePanel.nodeTypes[attr.nodeType](attr);
        }else{
            //console.info(attr.leaf ? 'Ext.tree.TreeNode' : 'Rad.AsyncTreeNode');
            return attr.leaf ?
                        //new Ext.tree.TreeNode(attr) :
                        new Rad.AsyncTreeNode(attr) :
                        new Rad.AsyncTreeNode(attr);
        }
    }
});

/**
 * Rad.FormTree
 *
 * Formulario para Rad.TreePanel
 */
Rad.FormTree = Ext.extend(Rad.Form, {
    onSuccess: function(form, action) {
        //var form = this.getForm();
        if (this.record != null) {
            
            // Si enviamos el formulario y obtenemos el id lo seteamos en el registro
            if (action) {
            //console.dir({'record':this});
            //form.loadRecord(new this.record.store.recordType(action.result.record));
            }
            //field.setValue(this.gridBind.store.getCount() + 1);
            
            if (action) {
                this.updateGridStoreRecord(action.result.record);
            //this.record.store.commitChanges();
            } else {
                this.updateGridStoreRecord();
                this.getForm().fireEvent('actioncomplete', this); 	// Si no se envia el formulario disparamos el evento actioncomplete
            }
        }
    },
    
    updateGridStoreRecord: function(values) {
        var selectedNode = this.ownerCt.grid.getSelectionModel().getSelectedNode();
        selectedNode.attributes.data = values;
        selectedNode.setText(values[this.ownerCt.grid.display]);
        selectedNode.setId(values.Id);
        
        //console.dir({sn:selectedNode});
        selectedNode.parentNode.expandable = true;
        selectedNode.parentNode.leaf = false;
        selectedNode.parentNode.ui.updateExpandIcon();
    }
});

/**
 * Rad.ABMWindowTree
 *
 * Ventana de ABM para Rad.TreePanel
 */
Rad.ABMWindowTree = Ext.extend(Rad.ABMWindow, {
    autoHideOnSubmit: true,
    grid: null,
    initComponent: function () {
        this.initialConfig.closeAction  = 'hide';
        this.closeAction = 'hide';

        Rad.ABMWindow.superclass.initComponent.call(this, arguments);
        
        if (this.autoHideOnSubmit) {
            this.getForm().on('actioncomplete', function() {
                this.hide();
            }, this);
        }
        this.on('hide', function() {
            this.grid.enable();
            var selected = this.grid.getSelectionModel().getSelectedNode();
            if (!selected.attributes.data.Id) {
                //console.dir({selected:selected});
                selected.remove();
            }
        /*
            var selected = this.grid.getSelectionModel().getSelected();
            if (selected) {
                if (this.grid.addingRow && !this.grid.deferSave) {
                    if (selected.get(selected.store.reader.meta.idProperty) == 0) {
                        // Suspendo los eventos para q el writer no intente borrar el registro del lado del servidor
                        this.grid.store.suspendEvents();
                        this.grid.store.remove(selected);
                        this.grid.store.resumeEvents();
                        this.grid.getView().refresh();
                    } else {
                        // sino enviamos el publish de que se agrego una linea
                        this.grid.storeUpdatePublish('add',selected);
                    }
                }
				this.grid.reloadChildGrids(selected);
			}
            this.grid.addingRow = false;
			var el = this.grid.getGridEl();
			el.unmask();
			this.grid.disableButtons(false);
			this.grid.onAbmWindowHide();
			*/
        }, this);
    /*
		this.on('show', function() {
           //this.onAbmWindowShow();
        }, this.tree);
        */
    },
    
    /**
	* Para el formulario ABM comun el unico hijo es el Rad Form
	* en caso de ser una ventana personalizada debera sobreescribirse este método
	*/
    getForm: function () {
        if (this.items.items[0] instanceof Rad.Form) {
            return this.items.items[0];
        } else if (this.tree.abmFormId){ //Es se mantiene por compatibilidad (BORRAR AL TERMINAR DE MIGRAR)
            return this.findById(this.tree.abmFormId);
        } else {
            this.publish('/desktop/showError', 'La ventana ABMWindowTree no puede retornar el formulario<br>Posiblemente falte sobreescribir el método getForm');
            return false;
        }
    }
});

Ext.reg('radasync', Rad.AsyncTreeNode);
Ext.reg('radtree', Rad.TreePanel);
Ext.reg('radformtree', Rad.FormTree);