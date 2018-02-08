/**
 * Templates genericos para campos del formulario
 */

RadTemplates = {};

RadTemplates.articulo = new Ext.XTemplate(
        '<tpl for=".">',
                '<div class="x-combo-list-item">',
                        '<h4>{Codigo}: {Descripcion}</h4>',
                        'Marca: <i>{Marca_cdisplay}</i>&nbsp;&nbsp; - &nbsp; <i>{IVA_cdisplay}</i>',
                '</div>',
        '</tpl>'
);

RadTemplates.articuloVersion = new Ext.XTemplate(
        '<tpl for=".">',
                '<div class="x-combo-list-item x-combo-detalle">',
                        '<h4><span class="x-combo-span-right">{ArticulosCodigo}</span>{Articulo_cdisplay}</h4>',
                        '{Descripcion}',
                '</div>',
        '</tpl>'
);

RadTemplates.articuloAlmacenes = new Ext.XTemplate(
        '<tpl for=".">',
                '<div class="x-combo-list-item">',
                        '<h4>{Codigo}: {Descripcion}</h4>',
                        'Marca: <i>{Marca_cdisplay}</i>',
                        '<tpl if="EsParaCompra = 1">&nbsp;&nbsp;- <i>Se Compra</i></tpl>',
                        '<tpl if="EsParaVenta = 1">&nbsp;&nbsp;- <i>Se Vende</i></tpl>',
                        '<tpl if="EsInsumo = 1">&nbsp;&nbsp;- <i>Es Insumo</i></tpl>',
                        '<tpl if="EsProducido = 1">&nbsp;&nbsp;- <i>Es Producido</i></tpl>',
                '</div>',
        '</tpl>'
);


RadTemplates.chequeras = new Ext.XTemplate(
        '<tpl for=".">',
                '<div class="x-combo-list-item">',
                        '<h4>Serie {Serie} / Chequera {NumeroDeChequera}</h4>',
                        '<div>{TipoDeCuenta} {CuentaBancaria_cdisplay}</div>',
                        '<div>{BancosSucursalesDescripcion}</div>',
                '</div>',
        '</tpl>'
);

RadTemplates.cuentaBancaria = new Ext.XTemplate(
        '<tpl for=".">',
                '<div class="x-combo-list-item">',
                        '<div>{TipoDeCuenta_cdisplay} : {BancoSucursal_cdisplay} : {Numero}</div>',
                '</div>',
        '</tpl>'
);
//CONCAT( `TC`.`Codigo` ,' : ' ,`B`.`Descripcion` ,' : ' ,`CB`.`Numero` ) AS `Descripcion`