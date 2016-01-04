dojo.require( 'modulo.padrao.geral' );
dojo.require( 'modulo.relatorio.padrao' );

dojo.provide( 'modulo.relatorio.conta' );

dojo.declare( 'modulo.relatorio.conta', [modulo.padrao.geral, modulo.relatorio.padrao],
{
    constructor: function()
    {
	this.urlTarget = '/relatorio/conta/';
    },
    
    buscaTiposLancamento: function()
    {
	var projeto = dijit.byId( 'projeto_id' ).get( 'value' );
	
	objGeral.changeFilteringSelect(
	    'fn_tipo_lanc_id',
	    baseUrl + '/relatorio/conta/tipo-lancamento/id/',
	    projeto
	);
    }
});

var relatorioConta = new modulo.relatorio.conta();