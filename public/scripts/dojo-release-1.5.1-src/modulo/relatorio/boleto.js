dojo.require( 'modulo.padrao.geral' );
dojo.require( 'modulo.relatorio.padrao' );

dojo.provide( 'modulo.relatorio.boleto' );

dojo.declare( 'modulo.relatorio.boleto', [modulo.padrao.geral, modulo.relatorio.padrao],
{
    constructor: function()
    {
	this.urlTarget = '/relatorio/boleto/';
    }
});

var relatorioBoleto = new modulo.relatorio.boleto();