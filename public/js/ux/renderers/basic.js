Ext.ns('Ext.ux.Format');

Ext.ux.Format = function() {
    return {
        menorrojo: function(val,menor) {
            if (menor == undefined) menor = 50;
            if(val >= menor){
                return '<span style=\"color:green;\">$' + val + '</span>';
            }else if(val < menor){
                return '<span style=\"color:red;\">$' + val + '</span>';
            }
            return val;
        },

        moneda4decimales: function(v, params, record) {
            v = (Math.round((v-0)*10000))/10000;
            v = (v == Math.floor(v)) ? v + ".0000" : ((v*10 == Math.floor(v*10)) ? v + "0" : v);
            v = String(v);
            var ps = v.split('.');
            var whole = ps[0];
            var sub = ps[1] ? '.'+ ps[1] : '.0000';
            var r = /(\d+)(\d{3})/;
            while (r.test(whole)) {
                whole = whole.replace(r, '$1' + ',' + '$2');
            }
            v = whole + sub;
            if(v.charAt(0) == '-'){
                return '-$ ' + v.substr(1);
            }
            return "$ " +  v;
        },

        porcentaje: function(v, params, record) {
            v = (Math.round((v-0)*100))/100;
            v = (v == Math.floor(v)) ? v + ".00" : ((v*10 == Math.floor(v*10)) ? v + "0" : v);
            v = String(v);
            var ps = v.split('.');
            var whole = ps[0];
            var sub = ps[1] ? '.'+ ps[1] : '.00';
            var r = /(\d+)(\d{3})/;
            while (r.test(whole)) {
                whole = whole.replace(r, '$1' + ',' + '$2');Ext.ns();
            }
            v = whole + sub;
            if(v.charAt(0) == '-'){
                return '-% ' + v.substr(1);
            }
            return "% " +  v;
        },

        redondeo4decimales: function(v, params, record) {
            return parseFloat(v).toFixed(4);
        },

        redondeo2decimales: function(v, params, record) {
            return parseFloat(v).toFixed(2);
        },

        saldo2decimales: function(v, params, record) {
            return parseFloat(v).toFixed(2); // Esto no sq xq no esta comentado
            if (record.data.Saldo < 0) {
                return '<font color=red>'+parseFloat(record.data.Saldo).toFixed(2)+'</font>'
            } else {
                return parseFloat(record.data.Saldo).toFixed(2);
            }
        },

        estados: function(v, params, record) {
            var estados = {
                'Nada':            'red',
                'Parcialmente':    'blue',
                'Totalmente':      'green',
                'Contado':         'lightseagreen',
                'Anulado':         'maroon',
                'Excedido':        'black'
            }
            var color = (estados[v]) ? estados[v] : 'black';
            return "<font color='"+color+"'>"+record.data.EstadoPagado+"</font>";
        },

        mmiTrazabilidad: function(v, params, record) {
            var a= '<div onclick=\"app.publish(app.channels.apps + \'/Almacenes/Trazabilidad\', {action:\'launch\', id: '+record.data.Id+'})\" style=\"background-image:url(images/arrow_divide.png)\" qtip=\"Trazabilidad\" class=\"ux-cell-action \"> </div>';
            return a+v;
        },

        pagadoCon: function(app) {
            var rend =  function(v, params, record) {
                var v;
                var a= '<div onclick="app.publish(app.channels.apps+\''+ app +'\', {action:\'custom\', value: '+record.data.Id+'})" style="background-image:url(images/money.png)" qtip="Ver Pagos" class="ux-cell-action"> </div>';

                if (record.data.EstadoPagado=='Nada') {
                    v = '<font color=red>'+record.data.EstadoPagado+'</font>'
                }
                if (record.data.EstadoPagado == 'Parcialmente') {
                    v = a+'<font color=blue>'+record.data.EstadoPagado+'</font>'
                }
                if (record.data.EstadoPagado == 'Totalmente') {
                    v = a+'<font color=green>'+record.data.EstadoPagado+'</font>'
                }
                if (record.data.EstadoPagado == 'Contado') {
                    v = a+'<font color=LightSeaGreen>'+record.data.EstadoPagado+'</font>'
                }
                if (record.data.EstadoPagado == 'Anulado') {
                    v = '<font color=Maroon>'+record.data.EstadoPagado+'</font>'
                }
                if (record.data.EstadoPagado == 'Excedido') {
                    v = a+'<font color=black>'+record.data.EstadoPagado+'</font>'
                }
                return v;
            };
            return rend;
        },

        facturadoCon: function(app) {
            var renderer = function(v, params, record) {
                var a= '<div onclick="app.publish(app.channels.apps+\''+ app +'\', {action:\'facturado\', value: '+record.data.Id+'})" style="background-image:url(images/lorry_go.png)" qtip="Ver Facturas" class="ux-cell-action"> </div>';

                if (record.data.EstadoFacturado=='Nada') {
                    v = '<font color=black>'+record.data.EstadoFacturado+'</font>'}
                if (record.data.EstadoFacturado=='Parcialmente') {
                    v = a+'<font color=brown>'+record.data.EstadoFacturado+'</font>'
                }
                if (record.data.EstadoFacturado == 'Totalmente') {
                    v = a+'<font color=blue>'+record.data.EstadoFacturado+'</font>'
                }
                if (record.data.EstadoFacturado == 'Excedido') {
                    v = a+'<font color=red>'+record.data.EstadoFacturado+'</font>'
                }

                return v;
            }
            return renderer;
        },



        recibidoConRemito: function(app) {
            var renderer = function(v, params, record) {
                var a= '<div onclick="app.publish(app.channels.apps+\''+ app +'\', {action:\'recibido\', value: '+record.data.Id+'})" style="background-image:url(images/lorry_go.png)" qtip="Ver Remitos" class="ux-cell-action"> </div>';

                if (record.data.EstadoRecibido=='Nada') {
                    v = '<font color=red>'+record.data.EstadoRecibido+'</font>'}
                if (record.data.EstadoRecibido=='Parcialmente') {
                    v = a+'<font color=blue>'+record.data.EstadoRecibido+'</font>'
                }
                if (record.data.EstadoRecibido == 'Totalmente') {
                    v = a+'<font color=green>'+record.data.EstadoRecibido+'</font>'
                }
                if (record.data.EstadoRecibido == 'Excedido') {
                    v = a+'<font color=black>'+record.data.EstadoRecibido+'</font>'
                }

                return v;
            }
            return renderer;
        },

     

        zeroFill: function(ceros, campo) {
            var renderer = function(v, params, record) {
                if (!record.data[campo])
                    return record.data[campo];
                var num = record.data[campo];
                if (num.toString().length >= ceros)
                    return num;
                var mask = Array(ceros+1).join('0');
                return (mask + num.toString()).slice(-(ceros));
            };
            return renderer;
        },

        miniatura: function(campo, modulo, modelo, size, desacarga, vacio) {
            var rend =  function(v, params, record) {
                if (v != '') {
                    var lnk = '';
                    if (desacarga) {
                        lnk = '<a href="/default/datagateway/downloadfile/model/' + modelo + '/m/' + modulo + '/field/' + campo + '/id/'+ v.replace('.','/ext/') + '"/>' + desacarga + '</a>';
                    }
                    return '<img src="/default/datagateway/getthumbnailfile/model/' + modelo + '/m/' + modulo + '/size/' + size + '/field/' + campo + '/id/'+ v.replace('.','/ext/') + '"/>' + lnk;
                } else {
                    if (vacio != undefined) return vacio;
                    return 'Vacio';
                }
            };
            return rend;
        },
        comprobanteRegistroPrecios: function(v, params, record) {

            if (!record.data.Comprobante) return '-';
            var app;
            if (record.data.TipoDeRegistroDePrecio == '1') {
                app = '/Facturacion/facturasCompras';
            } else  {
                app = '/Facturacion/facturasVentas';
            }
            var a = '<div onclick="app.publish(app.channels.apps+\''+ app +'\', {action:\'find\', value: '+record.data.Comprobante+'})" style="background-image:url(images/page_go.png)" qtip="Ver Factura" class="ux-cell-action"> </div>';
            var v = a+'<font color=blue>'+record.data.ComprobantePunto + ' - ' +record.data.Comprobante_cdisplay+'</font>'

            return v;
        },
        tipoDeLiquidaciones: function(v, params, r) {
            switch (r.data.TipoDeLiquidacion) {
                case '1':
                    return '<span style="color:black">' + r.data.TipoDeLiquidacion_cdisplay + '</span>';
                case '2':
                    return '<span style="color:#2554C7">' + r.data.TipoDeLiquidacion_cdisplay + '</span>';
                case '3':
                    return '<span style="color:#9DC209">' + r.data.TipoDeLiquidacion_cdisplay + '</span>';
                case '4':
                    return '<span style="color:#F87217">' + r.data.TipoDeLiquidacion_cdisplay + '</span>';
            }
        }


    };
}();