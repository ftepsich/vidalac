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

defaultWinCfg = {
id: this.id+'-win',
title: this.title,
iconCls: 'icon-grid',
border: false,
shim: false,
resizable:false,
animCollapse: false,
layout: 'fit',
width: 600,
height:200,

items: [
this.renderWindowContent()
]
};

return app.desktop.createWindow(defaultWinCfg);
},
    
renderWindowContent: function ()
{
return {
xtype: 'form',
url : '/Facturacion/ReporteOrdenesDePagosCuenta/verreporte',
layout: 'form',
border: false,
bodyStyle: 'padding:10px',
defaults: {
border: false
},
items: [
{
    xtype:          'compositefield',
    fieldLabel:     'Libro Iva',
    items: [
        {xtype: 'displayfield', value: 'Desde:'},{displayField: 'Descripcion',name : 'libroivadesde',valueField: 'Id', xtype: 'xcombo', selectOnFocus: true, forceSelection: true, forceReload: true,
        hiddenName: "libroIvaDesde",loadingText: "Cargando...", msgTarget: 'under',width: 116,
        triggerAction: 'all', store: new Ext.data.JsonStore({
            id: 0,
            url:"datagateway\/combolist\/model\/LibrosIVA/m\/Contable\/search\/Descripcion\/sort\/Id\/dir\/desc",
            storeId: "LibroIVAStore"
        })},
        {xtype: 'displayfield', value: 'Hasta:'},{ displayField: 'Descripcion',name:'libroivahasta', valueField: 'Id',xtype: 'xcombo',selectOnFocus: true, forceSelection: true, forceReload: true,
        hiddenName: "libroIvaHasta",loadingText: "Cargando...", msgTarget: 'under',width: 116,

        triggerAction: 'all',store: new Ext.data.JsonStore({
            id: 0,
            url:"datagateway\/combolist\/model\/LibrosIVA/m\/Contable\/search\/Descripcion\/sort\/Id\/dir\/desc",
            storeId: "LibroIVAStore"
        })},
    ]
},
{
    typeAhead: true,
    xtype: 'xcombo',
    fieldLabel: 'Cuenta',
    anchor: '96%',
    displayField: 'Descripcion',
    name: 'Cuenta',
    valueField: 'Id',
    selectOnFocus: true,
    forceSelection: true,
    forceReload: true,
    hiddenName: "Cuenta",
    loadingText: "Cargando...",
    msgTarget: 'under',
    triggerAction: 'all',
    store: new Ext.data.JsonStore({
        id: 0,
        url: "datagateway\/combolist\/model\/PlanesDeCuentas/m\/Contable\/search\/Descripcion\/sort\/Descripcion\/dir\/asc",
        storeId: "PlanDeCuentaStore"
    }),
    editable: true,
    autocomplete: true

},
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
text: 'Ver Reporte',
handler: function () {
values = this.ownerCt.ownerCt.getForm().getValues();
var params = '';

if (values.libroIvaDesde == 'undefined'  || values.libroIvaHasta == 'undefined' ) {
Ext.Msg.alert('Atencion', 'Debe seleccionar un periodo Libro IVA Desde/Hasta');
return;
} else {
params += '/libroivadesde/'+values.libroIvaDesde;
params += '/libroivahasta/'+values.libroIvaHasta;
}

if (values.Cuenta == 'undefined') {
Ext.Msg.alert('Atencion', 'Debe seleccionar una Cuenta.');
return;
} else {
params += '/cuenta/'+values.Cuenta;
}

if (values.formato != '') {
params += '/formato/'+values.formato;
}

app.publish('/desktop/modules/js/commonApps/showUrl.js', {
action: 'launch',
url: '/Facturacion/ReporteOrdenesDePagosCuenta/verreporte'+params,
width: 900,
height: 500,
title: 'Reporte Ordenes de Pagos (Plan de Cuenta)'
});
}
}
]
};
}
    
});

new Apps.<?=$this->name?>();

