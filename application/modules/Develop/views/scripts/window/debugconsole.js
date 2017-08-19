Ext.ns( 'Apps' );

Apps.<?=$this->name?> = Ext.extend(RadDesktop.Module, {
    autoStart: true,
    title: '<?=$this->title?>',
    appChannel: '/desktop/modules<?=$_SERVER['REQUEST_URI']?>',
    requires: [
      '/js/ace/ace.js'
    ],


    eventfind: function (ev) {
        this.createWindow();
    },

    eventsearch: function (ev) {
        this.createWindow();
    },

    eventlaunch: function(ev) {
		this.createWindow();
    },

    eventservererror: function(ev) {
        var win = this.createWindow();
        win.items.get(0).setActiveTab(1);
        var defaultData = {
            'date': ev.date,
            'title': ev.title,
            'error': ev.error,
            
        };
        var p = new this.store.recordType(defaultData, ev.error); // create new record

        this.store.insert(0, p); // insert a new record into the store (also see add)
        this.ErrorGrid.getSelectionModel().selectFirstRow();
    },
	
    createWindow: function() {
        var win = app.desktop.getWindow(this.id+'-win');
        if ( !win ) {
            win = this.create();
		}
        win.show();
        return win;
    },
	
    create: function() {
	defaultWinCfg = {
            id: this.id+'-win',
            title: this.title,
            width: 800,
            maximized: true,
            height: 600,
            iconCls: 'icon-grid',
            border:  false,
            shim: false,
            closeAction: 'hide',
            animCollapse: false,
            layout: 'fit',
            items: [
                this.renderContent()
            ]
        };

        return app.desktop.createWindow(defaultWinCfg);
    },
    
    renderContent: function () {
        return {
            xtype: 'tabpanel',
            activeTab: 0,
            items: [
                this.renderPhpDebug(),
                this.renderErrorTrace()
            ]
        }
    },

    renderErrorTrace: function() {
        this.store = new Ext.data.ArrayStore({
            // store configs
            autoDestroy: true,
            storeId: 'errorsStore',
            // reader configs
            idIndex: 0,
            fields: [
                {name: 'date', dataIndex:'date', type: 'date', dateFormat: 'd/m/Y h:i:s'},
               'error',
               'title'
               
            ]
        });

        this.ErrorGrid = new Ext.grid.GridPanel({
            layout: 'fit',
            border: true,
            margins: '0 0 5 0',
            flex:1,
            autoExpandColumn: 'title',
            store: this.store ,
            columns: [{
                    id       :'date',
                    header   : 'Fecha', 
                    width    : 160,
                },
                {
                    id       :'error',
                    header   : 'Codigo', 
                    width    : 160,
                },{
                    id       :'title',
                    header   : 'Descripcion', 
                    width    : 160,
                }
            ]
        });

        this.tabsErrores = new Ext.Panel({
            flex: 2,
            items: [{
                xtype: 'box',
                autoEl: {
                    tag: 'iframe',
                    style: 'height: 100%; width: 100%',
                    src: '' 
                }
            }]
        });

        this.ErrorGrid.getSelectionModel().on('rowselect', function(rs, i, row){
            this.tabsErrores.items.get(0).el.dom.src = '/error/getajaxerror/number/'+row.data.error;
        }, this);

        

        return {
            title: 'Errors',
            layout: {
                type:'vbox',
                padding:'5',
                align : 'stretch',
            },
            items:[
                this.ErrorGrid,
                this.tabsErrores
            ]
        };
    },

    log: function(text)
    {
        cont = this.PhpConsole.getValue();
        if (cont != '') cont += '\n----------------------------------------------\n';
        this.PhpConsole.setValue(cont + text);
    },

    ejecutar: function (qcodigo)
    {

        if (this.modo == 'php') {
            Rad.callRemoteJsonAction({
                url:'/Develop/debugconsole/execute/',
                method: 'POST',
                params: {codigo: qcodigo },
                scope: this,
                success: function( result) {
                    this.log(result.html); 
                },
                failure: function(response) {
                    
                    window.app.desktop.showMsg({
                        title: 'Error',
                        manager: window.app.desktop.getManager(),
                        renderTo: 'x-desktop',
                        width: 600,
                        msg: response.msg,
                        modal: true,
                        icon: Ext.Msg.ERROR,
                        buttons: Ext.Msg.OK
                    });
                    
                }
            });    
        } else {
            eval(qcodigo);
        }
        
    },
    
    renderPhpDebug: function () {

        this.PhpConsole = new Ext.form.TextArea({
            style:"color: #E6E1DC; background-color: #3d3d3d; background-image: -moz-linear-gradient(left, #3D3D3D, #333); background-image: -ms-linear-gradient(left, #3D3D3D, #333); background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#3D3D3D), to(#333)); background-image: -webkit-linear-gradient(left, #3D3D3D, #333); background-image: -o-linear-gradient(left, #3D3D3D, #333); background-image: linear-gradient(left, #3D3D3D, #333); background-repeat: repeat-x; border-right: 1px solid #4d4d4d; bext-shadow: 0px 1px 1px #4d4d4d; bolor: #222;font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', 'Consolas', 'source-code-pro', monospace;font-size: 12px;line-height: normal;",
            fieldLabel     : 'consola',
            anchor         : '100%',
            height         : '100%',
            name           : 'codigo'
        });
        this.PhpEditor = new Ext.Panel({
            layout: 'fit',
            title: 'Codigo',
            border: true,
            layout: 'fit',
            flex:1,
            bodyStyle:'padding:5px 5px 5px',
            html:"<?=$this->code?>",
            tbar: [{
                    tooltip: 'Ejecutar (F10)',
                    icon:'images/lightning_go.png',
                    scope: this,
                    handler: function() {
                        this.ejecutar(this.editor.getValue());
                    }
                },'-',{
                    tooltip: 'Limpiar consola',
                    icon:'images/page_delete.png',
                    
                    handler: function () {
                        this.PhpConsole.reset();
                    },
                    scope: this
                },'-',{
                    text: 'Modo',
                    menu: {        // <-- submenu by nested config object
                        items: [
                            // stick any markup in a menu
                            '<b class="menu-title">Selecione un Modo</b>',
                            {
                                text: 'PHP',
                                checked: true,
                                scope: this,
                                group: 'lenguaje',
                                checkHandler: function(){
                                    this.editor.getSession().setMode("ace/mode/php");
                                    this.modo = 'php';
                                }
                            }, {
                                text: 'JavaScript',
                                checked: false,
                                scope: this,
                                group: 'lenguaje',
                                checkHandler: function(){
                                    this.editor.getSession().setMode("ace/mode/javascript");
                                    this.modo = 'javascript';
                                }
                            }
                        ]
                    }
                }]
        });

        this.PhpEditor.on('afterrender', function() {
            ace.config.set("modePath", "/js/ace/");
            ace.config.set("workerPath", "js/ace/");
            ace.config.set("themePath", "js/ace/");

            this.editor  = ace;
            this.editor  = ace.edit(this.PhpEditor.body.dom);
            this.editor.setTheme("ace/theme/ambiance");
            this.editor.getSession().setMode("ace/mode/php");
            this.editor.setValue("<?='<?php\n'.$this->code?>");

            this.modo = 'php';

            var t = this;
            this.editor.commands.addCommand({
                name: 'ejecutar',
                bindKey: {win: 'F10',  mac: 'F10'},
                exec: function(editor) {
                        t.ejecutar(editor.getValue());    
                },
                readOnly: true // false if this command should not apply in readOnly mode
            });
        },this);

        
        return {
            title: 'Php Console',
            defaults: {
                border: false
            },
            layout: {
                type:'hbox',
                padding:'5',
                align : 'stretch',
            },
            items:[
                this.PhpEditor,
                {
                    title: 'Consola',
                    flex:1,
                    tools: [
                        { id:'gear' }
                    ],
                    margins: '0 0 0 5',
                    layout:'anchor',
                    items: [
                        this.PhpConsole
                    ]
                }
            ]
        }
    }
});

new Apps.<?=$this->name?>();