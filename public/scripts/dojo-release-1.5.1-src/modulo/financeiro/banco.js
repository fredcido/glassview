dojo.require( 'modulo.padrao.geral' );
dojo.require( 'modulo.padrao.grid' );

dojo.provide( 'modulo.financeiro.banco' );

dojo.declare( 'modulo.financeiro.banco', [modulo.padrao.geral, modulo.padrao.grid],
{
    constructor: function()
    {
    },

    initGrid: function()
    {
        objGeral.loading( true );
        
        financeiroBanco.gridHeader = '#,'+
                                    objGeral.translate('Código')+','+
                                    objGeral.translate('Nome');
	
        document.getElementById('gridFinanceiroBanco').style.height= objGrid.gridHeight +"px";
        financeiroBanco.grid = new dhtmlXGridObject('gridFinanceiroBanco');
        financeiroBanco.grid.setHeader( financeiroBanco.gridHeader );
        financeiroBanco.grid.attachHeader("#rspan,#text_filter,#text_filter");
        financeiroBanco.grid.setInitWidths( objGrid.idWidth + ",300,*");
        financeiroBanco.grid.setColAlign("center,left,left");
        financeiroBanco.grid.setColTypes("ro,ro,ro");
        financeiroBanco.grid.setColSorting("str,str,str");
        financeiroBanco.grid.setSkin( objGrid.theme );
        financeiroBanco.grid.enablePaging( true, objGrid.pagResults, objGrid.pagIndexes, 'divpagFinanceiroBancoGrid', true, 'divpagfinanceiroBanco');
        financeiroBanco.grid.attachFooter( objGrid.tituloTotal + ",#cspan,{#stat_count}");
	financeiroBanco.grid.attachEvent( 'onRowDblClicked', financeiroBanco.edit );  
        financeiroBanco.grid.init();
        financeiroBanco.grid.load( baseUrl + '/financeiro/banco/list', dojo.hitch( objGeral, 'loading', false ) , "json");
    },
    
    novo: function()
    {
        objGeral.createDialog( 
                '/financeiro/banco/form/',
                objGeral.translate('Banco')
            );
    },
    
    edit: function()
    {
        if ( !financeiroBanco.grid.getSelectedId() ) {
            
            objGeral.msgAlerta( objGeral.translate('Selecione o item para edição.') );
            return false;
        }
            
        objGeral.createDialog( 
                '/financeiro/banco/edit/id/' + financeiroBanco.grid.getSelectedId(),
                objGeral.translate('Banco')
            );
        
        return true;
    },
    
    atualizarGrid: function()
    {
        objGeral.atualizarGrids( [financeiroBanco.grid] );
    }
});

var financeiroBanco = new modulo.financeiro.banco();