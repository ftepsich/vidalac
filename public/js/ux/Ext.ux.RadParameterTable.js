/**
 * @copyright SmartSoftware SRL
 * @author Martin Alejandro Santangelo
 */
Ext.ns('Rad');

Rad.ParameterTable =  Ext.extend(Ext.grid.PropertyGrid, {
    constructor: function (config) {
        config = config || {};

        if (!config.masterGrid) alert('Rad.ParameterTable necesita el parametro masterGrid');
        if (!config.model) alert('Rad.ParameterTable necesita el parametro model');
        if (!config.module) alert('Rad.ParameterTable necesita el parametro module');


        Ext.grid.PropertyGrid.superclass.constructor.call(this, config);

        if (config.caracteristicas) {
            this.customEditors = {};
            for (var i = 0; i < config.caracteristicas.length; i++){

                var c = config.caracteristicas[i];
                this.customEditors[c.Descripcion] = this.getEditorFromType(c.TipoDeCampo,c.Id);
            }
        }

        this.on('validateedit', function(e) {
            // console.log(arguments);
            var selected = this.masterGrid.getSelectionModel().getSelected();
            if (selected) {
                Rad.callRemoteJsonAction ({
                    url: '/default/datagateway/setcaracteristicas',
                    params: {
                        model: this.model,
                        m: this.module,
                        property: e.record.id,
                        value:    e.value,
                        id:       selected.data.Id
                    },
                    async: false,
                    success: function() {

                    },

                    failure: function (r) {
                        e.record.data.value = e.originalValue;
                        return true;
                    }
                });
            }
        },this);
    },

    getEditorFromType: function(fieldType, caracteristica) {
        switch (fieldType) {
            case '1':  //CAMPO_ENTERO:
                return new Ext.grid.GridEditor(new Ext.form.NumberField({selectOnFocus:true, decimalPrecision:0}));
            break;
            case '2':  //CAMPO_DECIMAL:
                return new Ext.grid.GridEditor(new Ext.form.NumberField({selectOnFocus:true, decimalPrecision:6}));
            break;
            case '3':  //CAMPO_FECHA:
                return new Ext.grid.GridEditor(new Ext.ux.form.XDateField({format:'d/m/Y'}));
            break;

            case '5':  //CAMPO_LISTA:
                return new Ext.grid.GridEditor(
                    new Ext.form.ComboBox({
                        displayField: 'description',
                        valueField: 'description',
                        lazyRender: true,
                        // editable:true,
                        forceSelection: true,
                        autocomplete:true,
                        allowBlank:true,
                        store: new Ext.data.JsonStore({
                            root: 'rows',
                            totalProperty: 'count',
                            idProperty:'id',
                            url: '/default/datagateway/getcaracteristicaslista/caracteristica/'+caracteristica+'/model/'+this.model+'/m/'+this.module,
                            fields:[{name:'id'},{name:'description'}]
                        }),
                        typeAhead: false,
                        triggerAction: 'all',
                        selectOnFocus:true
                    })
                );

            break;
            case '6':  //CAMPO_BOLEANO:
                return new Ext.grid.GridEditor(
                    new Ext.form.ComboBox({
                    store: ['Si', 'No'],
                    triggerAction: 'all',
                    selectOnFocus:true,
                }));
            break;
            case '7':  //CAMPO_FECHAYHORA:
                return new Ext.grid.GridEditor( new Ext.ux.form.DateTime(new Ext.ux.form.XDateField({dateFormat:'d/m/Y', timeFormat: 'H:i:s'})));
            break;
        }
    },

    getRowSelectHandler: function() {
        var that = this;
        return function(i,id, r) {
            if (!r.data.Id) return;
            var el = that.getGridEl();
            el.mask();
            Rad.callRemoteJsonAction ({
                url: '/default/datagateway/getcaracteristicas/model/'+that.model+'/m/'+that.module,
                params: {
                    id: r.data.Id
                },
                success: function (response) {
                    that.setSource(response.data);
                    el.unmask();
                },
                failure: function (){
                    el.unmask();
                }
            });
        }
    }
});