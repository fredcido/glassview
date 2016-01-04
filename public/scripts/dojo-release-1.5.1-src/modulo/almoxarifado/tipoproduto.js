/**
 * @version $Id: tipoproduto.js 1041 2013-11-08 17:58:00Z helion $
 */

dojo.require("modulo.padrao.geral");
dojo.require("modulo.padrao.grid");

dojo.provide('modulo.almoxarifado.tipoproduto');

dojo.declare( 'modulo.almoxarifado.tipoproduto', [modulo.padrao.geral, modulo.padrao.grid],
{
    constructor: function()
    {
    },

    initGrid: function()
    {
        objGeral.loading( true );
	
        document.getElementById( 'gridAlmoxarifadoTipoProduto' ).style.height= objGrid.gridHeight +"px";
        almoxarifadoTipoProduto.grid = new dhtmlXGridObject('gridAlmoxarifadoTipoProduto');
        almoxarifadoTipoProduto.grid.setHeader("#,Nome,Descrição");
        almoxarifadoTipoProduto.grid.attachHeader("#rspan,#text_filter,#text_filter");
        almoxarifadoTipoProduto.grid.setInitWidths( objGrid.idWidth + ",250,*");
        almoxarifadoTipoProduto.grid.setColAlign("center,left,left");
        almoxarifadoTipoProduto.grid.setColTypes("ro,ro,ro");
        almoxarifadoTipoProduto.grid.setColSorting("str,str,str");
        almoxarifadoTipoProduto.grid.setSkin( objGrid.theme );
        almoxarifadoTipoProduto.grid.enablePaging( true, objGrid.pagResults, objGrid.pagIndexes, 'divpagAlmoxarifadoTipoProdutoGrid', true, 'divpagAlmoxarifadoTipoProduto');
        almoxarifadoTipoProduto.grid.attachFooter( objGrid.tituloTotal + ",#cspan,{#stat_count}");
        almoxarifadoTipoProduto.grid.attachEvent( 'onRowDblClicked', almoxarifadoTipoProduto.edit );
        almoxarifadoTipoProduto.grid.init();
        almoxarifadoTipoProduto.grid.load( baseUrl + '/almoxarifado/tipo-produto/list', dojo.hitch( objGeral, 'loading', false ) , "json");
    },
    
    novo: function()
    {
        objGeral.createDialog( '/almoxarifado/tipo-produto/form/', 'Tipo de Produto' );
    },
    
    edit: function()
    {
        if ( !almoxarifadoTipoProduto.grid.getSelectedId() ) {
            
            objGeral.msgAlerta( objGeral.translate('Selecione o item para edição.') );
            return false;
        }
            
        objGeral.createDialog( '/almoxarifado/tipo-produto/edit/id/' + almoxarifadoTipoProduto.grid.getSelectedId(), 'Tipo de Produto' );
        
        return true;
    },
    
    atualizarGrid: function()
    {
        objGeral.atualizarGrids( [almoxarifadoTipoProduto.grid] );
    }
});

var almoxarifadoTipoProduto = new modulo.almoxarifado.tipoproduto();