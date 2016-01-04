dojo.require( 'modulo.padrao.geral' );
dojo.require( 'modulo.relatorio.padrao' );

dojo.provide( 'modulo.relatorio.contaprojeto' );

dojo.declare( 'modulo.relatorio.contaprojeto', [modulo.padrao.geral, modulo.relatorio.padrao],
{
    constructor: function()
    {
	this.urlTarget = '/relatorio/conta-projeto/';
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

var relatorioContaProjeto = new modulo.relatorio.contaprojeto();