Ext.ns('Rad');
/**
 * Esta funcion itera sobre los hijos de un panel form e incializa los stores
 */
Rad.Form = Ext.extend(Ext.form.FormPanel, {
    childGrids: [],
    /**
     * Indica q cuando se cierra la ventana en vez de recargar el registro, hace un reload completo del store.
     */
    reloadGridOnClose: false,
    border: false,
    frame: true,
    record: null,
    labelWidth: 80,

    /**
     * Si se reemplaza por una funcion sera llamada luego del cargar el registro
     */
    onAfterLoadRecord: null,

    /**
     * Reconstruye los store de los combos
     */
    buildComboStore:  function (item) {
        if (item.xtype == 'xcombo' || item.xtype == 'LinkTriggerField' || item.xtype == 'AdvCombo') {
            if (item.store) {
                storeCfg = item.store;
                if (storeCfg.storeType)	item.store = new Ext.data[storeCfg.storeType](storeCfg.config);
            }
        } else {
            // si tiene una grilla como componente la enganchamos para enviar la informacion nueva en el submit
            if (item.xtype == 'radformabmeditorgridpanel') {
                this.childGrids.push(item);
            } else {
                if (item.items) {
                    Ext.each (item.items, this.buildComboStore,this);
                }
            }
        }
    },
    initComponent:function() {
        this.childGrids = [];
        this.addEvents('afterloadrecord');

        // Parche para que cree el store del combo automaticamente (Martin)
        this.initialConfig.trackResetOnLoad=true;
        Ext.each (this.initialConfig.items, this.buildComboStore,this);

        // call parent
        Rad.Form.superclass.initComponent.apply(this, arguments);
    },

    loadChildGrid: function() {
        Ext.each(this.childGrids, function (grid) {
            var cmp = Ext.ComponentMgr.get(grid.id);
            cmp.parentForm = this;    	// Le seteamos el formulario padre a las grillas hijas para que saquen el valor del campo relacionado
            var field = this.getForm().findField(grid.parentRemoteField);
            var value = null;
            if (field) {
                value = field.getValue();
            }
            if (!value) {
                value = 0;
                cmp.newRecord = true;
            } else {
                cmp.newRecord = false;
            }
            detailGrid = {
                remotefield: grid.parentLocalField,
                localfield:  grid.parentRemoteField
            };
            cmp.loadAsDetailGrid(detailGrid,value);
        }, this);
    },
    // Creo mi propia funcion q llama  a la de BasicForm y guarda el record
    loadRecord: function(record) {
        this.record = record;
        //this.getForm().reset();
        this.getForm().loadRecord(record);
        this.autoLoadCombos();
        this.loadChildGrid();
        this.fireEvent("afterloadrecord", this);
    },

    onLoadClick: function() {
        this.load({
            url: this.url,
            waitMsg: 'Cargando...'
        });
    },

    onAfterMetaChange: function (){},

    submit: function() {
        var opt = {
            url: this.url,
            scope: this,
            success: this.onSuccess,
            failure: this.onFailure,
            waitTitle: '',
            waitMsg: '',
            params: {}
        };
        if (this.childGrids) {
            Ext.each (this.childGrids, function (item) {
                var cmp = Ext.ComponentMgr.get(item.id);
                if (cmp) {
                    opt.params[cmp.name] = cmp.getValue();
                }
            },this);
        }
        if (this.url) {

            v = this.getForm().isValid();
            if(!v) {
                this.showError('El formulario contiene errores');
                return;
            }
            // si el formulario esta modificado lo envio, sino solo disparo el evento para que siga sin modificar nada
            if (this.getForm().isDirty()) {
                this.getForm().submit(opt);
            } else {
                this.getForm().fireEvent('actioncomplete', this);
            }
        } else {
            v= this.getForm().isValid();
            if(v) {
                this.onSuccess();
            } else {
                this.showError('El formulario contiene errores');
            }
        }
    },

    // Hace la carga inicial de todo los combos del formulario (Martin Santangelo)
    autoLoadCombos: function() {
        var fn = function(item){
            if (item.xtype == 'xcombo' || item.xtype == 'LinkTriggerField' || item.xtype == 'AdvCombo') {
                item.doCustomLoad();
            } else {
                if (item.items &&  item.items.each) {
                    item.items.each(fn,this);
                }
            }
        }
        this.items.each(fn,this);
    },

    fixBindedGridComboDescriptions: function(item) {
        if (item.xtype == 'xcombo' || item.xtype == 'combo' || item.xtype == 'LinkTriggerField') {
            var form = this.getForm();
            var combo = form.findField( item.id);
            this.record.set(item.name + "_cdisplay", combo.getRawValue());
        } else {
            if (item.items) {
                item.items.each(this.fixBindedGridComboDescriptions,this);
            }
        }
    },

    updateGridStoreRecord: function(values) {
        // Si no tiene store es pq no tiene una grilla asociada, entonces ignoramos
        if (!this.record.store) return;
        //values = null;
        if (values) {
            // no Queremos q el store escriba los cambios ya q ya los escribimos nosotros
            // TODO: hacer otra implementacion posiblemente usando ext direct
            if (!this.reloadGridOnClose) {
                var fs = this.record.fields;
                fs.each(function(f) {
                    var v = values[f.name];
//                    if (v != null){
                    this.record.data[f.name] =  f.convert(v);
//                    }
                }, this);
                this.record._phid = this.record.id;
                this.record.id = values['Id'];
                this.record.store.reMap(this.record);

                this.record.store.fireEvent('datachanged',this.record.store);
            } else {
                this.record.store.reload();
            }

        } else {
            // Updateamos el registro en la grilla
            this.getForm().updateRecord(this.record);
            // Acomodamos la columna descripcion de los combos ya q al no grabar el server no me los envio
            this.items.each(this.fixBindedGridComboDescriptions,this);
        }
    },

    onSuccess: function(form, action) {
        var form = this.getForm();
        if (this.record != null) {
            // Si enviamos el formulario y obtenemos el id lo seteamos en el registro
            if (action) {
                form.loadRecord(new Ext.data.Record(action.result.record));
            }            		//field.setValue(this.gridBind.store.getCount() + 1);
            if (action) {
                this.updateGridStoreRecord(action.result.record);
                //this.record.store.commitChanges();
            } else {
                this.updateGridStoreRecord();
                this.getForm().fireEvent('actioncomplete', this); 	// Si no se envia el formulario disparmos el evento actioncomplete
            }
        }
    },

    onFailure: function(form, action) {
        // si es un confirm no muestro nada
        if (action.response && action.response.status == 506) return;

        if (action && action.result) {
            if (action.result.errors) {
                var txt = '<div align="left">';
                for (e in action.result.errors) {
                    txt = txt +'<b>'+e+'</b>: '+ action.result.errors[e]+'<br>';
                }
                txt = txt + '</div>';
                this.showError(txt);
            } else {
                this.showError(action.result.msg);
            }
        } else {

            if (action.failureType == 'client') {
                this.showError('No se completaron todos los campos requeridos');
            } else {
                this.showError(action.response.responseText);
            }
        }
    },

    showError: function(msg, title) {
        title = title || 'Error';
        window.app.desktop.showMsg({
            title: title,
            msg: msg,
            modal: true,
            icon: Ext.Msg.ERROR,
            buttons: Ext.Msg.OK
        });
    }
});

Ext.reg('radform', Rad.Form);
