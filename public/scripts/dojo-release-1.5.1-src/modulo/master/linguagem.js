dojo.require( 'modulo.padrao.geral' );
dojo.require( 'modulo.padrao.grid' );

dojo.provide( 'modulo.master.linguagem' );

dojo.declare( 'modulo.master.linguagem', [modulo.padrao.geral, modulo.padrao.grid],
{
    constructor: function()
    {
    },

    initGrid: function()
    {
        objGeral.loading( true );

        masterLinguagem.gridHeader = '#,'+
                                    objGeral.translate('Nome')+','+
                                    objGeral.translate('Local')+','+
                                    objGeral.translate('Status');
                                
        document.getElementById('gridMasterLinguagem').style.height= objGrid.gridHeight +"px";
        masterLinguagem.grid = new dhtmlXGridObject( 'gridMasterLinguagem' );
        masterLinguagem.grid.setHeader( masterLinguagem.gridHeader );
        masterLinguagem.grid.attachHeader("#rspan,#text_filter,#text_filter,#select_filter");
        masterLinguagem.grid.setInitWidths( objGrid.idWidth + ",*,100,100");
        masterLinguagem.grid.setColAlign("center,left,left,left");
        masterLinguagem.grid.setColTypes("ro,ro,ro,ro");
        masterLinguagem.grid.setColSorting("str,str,str,str");
        masterLinguagem.grid.setSkin( objGrid.theme );
        masterLinguagem.grid.enablePaging( true, objGrid.pagResults, objGrid.pagIndexes, 'divpagMasterLinguagemGrid', true, 'divpagMasterLinguagem');
        masterLinguagem.grid.attachFooter( objGrid.tituloTotal + ",#cspan,#cspan,{#stat_count}" );
        masterLinguagem.grid.attachEvent( 'onRowDblClicked', masterLinguagem.edit );
        masterLinguagem.grid.init();
        masterLinguagem.grid.load( baseUrl + '/master/linguagem/list', dojo.hitch( objGeral, 'loading', false ) , "json");
    },
    
    novo: function()
    {
        objGeral.createDialog( 
                '/master/linguagem/form/', 
                objGeral.translate('Linguagem')
         );
    },
    
    edit: function()
    {
        if ( !masterLinguagem.grid.getSelectedId() ) {
            
            objGeral.msgAlerta( objGeral.translate('Selecione o item para edição.') );
            return false;
        }
            
        objGeral.createDialog( 
                '/master/linguagem/edit/id/' + masterLinguagem.grid.getSelectedId(), 
                objGeral.translate('Linguagem')
            );
        
        return true;
    },
    
    atualizarGrid: function()
    {
        objGeral.atualizarGrids( [masterLinguagem.grid] );
    }
});

var masterLinguagem = new modulo.master.linguagem();