Ext.namespace("Ext.ux");

// Drag Zone de Iconos de Escritorio
Ext.ux.FileDragZone = function(view, config){
    this.view = view;
    this.scroll = false; 
    Ext.ux.FileDragZone.superclass.constructor.call(this, view.getEl(), config);
};

Ext.extend(Ext.ux.FileDragZone, Ext.dd.DragZone, {
    
    getDragData : function(e) {
        var target = e.getTarget(this.targetCls || '.thumb-wrap');
        if (target) {
            var view = this.view;
            if (!view.isSelected(target)) {
                view.onClick(e);
            }
            var records = view.getSelectedRecords();
            var dragData = {
                selections: records,
                nodes: view.getSelectedNodes()
            };
            if (records.length == 1) {
                dragData.ddel = target;
                dragData.single = true;
            } else {
                var div = document.createElement('div'); // create the multi element drag "ghost"
                div.className = 'multi-proxy';
                var count = document.createElement('div'); // selected image count
                count.innerHTML = records.length + ' selected';
                div.appendChild(count);
                
                dragData.ddel = div;
                dragData.multi = true;
            }
            return dragData;
        }
        return false;
    },

    // box the icon after a failed drag
    afterRepair: function() {
        if ( this.dragData.nodes ) {
            for(var i = 0, len = this.dragData.nodes.length; i < len; i++){
                Ext.fly(this.dragData.nodes[i]).frame('#8db2e3', 1);
            }
        }
        this.dragging = false;    
    },
    
    // override the default repairXY with one offset for the margins and padding
    getRepairXY: function(e) {
        if (!this.dragData.multi) {
            var xy = Ext.Element.fly(this.dragData.ddel).getXY();
            xy[0] += 3; xy[1] += 3;
            return xy;
        }
        return false;
    }

});


/**
 * Shortcuts
 */
Ext.ux.Shortcuts = function(config){
    var desktopEl = Ext.get(config.renderTo)
        ,taskbarEl = config.taskbarEl;
    
    // TODO share this common record globally
    var shortcutRecord = Ext.data.Record.create(['id', 'channel', 'text', 'icon']);
    // TODO load shortcuts remotely
    var store = new Ext.data.JsonStore({
        url: '/default/Desktop/getdesktopicons',
		idProperty: 'Id',
        root: 'files',
        fields: shortcutRecord,
        listeners: {
            'load': function() {
                this.publish('/desktop/shortcuts/loaded',{});
            }
        }
    });
    store.load();

    var dbg = Ext.getCmp('x-debug-browser');
    var extra = dbg ? dbg.el.getHeight() : 0;
    var iconHeight = 96;

    var sizes = {
        desktopHeight: ( Ext.lib.Dom.getViewHeight() - taskbarEl.getHeight() - extra )
    };
                
	function isOverflow(y){
		if(y > sizes.desktopHeight){
			return true;
		}
		return false;
	}

    var tpl = new Ext.XTemplate(
        '<tpl for=".">',
           '<tpl if="(xindex &gt; 1) && !((xindex - 1) % this.cols(xindex))">',
                '<div style="clear:left"></div>',
            '</tpl>',
            '<div class="thumb thumb-wrap ux-shortcut-item-btn {cls}-shortcut" id="shortcut-{id}" style="float: right;">',
            '<div class="thumb ux-shortcut-btn"><img src="images/modulos/{icon}64.png" title="{text}"/></div>',
            '<span class="x-editable ux-shortcut-btn-text x-unselectable" style="color: white;">{shortName}</span></div>',
        '</tpl>',
        '<div class="x-clear"></div>',
        {
            cols: function(idx) {
                var j = 0;
                for ( var i = 0, len = store.getCount(); i < len; i++ ) {
                    if ( ( j * iconHeight ) >= sizes.desktopHeight ) {
                        j--;
                        break;
                    }
                    j++;
                }
//                log('j:'+j+' cols:'+( Math.ceil( store.getCount() / j ) )+' total:'+store.getCount()+' desktop:'+sizes.desktopHeight);
                return Math.ceil( store.getCount() / j );
            }
        }
    );
    
    var view = new Ext.DataView({
        id: config.viewId,
        store: store,
        tpl: tpl,
        width: '100%',
        height: '100%',
//        autoHeight: true,
        multiSelect: true,
        overClass: 'x-view-over',
        itemSelector: 'div.thumb-wrap',
        emptyText: '',

        plugins: [
//            new Ext.DataView.LabelEditor({dataIndex: 'name'}),
//            new Ext.DataView.DragSelector({dragSafe: true})
        ],

        prepareData: function(data){

            data.shortName = Ext.util.Format.ellipsis(data.text, 50);
            return data;
        },
        
        listeners: {
            selectionchange: {
                fn: function(dv,nodes){
                    var l = nodes.length;
                    try {
                        window.status = l+' item'+( l != 1 ? 's' : '' )+' selected';
                    } catch(e) {};
                }
            },
            dblclick: {
                fn: function(dv) {
                    var records = dv.getSelectedRecords();
                    var record = records[0];
                    if ( !record || !record.data.channel)
                        return false;
                    //log('launching '+record.data.id);
					if (!record.data.ev) ev = {action: 'launch'};
					else ev = record.data.ev;
                    app.publish( record.data.channel, ev);
                }
            }
        }
    });
    this.desktopPanel = new Ext.Panel({
        renderTo: config.renderTo,
        id: 'desktop-view',
        frame: false,
        border: false,
        width: '100%',
        height: sizes.desktopHeight,
        //autoHeight:true,
        layout:'fit',
        items: view
    });

    this.reload = function() {
        store.reload();
    };

    this.addShortcut = function(config) {
        var record = new shortcutRecord({
            id: config.id,
            text: config.text,
			//shortName : Ext.util.Format.ellipsis(config.text, 30),
            icon: config.icon,
            channel: config.channel,
            ev: config.ev
        });
		
		//console.log(record);
        store.add([record]);
        view.refresh();
        return record;
    };

    this.removeShortcut = function(shortcut) {
        var record = store.getById(shortcut.id);
        if ( record ) {
            var node = view.getNode(store.find('id',shortcut.data.id));
            //app.desktop.config.launchers.shortcut.remove(shortcut.data.id);
            // it should always find it, but we'll be careful
            if ( node )
                Ext.fly( node ).ghost('l',{ duration: .5, callback: function() { store.remove(record); view.refresh(); } });
            else {
                store.remove(record);
                view.refresh();
            }
        }
    };

    this.handleUpdate = function() {
        var dbg = Ext.getCmp('x-debug-browser');
        var extra = dbg ? dbg.el.getHeight() : 0;
        sizes.desktopHeight = Ext.lib.Dom.getViewHeight() - taskbarEl.getHeight() - extra;
        this.desktopPanel.setHeight( sizes.desktopHeight );
        view.refresh();
    };
    Ext.EventManager.onWindowResize(this.handleUpdate, this, {delay:500});
 

    var dragZone = new Ext.ux.FileDragZone(view,{
        containerScroll: false,
        ddGroup: 'file-manager-group',
        startDrag: function(x,y) {
            Ext.get("ux-papelera-img").fadeIn();

        },
        endDrag: function(x,y) {
            Ext.get("ux-papelera-img").puff();
        }
    });


    var dropTarget = new Ext.dd.DropTarget('ux-papelera', {
        ddGroup    : 'file-manager-group',
        copy       : false,
        notifyDrop : function(ddSource, e, odata){

            var id = odata.selections[0].data.id
            Models.Model_UsuariosEscritorioMapper.removeShortcut(id, function(result, e) {
                if (e.status) {
                    app.desktop.shortcuts.reload();
                }
            }, this);

            return true;
        }
    });     
};

