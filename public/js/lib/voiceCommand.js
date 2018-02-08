/**
 * Experimento JARVIS :D
 * @author Martin Alejandro Santangelo;
 */
Ext.ns( 'Mas' );

/** 
 * Escucha comandos de vos de un contexto json y ejecuta las acciones 
 */
Mas.voiceCommand = Ext.extend(Object,{
    final_transcript: '',
    globalContext: null,
    context: null,
    waitContext: 1000,
    escuchando: false, // indica si el reconocedor de voz se encuentra escuchando
    ready: true, // indica q esta listo para arrancar y no esta ejecutando
    constructor: function(config) {
        
        // this.session = null;
        t = this;
        Ext.apply(this,config||{});
        
        var SpeechRecognition = window.SpeechRecognition || 
                        window.webkitSpeechRecognition || 
                        window.mozSpeechRecognition || 
                        window.oSpeechRecognition || 
                        window.msSpeechRecognition;

        if (!SpeechRecognition) {
            app.publish('/desktop/showWarning', 'El navegador no tiene soporte para reconocimiento de voz, utilice una versión actualizado de Chrome')
            return;
        }

        this.finalize= function(){
            this.ready = true;
        };

        if (!this.scope) this.scope = this;
            
        this.recognition = new SpeechRecognition();
        this.recognition.continuous = false;
        this.recognition.interimResults = true;
        this.recognition.lang = 'es_AR';

        this.recognition.onstart = function() {
            console.log('onstart');
            t.escuchando = true;
            t.ready = false;
            t.final_transcript='';
            t.onStart();
        };


        this.recognition.onend = function() {
            console.log('onend');
            t.escuchando = false;
            t.onEnd();

            // Ejecutamos la accion 

            var palabras = Ext.util.Format.trim(t.final_transcript);

            if (palabras == '') {
                console.log('no dijo nada');
                // t.context = null;
                t.finalize();
                t.onEndExec();
                return;
            }

            console.log('dijo '+palabras);

            // salgo del contexto actual para volver al principal
            if (palabras == 'salir') {
                t.context = null;
                t.start(r);
            }

            //analizo la frase para ver si esta dentro de la funcionalidad de jarvis
            var frase = palabras.split(' ');
            var ac = t.context || t.globalContext;
            var termino;
            var r;

            for (var d = 0; d < frase.length; d++) {
                termino = frase[d];
                
                // existe el termino
                if (ac[termino] != undefined) {
                    ac = ac[termino];

                    if (Ext.isFunction(ac)) {
                        var parametro = frase.splice(d+1);

                        r = ac.call(t.scope,parametro);
                        if (typeof(r) == 'object') {
                            Ext.defer(function(){
                                this.start(r);
                            },t.waitContext, t);
                        } else {
                            t.finalize();
                            t.onEndExec();
                        }
                        break;
                    }
                } else {
                    if (Ext.isFunction(ac.fn)) {
                        var parametro = frase.splice(d);
                        r = ac.fn.call(t.scope,parametro);
                        
                        if (typeof(r) == 'object') {
                            Ext.defer(function(){
                                this.start(r);
                            },t.waitContext, t);
                        } else {
                            t.finalize();
                            t.onEndExec();
                        }
                        
                        break;
                    } else {
                        t.onNoRecognized();
                        t.finalize();
                        t.onEndExec();
                    } 
                    break;
                }
            }
            
        };
        this.recognition.onerror = function(){
            console.log('onerror');
            t.onEnd();
            t.onError();
        };

        this.recognition.onsoundstart = function() {
            t.onSoundStart();
        };

        this.recognition.onresult = function(event) {

            var interim_transcript = '';


            for (var i = event.resultIndex; i < event.results.length; ++i) {
                if (event.results[i].isFinal) {
                    t.final_transcript += event.results[i][0].transcript;
                } else {
                    interim_transcript += event.results[i][0].transcript;
                }
            }

            t.onResults(interim_transcript, t.final_transcript);      
        };
        
    },
    start: function(context) {
        if (context != undefined) {
            this.context = context; 
        }
        
        this.recognition.start();
    },
    stop: function() {
        this.recognition.stop();
    },
    abort: function() {
        this.recognition.abort();
    },
    onStart: function() {
        
    },
    onEnd: function() {

    },
    onEndExec: function() {

    },
    onSoundStart: function() {

    },
    onError: function(){

    },
    onNoRecognized: function(){

    },
    onResults: function(interim_res, final_res){

    },
    isListening: function() {
        return this.escuchando;
    },
    isReady: function() {
        return this.ready;
    },
    getDictation:function(){
        return this.final_transcript;
    },
    getContextName: function(){
        if (!this.context) return '';
        return this.context['@name'];
    }
});