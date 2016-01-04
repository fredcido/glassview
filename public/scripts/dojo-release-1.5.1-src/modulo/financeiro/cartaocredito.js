dojo.require( 'modulo.padrao.geral' );
dojo.require( 'modulo.padrao.grid' );

dojo.provide( 'modulo.financeiro.cartaocredito' );

dojo.declare( 'modulo.financeiro.cartaocredito', [modulo.padrao.geral, modulo.padrao.grid],
{
    constructor: function()
    {
    },

    initGrid: function()
    {
        objGeral.loading( true );
        
        financeiroCartaoCredito.gridHeader = '#,'+
                                                objGeral.translate('Descrição')+','+
                                                objGeral.translate('Titular')+','+
                                                objGeral.translate('Número')+','+
                                                objGeral.translate('Validade')+','+
                                                objGeral.translate('Status');
	
        document.getElementById('gridFinanceiroCartaoCredito').style.height= objGrid.gridHeight +"px";
        financeiroCartaoCredito.grid = new dhtmlXGridObject('gridFinanceiroCartaoCredito');
        financeiroCartaoCredito.grid.setHeader( financeiroCartaoCredito.gridHeader );
        financeiroCartaoCredito.grid.attachHeader("#rspan,#text_filter,#text_filter,#text_filter,#text_filter,#select_filter");
        financeiroCartaoCredito.grid.setInitWidths( objGrid.idWidth + ",*,300,200,200,200");
        financeiroCartaoCredito.grid.setColAlign("center,left,left,left,left,left");
        financeiroCartaoCredito.grid.setColTypes("ro,ro,ro,ro,ro,ro");
        financeiroCartaoCredito.grid.setColSorting("str,str,str,str,str,str");
        financeiroCartaoCredito.grid.setSkin( objGrid.theme );
        financeiroCartaoCredito.grid.enablePaging( true, objGrid.pagResults, objGrid.pagIndexes, 'divpagFinanceiroCartaoCreditoGrid', true, 'divpagfinanceiroCartaoCredito');
        financeiroCartaoCredito.grid.attachFooter( objGrid.tituloTotal + ",#cspan,#cspan,#cspan,#cspan,{#stat_count}");
        financeiroCartaoCredito.grid.attachEvent( 'onRowDblClicked', financeiroCartaoCredito.edit );
        financeiroCartaoCredito.grid.init();
        financeiroCartaoCredito.grid.load( baseUrl + '/financeiro/cartao-credito/list', dojo.hitch( objGeral, 'loading', false ) , "json");
    },
    
    novo: function()
    {
        objGeral.createDialog( 
                '/financeiro/cartao-credito/form/', 
                objGeral.translate('Cartão de Crédito')
            );
    },
    
    edit: function()
    {
        if ( !financeiroCartaoCredito.grid.getSelectedId() ) {
            
            objGeral.msgAlerta( objGeral.translate('Selecione o item para edição.') );
            return false;
        }
            
        objGeral.createDialog( 
                '/financeiro/cartao-credito/edit/id/' + financeiroCartaoCredito.grid.getSelectedId(), 
                objGeral.translate('Cartão de Crédito')
            );
        
        return true;
    },
    
    atualizarGrid: function()
    {
        objGeral.atualizarGrids( [financeiroCartaoCredito.grid] );
    }
});

var financeiroCartaoCredito = new modulo.financeiro.cartaocredito();