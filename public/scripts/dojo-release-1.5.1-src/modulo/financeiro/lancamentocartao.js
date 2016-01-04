dojo.require( 'modulo.padrao.geral' );
dojo.require( 'modulo.padrao.grid' );

dojo.provide( 'modulo.financeiro.lancamentocartao' );

dojo.declare( 'modulo.financeiro.lancamentocartao', [modulo.padrao.geral, modulo.padrao.grid],
{
    gridLancamentosPendentes: null,
    
    gridLancamentosEfetivados: null,
    
    liberaDragDrop: false,
    
    dadosEfetivar: null,
    
    tiposLancamentosAgrupados: null,
    
    constructor: function()
    {
        this.noChangeTipoLancamento = false;
        this.noDoubleClickTree = null;
    },

    initGrid: function()
    {
        objGeral.loading( true );
        
        financeiroLancamentoCartao.gridHeader = '#,'+
                                                objGeral.translate('Data')+','+
                                                objGeral.translate('Cartão de Crédito')+','+
                                                objGeral.translate('Descrição')+','+
                                                objGeral.translate('Valor')+','+
                                                objGeral.translate('Status');
	
        document.getElementById('gridFinanceiroLancamentoCartao').style.height= objGrid.gridHeight +"px";
        financeiroLancamentoCartao.grid = new dhtmlXGridObject('gridFinanceiroLancamentoCartao');
        financeiroLancamentoCartao.grid.setHeader( financeiroLancamentoCartao.gridHeader );
        financeiroLancamentoCartao.grid.attachHeader("#rspan,#text_filter,#select_filter,#text_filter,#text_filter,#select_filter");
        financeiroLancamentoCartao.grid.setInitWidths( objGrid.idWidth + ",80,220,*,110,100");
        financeiroLancamentoCartao.grid.setColAlign("center,left,left,left,left,left");
        financeiroLancamentoCartao.grid.setColTypes("ro,ro,ro,ro,ro,ro");
        financeiroLancamentoCartao.grid.setColSorting("str,date,str,str,str,str");
	financeiroLancamentoCartao.grid.setCustomSorting( dojo.hitch( objGeral, 'sortCurrency' ), 3 );
        financeiroLancamentoCartao.grid.setSkin( objGrid.theme );
        financeiroLancamentoCartao.grid.enablePaging( true, objGrid.pagResults, objGrid.pagIndexes, 'divpagFinanceiroLancamentoCartaoGrid', true, 'divpagfinanceiroLancamentoCartao');
        financeiroLancamentoCartao.grid.attachFooter( objGrid.tituloTotal + ",#cspan,#cspan,#cspan,{#stat_total},{#stat_count}");
        financeiroLancamentoCartao.grid.attachEvent( 'onRowDblClicked', financeiroLancamentoCartao.edit );
        financeiroLancamentoCartao.grid.init();
        financeiroLancamentoCartao.grid.load( baseUrl + '/financeiro/lancamento-cartao/list', dojo.hitch( objGeral, 'loading', false ) , "json");
    },
    
    novo: function()
    {
        objGeral.createDialog( 
                '/financeiro/lancamento-cartao/form/', 
                objGeral.translate('Lançamentos Cartão')
            );
    },
    
    edit: function()
    {
        if ( !financeiroLancamentoCartao.grid.getSelectedId() ) {
            
            objGeral.msgAlerta( objGeral.translate('Selecione o item para edição.') );
            return false;
        }
            
        objGeral.createDialog( 
                '/financeiro/lancamento-cartao/edit/id/' + financeiroLancamentoCartao.grid.getSelectedId(), 
                objGeral.translate('Cartão de Credito')
            );
        
        return true;
    },
    
    atualizarGrid: function()
    {
        objGeral.atualizarGrids( [financeiroLancamentoCartao.grid] );
    },
    
    filteringTipoLancamento: function( idFiltering )
    {
        var index = parseInt( $(idFiltering).attr('id').replace(/^[a-zA-Z_]+/g, '') );
        dijit.byId( 'tipolan_' + index ).attr( 'value','' );
        document.getElementById( 'vltplan_' + index ).value = '';
    },

    addTipoLancamento: function()
    {
        objGeral.loading( true );
        
        var hashid = this.geraHashId();
        
	var tdFieldProjeto = $('<td />');
	var tdFieldTipo    = $('<td />');
	var tdFieldButton  = $('<td />');
	var tdFieldValor   = $('<td />');
	var tdExcl         = $('<td />');
	var tr             = $('<tr />');
        
        var storeTipoLanc = new dojo.data.ItemFileReadStore({
            url:  baseUrl + "/financeiro/lancamento-cartao/tipolancamentolist"
        });

	var fieldTipo =  new dijit.form.ValidationTextBox({
            name: 'text_lancamento[]',
            id: 'tipolan_' + hashid,
            //readOnly: true,
            required: true,
            onChange: function()
            {
                financeiroLancamentoCartao.setChangeTipoLancamento(this);
            },
            style: "width: 230px;",
            labelAttr: 'label',
            labelType: 'html'
        });
        
        dojo.connect(
                fieldTipo,
                "onChange",
                null,
                function()
                {
                    financeiroLancamentoCartao.validaTipoLancamentoTala( fieldTipo );
                }
            );
                
        var fieldHidden = document.createElement('input');
        fieldHidden.type = 'hidden';
        fieldHidden.name = 'fn_tipo_lanc_id[]';
        fieldHidden.id   = 'vltplan_' + hashid;
        
        var fieldButton = new dijit.form.Button (
            {
                id: 'buttonn_' + hashid,
                onClick: function()
                {
                    financeiroLancamentoCartao.changeTipoLancamento(this);
                },
                iconClass:"icon-toolbar-applicationformmagnify"
            }
        );
        var storeProjeto = new dojo.data.ItemFileReadStore({
            url: baseUrl + "/financeiro/lancamento-cartao/projetolist"
          });
          
        var fieldProjeto =  new dijit.form.FilteringSelect({
            name: "projeto_id[]",
            store: storeProjeto,
            id: 'projeto_' + hashid,
            style: "width: 230px;",
	    labelAttr: 'label',
	    labelType: 'hmtl',
            onChange: function( valor )
            {
                var index = parseInt( $(this).attr('id').replace(/^[a-zA-Z_]+/g, '') );
                dijit.byId( 'tipolan_' + index ).attr( 'value','' );
                document.getElementById( 'vltplan_' + index ).value = '';
            }
        });

        
	var fieldValor = new dijit.form.CurrencyTextBox({
                        name: 'fn_lanc_cc_tipo_valor[]',
                        style: "width: 230px;",
                        id: 'valorpr_' + hashid,
                        required: true,
                        currency: 'R$ ',
                        value: 0
                    });

        dojo.connect(
                fieldValor,
                "onChange",
                null,
                function()
                {
                    financeiroLancamentoCartao.somaLancamentos( fieldValor );
                }
            );

	tdFieldProjeto.append( fieldProjeto.domNode );
        tdFieldTipo.append(    fieldTipo.domNode );
        tdFieldTipo.append(    fieldHidden );
        tdFieldButton.append(  fieldButton.domNode );
        tdFieldValor.append(   fieldValor.domNode );

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

	tr.append( tdFieldProjeto ).append( tdFieldTipo ).append( tdFieldButton ).append( tdFieldValor ).append( tdExcl );

	$('#tbl-lancamento-tipo').append( tr );

        storeTipoLanc.fetch(
        {
            onComplete: function()
            {
                dijit.byId( fieldTipo.id ).focus();
            }
        });

        storeProjeto.fetch(
        {
            onComplete: function()
            {
                objGeral.loading( false );
                dijit.byId( fieldProjeto.id ).focus();
            }
        });
    },
    
    validaTipoLancamentoTala: function( obj )
    {
        if ( !objGeral.empty( obj.value  ) ) 
        {
            objGeral.loading( true );
            var xhrPost = dojo.xhrPost({
                form: "formfinanceirolancamentocartao",
                handleAs: "json",
                url: baseUrl + "/financeiro/lancamento-cartao/valida-tipo-lancamento",
                load: function( response ){
                    
                    objGeral.loading( false );
                    if( !response.validacao ){
                        
                        obj.value = '';
                        obj.displayedValue = '';
                        objGeral.msgAlerta( objGeral.translate('Tipo de lançamento duplicado.') );
                    }
                },
                error: function(error){
                    
                    objGeral.msgErro( objGeral.translate('Erro ao executar operação') );
                }
            });
            
        }
    },
    
    somaLancamentos: function( obj )
    {

        objGeral.loading( true );
        var xhrPost = dojo.xhrPost({
            form: "formfinanceirolancamentocartao",
            handleAs: "json",
            url: baseUrl + "/financeiro/lancamento-cartao/soma-lancamento",
            load: function( response ){

                objGeral.loading( false );
                dijit.byId( 'fn_lanc_cartao_valor' ).attr( 'value' , response.total );

            },
            error: function(error){

                objGeral.msgErro( objGeral.translate('Erro ao executar operação') );
            }
        });

    },
    
    removeTipoLancamentoTela: function( lanc, tipo ,img )
    {
	if ( !confirm( objGeral.translate('Deseja realmente remover este item?') ) )
	    return false;

	objGeral.deleteRow( $(img).parent().parent() );
        this.somaLancamentos()
	return true;
    },
    
    reconciliar: function()
    {
	objGeral.createDialog( 
                '/financeiro/lancamento-cartao/reconciliar/', 
                objGeral.translate( 'Reconciliação de Cartão de Crédito' ),
		dojo.hitch( this, 'initGridReconciliacao' )
            );
    },
    
    initGridReconciliacao: function()
    {
	gridHeader = objGeral.translate( 'Descrição' ) + ',' +
		     objGeral.translate( 'Valor' ) + ',' +
		     objGeral.translate( 'Data' );
	
        this.gridLancamentosPendentes = new dhtmlXGridObject('lancamentos-pendentes');
	with ( this.gridLancamentosPendentes ) {
	    
	    setHeader( objGeral.translate( 'Lançamentos pendentes' ) + ',#cspan,#cspan', null, ["text-align:center;"] );
	    attachHeader( gridHeader );
	    setInitWidths( "*,100,100" );
	    setColAlign( "left,left,left" );
	    setMultiLine( false );
	    setColTypes( "ro,ro,ro" );
	    setColSorting( "str,na,date" );
	    enableDragAndDrop( true );
	    setCustomSorting( dojo.hitch( objGeral, 'sortCurrency' ), 1 );
	    attachEvent( 'onDrop', dojo.hitch( this, 'controlDropGrid' ) );
	    attachEvent( 'onBeforeDrag', dojo.hitch( this, 'blockDropGrid' ) );
	    setSkin( objGrid.theme );
	    attachFooter( objGrid.tituloTotal + ",{#stat_total},{#stat_count}");
	    init();
	}
	
        this.gridLancamentosEfetivados = new dhtmlXGridObject('lancamentos-efetivados');
	with ( this.gridLancamentosEfetivados ) {
	    
	    setHeader( objGeral.translate( 'Lançamentos efetivados' ) + ',#cspan,#cspan', null, ["text-align:center;"] );
	    attachHeader( gridHeader );
	    setInitWidths( "*,100,100");
	    setColAlign( "left,left,left" );
	    setMultiLine( false );
	    setColTypes( "ro,ro,ro" );
	    setColSorting( "str,na,date" );
	    enableDragAndDrop( true );
	    setCustomSorting( dojo.hitch( objGeral, 'sortCurrency' ), 1 );
	    attachEvent( 'onDrop', dojo.hitch( this, 'controlDropGrid' ) );
	    setSkin( objGrid.theme );
	    attachFooter( objGrid.tituloTotal + ",{#stat_total},{#stat_count}");
	    init();
	}
	
	this.liberaDropGrid( false );
    },
    
    blockDropGrid: function()
    {
	return this.liberaDragDrop;
    },
    
    controlDropGrid: function()
    {
	var flagRows = objGeral.empty( this.gridLancamentosEfetivados.getRowsNum() );
	
	dijit.byId( 'buttonEfetivarReconciliacaoCartao' ).setDisabled( flagRows );
	dijit.byId( 'fn_cc_fat_total' ).set( 'readOnly', !flagRows );
	
	return true;
    },
    
    buscaLancamentos: function()
    {
	var cartao = dijit.byId( 'fn_cc_id' ).get( 'value' );
	
	if ( objGeral.empty( cartao ) )
	    return false;
	
	var self = this;
	
	var obj = {
	    url: baseUrl + '/financeiro/lancamento-cartao/busca-lancamentos/cartao/' + cartao,
	    handle: 'json',
	    callback: function( response )
	    {
		self.gridLancamentosPendentes.clearAll();
		self.gridLancamentosPendentes.parse( response.lancamentos, 'json' );
		
		if ( objGeral.empty( response.fatura ) )
		    dijit.byId( 'fn_cc_fat_total' ).set( 'readOnly', false ).set( 'value', '' ).focus();
		else {
		 
		    dijit.byId( 'formFinanceiroReconciliacaoCartao' ).getDescendants().forEach(
			function( item )
			{
			    item.set( 'readOnly', false ).focus();
			}
		    );
		    
		    dijit.byId( 'formFinanceiroReconciliacaoCartao' ).setValues( response.fatura );
		    
		    with ( dijit.byId( 'fn_cc_fat_total' ) ) {
			
			focus();
			blur();
			set( 'readOnly', true );
		    }
		    
		    dijit.byId( 'buttonSalvarReconciliacaoCartao' ).focus();
		    
		    self.gridLancamentosEfetivados.clearAll();
		    self.gridLancamentosEfetivados.parse( response.efetivados, 'json' );
		    
		    // Libera movimentacao
		    self.liberaMovimentacao();
		    
		    // Libera botao de efetivacao
		    dijit.byId( 'buttonEfetivarReconciliacaoCartao' ).setDisabled( false );
		}
	    }
	}
	
	objGeral.buscaAjax( obj );
	
	return true;
    },
    
    liberaDropGrid: function ( flag )
    {
	this.liberaDragDrop = Boolean( flag );
    },
    
    liberaMovimentacao: function()
    {
	var valor = dijit.byId( 'fn_cc_fat_total' ).get( 'value' );
	
	this.liberaDropGrid( valor );
	
	dijit.byId( 'fn_cc_fat_ref' ).set( 'readOnly', objGeral.empty( valor ) );
	dijit.byId( 'fn_cc_fat_vencimento' ).set( 'readOnly', objGeral.empty( valor ) );
	
	if ( objGeral.empty( valor ) ) {
	    
	    dijit.byId( 'fn_cc_fat_ref' ).set( 'value', null );
	    dijit.byId( 'fn_cc_fat_vencimento' ).set( 'value', null );
	}
    },
    
    validarReconciliacao: function( form )
    {
	if ( !form.validate() ) {
	    
	    this.msgErro( objGeral.translate( 'Verifique todos os campos marcados antes de continuar.' ) );
	    return false;
	}
	
	if ( objGeral.empty( this.gridLancamentosEfetivados.getRowsNum() ) ) {
	    
	    this.msgErro( objGeral.translate( 'Não existem lançamentos efetivados.' ) );
	    return false;
	}
	
	return true;
    },
    
    salvarReconciliacao: function( formId )
    {
	var objForm = dijit.byId( formId );
	
	if ( !this.validarReconciliacao( objForm ) )
	    return false;
	
	var efetivados = this.gridLancamentosEfetivados.getAllRowIds( ',' );
	
	var obj = {
	    url: baseUrl + '/financeiro/lancamento-cartao/salvar-reconciliacao/',
	    data: {
		'lancamentos[]': efetivados.split( ',' )
	    },
	    handle: 'json',
	    callback: function( response )
	    {
		if ( response.status ) {
		  
		    dojo.byId( 'fn_cc_fat_id' ).value = response.fatura;
		    objGeral.msgSucesso( objGeral.translate( 'Operação realizada com sucesso.' ) )
		    
		} else
		    objGeral.msgErro( objGeral.translate( 'Erro ao salvar fatura.' ) );
	    },
	    form: dojo.byId( formId )
	}
	
	objGeral.buscaAjax( obj );
	
	return true;
    },
    
    clearDataEfetivar: function()
    {
	this.dadosEfetivar = null;
    },
    
    efetivarReconciliacao: function( formId )
    {
	this.clearDataEfetivar();
	
	var objForm = dijit.byId( formId );
	
	if ( !this.validarReconciliacao( objForm ) )
	    return false;
	
	var total = dijit.byId( 'fn_cc_fat_total' ).get( 'value' );
	var totalEfetivado = 0;
	
	// Soma total de lancamentos efetivado
	this.gridLancamentosEfetivados.forEachRow(
	    function( id )
	    {
		totalEfetivado += objGeral.toFloat( this.cells( id, 1 ).getValue() );
	    }
	);
	    
	totalEfetivado = totalEfetivado.toFixed( 2 );
	    
	// Valida total de lancamento com total efetivado
	if ( total != totalEfetivado ) {
	    
	    this.msgAlerta( objGeral.translate( 'Total da fatura não confere com o total efetivado.' ) );
	    return false;
	}
	
	var efetivados = this.gridLancamentosEfetivados.getAllRowIds( ',' );
	
	this.dadosEfetivar = {
	    'lancamentos[]': efetivados.split( ',' ),
	    total: objGeral.toFloat( totalEfetivado ),
	    cartao: dijit.byId( 'fn_cc_id' ).get( 'displayedValue' ),
	    ref: dijit.byId( 'fn_cc_fat_ref' ).get( 'displayedValue' ),
	    fn_cc_fat_id: dojo.byId( 'fn_cc_fat_id' ).value
	};
	
	this.dadosEfetivar = dojo.safeMixin( this.dadosEfetivar, objForm.getValues() );
	
	// Formata data de vencimento
	this.dadosEfetivar.fn_cc_fat_vencimento = this.formateDate( this.dadosEfetivar.fn_cc_fat_vencimento );
	
	var self = this;
	
	var obj = {
	    url: baseUrl + '/financeiro/lancamento-cartao/verifica-permissao-efetivar/',
	    handle: 'json',
	    callback: function( response )
	    {
		if ( !response.permissao ) {
		  
		    objGeral.msgErro( objGeral.translate( 'Usuário sem permissão para efetivar fatura.' ) );
		    self.clearDataEfetivar();
		    
		} else
		    self.criarLancamento( formId );
		    
	    },
	    callbackError: function( response )
	    {
		objGeral.msgErro( objGeral.translate( 'Usuário sem permissão para efetivar fatura.' ) );
		self.clearDataEfetivar();
	    }
	}
	
	objGeral.buscaAjax( obj );
	    
	return true;
    },
    
    criarLancamento: function( formId )
    {
	var self = this;
	var obj = {
	    url: baseUrl + '/financeiro/lancamento-cartao/prepara-fatura-lancamento/',
	    handle: 'json',
	    data: this.dadosEfetivar,
	    callback: function( response )
	    {
		self.tiposLancamentosAgrupados = response;
		
		dojo.byId( 'fn_cc_fat_id' ).value = response.fatura;
	
		objGeral.createDialog( 
		    '/financeiro/lancamento/form/', 
		    objGeral.translate( 'Lançamento' ),
		    dojo.hitch( self, 'preparaLancamentoFatura' )
		);
		    
	    },
	    form: dojo.byId( formId )
	}
	
	objGeral.buscaAjax( obj );
    },
    
    preparaLancamentoFatura: function()
    {	
	dijit.byId( 'lancamento-fn_lancamento_tipo' ).set( 'value', 'D' ).set( 'readOnly', true );
	dijit.byId( 'lancamento-fn_lancamento_status' ).set( 'value', 'A' ).set( 'readOnly', true );
	dijit.byId( 'lancamento-tela' ).set( 'readOnly', true );
	dijit.byId( 'lancamento-fn_lancamento_efetivado' ).set( 'disabled', false ).set( 'checked', true ).set( 'readOnly', true );
	dijit.byId( 'lancamento-fn_lancamento_valor' ).set( 'value', this.dadosEfetivar.fn_cc_fat_total );
	
	var obs = 'Efetivação fatura ' + this.dadosEfetivar.cartao + ' Ref.: ' + this.dadosEfetivar.ref ;
	dijit.byId( 'lancamento-fn_lancamento_obs' ).set( 'value', obs ).set( 'readOnly', true );
	
	divElement = $( '<div />' );
	divElement.addClass( 'element' );
	
	label = $( '<label />' );
	label.attr( 'for', 'fn_lancamento_dtefetivado' )
	     .addClass( 'required' )
	     .html( 'Dt. Efetivação' );
	     
	divElement.append( label );
	
	spanInput = $( '<span />' );
	spanInput.addClass( 'input' );
	
	if ( dijit.byId( 'fn_lancamento_dtefetivado' ) )
	    dijit.byId( 'fn_lancamento_dtefetivado' ).destroy();
	
	dateEfetivacao = new dijit.form.DateTextBox({ name: 'fn_lancamento_dtefetivado', id: 'fn_lancamento_dtefetivado', required: true, value: new Date() });
	spanInput.append( dateEfetivacao.domNode );
	
	divElement.append( spanInput );
	
	dijit.byId( 'valor_cheque' ).destroy();
	$( 'label[for=valor_cheque]' ).closest( 'div.element' ).replaceWith( divElement );
	
	dijit.byId( 'btn_cheque' ).destroy();
	
	$( '.icon-toolbar-add' ).parent().remove();
	
	dijit.byId( 'financeiro_lancamento_salvar' ).onClick = null;
	dojo.connect( dijit.byId( 'financeiro_lancamento_salvar' ), 'onClick', dojo.hitch( this, 'salvaLancamento' ) );
	
	this.criaElementosLancamento( this.tiposLancamentosAgrupados.itens );
    },
    
    
    criaElementosLancamento: function( itens )
    {
	objGeral.loading( true );
	
	dojo.forEach( itens, 
	    function( item )
	    {
		var tr = $( '<tr />' ).appendTo( $( '#tbl-lancamento-tipo' ) );
		
		// Insere para projeto
		var projeto = $( '<input />').attr( {type: 'hidden', name: 'lancamento[projeto_id][]', value: item.projeto_id} );
		tr.append( $( '<td />' ).append( projeto ).append( item.projeto_nome ) );
		
		// Insere para tipo de lancamento
		var tipoLanc = $( '<input />').attr( {type: 'hidden', name: 'lancamento[fn_tipo_lanc_id][]', value: item.fn_tipo_lanc_id} );
		tr.append( $( '<td />' ).append( tipoLanc ).append( item.fn_tipo_lanc_desc ) );
		
		// Insere para valor
		var valor = $( '<input />').attr( {type: 'hidden', name: 'lancamento[fn_lanc_projeto_valor][]', value: item.total } );
		tr.append( $( '<td />' ).append( valor ).append( dojo.currency.format( parseFloat( item.total ), { currency: 'R$', locale: 'pt-br' } ) ) );
		
		// Insere imagem
		var imgNo = $( '<div class="icon-toolbar-delete">' ).css( {cursor: 'no-drop'} );
		tr.append( $( '<td />' ).append( imgNo ) );
	    }
	);
	    
	objGeral.loading( false );
    },
    
    
    salvaLancamento: function()
    {
	var formId = 'formfinanceirolancamento';
	var objForm = dijit.byId( formId );
            
	if ( this.validaForm( objForm, this ) ) {

	    this.loading( true );

	    var self = this;
	    
	    var config = {
		url: baseUrl + '/financeiro/lancamento-cartao/efetivar-reconciliacao/',
		handleAs: 'json',
		content: this.dadosEfetivar,
		handle: function()
		{
		    self.loading( false );
		},
		load: function( response )
		{
		    if ( response.status ) {
			
			self.msgSucesso( objGeral.translate( 'Operação realizada com sucesso.' ) );
			// Fecha Dialog de Lancamento
			self.closeGenericDialog();
			// Fecha Dialog de Reconciliacao
			self.closeGenericDialog();
			
		    } else
			if ( response.msg )
			    self.msgErro( objGeral.translate( response.msg ) );
			else
			    self.msgErro( objGeral.translate( 'Erro ao executar operação' ) );
			
		},
		error: function()
		{
		    self.msgErro( objGeral.translate( 'Erro ao executar operação' ) );
		},
		form: dojo.byId( formId ),
		timeout: self.timeout
	    }

	    // Submete em ajax
	    dojo.xhrPost( config );
	}
    },
            
    isExistId: function( hashId )
    {
        if( dojo.byId( 'projeto_' + hashId) ||
            dojo.byId( 'buttonn_' + hashId) ||
            dojo.byId( 'tipolan_' + hashId) ||
            dojo.byId( 'vltplan_' + hashId) ||
            dojo.byId( 'valorpr_' + hashId)){

            return true; 
        }else{

            return false
        }
    },

    geraHashId: function()
    {
        var hashid = new Date().getTime();

        while(financeiroLancamentoCartao.isExistId(hashid)){

            hashid = new Date().getTime();
        }

        return hashid;
    },
    
    setValuesTipoLancamento: function( parent, children )
    {	
        var strid = parent.id;
        strid = strid.toString()

        if( strid.split("_")[0] != 'CAT' ){
            
            financeiroLancamentoCartao.noChangeTipoLancamento = true;
            dijit.byId( financeiroLancamentoCartao.idTipoLancText ).attr('value', parent.path );
            document.getElementById( financeiroLancamentoCartao.idTipoLancValor ).value = parent.id ;
            
            if( financeiroLancamentoCartao.noDoubleClickTree ){

                objGeral.closeGenericDialog();
                financeiroLancamentoCartao.noDoubleClickTree = false
            }
        }else{

            objGeral.msgAlerta( objGeral.translate('Você selecionou uma categoria, você deve selecionar um tipo de lançamento!') )
        }
        return true;
    },
           
    initTree: function()
     {
         financeiroLancamentoCartao.noDoubleClickTree = true;
         var objProjeto =  dijit.byId( financeiroLancamentoCartao.idProjeto );
         var id = objProjeto.value;

         if ( !objGeral.empty( this.treeView ) ) {
             this.treeView.destroyRecursive( true );
         }

         objGeral.loading( true );

         var store = new dojo.data.ItemFileWriteStore({
             url: baseUrl + '/financeiro/lancamento-cartao/treetipolancamento/id/' + id ,
             hierarchical: true
         });

         store.fetch({
             onComplete:
             function()
             {
                 objGeral.loading( false );
             }
         });

         var treeModel = new modulo.custom.ForestStoreModel({
             store: store,
             query: {
                 type: 'root'
             },
             childrenAttrs: ['children']
         }, 'store' );

         this.treeView = new modulo.custom.TreeMenu({ 
             model: treeModel, 
             showRoot: false, 
             persist: true,
             dragThreshold: 8,
             betweenThreshold: 5,
             dndController: false,
             //dndController: 'dijit.tree.dndSource',
             customIcons: false
         });

         dojo.connect( this.treeView, 'onClick', dojo.hitch( financeiroLancamentoCartao, 'setValuesTipoLancamento' ) );

         dojo.byId( 'tree-tipo-lancamento' ).appendChild( this.treeView.domNode );

         this.treeView.startup();
     },
     setChangeTipoLancamento: function (ojbTipo)
    {
        if( financeiroLancamentoCartao.noChangeTipoLancamento ){

            financeiroLancamentoCartao.noChangeTipoLancamento = false;
            return false;
        }

        var idTipoLanc = ojbTipo.id;               
        financeiroLancamentoCartao.idProjeto  = 'projeto_' + parseInt( idTipoLanc.replace(/^[a-zA-Z_]+/g, '') );
        financeiroLancamentoCartao.idTipoLancText  = 'tipolan_' + parseInt( idTipoLanc.replace(/^[a-zA-Z_]+/g, '') );
        financeiroLancamentoCartao.idTipoLancValor = 'vltplan_' + parseInt( idTipoLanc.replace(/^[a-zA-Z_]+/g, '') );

        var valProjeto = dijit.byId( financeiroLancamentoCartao.idProjeto ).attr('value');

        if(ojbTipo.value == ''){

            return false;
        }
        if(valProjeto == ''){

            objGeral.msgAlerta( objGeral.translate('Selecione projeto') );
            dijit.byId( financeiroLancamentoCartao.idTipoLancText ).attr('value','');
            dojo.byId( financeiroLancamentoCartao.idTipoLancValor ).value = '';
            return false;
        }else{

             objGeral.buscaAjax({
                     url: baseUrl + '/financeiro/lancamentocartao/busca-tipo-lancamento-codigo/',
                     data: {fn_tipo_lanc_cod: ojbTipo.value, projeto_id: valProjeto},
                     handle: 'json',
                     callback: function( response )
                     {
                         financeiroLancamentoCartao.noChangeTipoLancamento = true;
                         if(response == null){

                             objGeral.msgAlerta( objGeral.translate('Código de tipo de lançamento não existe!') );
                             dijit.byId( financeiroLancamentoCartao.idTipoLancText ).attr('value','');
                             dojo.byId( financeiroLancamentoCartao.idTipoLancValor ).value = '';
                         }else{

                             dijit.byId( financeiroLancamentoCartao.idTipoLancText ).attr('value',response.path);
                             dojo.byId( financeiroLancamentoCartao.idTipoLancValor ).value = response.fn_tipo_lanc_id;
                         }
                     }
             });
        }

        return true;
    },
           
    changeTipoLancamento: function (ojbTipo)
    {
        var idTipoLanc = ojbTipo.id;               
        financeiroLancamentoCartao.idProjeto  = 'projeto_' + parseInt( idTipoLanc.replace(/^[a-zA-Z_]+/g, '') );
        financeiroLancamentoCartao.idTipoLancText  = 'tipolan_' + parseInt( idTipoLanc.replace(/^[a-zA-Z_]+/g, '') );
        financeiroLancamentoCartao.idTipoLancValor = 'vltplan_' + parseInt( idTipoLanc.replace(/^[a-zA-Z_]+/g, '') );

        var objProjeto = dojo.byId( financeiroLancamentoCartao.idProjeto );

        if(objProjeto.value == ''){

            objGeral.msgAlerta( objGeral.translate('Selecione projeto') );
            return false;
        }else{

             var tipoLancamentoDialogTree = objGeral.createDialog( 
                                         '/financeiro/lancamento-cartao/lancamentotipolancamento/', 
                                         objGeral.translate( 'Selecione tipo de lançamento' ),
                                         financeiroLancamentoCartao.initTree
                                     );
        }
        return true;
    }
});

var financeiroLancamentoCartao = new modulo.financeiro.lancamentocartao();