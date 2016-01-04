dojo.require( 'modulo.padrao.geral' );
dojo.require( 'modulo.relatorio.padrao' );

dojo.provide( 'modulo.relatorio.projeto' );

dojo.declare( 'modulo.relatorio.projeto', [modulo.padrao.geral, modulo.relatorio.padrao],
{
    constructor: function()
    {
    	this.urlTarget = '/relatorio/projeto/';
    },
    
    setConstraint: function( elem )
    {
    	switch ( elem.id )
    	{
    		case 'dt_start':
    			
    			dojo.mixin(
					dijit.byId('dt_end').constraints, 
					{
						min: dijit.byId('dt_start').get('value')
					}
				);
    			
    			break;
    			
    		case 'dt_end':
    			
    			dojo.mixin(
    				dijit.byId('dt_start').constraints,
    				{
    					max: dijit.byId('dt_end').get('value')
    				}
    			);
    			
    			break;
    	
    	}
    }
});

var relatorioProjeto = new modulo.relatorio.projeto();