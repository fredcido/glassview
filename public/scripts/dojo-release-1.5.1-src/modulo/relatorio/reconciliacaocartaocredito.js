dojo.require( 'modulo.padrao.geral' );
dojo.require( 'modulo.relatorio.padrao' );

dojo.provide( 'modulo.relatorio.reconciliacaocartaocredito' );

dojo.declare( 'modulo.relatorio.reconciliacaocartaocredito', [modulo.padrao.geral, modulo.relatorio.padrao],
{
    constructor: function()
    {
    	this.urlTarget = '/relatorio/reconciliacao-cartao-credito/';
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

var relatorioReconciliacaoCartaoCredito = new modulo.relatorio.reconciliacaocartaocredito();