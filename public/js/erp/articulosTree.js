/**
 * Rad Framework
 * Copyright(c) 2013 SmartSoftware
 * @author Martín A. Santangelo
 */

Ext.ns( 'Rad' );

/**
 * Rad.TreePanel
 */
Rad.ArticulosTreePanel = Ext.extend(Ext.TabPanel, {
    activeTab:0,
    deferredRender: false,
    itemSelector: 'span',
    selected: null,

    tplArbol: new Ext.XTemplate(
        '<ul>',
        '<tpl for=".">',
            '<li ',
            '<tpl if="TipoDeRelacionArticulo == \'1\'">class="resaltado"</tpl>',
            '<tpl if="TipoDeRelacionArticulo == \'2\'">class="resaltado producto"</tpl>',
            '><span artVer="{ArticuloVersionId}"> <b>{[Math.round(values.Cantidad * 100) / 100]}</b>',
                '<tpl if="UnidadDeMedidaR != \'u\'">',
                   ' <b>{UnidadDeMedidaR}</b>',
                '</tpl>',
            ' - {ArticuloDesc}</span>',
                '<tpl if="typeof Hijos !=\'undefined\'">',
                   '{[ this.recurse(values) ]}',
                '</tpl>',
            '</li>',
        '</tpl>',
        '</ul>',
        {
            recurse: function(values) {
               return this.apply(values.Hijos);
            }
        }
    ),

    tplRequerimientos: new Ext.XTemplate(
        '<ul>',
        '<li><span>{[values[0].ArticuloDesc]}</span>',
        '<ul>',
        '<tpl for=".">',
            '<tpl if="MateriaPrima == \'1\' && (TipoDeRelacionArticulo != \'2\' || TieneFormula != \'1\')">',
                '<li><span> <b>{CantidadTotal} {UnidadDeMedidaR}</b> - {ArticuloDesc}</span></li>',
            '</tpl>',
        '</tpl>',
        '</ul>',
        '</li></ul>'
    ),

    tplInfo: new Ext.XTemplate(
        '<ul>',
        '<li><span>Producto: {productoDescripcion}</span>',
        '<ul>',
            '<li><span> <b>Cantidad</b> {productoCantTotal} {productoUMD}</span></li>',
        '</ul>',
        '</li></ul>'
    ),

    initComponent: function() {
        /**
         * @event clickNode
         * Fires when a node is clicked
         * @param {Ext.Component} this
         * @param {number} articuloVersion
         * @param item
         * @param {event} e
         */
        this.addEvents('clickNode','dblclickNode','select');

        // Tabs
        this.items = [{
            title: 'Estructura',
            html:'<div id="'+this.getId()+'ArticulosArbol" class="arbol" ></div>',
        },{
            title: 'Requerimientos',
            html:'<div id="'+this.getId()+'ArticulosRequerimientos" class="arbol"></div>',
        },{
            title: 'Información',
            html:'<div id="'+this.getId()+'ArticulosInformacion" class="arbol"></div>',
        }]

        Rad.ArticulosTreePanel.superclass.initComponent.call(this);
    },

    load: function(id)
    {
        Rad.callRemoteJsonAction ({
            params: {
                'id': id,
            },
            url: '/Base/administrarArticulos/getarbol',
            scope: this,
            success: function(response) {
                var artVer  = null;
                if (this.selected) {
                    artVer = this.selected.getAttribute('artVer');
                    this.selected = null;
                }

                var detailEl = Ext.get(this.getId()+'ArticulosArbol');
                var detailEl2 = Ext.get(this.getId()+'ArticulosRequerimientos');
                var detailEl3 = Ext.get(this.getId()+'ArticulosInformacion');

                this.tplArbol.overwrite(detailEl, response.arbol);
                this.tplRequerimientos.overwrite(detailEl2, response.desglose);
                this.tplInfo.overwrite(detailEl3, response);

                if (artVer != null) {
                    this.select(detailEl.select('[artVer='+artVer+']').item(0));
                }

            }
        });
    },

    afterRender : function() {
        Rad.ArticulosTreePanel.superclass.afterRender.call(this);

        this.mon(this.getTemplateTarget(), {
            "click": this.onClick,
            "dblclick": this.onDblClickm,
            // "contextmenu": this.onContextMenu,
            scope:this
        });
    },

    getTemplateTarget: function() {
        return this.items.get(0).getEl();
    },

    // private
    onClick : function(e){
        var item = e.getTarget(this.itemSelector, this.getTemplateTarget());
        if(item){
            var el = Ext.get(item);
            this.clearSelections();
            this.select(el);
            this.fireEvent("clickNode", this, el.getAttribute('artVer'), item, e);
        } else {
            if(this.fireEvent("containerclick", this, e) !== false){
                this.onContainerClick(e);
            }
        }
    },

    onContainerClick : function(e){
        this.clearSelections();
    },

    // private
    onDblClick : function(e){
        var item = e.getTarget(this.itemSelector, this.getTemplateTarget());
        if(item){
            var el = Ext.get(item);
            this.fireEvent("dblclickNode", this, el.getAttribute('artVer'), item, e);
        }
    },

    isSelected : function(node){
        return this.selected == node;
    },

    select: function(node){
        if(node && !this.isSelected(node)){
            node.addClass('selected');
            this.selected = node;
        }
    },

    clearSelections: function() {
        if(this.selected != null){

            this.selected.removeClass('selected');

            this.selected = null;
        }
    }

});