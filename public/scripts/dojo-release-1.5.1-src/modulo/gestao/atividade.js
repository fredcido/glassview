dojo.require( 'modulo.padrao.geral' );
dojo.require( 'modulo.padrao.grid' );

dojo.provide( 'modulo.gestao.atividade' );

dojo.declare( 'modulo.gestao.atividade' , [modulo.padrao.geral, modulo.padrao.grid],
{
    constructor: function()
    { 
    },

    initGrid: function()
    {
        objGeral.loading( true );
        
        gestaoAtividade.gridHeader = '#,'+
                                     objGeral.translate('Nome')+','+
                                     objGeral.translate('Descrição');
                            
        document.getElementById( 'gridGestaoAtividade' ).style.height= objGrid.gridHeight +"px";
        gestaoAtividade.grid = new dhtmlXGridObject('gridGestaoAtividade');
        gestaoAtividade.grid.setHeader( gestaoAtividade.gridHeader );
        gestaoAtividade.grid.attachHeader("#rspan,#text_filter,#text_filter");
        gestaoAtividade.grid.setInitWidths( objGrid.idWidth + ",200,*");
        gestaoAtividade.grid.setColAlign("center,left,left");
        gestaoAtividade.grid.setColTypes("ro,ro,ro");
        gestaoAtividade.grid.setColSorting("str,str,str");
        gestaoAtividade.grid.setSkin( objGrid.theme );
        gestaoAtividade.grid.enablePaging( true, objGrid.pagResults, objGrid.pagIndexes, 'divpagGestaoAtividadeGrid', true, 'divpagGestaoAtividade');
        gestaoAtividade.grid.attachFooter( objGrid.tituloTotal + ",#cspan,{#stat_count}");
        gestaoAtividade.grid.attachEvent( 'onRowDblClicked', gestaoAtividade.edit );
        gestaoAtividade.grid.init();
        gestaoAtividade.grid.load( baseUrl + '/gestao/atividade/list', dojo.hitch( objGeral, 'loading', false ) , "json");

    },
    
    novo: function()
    {
        objGeral.createDialog( 
                '/gestao/atividade/form/', 
                objGeral.translate('Atividade')
            );
    },
    
    edit: function()
    {
        if ( !gestaoAtividade.grid.getSelectedId() ) {
            
            objGeral.msgAlerta( objGeral.translate('Selecione o item para edição.') );
            return false;
        }
            
        objGeral.createDialog( 
                '/gestao/atividade/edit/id/' + gestaoAtividade.grid.getSelectedId(), 
                objGeral.translate('Atividade')
            );
        
        return true;
    },
    
    atualizarGrid: function()
    {
        objGeral.atualizarGrids( [gestaoAtividade.grid] );
    }
});

var gestaoAtividade = new modulo.gestao.atividade();