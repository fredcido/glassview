dojo.require( 'modulo.padrao.geral' );
dojo.require( 'modulo.padrao.grid' );

dojo.provide( 'modulo.almoxarifado.produto' );

dojo.declare( 'modulo.almoxarifado.produto', [modulo.padrao.geral, modulo.padrao.grid],
{
    constructor: function()
    {
    },

    initGrid: function()
    {
        objGeral.loading( true );
        
        almoxarifadoProduto.gridHeader = '#,'+
                                        objGeral.translate('Descrição')+','+
                                        objGeral.translate('Tipo')+','+
                                        objGeral.translate('Unidade')+','+
                                        objGeral.translate('Valor Unitário')+','+
                                        objGeral.translate('Estoque Atual')+','+
                                        objGeral.translate('Estoque Mínimo')+','+
                                        objGeral.translate('Estoque Máximo')+','+
                                        objGeral.translate('Status');
	
        document.getElementById( 'gridAlmoxarifadoProduto' ).style.height= objGrid.gridHeight +"px";
        almoxarifadoProduto.grid = new dhtmlXGridObject('gridAlmoxarifadoProduto');
        almoxarifadoProduto.grid.setHeader( almoxarifadoProduto.gridHeader );
        almoxarifadoProduto.grid.attachHeader("#rspan,#text_filter,#select_filter,#select_filter,#text_filter,#text_filter,#text_filter,#text_filter,#select_filter");
        almoxarifadoProduto.grid.setInitWidths( objGrid.idWidth + ",*,120,100,100,150,150,150,80");
        almoxarifadoProduto.grid.setColAlign("center,left,left,left,right,right,right,right,center");
        almoxarifadoProduto.grid.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro,ro");
        almoxarifadoProduto.grid.setColSorting("str,str,str,str,na,str,str,str,str");
	almoxarifadoProduto.grid.setCustomSorting( dojo.hitch( objGeral, 'sortCurrency' ), 4);
        almoxarifadoProduto.grid.setSkin( objGrid.theme );
        almoxarifadoProduto.grid.enablePaging( true, objGrid.pagResults, objGrid.pagIndexes, 'divpagAlmoxarifadoProdutoGrid', true, 'divpagAlmoxarifadoProduto');
        almoxarifadoProduto.grid.attachFooter( objGrid.tituloTotal + ",#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,{#stat_count}");
        almoxarifadoProduto.grid.attachEvent( 'onRowDblClicked', almoxarifadoProduto.edit );
        almoxarifadoProduto.grid.init();
        almoxarifadoProduto.grid.load( baseUrl + '/almoxarifado/produto/list', dojo.hitch( objGeral, 'loading', false ) , "json");
    },
    
    novo: function()
    {
        objGeral.createDialog( 
                '/almoxarifado/produto/form/', 
                objGeral.translate('Produto') ,
                almoxarifadoProduto.setMinMaxEstoque
            );
    },
    
    edit: function()
    {
        if ( !almoxarifadoProduto.grid.getSelectedId() ) {
            
            objGeral.msgAlerta( objGeral.translate('Selecione o item para edição.') );
            return false;
        }
            
        objGeral.createDialog( 
                '/almoxarifado/produto/edit/id/' + almoxarifadoProduto.grid.getSelectedId(), 
                objGeral.translate('Produto') , 
                almoxarifadoProduto.setMinMaxEstoque 
            );
        
        return true;
    },
    
    atualizarGrid: function()
    {
        objGeral.atualizarGrids( [almoxarifadoProduto.grid] );
    },
    
    setMinMaxEstoque: function( )
    {
        dijit.byId("produto_estoque_min").constraints.min = 0;
        dijit.byId("produto_estoque_max").constraints.max = 9999999;
        dijit.byId("produto_estoque_min").constraints.max = dijit.byId("produto_estoque_max").attr('value') ;
        dijit.byId("produto_estoque_max").constraints.min = dijit.byId("produto_estoque_min").attr('value') ;
    }
});

var almoxarifadoProduto = new modulo.almoxarifado.produto();