dojo.require( 'modulo.padrao.geral' );
dojo.require( 'modulo.relatorio.padrao' );

dojo.provide( 'modulo.relatorio.transferencia' );

dojo.declare( 'modulo.relatorio.transferencia', [modulo.padrao.geral, modulo.relatorio.padrao],
{
    constructor: function()
    {
	this.urlTarget = '/relatorio/transferencia/';
    }
});

var relatorioTransferencia = new modulo.relatorio.transferencia();