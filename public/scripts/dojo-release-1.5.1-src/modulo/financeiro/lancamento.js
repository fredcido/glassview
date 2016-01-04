dojo.require( 'modulo.padrao.geral' );
dojo.require( 'modulo.padrao.grid' );

dojo.provide( 'modulo.financeiro.lancamento' );

dojo.declare( 
	'modulo.financeiro.lancamento', [modulo.padrao.geral, modulo.padrao.grid],
	{
        constructor: function()
        {
            this.noChangeTipoLancamento = false;
            this.noDoubleClickTree = null;
        },

        initGrid: function()
        {
            objGeral.loading( true );

            financeiroLancamento.gridHeader = '#,'+
            objGeral.translate('Data')+','+
            objGeral.translate('Conta')+','+
            objGeral.translate('Nota Fiscal')+','+
            objGeral.translate('Fornecedor')+','+
            objGeral.translate('Valor');

            document.getElementById('gridFinanceiroLancamentoSaida').style.height= objGrid.gridHeight +"px";
            financeiroLancamento.gridSaida = new dhtmlXGridObject('gridFinanceiroLancamentoSaida');
            financeiroLancamento.gridSaida.setHeader( financeiroLancamento.gridHeader );
            financeiroLancamento.gridSaida.attachHeader("#rspan,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter");
            financeiroLancamento.gridSaida.setInitWidths( objGrid.idWidth + ",*,300,200,200,200");
            financeiroLancamento.gridSaida.setColAlign("center,left,left,left,left,left");
            financeiroLancamento.gridSaida.setColTypes("ro,ro,ro,ro,ro,ro");
            financeiroLancamento.gridSaida.setColSorting("str,str,str,str,str,str");
            financeiroLancamento.gridSaida.setSkin( objGrid.theme );
            financeiroLancamento.gridSaida.enablePaging( true, objGrid.pagResults, objGrid.pagIndexes, 'divpagFinanceiroLancamentoSaidaGrid', true, 'divpagfinanceiroLancamentoSaida');
            financeiroLancamento.gridSaida.attachFooter( objGeral.translate('Lançamentos') + ",#cspan,#cspan,{#stat_count}," + objGeral.translate('Total R$') + ",{#stat_total}");
            financeiroLancamento.gridSaida.attachEvent( 'onRowDblClicked', financeiroLancamento.edit );
            financeiroLancamento.gridSaida.init();
            financeiroLancamento.gridSaida.load( baseUrl + '/financeiro/lancamento/list-saida/', dojo.hitch( objGeral, 'loading', false ) , "json");

            document.getElementById('gridFinanceiroLancamentoEntrada').style.height= objGrid.gridHeight +"px";
            financeiroLancamento.gridEntrada = new dhtmlXGridObject('gridFinanceiroLancamentoEntrada');
            financeiroLancamento.gridEntrada.setHeader( financeiroLancamento.gridHeader );
            financeiroLancamento.gridEntrada.attachHeader("#rspan,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter");
            financeiroLancamento.gridEntrada.setInitWidths( objGrid.idWidth + ",*,300,200,200,200");
            financeiroLancamento.gridEntrada.setColAlign("center,left,left,left,left,left");
            financeiroLancamento.gridEntrada.setColTypes("ro,ro,ro,ro,ro,ro");
            financeiroLancamento.gridEntrada.setColSorting("str,str,str,str,str,str");
            financeiroLancamento.gridEntrada.setSkin( objGrid.theme );
            financeiroLancamento.gridEntrada.enablePaging( true, objGrid.pagResults, objGrid.pagIndexes, 'divpagFinanceiroLancamentoEntradaGrid', true, 'divpagfinanceiroLancamentoEntrada');
            financeiroLancamento.gridEntrada.attachFooter( objGeral.translate('Lancamentos') + ",#cspan,#cspan,{#stat_count}," + objGeral.translate('Total R$') + ",{#stat_total}");
            financeiroLancamento.gridEntrada.attachEvent( 'onRowDblClicked', financeiroLancamento.edit );
            financeiroLancamento.gridEntrada.init();
            financeiroLancamento.gridEntrada.load( baseUrl + '/financeiro/lancamento/list-entrada/', dojo.hitch( objGeral, 'loading', false ) , "json");

                    dojo.subscribe(
                            'containerContas-selectChild',
                            function ( node )
                            {
                                    financeiroLancamento.idAbaContainerContas = node.id;
                                    switch( node.id )
                                    {
                                        case 'transferencias':
                                            financeiroLancamento.initGridTransferencias();
                                            financeiroLancamento.blockButtons( true, ['button_financeiro_lancamento_atualizar', 'button_financeiro_lancamento_salvaralterar'] );
                                        break;
                                        case 'efetivados':
                                            financeiroLancamento.initGridEfetivados();
                                            financeiroLancamento.blockButtons( true, ['button_financeiro_lancamento_atualizar', 'button_financeiro_lancamento_salvaralterar'] );
                                        break;
                                        default:
                                            financeiroLancamento.blockButtons( false );
                                    }
                            }
                    );

                    financeiroLancamento.idAbaContainerContas = 'contas-pagar';
        },

	    blockButtons: function ( flag, release )
	    {
		$( '#tabfinanceiro-lancamento .mainTela .toolbar > span' ).each(
		    function( index, element )
		    {
			var widget = $( element ).attr( 'widgetid' );
			
			if ( objGeral.empty( release ) || $.inArray( widget, release ) < 0 )
			    dijit.byNode( element ).setDisabled( flag );
		    }
		);
	    },
	    
	    initGridTransferencias: function()
	    {
		if ( !objGeral.empty( financeiroLancamento.gridTransferencia ) )
		    return;
		
		objGeral.loading( true );
		
		gridHeader = '#,' + objGeral.translate( 'Data' ) + ',' + objGeral.translate( 'Data efetivação' ) + ',' + 
			    objGeral.translate( 'Conta Origem' ) + ',' + objGeral.translate( 'Conta Destino' )+ ',' + objGeral.translate('Valor');
		
                document.getElementById('gridFinanceiroTransferencia').style.height= objGrid.gridHeight +"px";
		financeiroLancamento.gridTransferencia = new dhtmlXGridObject( 'gridFinanceiroTransferencia' );
	        financeiroLancamento.gridTransferencia.setHeader( gridHeader );
	        financeiroLancamento.gridTransferencia.attachHeader( "#rspan,#text_filter,#text_filter,#select_filter,#select_filter,#text_filter");
	        financeiroLancamento.gridTransferencia.setInitWidths( objGrid.idWidth + ",200,200,*,300,200");
	        financeiroLancamento.gridTransferencia.setColAlign("center,left,left,left,left,right");
	        financeiroLancamento.gridTransferencia.setColTypes("ro,ro,ro,ro,ro,ro");
	        financeiroLancamento.gridTransferencia.setColSorting("str,str,str,str,str,str");
	        financeiroLancamento.gridTransferencia.setSkin( objGrid.theme );
	        financeiroLancamento.gridTransferencia.enablePaging( true, objGrid.pagResults, objGrid.pagIndexes, 'divpagFinanceiroTransferenciaGrid', true, 'gridFinanceiroTransferencia');
	        financeiroLancamento.gridTransferencia.attachFooter( objGeral.translate('Transferências') + ",#cspan,#cspan,{#stat_count}," + objGeral.translate('Total R$') + ",{#stat_total}");
	        financeiroLancamento.gridTransferencia.attachEvent( 'onRowDblClicked', financeiroLancamento.editTransferencia );
                financeiroLancamento.gridTransferencia.init();
	        financeiroLancamento.gridTransferencia.load( baseUrl + '/financeiro/lancamento/list-transferencia/', dojo.hitch( objGeral, 'loading', false ) , "json" );
	    },
	    
	    initGridEfetivados: function()
	    {
		if ( !objGeral.empty( financeiroLancamento.gridEfetivados ) )
		    return;
		
		objGeral.loading( true );
		
		gridHeader = '#,' + objGeral.translate('Data') + ',' + objGeral.translate('Data Efetivação') + ',' + 
			      objGeral.translate('Tipo') + ',' + objGeral.translate('Conta') + ',' + objGeral.translate('Nota Fiscal') + ',' + 
			     objGeral.translate('Fornecedor') + ',' + objGeral.translate('Valor');
		
                document.getElementById('gridFinanceiroEfetivados').style.height= objGrid.gridHeight +"px";
	        financeiroLancamento.gridEfetivados = new dhtmlXGridObject('gridFinanceiroEfetivados');
	        financeiroLancamento.gridEfetivados.setHeader( gridHeader );
	        financeiroLancamento.gridEfetivados.attachHeader("#rspan,#text_filter,#text_filter,#select_filter,#select_filter,#text_filter,#text_filter,#text_filter");
	        financeiroLancamento.gridEfetivados.setInitWidths( objGrid.idWidth + ",*,120,150,200,200,250,200");
	        financeiroLancamento.gridEfetivados.setColAlign("center,left,left,left,left,left,left,right");
	        financeiroLancamento.gridEfetivados.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro");
	        financeiroLancamento.gridEfetivados.setColSorting("str,str,str,str,str,str,str,str");
	        financeiroLancamento.gridEfetivados.setSkin( objGrid.theme );
	        financeiroLancamento.gridEfetivados.enablePaging( true, objGrid.pagResults, objGrid.pagIndexes, 'divpagFinanceiroEfetivadosGrid', true, 'gridFinanceiroEfetivados');
	        financeiroLancamento.gridEfetivados.attachFooter( objGeral.translate('Lançamentos') + ",#cspan,#cspan,#cspan,#cspan,{#stat_count}," + objGeral.translate('Total R$') + ",{#stat_total}");
		financeiroLancamento.gridEfetivados.attachEvent( 'onRowDblClicked', financeiroLancamento.detalhaEfetivados );
	        financeiroLancamento.gridEfetivados.init();
	        financeiroLancamento.gridEfetivados.load( baseUrl + '/financeiro/lancamento/list-efetivados/', dojo.hitch( objGeral, 'loading', false ) , "json");
	    },
	    
	    detalhaEfetivados: function()
	    {
		var id = financeiroLancamento.gridEfetivados.getSelectedId();
		objGeral.createDialog( 
			'/financeiro/lancamento/edit/id/' + id + '/efetivado/1', 
			objGeral.translate( 'Lançamento Efetivado' )
		);
	    },
	    
	    editTransferencia: function()
	    {
		var id = financeiroLancamento.gridTransferencia.getSelectedId();
		if ( objGeral.empty( id ) ) {
		    
		    objGeral.msgAlerta( objGeral.translate('Selecione o item para edição.') );
		    return false;
		}
		
		objGeral.createDialog( 
		    '/financeiro/lancamento/edit-transferencia/id/' + id, 
		    objGeral.translate('Transferência')
		);
	    },
	    
	    editEfetivados: function()
	    {
		var id = financeiroLancamento.gridEfetivados.getSelectedId();
		if ( objGeral.empty( id ) ) {
		    
		    objGeral.msgAlerta( objGeral.translate('Selecione o item para edição.') );
		    return false;
		}
		
		objGeral.createDialog( 
		    '/financeiro/lancamento/pagamento/edit/1/id/' + id, 
		    objGeral.translate('Pagamento')
		);
	    },
	    
	    /**
	     * 
	     */
		novo: function()
		{
			objGeral.createDialog( 
				'/financeiro/lancamento/form/', 
				objGeral.translate('Lançamento'),
				function()
				{
					//Seleciona o tipo de lancamento de acordo com a aba selecionada
					switch ( financeiroLancamento.idAbaContainerContas ) {
					
						case 'contas-pagar':
							dijit.byId('lancamento-fn_lancamento_tipo').set( 'value', 'D' );
							break;
							
						case 'contas-receber':
							dijit.byId('lancamento-fn_lancamento_tipo').set( 'value', 'C' );
							break;
					
					} 
				}
			);
	    },
	    
	    /**
	     * 
	     */
	    edit: function()
	    {
	    	switch ( this.idAbaContainerContas ) {
	    		
	    		case 'contas-pagar':
	    			if ( !this.gridSaida.getSelectedId() ) {
						objGeral.msgAlerta( objGeral.translate('Selecione o item para edição.') );
						return false;
					}
				    
					this.valorSelecionado = this.gridSaida.getSelectedId();
	    			break;
	    			
	    		case 'contas-receber':
	    			if ( !this.gridEntrada.getSelectedId() ) {
						objGeral.msgAlerta( objGeral.translate('Selecione o item para edição.') );
						return false;
					}
				
					this.valorSelecionado = this.gridEntrada.getSelectedId();
	    			break;
				
	    		case 'transferencias':
	    			 this.editTransferencia();
				 return false;
	    			break;
				
	    		case 'efetivados':
	    			 this.editEfetivados();
				 return false;
	    			break;
	    		
	    	}
	            
			objGeral.createDialog( 
				'/financeiro/lancamento/edit/id/' + this.valorSelecionado, 
				objGeral.translate('Lançamento')
			);
	        
			return true;
	    },
	    
	    /**
	     * Remove tipo de lancamento por projeto
	     * 
	     * @param {Number} key 
	     */
	    removeTipoLancamento: function( key, elem )
	    {
			if ( !confirm( objGeral.translate('Deseja realmente remover este item?') ) )
    			return false;

			var projeto_id       = dijit.byId('projeto_' + key).get('value');
			var fn_tipo_lanc_id  = document.getElementById( 'vltplan_' + key).value;
			var fn_lancamento_id = dojo.byId('lancamento-fn_lancamento_id').value;

			var obj = {
				url: baseUrl + '/financeiro/lancamento/remove-lancamento-projeto/',
				data: {
					projeto_id: projeto_id,
					fn_tipo_lanc_id: fn_tipo_lanc_id,
					fn_lancamento_id: fn_lancamento_id
				},
				handle: 'json',
    			callback: function( response )
				{
					if ( response.status ) {

						objGeral.deleteRow( $('#trid_' + key) );
						objGeral.msgSucesso( response.description[0].message );
						
					} else {
						 
						objGeral.msgErro( response.description[0].message );
						
					}
				}
			};

			objGeral.buscaAjax( obj );

			return true; 
	    },
	    
	    /**
	     * Adiciona opcoes para definir o tipo de lancamento por projeto
	     */
	    addTipoLancamento: function()
	    {
	    	objGeral.loading( true );
	    	
                var hashid = financeiroLancamento.geraHashId();
                
	    	var elemTR = $('<tr>');
	
	    	var storeProjeto = new dojo.data.ItemFileReadStore({
	    		url:  baseUrl + '/financeiro/lancamento/projeto'
    		});
	    	
	    	var i;
	    	
	    	for ( i = 0; i < 5; i++ ) 
	    		elemTR.append( $('<td>') );
		    
	    	for ( i = 0; i < 5; i++ ) {
	    		
	    		switch ( i ) {
	    			
	    			case 0:
	    			
	    				var child = new dijit.form.FilteringSelect({
				            name: 'lancamento[projeto_id][]',
				            id: 'projeto_' + hashid,
				            required: true,
				            store: storeProjeto,
					    style: 'width: 200px',
				            onChange: function( valor )
					    {
						var index = parseInt( $(this).attr('id').replace(/^[a-zA-Z_]+/g, '') );
                                                dijit.byId( 'tipolan_' + index ).attr( 'value','' );
                                                document.getElementById( 'vltplan_' + index ).value = '';
				            }
				        });
				        
	    				break;
	    				
    				case 1:
    				
    					var child = new dijit.form.ValidationTextBox(
					    {
						name: 'lancamento[text_lancamento][]',
						id: 'tipolan_' + hashid,
                                                //readOnly: true,
                                                required: true,
                                                onChange: function()
    						{
                                                    financeiroLancamento.setChangeTipoLancamento(this);
    						},
						labelAttr: 'label',
						labelType: 'html',
						style: 'width: 200px'
					    }
					);
                                            
                                    var childHidden = document.createElement('input');
                                    childHidden.type = 'hidden';
                                    childHidden.name = 'lancamento[fn_tipo_lanc_id][]';
                                    childHidden.id   = 'vltplan_' + hashid;
                                    
                                break;
                                case 2:
    					var child = new dijit.form.Button (
					    {
						id: 'buttonn_' + hashid,
                                                onClick: function()
    						{
                                                    financeiroLancamento.changeTipoLancamento(this);
    						},
                                                iconClass:"icon-toolbar-applicationformmagnify"
					    }
					);
    					
    				 break;	
    				case 3:
    					
    					var child = new dijit.form.CurrencyTextBox({
    						name: 'lancamento[fn_lanc_projeto_valor][]',
                                                id: 'valorpr_' + hashid,
    						required: true,
    						constraints: {min: 0},
    						currency: 'R$ ',
    						value: 0,
                                                style: 'width: 200px',
    						onKeyUp: function()
    						{
    							financeiroLancamento.valorTotal();
    						}
    					});
    					
                                break;

                                case 4:

                                        var child = $('<div class="icon-toolbar-cancel">').click(
                                                function()
                                                {
                                                        if ( !confirm(objGeral.translate('Deseja realmente remover este item?')) )
                                                                return false;

                                                        objGeral.deleteRow( $(this).parent().parent() );

                                                        //Atualiza valor total
                                                        financeiroLancamento.valorTotal();

                                                        return true;
                                                }
                                        ).attr( 
                                                'title', objGeral.translate('Remover Lançamento') 
                                        ).css( 
                                                {cursor: 'pointer'} 
                                        );

                                        break;
	    			
	    		}
	    		
	    		if ( child ) {
			    if ( i != 4 ) {
				
				elemTR.find('td').eq(i).append( child.domNode );
                                if ( i == 1 ) {

                                    elemTR.find('td').eq(i).append( childHidden );
                                }
			    } else{
                                
                                elemTR.find('td').eq(i).append( child );
                            }
                            
			}
				
	    	}
	    	
	    	$('#tbl-lancamento-tipo').append( elemTR );
	    	
	    	storeProjeto.fetch({
	            onComplete: function()
	            {
	                objGeral.loading( false );
	            }
	        });
	    },
	    
	    /**
	     * 
	     */
	    filteringTipoLancamento: function ( elem )
	    {
	    	var index = $(elem).attr('id').replace(/^[a-zA-Z_]+/g, '');
	    	var valor = dijit.byId( $(elem).attr('id') ).get( 'value' );
	    	
            if ( !objGeral.empty( valor ) ) {
            	
                objGeral.changeFilteringSelect(
                    'fn_tipo_lanc_id_' + index,
                    baseUrl + '/financeiro/lancamento/tipo-lancamento/id/',
                    valor
                );
                
            } else {
            	
                dijit.byId( 'fn_tipo_lanc_id_' + index ).set( 'value', '' );
                dijit.byId( 'fn_tipo_lanc_id_' + index ).set( 'disabled', true );
                
            }
	    },
	    
	    /**
	     * Calcula valor total do lancamento
	     */
	    valorTotal: function ()
	    {
	    	var valorTotal = 0;
                
                $('input[type="text"]').each(function(i) {
                    
                    var strId = $(this).attr('id');
                    
                    if( strId != undefined){
                        
                        var strId2 = strId.replace(/^valorpr_/, '');
                        if(strId != strId2){
                            
                            var valor 	= dijit.byId( strId ).get( 'value' );
                            valorTotal += !isNaN(valor) ? valor : 0;
                        }
                    }
                })
                if(dijit.byId( 'lancamento-fn_lancamento_valor' )){
                    
                    dijit.byId( 'lancamento-fn_lancamento_valor' ).set( 'value', valorTotal );
                }else{
                    
                    dijit.byId( 'lancamento-fn_forma_pgto_valor' ).set( 'value', valorTotal );
                }
	    },
	    
	    /**
	     * Atualiza grid principal
	     */
		atualizarGrid: function()
		{
			switch ( this.idAbaContainerContas ) {
	    		
	    		case 'contas-pagar':
	    			objGeral.atualizarGrids( [financeiroLancamento.gridSaida] );
	    			break;
	    			
	    		case 'contas-receber':
	    			objGeral.atualizarGrids( [financeiroLancamento.gridEntrada] );
	    			break;
	    		case 'transferencias':
	    			objGeral.atualizarGrids( [financeiroLancamento.gridTransferencia] );
	    			break;
	    		case 'efetivados':
	    			objGeral.atualizarGrids( [financeiroLancamento.gridEfetivados] );
	    			break;
	    	}
		},
		
		/**
		 * 
 		 * @param {Object} response
		 */
		afterSubmit: function( response )
		{
			if ( !objGeral.empty(response.data) ) {
			
				switch ( response.data.tela ) {
					
					//Ativo
					case 'A':
						almoxarifadoAtivo.novo();
						break;
					
					//Estoque
					case 'E':
						almoxarifadoEstoque.novo();
						break;
					
				}
				
				financeiroLancamento.atualizarGrid();
				
			} else {
			
				if ( !objGeral.empty(response.refresh) )
					financeiroLancamento.atualizarGrid();
					
			}
		},
		
		/**
		 * 
		 */
		openCheque: function ()
		{
            financeiroCheque.gridBuscaCheque();
            dijit.byId( 'dialogBuscaCheque' ).show();
            $('#divpagBuscaFinanceiroChequeGrid').children().remove();

		},
		
		/**
		 * @param string value
		 */
		stateElementEfetivar: function ( value )
		{
			return false;
			
			if ( 'D' == value ) {
				dijit.byId('lancamento-fn_lancamento_efetivado').set( 'checked', false );
				dijit.byId('lancamento-fn_lancamento_efetivado').set( 'disabled', true );
			} else {
				dijit.byId('lancamento-fn_lancamento_efetivado').set( 'disabled', false );
			}
		},
		
		/**
		 * Abre tela para realizar transferencia 
		 */
		formTransferencia: function ()
		{
			objGeral.createDialog( 
				'/financeiro/lancamento/transferencia/', 
				objGeral.translate('Transferência') 
			);
	    },
	    
	    /**
	     * 
 		 * @param {Object} value
	     */
	    buscaContaDestino: function ( value )
	    {
	    	if ( !objGeral.empty(value) ) {
	    	
	    		var url 	= baseUrl + '/financeiro/lancamento/conta-destino/id/' + value;
	    		var store 	= new dojo.data.ItemFileReadStore( {url: url} );
	    		
	    		store.fetch({
	    			onComplete: function()
	    			{
	    				dijit.byId('conta_destino').set( 'disabled', false );
	    			}	
	    		});
	    		
	    		dijit.byId('conta_destino').store = store;	    	
		    		    	
	    	} else {
	    		
	    		dijit.byId('conta_destino').set( 'value', '' );
	    		dijit.byId('conta_destino').set( 'disabled', true );
	    		
	    		dijit.byId('saldo_conta_origem').set( 'value', '' );
	    		dijit.byId('saldo_conta_destino').set( 'value', '' );
	    		
	    	}
	    },
	    
	    /**
	     * 
 		 * @param {Object} filtering
	     */
	    buscaSaldo: function ( filtering )
	    {
	    	var id = dijit.byId(filtering).get('value');
	    	
	    	if ( !objGeral.empty(id) ) {
	    	
		    	objGeral.buscaAjax({
		    		url: baseUrl + '/financeiro/lancamento/busca-saldo/',
		    		data: {fn_conta_id: id},
		    		handle: 'json',
		    		callback: function( response )
		    		{
		    			dijit.byId('saldo_' + filtering).set( 'value', parseFloat(response.saldo) );
		    		}
		    	});
	    	
	    	} else {
	    		
	    		dijit.byId('conta_destino').set( 'value', '' );
	    		dijit.byId('conta_destino').set( 'disabled', true );
	    		
	    		dijit.byId('saldo_conta_origem').set( 'value', '' );
	    		dijit.byId('saldo_conta_destino').set( 'value', '' );
	    		
	    	}
	    },
	    
	    /**
		 * Abre tela para realizar pagamento 
		 */
		formPagamento: function ()
		{
			var id;
			var tipo_lancamento;
			
			switch ( financeiroLancamento.idAbaContainerContas ) {
			
				case 'contas-receber':
					id = this.gridEntrada.getSelectedId();
					tipo_lancamento = 'C';
					break;
					
				case 'contas-pagar':
					id = this.gridSaida.getSelectedId();
					tipo_lancamento = 'D';
					break;
			
			}
			
			if ( objGeral.empty(id) ) {
				objGeral.msgAlerta( objGeral.translate('Selecione uma conta a pagar.') );
				return false;
			}
			
			objGeral.createDialog( 
				'/financeiro/lancamento/pagamento/id/' + id, 
				objGeral.translate('Efetivar'),
				function ()
				{
					dojo.byId('fn_tipo_lancamento').value = tipo_lancamento; 
				}
			);
	   },
           
           initTree: function()
            {
                financeiroLancamento.noDoubleClickTree = true;
                var objProjeto =  dijit.byId( financeiroLancamento.idProjeto );
                var id = objProjeto.value;

                if ( !objGeral.empty( this.treeView ) ) {
                    this.treeView.destroyRecursive( true );
                }

                objGeral.loading( true );

                var store = new dojo.data.ItemFileWriteStore({
                    url: baseUrl + '/financeiro/lancamento/treetipolancamento/id/' + id ,
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

                dojo.connect( this.treeView, 'onClick', dojo.hitch( financeiroLancamento, 'setValuesTipoLancamento' ) );

                dojo.byId( 'tree-tipo-lancamento' ).appendChild( this.treeView.domNode );

                this.treeView.startup();
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
                
                while(financeiroLancamento.isExistId(hashid)){
                    
                    hashid = new Date().getTime();
                }
                
                return hashid;
            },
           setChangeTipoLancamento: function (ojbTipo)
           {
               if( financeiroLancamento.noChangeTipoLancamento ){
                   
                   financeiroLancamento.noChangeTipoLancamento = false;
                   return false;
               }
               
               var idTipoLanc = ojbTipo.id;               
               financeiroLancamento.idProjeto  = 'projeto_' + parseInt( idTipoLanc.replace(/^[a-zA-Z_]+/g, '') );
               financeiroLancamento.idTipoLancText  = 'tipolan_' + parseInt( idTipoLanc.replace(/^[a-zA-Z_]+/g, '') );
               financeiroLancamento.idTipoLancValor = 'vltplan_' + parseInt( idTipoLanc.replace(/^[a-zA-Z_]+/g, '') );

               var valProjeto = dijit.byId( financeiroLancamento.idProjeto ).attr('value');
                
               if(ojbTipo.value == ''){
                   
                   return false;
               }
               if(valProjeto == ''){
                   
                   objGeral.msgAlerta( objGeral.translate('Selecione projeto') );
                   dijit.byId( financeiroLancamento.idTipoLancText ).attr('value','');
                   dojo.byId( financeiroLancamento.idTipoLancValor ).value = '';
                   return false;
               }else{
	    	
                    objGeral.buscaAjax({
                            url: baseUrl + '/financeiro/lancamento/busca-tipo-lancamento-codigo/',
                            data: {fn_tipo_lanc_cod: ojbTipo.value, projeto_id: valProjeto},
                            handle: 'json',
                            callback: function( response )
                            {
                                financeiroLancamento.noChangeTipoLancamento = true;
                                if(response == null){
                                    
                                    objGeral.msgAlerta( objGeral.translate('Código de tipo de lançamento não existe!') );
                                    dijit.byId( financeiroLancamento.idTipoLancText ).attr('value','');
                                    dojo.byId( financeiroLancamento.idTipoLancValor ).value = '';
                                }else{
                                    
                                    dijit.byId( financeiroLancamento.idTipoLancText ).attr('value',response.path);
                                    dojo.byId( financeiroLancamento.idTipoLancValor ).value = response.fn_tipo_lanc_id;
                                }
                            }
                    });
               }
               
               return true;
           },
           changeTipoLancamento: function (ojbTipo)
           {
               var idTipoLanc = ojbTipo.id;               
               financeiroLancamento.idProjeto  = 'projeto_' + parseInt( idTipoLanc.replace(/^[a-zA-Z_]+/g, '') );
               financeiroLancamento.idTipoLancText  = 'tipolan_' + parseInt( idTipoLanc.replace(/^[a-zA-Z_]+/g, '') );
               financeiroLancamento.idTipoLancValor = 'vltplan_' + parseInt( idTipoLanc.replace(/^[a-zA-Z_]+/g, '') );

               var objProjeto = dojo.byId( financeiroLancamento.idProjeto );
                
               if(objProjeto.value == ''){
                   
                   objGeral.msgAlerta( objGeral.translate('Selecione projeto') );
                   return false;
               }else{
                   
                    var tipoLancamentoDialogTree = objGeral.createDialog( 
                                                '/financeiro/lancamento/lancamentotipolancamento/', 
                                                objGeral.translate( 'Selecione tipo de lançamento' ),
                                                financeiroLancamento.initTree
                                            );
               }
               
               return true;
           },
    
            setValuesTipoLancamento: function( parent, children )
            {	
                var strid = parent.id;
                strid = strid.toString()
                
                if( strid.split("_")[0] != 'CAT' ){
               
                    financeiroLancamento.noChangeTipoLancamento = true;
                    dijit.byId( financeiroLancamento.idTipoLancText ).attr('value', parent.path );
                    document.getElementById( financeiroLancamento.idTipoLancValor ).value = parent.id ;

                    if( financeiroLancamento.noDoubleClickTree ){

                        objGeral.closeGenericDialog();
                        financeiroLancamento.noDoubleClickTree = false
                    }
                }else{
                    
                    objGeral.msgAlerta( objGeral.translate('Você selecionou uma categoria, você deve selecionar um tipo de lançamento!') )
                }
                return true;
            },
            
            deletaLancamento: function( identify )
            {	
                
                if (confirm("Você está preste a excluir definitivamente um lançamento!\nDeseja continuar?")){

                    identify = document.getElementById( 'lancamento-fn_lancamento_id' ).value;
                    objGeral.buscaAjax({
                            url: baseUrl + '/financeiro/lancamento/delet/',
                            data: {identify: identify},
                            handle: 'json',
                            callback: function( response )
                            {
                                if(response.status){

                                    objGeral.closeGenericDialog();
                                    financeiroLancamento.atualizarGrid();
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
	}	
);

var financeiroLancamento = new modulo.financeiro.lancamento();