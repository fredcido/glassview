dojo.require( 'modulo.padrao.geral' );
dojo.require( 'modulo.padrao.grid' );

dojo.provide( 'modulo.admin.filial' );

dojo.declare( 'modulo.admin.filial', [modulo.padrao.geral, modulo.padrao.grid],
{
    constructor: function()
    {  
    },

    initGrid: function()
    {
        objGeral.loading( true );
        
        adminFilial.gridHeader = '#,'+
                                objGeral.translate('Filial')+','+
                                objGeral.translate('Cidade')+','+
                                objGeral.translate('Estado')+','+
                                objGeral.translate('Status');
                            
        document.getElementById('gridAdminFilial').style.height= objGrid.gridHeight +"px";
        adminFilial.grid = new dhtmlXGridObject('gridAdminFilial');
        adminFilial.grid.setHeader( adminFilial.gridHeader );
        adminFilial.grid.attachHeader("#rspan,#text_filter,#select_filter,#select_filter,#select_filter");
        adminFilial.grid.setInitWidths( objGrid.idWidth + ",*,320,200,100");
        adminFilial.grid.setColAlign("center,left,left,left,left");
        adminFilial.grid.setColTypes("ro,ro,ro,ro,ro");
        adminFilial.grid.setColSorting("str,str,str,str,str");
        adminFilial.grid.setSkin( objGrid.theme );
        adminFilial.grid.enablePaging( true, objGrid.pagResults, objGrid.pagIndexes, 'divpagAdminFilialGrid', true, 'divpagAdminFilial');
        adminFilial.grid.attachFooter( objGrid.tituloTotal + ",#cspan,#cspan,#cspan,{#stat_count}");
        adminFilial.grid.attachEvent( 'onRowDblClicked', adminFilial.edit );
        adminFilial.grid.init();
        adminFilial.grid.load( baseUrl + '/admin/filial/list', dojo.hitch( objGeral, 'loading', false ) , "json");

    },
    
    novo: function()
    {
        objGeral.createDialog( 
                '/admin/filial/form/', 
                objGeral.translate('Filial')
            );
    },
    
    edit: function()
    {
        if ( !adminFilial.grid.getSelectedId() ) {
            
            objGeral.msgAlerta( objGeral.translate('Selecione o item para edição.') );
            return false;
        }
            
        objGeral.createDialog( 
                    '/admin/filial/edit/id/' + adminFilial.grid.getSelectedId(), 
                    objGeral.translate('Filial')
                );
        
        return true;
    },
    
    atualizarGrid: function()
    {
        objGeral.atualizarGrids( [adminFilial.grid] );
    },

    changeEstado: function( pais )
    {

        if ( objGeral.empty( pais )  ) {
            
            dijit.byId('estado_id').attr('value', '' );
            dijit.byId('estado_id').setDisabled( true );
            
        }else{
           
           objGeral.changeFilteringSelect( 'estado_id', baseUrl + "/admin/filial/buscaestado/pais/", pais.value );
        }
        
    },

    changeCidade: function( estado )
    {

        if ( objGeral.empty( estado )  ) {
            
            dijit.byId('cidade_id').attr('value', '' );
            dijit.byId('cidade_id').setDisabled( true );
        } else {
            
            objGeral.changeFilteringSelect( 'cidade_id', baseUrl + "/admin/filial/buscacidade/estado/", estado.value );
        }
    }
    
   
});

var adminFilial = new modulo.admin.filial();