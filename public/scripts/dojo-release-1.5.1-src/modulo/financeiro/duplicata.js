dojo.require( 'modulo.padrao.geral' );
dojo.require( 'modulo.padrao.grid' );

dojo.provide( 'modulo.financeiro.duplicata' );

dojo.declare( 'modulo.financeiro.duplicata', [modulo.padrao.geral, modulo.padrao.grid],
{
    constructor: function()
    {
    },

    initGrid: function()
    {
        objGeral.loading( true );
        
        financeiroDuplicata.gridHeader = '#,'+
                                    objGeral.translate('Data')+','+
                                    objGeral.translate('Terceiro')+','+
                                    objGeral.translate('Documento Fiscal')+','+
                                    objGeral.translate('Situação')+','+
                                    objGeral.translate('Parcelas')+','+
                                    objGeral.translate('Tipo')+','+
                                    objGeral.translate('Valor');
                                
        document.getElementById("gridFinanceiroDuplicata").style.height= objGrid.gridHeight +"px";
        financeiroDuplicata.grid = new dhtmlXGridObject('gridFinanceiroDuplicata');
        financeiroDuplicata.grid.setHeader( financeiroDuplicata.gridHeader );
        financeiroDuplicata.grid.attachHeader("#rspan,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#select_filter,#text_filter");
        financeiroDuplicata.grid.setInitWidths( objGrid.idWidth + ",90,*,300,120,80,120,200");
        financeiroDuplicata.grid.setColAlign("center,center,left,left,left,center,left,left");
        financeiroDuplicata.grid.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro");
        financeiroDuplicata.grid.setColSorting("str,date,str,str,str,str,str,na");
        financeiroDuplicata.grid.setSkin( objGrid.theme );
        financeiroDuplicata.grid.enablePaging( true, objGrid.pagResults, objGrid.pagIndexes, 'divpagFinanceiroDuplicataGrid', true, 'divpagFinanceiroDuplicata');
        financeiroDuplicata.grid.attachFooter( objGeral.translate('Registros') + ",#cspan,#cspan,#cspan,#cspan,{#stat_count}," + objGeral.translate('Total R$') + ",{#stat_total}");
        financeiroDuplicata.grid.attachEvent( 'onRowDblClicked', financeiroDuplicata.edit );
        financeiroDuplicata.grid.init();
        financeiroDuplicata.grid.load( baseUrl + '/financeiro/duplicata/list', dojo.hitch( objGeral, 'loading', false ) , "json");
    },
    
    novo: function()
    {
        objGeral.createDialog( 
                '/financeiro/duplicata/form/',
                objGeral.translate('Duplicata')
            );
    },
    
    edit: function()
    {
        if ( !financeiroDuplicata.grid.getSelectedId() ) {
            
            objGeral.msgAlerta( objGeral.translate('Selecione o item para edi&ccedil;&atilde;o.') );
            return false;
        }
            
        objGeral.createDialog( 
                '/financeiro/duplicata/edit/id/' + financeiroDuplicata.grid.getSelectedId(),
                objGeral.translate('Duplicata')
            );
        
        return true;
    },
    
    atualizarGrid: function()
    {
        objGeral.atualizarGrids( [financeiroDuplicata.grid] );
    },
    
    geraPacelamento: function()
    {
        var vlTotal   = dijit.byId( "fn_duplicata_total" ).attr('value');
        var vlQtdParc = dijit.byId( "fn_duplicata_parcelas" ).attr('value');
        var vlDataAtu = dojo.date.stamp.fromISOString( dojo.byId( "fn_duplicata_data" ).value );
        
        objGeral.loading( true );
        
        var vlParc = vlTotal / vlQtdParc ;
        vlParc = vlParc.toFixed(2);
        
        var vlax = parseFloat(vlParc);
        var vlParc1 = (vlTotal - (vlax * vlQtdParc)) + vlax;

        this.addPacelaDuplicata('',null,0);
        objGeral.deleteRow( '#tbl-parcelas-duplicata tr' );
        
        if(vlQtdParc > 72 ){
            
            objGeral.loading( false );
            objGeral.msgAlerta( objGeral.translate('Limite de parcelas atingido.') );
            return false;
        }
        for (i=1;i<=vlQtdParc;i++)
        {
            vlDataAtu = dojo.date.add( vlDataAtu, "day", 30);
            //vlDataAtu = dojo.date.add( vlDataAtu, "month", 1);
            if(i == 1)
                this.addPacelaDuplicata( i+'/'+vlQtdParc, vlDataAtu, vlParc1);
            else
                this.addPacelaDuplicata( i+'/'+vlQtdParc, vlDataAtu, vlParc);
        }
        
        objGeral.loading( false );

    },
    
    addPacelaDuplicata: function( txtParc , vlData, vlValor)
    {
	var tdFieldParce = $('<td />');
	var tdFieldVence = $('<td />');
	var tdFieldValor = $('<td />');
	var tdFieldSitua = $('<td />');
	var tr           = $('<tr />');
        
        
	var fieldParce = txtParc;
                    
	var fieldVence = new dijit.form.DateTextBox({
                            name: 'fn_lancamento_data[]',
                            required: true,
                            value: vlData
                    });
                    
	var fieldValor = new dijit.form.CurrencyTextBox({
                            name: 'fn_lancamento_valor[]',
                            required: true,
                            constraints: {min:1},
                            currency: 'R$ ',
                            value: parseFloat(vlValor)
                    });
                    
        var fieldSitua = '-';
        
	tdFieldParce.append( fieldParce );
        tdFieldVence.append( fieldVence.domNode );
        tdFieldValor.append( fieldValor.domNode );
        tdFieldSitua.append( fieldSitua );
        
	tr.append( tdFieldParce ).append( tdFieldVence ).append( tdFieldValor ).append( tdFieldSitua );

	$('#tbl-parcelas-duplicata').append( tr );
        
        return true;
    },
    
    buscaDocFiscal:function()
    {
        dijit.byId( 'dl_terceiro_id_remetente' ).attr('value','');
        dijit.byId( 'dl_terceiro_id_destinatario' ).attr('value','');
        dijit.byId( 'dl_fn_doc_fiscal_numero' ).attr('value','');
        dijit.byId( 'dl_fn_doc_fiscal_data' ).attr('value','');
        financeiroDocumentoFiscal.gridBuscaDocFical();
        dijit.byId( 'dialogBuscaDocFiscal' ).show();
    },
    
    deletaDuplicata: function()
    {	
        if (confirm("Você está preste a excluir definitivamente uma duplicata!\nDeseja continuar?")){

            identify = document.getElementById( 'fn_duplicata_id' ).value;
            objGeral.buscaAjax({
                    url: baseUrl + '/financeiro/duplicata/delet/',
                    data: {identify: identify},
                    handle: 'json',
                    callback: function( response )
                    {
                        if(response.status){

                            objGeral.closeGenericDialog();
                            financeiroDuplicata.atualizarGrid();
                            objGeral.msgSucesso( response.description[0].message );
                        }else{

                            objGeral.msgAlerta( response.description[0].message );
                        }
                    }
            });
        }else{

            return false
        }
    }
});

var financeiroDuplicata = new modulo.financeiro.duplicata();