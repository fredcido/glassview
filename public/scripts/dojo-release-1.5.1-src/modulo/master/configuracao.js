dojo.require( 'modulo.padrao.geral' );
dojo.require( 'modulo.padrao.grid' );

dojo.provide( 'modulo.master.configuracao' );

dojo.declare( 'modulo.master.configuracao' , [modulo.padrao.geral],
{
    constructor: function()
    {
    },
    
    
    afterSubmit: function( response )
    {
        if ( response.status )
            history.go( 0 );
    }
});

var masterConfiguracao = new modulo.master.configuracao();