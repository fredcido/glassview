dojo.require( 'modulo.padrao.geral' );
dojo.require( 'modulo.padrao.grid' );

dojo.provide( 'modulo.padrao.lembreteconfig' );

dojo.declare( 'modulo.padrao.lembreteconfig' , [modulo.padrao.geral, modulo.padrao.grid] ,
{
    container: null,
    
    constructor: function()
    {
    },
    
    buscaConfigLembretes: function()
    {
	var tipo = dijit.byId( 'lembrete_config_tipo' ).get( 'value' );
	
	if ( objGeral.empty( tipo ) ) {
	    
	    dijit.byId( 'perfis' ).setValue( [] );
	    return false;
	}
	
	var obj = {
	    url: baseUrl + '/default/lembrete-config/list-perfis/',
	    data: {
		tipo: tipo
	    },
	    handle: 'json',
	    callback: function( response )
	    {
		dijit.byId( 'perfis' ).setValue( response );
	    }
	}
	
	objGeral.buscaAjax( obj );
	
	return true;
    },
    
    afterSubmit: function()
    {
	
    }
});

var defaultLembreteConfig = new modulo.padrao.lembreteconfig();