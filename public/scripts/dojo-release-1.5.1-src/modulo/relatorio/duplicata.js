dojo.require( 'modulo.padrao.geral' );
dojo.require( 'modulo.relatorio.padrao' );

dojo.provide( 'modulo.relatorio.duplicata' );

dojo.declare( 'modulo.relatorio.duplicata', [modulo.padrao.geral, modulo.relatorio.padrao],
{
    constructor: function()
    {
	this.urlTarget = '/relatorio/duplicata/';
    }
});

var relatorioDuplicata = new modulo.relatorio.duplicata();