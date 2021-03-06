dojo.require( 'modulo.padrao.geral' );
dojo.require( 'modulo.padrao.grid' );

dojo.provide( 'modulo.admin.terceiro' );

dojo.declare( 'modulo.admin.terceiro' , [modulo.padrao.geral, modulo.padrao.grid],
{
    constructor: function()
    {
    },

    initGrid: function()
    {
        objGeral.loading( true );
        
        adminTerceiro.gridHeader = '#,'+
                                    objGeral.translate('Nome')+','+
                                    objGeral.translate('Tipo')+','+
                                    objGeral.translate('Pessoa')+','+
                                    objGeral.translate('CNPJ')+' / '+objGeral.translate('CPF')+','+
                                    objGeral.translate('Contato')+','+
                                    objGeral.translate('Telefone')+','+
                                    objGeral.translate('Fax');
                                
        document.getElementById('gridAdminTerceiro').style.height= objGrid.gridHeight +"px";
        adminTerceiro.grid = new dhtmlXGridObject('gridAdminTerceiro');
        adminTerceiro.grid.setHeader( adminTerceiro.gridHeader );
        adminTerceiro.grid.attachHeader("#rspan,#text_filter,#select_filter,#select_filter,#text_filter,#text_filter,#text_filter,#text_filter");
        adminTerceiro.grid.setInitWidths( objGrid.idWidth + ",*,100,100,150,250,160,160");
        adminTerceiro.grid.setColAlign("center,left,left,left,left,left,left,left");
        adminTerceiro.grid.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro");
        adminTerceiro.grid.setColSorting("str,str,str,str,str,str,str,str");
        adminTerceiro.grid.setSkin( objGrid.theme );
        adminTerceiro.grid.enablePaging( true, objGrid.pagResults, objGrid.pagIndexes, 'divpagAdminTerceiroGrid', true, 'divpagAdminTerceiro');
        adminTerceiro.grid.attachFooter( objGrid.tituloTotal + ",#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,{#stat_count}");
        adminTerceiro.grid.attachEvent( 'onRowDblClicked', adminTerceiro.edit );
        adminTerceiro.grid.init();
        adminTerceiro.grid.load( baseUrl + '/admin/terceiro/list', dojo.hitch( objGeral, 'loading', false ) , "json");

    },
    
    novo: function()
    {
        objGeral.createDialog( 
                '/admin/terceiro/form/', 
                objGeral.translate('Terceiro')
            );
    },
    
    edit: function()
    {
        if ( !adminTerceiro.grid.getSelectedId() ) {
            
            objGeral.msgAlerta( objGeral.translate('Selecione o item para edição.') );
            return false;
        }
            
        objGeral.createDialog( 
                '/admin/terceiro/edit/id/' + adminTerceiro.grid.getSelectedId(),
                objGeral.translate('Terceiro')
            );
        
        return true;
    },
    
    atualizarGrid: function()
    {
        objGeral.atualizarGrids( [adminTerceiro.grid] );
    }
});

var adminTerceiro = new modulo.admin.terceiro();