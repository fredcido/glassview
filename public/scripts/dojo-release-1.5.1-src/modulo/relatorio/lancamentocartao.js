dojo.require( 'modulo.padrao.geral' );
dojo.require( 'modulo.relatorio.padrao' );

dojo.provide( 'modulo.relatorio.lancamentocartao' );

dojo.declare( 'modulo.relatorio.lancamentocartao', [modulo.padrao.geral, modulo.relatorio.padrao],
{
    constructor: function()
    {
	this.urlTarget = '/relatorio/lancamento-cartao/';
    }
});

var relatorioLancamentoCartao = new modulo.relatorio.lancamentocartao();