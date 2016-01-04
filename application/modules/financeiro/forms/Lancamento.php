<?php

/**
 * 
 * @version $Id $
 */
class Financeiro_Form_Lancamento extends App_Forms_Default
{
	/**
	 * 
	 * @access public
	 * @return void
	 */
	public function init()
	{
		$this->setName('form-financeiro-lancamento');
		
		$belongs = 'lancamento';
					
		$elements = array();
		
		$elements[] = $this->createElement( 'hidden', 'fn_lancamento_id' )
			->setBelongsTo( $belongs );
			
		//Conta
		$dbConta = App_Model_DbTable_Factory::get( 'Conta' );
		$rowsConta = $dbConta->fetchAll( array('fn_conta_status' => 1), 'fn_conta_descricao' );
		
		$optConta = array( null => '' );
		if ( $rowsConta->count() ) 
			foreach ( $rowsConta as $rowConta )
				$optConta[$rowConta->fn_conta_id] = $rowConta->fn_conta_descricao;
		
		$elements[] = $this->createElement( 'FilteringSelect', 'fn_conta_id' )
			->setLabel( 'Conta' )
			->setBelongsTo( $belongs )
			->addMultiOptions( $optConta )
			->setAttrib( 'class', 'input-form' )
			->setDijitParam( 'placeHolder', 'Selecione a Conta' )
			->setRequired( true );
						
		//Fornecedor
		$dbTerceiro = App_Model_DbTable_Factory::get( 'Terceiro' );
		
		$rowsTerceiro = $dbTerceiro->fetchAll( null, 'terceiro_nome' );
		
		$optTerceiro = array( null => '' );
		
		if ( $rowsTerceiro->count() )
			foreach ( $rowsTerceiro as $rowTerceiro )
				$optTerceiro[$rowTerceiro->terceiro_id] = $rowTerceiro->terceiro_nome;
			
		$elements[] = $this->createElement( 'FilteringSelect', 'terceiro_id' )
			->setLabel( 'Fornecedor' )
			->setBelongsTo( $belongs )
			->addMultiOptions( $optTerceiro )
			->setAttrib( 'class', 'input-form' )
			->setDijitParam( 'placeHolder', 'Selecione um Fornecedor' );

		//Transacao			
		$elements[] = $this->createElement( 'FilteringSelect', 'fn_lancamento_tipo' )
			->setLabel( 'Transação' )
			->setBelongsTo( $belongs )
			->setAttrib( 'class', 'input-form' )
			->setAttrib( 'onChange', 'financeiroLancamento.stateElementEfetivar( this.value )')
			->addMultiOptions(
				array(
					'C' => 'Crédito',
					'D' => 'Débito'
				) 
			)
			->setRequired( true );
			
		//Status
		$elements[] = $this->createElement( 'FilteringSelect', 'fn_lancamento_status' )
			->setLabel( 'Status' )
			->setBelongsTo( $belongs )
			->setAttrib( 'class', 'input-form' )
			->addMultiOptions(
				array(
					'A' => 'Ativo',
					'I' => 'Inativo',
					'E' => 'Estorno'
				)
			);
			
		$elements[] = $this->createElement( 'CheckBox', 'fn_lancamento_efetivado' )
			->setLabel( 'Efetivar' )
			->setBelongsTo( $belongs );

		//Cheque		
		$elements[] = $this->createElement( 'hidden', 'fn_cheque_id' )
			->setBelongsTo( $belongs )
			->setIsArray( true );
			
		$elements[] = $this->createElement( 'ValidationTextBox', 'valor_cheque' )
			->setLabel( 'Cheque' )
			->setAttrib( 'style', 'width:169px;' )
			->setAttrib( 'readOnly', 'true' );
		 
		$elements[] = $this->createElement( 'Button', 'btn_cheque' )
			->setLabel( '' )
			->setAttrib( 'onClick','financeiroLancamento.openCheque();' )
			->setAttrib( 'style', 'float:left; margin-top:-2px;' )
			->setDijitParam( 'iconClass','icon-toolbar-applicationformmagnify' )
			->setDecorators( array('DijitElement') )
			->setDijitParam( 'showLabel', 'false' );
			
		//Valor Total
		$elements[] = $this->createElement( 'CurrencyTextBox', 'fn_lancamento_valor' )
			->setLabel( 'Valor' )
			->setBelongsTo( $belongs )
			->setAttrib( 'class', 'input-form' )
			->setAttrib( 'readOnly', true )
			->setDijitParam( 'currency', 'R$ ' )
			->addFilter( 'StringTrim' )
			->setValue( 0 );
			
		//Documento Fiscal
		$elements[] = $this->createElement('hidden', 'fn_doc_fiscal_id');
		
		$elements[] = $this->createElement( 'ValidationTextBox', 'fn_doc_fiscal_numero' )
			->setLabel( 'Documento Fiscal' )
			->setAttrib( 'style', 'width:169px;' )
			->setAttrib( 'readOnly', 'true' )
			->setRequired( true );
		 
		$elements[] = $this->createElement( 'Button', 'btn_doc_fiscal' )
			->setLabel( '' )
			->setAttrib( 'onClick','financeiroDuplicata.buscaDocFiscal();' )
			->setAttrib( 'style', 'float:left; margin-top:-2px;' )
			->setDijitParam( 'iconClass','icon-toolbar-applicationformmagnify' )
			->setDecorators( array('DijitElement') )
			->setDijitParam( 'showLabel', 'false' );
			
		//Tipo Lancamento
		$elements[] = $this->createElement( 'FilteringSelect', 'tela' )
			->setLabel( 'Tipo Lançamento' )
			->setBelongsTo( $belongs )
			->setAttrib( 'class', 'input-form' )
			->addMultiOptions(
				array(
					'' 	=> null,
					'A' => 'Ativo',
					'E' => 'Estoque'
				) 
			);
			
		//Observacao
		$elements[] = $this->createElement( 'SimpleTextarea', 'fn_lancamento_obs' )
		   ->setLabel( 'Observação' )
		   ->setBelongsTo( $belongs )
		   ->setAttrib( 'maxlength', 250 )
		   ->setAttrib( 'cols', 26 )
		   ->setDijitParam( 'progressObserver', true )
		   ->addFilters( array( 'StringTrim', 'StripTags' ) );
			   
		/* Elementos da Grid */
		
		//Projeto
		$dbProjeto = App_Model_DbTable_Factory::get( 'Projeto' );
		
		$rowsProjeto = $dbProjeto->fetchAll( array('projeto_status = ?' => 'I'), 'projeto_nome' );
		
		$optProjeto = array( null => '' );
		
		foreach ( $rowsProjeto as $rowProjeto )
			$optProjeto[$rowProjeto->projeto_id] = $rowProjeto->projeto_nome;
		
		$elements[] = $this->createElement( 'FilteringSelect', 'projeto_id' )
			->setBelongsTo( $belongs )
			->addMultiOptions( $optProjeto )
			->setAttrib( 'class', 'input-form' )
			->setAttrib( 'readOnly', true )
			->setDijitParam( 'placeHolder', 'Selecione o Projeto' )
			->setRegisterInArrayValidator( false )
			->setIsArray( true );
			
		//Tipo Lancamento Texto
		$elements[] = $this->createElement( 'ValidationTextBox', 'text_lancamento' )
			->setAttrib( 'class', 'input-form' )
			->setAttrib( 'readOnly', true )
			->setRequired( false )
			->setIsArray( true );
			
                //Tipo Lancamento Id
		$elements[] = $this->createElement( 'hidden', 'fn_tipo_lanc_id' )
			->setBelongsTo( $belongs )
			->setIsArray( true );
                
		$elements[] = $this->createElement( 'CurrencyTextBox', 'fn_lanc_projeto_valor' )
			->addFilter( 'StringTrim' )
			->setAttrib( 'class', 'input-form' )
			->setAttrib( 'onKeyUp', 'financeiroLancamento.valorTotal();' )
			->setBelongsTo( $belongs )
			->setDijitParam( 'placeHolder', 'Informe o valor do lançamento' )
			->setDijitParam( 'currency', 'R$ ' )
			->setValue( 0 )
			->setIsArray( true );
		
                
		//Documento Fiscal
		$elements[] = $this->createElement('hidden', 'lancamento_duplicata_id');
		
		$elements[] = $this->createElement( 'ValidationTextBox', 'duplicata_desc' )
			->setLabel( 'Duplicata' )
                        ->setAttrib( 'class', 'input-form' )
			->setAttrib( 'readOnly', 'true' );
                
		$elements[] = $this->createElement( 'CurrencyTextBox', 'duplicata_valor' )
			->setLabel( 'Valor da parcela' )
			->setAttrib( 'class', 'input-form' )
                        ->setAttrib( 'readOnly', 'true' )
			->setBelongsTo( $belongs )
			->setDijitParam( 'currency', 'R$ ' )
			->setValue( 0 )
			->setIsArray( true );
                
		$this->addElements( $elements );
		
		$this->addDisplayGroup(
			array(
				'fn_lancamento_id',
				'fn_conta_id',
				'terceiro_id',
				'fn_lancamento_tipo',
				'fn_lancamento_status',
				'fn_lancamento_valor',
				'fn_lancamento_efetivado',
                                'duplicata_desc'
			),
			'left',
			array(
				'decorators' => array('FormElements') 
			)
		);
		
		$this->addDisplayGroup(
			array(
				'fn_doc_fiscal_id', 
				'fn_doc_fiscal_numero',
				'btn_doc_fiscal',
				//'fn_cheque_id',
				'valor_cheque',
				'btn_cheque',
				'tela',
				'fn_lancamento_obs',
                                'lancamento_duplicata_id',
                                'duplicata_valor'
			),
			'right',
			array(
				'decorators' => array('FormElements')
			)
		);
                
                $this->setCustomButtons(
                        array(
                            array(
                                'action'   => App_Plugins_Acl::getIdentifier( '/financeiro/lancamento/excluir', 'Excluir' ),
                                'id'       => 'buttonRemoveLancamento',
                                'name'     => 'buttonRemoveLancamento',
                                'label'    => 'Excluir',
                                'icon'     => 'dijitEditorIcon dijitEditorIconDelete',
                                'click'    => 'financeiroLancamento.deletaLancamento( "' . $this->getId() . '" )',
                                'disabled' => 'true'
                            )
                           )
                             )
                     ->setRenderDefaultButtons( true )
                     ->_defineDecorators();
                  //$this->_defineDecorators();
	}

	/**
	 * 
	 * @access public
	 * @return array
	 */
	public function getValues()
	{
		$data = array();
		$values = parent::getValues();
		
		foreach ( $values as $key => $value ) {

			if ( 'lancamento' === $key ) {

				foreach ( $value as $k => $v )
					$data[$k] = $v;
				
			} else $data[$key] = $value;	
			
		}
		
		return $data;
	}
}