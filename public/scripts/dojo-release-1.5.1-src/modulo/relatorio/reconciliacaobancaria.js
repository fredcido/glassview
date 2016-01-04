dojo.require( 'modulo.padrao.geral' );
dojo.require( 'modulo.relatorio.padrao' );

dojo.provide( 'modulo.relatorio.reconciliacaobancaria' );

dojo.declare( 'modulo.relatorio.reconciliacaobancaria', [modulo.padrao.geral, modulo.relatorio.padrao],
{
    constructor: function()
    {
	this.urlTarget = '/relatorio/reconciliacao-bancaria/';
    }
});

var relatorioReconciliacaoBancaria = new modulo.relatorio.reconciliacaobancaria();