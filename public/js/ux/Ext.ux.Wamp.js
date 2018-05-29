/**
 * Wraper para Web socket wamp protocol
 * utilizando autobahn
 *
 * @author Martin A. Santangelo
 * TODO: renmobrar los archivos en el namespace Rad a Rad.algo
 */

Ext.ns('Rad');

Rad.wamp = Ext.extend(Object,{
    session: null,
    constructor: function(config) {
        
        // this.session = null;
        t = this;
        Ext.apply(this,config||{});
        
        ab.connect(
            this.wsuri, 
            function(s) {
                t.session = s;
            },
            function(code, reason) {
                t.session = null;
            }
        );
        
    },
    publish: function (topic, ev) {
        this.checkConn();
        this.session.publish(topic, ev);
    },
    subscribe: function (topic, callback) {
        this.checkConn();
        this.session.subscribe(topic, callback);
    },
    isConected: function() {
        return (this.session != null);
    },
    getSession: function() {
        return this.session;
    },
    
    checkConn: function()
    {
        if (!this.isConected()) throw {message: "No esta conectado al Server WAMP"};
    }
});