dojo.require( 'modulo.padrao.geral' );
dojo.require( 'modulo.relatorio.padrao' );

dojo.provide( 'modulo.relatorio.timeline' );

dojo.declare( 'modulo.relatorio.timeline', [modulo.padrao.geral, modulo.relatorio.padrao],
{
    /**
     * 
     */
    constructor: function()
    {
    	this.urlTarget = '/relatorio/timeline/';
    },
    
    /**
     * @
     */
    setConstraint: function( elem )
    {
    	switch ( elem.id )
    	{
    		case 'dt_inicio':
    			
    			dojo.mixin(
					dijit.byId('dt_fim').constraints, 
					{
						min: dijit.byId('dt_inicio').get('value')
					}
				);
    			
    			break;
    			
    		case 'lanc_dt_fim':
    			
    			dojo.mixin(
    				dijit.byId('dt_inicio').constraints,
    				{
    					max: dijit.byId('dt_fim').get('value')
    				}
    			);
    			
    			break;
    	
    	}
    }
});

var relatorioTimeline = new modulo.relatorio.timeline();