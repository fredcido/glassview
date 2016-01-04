dojo.require( 'modulo.padrao.geral' );
dojo.require( 'modulo.relatorio.padrao' );

dojo.provide( 'modulo.relatorio.estoque' );

dojo.declare( 'modulo.relatorio.estoque', [modulo.padrao.geral, modulo.relatorio.padrao],
{
    constructor: function()
    {
	this.urlTarget = '/relatorio/estoque/';
    }
});

var relatorioEstoque = new modulo.relatorio.estoque();