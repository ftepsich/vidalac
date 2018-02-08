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
       
        this.cabecera = new Ext.ux.form.ComboBox({
            displayField:'desc',
            valueField: 'id',
            typeAhead: true,
            fieldLabel: 'Encabezado',
            name: 'cabecera',
            //anchor : '100%',
            value:1,
            mode: 'local',
            forceSelection: true,
            triggerAction: 'all',
            selectOnFocus: true,
            store: new Ext.data.ArrayStore({
                fields: ['desc', 'id'],
                data : [
                    ['Interno (simple)', '1'],
                    ['Completo (detallado)', '2']
                ]
            })
        });


        defaultWinCfg = {
            id: this.id+'-win',
            title: this.title,
            iconCls: 'icon-grid',
            border:  false,
            shim: false,
            resizable:false,
            animCollapse: false,
            layout: 'fit',
            width: 460,
            height:300,
            items: [
                this.renderWindowContent()
            ]
        };
        return app.desktop.createWindow(defaultWinCfg);
    },
	
	renderWindowContent: function() {
            
        mdate = new Date();
        return {
            xtype: 'form',
            url : '/Base/ReporteMontosDeVentasxLocalidad/verreporte',
            layout: 'form',
            border: false,
            bodyStyle: 'padding:10px',
            defaults: {
                border: false
            },
            items: [
                {
                 id: 'resumenVentasMensualMes-Id',
                                xtype: 'combo',
                                fieldLabel: 'Mes',
								name:'mes',
                                mode: 'local',
                                editable: false,
                                width: 160,
                                valueField: 'Mes',
                                forceSelection: false,
                                displayField: 'Descripcion',
                                triggerAction: 'all',
                                value: <?= date('m') ?>,
                                store: new Ext.data.ArrayStore({
                                    id: 0,
                                    fields: [ 'Mes', 'Descripcion' ],
                                    data: [
                                        [1, '01 - Enero'],      [2, '02 - Febrero'],    [3, '03 - Marzo'], 
                                        [4, '04 - Abril'],      [5, '05 - Mayo'],       [6, '06 - Junio'],
                                        [7, '07 - Julio'],      [8, '08 - Agosto'],     [9, '09 - Septiembre'], 
                                        [10, '10 - Octubre'],   [11, '11 - Noviembre'], [12, '12 - Diciembre']
                                    ]
								})
                }
				
				,{
                    id: 'resumenVentasMensualAnio-Id',
                    xtype: 'numberfield',
					name:'anio',
					fieldLabel: 'Año',
					width: 60,
					allowDecimals: false,
					allowNegative: false,
					minValue: 1900,
					maxLength: 4,
					value: "<?=date('Y')?>"
                },
                this.cabecera,
                {
                    xtype: 'radiogroup',
                    fieldLabel: 'Formato',
                    width: 150,
                    items: [
                        { boxLabel: 'PDF', name: 'formato', inputValue: 'pdf', checked: true },
                        { boxLabel: 'Excel', name: 'formato', inputValue: 'xls' }
                    ]
                }
            ],
            buttons:[
                {
                    text:  'Ver Reporte',
                    handler: function () {
                       
								var mes = Ext.getCmp('resumenVentasMensualMes-Id').getValue();
								var anio = Ext.getCmp('resumenVentasMensualAnio-Id').getValue();
                                    
                                if (!mes || !anio) {
                                        Ext.Msg.alert('Advertencia', 'Debe completar el mes y año');
                                        return;
                                }
                  							
					values = this.ownerCt.ownerCt.getForm().getValues();
                    //die(values)
					var  params = '';

                        if (values.anio != 'undefined') params += '/anio/'+values.anio;
                        if (values.mes  != 'undefined') params += '/mes/'+values.mes;
                        if (values.cabecera != '')      params += '/cabecera/'+values.cabecera;
                        if (values.formato != '') {     params += '/formato/'+values.formato;
                        }
                      
                       app.publish('/desktop/modules/js/commonApps/showUrl.js', {
                           action: 'launch',
                           url: '/Base/ReporteMontosDeVentasxLocalidad/verreporte'+params,
                           width: 900,
                           height: 500,
                           title: 'Clientes'
						});
                    }
                }
            ]
        };
	}
	
	
});

new Apps.<?=$this->name?>();
