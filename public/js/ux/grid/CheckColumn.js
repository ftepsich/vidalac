/*!
 * Ext JS Library 3.2.1
 * Copyright(c) 2006-2010 Ext JS, Inc.
 * licensing@extjs.com
 * http://www.extjs.com/license
 */
Ext.ns('Ext.ux.grid');

/**
 * @class Ext.ux.grid.CheckColumn
 * @extends Object
 * GridPanel plugin to add a column with check boxes to a grid.
 * <p>Example usage:</p>
 * <pre><code>
// create the column
var checkColumn = new Ext.grid.CheckColumn({
   header: 'Indoor?',
   dataIndex: 'indoor',
   id: 'check',
   width: 55
});

// add the column to the column model
var cm = new Ext.grid.ColumnModel([{
       header: 'Foo',
       ...
    },
    checkColumn
]);

// create the grid
var grid = new Ext.grid.EditorGridPanel({
    ...
    cm: cm,
    plugins: [checkColumn], // include plugin
    ...
});
 * </code></pre>
 * In addition to storing a Boolean value within the record data, this
 * class toggles a css class between <tt>'x-grid3-check-col'</tt> and
 * <tt>'x-grid3-check-col-on'</tt> to alter the background image used for
 * a column.
 */
Ext.ux.grid.CheckColumn = function(config){
    Ext.apply(this, config);
    if(!this.id){
        this.id = Ext.id();
    }
    this.renderer = this.renderer.createDelegate(this);
};

Ext.ux.grid.CheckColumn.prototype ={
	onlyDiferentValues: false,
	modifiedChecks: {},
    init : function(grid){
        this.grid = grid;
        this.grid.on('render', this.bindEvents,this);
        if (this.onlyDiferentValues)this.grid.store.on('load', function(){this.modifiedChecks= {};},this);
    },
	
	bindEvents: function () {
		var view = this.grid.getView();
		view.mainBody.on('mousedown', this.onMouseDown, this);
		Ext.fly(view.innerHd).on('mousedown', this.onHdMouseDown, this); // This is for the header.
	},

    onMouseDown : function(e, t) {
        if(Ext.fly(t).hasClass(this.createId())){
            e.stopEvent();
            var index = this.grid.getView().findRowIndex(t);
            var record = this.grid.store.getAt(index);
            //alert(t.className);
			if (this.onlyDiferentValues) {
				if (!this.modifiedChecks[record.data.Id]) {
					this.modifiedChecks[record.data.Id] = record.data.Id;
					record.set(this.dataIndex, !record.data[this.dataIndex]);
				} else {
					delete this.modifiedChecks[record.data.Id];
					record.reject();
				}
			} else {
				record.set(this.dataIndex, !record.data[this.dataIndex]);
			}
        }
    },

    // private
    onHdMouseDown : function(e, t){
        if(t.className == 'x-grid3-hd-checker'){
            e.stopEvent();
            var hd = Ext.fly(t.parentNode);
            var isChecked = hd.hasClass('x-grid3-hd-checker-on');
            if(isChecked){
                hd.removeClass('x-grid3-hd-checker-on');
                //this.clearSelections();  //Need to replace with an internal function to clear all selections.
//                record.set(this.dataIndex, !record.data[this.dataIndex]);

            } else {
                hd.addClass('x-grid3-hd-checker-on');
                //this.selectAll(); //Need to replace with internal function to have all selections.
              //   if(Ext.fly(a).className == (this.createId())){
                    var index = this.grid.getView().findRowIndex(t);
                    var record = this.grid.store.getAt(index);
                    var len = this.grid.store.getCount();
                    alert(Ext.fly(this.grid.getView().findRowIndex(t)));
                    for(var i = index; i < len; i++){
                        record.set(this.dataIndex, !record.data[this.dataIndex]);
                    }
               // }
            }
        }
    },
  
    renderer : function(v, p, record){
        p.css += ' x-grid3-check-col-td'; 
        return String.format('<div class="x-grid3-check-col{0} {1}"> </div>', v ? '-on' : '', this.createId());
    },
    
    createId : function(){
        return 'x-grid3-cc-' + this.id;
    }
};

// register ptype
Ext.preg('checkcolumn', Ext.ux.grid.CheckColumn);

// backwards compat
Ext.grid.CheckColumn = Ext.ux.grid.CheckColumn;