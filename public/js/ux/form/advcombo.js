Ext.ux.form.AdvCombo = Ext.extend(Ext.ux.form.ComboBox, {

    // triggerClasses : ['x-form-trigger'],
    aditionalTriggersClasses: [],

    onTrigger1Click: function () {
        this.onTriggerClick();
    },

    showSearchMenu: function (obj) {
        this.searchMenu.show(this.triggers[this.searchIndex]);
    },
    showFetchMenu: function (obj) {
        this.fetchMenu.show(this.triggers[this.fetchIndex]);
    },

    onSearchItemCheck: function(item, checked){
        this.searchField = item.value;
    },
    onFetchItemCheck: function(item, checked){
        this.store.baseParams['fetch'] = item.value;
    },
	
    initComponent: function() {
            this.triggerClasses = ['x-form-trigger'];
            Ext.ux.form.AdvCombo.superclass.initComponent.call(this, arguments);
    },

    /**
     * Creamos los botones adicionales segun la configuracion
     */
    addTrigersFromConf: function () {
		// if (this.xtype == 'LinkTriggerField' && this.triggerClasses.length > 2) return;
		// else if (this.triggerClasses.length > 1) return;
		
        if (this.searchOptions) {
            var vmenu = [ '<b class="menu-title">Buscar Por</b>'];
            for(var i = 0; i < this.searchOptions.length; i++){
                o = this.searchOptions[i];
                vmenu.push({
                    text: o.text,
                    value: o.value,
                    checked: (i==0),
                    scope: this,
                    group: 'searchgroup',
                    checkHandler: this.onSearchItemCheck
                });
            }


            this.searchIndex = this.triggerClasses.length;

            this.searchMenu = menu = new Ext.menu.Menu({items: vmenu});

            this.triggerClasses.push({
                tag: "img",
                src: Ext.BLANK_IMAGE_URL,
                cls: "x-form-trigger x-form-search-trigger"
            });
            var triggerHandler = 'onTrigger' + this.triggerClasses.length + 'Click';

            if(!this[triggerHandler]) {
                this[triggerHandler] = this.showSearchMenu;
            }
        }

        // fetch
        if (this.fetchOptions) {
            var vmenu = [ '<b class="menu-title">Buscar</b>'];
            for(var i = 0; i < this.fetchOptions.length; i++){
                o = this.fetchOptions[i];
                vmenu.push({
                    text: o.text,
                    value: o.value,
                    checked: (i==0),
                    scope: this,
                    group: 'searchgroup',
                    checkHandler: this.onFetchItemCheck
                });
            }

            this.fetchMenu = menu = new Ext.menu.Menu({items: vmenu});

            this.fetchIndex = this.triggerClasses.length;

            this.triggerClasses.push({
                tag: "img",
                src: 'images/bullet_blue.png',
                cls: "x-form-trigger"
            });

            var triggerHandler = 'onTrigger' + this.triggerClasses.length + 'Click';
            if(!this[triggerHandler]) {
                    this[triggerHandler] = this.showFetchMenu;
            }
        }
    },


    onRender : function(ct, position){
		this.addTrigersFromConf();
        Ext.ux.form.AdvCombo.superclass.onRender.call(this, ct, position);
		
        

        this.wrap = this.el.wrap({
            cls: 'x-form-field-wrap x-form-field-trigger-wrap'
        });

        if(!this.triggerConfig){
            
            this.triggerClasses.concat(this.aditionalTriggersClasses);
            
            if(this.triggerClasses.length > 1){
                var triggers = [];
                for(var i = 0; i < this.triggerClasses.length; i++){
                    o = this.triggerClasses[i];
                    if (typeof(o)=='string') {

                        triggers.push({
                            tag: "img",
                            src: Ext.BLANK_IMAGE_URL,
                            cls: "x-form-trigger " + o
                        });
                    } else {
                        triggers.push(o);
                    }
                }
                

                this.triggerConfig = {
                    tag: 'span',
                    cls: 'x-form-twin-triggers',
                    cn: triggers
                };
            }else{
                this.triggerConfig = {
                    tag: "img",
                    src: Ext.BLANK_IMAGE_URL,
                    cls: "x-form-trigger "+ this.triggerClasses[0]
                    };
            }

        }
        this.trigger = this.wrap.createChild(this.triggerConfig);
        if(this.hideTrigger){
            this.trigger.setDisplayed(false);
        }
        this.initTrigger();
        if(!this.width){
            this.wrap.setWidth(this.el.getWidth() + this.trigger.getWidth());
        }
        if(!this.editable){
            this.editable = true;
            this.setEditable(false);
        }
    },
    getTrigger : function(index){
        return this.triggers[index];
    },

    initTrigger : function(){
        if (this.trigger.dom.tagName == 'IMG') {
            this.trigger.remove();
            return;
        }

        var ts = this.wrap.select('.x-form-trigger', true);
        this.wrap.setStyle('overflow', 'hidden');
        var triggerField = this;
        ts.each(function(t, all, index){
            t.hide = function(){
                var w = triggerField.wrap.getWidth();
                this.dom.style.display = 'none';
                triggerField.el.setWidth(w-triggerField.trigger.getWidth());
            };
            t.show = function(){
                var w = triggerField.wrap.getWidth();
                this.dom.style.display = '';
                triggerField.el.setWidth(w-triggerField.trigger.getWidth());
            };
            var triggerIndex = (this.triggerClasses.length > 1 ? index + 1 : '');
            if(this['hideTrigger' + triggerIndex]){
                t.dom.style.display = 'none';
            }
            var triggerHandler = 'onTrigger' + triggerIndex + 'Click';
            if(!this[triggerHandler]){
               
                this[triggerHandler] = function(){console.log(triggerHandler)};//Ext.emptyFn;
            }
           
            this.mon(t, 'click', this[triggerHandler], this, {
                preventDefault:true
            });
            t.addClassOnOver('x-form-trigger-over');
            t.addClassOnClick('x-form-trigger-click');
            var qtip = this['trigger' + triggerIndex + 'Tip'];
            if(qtip){
                if(Ext.isObject(qtip)){
                    Ext.QuickTips.register(Ext.apply({
                        target: t.id
                    }, qtip));
                } else {
                    t.dom.qtip = qtip;
                }
            }
        }, this);
        this.triggers = ts.elements;
    },


    /**
     * Debe retornar un obj con los parametros adicionales que quiere enviar al modulo o false para cancelar la llamada
     */
    onBeforeCallApp: function (combo)
    {
        return {};
    }
});

Ext.reg('AdvCombo',Ext.ux.form.AdvCombo);

