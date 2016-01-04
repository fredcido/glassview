dojo.require( 'modulo.padrao.geral' );
dojo.require( 'modulo.padrao.grid' );

dojo.provide( 'modulo.master.acao' );

dojo.declare( 'modulo.master.acao' , [modulo.padrao.geral,modulo.padrao.grid],
{
    constructor: function()
    {  
    },
    
    initGrid: function()
    {
        objGeral.loading( true );

        masterAcao.gridHeader = '#,'+
                                objGeral.translate('Descrição')+','+
                                objGeral.translate('Tela')+','+
                                objGeral.translate('Identificador');
                               
        document.getElementById('gridMasterAcao').style.height= objGrid.gridHeight +"px";
        masterAcao.grid = new dhtmlXGridObject( 'gridMasterAcao' );
        masterAcao.grid.setHeader( masterAcao.gridHeader );
        masterAcao.grid.attachHeader("#rspan,#text_filter,#select_filter,#text_filter");
        masterAcao.grid.setInitWidths( objGrid.idWidth + ",*,350,350");
        masterAcao.grid.setColAlign("center,left,left,left");
        masterAcao.grid.setColTypes("ro,ro,ro,ro");
        masterAcao.grid.setColSorting("str,str,str,str");
        masterAcao.grid.setSkin( objGrid.theme );
        masterAcao.grid.enablePaging( true, objGrid.pagResults, objGrid.pagIndexes, 'divpagMasterAcaoGrid', true, 'divpagMasterAcao');
        masterAcao.grid.attachFooter( objGrid.tituloTotal + ",#cspan,#cspan,{#stat_count}" );
        masterAcao.grid.attachEvent( 'onRowDblClicked', masterAcao.edit );
        masterAcao.grid.init();
        masterAcao.grid.load( baseUrl + '/master/acao/list', dojo.hitch( objGeral, 'loading', false ) , "json");
    },
    
    novo: function()
    {
        objGeral.createDialog( 
                '/master/acao/form/', 
                objGeral.translate('Ação')
            );
    },
    
    edit: function()
    {
        if ( !masterAcao.grid.getSelectedId() ) {
            
            objGeral.msgAlerta( objGeral.translate('Selecione o item para edição.') );
            return false;
        }
            
        objGeral.createDialog( 
                    '/master/acao/edit/id/' + masterAcao.grid.getSelectedId(), 
                    objGeral.translate('Ação')
                );
        
        return true;
    },
    
    atualizarGrid: function()
    {
        objGeral.atualizarGrids( [masterAcao.grid] );
    },
    
    
    addPrivilegio: function()
    {
	var tdField = $('<td />');
	var tdExcl = $('<td />');
	var tr = $('<tr />');
	
	var field = new dijit.form.ValidationTextBox({ name: 'privilegios[]', required: true });
	
	tdField.append( field.domNode );
	
	var excluir = $('<div />');
	excluir.addClass( 'icon-toolbar-cancel' ).click(
	    function()
	    {
		if ( !confirm( objGeral.translate('Deseja realmente remover este item?') ) )
			return false;
		    
		objGeral.deleteRow( $(this).parent().parent() );
		return true;
	    }
	).attr( 'title', objGeral.translate('Remover Privilégio') ).css( 'cursor', 'pointer' );
	    
	tdExcl.append( excluir );
	
	tr.append( tdField ).append( tdExcl );
	
	$('#tbl-privilegio-acao').append( tr );
    },
    
    removePrilegioBase: function( id, img )
    {
	if ( !confirm( objGeral.translate('Deseja realmente remover este item?') ) )
	    return false;
	
	var obj = {
	    url: baseUrl + '/master/acao/delete-privilegio/id/' + id,
	    handle: 'json',
	    callback: function( response )
	    {
		if ( response.status )
		    objGeral.deleteRow( $(img).parent().parent() );
		else
		    objGeral.msgErro( response.message );
	    }
	}
	
	objGeral.buscaAjax( obj );
	
	return true;
    }
});

var masterAcao = new modulo.master.acao();