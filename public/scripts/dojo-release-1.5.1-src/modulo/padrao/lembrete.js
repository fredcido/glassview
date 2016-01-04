dojo.require( 'modulo.padrao.geral' );
dojo.require( 'modulo.padrao.grid' );

dojo.provide( 'modulo.padrao.lembrete' );

dojo.declare( 'modulo.padrao.lembrete' , [modulo.padrao.geral, modulo.padrao.grid] ,
{
    container: null,
    
    constructor: function()
    {
    },
    
    initGrid: function()
    {
        objGeral.loading( true );
	
        document.getElementById('gridDefaultLembrete').style.height= objGrid.gridHeight +"px";
        defaultLembrete.grid = new dhtmlXGridObject( 'gridDefaultLembrete' );
        defaultLembrete.grid.setHeader("#,Título,Lembrete,Fluxo,Nível,Data Prevista");
        defaultLembrete.grid.attachHeader("#rspan,#text_filter,#text_filter,#select_filter,#select_filter,#text_filter");
        defaultLembrete.grid.setInitWidths( objGrid.idWidth + ",320,*,200,200,150");
        defaultLembrete.grid.setColAlign("center,left,left,left,left,left");
        defaultLembrete.grid.setColTypes("ro,ro,ro,ro,ro,ro");
        defaultLembrete.grid.setColSorting("str,str,str,str,str,str");
        defaultLembrete.grid.setSkin( objGrid.theme );
        defaultLembrete.grid.enablePaging( true, objGrid.pagResults, objGrid.pagIndexes, 'divpagDefaultLembreteGrid', true, 'gridDefaultLembrete' );
        defaultLembrete.grid.attachFooter( objGrid.tituloTotal + ",#cspan,#cspan,#cspan,#cspan,{#stat_count}");
        defaultLembrete.grid.attachEvent( 'onRowDblClicked', defaultLembrete.edit );
        defaultLembrete.grid.init();
        defaultLembrete.grid.load( baseUrl + '/default/lembrete/list', dojo.hitch( objGeral, 'loading', false ) , "json");
    },
    
    novo: function()
    {
        objGeral.createDialog( '/default/lembrete/form/', objGeral.translate( 'Lembrete' ) );
    },
    
    edit: function()
    {
	if ( !defaultLembrete.grid.getSelectedId() ) {
	    
	    objGeral.msgAlerta( objGeral.translate('Selecione o item para edição.') );
            return false;
	}
	
	var fluxo = defaultLembrete.grid.getSelectedId().replace( /[0-9]/g, '' );
	
	if ( fluxo == 'R' ) {
	    
	    objGeral.msgAlerta( objGeral.translate( 'Lembrete recebido, não é possível editar.' ) );
            return false;
	}
	
	var id = defaultLembrete.grid.getSelectedId().replace( /[^0-9]/g, '' );
	
        objGeral.createDialog( '/default/lembrete/edit/id/' + id, objGeral.translate( 'Lembrete' ) );
        return true;
    },
    
    atualizarGrid: function() 
    { 
        objGeral.atualizarGrids( [defaultLembrete.grid] );
    },
    
    validate: function()
    {
	var elements = ['usuarios', 'perfis', 'nivel'];
	
	var valido = dojo.some( elements, 
	    function( item ) 
	    {
		return dojo.some( dojo.byId( item ).options, 
			    function( option )
			    {
				return option.selected;
			    }
			);
	    } 
	);
	    
	if ( valido )
	    return true;
	
	objGeral.msgErro( objGeral.translate( 'Selecione algum destino para o lembrete' ) );
	
	dijit.byId( 'tabs-lembrete' ).selectChild( 'tab-destino' );
	
	return false;
    },
    
    buscaLembretes: function()
    {
	if ( objGeral.empty( objAuth ) ) 
	    return false;

	dojo.xhrPost(
	    {
		url: baseUrl + '/default/lembrete/busca-lembretes/',
		handleAs: 'json',
		load: function ( response )
		{
		    if ( !objGeral.empty( response ) )
			defaultLembrete.showLembretes( response );
		}
	    }
	);
	    
	return true;
    },
    
    showLembretes: function( lembretes )
    {
	if ( $('#lembrete-container') )
	    $('#lembrete-container').remove();
	    
	container = $('<div />').attr( 'id', 'lembrete-container' ).appendTo( $('body') );
	
	dojo.forEach( lembretes, 
	    function( lembrete )
	    {
		var item = $('<div />').addClass( 'lembrete-item' ).hide();
		var titulo = $('<div />').addClass( 'lembrete-titulo' );
		var span = $('<span />').addClass( 'icon-toolbar-cancel' );
		var content = $('<div />').addClass( 'lembrete-content' );
		var rodape = $('<div />').addClass( 'lembrete-bottom' );
		
		// Se for urgente
		if ( lembrete.lembrete_nivel == 0 )
		    item.addClass( 'urgente' );
		
		item.attr( 'id', lembrete.lembrete_id );
		
		span.click( dojo.hitch( defaultLembrete, 'removeLembreteItem', item ) );
		
		var h2 = $('<h2 />').html( objGeral.translate( 'Lembrete' ) );
		h2.prepend( $('<span />').addClass( 'icon-toolbar-email' ) );
		
		var remetente = objGeral.empty( lembrete.usuario ) ? objGeral.translate( 'Sistema' ) : lembrete.usuario;
		
		titulo.append( h2 );
		titulo.append( span );
		
		content.append( $('<p />').html( '<b>' + objGeral.translate( 'Título' ) + ': </b>' + objGeral.truncate( lembrete.lembrete_titulo ) ) );
		content.append( $('<p />').html( '<b>' + objGeral.translate( 'De' ) + ': </b>' + objGeral.truncate( remetente ) ) );
		content.append( $('<br />') );
		content.append( $('<p />').html( '<i>"' + objGeral.truncate( lembrete.lembrete_msg, 30 ) + '"</i>' ) );
		
		rodape.html( objGeral.translate( 'LER' ) );
		rodape.click( dojo.hitch( defaultLembrete, 'abrirLembrete', item ) );
		
		item.append( titulo );
		item.append( content );
		item.append( rodape );
		
		item.slideDown( 'slow' );
		
		container.append( item );
		
		window.setTimeout( dojo.hitch( defaultLembrete, 'removeLembreteItem', item ), 30 * 1000 );
	    } 
	);
    },
    
    abrirLembrete: function( item )
    {
	this.lerLembrete( item.attr( 'id') );
	this.removeLembreteItem( item );
    },
    
    lerLembrete: function( id )
    {
	objGeral.createDialog( '/default/lembrete/detalha/id/' + id, objGeral.translate( 'Lembrete' ) );
    },
    
    removeLembreteItem: function( item )
    {
	item.slideUp( 'slow', 
	    function() 
	    { 
		item.remove(); 
		defaultLembrete.hideContainer();
	    }
	);
    },
    
    hideContainer: function()
    {
	if ( $('#lembrete-container div.lembrete-item').length < 1 )
	    $('#lembrete-container').remove();
    },
    
    detalhaLembreteGrid: function()
    {
	if ( !defaultLembrete.grid.getSelectedId() ) {
	    
	    objGeral.msgAlerta( objGeral.translate('Selecione o item para detalhar.') );
            return false;
	}
	
	var fluxo = defaultLembrete.grid.getSelectedId().replace( /[0-9]/g, '' );
	
	if ( fluxo == 'E' ) {
	    
	    objGeral.msgAlerta( objGeral.translate( 'Lembrete enviado, não é possível detalhar.' ) );
            return false;
	}
	
	var id = defaultLembrete.grid.getSelectedId().replace( /[^0-9]/g, '' );
	
	this.lerLembrete( id );
	
	return true;
    },
    
    mudarStatus: function( id )
    {
	dijit.byId( 'buttonbaixalembrete' ).setDisabled( true );
	
	var obj = {
	    url: baseUrl + '/default/lembrete/mudar-status/',
	    data: {
		id: id
	    },
	    handle: 'json',
	    callback: function( response )
	    {
		if ( !response.status ) {
		
		    objGeral.msgErro( objGeral.translate('Erro ao executar operação') );
		    dijit.byId( 'buttonbaixalembrete' ).setDisabled( false );
		} else
		    objGeral.closeGenericDialog();
	    }
	}
	
	objGeral.buscaAjax( obj );
    }
});

var defaultLembrete = new modulo.padrao.lembrete();