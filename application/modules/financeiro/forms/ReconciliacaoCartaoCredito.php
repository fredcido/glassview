<?php

class Financeiro_Form_ReconciliacaoCartaoCredito extends App_Forms_Default
{
    /**
     * 
     */
    public function init()
    {
        $this->setName( 'formFinanceiroReconciliacaoCartao' );
	
	$elements = array();

        $elements[] = $this->createElement( 'hidden', 'fn_cc_fat_id' );
        
	$dbCartaoCredito = new Model_DbTable_CartaoCredito();
	$data = $dbCartaoCredito->fetchAll( array( 'fn_cc_status = ?' => 'A' ), 'fn_cc_descricao' );

	$optCartaoCredito = array( null => '');
	foreach ( $data as $row )
	    $optCartaoCredito[$row->fn_cc_id] = $row->fn_cc_descricao;

	$elements[] = $this->createElement( 'FilteringSelect', 'fn_cc_id' )
			   ->addMultiOptions( $optCartaoCredito )
			   ->setLabel( 'Cartão de Crédito' )
			   ->setAttrib( 'style', 'width: 300px' )
			   ->setAttrib( 'onChange', 'financeiroLancamentoCartao.buscaLancamentos()')
			   ->setDijitParam( 'placeHolder', 'Selecione o Cartão de Credito' )
			   ->setRequired( true );
	
	$elements[] = $this->createElement( 'CurrencyTextBox', 'fn_cc_fat_total' )
			    ->setLabel( 'Total Fatura' )
			    ->setAttrib( 'readOnly', 'true' )
			    ->setDijitParam( 'placeHolder', 'Informe o valor da fatura' )
			    ->setAttrib( 'class', 'input-form' )
			    ->setAttrib( 'onChange', 'financeiroLancamentoCartao.liberaMovimentacao()')
			    ->setDijitParam( 'currency', 'R$ ' )
                            ->setValue(0)
			    ->setRequired( true );
	
	$elements[] = $this->createElement( 'FilteringSelect', 'fn_cc_fat_ref' )
			   ->addMultiOptions( $this->_getOptionsReferencia() )
			   ->setAttrib( 'class', 'input-form' )
			   ->setAttrib( 'readOnly', 'true' )
			   ->setLabel( 'Referência' )
			   ->setDijitParam( 'placeHolder', 'Mês/Ano de referência' )
			   ->setRequired( true );
      
	$elements[] = $this->createElement( 'DateTextBox', 'fn_cc_fat_vencimento' )
			   ->setRequired( true )
			   ->setAttrib( 'class', 'input-form' )
			   ->setAttrib( 'readOnly', 'true' )
			   ->setDijitParam( 'placeHolder', 'Selecione a data de vencimento' )
			   ->setLabel( 'Vencimento' );

	$this->setDefaultDecorator( 'columns' )
	     ->addElements( $elements );
	
	$this->addDisplayGroup( 
				$elements, 
				'elementos_form', 
				array( 'decorators' => array( 'FormElements' ) )
			      );
	
	$this->setRenderDefaultButtons( false )
	     ->setCustomButtons(
		array(
		    array(
			'action' => App_Plugins_Acl::getIdentifier( '/financeiro/lancamento-cartao/', 'Reconciliar' ),
			'id'     => 'buttonSalvarReconciliacaoCartao',
			'label'  => 'Salvar',
			'icon'   => 'dijitEditorIcon dijitEditorIconSave',
			'click'  => 'financeiroLancamentoCartao.salvarReconciliacao( "' . $this->getId() . '" )'
		    ),
		    array(
			'label'  => 'Fechar',
			'icon'   => 'dijitEditorIcon dijitEditorIconCancel',
			'click'  => 'objGeral.closeGenericDialog();'
		    ),
		    array(
			'action'    => App_Plugins_Acl::getIdentifier( '/financeiro/lancamento-cartao/', 'Reconciliar' ),
			'id'	    => 'buttonEfetivarReconciliacaoCartao',
			'label'	    => 'Efetivar',
			'icon'	    => 'icon-toolbar-tick',
			'click'	    => 'financeiroLancamentoCartao.efetivarReconciliacao( "' . $this->getId() . '" )',
			'disabled'  => 'true'
		    )
		)
	     )
	     ->_defineDecorators();
    }
    
    /**
     *
     * @return array
     */
    protected function _getOptionsReferencia()
    {
	$meses = range( 1, 12 );
	$anos = range( date('Y') + 30, date('Y') - 10 );
	
	$opts[''] = '';
	foreach ( $anos as $ano ) {
	    foreach ( $meses as $mes ) {
		
		$mes = str_pad( $mes, 2, '0', STR_PAD_LEFT );
		$opts[ sprintf( '%s-%s-%s', $ano, $mes, '01' ) ] = sprintf( '%s/%s', $mes, $ano );
		
	    }
	}
	
	return $opts;
		
    }
}