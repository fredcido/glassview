<?php

class Financeiro_Form_LancamentoCartao extends App_Forms_Default
{
    /**
     * 
     */
    public function init()
    {
        $this->setName( 'form-financeiro-lancamento-cartao' );
	
	$elements = array();

        $elements[] = $this->createElement( 'hidden', 'fn_lanc_cartao_id' );
        
	$elements[] = $this->createElement( 'hidden', 'fn_lanc_cartao_status' )
                           ->setValue('P');

	$dbCartaoCredito = new Model_DbTable_CartaoCredito();
	$data = $dbCartaoCredito->fetchAll( array( 'fn_cc_status = ?' => 'A' ), 'fn_cc_descricao' );

	$optCartaoCredito = array( null => '');
	foreach ( $data as $row )
	    $optCartaoCredito[$row->fn_cc_id] = $row->fn_cc_descricao;

	$elements[] = $this->createElement( 'FilteringSelect', 'fn_cc_id' )
			   ->setLabel( 'Cartão' )
			   ->addMultiOptions( $optCartaoCredito )
			   ->setAttrib( 'class', 'input-form' )
			   ->setDijitParam( 'placeHolder', 'Selecione o Cartão de Credito' )
			   ->setRequired( true );
        
	$elements[] = $this->createElement( 'ValidationTextBox', 'fn_lanc_cartao_desc' )
			   ->setLabel( 'Descrição' )
			   ->setDijitParam( 'placeHolder', 'Digite descrição do lançamento' )
			   ->setAttrib( 'maxlength', 200 )
                           ->setAttrib('style', 'width:280px;')
			   ->setAttrib( 'class', 'input-form' )
			   ->addFilter( 'StringTrim' )
			   ->setRequired( true );
        
        $elements[] = $this->createElement( 'CurrencyTextBox', 'fn_lanc_cartao_valor' )
			   ->setLabel( 'Valor Total' )
			   ->setDijitParam( 'placeHolder', 'Informe o valor do lançamento' )
			   ->setAttrib( 'class', 'input-form' )
                           ->setAttrib( 'readOnly', true )
			   ->setDijitParam( 'currency', 'R$ ' )
			   ->addFilter( 'StringTrim' )
                           ->setValue(0)
			   ->setRequired( true );
        
	$elements[] = $this->createElement( 'DateTextBox', 'fn_lanc_cartao_data' )
			   ->setLabel( 'Data' )
                           ->setValue( Zend_Date::now()->toString('yyyy-MM-dd') )
			   ->addFilter( 'StringTrim' )
			   ->setRequired( true )
                           ->setAttrib('style', 'width:100px;');    
        

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

        $elements[] = $this->createElement( 'hidden', 'fn_lanc_cartao_tipo_id' )
                            ->setIsArray( true );

//	$dbProjeto = new Model_DbTable_Projeto();
//	$data = $dbProjeto->fetchAll( array( 'projeto_status = ?' => 'I' ), 'projeto_nome' );
//
	$optProjeto = array( null => '');
//	foreach ( $data as $row )
//	    $optProjeto[$row->projeto_id] = $row->projeto_nome;

	$elements[] = $this->createElement( 'FilteringSelect', 'projeto_id' )
			   //->setLabel( 'Projeto' )
			   ->addMultiOptions( $optProjeto )
                           ->setAttrib( 'onChange', 'financeiroLancamentoCartao.filteringTipoLancamento();' )
			   ->setAttrib( 'class', 'input-form' )
                           ->setRegisterInArrayValidator(false)
			   ->setDijitParam( 'placeHolder', 'Selecione o Projeto' )
			   //->setRequired( true )
                           ->setIsArray( true );

        
	$elements[] = $this->createElement( 'ValidationTextBox', 'text_lancamento' )
			   ->setAttrib( 'class', 'input-form' )
			   ->setAttrib( 'readOnly', 'true' )
                           ->setIsArray( true );
        
	$elements[] = $this->createElement( 'hidden', 'fn_tipo_lanc_id' )
                           ->setIsArray( true );

        $elements[] = $this->createElement( 'CurrencyTextBox', 'fn_lanc_cc_tipo_valor' )
			   //->setLabel( 'Valor' )
			   ->setDijitParam( 'placeHolder', 'Informe o valor do lançamento' )
			   ->setAttrib( 'class', 'input-form' )
			   ->setDijitParam( 'currency', 'R$ ' )
			   ->addFilter( 'StringTrim' )
                           ->setValue(0)
			   //->setRequired( true )
                           ->setIsArray( true );
        

	$this->addElements( $elements );

        $this->addDisplayGroup(
                array(
                        'fn_lanc_cartao_id',
                        'fn_cc_id',
                        'fn_lanc_cartao_desc',
                        'fn_lanc_cartao_status',
                        'fn_doc_fiscal_id',
                        'fn_doc_fiscal_numero',
                        'btn_doc_fiscal'
                ),
                'left',
                array(
                        'decorators' => array('FormElements')
                )
        );

        $this->addDisplayGroup(
                array(
                        'fn_lanc_cartao_data',
                        'fn_lanc_cartao_valor'
                ),
                'right',
                array(
                        'decorators' => array('FormElements')
                )
        );
	
	$this->_defineDecorators();
    }
}