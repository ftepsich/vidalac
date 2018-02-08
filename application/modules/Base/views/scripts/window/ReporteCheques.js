Ext.ns( 'Apps' );

Apps.<?=$this->name?> = Ext.extend(RadDesktop.Module, {
    autoStart: true,
    title: '<?=$this->title?>',
    appChannel: '/desktop/modules<?=$this->url()?>',
	
    eventlaunch: function(ev)
    {
        this.createWindow();
    },

    createWindow: function()
    {
        var win = app.desktop.getWindow(this.id+'-win');
        if ( !win ) {
            win = this.create();
        }
        win.show();
    },
	
    create: function()
    {
        /* ---- STORES ---- */

        this.arrCabecera = new Ext.data.ArrayStore({
            fields: ['desc', 'id'],
            data :  [
                        ['Interno (simple)',         '1'],
                        ['Completo (detallado)',     '2'],
                        ['Vacio (para exportacion)', '3']
                    ]
        });

        this.arrSiNo = new Ext.data.ArrayStore({
            fields: ['desc', 'id'],
            data :  [
                        ['Todos',   '3'],
                        ['Si',      '1'],
                        ['No',      '2']
                    ]
        });

        this.storeBancos = new Ext.data.JsonStore({ 
            id:        0,
            url:       'datagateway\/combolist\/model\/BancosSucursales\/m\/Base\/search\/Descripcion',
            storeId:   'BancoSucursalStore'
        });

        this.storeChequeras = new Ext.data.JsonStore({ 
            id:        0,
            url:       'datagateway\/combolist\/model\/Chequeras\/m\/Base\/search\/NumeroDeChequera',
            storeId:   'NumeroDeChequeraStore'
        });

        this.storePersonas = new Ext.data.JsonStore({    
            id:        0,
            url:       'datagateway\/combolist\/model\/Personas\/m\/Base\/search\/RazonSocial',
            storeId:   'PersonaStore'
        });

        this.storeEstados = new Ext.data.JsonStore({    
            id:        0,
            url:       'datagateway\/combolist\/model\/ChequesEstados\/m\/Base\/search\/Descripcion',
            storeId:   'ChequesEstadosStore'
        });

        this.storeOrdenesDePagos = new Ext.data.JsonStore({    
            id:        0,
            url:       'datagateway\/combolist\/model\/OrdenesDePagos\/m\/Facturacion\/search\/Numero',
            storeId:   'OrdenesDePagoStore'
        });       

        this.storeCuentasBancarias = new Ext.data.JsonStore({    
            id:        0,
            url:       'datagateway\/combolist\/model\/VBancosCuentas\/m\/Base\/search\/Descripcion',
            storeId:   'CuentasBancarias'
        }); 

        this.storeCuentasBancariasPropias = new Ext.data.JsonStore({    
            id:        0,
            url:       'datagateway\/combolist\/model\/VBancosCuentas\/m\/Base\/search\/Descripcion\/fetch\/EsPropia',
            storeId:   'CuentasBancariasPropias'
        }); 

        this.storeCuentasBancariasNoPropias = new Ext.data.JsonStore({    
            id:        0,
            url:       'datagateway\/combolist\/model\/VBancosCuentas\/m\/Base\/search\/Descripcion\/fetch\/NoEsPropia',
            storeId:   'CuentasBancariasNoPropias'
        }); 
        
        /* ---- COMPONENTES ---- */

        this.cabecera = new Ext.ux.form.ComboBox({
            displayField:   'desc',
            valueField:     'id',
            typeAhead:      true,
            fieldLabel:     'Encabezado',
            name:           'cabecera',
            value:          1,
            mode:           'local',
            forceSelection: true,
            triggerAction:  'all',
            selectOnFocus:  true,
            store:          this.arrCabecera
        });        

        this.bancos = new Ext.ux.form.ComboBox({
            anchor:         '95%',
            displayField:   'Descripcion',
            autoLoad:       false,
            selectOnFocus:  true,
            forceSelection: true,
            forceReload:    true,
            hiddenName:     'bancoSucursal',
            loadingText:    'Cargando...',
            lazyRender:     true,
            store:          this.storeBancos,
            typeAhead:      true,
            valueField:     'Id',
            pageSize:       50,
            editable:       true,
            autocomplete:   true,
            allowBlank:     true,
            allowNegative:  false,
            fieldLabel:     'Banco',
            name:           'bancoSucursal',
            displayFieldTpl:'{Banco_cdisplay}',
            tpl:            '<tpl for=\".\"><div class=x-combo-list-item><b>{Banco_cdisplay}<\/b> ( {Sucursal} )<\/div><\/tpl>'
        });

        this.cuentasBancariasDestino = new Ext.ux.form.ComboBox({
            anchor:         '95%',
            displayField:   'Descripcion',
            disabled: 		true,
            autoLoad:       true,
            selectOnFocus:  true,
            forceSelection: true,
            forceReload:    true,
            hiddenName:     'cuentaBancariaDestino',
            loadingText:    'Cargando...',
            lazyRender:     true,
            store:          this.storeCuentasBancarias,
            typeAhead:      true,
            valueField:     'CuentaBancariaId',
            pageSize:       50,
            editable:       true,
            autocomplete:   true,
            allowBlank:     true,
            allowNegative:  false,
            fieldLabel:     'Cuenta Destino',
            name:           'cuentaBancariaDestino',
            displayFieldTpl:'{Descripcion}',
            tpl:            '<tpl for=\".\"><div class=x-combo-list-item><b>{Descripcion}<\/b><i> de {RazonSocial}<\/i><\/div><\/tpl>'
        });

        this.cuentasBancariasPropias = new Ext.ux.form.ComboBox({
            anchor:         '95%',
            displayField:   'Descripcion',
            autoLoad:       true,
            selectOnFocus:  true,
            forceSelection: true,
            forceReload:    true,
            hiddenName:     'cuentaBancariaPropia',
            loadingText:    'Cargando...',
            lazyRender:     true,
            store:          this.storeCuentasBancariasPropias,
            typeAhead:      true,
            valueField:     'CuentaBancariaId',
            pageSize:       50,
            editable:       true,
            autocomplete:   true,
            allowBlank:     true,
            allowNegative:  false,
            fieldLabel:     'Cuenta Propia',
            name:           'cuentaBancariaPropia'
        });

        /*
        this.cuentaBancariaNoPropia = new Ext.ux.form.ComboBox({
            anchor:         '95%',
            displayField:   'Descripcion',
            autoLoad:       true,
            selectOnFocus:  true,
            forceSelection: true,
            forceReload:    true,
            hiddenName:     'cuentaBancariaNoPropia',
            loadingText:    'Cargando...',
            lazyRender:     true,
            store:          this.storeCuentasBancariasNoPropias,
            typeAhead:      true,
            valueField:     'CuentaBancariaId',
            pageSize:       50,
            editable:       true,
            autocomplete:   true,
            allowBlank:     true,
            allowNegative:  false,
            fieldLabel:     'Cuenta Bancaria',
            name:           'cuentaBancariaNoPropia',
            displayFieldTpl:'{Descripcion}',
            tpl:            '<tpl for=\".\"><div class=x-combo-list-item><b>{Descripcion}<\/b><i> de {RazonSocial}<\/i><\/div><\/tpl>'
        });
		*/

        this.chequeras = new Ext.ux.form.ComboBox({
            displayField:   'Chequera',
            autoLoad:       false,
            selectOnFocus:  true,
            forceSelection: true,
            forceReload:    true,
            hiddenName:     'chequera',
            loadingText:    'Cargando...',
            lazyRender:     true,
            store:          this.storeChequeras,
            typeAhead:      true,
            valueField:     'Id',
            pageSize:       50,
            editable:       true,
            autocomplete:   true,
            allowBlank:     true,
            allowNegative:  false,
            fieldLabel:     'Chequera',
            name:           'chequera',
            displayFieldTpl:'{NumeroDeChequera}',
            tpl:            '<tpl for=\".\"><div class=x-combo-list-item><h3>{NumeroDeChequera}<\/h3><\/div><\/tpl>'
        });
 
        /*
        this.ordenDePago = new Ext.ux.form.ComboBox({
            displayField:   'Orden de Pago',
            anchor:         '95%',
            autoLoad:       false,
            selectOnFocus:  true,
            forceSelection: true,
            forceReload:    true,
            hiddenName:     'OrdenDePago',
            loadingText:    'Cargando...',
            lazyRender:     true,
            store:          this.storeOrdenesDePagos,
            typeAhead:      true,
            valueField:     'Comprobante',
            pageSize:       50,
            editable:       true,
            autocomplete:   true,
            allowBlank:     true,
            allowNegative:  false,
            fieldLabel:     'Orden de Pago',
            name:           'ordenDePago',
            displayFieldTpl:'{Numero} a {Persona_cdisplay}',
            tpl:            '<tpl for=\".\"><div class=x-combo-list-item><h3>{Numero} a {Persona_cdisplay}<\/h3><\/div><\/tpl>'
        });
        */

        this.estado = new Ext.ux.form.ComboBox({
            displayField:   'Estado',
            autoLoad:       false,
            selectOnFocus:  true,
            forceSelection: true,
            forceReload:    true,
            hiddenName:     'estado',
            loadingText:    'Cargando...',
            lazyRender:     true,
            store:          this.storeEstados,
            typeAhead:      true,
            valueField:     'Id',
            pageSize:       50,
            editable:       true,
            autocomplete:   true,
            allowBlank:     true,
            allowNegative:  false,
            fieldLabel:     'Estados',
            name:           'estado',
            displayFieldTpl:'{Descripcion}',
            tpl:            '<tpl for=\".\"><div class=x-combo-list-item><h3>{Descripcion}<\/h3><\/div><\/tpl>'
        });

        this.razonSocial = new Ext.ux.form.ComboBox({
            ref:            '../persona',
            xtype:          'xcombo',
            anchor:         '95%',
            displayField:   'RazonSocial',
            autoLoad:       false,
            selectOnFocus:  true,
            forceSelection: true,
            forceReload:    true,
            hiddenName:     'razonSocial',
            loadingText:    'Cargando...',
            lazyRender:     true,
            store:          this.storePersonas,
            typeAhead:      true,
            valueField:     'Id',
            pageSize:       50,
            editable:       true,
            autocomplete:   true,
            allowNegative:  false,
            fieldLabel:     'Razon Social',
            name:           'razonSocial',
            displayFieldTpl:'{RazonSocial}',
            tpl:            '<tpl for=\".\"><div class=x-combo-list-item><h3>{RazonSocial}<\/h3><\i> ---[ {Denominacion} ]---<\/i><\/div><\/tpl>'
        });

        this.ordenCombo = new Ext.ux.form.ComboBox({
            displayField:   'desc',
            valueField:     'id',
            typeAhead:      true,
            fieldLabel:     'Orden',
            hiddenName:     'orden',
            name:           'orden',
            mode:           'local',
            value:          1,
            forceSelection: true,
            triggerAction:  'all',
            selectOnFocus:  true,
            store: new Ext.data.ArrayStore({
                fields: ['desc', 'id'],
                data : [
                    ['F. Emision', '1'],
                    ['F. Vencimiento', '4'],
                    ['Banco', '2'],
                    ['Nro. Cheque', '3'],
                    ['Razon Social', '5']
                ]
            })
        });

        this.ordenSentidos = new Ext.ux.form.ComboBox({
            displayField:   'desc',
            valueField:     'id',
            typeAhead:      true,
            fieldLabel:     'OrdenSentido',
            hiddenName:     'ordenSentido',
            name:           'ordenSentido',
            mode:           'local',
            value:          0,
            forceSelection: true,
            triggerAction:  'all',
            selectOnFocus:  true,
            store: new Ext.data.ArrayStore({
                fields: ['desc', 'id'],
                data : [
                    ['Ascendente',  '0'],
                    ['Descendente', '1']
                ]
            })
        });

        /*
        this.tipoEmisorCombo = new Ext.ux.form.ComboBox({
            displayField:   'desc',
            valueField:     'id',
            typeAhead:      true,
            fieldLabel:     'Tipo',
            name:           'tipoEmisor',
            mode:           'local',
            forceSelection: true,
            triggerAction:  'all',
            selectOnFocus:  true,
            store: new Ext.data.ArrayStore({
                fields: ['desc', 'id'],
                data : [
                    ['Propio', '1'],
                    ['De Tercero', '2']
                ]
            })
        });
        */

        this.cobrado = new Ext.ux.form.ComboBox({
            displayField:   'desc',
            valueField:     'id',
            typeAhead:      true,
            fieldLabel:     'Cobrado',
            name:           'cobrado',
            mode:           'local',
            forceSelection: true,
            triggerAction:  'all',
            selectOnFocus:  true,
            store:          this.arrSiNo
        });

        this.impreso = new Ext.ux.form.ComboBox({
            displayField:   'desc',
            valueField:     'id',
            typeAhead:      true,
            fieldLabel:     'Impreso',
            name:           'impreso',
            mode:           'local',
            forceSelection: true,
            triggerAction:  'all',
            selectOnFocus:  true,
            store:          this.arrSiNo
        });

        this.noAlaOrden = new Ext.ux.form.ComboBox({
            displayField:   'desc',
            valueField:     'id',
            typeAhead:      true,
            fieldLabel:     'No a la Orden',
            name:           'noAlaOrden',
            mode:           'local',
            forceSelection: true,
            triggerAction:  'all',
            selectOnFocus:  true,
            store:          this.arrSiNo
        });

        this.cruzado = new Ext.ux.form.ComboBox({
            displayField:   'desc',
            valueField:     'id',
            typeAhead:      true,
            fieldLabel:     'Cruzado',
            name:           'cruzado',
            mode:           'local',
            forceSelection: true,
            triggerAction:  'all',
            selectOnFocus:  true,
            store:          this.arrSiNo
        });        

        this.emisionDesde = new Ext.ux.form.XDateField({
            ref:            '../emisionDesde',
            format:         'd/m/Y',
            dateFormat:     'Y-m-d',
            name:           'emisionDesde',
            fieldLabel :    'Desde'
        });

        this.emisionHasta = new Ext.ux.form.XDateField({
            ref:            '../emisionHasta',
            format:         'd/m/Y',
            dateFormat:     'Y-m-d',
            name:           'emisionHasta',
            fieldLabel :    'Hasta'
        });

        this.vencimientoDesde = new Ext.ux.form.XDateField({
            ref:            '../vencimientoDesde',
            format:         'd/m/Y',
            dateFormat:     'Y-m-d',
            name:           'vencimientoDesde',
            fieldLabel :    'Desde'
        });

        this.vencimientoHasta = new Ext.ux.form.XDateField({
            ref:            '../vencimientoHasta',
            format:         'd/m/Y',
            dateFormat:     'Y-m-d',
            name:           'vencimientoHasta',
            fieldLabel :    'Hasta'
        });

        this.cobroDesde = new Ext.ux.form.XDateField({
            ref:            '../cobroDesde',
            format:         'd/m/Y',
            dateFormat:     'Y-m-d',
            name:           'cobroDesde',
            fieldLabel :    'Desde'
        });

        this.cobroHasta = new Ext.ux.form.XDateField({
            ref:            '../cobroHasta',
            format:         'd/m/Y',
            dateFormat:     'Y-m-d',
            name:           'cobroHasta',
            fieldLabel :    'Hasta'
        });

        this.terceroEmisor = new Ext.ComponentMgr.create({
        	xtype: 			'textfield',
			fieldLabel:     'Tercero Emisor',
			name :          'terceroEmisor',
            anchor:         '95%',
            disabled: 		true
        });

        this.cuitTerceroEmisor = new Ext.ComponentMgr.create({
        	xtype: 			'numberfield',
			fieldLabel:     'CUIT T. Emisor',
			name :          'cuitTerceroEmisor',
			mask: 			{ text: '99-99.999.999-9'},
            anchor:         '95%',
            disabled: 		true
        });

        this.pagueseA = new Ext.ComponentMgr.create({
        	xtype: 			'textfield',
			fieldLabel:     'Paguese A',
			name :          'pagueseA',
            anchor:         '95%'
        });

        this.ordenesDePagos = new Ext.ComponentMgr.create({
        	xtype: 			'numberfield',
			fieldLabel:     'Orden de Pago',
			name :          'ordenDePago',
            anchor:         '95%'
        });

        this.recibos = new Ext.ComponentMgr.create({
        	xtype: 			'numberfield',
			fieldLabel:     'Recibo',
			name :          'recibo',
            anchor:         '95%'
        });        


        this.tiposCheques = new Ext.ComponentMgr.create({
            xtype: 			'radiogroup',
            fieldLabel:     'Tipo',
            //name :          'tipo',
            listeners: {
                'change': {
                    'fn': function (rg, c){
                        if (c.boxLabel == 'Propios') {
                            /* Muestro */
                            this.cuentasBancariasPropias.enable();
                            this.chequeras.enable();
                            this.pagueseA.enable();
                            this.cobrado.enable();
                            this.impreso.enable();
                            this.cobroDesde.enable();
                            this.cobroHasta.enable();
                            /* Oculto */
                            this.cuentasBancariasDestino.disable();	this.cuentasBancariasDestino.clearValue();
                            this.terceroEmisor.disable();	        this.terceroEmisor.setValue('');
							this.cuitTerceroEmisor.disable();		this.cuitTerceroEmisor.setValue('');
                            // this.loc.hide();
                            // this.loc.clearValue();
                        } else {
                        	/* Muestro */
                            this.cuentasBancariasDestino.enable();
                            this.terceroEmisor.enable();
							this.cuitTerceroEmisor.enable();                                        	
                            //this.loc.show();
                            /* Oculto */
                            this.cuentasBancariasPropias.disable();	this.cuentasBancariasPropias.clearValue();
                            this.chequeras.disable();				this.chequeras.clearValue();
                            this.pagueseA.disable();				this.pagueseA.setValue('');
                            this.cobrado.disable();					this.cobrado.clearValue();
                            this.impreso.disable();					this.impreso.clearValue();
                            this.cobroDesde.disable();				this.cobroDesde.setValue('');
                            this.cobroHasta.disable();				this.cobroHasta.setValue('');
                        }
                    },
                    'scope':this
                }
            },
            width: 150,
            items: [
                { boxLabel: 'Propios', name: 'propios', inputValue: 'Propios', checked: true },
                { boxLabel: 'Terceros', name: 'propios', inputValue: 'Terceros' }
            ]
        });

        defaultWinCfg = {
            id:             this.id+'-win',
            title:          this.title,
            iconCls:        'icon-grid',
            border:         false,
            shim:           false,
            resizable:      false,
            animCollapse:   false,
            layout:         'fit',
            width:          750,
            height:         530,
            items: [
                this.renderWindowContent()
            ]
        };
        return app.desktop.createWindow(defaultWinCfg);
    },
	
	listadoChequesPropios: function() {
        var that = this;
        return {
            xtype: 'form',
            url :   '/Base/ReporteCheques/verreporte',
            layout: 'form',
            border: false,
            bodyStyle: 'padding:10px',
            defaults: {
                border: false
            },
            items: [
                {
                    xtype:   'fieldset',
                    title:   '  Datos Generales  ',
                    border:  true,                    
                    items: [
						this.tiposCheques,                   
                    	this.bancos,
                        {
                            xtype:          'compositefield',
                            fieldLabel:     'Cheque Nro.',
                            items: [
                                {xtype: 'displayfield', value: 'Desde'},{name : 'numeroDesde', xtype: 'numberfield'},
                                {xtype: 'displayfield', value: 'Hasta'},{name : 'numeroHasta', xtype: 'numberfield'},
                            ]
                        },                    	
                        this.razonSocial,
                        {
                            xtype:          'compositefield',
                            fieldLabel:     'Monto',
                            items: [
                                {xtype: 'displayfield', value: 'Desde'},{name : 'montoDesde', xtype: 'numberfield'},
                                {xtype: 'displayfield', value: 'Hasta'},{name : 'montoHasta', xtype: 'numberfield'},
                            ]
                        },
						this.noAlaOrden,
						this.cruzado,
                        {
                            xtype:          'compositefield',
                            fieldLabel:     'Comprobante',
                            items: [
                                {xtype: 'displayfield', value: 'Orden de Pago'},this.ordenesDePagos,
                                {xtype: 'displayfield', value: 'Recibo'},this.recibos
                            ]
                        },						
                    ]
                },
                {
                xtype: 'tabpanel',
                activeTab: 0,
                items: [ 
                    {
                        title: ' Propios',
                        items: [{   
                                    xtype:   'fieldset',
                                    title:   '  Propios  ',
                                    border:  true,
                                    items: [
                                        this.cuentasBancariasPropias,
                                        this.chequeras,
                                        this.pagueseA
                                    ]
                                }
                        ]
                    },               
                    {
                        title: ' Terceros',
                        items: [{
                                    xtype:   'fieldset',
                                    title:   '  Terceros  ',
                                    border:  true,                    
                                    items: [
                                        this.cuentasBancariasDestino,
                                        this.terceroEmisor,
                                        this.cuitTerceroEmisor
                                    ]
                                }
                        ]
                    },               
                    {
                        title: ' Fechas',
                        items: [{
                                    xtype:   'fieldset',
                                    title:   '  Fechas  ',
                                    border:  true,
                                    items: [{
                                                xtype:      'compositefield',
                                                fieldLabel: 'Emision',
                                                anchor:     '95%',
                                                items: [
                                                    {xtype: 'displayfield', value: 'Desde'  }, this.emisionDesde,
                                                    {xtype: 'displayfield', value: 'Hasta'  }, this.emisionHasta,
                                                ]
                                            },
                                            {
                                                xtype:      'compositefield',
                                                fieldLabel: 'Vencimiento',
                                                anchor:     '95%',
                                                items: [
                                                    {xtype: 'displayfield', value: 'Desde'  }, this.vencimientoDesde,
                                                    {xtype: 'displayfield', value: 'Hasta'  }, this.vencimientoHasta,
                                                ]
                                            },
                                            {
                                                xtype:      'compositefield',
                                                fieldLabel: 'Cobro',
                                                anchor:     '95%',
                                                items: [
                                                    {xtype: 'displayfield', value: 'Desde'  }, this.cobroDesde,
                                                    {xtype: 'displayfield', value: 'Hasta'  }, this.cobroHasta,
                                                ]
                                            }]
                                }
                        ]
                    },
                    {
                        title: ' Situacion',
                        items: [{
                                    xtype:   'fieldset',
                                    title:   '  Situacion  ',
                                    border:  true,                    
                                    items: [
                                        this.cobrado,
                                        this.impreso,
                                        this.estado
                                    ]
                                }
                        ]
                    },
                    {
                        title: ' Formatos',
                        items: [{
                                    xtype:   'fieldset',
                                    title:   'Formato',
                                    border:  true,
                                    items: [
                                            this.cabecera,
                                            {
                                                xtype:          'radiogroup',
                                                fieldLabel:     'Formato',
                                                items: [
                                                    { boxLabel: 'PDF', name: 'formato', inputValue: 'pdf', checked: true },
                                                    { boxLabel: 'Excel', name: 'formato', inputValue: 'xls' },
                                                    { boxLabel: 'CSV', name: 'formato', inputValue: 'csv' },
                                                ]
                                            },
                                            {
                                                xtype:      'compositefield',
                                                fieldLabel: 'Orden',
                                                anchor:     '95%',
                                                items: [
                                                    this.ordenCombo,
                                                    this.ordenSentidos
                                                ]
                                            }
                                    ]
                                }
                        ]
                    }
                ]                
                }





            ],
            buttons:[
                {
                    text:  'Ver Reporte',
                    handler: function () {
                       values           = this.ownerCt.ownerCt.getForm().getValues();
                       var params       = '';
                       var filtro       = '';
                       var separador    = '';
                       var separadorAUsar = ' | ';

                        // general
                        if (values.propios != '') {
                        	params += '/tipo/'+values.propios;
							filtro += "Tipo: " + values.propios;
                            separador = separadorAUsar;

                            if (values.propios == 'Propios') {
                                // Activos solo cuando son propios
                                if (values.cuentaBancariaPropia && values.cuentaBancariaPropia != '' && values.cuentaBancariaPropia != 'undefined') {
                                    params += '/cuentaBancariaPropia/'+values.cuentaBancariaPropia;
                                    filtro += separador + "Cuenta bancaria propia: " + that.cuentasBancariasPropias.getRawValue();
                                    separador = separadorAUsar;                              
                                }
                                if (values.chequera && values.chequera != '') {
                                    params += '/chequera/'+values.chequera;
                                    filtro += separador + "Chequera: " + that.chequeras.getRawValue();
                                    separador = separadorAUsar;                            
                                }
                                if (values.pagueseA && values.pagueseA != '') {
                                    params += '/pagueseA/'+values.pagueseA;
                                    filtro += separador + "Paguese a: " + values.pagueseA;
                                    separador = separadorAUsar;                              
                                }
                                if (values.cobroDesde && values.cobroDesde != 'undefined') {
                                    params += '/cobroDesde/'+values.cobroDesde;
                                    filtro += separador + "F. Cobro >= " + values.cobroDesde;
                                    separador = separadorAUsar;                             
                                }
                                if (values.cobroHasta && values.cobroHasta != 'undefined') {
                                    params += '/cobroHasta/'+values.cobroHasta;
                                    filtro += separador + "F. Cobro <= " + values.cobroHasta;
                                    separador = separadorAUsar;                             
                                }
                                if (values.cobrado && values.cobrado != '' && values.cobrado != '3') {
                                    params += '/cobrado/'+values.cobrado;
                                    filtro += separador + "Cobrado: " + that.cobrado.getRawValue();
                                    separador = separadorAUsar;                             
                                }  
                                if (values.impreso && values.impreso != '' && values.impreso != '3') {
                                    params += '/impreso/'+values.impreso;
                                    filtro += separador + "Impreso: " + that.impreso.getRawValue();
                                    separador = separadorAUsar;                              
                                }
                            } else {
                                // Activos solo para terceros
                                if (values.cuentaBancariaDestino && values.cuentaBancariaDestino != '' && values.cuentaBancariaDestino != 'undefined') {
                                    params += '/cuentaBancariaDestino/'+values.cuentaBancariaDestino;
                                    filtro += separador + "Cuenta bancaria destino: " + that.cuentasBancariasDestino.getRawValue();
                                    separador = separadorAUsar;                              
                                }                       
                                if (values.terceroEmisor && values.terceroEmisor != '') {
                                    params += '/terceroEmisor/'+values.terceroEmisor;
                                    filtro += separador + "Tercero emisor: " + values.terceroEmisor;
                                    separador = separadorAUsar;                              
                                }
                                if (values.cuitTerceroEmisor && values.cuitTerceroEmisor != '') {
                                    params += '/cuitTerceroEmisor/'+values.cuitTerceroEmisor;
                                    filtro += separador + "CUIT T. emisor: " + values.cuitTerceroEmisor;
                                    separador = separadorAUsar;                              
                                } 
                            }
                        }       
                        if (values.bancoSucursal !='') {
                            params += '/bancoSucursal/' + values.bancoSucursal;
                            filtro += "Banco: " + that.bancos.getRawValue();
                            separador = separadorAUsar; 
                        }    
                        if (values.numeroDesde != '') {
                            params += '/numeroDesde/'+values.numeroDesde;
                            filtro += separador + "Numero >= " + values.numeroDesde;
                            separador = separadorAUsar;
                        }       
                        if (values.numeroHasta != '') {
                            params += '/numeroHasta/'+values.numeroHasta;
                            filtro += separador + "Numero <= " + values.numeroHasta;
                            separador = separadorAUsar;                            
                        }       
                        if (values.razonSocial != '') {
                            params += '/razonSocial/'+values.razonSocial;
                            filtro += separador + "Proveedor: " + that.razonSocial.getRawValue();
                            separador = separadorAUsar;                                
                        }       
     
                        if (values.noAlaOrden != '' && values.noAlaOrden != '3') {
                            params += '/noAlaOrden/'+values.noAlaOrden;
                            filtro += separador + "N.O.: " + that.noAlaOrden.getRawValue();
                            separador = separadorAUsar;                              
                        }
                        if (values.cruzado != '' && values.cruzado != '3') {
                            params += '/cruzado/'+values.cruzado;
                            filtro += separador + "Cruzado: " + that.cruzado.getRawValue();
                            separador = separadorAUsar;                              
                        }  
                        if (values.montoDesde != '') {
                            params += '/montoDesde/'+values.montoDesde;
                            filtro += separador + "Monto >= " + values.montoDesde;
                            separador = separadorAUsar;
                        }        
                        if (values.montoHasta != '') {
                            params += '/montoHasta/'+values.montoHasta;
                            filtro += separador + "Monto <= " + values.montoHasta;
                            separador = separadorAUsar;                            
                        }        
                        if (values.ordenDePago != '') {
                            params += '/ordenDePago/'+values.ordenDePago;
                            filtro += separador + "Orden de pago: " + values.ordenDePago;
                            separador = separadorAUsar;                             
                        }
                        if (values.recibo != '') {
                            params += '/recibo/'+values.recibo;
                            filtro += separador + "Recibo: " + values.recibo;
                            separador = separadorAUsar;                             
                        }                             
                        // fechas
                        if (values.emisionDesde != 'undefined') {
                            params += '/emisionDesde/'+values.emisionDesde;
                            //var dt = new Date(values.emisionDesde);
                            //filtro += separador + "Fecha_Emision_<= " + dt.format('d-m-Y'); --> me resta un dia de esta forma
                            filtro += separador + "F. Emision >= " + values.emisionDesde;
                            separador = separadorAUsar;                                 
                        }         
                        if (values.emisionHasta != 'undefined') {
                            params += '/emisionHasta/'+values.emisionHasta;
                            filtro += separador + "F. Emision <= " + values.emisionHasta;
                            separador = separadorAUsar;                             
                        }        
                        if (values.vencimientoDesde != 'undefined') {
                            params += '/vencimientoDesde/'+values.vencimientoDesde;
                            filtro += separador + "F. Vencimiento >= " + values.vencimientoDesde;
                            separador = separadorAUsar;                                
                        }     
                        if (values.vencimientoHasta != 'undefined') {
                            params += '/vencimientoHasta/'+values.vencimientoHasta;
                            filtro += separador + "F. Vencimiento <= " + values.vencimientoHasta;
                            separador = separadorAUsar;                             
                        }     
                        // estado
                        if (values.estado != '') {
                            params += '/estado/'+values.estado;
                            filtro += separador + "Estado: " + that.estado.getRawValue();
                            separador = separadorAUsar;                              
                        }
                        // formato
                        if (values.orden != '')         params += '/ordenCombo/'+values.orden;
                        if (values.ordenSentido != '')  params += '/ordenSentido/'+values.ordenSentido;
                        if (values.cabecera != '')      params += '/cabecera/'+values.cabecera;
                        if (values.formato != '')       params += '/formato/'+values.formato;
                        
                        if (filtro != '')                   params += '/filtro/'+filtro;
                        // if (values.tipoEmisor != '')     params += '/tipo/'+values.tipoEmisor;

                        app.publish('/desktop/modules/js/commonApps/showUrl.js', {
                           action: 'launch',
                           url: '/Base/ReporteCheques/verreporte'+params,
                           width: 900,
                           height: 500,
                           title: 'Cheques'
                        });
                    }
                }
            ]
        };
	},
	
	calendarioCheques: function() {
	    var that = this;
        return {
	        xtype: 'form',
	        id: 'asd',
	        border: false,
            bodyStyle: 'padding:10px',
	        defaults: {
	            border: false
	        },
	        items: [
                {
                   xtype: 'xdatefield',
                   ref: '../desde',
                   format: 'd/m/Y',
                   dateFormat:'Y-m-d',
                   name: 'desde',
                   fieldLabel : 'Desde'
                },
                {
                   xtype: 'xdatefield',
                   ref: '../hasta',
                   format: 'd/m/Y',
                   dateFormat:'Y-m-d',
                   name: 'hasta',
                   fieldLabel : 'Hasta'
                }
	        ],
            buttons:[
                {
                    text:  'Ver Calendario',
                    handler: function () {
                        var values = this.findParentByType('form').getForm().getValues(); 
                        
                        if (values.desde == 'undefined' || !values.hasta == 'undefined') {
                            Ext.Msg.alert('Atencion', 'Debe completar todos los campos');
                            return;
                        }
                        var desde = new Date(values.desde);
                        var hasta = new Date(values.hasta).add(Date.DAY, 1).add(Date.SECOND, -1);
                        
                        if (desde > hasta) {
                            Ext.Msg.alert('Atencion', 'Rango de fecha invalido');
                            return;
                        }
                        
                        var startDate = desde.format('d-m-Y H:i:s');
                        var endDate = hasta.format('d-m-Y H:i:s');
                        
                        app.publish('/desktop/modules/Window/birtreporter', {
                            action: 'launch',
                            template: 'CalendarReport',
                            id: 1,
                            params: [
                                {
                                    name: 'pStartDate',
                                    type: 'datetime',
                                    value: startDate
                                },
                                {
                                    name: 'pEndDate',
                                    type: 'datetime',
                                    value: endDate
                                }
                            ],
                            width: 900,
                            height: 500,
                            title: 'Cheques',
                            output: 'pdf'
                        });
                    }
                }
            ]
	    }
	},
	
    renderWindowContent: function ()
    {
        return {
            xtype: 'tabpanel',
            activeTab: 0,
            items: [
                {
                    title: 'Listado Cheques Propios',
                    items: [ this.listadoChequesPropios() ]
                },               
                {
                    title: 'Calendario',
                    items: [ this.calendarioCheques() ]
                }
            ]
        }
    }
	
});

new Apps.<?=$this->name?>();
