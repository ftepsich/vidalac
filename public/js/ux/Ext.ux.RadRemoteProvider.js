Ext.direct.RadRemotingProvider = Ext.extend(Ext.direct.RemotingProvider, {
    doSend : function(data){
        var o = {
            url: this.url,
            callback: this.onData,
            scope: this,
            ts: data,
            timeout: this.timeout
        }, callData;

        var headers;

        if(Ext.isArray(data)){
            callData = [];
            for(var i = 0, len = data.length; i < len; i++){
                callData.push(this.getCallData(data[i]));
            }
        }else{
            headers = data.headers;
            delete(data.headers);
            callData = this.getCallData(data);
        }

        if(this.enableUrlEncode){
            var params = {};
            params[Ext.isString(this.enableUrlEncode) ? this.enableUrlEncode : 'data'] = Ext.encode(callData);
            o.params = params;
        }else{
            o.jsonData = callData;
        }
        o.headers = headers;

        Ext.Ajax.request(o);
    }

});

Ext.Direct.PROVIDERS['radremoting'] = Ext.direct.RadRemotingProvider;
