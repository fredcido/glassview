dojo.require( 'modulo.padrao.geral' );
dojo.require( 'modulo.padrao.grid' );

dojo.provide( 'modulo.gestao.projeto' );

dojo.declare( 'modulo.gestao.projeto', [modulo.padrao.geral, modulo.padrao.grid],
{
    constructor: function()
    { 
    },

    initGrid: function()
    {
        objGeral.loading( true );
        
        gestaoProjeto.gridHeader = '#,'+
                                    objGeral.translate('Nome')+','+
                                    objGeral.translate('Descrição')+','+
                                    objGeral.translate('Orçamento')+','+
                                    objGeral.translate('Início')+','+
                                    objGeral.translate('Fim')+','+
                                    objGeral.translate('Status');
                                
        document.getElementById( 'gridGestaoProjeto' ).style.height= objGrid.gridHeight +"px";
        gestaoProjeto.grid = new dhtmlXGridObject('gridGestaoProjeto');
        gestaoProjeto.grid.setHeader( gestaoProjeto.gridHeader );
        gestaoProjeto.grid.attachHeader("#rspan,#text_filter,#text_filter,#text_filter,#numeric_filter,#text_filter,#select_filter");
        gestaoProjeto.grid.setInitWidths( objGrid.idWidth + ",200,*,200,150,150,100");
        gestaoProjeto.grid.setColAlign("center,left,left,left,left,left,left");
        gestaoProjeto.grid.setColTypes("ro,ro,ro,ro,ro,ro,ro");
        gestaoProjeto.grid.setColSorting("str,str,str,str,na,str,str");
	gestaoProjeto.grid.setCustomSorting( dojo.hitch( objGeral, 'sortCurrency' ), 3 );
        gestaoProjeto.grid.setSkin( objGrid.theme );
        gestaoProjeto.grid.enablePaging( true, objGrid.pagResults, objGrid.pagIndexes, 'divpagGestaoProjetoGrid', true, 'divpagGestaoProjeto');
        gestaoProjeto.grid.attachFooter( objGrid.tituloTotal + ",#cspan,#cspan,#cspan,#cspan,#cspan,{#stat_count}");
        gestaoProjeto.grid.attachEvent( 'onRowDblClicked', gestaoProjeto.edit );
        gestaoProjeto.grid.init();
        gestaoProjeto.grid.load( baseUrl + '/gestao/projeto/list', dojo.hitch( objGeral, 'loading', false ) , "json");
    },
    
    novo: function()
    {
        objGeral.createDialog( 
                '/gestao/projeto/form/', 
                objGeral.translate('Projeto')
            );
    },
    
    edit: function()
    {
        if ( !gestaoProjeto.grid.getSelectedId() ) {
            
            objGeral.msgAlerta( objGeral.translate('Selecione o item para edição.') );
            return false;
        }
            
        objGeral.createDialog( 
                '/gestao/projeto/edit/id/' + gestaoProjeto.grid.getSelectedId(), 
                objGeral.translate('Projeto')
            );
        
        return true;
    },
    
    atualizarGrid: function()
    {
        objGeral.atualizarGrids( [gestaoProjeto.grid] );
    }
});

var gestaoProjeto = new modulo.gestao.projeto();