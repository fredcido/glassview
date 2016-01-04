dojo.require( 'modulo.padrao.geral' );
dojo.require( 'modulo.padrao.grid' );

dojo.provide( 'modulo.admin.traducao' );

dojo.declare( 'modulo.admin.traducao' , [modulo.padrao.geral,modulo.padrao.grid],
{
    constructor: function()
    { 
    },

    initGrid: function()
    {
        objGeral.loading( true );
        
        adminTraducao.gridHeader = '#,'+
                                    objGeral.translate('Linguagem')+','+
                                    objGeral.translate('Tipo')+','+
                                    objGeral.translate('Menu')+' / '+objGeral.translate('Termo')+','+
                                    objGeral.translate('Tradução');
                                
        document.getElementById('gridAdminTraducao').style.height= objGrid.gridHeight +"px";
        adminTraducao.grid = new dhtmlXGridObject('gridAdminTraducao');
        adminTraducao.grid.setHeader( adminTraducao.gridHeader );
        adminTraducao.grid.attachHeader("#rspan,#select_filter,#select_filter,#text_filter,#text_filter");
        adminTraducao.grid.setInitWidths( objGrid.idWidth + ",150,100,400,*");
        adminTraducao.grid.setColAlign("center,left,left,left,left");
        adminTraducao.grid.setColTypes("ro,ro,ro,ro,ro");
        adminTraducao.grid.setColSorting("str,str,str,str,str");
        adminTraducao.grid.setSkin( objGrid.theme );
        adminTraducao.grid.enablePaging( true, objGrid.pagResults, objGrid.pagIndexes, 'divpagAdminTraducaoGrid', true, 'divpagAdminTraducao');
        adminTraducao.grid.attachFooter( objGrid.tituloTotal + ",#cspan,#cspan,#cspan,{#stat_count}");
        adminTraducao.grid.attachEvent( 'onRowDblClicked', adminTraducao.edit );
        adminTraducao.grid.init();
        adminTraducao.grid.load( baseUrl + '/admin/traducao/list', dojo.hitch( objGeral, 'loading', false ) , "json");

    },
    
    novo: function()
    {
        objGeral.createDialog( 
            '/admin/traducao/form/', 
            objGeral.translate('Tradução')
        );
    },
    
    edit: function()
    {
        if ( !adminTraducao.grid.getSelectedId() ) {
            
            objGeral.msgAlerta( objGeral.translate('Selecione o item para edição.') );
            return false;
        }
            
        objGeral.createDialog(
            '/admin/traducao/edit/id/' + adminTraducao.grid.getSelectedId(), 
            objGeral.translate('Tradução')
        );
        
        return true;
    },
    
    atualizarGrid: function()
    {
        objGeral.atualizarGrids( [adminTraducao.grid] );
    },
    
    carregaTermo:function(  )
    {
        var tTrad  = dijit.byId('traducao_tipo').attr('value');
        var lingua = dijit.byId('linguagem_id').attr('value');

        if ( objGeral.empty( lingua ) || objGeral.empty( tTrad )  ) {
            
            dijit.byId('menu_termo_id').attr('value', '' );
            dijit.byId('menu_termo_id').setDisabled( true );
            
        }else{

           objGeral.changeFilteringSelect( 
                'menu_termo_id', 
                baseUrl + "/admin/traducao/listatermos/tipo/" + tTrad + "/lang/", 
                lingua
            );
        }
    }
});

var adminTraducao = new modulo.admin.traducao();