Ext.ux.form.LinkTriggerField = Ext.extend(Ext.ux.form.AdvCombo, {

    /**
     * Debe retornar un obj con los parametros adicionales que quiere enviar al modulo o false para cancelar la llamada
     */
    onBeforeCallApp: function (combo)
    {
        return {};
    },
	
    initComponent: function() {
        Ext.ux.form.LinkTriggerField.superclass.initComponent.call(this, arguments);
        this.triggerClasses = [
            'x-form-trigger',
            {
                    tag: "img",
                    src: 'images/bullet_go.png',
                    cls: "x-form-trigger"
            }
        ];
    },

    onTrigger2Click: function () {
        defaults = {
            action: 'find',
            value: this.link.fixedValue || this.getValue()
        };
        Ext.applyIf(
            defaults,
            this.onBeforeCallApp(this)
        );
       
        this.publish(app.channels.apps + this.link, defaults);
    }
});

Ext.reg('LinkTriggerField',Ext.ux.form.LinkTriggerField);