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
url : '/Facturacion/ReporteOrdenesDePagos/verreporte',
layout: 'form',
border: false,
bodyStyle: 'padding:10px',
defaults: {
border: false
},
items: [
{
xtype: 'compositefield',
fieldLabel: 'Fecha',
items: [
{xtype: 'displayfield', value: 'Desde:'},{name : 'fechaDesde', xtype: 'xdatefield',format: 'd/m/Y', dateFormat:'Y-m-d'},
{xtype: 'displayfield', value: 'Hasta:'},{name : 'fechaHasta', xtype: 'xdatefield',format: 'd/m/Y', dateFormat:'Y-m-d'},
]
}, 
{
    fieldLabel: 'Proveedor',
    ref: '../persona',
    xtype:"xcombo",
    anchor: '96%',
    displayField: 'RazonSocial',
    name: 'Persona',
    typeAhead:true,
    valueField: 'Id',
    allowBlank: true,
    msgTarget: 'under',
    triggerAction: 'all',
    autoLoad:true,
    selectOnFocus:true,
    forceSelection:true,
    forceReload:true,
    hiddenName:"Persona",
    loadingText:"Cargando...",
    lazyRender:true,
    store:new Ext.data.JsonStore({
       id:0,
       url:"datagateway\/combolist\/model\/Proveedores\/m\/Base\/search\/RazonSocial",
       storeId:"ProveedoresStore"
    }),
pageSize:20,
editable:true,
autocomplete:true
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

if (values.fechaDesde == 'undefined' || values.fechaHasta == 'undefined') {
Ext.Msg.alert('Atencion', 'Debe completar las fechas Desde/Hasta.');
return;
} else {
params += '/fechadesde/'+values.fechaDesde;
if (values.hasta != 'undefined') {
params += '/fechahasta/'+values.fechaHasta;
}
}

if (values.Persona != '') {
params += '/persona/'+values.Persona;
}

if (values.formato != '') {
params += '/formato/'+values.formato;
}

app.publish('/desktop/modules/js/commonApps/showUrl.js', {
action: 'launch',
url: '/Facturacion/ReporteOrdenesDePagos/verreporte'+params,
width: 900,
height: 500,
title: 'Reporte Ordenes de Pago'
});
}
}
]
};
}
    
});

new Apps.<?=$this->name?>();

