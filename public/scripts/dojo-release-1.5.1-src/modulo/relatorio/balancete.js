dojo.require( 'modulo.padrao.geral' );
dojo.require( 'modulo.relatorio.padrao' );

dojo.provide( 'modulo.relatorio.balancete' );

dojo.declare( 'modulo.relatorio.balancete', [modulo.padrao.geral, modulo.relatorio.padrao],
{
    constructor: function()
    {
	this.urlTarget = '/relatorio/balancete/';
    }
});

var relatorioBalancete = new modulo.relatorio.balancete();