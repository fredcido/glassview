dojo.require( 'modulo.padrao.geral' );
dojo.require( 'modulo.padrao.grid' );

dojo.provide( 'modulo.almoxarifado.situacaoativo' );

dojo.declare( 'modulo.almoxarifado.situacaoativo', [modulo.padrao.geral, modulo.padrao.grid],
{
    constructor: function()
    {
    },

    initGrid: function()
    {
        objGeral.loading( true );
        
        almoxarifadoSituacaoAtivo.gridHeader = '#,'+
                                                objGeral.translate('Nome')+','+
                                                objGeral.translate('Descrição');
                                            
	document.getElementById( 'gridAlmoxarifadoSituacaoAtivo' ).style.height= objGrid.gridHeight +"px";
        almoxarifadoSituacaoAtivo.grid = new dhtmlXGridObject('gridAlmoxarifadoSituacaoAtivo');
        almoxarifadoSituacaoAtivo.grid.setHeader( almoxarifadoSituacaoAtivo.gridHeader );
        almoxarifadoSituacaoAtivo.grid.attachHeader("#rspan,#text_filter,#text_filter");
        almoxarifadoSituacaoAtivo.grid.setInitWidths( objGrid.idWidth + ",200,*");
        almoxarifadoSituacaoAtivo.grid.setColAlign("center,left,left");
        almoxarifadoSituacaoAtivo.grid.setColTypes("ro,ro,ro");
        almoxarifadoSituacaoAtivo.grid.setColSorting("str,str,str");
        almoxarifadoSituacaoAtivo.grid.setSkin( objGrid.theme );
        almoxarifadoSituacaoAtivo.grid.enablePaging( true, objGrid.pagResults, objGrid.pagIndexes, 'divpagAlmoxarifadoSituacaoAtivoGrid', true, 'divpagAlmoxarifadoSituacaoAtivo');
        almoxarifadoSituacaoAtivo.grid.attachFooter( objGrid.tituloTotal + ",#cspan,{#stat_count}");
        almoxarifadoSituacaoAtivo.grid.attachEvent( 'onRowDblClicked', almoxarifadoSituacaoAtivo.edit );
        almoxarifadoSituacaoAtivo.grid.init();
        almoxarifadoSituacaoAtivo.grid.load( baseUrl + '/almoxarifado/situacao-ativo/list', dojo.hitch( objGeral, 'loading', false ) , "json");
    },
    
    novo: function()
    {
        objGeral.createDialog( 
                '/almoxarifado/situacao-ativo/form/', 
                objGeral.translate('Situação de Ativo')
            );
    },
    
    edit: function()
    {
        if ( !almoxarifadoSituacaoAtivo.grid.getSelectedId() ) {
            
            objGeral.msgAlerta( objGeral.translate('Selecione o item para edição.') );
            return false;
        }
            
        objGeral.createDialog( 
                '/almoxarifado/situacao-ativo/edit/id/' + almoxarifadoSituacaoAtivo.grid.getSelectedId(), 
                objGeral.translate('Situação de Ativo')
            );
        
        return true;
    },
    
    atualizarGrid: function()
    {
        objGeral.atualizarGrids( [almoxarifadoSituacaoAtivo.grid] );
    }
});

var almoxarifadoSituacaoAtivo = new modulo.almoxarifado.situacaoativo();