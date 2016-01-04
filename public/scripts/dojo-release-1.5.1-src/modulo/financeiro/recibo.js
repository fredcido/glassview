dojo.require( 'modulo.padrao.geral' );
dojo.require( 'modulo.padrao.grid' );

dojo.provide( 'modulo.financeiro.recibo' );

dojo.declare( 'modulo.financeiro.recibo', [modulo.padrao.geral, modulo.padrao.grid],
{
    constructor: function()
    {
    },

    initGrid: function()
    {
        objGeral.loading( true );
        
        financeiroRecibo.gridHeader = '#,#master_checkbox,'+
                                    objGeral.translate('ID')+','+
                                    objGeral.translate('De/Para')+','+
                                    objGeral.translate('CPF/CNPJ')+','+
                                    objGeral.translate('Valor')+','+
                                    objGeral.translate('Data');
	
        document.getElementById('gridFinanceiroRecibo').style.height= objGrid.gridHeight +"px";
        financeiroRecibo.grid = new dhtmlXGridObject( 'gridFinanceiroRecibo' );
        financeiroRecibo.grid.setHeader( financeiroRecibo.gridHeader );
        financeiroRecibo.grid.attachHeader("#rspan,#rspan,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter");
        financeiroRecibo.grid.setInitWidths( objGrid.idWidth + ",40,120,*,200,200,100");
        financeiroRecibo.grid.setColAlign("center,center,center,left,left,left,left");
        financeiroRecibo.grid.setColTypes("ro,ch,ro,ro,ro,ro,ro");
        financeiroRecibo.grid.setColSorting("str,str,int,str,str,na,date");
	financeiroRecibo.grid.setCustomSorting( dojo.hitch( objGeral, 'sortCurrency' ), 5 );
        financeiroRecibo.grid.setSkin( objGrid.theme );
        financeiroRecibo.grid.enablePaging( true, objGrid.pagResults, objGrid.pagIndexes, 'divpagFinanceiroReciboGrid', true, 'divpagfinanceiroRecibo');
        financeiroRecibo.grid.attachFooter( objGrid.tituloTotal + ",#cspan,#cspan,#cspan,#cspan,#cspan,{#stat_count}");
        financeiroRecibo.grid.attachEvent( 'onRowDblClicked', financeiroRecibo.edit );
        financeiroRecibo.grid.init();
        financeiroRecibo.grid.load( baseUrl + '/financeiro/recibo/list', dojo.hitch( objGeral, 'loading', false ) , "json");
    },
    
    novo: function()
    {
        objGeral.createDialog( 
                '/financeiro/recibo/form/',
                objGeral.translate( 'Recibo' )
            );
    },
    
    edit: function()
    {
        if ( !financeiroRecibo.grid.getSelectedId() ) {
            
            objGeral.msgAlerta( objGeral.translate('Selecione o item para edição.') );
            return false;
        }
            
        objGeral.createDialog( 
                '/financeiro/recibo/edit/id/' + financeiroRecibo.grid.getSelectedId(),
                objGeral.translate('Recibo')
            );
        
        return true;
    },
    
    atualizarGrid: function()
    {
        objGeral.atualizarGrids( [financeiroRecibo.grid] );
    },
    
    buscaDadosReceptor: function()
    {
	var terceiro = dijit.byId( 'receptor' ).get( 'value' );
	
	dijit.byId( 'terceiro_cpf_cnpj' ).set( 'value', null );
	
	if ( objGeral.empty( terceiro ) )
	    return false;
	    
	var obj = {
	    url: baseUrl + '/financeiro/recibo/dados-receptor/id/' + terceiro,
	    handle: 'json',
	    callback: function( response )
	    {
		dijit.byId( 'terceiro_cpf_cnpj' ).set( 'value', response.cpf_cnpj );
	    }
	};
	
	objGeral.buscaAjax( obj );
	
	return true;
    },
    
    imprimir: function()
    {
	var recibos = financeiroRecibo.grid.getCheckedRows(1);
            	    
	if ( objGeral.empty( recibos ) ) {
            objGeral.msgAlerta( objGeral.translate( 'Selecione o recibo para imprimir.' ) );
            return false;
        }
            
        location.href = baseUrl + '/financeiro/recibo/imprimir/recibos/' + recibos;
                
        return true;
    },
    
    afterSubmit: function( response )
    {
	this.atualizarGrid();
	
	if ( confirm( objGeral.translate( 'Deseja imprimir o recibo agora?' ) ) )
	    location.href = baseUrl + '/financeiro/recibo/imprimir/recibos/' + response.id;
    }
});

var financeiroRecibo = new modulo.financeiro.recibo();