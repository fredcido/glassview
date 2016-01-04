dojo.require( 'modulo.padrao.geral' );
dojo.require( 'modulo.padrao.grid' );

dojo.provide( 'modulo.master.modulo' );

dojo.declare( 'modulo.master.modulo', [modulo.padrao.geral, modulo.padrao.grid],
{
    constructor: function()
    {
    },

    initGrid: function()
    {
        objGeral.loading( true );
        
        masterModulo.gridHeader = '#,'+
                                objGeral.translate('Nome')+','+
                                objGeral.translate('Status');
                            
        document.getElementById( 'gridMasterModulo' ).style.height= objGrid.gridHeight +"px";
        masterModulo.grid = new dhtmlXGridObject( 'gridMasterModulo' );
        masterModulo.grid.setHeader( masterModulo.gridHeader );
        masterModulo.grid.attachHeader("#rspan,#text_filter,#select_filter");
        masterModulo.grid.setInitWidths( objGrid.idWidth + ",*,100");
        masterModulo.grid.setColAlign("center,left,left");
        masterModulo.grid.setColTypes("ro,ro,ro");
        masterModulo.grid.setColSorting("str,str,str");
        masterModulo.grid.setSkin( objGrid.theme );
        masterModulo.grid.enablePaging( true, objGrid.pagResults, objGrid.pagIndexes, 'divpagMasterModuloGrid', true, 'divpagMasterModulo');
        masterModulo.grid.attachFooter( objGrid.tituloTotal + ",#cspan,{#stat_count}" );
        masterModulo.grid.attachEvent( 'onRowDblClicked', masterModulo.edit );
        masterModulo.grid.init();
        masterModulo.grid.load( baseUrl + '/master/modulo/list', dojo.hitch( objGeral, 'loading', false ) , "json");
    },
    
    novo: function()
    {
        objGeral.createDialog( 
                '/master/modulo/form/', 
                objGeral.translate('Módulo')
            );
    },
    
    edit: function()
    {
        if ( !masterModulo.grid.getSelectedId() ) {
            
            objGeral.msgAlerta( objGeral.translate('Selecione o item para edição.') );
            return false;
        }
            
        objGeral.createDialog( 
                    '/master/modulo/edit/id/' + masterModulo.grid.getSelectedId(),
                    objGeral.translate('Módulo')
                );
        
        return true;
    },
    
    atualizarGrid: function()
    {
        objGeral.atualizarGrids( [masterModulo.grid] );
    }
});

var masterModulo = new modulo.master.modulo();