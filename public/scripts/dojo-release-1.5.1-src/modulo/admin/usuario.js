dojo.require( 'modulo.padrao.geral' );
dojo.require( 'modulo.padrao.grid' );

dojo.provide( 'modulo.admin.usuario' );

dojo.declare( 'modulo.admin.usuario', [modulo.padrao.geral, modulo.padrao.grid],
{
    constructor: function()
    {  
    },

    initGrid: function()
    {
        objGeral.loading( true );
        
        adminUsuario.gridHeader = '#,'+
                                    objGeral.translate('Nome')+','+
                                    objGeral.translate('Login')+','+
                                    objGeral.translate('Perfil')+','+
                                    objGeral.translate('E-mail')+','+
                                    objGeral.translate('Nível')+','+
                                    objGeral.translate('Status');
                                
        document.getElementById('gridAdminUsuario').style.height= objGrid.gridHeight +"px";
        adminUsuario.grid = new dhtmlXGridObject('gridAdminUsuario');
        adminUsuario.grid.setHeader( adminUsuario.gridHeader );
        adminUsuario.grid.attachHeader("#rspan,#text_filter,#text_filter,#select_filter,#text_filter,#select_filter,#select_filter");
        adminUsuario.grid.setInitWidths( objGrid.idWidth + ",*,180,200,300,200,80");
        adminUsuario.grid.setColAlign("center,left,left,left,left,left,left");
        adminUsuario.grid.setColTypes("ro,ro,ro,ro,ro,ro,ro");
        adminUsuario.grid.setColSorting("str,str,str,str,str,str,str");
        adminUsuario.grid.setSkin( objGrid.theme );
        adminUsuario.grid.enablePaging( true, objGrid.pagResults, objGrid.pagIndexes, 'divpagAdminUsuarioGrid', true, 'divpagAdminUsuario');
        adminUsuario.grid.attachFooter( objGrid.tituloTotal + ",#cspan,#cspan,#cspan,#cspan,#cspan,{#stat_count}");
        adminUsuario.grid.attachEvent( 'onRowDblClicked', adminUsuario.edit );
        adminUsuario.grid.init();
        adminUsuario.grid.load( baseUrl + '/admin/usuario/list', dojo.hitch( objGeral, 'loading', false ) , "json");

    },
    
    novo: function()
    {
        objGeral.createDialog( 
                '/admin/usuario/form/', 
                objGeral.translate('Usuário')
            );
    },
    edit: function()
    {
        if ( !adminUsuario.grid.getSelectedId() ) {
            
            objGeral.msgAlerta( objGeral.translate('Selecione o item para edição.') );
            return false;
        }
            
        objGeral.createDialog( 
                '/admin/usuario/edit/id/' + adminUsuario.grid.getSelectedId(), 
                objGeral.translate('Usuário')
            );
        
        return true;
    },
    
    atualizarGrid: function()
    {
        objGeral.atualizarGrids( [adminUsuario.grid] );
    },
    
    liberaPerfil: function()
    {
	var nivel = dijit.byId( 'usuario_nivel' ).get( 'value' );
	var required = nivel == 'N';

	objGeral.setRequired( 'perfil_id', required );
	dijit.byId( 'perfil_id' ).setDisabled( !required );
    }
});

var adminUsuario = new modulo.admin.usuario();