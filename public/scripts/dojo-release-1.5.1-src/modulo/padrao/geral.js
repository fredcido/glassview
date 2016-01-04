
dojo.provide( 'modulo.padrao.geral' );

dojo.declare( 'modulo.padrao.geral',[],
{ 	
        constructor: function(){ },
        
	reqAjax : 0 ,

	timeout : 0 ,

	especReqAjax : new Array() ,

	loading: function ( show )
	{
            if ( show ) {

                this.reqAjax++;
                dojo.byId('loading').style.display = 'block';
                dojo.byId('loading_bkg').style.display = 'block';
            } else {

	        this.reqAjax--;
                if ( this.reqAjax < 1 ) {
                    
                    dojo.byId('loading').style.display = 'none';
                    dojo.byId('loading_bkg').style.display = 'none';
                    
                }
            }
	},
	
	fimCarregando: function ( )
	{
		dojo.fx.chain([
		dojo.fadeOut({
				node: 'carregando_sistema',
				duration: 1000,
				onEnd: function(){dojo.byId('carregando_sistema').style.display =  'none';}
				})
		]).play();
	},
	
	especLoading: function ( flag, div )
	{
	    if ( this.empty( dojo.byId( div ) ) )
		return false;

            if ( this.empty( this.especReqAjax[div] ) )
                this.especReqAjax[div] = 0;

            if ( flag ) {

                this.especReqAjax[div]++;
                document.getElementById(div).style.display = 'block';
            } else {

                this.especReqAjax[div]--;
                if ( this.especReqAjax[div] < 1 )
                        document.getElementById( div ).style.display = 'none';
            }

            return true;
	},
        
	buscaAjax: function ( obj, load )
	{
	    if ( !obj.noload ) {
		
		if ( load )
		    this.especLoading( true, load );
		else
		    this.loading( true );
	    }

            if ( obj.data && obj.data !== undefined ) {
                    // Post
                    dojo.xhrPost(
                    {
                            // A seguinte URL deve corresponder ao utilizado para testar o servidor.
                            url: escape( obj.url ),
                            content: obj.data,
                            handleAs: obj.handle,
			    form: obj.form,
                            load: function ( responseObject )
                            {
                                    if ( obj.callback && typeof( obj.callback ) !== undefined)
                                            obj.callback(responseObject);

				    if ( !obj.noload ) {
					if ( load )
						objGeral.especLoading( false, load );
					else
						objGeral.loading( false );

				    }
                                    return responseObject;
                            },
                            error: function ( response )
                            {
				if ( !obj.noMsg ) {
				    
				    if ( obj.msg )
					objGeral.msgErro( obj.msg );
				    else
					objGeral.msgErro( objGeral.translate('Erro ao executar operação') );
				}

				if ( !obj.noload ) {
				    
				    if ( load )
					objGeral.especLoading( false, load );
				    else
					objGeral.loading( false );
				}

				if ( obj.callbackError && typeof( obj.callbackError ) !== undefined)
				    obj.callbackError( response );

				return response;
                            }
                    }
                    );

            } else {
                    // Get
                    dojo.xhrGet(
                    {
                            // A seguinte URL deve corresponder ao utilizado para testar o servidor.
                            url: escape( obj.url ),
                            handleAs: obj.handle,
                            load: function ( responseObject )
                            {
				if ( obj.callback && typeof(obj.callback) !== undefined )
				    obj.callback( responseObject );

				if ( !obj.noload ) {
				    
				    if ( load )
					objGeral.especLoading( false, load );
				    else
					objGeral.loading( false );
				}

				return responseObject;
                            },
                            error: function ( response, ioArgs)
                            {
				if ( !obj.noMsg ) {
				    if ( obj.msg )
					objGeral.msgErro( obj.msg );
				    else
					objGeral.msgErro( objGeral.translate('Erro ao executar operação') );
				}

				if ( !obj.noload ) {
				    
				    if ( load )
					objGeral.especLoading( false, load );
				    else
					objGeral.loading( false );
				}
				    
				if ( obj.callbackError && typeof( obj.callbackError ) !== undefined)
				    obj.callbackError( response );    

				objGeral.loading( false );
                            }
                    }
                    );

            }
	},
	
	msgErro: function ( text )
	{
            this.showMsg( text, 'error' );
	},
	
	msgAlerta: function ( text )
	{
            this.showMsg( text, 'alert' );
	},
	
	msgSucesso: function ( text )
	{
            this.showMsg( text, 'confirm' );
	},
	
	showMsg: function ( text, type )
	{
            var divContainer = dojo.byId('msgDiv');
            dojo.removeClass( divContainer, 'error');
            dojo.removeClass( divContainer, 'alert');
            dojo.removeClass( divContainer, 'confirm');

            dojo.addClass( divContainer, type );

            dojo.byId('msgDivText').innerHTML = text;

            this.animateMsg( true );
	},
	
	animateMsg: function ( show )
	{
            var nodeTarget = dojo.byId('msgDiv');
            var nodeBack = dojo.byId('back_msg');

            if ( show ) {
                nodeBack.style.display = 'block';
                dojo.fx.wipeIn({node: nodeTarget}).play();
            } else {
                nodeBack.style.display = 'none';
                dojo.fx.wipeOut({node: nodeTarget}).play();
            }
	},
	
	empty: function ( mixed_var )
	{
		// !No description available for empty. @php.js developers: Please update the function summary text file.
		//
		// version: 911.1619
		// discuss at: http://phpjs.org/functions/empty    // +   original by: Philippe Baumann
		// +      input by: Onno Marsman
		// +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
		// +      input by: LH
		// +   improved by: Onno Marsman    // +   improved by: Francesco
		// +   improved by: Marc Jansen
		// +   input by: Stoyan Kyosev (http://www.svest.org/)
		// *     example 1: empty(null);
		// *     returns 1: true    // *     example 2: empty(undefined);
		// *     returns 2: true
		// *     example 3: empty([]);
		// *     returns 3: true
		// *     example 4: empty({});    // *     returns 4: true
		// *     example 5: empty({'aFunc' : function () { alert('humpty'); } });
		// *     returns 5: false

		var key;
		if ( ( mixed_var === '') ||
			 ( mixed_var === 0 ) ||
			 ( mixed_var == '' ) ||
			 ( mixed_var === '0' ) ||
			 ( mixed_var === 'null' ) ||
			 ( mixed_var == 'NULL'  ) ||
			 ( mixed_var == null  ) ||
			 ( mixed_var === false  ) ||
			 ( mixed_var == 'undefined'  ) ||
			 ( mixed_var == undefined  ) ||
			 ( mixed_var === 'undefined' )
		){
			return true;
		}

		if ( typeof mixed_var == 'object' ) {

			for ( key in mixed_var ) {
				return false;
			}
			return true;
		}
		return false;
	},
	
	submit: function( formId, objTela )
	{
            var objForm = dijit.byId( formId );
            
            if ( this.validaForm( objForm, objTela ) ) {

                this.loading( true );

                var self = this;

                var config = {
                    url: objForm.get('action'),
                    handleAs: "json",
                    handle: function()
                    {
                        self.loading( false );
                    },
                    load: function( response )
                    {
                        self.parseRetornoSubmit( response, objTela );
                    },
                    error: function( response )
                    {
                        self.msgErro( objGeral.translate('Erro ao executar operação') );
                    },
                    form: dojo.byId( formId ),
                    timeout: self.timeout
                }
                
                // Submete em ajax
                dojo.xhrPost( config );
            }
	},
	
	submitIframe: function ( form, objTela )
	{
            if ( this.validaForm( dijit.byId( form ), objTela ) ) {

                this.loading( true );
                dojo.io.iframe.send(
                    {
                        form: form,
                        handleAs: "json",
                        handle: function()
                        {
                            objGeral.loading( false );
                        },
                        load: function( response )
                        {
                            if ( typeof response.error == 'undefined' )
                                objGeral.msgErro( objGeral.translate('Erro ao executar operação') );
                            else
                                this.parseRetornoSubmit( response, objTela );
                        },
                        error: function()
                        {
                            objGeral.msgErro( objGeral.translate('Erro ao executar operação') );
                        },
                        timeout: this.timeout
                    }
                );
            }
	},

	parseRetornoSubmit: function( response, objTela )
	{
	    var messages;
	    
	    this.createErrors( response.errors );
	    
	    if ( !response.status ) {

		messages = !this.empty( response.description ) ? 
			    this.messages( response.description ) : 
			    objGeral.translate('Erro ao executar operação');

		this.msgErro( messages );

	    } else {
	    	
		messages = !this.empty( response.description ) ? 
			    this.messages( response.description ) :
                            objGeral.translate('Operação realizada com sucesso.');

		this.msgSucesso( messages );
		
		this.closeGenericDialog();
		
		// Executa funcao do retorno
		objTela.afterSubmit && objTela.afterSubmit( response );
	    }
	},
	
	createErrors: function( errors )
	{
	    $( 'ul.errors' ).remove();

	    for ( index in errors ) {

		if ( dijit.byId( index ) )
		    dijit.byId( index ).set( 'state', 'Error' );

		var ul = $('<ul />');

		ul.addClass('errors');
		ul.attr('id', 'error_' + index );

		var node = dijit.byId( index ) ? dijit.byId( index ).domNode : $( '#' + index );

		$( node ).closest( 'div.element' ).append( ul );

		for ( m in errors[index] ) {
		    var li = $('<li />').html( errors[index][m] );
		    ul.append( li );
		}
	    }
	},
        
        messages: function( messages )
        {
            var ul = '<ul class="messages">';
            
            for ( i in messages )
                ul += '<li>' + messages[i].message + '</li>';
            
            ul += '</ul>';
            
            return ul;
        },

	execFunction: function( fnName, params )
	{
		// Se existir o callback
		if ( typeof fnName == 'function' ) {
			return fnName( params );
		} else if ( typeof fnName == 'string' ) {

			var fn = window[fnName];
			if ( typeof fn == 'function' )
				return fn( params );
			else
				return false;
		}

		return false;
	},

	validaForm: function( form, objTela )
	{
            if ( !form.validate() ) {

                this.msgErro('Verifique todos os campos marcados antes de continuar.');
                return false;

            } else if ( objTela.validate && !objTela.validate() ) {
                return false;
            }

            return true;
	},
	
	changeFilteringSelect: function( filtering, caminho, value, valorpadrao )
	{
		if ( typeof valorpadrao == 'undefined' )
                    dijit.byId( filtering ).attr('displayedValue','');
		
		dijit.byId( filtering ).setDisabled( true );
		
		if ( !value )
		    return true;
		
		this.loading( true );

		newRead = new dojo.data.ItemFileReadStore(
                            {
				url: caminho + value
                            });

		newRead.fetch(
		{
		    onComplete:
		    function()
		    {
			objGeral.loading( false );
			dijit.byId( filtering ).setDisabled(false);
			dijit.byId( filtering ).focus();

			if ( valorpadrao != undefined )
			    dijit.byId( filtering ).attr('value', valorpadrao );

		    }
		});
		
		dijit.byId( filtering ).store = newRead;
		dijit.byId( filtering ).setDisabled(false);
                
	},

	newWindow: function( pagina, title, largura, altura )
	{
	   var config;
	   var titleWindow = dojo.trim( title || '' );
	   
	   if ( largura && altura ) {

		var esquerda = (screen.width - largura)/2;
		var topo = (screen.height - altura)/2;

		config = 'toolbar=no,location=no,fullscreen=yes,status=no,menubar=no,scrollbars=yes,resizable=no,height=' +
				altura + ', width=' + largura + ', top=' + topo + ', left=' + esquerda;

		return window.open( pagina, titleWindow , config);
	   } else {
		return window.open( pagina, titleWindow);
	   }
	},

	atualizarGrids: function( grids )
	{
	    dojo.map( grids,
		    function( item )
		    {
			if ( item.reload )
			    item.reload();
		    }
	    );
	},

	deleteRow: function( tr )
	{
	    // Destroi todos widgets dentro da linha
	    dojo.forEach( dijit.findWidgets( $(tr).html() ), function( node ){node.destroy();});
	    // Destroi a linha
	    $(tr).remove();

	    return true;
	},

	truncate: function( str, size )
	{
		size = size || 25;
		
		if ( str.length > size )
		str = str.substr(0, size) + '...';

		return str;
	},

	toCamelCase: function( str )
	{
		return str.replace(/(\-[a-z])/g, function($1){return $1.toUpperCase().replace('-','');});
	},

	parseId: function( controller )
	{
		var id = controller.replace(/^\/(.+?)\/$/gi, '$1');
		return id.replace('/','-')
	},

	openTab: function( url, title, callback, iconClass )
	{
	    if ( this.empty( url ) ) {

		    this.msgErro('Opera&ccedil;&atilde;o inv&aacute;lida para abertura de aba.');
		    return false;
	    }

	    //this.requireModule( url );

	    var urlClean = this.parseId( url );

	    var id = 'tab' + urlClean;

	    var tabs = dijit.byId( "contentCenter" );

	    var pane;

	    if ( dijit.byId( id ) ) {

			pane = dijit.byId( id );
	    } else {

		    //var iconClass = 'customIcon icon-' + urlClean;

		    pane = new dijit.layout.ContentPane({
			    title: title,
			    id: id,
			    iconClass: iconClass,
			    closable: "true",
			    href: baseUrl + url,
			    onDownloadEnd: dojo.hitch( callback ),
			    onDownloadError: function()
			    {
				    this.msgErro('Erro ao abrir tela.');
			    }
		    });

		    tabs.addChild( pane );
	    }

	    tabs.selectChild( pane );
	    return true;
	},
        
	novo: function()
	{
		
	},
	
	edit: function()
	{
		
	},

	requireModule: function( strController )
	{
		var StrCtrl = strController.split('/');
		StrCtrl = '/' + StrCtrl[1] + '/' + StrCtrl[2];

		var funcExec = 'objGeral.get' + StrCtrl.replace(/(?:^\w|[A-Z]|\b\w)/g, function(letter, index) {
			return index == 0 ? letter.toLowerCase() : letter.toUpperCase();
		  }).replace(/\/+/g, '');
		  
		  return eval( funcExec) ? eval( funcExec+'();' ) : false;
	},
	
	afterSubmit: function( response )
	{
		this.atualizarGrid();
	},
	
	genericDialog: 'genericDialog',
	
	createdDialogs: new Array(),
	
	createDialog: function( url, title, callback )
	{
            if ( this.empty( url ) ) {
                
                this.msgErro('Opera&ccedil;&atilde;o inv&aacute;lida para abertura de janela.');
                return false;
            }
	    
            this.clearDialogs();

            var dialog = new dijit.Dialog({
                                    title: title,
                                    href: baseUrl + url,
                                    onDownloadEnd: function()
                                    {
                                        setTimeout(
                                        function()
                                        {
                                            objGeral.execFunction( callback );
                                        },
                                        50);
                                    },
                                    onDownloadError: function()
                                    {
                                        objGeral.msgErro('Opera&ccedil;&atilde;o inv&aacute;lida para abertura de janela.');
                                    },
                                    onHide: dojo.hitch( this, 'closeGenericDialog' )
                               });
                         
            dialog._onKey = function(evt) { 
                
                if(evt.keyCode == dojo.keys.ESCAPE || evt.keyCode == dojo.keys.ENTER‎){
                    
                    dojo.stopEvent(evt);
                }
            };
   
            this.createdDialogs.push( dialog );

            dialog.show();
            
            return dialog;
            
	},
	
	closeGenericDialog: function()
	{
		var index = this.createdDialogs.length - 1;

		if ( !this.empty( this.createdDialogs[index] ) ) {

			var item = this.createdDialogs[index];

			item.hide();

			setTimeout(
			function()
			{
			    if ( !item.open )
				item.destroyRecursive();
			},
			500);

			this.createdDialogs.splice( index, 1 );
		}
	},
	
	getCurrentDialog: function()
	{
	    var index = this.createdDialogs.length - 1;
	    if ( !this.empty( this.createdDialogs[index] ) )
		return this.createdDialogs[index];
	    else
		return null;
	},
	
	clearDialogs: function()
	{
	    var self = this;
	    dojo.forEach( this.createdDialogs,
		    function ( item, index )
		    {
			    if ( !item.attr('open') ) {
				
				item.destroyRecursive();
				item.destroy();
				self.createdDialogs.splice(index, 1 );
			    }
		    }
	    );
	},
        
	centerDialog: function()
	{
		var index = this.createdDialogs.length - 1;
		if ( !this.empty( this.createdDialogs[index] ) ) {
			this.createdDialogs[index].layout();
		}
	},
	
	logout: function()
	{
	    location.href = baseUrl + '/auth/logout';
	},
	
	setRequired: function( widget, required )
	{
	    widget = typeof widget == 'string' ? dijit.byId( widget ) : widget;
	    required = required ? true : false;
	    
	    var id = widget.id;
	    
	    widget.set( 'required', required );
	    
	    var label = $( 'label[for=' + id +']' );
					 
	    if ( required )
		label.removeClass( 'optional' ).addClass( 'required' ).html( label.html() + '*' );
	    else
		label.removeClass( 'required' ).addClass( 'optional' ).html( label.html().replace( '*', '' ) );
	},
	
	sortCurrency: function( firstValue, anotherValue, order )
	{
	    firstValue = this.toFloat( firstValue );
	    anotherValue = this.toFloat( anotherValue );
	    
	    if ( order == 'asc' )
		return firstValue > anotherValue ? 1 : -1;
	    else 
		return firstValue > anotherValue ? -1 : 1;
		
	},
    
	
	toFloat: function( num )
	{
	    num = num.toString().replace(/[^-0-9,]/g, '').replace( ',', '.' )
	    return parseFloat( num );   
	},
        
        translate: function ( str )
        {
            return ( this.empty( objTranslate[str] ) ? str : objTranslate[str] );
        },
        
        formateDate: function( data, mascara ){
            
            if(!mascara )
                mascara = 'yyyy-MM-dd';
            
            return dojo.date.locale.format( 
                data, 
                {
                    datePattern: mascara,
                    selector: 'date' 
                }
            );
            
        }

});

var objGeral = new modulo.padrao.geral();