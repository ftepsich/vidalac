Ext.ns('Ext.ux.form');

Ext.ux.form.ComboBox = function (cfg) {
    if (!cfg) cfg = {};
    if (!cfg.store && cfg.url) {
        cfg.store = new Ext.data.Store({
            baseParams: cfg.baseParams || {},
            url: cfg.url,
            reader: new Ext.data.JsonReader({},
                [cfg.valueField, cfg.displayField || cfg.valueField])
        });
    } else {
        if (!cfg.store || Ext.type(cfg.store) == 'array' || cfg.store instanceof Ext.data.SimpleStore) {
            cfg.mode = "local";
        }
        else {
            cfg.mode = "remote";
        }
    }

    if (cfg.transform) {
        this.clearValueOnRender = !Ext.fly(cfg.transform).first("[selected=true]");
    }

    /*
     * If we have a valueField this will make
     * form.getValues return the correct value
     */
    if (cfg.valueField) {
        var extraCfg = {
            hiddenName: cfg.name,
            hiddenId: cfg.name + "Id"
        };
    } else {
        var extraCfg = {};
    }

    Ext.ux.form.ComboBox.superclass.constructor.call(this, Ext.apply(extraCfg, {
        minListWidth: cfg.width
    },
    cfg));
};

Ext.extend(Ext.ux.form.ComboBox, Ext.form.ComboBox, {
    editable: false,
    triggerAction: 'all',
    autoLoad: true,
    forceSelection: true,
    clearValueOnRender: false,
    displayFieldTpl:  null,     // Template para la descripcion a mostrar
    descriptionPanel: null,
    filterFrom: null,
    mustFilter: true,



    /**
     * Funcion
     *
     * Esta se usa para retornar el valor q se enviara cada vez q el combo se carga como parametro adicional
     */
    filterQuery: null,

    initComponent: function () {

        if (this.clearValueOnRender) {
            this.on({
                'render': function () {
                    this.clearValue();
                },
                'destroy': function (){
                    Ext.destroy(this.tooltip);
                }
            },
            this);
        }
        /**
         * If width is set to 'auto' and minListWidth is not set then we need
         * to set a minListWidth so the list is guranteed to at least be the
         * same size as the combo box
         */
        if (((!this.width || this.width == 'auto') && !this.minListWidth)) {
            this.on("render", function () {
                this.minListWidth = this.wrap.getWidth();
            },
            this);
        }

        Ext.ux.form.ComboBox.superclass.initComponent.apply(this, arguments);
        if (this.mode == "remote") {
            this.store.on('load', this.assureValueEntry, this);
            if (this.autoLoad) {
                this.on("render", function () {
                    if (this.store.getCount() == 0) {
                        if (this.triggerAction == 'all') {
                            this.doQuery(this.allQuery, true);
                        } else {
                            this.doQuery(this.getRawValue());
                        }
                    }
                },
                this);
            }
        } else {
            this.assureValueEntry(this.store);
        }

        if (this.filterFrom && this.mustFilter) {
            this.form = this.findParentByType('radform');
            if (this.form ) {	// Si el padre es un radForm
                this.form.on('afterrender', this.attachClearEvent, this);
            }
        }
    },

    attachClearEvent: function (i, component, index ) {
        for (filter in this.filterFrom) {
            field = this.form.getForm().findField(filter);
            if (field) {
                field.on('select', function () {
                    if (this.mustFilter)
                        this.clearValue();
                }, this);
            }
        }
    },
    /**
     * Esta funcion hace la carga inicial del combo, y si esta pagina envia el id actual para traerlo tambien
     * asi puede reemplazarse por su descripcion
     * ASUME EL TRIGERALL EN TRUE
     */
    doCustomLoad: function ()
    {
        //si tiene tooltip lo limpiamos
        if (this.tooltip) {
            Ext.destroy(this.tooltip);
        }
        this.customLoad = true;
        // Si el store no tiene datos o si se uso el valor para mostrar la descripcion se ejecuta el doQuery
        if (this.store.getCount() == 0|| this.getValue() == this.getRawValue()) {
            if (this.triggerAction == 'all') {
                this.doQuery(this.allQuery, true);
            } else {
                this.doQuery(this.getRawValue());
            }
        }
        this.customLoad = false;
    },

    assureValueEntry: function () {
        if (this.forceSelection && this.getValue() == this.getRawValue()) this.setValue(this.value);
    },

    setValue: function (v) {
        var text = v;
        if (this.valueField) {
            var r = this.findRecord(this.valueField, v);
            if (r) {
                // Si el combo tiene un template para la descripcion lo usamos
                if (this.displayFieldTpl) {
                    tpl = new Ext.XTemplate(this.displayFieldTpl);
                    text = tpl.apply( r.data);
                    Ext.destroy (tpl);
                } else {
                    text = r.data[this.displayField];
                }
                // Si el combo tiene un template para mostrar en un panel lo usamos
                if (this.descriptionPanel) {

                    var tpl  = new Ext.Template ( this.descriptionPanel.tpl );

                    // if (this.tooltip == undefined) {

                    this.tooltip = new Ext.ToolTip({
                        target: this.id,
                        anchor: 'left',
                        width: 300,
                        autoHide: true,
                        html: tpl.apply( r.data)
                    });

                // } else {
                // this.tooltip.update(tpl.apply(r.data));
                // }

                //tpl.overwrite(Ext.getCmp(this.descriptionPanel.id).body, r.data);

                }

            } else if (this.valueNotFoundText !== undefined) {
                text = this.valueNotFoundText;
            } else if (this.lastSelectionText !== undefined && v ==  this.value) {
                text = this.lastSelectionText;
            }
        }
        this.lastSelectionText = text;
        if (this.hiddenField) {
            this.hiddenField.value = v;
        }

        Ext.form.ComboBox.superclass.setValue.call(this, text);
        this.value = v;
    },

    /**
     * If you load via this method then we assume we don't need to run doQuery again.
     */
    load: function (options) {
        this.store.load(options);
        var q = (this.triggerAction == 'all') ? this.allQuery: this.getRawValue();
        if (q === undefined || q === null) q = '';
        this.lastQuery = q;
    },

    doQuery: function(q, forceAll) {
        this.store.baseParams['search'] = this.searchField;

        if (this.filterFrom && this.mustFilter && !this.form ) {	// Si el padre es un radForm
            var roweditor = this.ownerCt;
            if (roweditor) this.roweditor = roweditor;
            if (!this.roweditor) alert('El combo '+this.name+' no encontro el formulario o grilla para filtrar');
        }

        //Lo cambio para que haga si o si la busqueda
        if (this.forceReload) this.lastQuery = '%$%';

        // Filtro por otro campo
        if (this.filterFrom && this.mustFilter && (this.form || this.roweditor) && !this.customLoad) {
            this.lastQuery = '%$%';
            var filterValue;
            if (this.form) {
                var values = this.form.getForm().getValues();

                for (filter in this.filterFrom) {
                    if (values[filter] != undefined) {
                        // probamos primero obtener el valor de los campos del formulario
                        filterValue = values[filter];
                    } else {
                        // si no del registro del store enganchado al formulario
                        filterValue = this.form.record.data[filter];
                    }

                    if (filterValue) this.store.baseParams[this.filterFrom[filter]] = filterValue;
                    else {

                        app.publish('/desktop/showError','Debe seleccionar primero '+filter);
                        return false;
                    }
                }
            } else {
                for (filter in this.filterFrom) {
                    filterValue = this.roweditor.record.data[filter];
                    if (filterValue) this.store.baseParams[this.filterFrom[filter]] = filterValue;
                }
            }
        }

        // Enviamos el id si es paginado para que el servidor nos envie ese registro asi obtenemos la descripcion
        if (this.pageSize != 0) {
            this.store.baseParams['Id'] = this.getValue();
        }

        Ext.ux.form.ComboBox.superclass.doQuery.call(this,q, forceAll);
    /*
        if (this.forceReload) {
        	this.store.baseParams.query = q;
      		this.store.reload();
        } else {
        	Ext.ux.form.ComboBox.superclass.doQuery.call(this,q, forceAll);
        }*/
    }
});
Ext.reg('xcombo', Ext.ux.form.ComboBox);
