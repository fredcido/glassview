dojo.require( 'modulo.padrao.geral' );
dojo.require( 'modulo.padrao.grid' );

dojo.provide( 'modulo.admin.funcionario' );

dojo.declare( 'modulo.admin.funcionario', [modulo.padrao.geral, modulo.padrao.grid],
{
    constructor: function()
    { 
    },

    initGrid: function()
    {
        objGeral.loading( true );
        
        adminFuncionario.gridHeader = '#,'+
                                        objGeral.translate('Nome')+','+
                                        objGeral.translate('E-mail')+','+
                                        objGeral.translate('Cargo');
                                    
        document.getElementById('gridAdminFuncionario').style.height= objGrid.gridHeight +"px";
        adminFuncionario.grid = new dhtmlXGridObject('gridAdminFuncionario');
        adminFuncionario.grid.setHeader( adminFuncionario.gridHeader );
        adminFuncionario.grid.attachHeader("#rspan,#text_filter,#text_filter,#select_filter");
        adminFuncionario.grid.setInitWidths( objGrid.idWidth + ",*,400,300");
        adminFuncionario.grid.setColAlign("center,left,left,left");
        adminFuncionario.grid.setColTypes("ro,ro,ro,ro");
        adminFuncionario.grid.setColSorting("str,str,str,str");
        adminFuncionario.grid.setSkin( objGrid.theme );
        adminFuncionario.grid.enablePaging( true, objGrid.pagResults, objGrid.pagIndexes, 'divpagAdminFuncionarioGrid', true, 'divpagAdminFuncionario');
        adminFuncionario.grid.attachFooter( objGrid.tituloTotal + ",#cspan,#cspan,{#stat_count}");
        adminFuncionario.grid.attachEvent( 'onRowDblClicked', adminFuncionario.edit );
        adminFuncionario.grid.init();
        adminFuncionario.grid.load( baseUrl + '/admin/funcionario/list', dojo.hitch( objGeral, 'loading', false ) , "json");

    },
    
    novo: function()
    {
        objGeral.createDialog( 
                '/admin/funcionario/form/', 
                objGeral.translate('Funcionário')
            );
    },
    
    edit: function()
    {
        if ( !adminFuncionario.grid.getSelectedId() ) {
            
            objGeral.msgAlerta( objGeral.translate('Selecione o item para edição.') );
            return false;
        }
            
        objGeral.createDialog( 
                    '/admin/funcionario/edit/id/' + adminFuncionario.grid.getSelectedId(), 
                    objGeral.translate('Funcionário')
                );
        
        return true;
    },
    
    atualizarGrid: function()
    {
        objGeral.atualizarGrids( [adminFuncionario.grid] );
    },
    
    liberaUsuario: function()
    {
	var usuario = dijit.byId( 'usuario' ).get( 'value' );
	
	var fields = ['usuario_nivel', 'usuario_login', 'usuario_senha', 'usuario_senha2', 'perfil_id' ];
	var required = usuario == 'S';

	dojo.forEach( fields, 
	    function( item )
	    {
		objGeral.setRequired( item, required );
		dijit.byId( item ).set( 'readOnly', !required );
		dijit.byId( item ).set( 'value', '' );
	    } 
	);
    },
    
    liberaPerfil: function()
    {
	var nivel = dijit.byId( 'usuario_nivel' ).get( 'value' );
	var required = nivel == 'N';

	objGeral.setRequired( 'perfil_id', required );
	dijit.byId( 'perfil_id' ).setDisabled( !required );
    }
});

var adminFuncionario = new modulo.admin.funcionario();