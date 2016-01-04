dojo.require( 'modulo.padrao.geral' );

dojo.provide( 'modulo.relatorio.padrao' );

dojo.declare( 'modulo.relatorio.padrao', [modulo.padrao.geral],
{
    urlTarget: null,
    
    constructor: function()
    {
    },
    
    visualizar: function( formId )
    {
	if ( !this.validaForm( dijit.byId( formId ), this ) )
	    return false;
	
	var url = baseUrl + this.urlTarget + 'visualizar/';
		
	this.submitPopup( formId, url );
	
	return true;
    },
    
    gerarPdf: function( formId )
    {
	if ( !this.validaForm( dijit.byId( formId ), this ) )
	    return false;
	
	var url = baseUrl + this.urlTarget + 'gerar-pdf/';
	
	this.submitIframe( formId, url );
	
	return true;
    },
    
    gerarExcell: function( formId )
    {
	if ( !this.validaForm( dijit.byId( formId ), this ) )
	    return false;
	
	var url = baseUrl + this.urlTarget + 'gerar-excell/';
	
	this.submitIframe( formId, url );
		
	return true;
    },
    
    submitPopup: function( formId, url )
    {
	var form = dijit.byId( formId );
	form.set( 'action', url ).set( 'method', 'post' );
	    
	var popupName = this.parseId( url );
	var janela = objGeral.newWindow( url, popupName, 800, 600 );
	form.set( 'target', popupName );
	    
	form.submit();
	
	return janela;
    },
    
    
    submitIframe: function( formId, url )
    {
	var form = dijit.byId( formId );
	form.set( 'action', url ).set( 'method', 'post' );
	
	var iframeName = this.parseId( url );
	form.set( 'target', iframeName );
	
	if ( $('#' + iframeName ) )
	    $('#' + iframeName ).remove();
	
	$('<iframe />')
	 .attr( 'name', iframeName )
	 .attr( 'id', iframeName )
	 .hide()
	 .appendTo( 'body' );
		     
	 form.submit();
    },
    
    parseId: function( id )
    {
	return id.replace( /[^0-9a-z]/gi, '' ).toLowerCase();
    },
    
    validaDatas: function( ini, fim )
    {
	var dtIni = dijit.byId( ini );
	var dtFim = dijit.byId( fim );
	
	dtFim.constraints.min = !dtIni.get( 'value' ) ? undefined : dtIni.get( 'value' );
	dtIni.constraints.max = !dtFim.get( 'value' ) ? undefined : dtFim.get( 'value' );
    }
});