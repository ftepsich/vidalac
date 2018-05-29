Ext.ns( 'Apps' );

Apps.<?=$this->name?> = Ext.extend(RadDesktop.Module, {
    title: '<?=$this->title?>',
    appChannel: '/desktop/modules<?=$this->url()?>',
    requires: [
      '/direct/Almacenes/RemitosSinRemitoSalida?javascript'
    ],

    eventfind: function (ev) {
        this.createWindow();
        var p = this.grid.buildFilter(0, 'Id', ev.value);
        this.grid.store.load({params:p});
    },

    eventsearch: function (ev) {
        this.createWindow();
        var p = this.grid.buildFilter(0, ev. field, ev.value);
        this.grid.store.load({params:p});
    },

    eventlaunch: function(ev) {
        this.createWindow();
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
        // Forulario principal
        this.form   = Ext.ComponentMgr.create(<?=$this->form?>);
        // despues del submit exitoso se pasa al paso 1 del wizard
        this.form.on(
            'actioncomplete',
            function() {
                this.wizard.setActiveItem(1);
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
                title  : 'Ordenes de Baja de Mercadería',
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
                    Models.Almacenes_Model_RemitosSinRemitoSalidaMapper.get(id, function (result, e) {
                        if (e.status) {
                            this.form.updateGridStoreRecord(result);
                        }
                    }, this);
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
                this.renderWizardItem('Completar datos de los artículos:','',this.gridRemitosArticulos,[{
                    text:   'Cerrar Orden',
                    scope: this,
                    handler: function() {
                        var Id = this.form.getForm().findField('Id').getValue();
                        Models.Almacenes_Model_RemitosSinRemitoSalidaMapper.cerrar(Id, Ext.emtpyFn, this);
                        this.grid.abmWindow.closeAbm();

                    }
                }])
            ]
        });
        // Logica del Wizard

        this.wizard.on(
            'activate',
            function (i) {
                switch(i) {
                    case 1: // cargamos la grilla de articulos para el paso 2
                        detailGrid = {remotefield: 'Comprobante', localfield: 'Id'};
                        form = this.form.getForm();
                        id   = form.findField('Id').getValue();

                        this.gridRemitosArticulos.parentForm = this.form;   // le seteamos el formulario padre para que saque los valores automaticamente
                        this.gridRemitosArticulos.loadAsDetailGrid(detailGrid, id);
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
        this.gridRemitosArticulos = Ext.ComponentMgr.create(<?=$this->gridRemitosArticulos?>);
        this.gridRemitosArticulos.buttons = [{
            text:   'Cerrar Orden',
            scope: this,
            handler: function() {
                var Id = this.form.getForm().findField('Id').getValue();
                Models.Almacenes_Model_RemitosSinRemitoSalidaMapper.cerrar(Id, Ext.emtpyFn, this);
                this.grid.abmWindow.closeAbm();

            }
        }];
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
    renderWizardItem: function (titulo, subtitulo, contenido, buttons) {
        var v = {
            layout: 'fit',
            border : false,
            frame : false,
            items:  {
                layout : 'border',
                border : false,
                items : [
                    {
                        region : 'north',
                        layout: 'fit',
                        border: false,
                        height : 50,
                        items : {
                            layout: 'fit',
                            border: false,
                            html: '<Font style=\'COLOR:#336699;FONT-FAMILY:Arial;FONT-SIZE:16pt;text-align:center;PADDING-TOP:50px;\'>'+titulo+'</font><br><Font style=\'COLOR:#336699;FONT-FAMILY:Arial;FONT-SIZE:10pt;text-align:center;padding-left:5px;\'>'+subtitulo+'</font>'
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
        };

        if (buttons) v.items.items[1].items[0].buttons = buttons;


        return v;

    }
});

new Apps.<?=$this->name?>();
