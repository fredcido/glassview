dojo.require( 'modulo.padrao.geral' );
dojo.require( 'modulo.padrao.grid' );

dojo.provide( 'modulo.admin.perfil' );

dojo.declare( 'modulo.admin.perfil', [modulo.padrao.geral, modulo.padrao.grid],
{
    constructor: function()
    {
    },

    initGrid: function()
    {
        objGeral.loading( true );
        
        adminPerfil.gridHeader = '#,'+
                                objGeral.translate('Nome')+','+
                                objGeral.translate('Descrição')+','+
                                objGeral.translate('Status');

        document.getElementById('gridAdminPerfil').style.height= objGrid.gridHeight +"px";
        adminPerfil.grid = new dhtmlXGridObject('gridAdminPerfil');
        adminPerfil.grid.setHeader( adminPerfil.gridHeader );
        adminPerfil.grid.attachHeader("#rspan,#text_filter,#text_filter,#select_filter");
        adminPerfil.grid.setInitWidths( objGrid.idWidth + ",*,400,80");
        adminPerfil.grid.setColAlign("center,left,left,left");
        adminPerfil.grid.setColTypes("ro,ro,ro,ro");
        adminPerfil.grid.setColSorting("str,str,str,str");
        adminPerfil.grid.setSkin( objGrid.theme );
        adminPerfil.grid.enablePaging( true, objGrid.pagResults, objGrid.pagIndexes, 'divpagAdminPerfilGrid', true, 'divpagAdminPerfil');
        adminPerfil.grid.attachFooter( objGrid.tituloTotal + ",#cspan,#cspan,{#stat_count}");
        adminPerfil.grid.attachEvent( 'onRowDblClicked', adminPerfil.edit );
        adminPerfil.grid.init();
        adminPerfil.grid.load( baseUrl + '/admin/perfil/list', dojo.hitch( objGeral, 'loading', false ) , "json");


    },
    
    novo: function()
    {
        objGeral.createDialog( 
                '/admin/perfil/form/', 
                objGeral.translate('Perfil')
            );
    },
    
    edit: function()
    {
        if ( !adminPerfil.grid.getSelectedId() ) {
            
            objGeral.msgAlerta( objGeral.translate('Selecione o item para edição.') );
            return false;
        }
            
        objGeral.createDialog( 
                    '/admin/perfil/edit/id/' + adminPerfil.grid.getSelectedId(), 
                    objGeral.translate('Perfil')
                );
        
        return true;
    },
    
    atualizarGrid: function()
    {
        objGeral.atualizarGrids( [adminPerfil.grid] );
    }
});

var adminPerfil = new modulo.admin.perfil();