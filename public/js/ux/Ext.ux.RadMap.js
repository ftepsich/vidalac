/**
 * RadMap, hereda de Managed Iframe y genera un mapa adentro de un iframe, para no tener problemas de licencia con google
 * 
 * @author Martin A. Santangelo
 */
Ext.ns('Rad');
Rad.Map = function(config) {
    // call parent constructor
	Rad.Map.superclass.constructor.call(this, config);
}

Ext.extend(Rad.Map, Ext.ux.ManagedIframePanel, {
	//defaultSrc	: 'http://maps.google.com/'
	loadMask: false,
	searchAddress: function(address) {
		this.defaultSrc = 'http://maps.google.com.ar/?output=embed&q='+encodeURI(address);
		this.setSrc();
	},
	searchPath: function(sAddress, dAddress) {
		this.defaultSrc = 'http://maps.google.com.ar/?output=embed&doflg=ptk&saddr='+encodeURI(sAddress)+'&daddr='+encodeURI(dAddress);
		this.setSrc();
	}
});

Ext.reg('radmap',Rad.Map);