Ext.ns( 'Apps' );

Apps.detailView = Ext.extend(RadDesktop.Module, {
    autoStart: true,
    appChannel: '/desktop/modules/js/commonApps/detailView.js',
    // requires: [
    //   '/js/ux/Ext.ux.HTML5Panel.js'
    // ],
    trayIcon: {
        icon: '/images/map.png',

        menu: [
            {
                text:'Buscar Dirección',
                handler: function() {
                    Ext.Msg.prompt('Buscar Dirección', 'Calle nro, ciudad, provincia', function(btn, text){
                        if (btn == 'ok'){
                            app.publish('/desktop/modules/js/commonApps/gmaps.js',
                                {
                                    action: 'searchAddress',
                                    address: text + ', Argentina'
                                }
                            );
                        }
                    });
                }
            }
            // ,{
            //     text:'video',
            //     handler: function() {
            //         win = app.getDesktop().createWindow({
            //             animCollapse: false,
            //             constrainHeader: true,
            //             title: 'Video Window',
            //             width: 740,
            //             height: 480,
            //             iconCls: 'icon-grid',
            //             shim: false,
            //             border: false,
            //             layout: 'fit',
            //             items: {
            //                 xtype: 'html5video',
            //                 ref: 'videoPanel', // put a reference in the Window
            //                 src: [{ // chrome and webkit-nightly (h.264)
            //                     src: 'http://studio.html5rocks.com/samples/video-player/hammock.ogv',
            //                     type: 'video/mp4'
            //                 }],
            //                 autobuffer: true,
            //                 autoplay: true,
            //                 controls: true
            //             }
            //         });
            //         win.show();
            //         // Hook up the provided preview tooltip to our TaskBar button
            //         win.videoPanel.getPreviewer().initTarget(win.taskButton.el);
            //     }
            // }
        ]
    },
    createDropTarget: function() {
         var drop = new Ext.dd.DropTarget('view-dropeable', {
            ddGroup : 'grillassistema',
            notifyDrop : function(dd, e, data) {
                var html = "<table>";
                for (e in data.selections[0].data)
                {
                    if (data.selections[0].data[e] instanceof Date) {
                        value = data.selections[0].data[e].format('d-m-Y');
                    } else {
                        if (data.selections[0].data[e+'_cdisplay'])
                        {
                            value = data.selections[0].data[e+'_cdisplay'];
                        } else {
                            if (e.indexOf('_cdisplay') == -1)
                                value = data.selections[0].data[e];
                            else value = '';
                        }
                    }
                    field = data.selections[0].fields.get(e)
                    if (field && field.header) titulo = field.header;
                    else titulo = e;
                    if (value != '')
                        html = html+'<tr><td><b>'+titulo+':</b></td><td>'+value+'</td></tr>';
                }
                html = html +'</table>';
                win = new Ext.Window({
                    title: 'Ver Datos',
                    width: 600,
                    html: html,
                    modal: false,
                    maxHeight: 700,
                    bodyStyle: 'padding: 10px;',
                    icon: Ext.Msg.INFO,
                    x:40,
                    y:40
                });
                win.show();
            }
        });

        var drop = new Ext.dd.DropTarget('mail-dropeable', {
            ddGroup : 'grillassistema',
            notifyDrop : function(dd, e, data) {
                var html = "<table>";
                for (e in data.selections[0].data)
                {
                    if (data.selections[0].data[e] instanceof Date) {
                        value = data.selections[0].data[e].format('d-m-Y');
                    } else {
                        if (data.selections[0].data[e+'_cdisplay'])
                        {
                            value = data.selections[0].data[e+'_cdisplay'];
                        } else {
                            if (e.indexOf('_cdisplay') == -1)
                                value = data.selections[0].data[e];
                            else value = '';
                        }
                    }
                    field = data.selections[0].fields.get(e)
                    if (field && field.header) titulo = field.header;
                    else titulo = e;
                    if (value != '')
                        html = html+'<tr><td><b>'+titulo+':</b></td><td>'+value+'</td></tr>';
                    else 
                        html = html+'<tr><td><b>'+titulo+':</b></td><td>Sin Datos</td></tr>';
                }
                html = html +'</table>';
                app.publish('/desktop/modules/js/commonApps/mail.js', {
                    action: 'launch',
                    destino: '',
                    asunto : '',
                    cuerpo : html
                });

            }
        });
    },
    startup: function() {
        var emailBtn = app.desktop.taskbar.trayPanel.add({
            icon: '/images/email_go.png',
            id: 'mail-dropeable',
            handler: function (){
                app.publish( '/desktop/notify',{
                    title: 'Ayuda',
                    iconCls: 'x-icon-information',
                    html: 'Arrastrando un registro de una grilla aqui puede enviarlo por mail'
                });
            }
        });
        var detailBtn =app.desktop.taskbar.trayPanel.add({
            icon: '/images/magnifier.png',
            id: 'view-dropeable',
            handler: function (){
                app.publish( '/desktop/notify',{
                    title: 'Ayuda',
                    iconCls: 'x-icon-information',
                    html: 'Arrastrando un registro de una grilla aqui puede ver los detalles'
                });
            }
        });

        this.createDropTarget.defer(300);

        // try {
        //     app.wamp.subscribe('/simple/chat/martin', function(topic, ev) {
        //         //console.log(ev);
        //         app.publish('/desktop/notify',ev);
        //     });
        // } catch (e) {
        //     console.warn(e.message);
        // }
    }
});

new Apps.detailView();