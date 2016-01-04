dojo.require( 'modulo.padrao.geral' );
dojo.require( 'modulo.relatorio.padrao' );

dojo.provide( 'modulo.relatorio.faturacartao' );

dojo.declare( 'modulo.relatorio.faturacartao', [modulo.padrao.geral, modulo.relatorio.padrao],
{
    constructor: function()
    {
	this.urlTarget = '/relatorio/fatura-cartao/';
    }
    
});

var relatorioFaturaCartao = new modulo.relatorio.faturacartao();