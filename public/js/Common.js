Ext.apply(Ext, {
    isFirebug: (window.console && window.console.firebug)
});

//fix charts
Ext.chart.Chart.CHART_URL = '/js/ext/resources/charts.swf';

// fuerzo a que no tengan sombras las ventanas
Ext.Window.prototype.floating = { shadow: false};
Ext.Panel.prototype.shadow = false;

// los tooltips sin bordes redondeeados asi me crea muchos menos obj en el DOM y son mas livianos
Ext.QuickTip.prototype.floating = { shadow: false};
Ext.QuickTip.prototype.frame = false;

/**
 * Extension de Tooltip para ajax
 * <input ext:qurl="servlets/verse.php?id=23243" ext:qtip=" ">
 * @type {String}
 */
Ext.QuickTip.prototype.tagConfig.url = 'qurl';
Ext.override(Ext.QuickTip, {
    // private
    urlBodyUpdate: function(t){
        var u, url, et = Ext.fly(t.el), cfg = this.tagConfig, ns = cfg.namespace;
        url = et.getAttribute(cfg.url, ns);
        if(url){
            u = this.body.getUpdater();
            u.update({
                scope: this,
                url: url,
                callback: function(el, success, response, options){
                    if(success && (et = Ext.fly(t.el))){
                        // found the element again, is changed when move fast through elements
                        var o = {};
                        o[ns+':'+cfg.attribute] = response.responseText; // set
                        o[ns+':'+cfg.url] = ''; // remove qurl to not be loaded many times
                        et.set(o);
                        if(!t.width){
                            this.doAutoWidth();
                        }
                    }
                }
            });
        }
    },

    showAt: Ext.QuickTip.prototype.showAt.createSequence(function(xy){
        var t = this.activeTarget;
        if(t){
            this.urlBodyUpdate(t);
        }
    })
});



/**
 *
    Fix para Ext.chart.Chart
    Bug "this.swf.setDataprovider is not a function"
    http://www.sencha.com/forum/showthread.php?78788-OPEN-197-3.0.0-svn-5208-this.swf.setDataprovider-is-not-a-function/page4
    Funciona para ExtJS 3.3.1
 */
Ext.override(Ext.chart.Chart, {
    refresh : function(){
        if(this.fireEvent('beforerefresh', this) !== false){
            var styleChanged = false;
            var data = [], rs = this.store.data.items;
            for(var j = 0, len = rs.length; j < len; j++){
                data[j] = rs[j].data;
            }
            var dataProvider = [];
            var seriesCount = 0;
            var currentSeries = null;
            var i = 0;
            if(this.series){
                seriesCount = this.series.length;
                for(i = 0; i < seriesCount; i++){
                    currentSeries = this.series[i];
                    var clonedSeries = {};
                    for(var prop in currentSeries){
                        if(prop == "style" && currentSeries.style !== null){
                            clonedSeries.style = Ext.encode(currentSeries.style);
                            styleChanged = true;
                        } else{
                            clonedSeries[prop] = currentSeries[prop];
                        }
                    }
                    dataProvider.push(clonedSeries);
                }
            }

            if(seriesCount > 0){
                for(i = 0; i < seriesCount; i++){
                    currentSeries = dataProvider[i];
                    if(!currentSeries.type){
                        currentSeries.type = this.type;
                    }
                    currentSeries.dataProvider = data;
                }
            } else{
                dataProvider.push({
                    type: this.type,
                    dataProvider: data
                });
            }
            if (this.swf && this.swf.setDataProvider) {
                this.swf.setDataProvider(dataProvider);
            }
            if(this.seriesStyles){
                this.setSeriesStyles(this.seriesStyles);
            }
            this.fireEvent('refresh', this);
        }
    }
});
/**
 *  fix drag & frop ext 3.4.1.1
 */
// Ext.dd.DragDropMgr.getZIndex = function(element) {
//     var body = document.body,
//         z,
//         zIndex = -1;
//     var overTargetEl = element;

//     element = Ext.getDom(element);
//     while (element !== body) {

//         // this fixes the problem
//         if(!element) {
//             this._remove(overTargetEl); // remove the drop target from the manager
//             break;
//         }
//         // fix end

//         if (!isNaN(z = Number(Ext.fly(element).getStyle('zIndex')))) {
//             zIndex = z;
//         }
//         element = element.parentNode;
//     }
//     return zIndex;
// };


/**
 * Manejadores de errores de Ajax
 */
var ajaxErrorHandler = function (obj, response, options, e)
{

    // Se pide confirmacion?
    if (response.status == 506) {
        Rad.confirmDialog(Ext.util.JSON.decode(response.responseText),
            // Reenvio de peticion con respuesta
            function(btn, text, cnf) {
                options.headers['confirm'] = '{"uid":"'+cnf.uid+'","data":"'+btn+'"}';
                obj.request(options);
            }
        );
        return false;
    }
    // Se perdio el login
    if (response.status == 505){
        var loginWin = new Rad.loginWindow();
        loginWin.show();
        return false;
    }

    // Errores avanzados en modo desarrollo
    if (response.status == 501){

        errorCode = Ext.util.JSON.decode(response.responseText);
        if (errorCode.error) {
            app.publish('/desktop/modules/Develop/debugconsole',{'action': 'servererror','title': errorCode.title, 'error': errorCode.error,'date': errorCode.date});
        }
    } else {
        msg = response.responseText;
        if (!msg) {
            switch(response.status) {
                case 500:
                    msg = 'Error del lado del servidor';
                    break;
                case 404:
                    msg = 'No existe el controlador';
                default:
                    msg = 'Error status: '+response.status+'<br>'+msg;
            }
        }
        app.publish('/desktop/showMsg', {
            title: 'Error',
            msg: msg,
            renderTo: 'x-desktop',
            modal: true,
            icon: Ext.Msg.ERROR,
            buttons: Ext.Msg.OK
        });
    }
};

Ext.util.Observable.observeClass(Ext.data.Connection);
Ext.data.Connection.on('requestexception', ajaxErrorHandler);

//Ext.Ajax.on('requestexception', ajaxErrorHandler);

Ext.namespace('Rad');

Rad.confirmDialog = function (cnf, callback) {
    var btns = (cnf.options.includeCancel) ? Ext.MessageBox.YESNOCANCEL : Ext.MessageBox.YESNO;
    app.desktop.showMsg({
        title: 'Atencion',
        msg: cnf.msg,
        modal: true,
        width: 300,
        buttons: btns ,
        icon: Ext.MessageBox.INFO,
        fn: function(btn, text) {
            if ((cnf.options.includeCancel && btn != 'cancel') || (!cnf.options.includeCancel && btn != 'no')){
                callback(btn, text, cnf);
            }
        }
    });
}

Rad.loginWindow = Ext.extend(Ext.Window, {
    title:'Iniciar Sesión',
    width:300,
    height:260,

    resizable:false,
    border: false,
    closable: false,
    //bodyStyle:'padding:10px;',
    buttonAlign:'center',
    layout:'fit',

    /**
     * Sobreescribir para ejecutar cuando el usuario se logueo corractamente
     */
    onSuccess: Ext.emptyFn,

    initComponent : function() {
        var usuarioField = new Ext.form.TextField({
            fieldLabel:'Usuario',
            value:'',
            name:'login',
            hasfocus:true,
            width:150,
            allowBlank:false,
            listeners: { 'specialkey':{fn: function(obj, e) {if (e.getKey() == 13) this.formulario.login.apply(this);}, scope: this} }
        });

        this.formulario = new Ext.form.FormPanel({
            baseCls:'x-plain',
            labelWidth:80,
            labelAlign:'right',
            defaultButton: usuarioField,
            standardSubmit : false,
            region:'center',

            login : function(){
                if (this.formulario.getForm().isValid()) {
                    this.formulario.getForm().submit({
                        scope: this,
                        standardSubmit: false,
                        waitMsg:'Por favor espere...',
                        url:'/auth/login',
                        failure:function(form, action) {
                            Ext.Msg.show({
                                title: 'Error',
                                msg: action.result.msg,
                                modal: true,
                                icon: Ext.Msg.ERROR,
                                buttons: Ext.Msg.OK
                            });
                        },
                        success:function(form, action) {
                            this.onSuccess();
                            this.close();
                        }
                    });
                } else {
                    Ext.Msg.show({
                        title: 'Error',
                        msg:  'Por favor ingrese un Usuario y Contraseña correctos.',
                        modal: true,
                        icon: Ext.Msg.ERROR,
                        buttons: Ext.Msg.OK
                    });
                }
            },


            items:[usuarioField,
            new Ext.form.TextField({
                fieldLabel:'Clave',
                value:'',
                name:'password',
                width:150,
                inputType:'password',
                allowBlank:false,
                listeners: { 'specialkey':{fn: function(obj, e) { if (e.getKey() == 13) this.formulario.login.apply(this); }, scope: this} }
            }),
            new Ext.form.Checkbox({
                fieldLabel:'Recordarme',
                name:'remember',
                inputValue:'1'
            })]
        });

        // items
        this.items = [{
            xtype: 'panel',
            style:'padding-left:10px;text-align:center;',
            border: false,
            layout: 'border',
            items: [
                {xtype:'panel',html:'<img src="images/cargando.jpg">',border: false,region:'north',height:110,bodyStyle:'background:#fff;'},
                this.formulario
                ]
        }];

        this.buttons = [{
            id: 'submit-button',
            text:'Login',
            scope: this,
            handler: this.formulario.login
        }];

        // llamamos al parent
        Rad.loginWindow.superclass.initComponent.call(this);
    }
});


    /**
     *
     *  Rad.callRemoteJsonAction({
     *      url: '/window/Almacenes/partirmmi',
     *          params: {
     *              id: selItem.data.Mmi.Id,
     *              cantidad: text,
     *          },
     *          success: function (response) {
     *          },
     *          failure: function (response) {
     *              return true;                    // true Muestra el mensaje de error en una ventanan emergente, false no
     *          }
     *  })
     *  @autor Martin Santangelo
     */
Rad.callRemoteJsonAction = function(params) {
    if (params.async == null) params.async = true;
    var cfg = {
        url: params.url,
        async: params.async,
        params: params.params,
        success: function(result, request) {

            var response = Ext.util.JSON.decode(result.responseText);

            if (response.success) {

                params.success.call(this,response);
            } else {
                if (!params.failure || params.failure(response)) {
                    app.publish('/desktop/showError', response.msg);
                }
            }

        }
    };
    if (params.scope) cfg.scope = params.scope;
    Ext.Ajax.request (cfg);
};


Rad.autoHideFields = function(form, combo, rules) {

    var desactivados = {};

    combo.on('select', function(t,data,id){
        for (var key in desactivados) {
            var field = form.getForm().findField(key);
            if (field) {
                field.show();
            }
        }

        desactivados = {};

        if (rules[data.id]) {

            des = rules[data.id];

            for (var i = des.length - 1; i >= 0; i--) {
                var field = form.getForm().findField(des[i]);
                if (field) {
                    desactivados[des[i]] = 1;
                    field.reset();
                    field.hide();
                }
            }
        }
    });
}
