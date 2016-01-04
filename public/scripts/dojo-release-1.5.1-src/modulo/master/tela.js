dojo.require( 'modulo.padrao.geral' );
dojo.require( 'modulo.padrao.grid' );

dojo.provide( 'modulo.master.tela' );

dojo.declare( 'modulo.master.tela', [modulo.padrao.geral, modulo.padrao.grid],
{
    constructor: function()
    {  
    },
    
    initGrid: function()
    {
        objGeral.loading( true );

        masterTela.gridHeader = '#,'+
                                objGeral.translate('Nome')+','+
                                objGeral.translate('Módulo')+','+
                                objGeral.translate('Path')+','+
                                objGeral.translate('Status');
                            
        document.getElementById( 'gridMasterTela' ).style.height= objGrid.gridHeight +"px";
        masterTela.grid = new dhtmlXGridObject( 'gridMasterTela' );
        masterTela.grid.setHeader( masterTela.gridHeader );
        masterTela.grid.attachHeader("#rspan,#text_filter,#select_filter,#text_filter,#select_filter");
        masterTela.grid.setInitWidths( objGrid.idWidth + ",*,200,200,100");
        masterTela.grid.setColAlign("center,left,left,left,left");
        masterTela.grid.setColTypes("ro,ro,ro,ro,ro");
        masterTela.grid.setColSorting("str,str,str,str,str");
        masterTela.grid.setSkin( "dhx_skyblue" );
        masterTela.grid.enablePaging( true, objGrid.pagResults, objGrid.pagIndexes, 'divpagMasterTelaGrid', true, 'divpagMasterTela');
        masterTela.grid.attachFooter( objGrid.tituloTotal + ",#cspan,#cspan,#cspan,{#stat_count}" );
        masterTela.grid.attachEvent( 'onRowDblClicked', masterTela.edit );
        masterTela.grid.init();
        masterTela.grid.load( baseUrl + '/master/tela/list', dojo.hitch( objGeral, 'loading', false ) , "json");
    },
    
    novo: function()
    {
        objGeral.createDialog( 
                '/master/tela/form/', 
                objGeral.translate('Tela')
            );
    },
    
    edit: function()
    {
        if ( !masterTela.grid.getSelectedId() ) {
            
            objGeral.msgAlerta( objGeral.translate('Selecione o item para edição.') );
            return false;
        }
            
        objGeral.createDialog( 
                '/master/tela/edit/id/' + masterTela.grid.getSelectedId(), 
                objGeral.translate('Tela')
            );
        
        return true;
    },
    
    atualizarGrid: function()
    {
        objGeral.atualizarGrids( [masterTela.grid] );
    }
});

var masterTela = new modulo.master.tela();