dojo.require( 'modulo.padrao.geral' );
dojo.require( 'modulo.relatorio.padrao' );

dojo.provide( 'modulo.relatorio.ativo' );

dojo.declare( 'modulo.relatorio.ativo', [modulo.padrao.geral, modulo.relatorio.padrao],
{
    constructor: function()
    {
	this.urlTarget = '/relatorio/ativo/';
    }
});

var relatorioAtivo = new modulo.relatorio.ativo();