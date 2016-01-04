dojo.require( 'modulo.padrao.geral' );
dojo.require( 'modulo.padrao.grid' );

dojo.provide( 'modulo.almoxarifado.ativo' );

dojo.declare( 'modulo.almoxarifado.ativo', [modulo.padrao.geral, modulo.padrao.grid],
{
    constructor: function()
    {
    },

    initGrid: function()
    {
        objGeral.loading( true );
	
        almoxarifadoAtivo.gridHeader = '#,'+
                                    objGeral.translate('Nome')+','+
                                    objGeral.translate('Filial')+','+
                                    objGeral.translate('Patrimônio')+','+
                                    objGeral.translate('Tipo Ativo')+','+
                                    objGeral.translate('Situação Ativo')+','+
                                    objGeral.translate('Status');
                        
        document.getElementById( 'gridAlmoxarifadoAtivo' ).style.height= objGrid.gridHeight +"px";
        almoxarifadoAtivo.grid = new dhtmlXGridObject('gridAlmoxarifadoAtivo');
        almoxarifadoAtivo.grid.setHeader( almoxarifadoAtivo.gridHeader );
        almoxarifadoAtivo.grid.attachHeader("#rspan,#text_filter,#select_filter,#text_filter,#select_filter,#select_filter,#select_filter");
        almoxarifadoAtivo.grid.setInitWidths( objGrid.idWidth + ",*,300,200,200,200,100");
        almoxarifadoAtivo.grid.setColAlign("center,left,left,left,left,left,left");
        almoxarifadoAtivo.grid.setColTypes("ro,ro,ro,ro,ro,ro,ro");
        almoxarifadoAtivo.grid.setColSorting("str,str,str,str,str,str,str");
        almoxarifadoAtivo.grid.setSkin( objGrid.theme );
        almoxarifadoAtivo.grid.enablePaging( true, objGrid.pagResults, objGrid.pagIndexes, 'divpagAlmoxarifadoAtivoGrid', true, 'divpagAlmoxarifadoAtivo');
        almoxarifadoAtivo.grid.attachFooter( objGrid.tituloTotal + ",#cspan,#cspan,#cspan,#cspan,#cspan,{#stat_count}");
        almoxarifadoAtivo.grid.attachEvent( 'onRowDblClicked', almoxarifadoAtivo.edit );
        almoxarifadoAtivo.grid.init();
        almoxarifadoAtivo.grid.load( baseUrl + '/almoxarifado/ativo/list', dojo.hitch( objGeral, 'loading', false ) , "json");
    },
    
    novo: function()
    {
        objGeral.createDialog( 
                '/almoxarifado/ativo/form/', 
                objGeral.translate('Ativo')
            );
    },
    
    edit: function()
    {
        if ( !almoxarifadoAtivo.grid.getSelectedId() ) {
            
            objGeral.msgAlerta( objGeral.translate('Selecione o item para edição.') );
            return false;
        }
            
        objGeral.createDialog( 
                '/almoxarifado/ativo/edit/id/' + almoxarifadoAtivo.grid.getSelectedId(), 
                objGeral.translate('Ativo')
            );
        
        return true;
    },
    
    atualizarGrid: function()
    {
        objGeral.atualizarGrids( [almoxarifadoAtivo.grid] );
    }
});

var almoxarifadoAtivo = new modulo.almoxarifado.ativo();