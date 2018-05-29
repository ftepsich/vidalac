Ext.namespace('ERP');
ERP.Paletizador = Ext.extend(Ext.Panel, {
    layout: 'border',
    Proveedor: null,
    setProveedor: function (p) {
        this.Proveedor = p;
    },
    loadArticulosRemito: function (remito) {
        detailGrid = {
            remotefield: 'Remito',
            localfield:  'Id'
        };

        this.rag.loadAsDetailGrid(detailGrid, remito);
    },	
    initComponent: function() {
        // ####  Reusable config options here
		
        //Dataview de depositos
        var depositoDV = new ERP.depositoDataView();
			
        var comboAlmacenRemPaletizar = new Ext.form.ComboBox({
            width: 150,
            displayField: 'Descripcion',
            forceSelection: true,
            forceReload: true,
            loadingText: 'Cargando...',
            lazyRender: true,
            triggerAction: 'all',
            editable: false,
            valueField: 'Id',
            autoLoad: true,
            fieldLabel: '',
            value : 1,
		
            store:
            new Ext.data.JsonStore ({
                url: 'datagateway/combolist/model/Almacenes/Predeposito/1',
                autoLoad: true,
                root: 'rows',
                idProperty: 'Id',
                totalProperty: 'count',
                fields: [ 'Id', 'Descripcion' ]
            }),
            listeners: {
                expand: function() {  //Fix para el ancho del combo q no lo acomoda solo en esta ventana anda a saber pq
                    this.list.setWidth(167);
                    this.innerList.setWidth(167);
                },
                select: function (c,e, i) {
                    depositoDV.store.baseParams = {
                        almacen: e.data.Id
                    };
                    depositoDV.store.reload();	
                    this.collapse();
                }
            }
        });
		
        var predepositosPanel = new Ext.Panel({
            region: 'center',
            title: 'Predeposito',
            frame: false,
            border: false,
            autoScroll: true,
            bodyStyle: 'background:#FFFFFF',
            tbar: new Ext.Toolbar ({
                items: [
                'Deposito',
                ]
            }),
			
            items: depositoDV
        });
		
        var remitoArticulosGrid = new Rad.GridPanel({
            filters:   		true,
            url:	   		"\/datagateway\/list\/model\/RemitosArticulos\/",
            modifyUrl: 		"\/datagateway\/savefield\/model\/RemitosArticulos\/",
            deleteUrl:		"\/datagateway\/delete\/model\/RemitosArticulos\/",
            createUrl:		"\/datagateway\/createrow\/model\/RemitosArticulos\/",
            model:	   		"RemitosArticulos",
            baseParams:		null,
            forceFit: 		true,
            stateful: 		false,
            iniSection:		"reducido",
            withPaginator:  false,
            title:			"Art\u00edculo",
            loadAuto:		false,
            ddGroup:		"articulosRemitosAmmi",
            sm:	new Ext.grid.RowSelectionModel({
                singleSelect:true,
                listeners: {
                    rowselect: function(i, idx, r) 
                    {
                        depositoDV.tpl.RemitoArticuloSeleccionado = r.data.Id;
                        depositoDV.refresh();
                    }
                }
            })
        });
		
        this.rag = remitoArticulosGrid;
		
        var drop = new Ext.dd.DropTarget(predepositosPanel.body.dom, {
            ddGroup : 'articulosRemitosAmmi',
			
            notifyDrop : function(dd, e, data) {
				
                if (! comboAlmacenRemPaletizar.getValue()) {
                    window.app.desktop.showMsg({
                        title: 'Atencion',
                        msg: 'Seleccione un Almacen primero...',
                        modal: true,
                        icon: Ext.Msg.WARNING,
                        buttons: Ext.Msg.OK
                    });
                    return;
                }
                var datos = data.selections[0].data;

                // Creo la ventana de generacion de palets
                if (datos.Cantidad == datos.CantidadPaletizada) {
                    window.app.desktop.showMsg({
                        title: 'Atencion',
                        msg: 'Este item ya esta completamente paletizado',
                        modal: true,
                        icon: Ext.Msg.WARNING,
                        buttons: Ext.Msg.OK
                    });
                    return;
                }
				
                if (!this.win) {
                    this.win = new app.desktop.createWindow({
                        layout:'form',
                        width: 590,
                        height: 175,
                        closeAction:'hide',
                        bodyStyle: 'padding: 5px;',
                        plain: true,
                        modal: true,
                        title: 'Crear Mmi',
                        autoDestroy: true,
                        items: [
                        {
                            layout: 'column',
                            xtype: 'radform',
                            listeners: {
                                actioncomplete: function () {
                                    depositoDV.store.reload();
                                    this.win.hide();
                                },
                                beforeaction : function (i, action) {
                                    i.baseParams.Proveedor = this.Proveedor;
                                    i.baseParams.Almacen   = comboAlmacenRemPaletizar.getValue();
                                }
                            },
                            url: '/window/remitos/generammi',
                            border: false,
                            defaults: {
                                layout: 'form', 
                                border: false
                            },
                            items: [
                            {
                                columnWidth: .50, 
                                items: [

                                {
                                    xtype:'numberfield', 
                                    fieldLabel:'Cant. a Paletizar', 
                                    name: 'cantidadAPaletizar', 
                                    width:'90%'
                                },

                                {
                                    xtype:'numberfield', 
                                    fieldLabel:'Cant. x Palet', 
                                    name: 'cantidadMaxima', 
                                    width:'90%'
                                },

                                {
                                    xtype:'xcombo',
                                    name: 'TipoPalet',
                                    width:'90%',
                                    displayField:'Descripcion',
                                    autocomplete:true,
                                    selectOnFocus:true,
                                    forceSelection:true,
                                    forceReload:true,
                                    hiddenName:'TipoPalet',
                                    loadingText:'Cargando...',
                                    lazyRender:false,
                                    store: new Ext.data.JsonStore ({
                                        id:0,
                                        url:'datagateway\/combolist\/model\/TiposDePalets',
                                        storeId:'MmiTipoStore',
                                        baseParams: {
                                            reconfigure: true
                                        }
                                    }),
                                    typeAhead:true,
                                    valueField:'Id',
                                    autoLoad:true,
                                    allowBlank:false,
                                    allowNegative:false,
                                    fieldLabel:'Tipo Palet',
                                    name:'Tipo_Palet'
                                }
                                ]
                            },{
                                columnWidth: .50, 
                                items: [

                                {
                                    xtype:'hiddenfield',  
                                    fieldLabel:'', 
                                    name: 'Almacen', 
                                    width:'90%'
                                },

                                {
                                    xtype:'hiddenfield',  
                                    fieldLabel:'', 
                                    name: 'Proveedor', 
                                    width:'90%'
                                },

                                {
                                    xtype:'textfield',  
                                    fieldLabel:'Lote', 
                                    name: 'Lote', 
                                    width:'90%'
                                },

                                {
                                    xtype:'xdatefield', 
                                    fieldLabel:'Elaboracion', 
                                    name: 'Elaboracion', 
                                    width:'90%',
                                    dateFormat: 'Y-m-d H:i:s'
                                },

                                {
                                    xtype:'xdatefield', 
                                    fieldLabel:'Vencimiento', 
                                    name: 'Vencimiento', 
                                    width:'90%',
                                    dateFormat: 'Y-m-d H:i:s'
                                }
                                ]
                            }
                            ]
                        }
			  
                        ]
                    });
                }
                this.win.show();
            }
        });
		
        Ext.apply(this, {
            listeners: {
                destroy: function() {
                    delete (this.win);
                }
            },
            items:[{
                border: false,
                region: 'west',
                width: 400,
                layout: 'fit',
                items: [
                remitoArticulosGrid
                ]
            }, 
            predepositosPanel
            ]
        });
        // And Call the superclass to preserve baseclass functionality
        Ext.ERP.Paletizador.superclass.initComponent.apply(this, arguments);
    }
});
