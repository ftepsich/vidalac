/**
 *  SmartSoftware
 *  Objetos Ext de la applicacion y Modulos
 *  
 *  Se presume que:
 *      Cada Modulo tiene un appChannel unico que lo identifica
 *      Todo Modulo Cargado esta registrado y agregado en modules
 */

// normalizamos el log
/*
var log;

if ( window.log ) {
    log = window.log;
} else if ( window.console ) {
    if ( window.controllers ) {
        log = function() {
            window.console.log.apply( window.console, arguments );
        };
    } else {
        log = function(m) {
            window.console.log(m);
        };
    }
} else {
    log = Ext.emptyFn;
}*/

Ext.ns( 'RadDesktop' );

/**
 * Normalizamos el manejo de errores de los DataProxy
 */
Ext.data.DataProxy.on('exception', function(proxy, type, action, exception, request) {

    // Ignora los Rad_Confirm, lo intercepta el error handler generico
    if (request.status == 506) {
        return false;
    }

    if (request.status != 505) {
        if (request.message) {
            window.app.publish('/desktop/showError', request.message);
        } else if (request.responseText){
                window.app.publish('/desktop/showError', request.responseText);
        } else {
            window.app.publish('/desktop/showError', 'Error de comunicacion con el servidor');
        }
    }
});

Ext.Direct.on("exception", function(event) {

    // envio de relogin por direct
    if (event.relogin == 'relogin')  {
        var loginWin = new Rad.loginWindow();
        loginWin.show();
        return;
    }

    // envio de confirmacion por direct
    if (event.confirm) {
        var t = event.getTransaction();

        Rad.confirmDialog(event,
            // Reenvio de peticion con respuesta
            function(btn, text, cnf) {
                t.headers = {}
                t.headers['confirm']= '{"uid":"'+cnf.uid+'","data":"'+btn+'"}';
                Ext.Direct.addTransaction(t);
                t.retry();
//                t.retry();
            }
            );
        return;
    }

    window.app.publish('/desktop/showError', event.message);
});



Ext.ns( 'Models' );

/**
 * Aplicacion Principal
 *
 * Esta aplicacion contiene al escritorio y contiene la logica para cargar bajo demanda,
 * ejecutar y comunicar los modulos
 */
RadDesktop.App = Ext.extend(Ext.util.Observable, {
    constructor: function (config) {
        Ext.apply(this, config);
        this.addEvents({
            'ready': true,
            'beforeunload': true
        });
        window.app = this;
        Ext.onReady(this.initApp, this);
    },
    idCounter: 1,
    id: function( x ) {
        return ( x || 'rd-gen' ) + (++app.idCounter);
    },

    isReady: false,
    startMenu: null,
    modules: [],    // Loaded Modules
    getStartConfig: function() {

    },
    
    channels: {
        registerApp:      '/desktop/module/register',
        apps:             '/desktop/modules',
        registerModelApi: '/desktop/model/register'
    },

    initApp: function() {
        this.subscribe( this.channels.registerApp, this.eventRegisterModule, this );
        this.subscribe( this.channels.apps, this.eventCallApp, this );
        this.subscribe( this.channels.registerModelApi, this.eventRegisterModel, this );
        
        this.startConfig = this.startConfig || this.getStartConfig();

        this.desktop = new RadDesktop.Desktop(this);

        this.wamp = new Rad.wamp({wsuri:'wss://'+window.location.hostname+':8443'});
        
        this.init();

        Ext.EventManager.on(window, 'beforeunload', this.onUnload, this);
        this.fireEvent('ready', this);
        this.isReady = true;
    },
    eventRegisterModel: function (ev) {

    },
    eventCallApp: function ( ev, channel ) {
        module = app.getModule(channel);
        if (module) {
            return true;
        } else {
            if (!ev.rethrow) {
                this.fetchRemoteModule(channel, ev);
                return false
            } else {
                this.publish('/desktop/showError','Error al cargar el modulo');
            }
            return false;
        }
    },
    fetchRemoteModule: function(channel, ev) {
        this.publish( '/desktop/notify',{
            title: 'Cargando...',
            iconCls: 'x-icon-information',
            html: 'El modulo se esta cargando'
        });
        var url = channel.replace(this.channels.apps, '');
        new RadDesktop.FileFetcher({
            files: [url],
            noCache: false,
            start: true,
            success: function() {
                //TODO: ver si quiero avisar así q el evento se disparo nuevamente
                ev.rethrow = true;
                this.publish(channel, ev);
            },
            failure: function() {
                this.publish( '/desktop/notify',{
                    title: 'Error',
                    iconCls: 'x-icon-error',
                    html: 'Error al cargar el modulo'
                });
            },
            scope: this
        });
    },
    
    getModule: function(channel) {
        var ms = this.modules;
        for (var i = 0, len = ms.length; i < len; i++) {
            if (ms[i].channel == channel || ms[i].id == channel) {
                return ms[i];
            }
        }
        return '';
    },
    eventRegisterModule: function( ev ) {
        if ( !this.isReady ) {
            return Ext.onReady( this.eventRegisterApp.createDelegate( this, [ ev ] ) );
        }
        module = this.getModule(ev.channel);
        if (module == '') {
            this.modules.push( ev );
            if (ev.trayIcon) {
                app.desktop.taskbar.trayPanel.add(ev.trayIcon);
            }
        } else {
            module = ev;
        }
    },

    getModules: Ext.emptyFn,
    init: Ext.emptyFn,

    onReady: function(fn, scope) {
        if (!this.isReady) {
            this.on('ready', fn, scope);
        } else {
            fn.call(scope, this);
        }
    },

    getDesktop: function() {
        return this.desktop;
    },

    onUnload: function(e) {
        if (this.fireEvent('beforeunload', this) === false) {
            e.stopEvent();
        }
    }
});


/**
* Cargador de Archivos Dinamico
*/
RadDesktop.FileFetcher = Ext.extend( Ext.util.Observable, {

    extRegexp: /\.([^\./]+)$/,
    basedirRegexp: /(^.+)\/[^\/]*$/,

    constructor: function( config ) {
        this.queue = [];
        this.active = false;

        if ( Ext.isArray( config ) )
            this.load( {
                files: config
            } );

        if ( Ext.isObject( config ) )
            this.start( config );
    },

    load: function( data ) {
        if ( Ext.isArray( data.files ) ) {
            var files = data.files;
            delete data.files;
            for ( var i = 0, len = files.length; i < len; i++ )
                this.queue.push( Ext.copyTo( {
                    file: files[ i ]
                }, data, 'callback,scope,noCache' ) );
        } else
            this.queue.push( Ext.copyTo( {
                file: data.file
            }, data, 'callback,scope,noCache' ) );
    },

    getType: function( file ) {
        var r = this.extRegexp.exec( file );
        if ( r )
            return r[ 1 ];
        else    // Si no tiene extension asumimos q es un js
            return 'js'
    },

    getBaseDir: function( file ) {
        var r = this.basedirRegexp.exec( file );
        return r[ 1 ];
    },

    start: function( config ) {
        var start = config.start ? true : false;
        delete config.start;

        Ext.apply( this, config );

        if ( this.files )
            this.load( {
                files: this.files
            } );

        if ( start || !config )
            this.checkQueue();
    },

    checkQueue: function() {
        if ( this.active )
            return;

        this.active = true;
        var item = this.queue[ 0 ];
        if ( !item.type )
            item.type = this.getType( item.file );
        item.id = app.id( item.type + '-file-' );

        item.basedir = this.getBaseDir( item.file );

        switch ( item.type ) {
            case 'js':
                //Ext.Loader.load([item.file],function(){this.requestDone(); },this);

                Ext.Ajax.request({
                     method: 'GET',
                     url: item.file,
                     scope: this,
                     disableCaching: ( item.noCache || this.noCache ? true : false ),
                     success: function( res ) {
                         try {
                             eval( res.responseText );// # sourceURL=ModuloCargado.js
                             this.requestDone();
                         } catch(e) {
                             var msg;
                             // Chequeamos si no se mando un mensaje de error por json
                             var error = null;
                             try {
                                error = Ext.util.JSON.decode(res.responseText);
                             } catch(e2) {}
                             
                             if (error) {
                                 msg = error.msg;
                             } else {
                                 msg = e.message;
                                 if (e.lineNumber) msg += '<br>linea '+(e.lineNumber - 328) + ' en ' + e.fileName;
                             }
                             app.publish( '/desktop/showMsg', {
                                 title: 'Error',
                                 msg:  msg,
                                 buttons: Ext.MessageBox.OK,
                                 icon: Ext.MessageBox.WARNING
                             });
                             this.requestFailed();
                         };
                        
                     },
                     failure: this.requestFailed
                 });

                break;
                
            case 'css':
                Ext.Ajax.request({
                    method: 'GET',
                    url: item.file,
                    scope: this,
                    disableCaching: ( item.noCache || this.noCache ? true : false ),
                    success: function( res ) {
                        // hack
                        var txt = res.responseText.replace( 'url(', 'url(' + item.basedir + '/', 'g' );
                        txt = txt.replace( "src='", "src='" + item.basedir + '/', 'g' );
                        Ext.util.CSS.createStyleSheet( txt, this.queue[ 0 ].id );
                        this.requestDone();
                    },
                    failure: this.requestFailed
                });
                break;

            default:
        //log('unhandled type in fetcher:'+item.type);
        }

    },

    requestDone: function() {
        this.active = false;

        var item = this.queue.shift();
        

        if ( this.itemHandler ) {
            if ( this.scope )
                this.itemHandler.call( this.scope, item );
            else
                this.itemHandler( item );
        }

        if ( item.callback ) {
            if ( item.scope )
                item.callback.call( item.scope, this, item );
            else
                item.callback( this, item );
        }

       
        if ( this.queue.length )
            this.checkQueue.defer( 10, this );
        else {
            
            if ( this.scope )
                this.success.call( this.scope, this );
            else
                this.success( this );
        }
    },

    requestFailed: function() {
        this.active = false;

        if ( this.failure ) {
            if ( this.scope )
                this.failure.call( this.scope, this );
            else
                this.failure( this );
        }
    },

    callback: function( callback, scope ) {
        this.queue.push( {
            type: 'callback',
            callback: callback,
            scope: scope
        } );
    }

});


/**
* Clase Base para los modulos del escritorio
*/
RadDesktop.Module = Ext.extend( Ext.util.Observable, {
    /**
    * Aqui se agregan los js de los que depende este modulo
    * estos se cargaran automaticamente antes de ejecutarse
    */
    trayIcon:null,
    requires: null,
    eventQueue: [],
    started:    false,
    
    constructor: function( config ) {
        Ext.apply( this, config );  
        if ( !this.id ) {
            this.id = app.id( 'app-' );
        }
        if ( !this.appChannel ) {
            this.appChannel = RadDesktop.App.channels.apps + this.id;
        }
        this.subscribe( this.appChannel, this.eventReceived, this );
        
        
        
        this.register({
            id:      this.id,
            channel: this.appChannel,
            title:   this.title,
            trayIcon:this.trayIcon
        });
        
        
        this.init( config );
         
        if ( this.requires ) {
            this.loadFiles( this.requires );
        } else {
            this.startup();
            this.postStartup();
        }
    },
    /**
    * Si hay eventos encolados los ejecuto ahora q ya esta iniciada la aplicacion
    */
    postStartup: function () {
        this.started = true; 
        while (this.eventQueue.length > 0) {
            ev = this.eventQueue.pop();
            this.eventReceived(ev, this.appChannel);
        }
    },
    /**
    * Esta funcion se ejecuta cuando el modulo termina de cargar y ejecuta su constructor (recibe la configuracion del obj)
    */
    init: Ext.emptyFn,
    /**
    * Esta funcion se ejecuta cuando el modulo termina de cargar correctamente, incluyendo sus dependencias
    */
    startup: Ext.emptyFn,
    
    
    eventReceived: function (ev, channel) {
        if (channel != this.appChannel) return;
        
        // Si la aplicacion no esta iniciada encolo los eventos
        if (!this.started) {
            this.eventQueue.push(ev);
         } else {
            if (this['event'+ev.action]) {
                this['event'+ev.action].call(this, ev );
            }
        }
    },
    register: function( ev ) {
        this.publish( app.channels.registerApp, ev );
    },
    __loadSuccess: function() {
        //this.publish( this.appChannel, { action: 'deps-loaded', success: true } );
        this.__fetcher = null;
        this.startup();
        this.postStartup();
    },

    __loadFailure: function() {
        this.__retryCount++;
        if ( this.__retryCount > 10 ) {
            this.publish(
                '/desktop/notify',{
                    title: 'Error',
                    iconCls: 'x-icon-error',
                    html: 'Error al cargar dependencias para el modulo'
                });
            return;
        }
        this.__fetcher.start();
    },
    
    
    __loadedFile: function( data ) {
    //this.publish( this.appChannel, Ext.apply( data, { action: 'file-loaded' } ) );
    },
    
    
    loadFiles: function( files ) {
        if ( Ext.isEmpty( files ) )
            return;
        if ( !Ext.isArray( files ) )
            files = [ files ];

        this.__fetcher = new RadDesktop.FileFetcher({
            files: files,
            noCache: false,
            start: true,
            itemHandler: this.__loadedFile,
            success: this.__loadSuccess,
            failure: this.__loadFailure,
            scope: this
        });
    }   
});

// Menu para channel
Ext.override( Ext.menu.BaseItem, {

    initComponent: function() {
        Ext.menu.BaseItem.superclass.initComponent.apply( this, arguments );

        if ( this.channel && !this.handler ) {
            this.on( 'click', function() {
                this.publish( this.channel, {
                    action: 'launch'
                } );
            }, this );
        }

        if ( this.handler )
            this.on( 'click', this.handler, this.scope || this );
    }

});
