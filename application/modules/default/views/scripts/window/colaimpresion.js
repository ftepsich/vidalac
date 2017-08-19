Ext.ns( 'Apps' );

Apps.<?=$this->name?> = Ext.extend(RadDesktop.Module, {
    autoStart: true,
    title: '<?=$this->title?>',

    // Si el controlador esta en el modulo default no usar $this->url()
    appChannel: '/desktop/modules<?=$_SERVER['REQUEST_URI']?>',
    
    
    eventlaunch: function(ev) {
        this.createWindow();
    },

    createWindow: function() {

        var win = app.desktop.getWindow(this.id+'-win');
        if ( !win ) {
            win = this.create();
            win.on('close', this.pararReload, this);
        }
        win.show();
    },

    cancelarTrabajo: function() {
        var selected = this.grid.getSelectionModel().getSelected();
        if (!selected) {
            this.publish('/desktop/showMsg/',{
                title:'Atencion',
                msg: 'Seleccione primero un trabajo',
                icon: Ext.MessageBox.WARNING
            });
            return;
        }

        Rad.callRemoteJsonAction({url: '/default/colaimpresion/canceljob',
            scope: this,
            params: {
                trabajo: selected.data.uri
            },
            success: function() {
                this.publish( '/desktop/notify',{
                    title: 'Adm. Impresion',
                    iconCls: 'x-icon-information',
                    html: 'Trabajo cancelado'
                });
                this.grid.store.reload();
            }
        });
    },
    
    create: function()
    {
        defaultWinCfg = {
            id: this.id+'-win',
            title: this.title,
            icon: 'images/printer.png',
            border:  false,
            shim: false,
            animCollapse: false,
            layout: 'fit',
            width: 700,
            height:500,
            items: [
                this.renderWindowContent()
            ]
        };
        return app.desktop.createWindow(defaultWinCfg);
    },

    pararReload: function() {
        if (this.reloadTask)  {
            Ext.TaskMgr.stop(this.reloadTask);            
        }
    },

    impresoraSeleccionada: function(r) {
        this.gstore.baseParams.impresora = r.data.id;
        this.gstore.load();

        this.pararReload();

        this.reloadTask = {
            run: this.gstore.reload,
            scope: this.gstore,
            interval: 4000
        };

        Ext.TaskMgr.start(this.reloadTask);  
    },

    
    renderWindowContent: function ()
    {
        this.impresoras = <?=$this->impresoras?>;
		
        var store = new Ext.data.ArrayStore({
            fields: ['id', 'descripcion'],
            data : this.impresoras
        });
		
		// combo de impresoras
        var combo = new Ext.form.ComboBox({
            store: store,
            displayField: 'descripcion',
            mode: 'local',
            triggerAction: 'all',
            emptyText:'seleccione impresora',
            selectOnFocus: true,
            width: 135,
            getListParent: function() {
                return this.el.up('.x-menu');
            },
            iconCls: 'no-icon'
        });
		
		

		// Toolbar
        var tbo = new Ext.Toolbar();
        tbo.add({
            text:'Cancelar',
            icon: 'images/palets/mmi32agregar.png',
            cls:  'x-btn-text-icon',
            scale: 'large',
            iconAlign:'top',
            handler: this.cancelarTrabajo,
            scope: this,
        });
        tbo.add({xtype: 'tbfill'});
        tbo.add('Impresora: ');
        tbo.addField(combo);

		// store de la grilla
        this.gstore = new Ext.data.JsonStore({
            autoDestroy: true,
            url: '/default/colaimpresion/getjobs/',
			root: 'rows',
			totalProperty: 'count',
            fields: [
               {name: 'descripcion'},
               {name: 'id', type: 'int'},
               {name: 'uri'},
               {name: 'fecha', type: 'date', format: 'd/m/Y'},
               {name: 'estado'}
            ]
        });
		
		// cuando se seleccione un impresora cargamos las tareas q tiene
		combo.on('select', function (s, r, index) {
			this.impresoraSeleccionada(r);
		},this);
	
		// grilla de tareas pendientes de la impresora seleccionada
        this.grid = new Ext.grid.GridPanel({
            store: this.gstore,
            viewConfig: { forceFit: true },
            layout: 'fit',
            tbar: tbo,
            columns: [
                {
                    id       : 'trabajo',
                    header   : 'Trabajo', 
                    width    : 260, 
                    sortable : false, 
                    dataIndex: 'descripcion'
                },
                {
                    header   : 'Fecha', 
                    width    : 130,
                    xtype: 'datecolumn',
                    sortable : false, 
                    dataIndex: 'fecha',
                    format: 'd/m/Y H:i:s'
                },
                {
                    header   : 'Estado', 
                    width    : 150, 
                    sortable : false, 
                    dataIndex: 'estado'
                },
                {
                    id       : 'id',
                    header   : 'id', 
                    width    : 75, 
                    sortable : false, 
                    dataIndex: 'id'
                }]
        });

        return this.grid;
    }
    
});

new Apps.<?=$this->name?>();