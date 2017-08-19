Ext.ns( 'Apps' );

Apps.<?=$this->name?> = Ext.extend(RadDesktop.Module, {

    title: '<?=$this->title?>',
    appChannel: '/desktop/modules<?=$this->url()?>',
    requires: [
        '/direct/Produccion/Producciones?javascript',
        '/js/ux/grid/ProgressColumn.js',
        '/js/ux/grid/css/ProgressColumn.css',
        '/js/ux/Ext.ux.chartsFilter.js',
        '/js/erp/requerimientosMaterialesPanel.js'
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

    eventlaunch: function(ev) {
        this.createWindow();
    },

    createWindow: function() {
        var win = app.desktop.getWindow(this.id+'-win');
        if ( !win ) {
            this.createGrid();
            this.createEditorWindow();
            win = this.create();
            // Si destruimos la ventana padre tenemos q destruir la hija tambien
            win.on('destroy',function(){
                this.abmWindow.destroy();
            },this);
        }
        win.show();
    },




    /**
     * Crea la ventana del Abm
     */
    createEditorWindow: function () {

        this.initEstadisticaPorTurno();

        // creamos el wizard
        this.wizard = new Rad.Wizard({
            border: false,
            defaults: {border:false},
            items: [
                this.renderWizardItem('Puestos de trabajo:','',this.renderPaso1()),
                this.renderWizardItem('Producir','',this.renderPaso2()),
            ]
        });

       this.wizard.on(
            'activate',
            function (i) {
                switch(i) {
                    case 0:
                        this.empleadosView.store.load();
                        var rt1 = {};
                        rt1['pfilter[0][field]']       = 'ActividadConfiguracion';
                        rt1['pfilter[0][data][value]'] = this.OrdenDeProduccion.data.ActividadConfiguracion;
                        rt1['pfilter[0][data][type]']  = 'numeric';
                        rt1['pfilter[0][data][comparison]'] = 'eq';
                        this.actividadesGrid.store.baseparams = rt1;

                        this.actividadesGrid.store.load();
                        break;
                    case 1:

                        var rt = {};
                        rt['pfilter[0][field]'] 	   = 'OrdenesDeProduccionesDetallesOrdenDeProduccion';
                        rt['pfilter[0][data][value]']      = this.OrdenDeProduccion.data.Id;
                        rt['pfilter[0][data][type]']       = 'numeric';
                        rt['pfilter[0][data][comparison]'] = 'eq';
                        this.ordenesProduccionMmiGrid.store.baseParams = rt;
                        this.ordenesProduccionMmiGrid.store.load();

                        var rt1 = {};
                        rt1['pfilter[0][field]']        = 'ProduccionesOrdenDeProduccion';
                        rt1['pfilter[0][data][value]']      = this.OrdenDeProduccion.data.Id;
                        rt1['pfilter[0][data][type]']       = 'numeric';
                        rt1['pfilter[0][data][comparison]'] = 'eq';
                        this.gridLog.store.baseParams = rt1;
                        this.gridLog.store.load();

                        this.produccionesMmis.store.baseParams = rt1;
                        this.produccionesMmis.store.load();
                        this.actualizarGrafico();
                        this.panelRequerimientos.loadRequerimientos(this.OrdenDeProduccion.data.Id);
                       break;
                }
            },
            this

        );


        this.wizard.on(
            'next',
            function (i) {
                switch(i) {
                    case 0:
                        Models.Produccion_Model_ProduccionesMapper.iniciarProducccion(this.Produccion, function(result, e) {
                            if (e.status) {
                                this.wizard.setActiveItem(1);
                            }
                        }, this);
                        return false;
                        break;

                    case 1:

                       break;
                }
            },
            this

        );



        this.abmWindow = app.desktop.createWindow({
            autoHideOnSubmit: false,
            width  : 1000,
            height : 520,
            animateTarget : null,
            maximized: true,
            closeAction : 'hide',
            modal: true,
            border : false,
            layout : 'fit',
            ishidden : true,
            title  : 'Produccion',
            plain  : true,
            items  : this.wizard
       });

    },
    /**
     * renderiza un item del wizard
     */
    renderWizardItem: function (titulo, subtitulo, contenido) {
        return {
            layout: 'fit',
            border: false,
            frame : false,
            items:  {
                layout : 'border',
                border : false,
                items : [{
                        region : 'north',
                        layout: 'fit',
                        border: false,
                        height : 25,
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
        }
    },

    /**
     * Creamos la grilla principal
     */
    createGrid: function () {
        this.grid = Ext.ComponentMgr.create(<?=$this->grid?>);
        this.grid.getTopToolbar().addButton([
            {
                text:	'Ver',
                icon:	'images/printer.png',
                cls	:	'x-btn-text-icon',
                scope:  this.grid,
                handler:	function () {
                    selected = this.getSelectionModel().getSelected();
                    if (selected) {
                        this.publish('/desktop/modules/Window/birtreporter', {
                            action: 'launch',
                            template: 'OrdenDeProduccion',
                            id: selected.id,
                            output: 'html',
                            width:  900,
                            height: 700
                        });
                    } else {
                        Ext.Msg.alert('Atencion', 'Seleccione un registro para ver el reporte');
                    }
                }
            },
            { xtype:'tbseparator' },
            {
                text:	'Producir',
                icon:	'images/bullet_wrench.png',
                cls	:'x-btn-text-icon',
                scope:  this,
                handler:	function () {
                    selected = this.grid.getSelectionModel().getSelected();
                    if (selected) {
                        this.startProduction(selected);
                    }
                }
            }

        ]);

        this.grid.onBeforeCreateColumns = function(columns)
        {
            columns.push(
                new Ext.ux.ProgressColumn({
                    header: '% Terminado',
                    width: 105,
                    dataIndex: 'MmisTerminado',
                    divisor: 'Cantidad',
                    align: 'center',
                    getBarClass: function(fraction) {
                        return (fraction > 0.99) ? 'high' : (fraction > 0.50) ? 'medium' : 'low';
                    },
                    renderer: function(value, meta, record, rowIndex, colIndex, store, pct) {
                        return Ext.util.Format.number(pct, "0.00%");
                    }
                })
            );
        }
    },

    startProduction: function(record) {
        this.OrdenDeProduccion = record;
        this.gridProduccionPorTurno.store.baseParams.id = this.OrdenDeProduccion.data.Id;
        // Iniciamos la linea de tiempo
        Models.Produccion_Model_ProduccionesMapper.iniciarLineaDeTiempo(record.data.Id, function(result, e) {
            if (e.status) {
                this.Produccion = result;
                this.abmWindow.show();
                this.wizard.setActiveItem(0);

				// Mostramos
				var detailEl = Ext.getCmp('produccion-OrdenDeProduccionPanel').body
				this.ordenProduccionTemplate.overwrite(detailEl, record.data);
            }
        }, this);


    },

    initializeEmpleadosDragZone: function (v) {
        v.dragZone = new Ext.dd.DragZone(v.getEl(), {

    //      On receipt of a mousedown event, see if it is within a draggable element.
    //      Return a drag data object if so. The data object can contain arbitrary application
    //      data, but it should also contain a DOM element in the ddel property to provide
    //      a proxy to drag.
            getDragData: function(e) {
                var sourceEl = e.getTarget(v.itemSelector, 10);
                if (sourceEl) {
                    d = sourceEl.cloneNode(true);
                    d.id = Ext.id();
                    return v.dragData = {
                        sourceEl: sourceEl,
                        repairXY: Ext.fly(sourceEl).getXY(),
                        ddel: d,
                        empleadoData: v.getRecord(sourceEl).data
                    }
                }
            },

    //      Provide coordinates for the proxy to slide back to on failed drag.
    //      This is the original XY coordinates of the draggable element.
            getRepairXY: function() {
                return this.dragData.repairXY;
            }
        });
    },

    quitarPersonasActividad: function(){
        selected = this.actividadesGrid.getSelectionModel().getSelected();
        if (selected) {
            Models.Produccion_Model_ProduccionesMapper.desasociarEmpleados(this.Produccion, selected.data.Id, function(result, e) {
                if (e.status) {
                    this.actividadesGrid.store.load();
                }
            }, this);
        } else {
            Ext.Msg.alert('Atencion', 'Seleccione un registro para ver el reporte');
        }
    },

    renderPaso1: function () {

        var empleadosStore = new Ext.data.JsonStore({
            // store configs
            autoLoad: false,
            autoDestroy: true,
            url: '/default/datagateway/list/model/Empleados/m/Base/fetch/EsDeProduccion'
        });

        this.puestosDeTrabajo = new Ext.data.JsonStore({
            // store configs
            autoLoad: false,
            autoDestroy: true,
            url: '/default/datagateway/list/model/LineasDeProduccionesPersonas/m/Produccion/order/Actividad'
        });

        this.puestosDeTrabajo.on('load', function() {
            var actividad, actividadRow, rowIndex;

            this.puestosDeTrabajo.data.each(function(item, idx, number){
                if (actividad != item.data.Actividad) {
                    actividad = item.data.Actividad;
                    actividadRow = this.actividadesGrid.store.getById(actividad);
                    rowIndex = this.actividadesGrid.store.indexOf(actividadRow);
                }
                htmlRow = this.actividadesGrid.getView().getRow(rowIndex);
                body = Ext.get(htmlRow).child('.actividades-target');
                body.update(item.data.Persona_cdisplay+', Dni: '+item.data.PersonasDni+'<br>'+body.dom.innerHTML);

            }, this);
        },this);

        this.empleadosView = new Ext.DataView({
            layout:'fit',
            cls: 'empleados-view',
            tpl: '<tpl for=".">' +
                    '<div class="empleados-source"><table width="100%" style="min-width:280px;"><tbody>' +
                        '<tr><td colspan=4 class="empleados-name">{RazonSocial}</td></tr>' +
                        '<tr><td class="empleados-label">Documento</td><td class="empleados-dni">{Dni}</td><td class="empleados-label">Apodo</td><td class="empleados-dni">{Denominacion}</td></tr>' +
                        '<tr><td class="empleados-label">Sexo</td><td class="empleados-sexo">{Sexo_cdisplay}</td><td class="empleados-label">Nacido</td><td class="empleados-sexo">{[Ext.util.Format.date(values.FechaNacimiento,"d/m/Y")]}</td></tr>' +
                    '</tbody></table></div>' +
                 '</tpl>',
            itemSelector: 'div.empleados-source',
            overClass: 'empleados-over',
            selectedClass: 'empleados-selected',
            singleSelect: true,
            store: empleadosStore,
            listeners: {
                render: this.initializeEmpleadosDragZone
            }
        });

        this.actividadesGrid = Ext.ComponentMgr.create(<?=$this->actividadesGrid?>);
        this.actividadesGrid.getTopToolbar().addButton([
            {
                text: 'Quitar',
                icon: 'images/delete.png',
                cls:  'x-btn-text-icon',
                scope: this,
                handler: this.quitarPersonasActividad

            }, {
                text: 'Actualizar',
                icon: 'images/arrow_refresh.png',
                cls:  'x-btn-text-icon',
                scope: this,
                handler: function (){
                    this.actividadesGrid.store.load();
                }

            }, {
                text: 'Clonar Turno Anterior',
                icon: 'images/user_go.png',
                cls:  'x-btn-text-icon',
                scope: this,
                handler: function (){

                    Models.Produccion_Model_ProduccionesMapper.clonarEmpleados(this.Produccion,function(result, e) {
                        if (e.status) {
                            this.actividadesGrid.store.load();
                        }
                    }, this);

                }

            }
        ]);


        this.actividadesGrid.on('render', this.initializeActividadesDropZone,this);

        this.actividadesGrid.store.on('load', function() {
            var rt = {};
            rt['pfilter[0][field]'] 	  = 'Produccion';
            rt['pfilter[0][data][value]'] = this.Produccion;
            rt['pfilter[0][data][type]']  = 'numeric';
            rt['pfilter[0][data][comparison]'] = 'eq';

            this.puestosDeTrabajo.baseParams = rt;
            this.puestosDeTrabajo.load();
        }, this);



        return {
            layout: 'border',
            items: [{
                title: 'Operarios',
                region: 'west',
                width: 300,
                margins: '0 5 0 0',
                border: true,
                items: this.empleadosView
            }, this.actividadesGrid ]
        };
    },

    showDetails : function(selected, template){
        var detailEl = Ext.getCmp('produccionPanelDetalleDeMmi').body;
        if (selected){
            detailEl.hide();
            template.overwrite(detailEl, selected.data);
            detailEl.slideIn('l', {stopFx:true,duration:.2});
        }else{
            detailEl.update('');
        }
    },



    UsarORetornarMateriaPrima: function() {
        var sModel  = this.ordenesProduccionMmiGrid.getSelectionModel();

        if (sModel.getCount() > 1) {
            Ext.Msg.alert('Atención', 'Seleccione solo un Mmi para quitar o agregar mercadería');
            return;
        }

        selected = sModel.getSelected();
        if (selected == undefined) {
            Ext.Msg.alert('Atención', 'Seleccione un Mmi para quitar o agregar mercadería');
            return;
        }
        if (!this.UsarMercaderia) this.createUsarMercaderiaWindow();
        else {
            this.UsarMercaderia.items.get(0).getForm().reset();
        }
        var rt = {};
        // Filtrar el combo por el tipo
        rt['pfilter[0][field]']       = 'TipoDeUnidad';
        rt['pfilter[0][data][value]'] = selected.data.TipoUnidadDeMedidaProducto;
        rt['pfilter[0][data][type]']  = 'numeric';
        rt['pfilter[0][data][comparison]'] = 'eq';
        this.comboStore.baseParams = rt;

        if (this.usarRetornarMercaderiaMode == 'utilizar') {
            this.UsarMercaderia.setTitle('Utiliar Materia Prima');
        } else {
            this.UsarMercaderia.setTitle('Retornar Materia Prima');
        }

        this.UsarMercaderia.show();
    },

    GenerarMmis: function() {
        if (!this.GenerarMmi) {
            this.createGenerarMmiWindow();
        } else {
            this.GenerarMmi.items.get(0).getForm().reset();
        }
        this.GenerarMmi.show();
    },


    onUsarORetornarMercaderia: function() {
        selected = this.ordenesProduccionMmiGrid.getSelectionModel().getSelected();
        if (selected == undefined) {
            Ext.Msg.alert('Atencion', 'Seleccione un Mmi para quitarle mercaderia');
            return;
        }
        var form = this.UsarMercaderia.items.get(0).getForm();

        var unidad = form.findField('UnidadDeMedida').getValue();
        var cantidad = form.findField('Cantidad').getValue();

        if (unidad == undefined || unidad == '') unidad = 0;

        if (this.usarRetornarMercaderiaMode == 'utilizar') {

            Models.Produccion_Model_ProduccionesMapper.quitarMercaderiaAMmi(selected.data.Mmi, cantidad, unidad, this.Produccion,function(result, e) {
                if (e.status) {
                    this.ordenesProduccionMmiGrid.store.load();
                    this.UsarMercaderia.hide();
                    this.gridLog.store.load();
                }
            }, this);
        } else {
               Models.Produccion_Model_ProduccionesMapper.retornarMercaderiaAMmi(selected.data.Mmi, cantidad, unidad, this.Produccion,function(result, e) {
                if (e.status) {
                    this.ordenesProduccionMmiGrid.store.load();
                    this.UsarMercaderia.hide();
                    this.gridLog.store.load();
                }
            }, this);
        }
    },

    onGenerarMmi: function() {
        var form = this.GenerarMmi.items.get(0).getForm();

        var cantidadxPalet = form.findField('CantidadxPalet').getValue();
        var cantidad = form.findField('Cantidad').getValue();
        var tipoDePalet = form.findField('TipoDePalet').getValue();

        // Mensaje de Espera
        var wait = app.desktop.showMsg({
            progressText: 'Espere por favor...',
            msg: 'Generando MMIs',
            modal: true,
            closable: false,
            width: 300,
            wait: true,
            waitConfig: {interval:200}
        });

        Models.Produccion_Model_ProduccionesMapper.generarMmi(this.Produccion, cantidad, cantidadxPalet,tipoDePalet,function(result, e) {

            if (e.status) {
                wait.hide();
                this.produccionesMmis.store.load();
                this.GenerarMmi.hide();
                this.gridLog.store.load();
                this.actualizarGrafico();
            }
        }, this);
    },

    createGenerarMmiWindow: function()
    {
       this.GenerarMmi = app.desktop.createWindow({
            width  : 440,
            height : 200,
            modal: false,
            border : false,
            closeAction : 'hide',
            layout : 'fit',
            ishidden : true,
            resizable: false,
            minimizable:false,
            maximizable:false,
            title  : 'Generar Mmis',
            plain  : true,
            items  : [
                {
                    xtype: 'form',
                    labelWidth: 120, // label settings here cascade unless overridden

                    frame:true,
                    bodyStyle:'padding:25px 25px 0;background:white;border:1px solid #bbb',
                    width: 350,
                    defaults: {anchor: '95%'},

                    items: [
                        {
                            fieldLabel: 'Cantidad Articulos',
                            name: 'Cantidad',
                            allowBlank:false,
                            xtype: 'numberfield'
                        },
                         {
                            fieldLabel: 'Cantidad x Palet',
                            name: 'CantidadxPalet',
                            allowBlank:false,
                            xtype: 'numberfield'
                        },
                        {
                            "xtype":"xcombo",
                            fieldLabel:'Tipo De Palet',
                            valueField:'Id',
                            "displayField":"Descripcion",
                            "autoLoad":false,
                            "autoSelect":true,
                            "selectOnFocus":true,
                            "forceSelection":true,
                            "forceReload":true,
                            "hiddenName":"TipoDePalet",
                            "loadingText":"Cargando...",
                            "lazyRender":true,
                            "searchField":"Descripcion",
                            "store":new Ext.data.JsonStore({
                               "id":0,
                               "url":"datagateway\/combolist\/model\/TiposDePalets/m\/Almacenes"
                            })
                        }
                    ],

                    buttons: [{
                        text: 'Generar',
                        handler: this.onGenerarMmi,
                        scope: this
                    },{
                        text: 'Cancelar',
                        handler: function () {
                            this.GenerarMmi.hide();
                        },
                        scope: this
                    }]
                }
            ]
       });
    },

    createUsarMercaderiaWindow: function()
    {
       this.comboStore = new Ext.data.JsonStore({
           "id":0,
           "url":"datagateway\/combolist\/model\/UnidadesDeMedidas\/m\/Base",
           "storeId":"UnidadDeMedidaStore"
       });


       this.UsarMercaderia = app.desktop.createWindow({
            width  : 340,
            height : 190,
            modal: true,
            border : false,
            closeAction : 'hide',
            layout : 'fit',
            ishidden : true,
            resizable: false,
            minimizable:false,
            maximizable:false,
            title  : 'Usar Mercaderia',
            plain  : true,
            items  : [
                {
                    xtype: 'form',
                    labelWidth: 75, // label settings here cascade unless overridden

                    frame:true,
                    bodyStyle:'padding:25px 25px 0;background:white;border:1px solid #bbb',
                    width: 350,
                    defaults: {anchor: '95%'},

                    items: [{
                            fieldLabel: 'Cantidad',
                            name: 'Cantidad',
                            allowBlank:false,
                            xtype: 'numberfield'
                        },
                        {"xtype":"xcombo",fieldLabel:'Unidad', valueField:'Id',"displayField":"Descripcion","autoLoad":false,"autoSelect":true,"selectOnFocus":true,"forceSelection":true,"forceReload":true,"hiddenName":"UnidadDeMedida","loadingText":"Cargando...","lazyRender":true,"searchField":"Descripcion","store":this.comboStore}
                    ],

                    buttons: [{
                        text: 'Usar',
                        handler: this.onUsarORetornarMercaderia,
                        scope: this
                    },{
                        text: 'Cancelar',
                        handler: function () {
                            this.UsarMercaderia.hide();
                        },
                        scope: this
                    }]
                }
            ]
       });
    },

    actualizarGrafico: function () {
         Models.Produccion_Model_ProduccionesMapper.getTotalProducido(this.OrdenDeProduccion.data.Id,function(result, e) {
            if (e.status) {
                var falta = this.OrdenDeProduccion.data.Cantidad - result;
                if (falta < 0) falta = 0;
                this.graficoData = [
                    ['Terminado',result],
                    ['Pendiente',falta]
                ];
                this.gstore.loadData(this.graficoData);
            }
        }, this);


        this.gridProduccionPorTurno.store.load();
    },

    UsarTodoMmi: function(){
        var sModel  = this.ordenesProduccionMmiGrid.getSelectionModel();


        if (sModel.getCount() == 0) {
            Ext.Msg.alert('Atención', 'Seleccione primero uno o mas Mmis');
            return;
        }


        selected = sModel.getSelections();
        idmmis = selected[0].data.Mmi;
        for (var i=1; i<selected.length;i++){
            idmmis += ','+selected[i].data.Mmi;
        }

        Models.Produccion_Model_ProduccionesMapper.quitarTodaMercaderiaAMmi(idmmis, this.Produccion,function(result, e) {
            if (e.status) {
                this.ordenesProduccionMmiGrid.store.load();
                this.gridLog.store.load();
            }
        }, this);
    },

    renderPaso2: function () {
        this.ordenesProduccionMmiGrid = Ext.ComponentMgr.create(<?=$this->ordenesProduccionMmiGrid?>);
        this.gridLog = Ext.ComponentMgr.create(<?=$this->gridLog?>);
        this.produccionesMmis = Ext.ComponentMgr.create(<?=$this->produccionesMmis?>);

        this.panelRequerimientos = new Rad.RequerimientosMaterialesPanel({title:'Requerimientos'});

        this.produccionesMmis.getTopToolbar().addButton({
            text:'Generar Mmi',
            icon: 'images/palets/mmi32agregar.png',
            cls:  'x-btn-text-icon',
            scale: 'large',
            iconAlign:'top',
            scope: this,
            handler: this.GenerarMmis
        });

        var ttb = this.ordenesProduccionMmiGrid.getTopToolbar();
        ttb.addButton([
            {
                text:'Utilizar',
                icon: 'images/32/quitar.png',
                cls:  'x-btn-text-icon',
                scale: 'large',
                iconAlign:'top',
                scope: this,
                handler: function () {
                    this.usarRetornarMercaderiaMode = 'utilizar';
                    this.UsarORetornarMateriaPrima();
                }
            }, {
                text:'Utilizar Todo',
                icon: 'images/32/quitartodo.png',
                cls:  'x-btn-text-icon',
                scale: 'large',
                iconAlign:'top',
                scope: this,
                handler: function () {
                    Ext.Msg.confirm('Atención',"Quiere marcar los Mmis seleccionados como utilizados?",function(btn){
                        if (btn == 'yes') {
                            this.UsarTodoMmi();
                        }
                    },this);

                }
            }, {
                text:'Retornar',
                icon: 'images/32/retornar.png',
                cls:  'x-btn-text-icon',
                scale: 'large',
                iconAlign:'top',
                scope: this,
                handler: function () {
                    this.usarRetornarMercaderiaMode = 'retornar';
                    this.UsarORetornarMateriaPrima()
                }
            },
            {
                text:'Recargar',
                icon: 'images/32/refresh.png',
                cls:  'x-btn-text-icon',
                scale: 'large',
                iconAlign:'top',
                scope: this,
                handler: function(){
                    this.ordenesProduccionMmiGrid.store.load();
                    this.actualizarGrafico();
                }
            },{xtype: 'tbfill'},
            {
                text:'Uni/Producto',
                icon: 'images/32/boxopen.png',
                cls:  'x-btn-text-icon',
                scale: 'large',
                iconAlign:'top',
                enableToggle: true,
                scope: this,
                toggleHandler: function(b,s){
                    var cm = this.ordenesProduccionMmiGrid.getColumnModel();

                    var c1 = cm.getIndexById(6);
                    var c2 = cm.getIndexById(7);
                    var c3 = cm.getIndexById(9);

                    if (s) {
                        cm.setRenderer(c1,
                            function(v, params, record){
                                return (record.data.CantidadProducto * record.data.CantidadActual)+' '+record.data.UnidadDeMedidaProductoDescripcion;
                        });
                        cm.setRenderer(c2,
                            function(v, params, record){
                                return (record.data.CantidadProducto * record.data.MmisCantidadActual)+' '+record.data.UnidadDeMedidaProductoDescripcion;
                        });

                        cm.setRenderer(c3,
                            function(v, params, record){
                                return (record.data.CantidadProducto * (record.data.CantidadActual - record.data.MmisCantidadActual))+' '+record.data.UnidadDeMedidaProductoDescripcion;
                        });

                        cm.getColumnById(6).summaryRenderer = function(calculatedValue, cellAttributes, r){
                            return calculatedValue* r.data.CantidadProducto;
                        };

                        cm.getColumnById(7).summaryRenderer = function(calculatedValue, cellAttributes, r){
                            return calculatedValue* r.data.CantidadProducto;
                        };

                        cm.getColumnById(9).summaryRenderer = function(calculatedValue, cellAttributes, r){
                            return calculatedValue* r.data.CantidadProducto;
                        };
                    } else {
                        cm.setRenderer(c1,
                            function(v, params, record){
                                return record.data.CantidadActual;
                        });
                        cm.setRenderer(c2,
                            function(v, params, record){
                                return record.data.MmisCantidadActual;
                        });
                        cm.setRenderer(c3,
                            function(v, params, record){
                                return record.data.CantidadActual - record.data.MmisCantidadActual
                            }
                        );
                        cm.getColumnById(6).summaryRenderer = function(calculatedValue, cellAttributes, r){
                            return calculatedValue;
                        };

                        cm.getColumnById(7).summaryRenderer = function(calculatedValue, cellAttributes, r){
                            return calculatedValue;
                        };
                        cm.getColumnById(9).summaryRenderer = function(calculatedValue, cellAttributes, r){
                            return calculatedValue;
                        };
                    }
                    this.ordenesProduccionMmiGrid.getView().refresh();
                }
            },{
                text:'Ver Orden',
                icon: 'images/32/alacarte.png',
                cls:  'x-btn-text-icon',
                scale: 'large',
                iconAlign:'top',
                enableToggle: true,
                scope: this,
                handler: function(){
                    // Ext.getCmp('produccionVerOrd1').toggleCollapse();
                }
            }

        ]);

        // Mostramos los detalles para La grilla de Mmis de materia prima
        this.ordenesProduccionMmiGrid.getSelectionModel().on('rowselect', function (t,  rowIndex, r ) {
            this.showDetails(r,this.detailsTemplate);
        }
        ,this);
        this.ordenesProduccionMmiGrid.getSelectionModel().on('rowdeselect', function (t,  rowIndex, r )  {
            this.showDetails(null,this.detailsTemplate);
        }
        ,this);

        // Mostramos los detalles para La grilla de Mmis movimientos
        this.gridLog.getSelectionModel().on('rowselect', function (t,  rowIndex, r ) {
            this.showDetails(r,this.detailsLogMmiTemplate);
        }
        ,this);
        this.gridLog.getSelectionModel().on('rowdeselect', function (t,  rowIndex, r )  {
            this.showDetails(null,this.detailsLogMmiTemplate);
        }
        ,this);



        this.ordenesProduccionMmiGrid.store.on('beforeload', function() {
           var rt = {};
           rt['pfilter[0][field]'] 	  = 'OrdenesDeProduccionesDetallesOrdenDeProduccion';
           rt['pfilter[0][data][value]'] = this.OrdenDeProduccion.data.Id;
           rt['pfilter[0][data][type]']  = 'numeric';
           rt['pfilter[0][data][comparison]'] = 'eq';
           this.ordenesProduccionMmiGrid.baseParams = rt;
        }, this);


        this.graficoData = [
            ['Terminado',0],
            ['Falta',100],

        ];
        this.gstore = new Ext.data.ArrayStore({
            // store configs
            autoDestroy: true,
            storeId: 'myStore',
            data: this.graficoData,
            idIndex: 0,
            fields: [
               'desc',
               {name: 'cuanto', type: 'float'}
            ]
        });

        // Template de Detalles Del MMi
        this.detailsTemplate = new Ext.XTemplate(
                // '<div class="panelDeDetalles panelDetalleAzul">',
                        '<tpl for=".">',
                                '<h3>{Mmi_cdisplay}</h3>',
                                '<div class="detalle">',
                                '<b>Producto:</b>',
                                '<span>{productoDescripcion}</span>',
                                '<b>Cantidad Actual / Total:</b>',
                                '<span>{[values.CantidadProducto*parseFloat(values.MmisCantidadActual)]} / {[values.CantidadProducto*parseFloat(values.CantidadActual)]} {UnidadDeMedidaProductoDescripcion}</span>',
                                '<b>Cantidad Consumida:</b>',
                                '<span>{[values.CantidadProducto*parseFloat(values.CantidadActual) - values.CantidadProducto*parseFloat(values.MmisCantidadActual)]} {UnidadDeMedidaProductoDescripcion}</span>',
                                '<b>Lote:</b>',
                                '<span>{MmisLotesNumero}</span>',
                                '<b>Elaboración - Vencimiento</b>',
                                '<span>{[Ext.util.Format.date(values.MmisLotesFechaElaboracion,"d/m/Y")]} - {[Ext.util.Format.date(values.MmisLotesFechaVencimiento,"d/m/Y")]}',
                                '</div>',

                        '</tpl>'
                // '</div>'
        );
        this.detailsTemplate.compile();

         // Template de Detalles Del Log de movimientos MMi
        this.detailsLogMmiTemplate = new Ext.XTemplate(
                '<div class="panelDeDetalles" >',
                        '<tpl for=".">',
                                '<h3>{MmisIdentificador}</h3>',
                                '<div class="detalle">',
                                '<b>Articulo:</b>',
                                '<span style="font-size:110%">{MmisArticulosArticuloDescripcion}</span>',
                                '<b>Movimiento:</b>',
                                '<tpl if="values.Tipo == 1">',
                                    '<tpl if="values.Cantidad &lt; 0">',
                                        '<span style="font-size:110%">Se utilizaron ',
                                        '{[Ext.util.Format.number(values.Cantidad,"0.00")*-1]}</span>',
                                    '</tpl>',
                                    '<tpl if="values.Cantidad &gt; 0">',
                                        '<span style="font-size:110%">Se retornaron ',
                                        '{[Ext.util.Format.number(values.Cantidad,"0.00")]}</span>',
                                    '</tpl>',
                                '</tpl>',
                                '<tpl if="values.Tipo == 2">',
                                    '<span style="font-size:110%">Se crearon ',
                                    '{[Ext.util.Format.number(values.Cantidad,"0.00")]}</span>',
                                '</tpl>',


                        '</tpl>',
                '</div>'
        );
        this.detailsTemplate.compile();

		 // Template de Detalles de ka Orden de Produccion
        this.ordenProduccionTemplate = new Ext.XTemplate(
                '<div style="overflow-y:scroll;height:177px">',
                        '<tpl for=".">',
                                '<h3 style="text-align:left; float:right;padding-right:10px;">Cantidad: {Cantidad}  </h3>',
                                '<h3 style="text-align:left;padding-bottom:5px;">Orden Número: {Id}</h3>',
                                '<h3 style="text-align:left;font-size:80%">{Articulo_cdisplay}</h3">',
                                '<div class="detalle" >',
                                '<b>Instrucciones:</b>',
                                '<span ><pre>{Instrucciones}</pre></span></div>',
                        '</tpl>',
                '</div>'
        );
        this.ordenProduccionTemplate.compile();

        return {
            layout: 'border',
            border: false,
            items: [{
                region: 'center',
                border: false,
                layout: 'border',
                margins: '0 5 0 0',
                items:[
                        {
                                xtype:  'tabpanel',
                                region: 'center',
                                activeTab: 0,
                                border: true,
                                listeners: {
                                    tabchange: function () {
                                        var detailEl = Ext.getCmp('produccionPanelDetalleDeMmi').body;
                                        if (detailEl) detailEl.update('');
                                    }
                                },
                                items: [
                                    this.ordenesProduccionMmiGrid,
                                    {
                                        border:true,
                                        layout: 'hbox',
                                        title: 'Producido',
                                        layoutConfig : {
                                            type : 'vbox',
                                            align : 'stretch',
                                            pack : 'start'
                                        },
                                        defaults : {
                                            flex : 1
                                        },
                                        items: [
                                            this.produccionesMmis,
                                            {
                                                border:true,
                                                margins: '0 0 0 5',
                                                layout: 'vbox',
                                                title: 'Producción Por Turno',
                                                layoutConfig : {
                                                    type : 'vbox',
                                                    align : 'stretch',
                                                    pack : 'start'
                                                },
                                                defaults : {
                                                    flex : 1
                                                },
                                                items: [
                                                    this.gridProduccionPorTurno,
                                                    new Ext.chart.ColumnChart({
                                                        store: this.gridProduccionPorTurno.store,
                                                        //url:'../ext-3.0-rc1/resources/charts.swf',
                                                        series: [{
                                                            type: 'column',
                                                            displayName: 'Cantidad',
                                                            yField: 'Cantidad',
                                                            xField: 'Numero'

                                                        }],
                                            //                    tipRenderer : function(chart, record, index, series) {
                                            //                        return series.displayName+'\nPeriodo: ' + record.data.Mes + '/' + record.data.Anio + "\n" +
                                            //                               'Total: ' + Ext.util.Format.usMoney(record.get(series.yField));
                                            //                    },
                                                        extraStyle: {
                                                            padding: 10,
                                                            animationEnabled: true,
                                                            legend:{
                                                                display:'bottom'
                                                            },
                                                            xAxis: {
                                                                color: 0x3366cc,
                                                                majorGridLines: {size: 1, color: 0xdddddd},

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
                                    },
                                    this.gridLog,
                                    this.panelRequerimientos
                                ]
                        },{
                                margins: '5 0 0 0',
                                region: 'south',
                                id: 'produccionVerOrd1',
                                height:200,
                                title: 'Orden de Producción',
                                bodyCssClass: 'panelDeDetalles panelDetalleAzul',
                                collapsible:true,
                                collapsed: true,
                                id: 'produccion-OrdenDeProduccionPanel',
                                bodyStyle: 'background-color:white;border-bottom:1px;'
                        }
                ]
            },{
                 region: 'east',
                 width: 270,
                 layout: 'vbox',
                 layoutConfig: {
                    align : 'stretch',
                    pack  : 'start',
                 },
                 bbar:{
                    layout:'hbox',
                    items:[
                        {
                            text:'Detener',
                            icon: 'images/32/Pause-icon.png',
                            cls:  'x-btn-text-icon',
                            scale: 'large',
                            iconAlign:'left',
                            flex:1,
                            scope: this,
                            handler: this.detenerProduccion
                        },{
                            text:'Finalizar',
                            icon: 'images/32/stop-red-icon.png',
                            cls:  'x-btn-text-icon',
                            scale: 'large',
                            iconAlign:'left',
                            flex:1,
                            scope: this,
                            handler: this.finalizarProduccion
                        }

                     ]
                 },
                 items: [

                    {
                        id:'produccionPanelDetalleDeMmi',
                        flex: 2,
                        bodyCssClass: 'panelDeDetalles panelDetalleAzul',
                        bodyStyle: 'background-color:white;border-bottom:1px;',
                        border: false
                    },
                    {
                        xtype: 'piechart',
                        flex:1,
                        dataField: 'cuanto',
                        categoryField: 'desc',
                        store: this.gstore,
                        series: [{
                            style: { colors: ['#1FC071', '#B2000F'] }
                        }],
                        extraStyle:
                        {
                            legend:
                            {
                                display: 'bottom',
                                padding: 5,
                                font:
                                {
                                    family: 'Tahoma',
                                    size: 13
                                }
                            }
                        }

                    }
                 ]
            }
            ]
        }
    },

    detenerProduccion: function() {
        var win = app.desktop.createWindow({
            width  : 400,
            height : 250,
            animateTarget : null,
            modal: true,
            border : false,
            layout : 'fit',
            ishidden : true,
            title  : 'Detener',
            plain  : true,
            items  : {
                xtype: 'form',
                labelWidth: 120, // label settings here cascade unless overridden

                frame:true,
                bodyStyle:'padding:25px 25px 0;background:white;border:1px solid #bbb',
                width: 350,
                defaults: {anchor: '95%'},

                items: [
                    {
                        "xtype":"xcombo",
                        fieldLabel:'Motivo',
                        valueField:'Id',
                        "displayField":"Descripcion",
                        "autoLoad":false,
                        "autoSelect":true,
                        "selectOnFocus":true,
                        "forceSelection":true,
                        "forceReload":true,
                        "hiddenName":"Motivo",
                        "loadingText":"Cargando...",
                        "lazyRender":true,
                        "searchField":"Descripcion",
                        "store":new Ext.data.JsonStore({
                           "id":0,
                           "url":"datagateway\/combolist\/model\/ProduccionesMotivosDeFinalizaciones/m\/Produccion"
                        })
                    },{
                        fieldLabel: 'Comentario',
                        name: 'Comentario',
                        allowBlank:false,
                        xtype: 'textarea'
                    }
                ],
                buttons: [{
                    text: 'Detener',
                    handler: function() {
                        var form = win.items.get(0).getForm();

                        var Motivo = form.findField('Motivo').getValue();
                        var Comentario = form.findField('Comentario').getValue();

                        if (!Motivo) return;

                        Models.Produccion_Model_ProduccionesMapper.detenerProduccion(this.Produccion,Motivo,Comentario,function(result, e) {
                            if (e.status) {
                                this.abmWindow.hide();
                                win.close();
                                this.grid.store.reload();
                            }

                        }, this);
                    },
                    scope:this

                }]
            }
       });
       win.show();

    },

    finalizarProduccion: function() {
         Ext.Msg.confirm('Atencion',"Esta seguro que quiere finalizar la producción?",function(btn){
            if (btn == 'yes') {
                var wait = app.desktop.showMsg({
                    progressText: 'Espere por favor...',
                    msg: 'Finalizando producción y enviando MMIs al deposito temporal',
                    modal: true,
                    closable: false,
                    width: 300,
                    wait: true,
                    waitConfig: {interval:200}
                });
                Models.Produccion_Model_ProduccionesMapper.finalizarProduccion(this.Produccion, function(result, e) {

                    if (e.status) {
                        wait.hide();
                        this.abmWindow.hide();
                        this.grid.store.reload();
                    }
                }, this);
            }
        },this);
    },

    initEstadisticaPorTurno: function () {
        store = new Ext.data.JsonStore({
            // store configs
            autoDestroy: true,
            autoLoad: false,
            url: '/Produccion/viewProduccion/getproduccionporturno/',
            remoteSort: false,
            sortInfo: {
                field: 'Produccion',
                direction: 'ASC'
            },
            storeId: 'myStore',

            // reader configs
            idProperty: 'id',
            root: 'rows',
            totalProperty: 'count',
            fields: [{
                name: 'Id'
            },{
                name: 'Numero'
            }, {
                name: 'Produccion'
            }, {
                name: 'Cantidad',
                type: 'float',

            }, {
                name: 'Comienzo',
                type: 'date',
                dateFormat: 'Y-m-d H:i:s'
            }, {
                name: 'Final',
                type: 'date',
                dateFormat: 'Y-m-d H:i:s'
            }]
        });

        this.gridProduccionPorTurno = new Ext.grid.GridPanel({
            flex:1,
            store: store,
            border: false,
            plugins: [
                new Ext.ux.grid.GridSummary()
            ],
            viewConfig: {
                forceFit: true
            },
            region: 'center',
//            title: 'Produccion Por Turno',
            columns: [
                 {
                    header   : 'Turno',
                    width    : 30,
                    dataIndex: 'Numero',
                    sortable : false
                },
                {
                    header   : 'Comienzo',
                    width   : 80,
                    dataIndex: 'Comienzo',
                    sortable : false,
                    xtype: 'datecolumn',
                    format: 'd/m/Y H:i:s'
                },{
                    header   : 'Final',
                    width    : 80,
                    sortable : false,
                    dataIndex: 'Final',
                    xtype: 'datecolumn',
                    format: 'd/m/Y H:i:s'
                },{
                    header   : 'Cantidad',
                    dataIndex: 'Cantidad',
                    align: 'right',
                    width    : 70,
                    summaryType: 'sum',
                    sortable : false
                }
            ]
        });
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

    initializeActividadesDropZone: function(g) {
        g.dropZone = new Ext.dd.DropZone(g.getView().scroller, {
            mainscope: this,

            getTargetFromEvent: function(e) {
                return e.getTarget('.actividades-target');
            },


            onNodeEnter : function(target, dd, e, data){
                Ext.fly(target).addClass('actividades-target-hover');
            },


            onNodeOut : function(target, dd, e, data){
                Ext.fly(target).removeClass('actividades-target-hover');
            },


            onNodeOver : function(target, dd, e, data){
                return Ext.dd.DropZone.prototype.dropAllowed;
            },


            onNodeDrop : function(target, dd, e, data){
                var isOk = null;
                var rowIndex = g.getView().findRowIndex(target);
                var h = g.getStore().getAt(rowIndex);
                Models.Produccion_Model_ProduccionesMapper.asociarEmpleados(this.mainscope.Produccion,data.empleadoData.Id, h.data.Id, function(result, e) {
                    if (e.status) {
                        var targetEl = Ext.get(target);
                        targetEl.update(data.empleadoData.RazonSocial+', Dni: '+data.empleadoData.Dni+'<br>'+targetEl.dom.innerHTML);
                    }
                    isOk = e.status;
                }, this.mainscope);

                return true;
            }
        });
    }
});

new Apps.<?=$this->name?>();
