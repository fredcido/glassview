dojo.require( 'modulo.padrao.geral' );
dojo.require( 'modulo.padrao.grid' );

dojo.provide( 'modulo.financeiro.cheque' );

dojo.declare( 'modulo.financeiro.cheque', [modulo.padrao.geral, modulo.padrao.grid],
{
    constructor: function()
    {
    },

    initGrid: function()
    {
        objGeral.loading( true );
        
        financeiroCheque.gridHeader = '#,'+
                                    objGeral.translate('Banco')+','+
                                    objGeral.translate('Conta')+','+
                                    objGeral.translate('Número')+','+
                                    objGeral.translate('Emitido para')+','+
                                    objGeral.translate('Emissão')+','+
                                    objGeral.translate('Para')+','+
                                    objGeral.translate('Situação');
	
        document.getElementById('gridFinanceiroCheque').style.height= objGrid.gridHeight +"px";
        financeiroCheque.grid = new dhtmlXGridObject('gridFinanceiroCheque');
        financeiroCheque.grid.setHeader( financeiroCheque.gridHeader );
        financeiroCheque.grid.attachHeader("#rspan,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter");
        financeiroCheque.grid.setInitWidths( objGrid.idWidth + ",200,200,200,*,90,90,120");
        financeiroCheque.grid.setColAlign("center,left,left,left,left,left,left,left");
        financeiroCheque.grid.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro");
        financeiroCheque.grid.setColSorting("str,str,str,str,str,str,str,str");
        financeiroCheque.grid.setSkin( objGrid.theme );
        financeiroCheque.grid.enablePaging( true, objGrid.pagResults, objGrid.pagIndexes, 'divpagFinanceiroChequeGrid', true, 'divpagfinanceiroCheque');
        financeiroCheque.grid.attachFooter( objGrid.tituloTotal + ",#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,{#stat_count}");
        financeiroCheque.grid.attachEvent( 'onRowDblClicked', financeiroCheque.edit );
        financeiroCheque.grid.init();
        financeiroCheque.grid.load( baseUrl + '/financeiro/cheque/list', dojo.hitch( objGeral, 'loading', false ) , "json");
    },
    
    novo: function()
    {
        objGeral.createDialog( 
                '/financeiro/cheque/form/',
                objGeral.translate('Cheque')
            );
    },
    
    edit: function()
    {
        if ( !financeiroCheque.grid.getSelectedId() ) {
            
            objGeral.msgAlerta( objGeral.translate('Selecione o item para edição.') );
            return false;
        }
            
        objGeral.createDialog( 
                '/financeiro/cheque/edit/id/' + financeiroCheque.grid.getSelectedId(),
                objGeral.translate('Cheque')
            );
        
        return true;
    },
    
    atualizarGrid: function()
    {
        objGeral.atualizarGrids( [financeiroCheque.grid] );
    },
    
    gridBuscaCheque: function()
    {
        objGeral.loading( true );

        financeiroCheque.gridHeader = '#,'+
                                    objGeral.translate('Banco')+','+
                                    objGeral.translate('Conta')+','+
                                    objGeral.translate('Cheque')+','+
                                    objGeral.translate('Valor');

        financeiroCheque.grid = new dhtmlXGridObject('gridBuscaFinanceiroCheque');
        financeiroCheque.grid.setHeader( financeiroCheque.gridHeader );
        financeiroCheque.grid.attachHeader("#rspan,#text_filter,#text_filter,#text_filter,#text_filter");
        financeiroCheque.grid.setInitWidths( objGrid.idWidth + ",*,150,150,150");
        financeiroCheque.grid.setColAlign("center,left,center,left,left");
        financeiroCheque.grid.setColTypes("ch,ro,ro,ro,price");
        financeiroCheque.grid.setColSorting("str,str,str,str,str");
        financeiroCheque.grid.setSkin( objGrid.theme );
        financeiroCheque.grid.enablePaging( true, objGrid.pagResults, objGrid.pagIndexes, 'divpagBuscaFinanceiroChequeGrid', true, 'divpagBuscaFinanceiroCheque');
        financeiroCheque.grid.attachFooter( objGrid.tituloTotal + ",#cspan,#cspan,#cspan,{#stat_count}");
        financeiroCheque.grid.attachEvent(
        	"onCheckbox", 
        	function ( rowId, cellInd, state )
        	{
        		if ( state ) {
        			
        			$('#formfinanceirolancamento').append( 
        				$('<input type="hidden" name="lancamento[fn_cheque_id][]">').val( rowId ) 
        			);
        			
        		} else {
        			
        			$('#formfinanceirolancamento input[type="hidden"]').each(
					    function( indice, node )
						{
							if ( 'lancamento[fn_cheque_id][]' == node.name && rowId == node.value )
								$(node).remove();
						}
					);
        			
        		}
				
        	}
        );
        financeiroCheque.grid.init();        
        financeiroCheque.grid.load( 
        	baseUrl + '/financeiro/lancamento/lista-cheque/id/' + dojo.byId('lancamento-fn_lancamento_id').value, 
        	function( v )
        	{     		
				$('input[type=hidden]').each(
					function ( indice, node )
					{
						if ( 'lancamento[fn_cheque_id][]' == node.name ) {
							
							var count = financeiroCheque.grid.cells.length;
							var i = 0;
							
							for ( ; i < count; i++ ) {
								if ( node.value == financeiroCheque.grid.getRowId( i ) )
									financeiroCheque.grid.cells( (i + 1), 0).setValue( true );
							}
								
						}
					}
				);
        		
        		objGeral.loading( false );
        	}, 
        	"json"
        );
    }
});

var financeiroCheque = new modulo.financeiro.cheque();