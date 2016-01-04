dojo.require( 'modulo.padrao.geral' );
dojo.require( 'modulo.padrao.grid' );

dojo.provide( 'modulo.almoxarifado.unidademedida' );

dojo.declare( 'modulo.almoxarifado.unidademedida', [modulo.padrao.geral, modulo.padrao.grid],
{
    constructor: function()
    {
    },

    initGrid: function()
    {
        objGeral.loading( true );
        
        almoxarifadoUnidadeMedida.gridHeader = '#,'+
                                                objGeral.translate('Nome')+','+
                                                objGeral.translate('Status');
	
        document.getElementById( 'gridAlmoxarifadoUnidadeMedida' ).style.height= objGrid.gridHeight +"px";
        almoxarifadoUnidadeMedida.grid = new dhtmlXGridObject('gridAlmoxarifadoUnidadeMedida');
        almoxarifadoUnidadeMedida.grid.setHeader( almoxarifadoUnidadeMedida.gridHeader );
        almoxarifadoUnidadeMedida.grid.attachHeader("#rspan,#text_filter,#select_filter");
        almoxarifadoUnidadeMedida.grid.setInitWidths( objGrid.idWidth + ",*,100");
        almoxarifadoUnidadeMedida.grid.setColAlign("center,left,left");
        almoxarifadoUnidadeMedida.grid.setColTypes("ro,ro,ro");
        almoxarifadoUnidadeMedida.grid.setColSorting("str,str,str");
        almoxarifadoUnidadeMedida.grid.setSkin( objGrid.theme );
        almoxarifadoUnidadeMedida.grid.enablePaging( true, objGrid.pagResults, objGrid.pagIndexes, 'divpagAlmoxarifadoUnidadeMedidaGrid', true, 'divpagAlmoxarifadoUnidadeMedida');
        almoxarifadoUnidadeMedida.grid.attachFooter( objGrid.tituloTotal + ",#cspan,{#stat_count}");
        almoxarifadoUnidadeMedida.grid.attachEvent( 'onRowDblClicked', almoxarifadoUnidadeMedida.edit );
        almoxarifadoUnidadeMedida.grid.init();
        almoxarifadoUnidadeMedida.grid.load( baseUrl + '/almoxarifado/unidade-medida/list', dojo.hitch( objGeral, 'loading', false ) , "json");
    },
    
    novo: function()
    {
        objGeral.createDialog( 
                '/almoxarifado/unidade-medida/form/', 
                objGeral.translate('Unidade de Medida')
            );
    },
    
    edit: function()
    {
        if ( !almoxarifadoUnidadeMedida.grid.getSelectedId() ) {
            
            objGeral.msgAlerta( objGeral.translate('Selecione o item para edição.') );
            return false;
        }
            
        objGeral.createDialog( 
                '/almoxarifado/unidade-medida/edit/id/' + almoxarifadoUnidadeMedida.grid.getSelectedId(),
                objGeral.translate('Unidade de Medida')
            );
        
        return true;
    },
    
    atualizarGrid: function()
    {
        objGeral.atualizarGrids( [almoxarifadoUnidadeMedida.grid] );
    }
});

var almoxarifadoUnidadeMedida = new modulo.almoxarifado.unidademedida();