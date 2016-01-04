dojo.require( 'modulo.padrao.geral' );
dojo.require( 'modulo.padrao.grid' );

dojo.provide( 'modulo.financeiro.reconciliacao' );

dojo.declare( 'modulo.financeiro.reconciliacao', [modulo.padrao.geral, modulo.padrao.grid],
{
    constructor: function()
    {
        this.isSetDataInicial = true;
    },
    
    gridLancamentosPendentes: null,
    gridLancamentosEfetivados: null,
    liberaDragDrop: false,
    
    initGrid: function()
    {
        objGeral.loading( true );
        
        financeiroReconciliacao.gridHeader = '#,'+
                                    objGeral.translate('Conta')+','+
                                    objGeral.translate('Data Inicial')+','+
                                    objGeral.translate('Saldo Inicial')+','+
                                    objGeral.translate('Data Final')+','+
                                    objGeral.translate('Saldo Final')+','+
                                    objGeral.translate('Data Efetivação')+','+
                                    objGeral.translate('Situação');
	
        document.getElementById('gridFinanceiroReconciliacao').style.height= objGrid.gridHeight +"px";
        financeiroReconciliacao.grid = new dhtmlXGridObject('gridFinanceiroReconciliacao');
        financeiroReconciliacao.grid.setHeader( financeiroReconciliacao.gridHeader );
        financeiroReconciliacao.grid.attachHeader("#rspan,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#select_filter");
        financeiroReconciliacao.grid.setInitWidths( objGrid.idWidth + ",*,100,200,100,200,100,100");
        financeiroReconciliacao.grid.setColAlign("center,left,center,right,center,right,center,left");
        financeiroReconciliacao.grid.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro");
        financeiroReconciliacao.grid.setColSorting("str,str,str,str,str,str,str,str");
        financeiroReconciliacao.grid.setSkin( objGrid.theme );
        financeiroReconciliacao.grid.enablePaging( true, objGrid.pagResults, objGrid.pagIndexes, 'divpagFinanceiroReconciliacaoGrid', true, 'divpagfinanceiroReconciliacao');
        financeiroReconciliacao.grid.attachFooter( objGrid.tituloTotal + ",#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,{#stat_count}");
        financeiroReconciliacao.grid.attachEvent( 'onRowDblClicked', financeiroReconciliacao.edit );
        financeiroReconciliacao.grid.init();
        financeiroReconciliacao.grid.load( baseUrl + '/financeiro/reconciliacao/list', dojo.hitch( objGeral, 'loading', false ) , "json");
    },
    
    novo: function()
    {
        objGeral.createDialog( 
                '/financeiro/reconciliacao/form/',
                objGeral.translate('Reconciliação Bancária'),
		dojo.hitch( this, 'initGridReconciliacao' )
            );
    },
    
    edit: function()
    {
        if ( !financeiroReconciliacao.grid.getSelectedId() ) {
            
            objGeral.msgAlerta( objGeral.translate('Selecione o item para edição.') );
            return false;
        }
            
        objGeral.createDialog( 
                '/financeiro/reconciliacao/edit/id/' + financeiroReconciliacao.grid.getSelectedId(),
                objGeral.translate('Reconciliação Bancária'),
		dojo.hitch( this, 'initGridReconciliacao' )
            );
        return true;
    },
    
    atualizarGrid: function()
    {
        objGeral.atualizarGrids( [financeiroReconciliacao.grid] );
    },

    fecharDialogReconciliacao: function()
    {
        dijit.byId('dialogEfetivar').hide();
        financeiroReconciliacao.atualizarGrid();
    },
    
    initGridReconciliacao: function()
    {
	gridHeader = objGeral.translate( 'Documento' ) + ',' +
		     objGeral.translate( 'Data' ) + ',' +
		     objGeral.translate( 'Valor' );
	
        financeiroReconciliacao.gridLancamentosPendentes = new dhtmlXGridObject('lancamentos-pendentes');
	with ( this.gridLancamentosPendentes ) {
	    
	    setHeader( objGeral.translate( 'Lançamentos pendentes' ) + ',#cspan,#cspan', null, ["text-align:center;"] );
	    attachHeader( gridHeader );
	    setInitWidths( "*,100,100" );
	    setColAlign( "left,left,left" );
	    setMultiLine( false );
	    setColTypes( "ro,ro,ro" );
	    setColSorting( "str,date,float" );
	    enableDragAndDrop( true );
	    setCustomSorting( dojo.hitch( objGeral, 'sortCurrency' ), 2 );
	    attachEvent( 'onDrop', dojo.hitch( this, 'controlDropGrid' ) );
	    attachEvent( 'onBeforeDrag', dojo.hitch( this, 'blockDropGrid' ) );
	    setSkin( objGrid.theme );
	    attachFooter( objGrid.tituloTotal + ",#cspan,{#stat_total}");
	    init();
	}
	
        financeiroReconciliacao.gridLancamentosEfetivados = new dhtmlXGridObject('lancamentos-efetivados');
	with ( this.gridLancamentosEfetivados ) {
	    
	    setHeader( objGeral.translate( 'Lançamentos efetivados' ) + ',#cspan,#cspan', null, ["text-align:center;"] );
	    attachHeader( gridHeader );
	    setInitWidths( "*,100,100");
	    setColAlign( "left,left,left" );
	    setMultiLine( false );
	    setColTypes( "ro,ro,ro" );
	    setColSorting( "str,date,float" );
	    enableDragAndDrop( true );
	    setCustomSorting( dojo.hitch( objGeral, 'sortCurrency' ), 2 );
	    attachEvent( 'onDrop', dojo.hitch( this, 'controlDropGrid' ) );
	    attachEvent( 'onBeforeDrag', dojo.hitch( this, 'blockDropGrid' ) );
	    setSkin( objGrid.theme );
	    attachFooter( objGrid.tituloTotal + ",#cspan,{#stat_total}");
	    init();
	}
	
	financeiroReconciliacao.liberaDropGrid( false );
        financeiroReconciliacao.buscaLancamentos();
    },
    
    blockDropGrid: function()
    {
	return financeiroReconciliacao.liberaDragDrop;
    },
    
    controlDropGrid: function()
    {
	var flagRows = objGeral.empty( this.gridLancamentosEfetivados.getRowsNum() );
	
        dijit.byId( 'buttonSalvarReconciliacaoCartao' ).setDisabled( flagRows );
        
	if( objGeral.empty( dojo.byId( 'fn_recon_id' ).value) ){

            dijit.byId( 'buttonEfetivarReconciliacao' ).setDisabled( true );
        }else{

            dijit.byId( 'buttonEfetivarReconciliacao' ).setDisabled( flagRows );
        }
        this.calculaSaldoFinal();
	return true;
    },

    liberaDropGrid: function ( flag )
    {
	financeiroReconciliacao.liberaDragDrop = Boolean( flag );
    },
    
    buscaLancamentosSemSetarDataInicial: function()
    {
        financeiroReconciliacao.isSetDataInicial = false;
        financeiroReconciliacao.buscaLancamentos();
    },
    
    buscaLancamentos: function()
    {
	var conta   = dijit.byId( 'fn_conta_id' ).get( 'value' );
	var recon   = dojo.byId( 'fn_recon_id' ).value;
        
	var self = this;
	
        self.gridLancamentosPendentes.clearAll(); 
        self.gridLancamentosEfetivados.clearAll(); 

        if( objGeral.empty( recon) ){

            dijit.byId( 'fn_recon_ini_valor' ).attr('value', objGeral.toFloat( 0 ) );
            //dijit.byId( 'fn_recon_ini_data' ).attr('value', null );
        }
        
        if( dojo.byId( 'fn_recon_efetivada' ).value == 1 )
            dijit.byId( 'buttonEfetivarReconciliacao' ).setDisabled( true );

	if ( objGeral.empty( conta ) )
	    return false;

        var dataini = dijit.byId( 'fn_recon_ini_data' ).attr('value');
        if(  dataini  != null ){

            dataini = objGeral.formateDate( dataini );
        }else{

            dataini = '';
        }

        var datafim = dijit.byId( 'fn_recon_fim_data' ).attr('value');
        if(  datafim  != null ){

            datafim = objGeral.formateDate( datafim );
        }else{

            datafim = '';
        }

        if(financeiroReconciliacao.isSetDataInicial){
            
            var buscarDtIni = 0;
        }else{
            
            var buscarDtIni = 1;
        }
	var obj = {
	    url: baseUrl + '/financeiro/reconciliacao/busca-lancamentos/conta/' + conta +'/recon/'+recon+'/datafim/'+datafim+'/dataini/'+dataini+'/setdtini/'+buscarDtIni,
	    handle: 'json',
	    callback: function( response )
	    {
		self.gridLancamentosPendentes.parse( response.lancamentospedentes, 'json' );
		self.gridLancamentosEfetivados.parse( response.lancamentosefetivados, 'json' );

                if( objGeral.empty( recon ) ){
                    
                    dijit.byId( 'fn_recon_ini_data'  ).attr( 'value' ,response.reconciliacao.data_ini );
                    dijit.byId( 'fn_recon_ini_valor' ).attr( 'value' ,response.reconciliacao.valor_ini );

                }else{

                    if( objGeral.empty( dojo.byId( 'fn_recon_efetivada' ).value) ){

                        dijit.byId( 'buttonEfetivarReconciliacao' ).setDisabled( false );
                        if( financeiroReconciliacao.isSetDataInicial )
                            dijit.byId( 'fn_recon_ini_data'  ).attr( 'value' ,response.reconciliacao.data_ini );
                        
                        dijit.byId( 'fn_recon_ini_valor' ).attr( 'value' ,response.reconciliacao.valor_ini );
                    }else{
                        
                        dijit.byId( 'fn_recon_fim_data'  ).attr( 'readOnly' ,true );
                        dijit.byId( 'fn_recon_ini_data'  ).attr( 'readOnly' ,true );
                    }
                }

                dojo.mixin(
                    dijit.byId('fn_recon_fim_data').constraints,
                        {min: dijit.byId( 'fn_recon_ini_data'  ).attr( 'value')  }
                );

                if( objGeral.empty( dojo.byId( 'fn_recon_efetivada' ).value ) ){
                    // Libera Drop
                    financeiroReconciliacao.liberaDropGrid( 1 );
                }
                financeiroReconciliacao.calculaSaldoFinal();
                
                financeiroReconciliacao.isSetDataInicial = true;
	    }
	}
	
	objGeral.buscaAjax( obj );
	
	return true;
    },
    
    calculaSaldoFinal: function(  )
    {
        
	var efetivados = financeiroReconciliacao.gridLancamentosEfetivados.getAllRowIds( ',' );

	var obj = {
	    url: baseUrl + '/financeiro/reconciliacao/calcula-saldo-final/',
	    data: {
		'lancamentos[]': efetivados.split( ',' )
	    },
	    handle: 'json',
	    callback: function( response )
	    {
                
                dijit.byId( 'fn_recon_fim_valor' ).attr('value', response.saldofinal );

	    },
	    form: dojo.byId( 'formfinanceiroreconciliacao' )
	}
	
	objGeral.buscaAjax( obj );
	
	return true;
    },

    efetivarReconciliacao: function()
    {
        dijit.byId( 'dialogEfetivar' ).hide();
        dojo.byId( 'fn_recon_efetivada' ).value = 1;
        this.salvarReconciliacao( 'formfinanceiroreconciliacao'  );
    },
    
    salvarReconciliacao: function( formId )
    {
	var objForm = dijit.byId( formId );
	
	if ( !this.validarReconciliacao( objForm ) )
	    return false;
        
	var efetivados = financeiroReconciliacao.gridLancamentosEfetivados.getAllRowIds( ',' );

	var obj = {
	    url: baseUrl + '/financeiro/reconciliacao/save/',
	    data: {
		'lancamentos[]': efetivados.split( ',' )
	    },
	    handle: 'json',
	    callback: function( response )
	    {
                if( response.status ){

                    dijit.byId( 'buttonEfetivarReconciliacao' ).setDisabled( false );
                    dijit.byId( 'buttonSalvarReconciliacaoCartao' ).setDisabled( true );
                    
                    if( objGeral.empty( dojo.byId( 'fn_recon_efetivada' ).value ) ){
                        
                        dojo.byId( 'fn_recon_id' ).value = response.id ;
                        dijit.byId( 'dialogEfetivar' ).show();
                    }else{

                        objGeral.closeGenericDialog();
                        financeiroReconciliacao.atualizarGrid();
                        objGeral.msgSucesso( objGeral.translate('Operação realizada com sucesso.') );
                    }
                }else{
                    
                    objGeral.msgErro( objGeral.translate('Erro ao executar operação') );
                }
	    },
	    form: dojo.byId( formId )
	}
	
	objGeral.buscaAjax( obj );
	
	return true;
    },
    
    validarReconciliacao: function( form )
    {
	if ( !form.validate() ) {
	    
	    objGeral.msgErro( objGeral.translate( 'Verifique todos os campos marcados antes de continuar.' ) );
	    return false;
	}
	
	if ( objGeral.empty( financeiroReconciliacao.gridLancamentosEfetivados.getRowsNum() ) ) {
	    
	    objGeral.msgErro( objGeral.translate( 'Não existem lançamentos efetivados.' ) );
	    return false;
	}
	
	return true;
    }

});

var financeiroReconciliacao = new modulo.financeiro.reconciliacao();