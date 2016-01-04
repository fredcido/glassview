dojo.require( 'modulo.padrao.geral' );
dojo.require( 'modulo.padrao.grid' );

dojo.provide( 'modulo.master.linguagemtermo' );

dojo.declare( 'modulo.master.linguagemtermo', [modulo.padrao.geral, modulo.padrao.grid],
{
    constructor: function()
    {
    },

    initGrid: function()
    {
        objGeral.loading( true );
        
        masterLinguagemTermo.gridHeader = '#,'+objGeral.translate('Termo');
        
        document.getElementById('gridMasterLinguagemTermo').style.height= objGrid.gridHeight +"px";
        masterLinguagemTermo.grid = new dhtmlXGridObject( 'gridMasterLinguagemTermo' );
        masterLinguagemTermo.grid.setHeader( masterLinguagemTermo.gridHeader );
        masterLinguagemTermo.grid.attachHeader("#rspan,#text_filter");
        masterLinguagemTermo.grid.setInitWidths( objGrid.idWidth + ",*");
        masterLinguagemTermo.grid.setColAlign("center,left");
        masterLinguagemTermo.grid.setColTypes("ro,ro");
        masterLinguagemTermo.grid.setColSorting("str,str");
        masterLinguagemTermo.grid.setSkin( objGrid.theme );
        masterLinguagemTermo.grid.enablePaging( true, objGrid.pagResults, objGrid.pagIndexes, 'divpagMasterLinguagemTermoGrid', true, 'divpagMasterLinguagemTermo');
        masterLinguagemTermo.grid.attachFooter( objGrid.tituloTotal + ",{#stat_count}" );
        masterLinguagemTermo.grid.attachEvent( 'onRowDblClicked', masterLinguagemTermo.edit );
        masterLinguagemTermo.grid.init();
        masterLinguagemTermo.grid.load( baseUrl + '/master/linguagem-termo/list', dojo.hitch( objGeral, 'loading', false ) , "json");
    },
    
    novo: function()
    {
        objGeral.createDialog( 
                '/master/linguagem-termo/form/', 
                objGeral.translate('Termo de Linguagem')
            );
    },
    
    edit: function()
    {
        if ( !masterLinguagemTermo.grid.getSelectedId() ) {
            
            objGeral.msgAlerta( objGeral.translate('Selecione o item para edição.') );
            return false;
        }
            
        objGeral.createDialog( 
                    '/master/linguagem-termo/edit/id/' + masterLinguagemTermo.grid.getSelectedId(), 
                    objGeral.translate('Termo de Linguagem')
                );
        
        return true;
    },
    
    atualizarGrid: function()
    {
        objGeral.atualizarGrids( [masterLinguagemTermo.grid] );
    }
});

var masterLinguagemTermo = new modulo.master.linguagemtermo();