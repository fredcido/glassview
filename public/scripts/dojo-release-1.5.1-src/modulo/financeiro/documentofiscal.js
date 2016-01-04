dojo.require( 'modulo.padrao.geral' );
dojo.require( 'modulo.padrao.grid' );

dojo.provide( 'modulo.financeiro.documentofiscal' );

dojo.declare( 'modulo.financeiro.documentofiscal', [modulo.padrao.geral, modulo.padrao.grid],
{
    constructor: function()
    {
    },

    initGrid: function()
    {
        objGeral.loading( true );
        
        financeiroDocumentoFiscal.gridHeader = '#,'+
                                    objGeral.translate('Número')+','+
                                    objGeral.translate('Data')+','+
                                    objGeral.translate('Valor')+','+
                                    objGeral.translate('Remetente')+','+
                                    objGeral.translate('Destinatário');
	
        document.getElementById('gridFinanceiroDocumentoFiscal').style.height= objGrid.gridHeight +"px";
        financeiroDocumentoFiscal.grid = new dhtmlXGridObject('gridFinanceiroDocumentoFiscal');
        financeiroDocumentoFiscal.grid.setHeader( financeiroDocumentoFiscal.gridHeader );
        financeiroDocumentoFiscal.grid.attachHeader("#rspan,#text_filter,#text_filter,#text_filter,#select_filter,#select_filter");
        financeiroDocumentoFiscal.grid.setInitWidths( objGrid.idWidth + ",*,100,100,300,300");
        financeiroDocumentoFiscal.grid.setColAlign("center,left,center,right,left,left");
        financeiroDocumentoFiscal.grid.setColTypes("ro,ro,ro,ro,ro,ro");
        financeiroDocumentoFiscal.grid.setColSorting("str,str,str,str,str,str");
        financeiroDocumentoFiscal.grid.setSkin( objGrid.theme );
        financeiroDocumentoFiscal.grid.enablePaging( true, objGrid.pagResults, objGrid.pagIndexes, 'divpagFinanceiroDocumentoFiscalGrid', true, 'divpagfinanceiroDocumentoFiscal');
        financeiroDocumentoFiscal.grid.attachFooter( objGrid.tituloTotal + ",#cspan,#cspan,#cspan,#cspan,{#stat_count}");
        financeiroDocumentoFiscal.grid.attachEvent( 'onRowDblClicked', financeiroDocumentoFiscal.edit );
        financeiroDocumentoFiscal.grid.init();
        financeiroDocumentoFiscal.grid.load( baseUrl + '/financeiro/documento-fiscal/list', dojo.hitch( objGeral, 'loading', false ) , "json");
    },
    
    novo: function()
    {
        objGeral.createDialog( 
                '/financeiro/documento-fiscal/form/',
                objGeral.translate('Documento Fiscal')
            );
    },
    
    edit: function()
    {
        if ( !financeiroDocumentoFiscal.grid.getSelectedId() ) {
            
            objGeral.msgAlerta( objGeral.translate('Selecione o item para edição.') );
            return false;
        }
            
        objGeral.createDialog( 
                '/financeiro/documento-fiscal/edit/id/' + financeiroDocumentoFiscal.grid.getSelectedId(),
                objGeral.translate('Documento Fiscal'),
                financeiroDocumentoFiscal.somaTotalNota
            );
        
        return true;
    },
    
    atualizarGrid: function()
    {
        objGeral.atualizarGrids( [financeiroDocumentoFiscal.grid] );
    },

    addItenDocFiscal: function()
    {

	var tdFieldDesc   = $('<td />');
	var tdFieldQtd    = $('<td />');
	var tdFieldValorU = $('<td />');
	var tdFieldValorT = $('<td />');
	var tdExcl        = $('<td />');
	var tr            = $('<tr />');

	var fieldDesc = new dijit.form.ValidationTextBox({
                        name: 'fn_doc_fiscal_item_descricao[]',
                        required: true
                    });

	var fieldQtd = new dijit.form.NumberSpinner({
                        name: 'fn_doc_fiscal_item_qtde[]',
                        constraints: {min:1},
                        required: true,
                        value: 0
                    });


	var fieldValorU = new dijit.form.CurrencyTextBox({
                            name: 'fn_doc_fiscal_item_valor[]',
                            required: true,
                            currency: 'R$ ',
                            value: 0
                    });

	var fieldValorT = new dijit.form.CurrencyTextBox({
                            name: 'fn_doc_fiscal_item_total[]',
                            required: true,
                            currency: 'R$ ',
                            readOnly: true,
                            value: 0
                    });

        dojo.connect(
                fieldQtd,
                "onChange",
                null,
                function()
                {
                    financeiroDocumentoFiscal.somaValorTotal( fieldQtd.id, fieldValorU.id , fieldValorT.id);
                }
            );

        dojo.connect(
                fieldValorU,
                "onChange",
                null,
                function()
                {
                    financeiroDocumentoFiscal.somaValorTotal( fieldQtd.id, fieldValorU.id , fieldValorT.id);
                }
            );
	tdFieldDesc.append(   fieldDesc.domNode );
        tdFieldQtd.append(    fieldQtd.domNode );
        tdFieldValorU.append( fieldValorU.domNode );
        tdFieldValorT.append( fieldValorT.domNode );

	var excluir = $('<div />');
	excluir.addClass( 'icon-toolbar-cancel' ).click(
	    function()
	    {
		if ( !confirm( objGeral.translate('Deseja realmente remover este item?') ) )
			return false;

		objGeral.deleteRow( $(this).parent().parent() );
		return true;
	    }
	).attr( 'title', objGeral.translate('Remover Lançamento') ).css( 'cursor', 'pointer' );

	tdExcl.append( excluir );

	tr.append( tdFieldDesc ).append( tdFieldQtd ).append( tdFieldValorU ).append( tdFieldValorT ).append( tdExcl );

	$('#tbl-itens-doc-fiscal').append( tr );

    },

    somaValorTotal:function( idQtd, idVlu, idVlt)
    {
        objGeral.loading( true );
        
        var qtdS = dijit.byId( idQtd ).attr('value');
        var vlrS = dijit.byId( idVlu ).attr('value');
        var ttlS = qtdS * vlrS;
        dijit.byId( idVlt ).attr('value',ttlS);
        financeiroDocumentoFiscal.somaTotalNota();
    },
    
    somaTotalNota: function(){
        
        var xhrPost = dojo.xhrPost({
            form: "formfinanceirodocumentofiscal",
            handleAs: "json",
            url: baseUrl + "/financeiro/documento-fiscal/somanota",
            load: function( response ){
                objGeral.loading( false );
                dijit.byId( 'fn_doc_fiscal_valor_total_da_nota' ).attr('value', response.total);
            },
            error: function(error){

                objGeral.msgErro( objGeral.translate('Erro ao executar operação') );
            }
        });
    },

    removeItenDocFiscal: function( img )
    {
	if ( !confirm( objGeral.translate('Deseja realmente remover este item?') ) )
	    return false;

	objGeral.deleteRow( $(img).parent().parent() );
	return true;
    },

    gridBuscaDocFical: function()
    {
        objGeral.loading( true );

        financeiroDocumentoFiscal.gridHeader = '#,'+
                                    objGeral.translate('Número')+','+
                                    objGeral.translate('Data')+','+
                                    objGeral.translate('Remetente')+','+
                                    objGeral.translate('Destinatário')+','+
                                    objGeral.translate('Valor');

        financeiroDocumentoFiscal.grid = new dhtmlXGridObject('gridBuscaFinanceiroDocumentoFiscal');
        financeiroDocumentoFiscal.grid.setHeader( financeiroDocumentoFiscal.gridHeader );
        financeiroDocumentoFiscal.grid.attachHeader("#rspan,#text_filter,#text_filter,#select_filter,#select_filter,#text_filter");
        financeiroDocumentoFiscal.grid.setInitWidths( objGrid.idWidth + ",*,90,120,120,100");
        financeiroDocumentoFiscal.grid.setColAlign("center,left,center,left,left,right");
        financeiroDocumentoFiscal.grid.setColTypes("ro,ro,ro,ro,ro,ro");
        financeiroDocumentoFiscal.grid.setColSorting("str,str,str,str,str,str");
        financeiroDocumentoFiscal.grid.setSkin( objGrid.theme );
        financeiroDocumentoFiscal.grid.enablePaging( true, objGrid.pagResults, objGrid.pagIndexes, 'divpagBuscaFinanceiroDocumentoFiscalGrid', true, 'divpagfinanceiroDocumentoFiscal');
        financeiroDocumentoFiscal.grid.attachFooter( objGrid.tituloTotal + ",#cspan,#cspan,#cspan,#cspan,{#stat_count}");
        financeiroDocumentoFiscal.grid.init();
        financeiroDocumentoFiscal.grid.attachEvent("onRowSelect", function(id){
            
            dijit.byId( 'dialogBuscaDocFiscal' ).hide();
            dijit.byId( 'fn_doc_fiscal_numero' ).attr('value',id[1]);
            dojo.byId(  'fn_doc_fiscal_id' ).value = id[0];
            
            if (dijit.byId('terceiro_id'))
                dijit.byId('terceiro_id').attr('value', id[2]);
            
            if (dijit.byId('lancamento-terceiro_id'))
                dijit.byId('lancamento-terceiro_id').attr('value', id[2]);
            
            if (dijit.byId('fn_duplicata_total')){
                
                dijit.byId('fn_duplicata_total').attr('value', id[3]);
                dijit.byId('fn_duplicata_total').focus();
                financeiroDuplicata.geraPacelamento();
            }
          
        });
        financeiroDocumentoFiscal.grid.load( baseUrl + '/financeiro/documento-fiscal/lista-documentos-fiscais', dojo.hitch( objGeral, 'loading', false ) , "json");
    },

    gridFiltraDocFical: function()
    {
        objGeral.loading( true );

        var rem = dijit.byId( 'dl_terceiro_id_remetente' ).attr('value');
        var des = dijit.byId( 'dl_terceiro_id_destinatario' ).attr('value');
        var nro = dijit.byId( 'dl_fn_doc_fiscal_numero' ).attr('value');
        var dat = dijit.byId( 'dl_fn_doc_fiscal_data' ).attr('value');

        if( dat != null || !objGeral.empty(dat) ){

            dat = objGeral.formateDate( dat );
        }else{

            dat = '';
        }
        
        var vget = '/rem/'+rem+'/des/'+des+'/nro/'+nro+'/dat/'+dat;

        financeiroDocumentoFiscal.grid.clearAll();
        financeiroDocumentoFiscal.grid.load( baseUrl + '/financeiro/documento-fiscal/lista-documentos-fiscais'+vget, dojo.hitch( objGeral, 'loading', false ) , "json");
    }

});

var financeiroDocumentoFiscal = new modulo.financeiro.documentofiscal();
