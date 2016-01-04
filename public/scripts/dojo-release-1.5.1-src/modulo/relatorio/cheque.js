dojo.require( 'modulo.padrao.geral' );
dojo.require( 'modulo.relatorio.padrao' );

dojo.provide( 'modulo.relatorio.cheque' );

dojo.declare( 'modulo.relatorio.cheque', [modulo.padrao.geral, modulo.relatorio.padrao],
{
    constructor: function()
    {
	this.urlTarget = '/relatorio/cheque/';
    }
});

var relatorioCheque = new modulo.relatorio.cheque();