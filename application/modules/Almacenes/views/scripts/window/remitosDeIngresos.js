Ext.ns( 'Apps' );

Apps.<?=$this->name?> = Ext.extend(RadDesktop.Module, {
	title: '<?=$this->title?>',
	appChannel: '/desktop/modules<?=$this->url()?>',
	requires: [
      '/direct/Almacenes/RemitosDeEntradas?javascript'
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

	eventrecibido: function (ev) {
		this.createWindow();
		this.grid.store.load({params:{factura: ev.value}});
	},

	eventsearch: function (ev) {
		this.createWindow();
		var p = this.grid.buildFilter(0, ev.field, ev.value);
		this.grid.store.load({params:p});
	},

	eventlaunch: function(ev) {
		this.createWindow();
		this.grid.store.load();
	},

    createWindow: function() {
        var win = app.desktop.getWindow(this.id+'-win');
        if ( !win ) {
			this.createGrid();
			this.createEditorWindow();
            win = this.create();
		}
        win.show();
    },
	
	/**
	 * Crea la ventana del Abm
	 */
	createEditorWindow: function () {
		// Formulario principal
		this.form   = Ext.ComponentMgr.create(<?=$this->form?>);
		// despues del submit exitoso se pasa al paso 1 del wizard
		this.form.on(
			'actioncomplete', 
			function() {
				this.wizard.setActiveItem(1);
                if (this.form.record.data.Cerrado == 1)
                    this.grid.abmWindow.closeAbm();
			},
			this
		);
		
		this.createWizard();
		this.grid.abmWindow = app.desktop.createWindow({
				autoHideOnSubmit: false,
				width  : 950,
				height : 450,
				border : false,
				layout : 'fit',
				ishidden : true,
				title  : 'Remitos de Ingresos', 
				plain  : true,
				items  : this.wizard,
				form   : this.form,
				grid   : this.grid,
				getForm: function() {
					return this.form;
				}
			},		
			Rad.ABMWindow
		);
		// si la ventana se esconde volvemos al primer paso
		this.grid.abmWindow.on('hide', function(){
				this.wizard.setActiveItem(0);
				id = this.form.getForm().findField('Id').getValue();
				if (id != 0) {
				// Actualizo la grilla
				Rad.callRemoteJsonAction ({
					url: '/datagateway/get/model/RemitosDeEntradas/m/Almacenes/',
					params: {id: id},
					scope: this,
					success: function(response) {
						this.form.updateGridStoreRecord(response.data);
					}
				});
				}
			}, this
		);
	},
	
	/**
	 * Creo el wizard del abm
	 */
	createWizard: function() {
		this.createSecondaryGrids();
		
		// Creamos el Obj wizard
		this.wizard = new Rad.Wizard({
			border: false,
			defaults: {border:false},
			items: [
				this.renderWizardItem('Ingresar los datos del Remito:','',this.form),
				this.renderWizardItem('Seleccionar las Ordenes de Compra que se solicitaron:','',this.renderPaso2()),
				this.renderWizardItem('Completar datos de los artículos:','',this.gridRemitosArticulos),
				this.renderWizardItem('Finalizar El Remito:','',this.renderPaso3())
			]
		});
		// Logica del Wizard
		
		this.wizard.on(
			'activate', 
			function (i) {
				switch(i) {
					case 1:	// Si activo el paso 1 cargo la grilla Ordenes de compra
                        form = this.form.getForm();
                        id   = form.findField('Id').getValue();
                        proveedor  = form.findField('Persona').getValue();
                        detailGrid = {remotefield: 'ComprobantePadre', localfield: 'Id'};
                        this.gridOrdenesDeCompra.setPermanentFilter(0,'Persona',proveedor);
//                        this.gridRemito.setPermanentFilter(1,'Comprobante',id);
                        this.gridOrdenesDeCompra.loadAsDetailGrid(detailGrid, id);					
					break;
					case 2: // cargamos la grilla de articulos para el paso 2
						detailGrid = {remotefield: 'Comprobante', localfield: 'Id'};
						form = this.form.getForm();
						id   = form.findField('Id').getValue();
						
						this.gridRemitosArticulos.parentForm = this.form;	// le seteamos el formulario padre para que saque los valores automaticamente
						this.gridRemitosArticulos.loadAsDetailGrid(detailGrid, id);
					break;
					case 3: 
						Ext.getCmp('impresionRemitosDeEntradasHtml').setSrc('/Window/birtreporter/report?template=Comp_RemitoRecibido_Ver&output=html&id='+ this.form.getForm().findField('Id').getValue());
					break;
				}
			}, 
			this
			
		);
		
		this.wizard.on('next', function (i) {
				switch(i) {
					case 0:	
						this.form.submit();
						return false;
					break;
					case 1:
						this.gridOrdenesDeCompra.saveRelation();
					break;
				}
			}, this
		);
		this.wizard.on('finish', function(i) {
			this.grid.abmWindow.closeAbm();
		},this);
	},
	
	/**
	 * Creamos las grillas secundarias y su logica
	 */
	createSecondaryGrids: function () {
		// Remitos
		this.gridOrdenesDeCompra = Ext.ComponentMgr.create(<?=$this->gridOrdenesDeCompra?>);
		this.gridOrdenesDeCompraArticulos = Ext.ComponentMgr.create(<?=$this->gridOrdenesDeCompraArticulos?>);
		
		this.gridRemitosArticulos = Ext.ComponentMgr.create(<?=$this->gridRemitosArticulos?>);
	},
	
	/**
	 * Template para paso 2 (Ordenes de compras y sus articulos)
	 */
	renderPaso2: function () {
		return {
			xtype  : 'panel',
			layout : 'border',
			items  : [{
				region : 'north',
				layout : 'fit',
				height  : 200,
				split  : false,
				items  : [ this.gridOrdenesDeCompra ]
			},
			{
				region : 'center',
//				title : 'Articulos',
				layout: 'fit',
				items : [ this.gridOrdenesDeCompraArticulos ]
			}]
		}
	},
	
	
	/**
	 * Creamos la grilla principal
	 */
	createGrid: function () {
		this.grid = Ext.ComponentMgr.create(<?=$this->grid?>);		
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
    },
	
	/**
	 * Da formato a los items del wizard
	 */ 
	renderWizardItem: function (titulo, subtitulo, contenido) {
		return {
			layout: 'fit',
			border : false,
			frame : false,
			items:  {
				layout : 'border',
				border : false,
				items : [{
						region : 'north',
						layout: 'fit',
						border: false,
						height : 50,
						items : {
							layout: 'fit',
							border: false,
							html: '<img style=\'float:right;\' src=\'/images/268498431.png\'><Font style=\'COLOR:#336699;FONT-FAMILY:Arial;FONT-SIZE:16pt;text-align:center;PADDING-TOP:50px;\'>'+titulo+'</font><br><Font style=\'COLOR:#336699;FONT-FAMILY:Arial;FONT-SIZE:10pt;text-align:center;padding-left:5px;\'>'+subtitulo+'</font>'
						}
					},{
						region : 'center',
						layout: 'fit',
						border: false,
						items : [{
							layout: 'fit',
							items: contenido
						}]
				}]
			}
		}
	},
	
    renderPaso3: function () {
        return {
            layout: 'fit',
            border: false,
            items:  [{
                xtype: 'iframepanel',
                id: 'impresionRemitosDeEntradasHtml',
                bodyStyle: 'background-color:white;'
            }],
            buttons : [{
                text: 	'Cerrar Remito',
                scope: this,
                handler: function() {
                    var Id = this.form.getForm().findField('Id').getValue();
                    Models.Almacenes_Model_RemitosDeEntradasMapper.cerrar(Id, Ext.emtpyFn, this); 
					this.grid.abmWindow.closeAbm(); 
                }
            }]
        };
    }	
});

new Apps.<?=$this->name?>();
