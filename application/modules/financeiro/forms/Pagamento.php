<?php

/**
 * 
 * @version $Id $
 */
class Financeiro_Form_Pagamento extends App_Forms_Default 
{
    /**
     * 
     */
    public function init() 
    {
        $this->setName( 'form-financeiro-pagamento' );
        
        $elements = array();
        $belongs = 'lancamento';

        $elements[] = $this->createElement( 'hidden', 'fn_lancamento_id' )->setBelongsTo( $belongs );

        $elements[] = $this->createElement( 'DateTextBox', 'fn_lancamento_dtefetivado' )
                   ->setRequired( true )
                   ->setAttrib( 'class', 'input-form' )
                   ->setDijitParam( 'placeHolder', 'Selecione a data de efetivação' )
                   ->setValue( Zend_Date::now()->toString( 'yyyy-MM-dd' ) )
                   ->setBelongsTo( $belongs )
                   ->setLabel( 'Data' );

        $elements[] = $this->createElement('CurrencyTextBox', 'fn_forma_pgto_valor')
                ->setLabel('Valor')
                ->setDijitParam('currency', 'R$ ')
                ->setAttrib('class', 'input-form')
                ->setBelongsTo( $belongs )
                ->setRequired(true);
        
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
        
        //Transacao			
        $elements[] = $this->createElement( 'FilteringSelect', 'fn_lancamento_tipo' )
                ->setLabel( 'Transação' )
                ->setBelongsTo( $belongs )
                ->setAttrib( 'class', 'input-form' )
                ->setAttrib( 'readOnly', true )
                ->setAttrib( 'onChange', 'financeiroLancamento.stateElementEfetivar( this.value )')
                ->addMultiOptions(
                        array(
                                '' => '',
                                'C' => 'Crédito',
                                'D' => 'Débito'
                        ) 
                )
                ->setRequired( true );
        /* Elementos da Grid */

        //Projeto
        $dbProjeto = App_Model_DbTable_Factory::get( 'Projeto' );

        $rowsProjeto = $dbProjeto->fetchAll( array('projeto_status = ?' => 'I'), 'projeto_nome' );
        $optProjeto = array( null => '' );

        foreach ( $rowsProjeto as $rowProjeto )
                $optProjeto[$rowProjeto->projeto_id] = $rowProjeto->projeto_nome;

        $elements[] = $this->createElement( 'FilteringSelect', 'projeto_id' )
                ->addMultiOptions( $optProjeto )
                ->setAttrib( 'class', 'input-form' )
                ->setAttrib( 'readOnly', true )
                ->setDijitParam( 'placeHolder', 'Selecione o Projeto' )
                ->setRegisterInArrayValidator( false )
                ->setBelongsTo( $belongs )
                ->setIsArray( true );
                
        //Tipo Lancamento Texto
        $elements[] = $this->createElement( 'ValidationTextBox', 'text_lancamento' )
                ->setAttrib( 'class', 'input-form' )
                ->setAttrib( 'readOnly', true )
                ->setRequired( false )
                ->setBelongsTo( $belongs )
                ->setIsArray( true );
        
        //Tipo Lancamento Id
        $elements[] = $this->createElement( 'hidden', 'fn_tipo_lanc_id' )
                            ->setBelongsTo( $belongs )
                             ->setIsArray( true );
        
        $elements[] = $this->createElement( 'CurrencyTextBox', 'fn_lanc_projeto_valor' )
                ->addFilter( 'StringTrim' )
                ->setAttrib( 'class', 'input-form' )
                ->setAttrib( 'onKeyUp', 'financeiroLancamento.valorTotal();' )
                ->setDijitParam( 'placeHolder', 'Informe o valor do lançamento' )
                ->setDijitParam( 'currency', 'R$ ' )
                ->setValue( 0 )
                ->setBelongsTo( $belongs )
                ->setIsArray( true );

	$this->addElements( $elements );

        $this->addDisplayGroup(
                array(
                        'fn_lancamento_id',
                        'fn_conta_id',
                        'fn_lancamento_tipo'
                ),
                'left',
                array(
                        'decorators' => array('FormElements')
                )
        );

        $this->addDisplayGroup(
                array(
                        'fn_lancamento_dtefetivado',
                        'fn_forma_pgto_valor'
                ),
                'right',
                array(
                        'decorators' => array('FormElements')
                )
        );

        $this->_defineDecorators();
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