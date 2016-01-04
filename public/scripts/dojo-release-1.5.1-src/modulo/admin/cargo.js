dojo.require( 'modulo.padrao.geral' );
dojo.require( 'modulo.padrao.grid' );

dojo.provide( 'modulo.admin.cargo' );

dojo.declare( 'modulo.admin.cargo', [modulo.padrao.geral, modulo.padrao.grid],
{
    constructor: function()
    { 
    },

    initGrid: function()
    {
        objGeral.loading( true );
        
        adminCargo.gridHeader = '#,'+
                                objGeral.translate('Nome')+','+
                                objGeral.translate('Descrição')+','+
                                objGeral.translate('Status');
                                
        document.getElementById('gridAdminCargo').style.height= objGrid.gridHeight +"px";
        adminCargo.grid = new dhtmlXGridObject('gridAdminCargo');
        adminCargo.grid.setHeader( adminCargo.gridHeader );
        adminCargo.grid.attachHeader("#rspan,#text_filter,#text_filter,#select_filter");
        adminCargo.grid.setInitWidths( objGrid.idWidth + ",400,*,80");
        adminCargo.grid.setColAlign("center,left,left,left");
        adminCargo.grid.setColTypes("ro,ro,ro,ro");
        adminCargo.grid.setColSorting("str,str,str,str");
        adminCargo.grid.setSkin( objGrid.theme );
        adminCargo.grid.enablePaging( true, objGrid.pagResults, objGrid.pagIndexes, 'divpagAdminCargoGrid', true, 'divpagAdminCargo');
        adminCargo.grid.attachFooter( objGrid.tituloTotal + ",#cspan,#cspan,#cspan,#cspan,{#stat_count}");
        adminCargo.grid.attachEvent( 'onRowDblClicked', adminCargo.edit );
        adminCargo.grid.init();
        adminCargo.grid.load( baseUrl + '/admin/cargo/list', dojo.hitch( objGeral, 'loading', false ) , "json");

    },
    
    novo: function()
    {
        objGeral.createDialog( 
                '/admin/cargo/form/', 
                objGeral.translate('Cargo') 
            );
    },
    
    edit: function()
    {
        if ( !adminCargo.grid.getSelectedId() ) {
            
            objGeral.msgAlerta( objGeral.translate('Selecione o item para edição.') );
            return false;
        }
            
        objGeral.createDialog( 
                '/admin/cargo/edit/id/' + adminCargo.grid.getSelectedId(), 
                objGeral.translate('Cargo')
            );
        
        return true;
    },
    
    atualizarGrid: function()
    {
        objGeral.atualizarGrids( [adminCargo.grid] );
    }
});

var adminCargo = new modulo.admin.cargo();