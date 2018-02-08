Ext.ns('Rad');
Rad.Wizard = Ext.extend(Ext.Panel, {
    txtNext:	'Siguiente >>',
    txtPrev: 	'<< Atras',
    txtFinish: 	'Finalizar',
    position: 0,
	
    initComponent: function() {
        this.addEvents(
            'next',
            'prev',
            'finish',
            'activate'
        );
		
        this.buttonPrev = new  Ext.Toolbar.Button({
            text: '<< Atras',
            handler:  this.prev,
            disabled: true,
            scope: this,
            current: 0
        });
        this.buttonNext = new  Ext.Toolbar.Button({
            text: 'Siguiente >>',
            handler:  this.next,
            scope: this,
            disabled: false,
            current: 0
        });
        var config = {
            layout: 'card',
            activeItem: 0,
            bbar: [
            '->',
            this.buttonPrev,
            '|',
            this.buttonNext
            ]
        };
        Ext.apply(this, config);
        Ext.apply(this.initialConfig, config);


        Rad.Wizard.superclass.initComponent.apply(this, arguments);
    },
    next: function() {
        this.cardNav(1);
    },
    prev: function() {
        this.cardNav(-1);
    },
    getActiveItem: function () {
        return this.getLayout().activeItem;
    },
    getItemsCount: function () {
        return this.items.getCount();
    },
    onFinish: function() {
        this.fireEvent("finish",this);
    },
    setActiveItem: function(item) {
        if (item < 0 || item > this.getItemsCount()-1) {
            alert('RadWizard: No existe el item '+item);
            return;
        } else if(item > 0){
            this.buttonPrev.enable();
        } else {
            this.buttonPrev.disable();
        }
        if (item == this.getItemsCount() -1) {
            this.buttonNext.setText(this.txtFinish);
            this.buttonNext.setHandler( this.onFinish,this );
        } else {
            this.buttonNext.setText(this.txtNext);
            this.buttonNext.setHandler( this.next,this );
        }
        this.position = item;
        this.getLayout().setActiveItem(item);
        this.fireEvent("activate",item);
    },
    cardNav: function(nav) {
        var dest = this.position + nav;
        if (nav == 1) {
            var ok = this.fireEvent("next", this.position) !== false;
        } else {
            var ok = this.fireEvent("prev", this.position)  !== false;
        }
        if (ok) {
            this.setActiveItem(dest);
        }
    }
});