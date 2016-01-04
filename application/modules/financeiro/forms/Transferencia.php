<?php

class Financeiro_Form_Transferencia extends App_Forms_Default 
{
	public function init() 
	{
		$this->setName( 'form-financeiro-transferencia' );

		$elements = array();
                
		$elements[] = $this->createElement( 'hidden', 'fn_lancamento_id_origem' );
		$elements[] = $this->createElement( 'hidden', 'fn_lancamento_id_destino' );
                $elements[] = $this->createElement( 'hidden', 'fn_lancamento_data' )
                                   ->setValue(Zend_Date::now()->toString('yyyy-MM-dd HH:mm:ss'));
		
		/*
		$elements[] = $this->createElement('hidden', 'fn_doc_fiscal_id');
		
		$elements[] = $this->createElement( 'ValidationTextBox', 'fn_doc_fiscal_numero' )
			->setLabel( 'Documento Fiscal' )
			->setAttrib( 'style', 'width:169px;' )
			->setAttrib( 'readOnly', 'true' )
			->setRequired( true );
		 

		$elements[] = $this->createElement( 'Button', 'btn_doc_fiscal' )
			->setLabel( '' )
			->setAttrib( 'onClick','financeiroDuplicata.buscaDocFiscal()' )
			->setAttrib( 'style', 'float:left; margin-top:-2px;' )
			->setDijitParam( 'iconClass','icon-toolbar-applicationformmagnify' )
			->setDecorators( array('DijitElement') )
			->setDijitParam( 'showLabel', 'false' );
		*/
		$dbConta = App_Model_DbTable_Factory::get( 'Conta' );
		$rows = $dbConta->fetchAll( array('fn_conta_status' => 1), 'fn_conta_descricao' );

		$opt = array( null => '' );
		
		foreach ( $rows as $row )
			$opt[$row->fn_conta_id] = $row->fn_conta_descricao;

		$elements[] = $this->createElement('FilteringSelect', 'conta_origem')
			->setLabel('Conta Origem')
			->setAttrib('onChange', 'financeiroLancamento.buscaContaDestino(this.value);financeiroLancamento.buscaSaldo(this.id);')
			->setRequired(true)
			->setAttrib('class', 'input-form')
			->addMultiOptions($opt);

		$elements[] = $this->createElement('FilteringSelect', 'conta_destino')
			->setLabel('Conta Destino')
			->setRequired(true)
			->setAttrib('onChange', 'financeiroLancamento.buscaSaldo(this.id);')
			->setAttrib('class', 'input-form')
			->setAttrib('disabled', true);

		$elements[] = $this->createElement('CurrencyTextBox', 'saldo_conta_origem')
			->setLabel('Saldo Conta Origem')
			->setDijitParam( 'currency', 'R$ ' )
			->setAttrib('class', 'input-form')
			->setAttrib('readOnly', true);
			
		$elements[] = $this->createElement('CurrencyTextBox', 'saldo_conta_destino')
			->setLabel('Saldo Conta Destino')
			->setDijitParam( 'currency', 'R$ ' )
			->setAttrib('class', 'input-form')
			->setAttrib('readOnly', true);
		
		$elements[] = $this->createElement( 'DateTextBox', 'fn_lancamento_dtefetivado' )
			   ->setRequired( true )
			   ->setAttrib( 'class', 'input-form' )
			   ->setDijitParam( 'placeHolder', 'Selecione a data de lançamento' )
			   ->setLabel( 'Data' )
                           ->setAttrib('constraints', "{max:'".Zend_Date::now()->toString('yyyy-MM-dd')."'}")
                           ->setValue(Zend_Date::now()->toString('yyyy-MM-dd'));
			
		$elements[] = $this->createElement('CurrencyTextBox', 'valor')
			->setLabel('Valor')
			->setDijitParam('currency', 'R$ ')
			->setAttrib('class', 'input-form')
			->setRequired(true);
			
		$elements[] = $this->createElement( 'SimpleTextarea', 'fn_lancamento_obs' )
		   ->setLabel( 'Observação' )
		   ->setAttrib( 'maxlength', 250 )
		   ->setAttrib( 'cols', 26 )
		   ->setDijitParam( 'progressObserver', true )
		   ->addFilters( array( 'StringTrim', 'StripTags' ) );

		$this->addElements($elements);
	}

}