// This is just a sample script. Paste your real code (javascript or HTML) here.
Ext.ns('Ext.ux');

/* -NOTICE-
 * For HTML5 video to work, your server must
 * send the right content type, for more info see:
 * https://developer.mozilla.org/En/HTML/Element/Video
 */
Ext.ux.HTML5VideoPanel = Ext.extend(Ext.Panel, {

    // Provide defaults for configurable tip sizes.
    tipWidth: 160,
    tipHeight: 96,

    autoHidePreview: false,

    constructor: function (config) {
        Ext.ux.HTML5VideoPanel.superclass.constructor.call(this, Ext.applyIf(config, {
            autoplay: false,
            controls: true,
            bodyStyle: 'background-color:#000;color:#fff',
            suggestChromeFrame: false
        }));
    },

    onRender: function () {
        var fallback = '';

        if (this.fallbackHTML) {
            fallback = this.fallbackHTML;
        } else {
            fallback = "Your browser doesn't support html5 video. ";

            if (Ext.isIE && this.suggestChromeFrame) {
                /* chromeframe requires that your site have a special tag in the header
                 * see http://code.google.com/chrome/chromeframe/ for details
                 */
                fallback += '<a>Get Google Chrome Frame for IE</a>';
            } else if (Ext.isChrome) {
                fallback += '<a>Upgrade Chrome</a>';
            } else if (Ext.isGecko) {
                fallback += '<a>Upgrade to Firefox 3.5</a>';
            } else {
                fallback += '<a>Get Firefox 3.5</a>';
            }
        }

        // Configure the body element to be a <video> element
        this.bodyCfg = Ext.copyTo({
            tag: 'video',
            children: []
        },
        this, 'poster,start,loopstart,loopend,playcount,autobuffer,loop');

        // Truthy params enables them
        if (this.autoplay) this.bodyCfg.autoplay = 1;
        if (this.controls) this.bodyCfg.controls = 1;

        // Handle multiple sources
        if (Ext.isArray(this.src)) {
            for (var i = 0, len = this.src.length; i < len; i++) {
                if (!Ext.isObject(this.src[i])) {
                    throw "source list passed to html5video panel must be an array of objects";
                }
                this.bodyCfg.children.push(
                Ext.applyIf({
                    tag: 'source'
                }, this.src[i]));
            }
            this.bodyCfg.children.push({
                html: fallback
            });
        } else {
            this.bodyCfg.src = this.src;
            this.bodyCfg.html = fallback;
        }
        Ext.ux.HTML5VideoPanel.superclass.onRender.apply(this, arguments);
        this.video = this.body;
    },

    onResize: function () {
        Ext.ux.HTML5VideoPanel.superclass.onResize.apply(this, arguments);
        Ext.apply(this.body.dom, this.body.getSize());
    },

    onDestroy: function () {
        Ext.ux.HTML5VideoPanel.superclass.onDestroy.apply(this, arguments);
        if (this.tooltip) {
            delete this.tipCtx;
            this.tooltip.destroy();
            Ext.TaskMgr.stop(this.tipUpdateTask);
        }
    },

    getPreviewer: function () {
        if (!this.tooltip) {
            this.tooltip = new Ext.ToolTip({
                anchor: 'bottom',
                autoHide: this.autoHidePreview,
                hideDelay: Ext.num(this.hidePreviewDelay, Number.MAX_VALUE),
                height: this.tipHeight,
                width: this.tipWidth,
                bodyCfg: {
                    tag: 'canvas',
                    width: this.tipWidth,
                    height: this.tipHeight
                },
                listeners: {
                    render: this.onTipRender,
                    show: this.onTipShow,
                    hide: this.onTipHide,
                    scope: this
                }
            });

            // Task to keep the tip updated while it is visible
            this.tipUpdateTask = {
                run: this.updatePreview,
                interval: 20,
                scope: this
            };
        }
        return this.tooltip;
    },

    onTipRender: function () {
        this.tipCtx = this.tooltip.body.dom.getContext('2d');
    },

    onTipShow: function () {
        Ext.TaskMgr.start(this.tipUpdateTask);
    },

    onTipHide: function () {
        Ext.TaskMgr.stop(this.tipUpdateTask);
    },

    updatePreview: function () {
        if (this.tipCtx) {
            this.tipCtx.drawImage(this.body.dom, 0, 0, this.tipWidth, this.tipHeight);
        }
    }
});
Ext.reg('html5video', Ext.ux.HTML5VideoPanel);