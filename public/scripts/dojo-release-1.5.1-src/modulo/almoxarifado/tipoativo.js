/**
 * @version $Id: tipo_ativo.js 218 2012-02-14 01:09:13Z helion $
 */

dojo.require("modulo.padrao.geral");
dojo.require("modulo.padrao.grid");

dojo.provide('modulo.almoxarifado.tipoativo');

dojo.declare( 'modulo.almoxarifado.tipoativo', [modulo.padrao.geral, modulo.padrao.grid],
{
    constructor: function()
    {
    },

    initGrid: function()
    {
        objGeral.loading( true );
	
        document.getElementById( 'gridAlmoxarifadoTipoAtivo' ).style.height= objGrid.gridHeight +"px";
        almoxarifadoTipoAtivo.grid = new dhtmlXGridObject('gridAlmoxarifadoTipoAtivo');
        almoxarifadoTipoAtivo.grid.setHeader("#,Nome,Descrição,Status");
        almoxarifadoTipoAtivo.grid.attachHeader("#rspan,#text_filter,#text_filter,#select_filter");
        almoxarifadoTipoAtivo.grid.setInitWidths( objGrid.idWidth + ",200,*,100");
        almoxarifadoTipoAtivo.grid.setColAlign("center,left,left,left");
        almoxarifadoTipoAtivo.grid.setColTypes("ro,ro,ro,ro");
        almoxarifadoTipoAtivo.grid.setColSorting("str,str,str,str");
        almoxarifadoTipoAtivo.grid.setSkin( objGrid.theme );
        almoxarifadoTipoAtivo.grid.enablePaging( true, objGrid.pagResults, objGrid.pagIndexes, 'divpagAlmoxarifadoTipoAtivoGrid', true, 'divpagAlmoxarifadoTipoAtivo');
        almoxarifadoTipoAtivo.grid.attachFooter( objGrid.tituloTotal + ",#cspan,#cspan,{#stat_count}");
        almoxarifadoTipoAtivo.grid.attachEvent( 'onRowDblClicked', almoxarifadoTipoAtivo.edit );
        almoxarifadoTipoAtivo.grid.init();
        almoxarifadoTipoAtivo.grid.load( baseUrl + '/almoxarifado/tipo-ativo/list', dojo.hitch( objGeral, 'loading', false ) , "json");
    },
    
    novo: function()
    {
        objGeral.createDialog( '/almoxarifado/tipo-ativo/form/', 'Tipo de Ativo' );
    },
    
    edit: function()
    {
        if ( !almoxarifadoTipoAtivo.grid.getSelectedId() ) {
            
            objGeral.msgAlerta( objGeral.translate('Selecione o item para edição.') );
            return false;
        }
            
        objGeral.createDialog( '/almoxarifado/tipo-ativo/edit/id/' + almoxarifadoTipoAtivo.grid.getSelectedId(), 'Tipo de Ativo' );
        
        return true;
    },
    
    atualizarGrid: function()
    {
        objGeral.atualizarGrids( [almoxarifadoTipoAtivo.grid] );
    }
});

var almoxarifadoTipoAtivo = new modulo.almoxarifado.tipoativo();