<?php

class Financeiro_Form_Reconciliacao extends App_Forms_Default
{
    /**
     * 
     */
    public function init()
    {
        $this->setName( 'form-financeiro-reconciliacao' );
	
	$elements = array();

        $elements[] = $this->createElement( 'hidden', 'fn_recon_id' );
        
        $elements[] = $this->createElement( 'hidden', 'fn_recon_efetivada' )
                           ->setValue(0);
        
        $elements[] = $this->createElement( 'hidden', 'lancamentos' )
                    ->setIsArray( true );
        
	$dbConta = new Model_DbTable_Conta();
	$data = $dbConta->fetchAll( array( 'fn_conta_status = ?' => 1 ), 'fn_conta_descricao' );

	$optConta = array( null => '');
	foreach ( $data as $row )
	    $optConta[$row->fn_conta_id] = $row->fn_conta_descricao;

	$elements[] = $this->createElement( 'FilteringSelect', 'fn_conta_id' )
			   ->addMultiOptions( $optConta )
			   ->setLabel( 'Conta' )
			   ->setAttrib( 'style', 'width: 300px' )
			   ->setAttrib( 'onChange', 'financeiroReconciliacao.buscaLancamentos()')
			   ->setDijitParam( 'placeHolder', 'Selecione Conta' )
			   ->setRequired( true );
	
	$elements[] = $this->createElement( 'DateTextBox', 'fn_recon_dtefetivada' )
			   ->setLabel( 'Data Efetivação' )
			   ->addFilter( 'StringTrim' )
                           ->setAttrib( 'readOnly', true )
                           ->setAttrib('style', 'width:100px;');
        
	$elements[] = $this->createElement( 'DateTextBox', 'fn_recon_ini_data' )
			   ->setLabel( 'Data Inicial' )
			   ->addFilter( 'StringTrim' )
			   ->setRequired( true )
                           //->setAttrib( 'readOnly', true )
			   ->setAttrib( 'onChange', 'financeiroReconciliacao.buscaLancamentosSemSetarDataInicial()')
                           ->setAttrib('style', 'width:100px;');
        
 	$elements[] = $this->createElement( 'DateTextBox', 'fn_recon_fim_data' )
			   ->setLabel( 'Data Final' )
			   ->addFilter( 'StringTrim' )
			   ->setRequired( true )
                           ->setValue( Zend_Date::now()->toString('yyyy-MM-dd') )
                           ->setDijitParam( 'constraints',array('max' => Zend_Date::now()->toString('yyyy-MM-dd') ) )
                           ->setAttrib( 'onChange', 'financeiroReconciliacao.buscaLancamentosSemSetarDataInicial()')
                           ->setAttrib('style', 'width:100px;');
        
        $elements[] = $this->createElement( 'CurrencyTextBox', 'fn_recon_ini_valor' )
			   ->setLabel( 'Saldo Inicial' )
			   ->setAttrib( 'class', 'input-form' )
			   ->setDijitParam( 'currency', 'R$ ' )
			   ->addFilter( 'StringTrim' )
                           ->setValue(0)
                           ->setAttrib( 'readOnly', true )
			   ->setRequired( true );
               
        
        $elements[] = $this->createElement( 'CurrencyTextBox', 'fn_recon_fim_valor' )
			   ->setLabel( 'Saldo Final' )
			   ->setAttrib( 'class', 'input-form' )
			   ->setDijitParam( 'currency', 'R$ ' )
			   ->addFilter( 'StringTrim' )
                           ->setValue(0)
                           ->setAttrib( 'readOnly', true )
			   ->setRequired( true );
        
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
			'action'   => App_Plugins_Acl::getIdentifier( '/financeiro/lancamento-cartao/', 'Reconciliar' ),
			'id'       => 'buttonSalvarReconciliacaoCartao',
			'label'    => 'Salvar',
			'icon'     => 'dijitEditorIcon dijitEditorIconSave',
			'click'    => 'financeiroReconciliacao.salvarReconciliacao( "' . $this->getId() . '" )',
                        'disabled' => 'true'
		    ),
		    array(
			'label'  => 'Cancelar',
			'icon'   => 'dijitEditorIcon dijitEditorIconCancel',
			'click'  => 'objGeral.closeGenericDialog();'
		    ),
		    array(
			'action'    => App_Plugins_Acl::getIdentifier( '/financeiro/lancamento-cartao/', 'Reconciliar' ),
			'id'	    => 'buttonEfetivarReconciliacao',
			'label'	    => 'Efetivar',
			'icon'	    => 'icon-toolbar-tick',
			'click'	    => 'financeiroReconciliacao.efetivarReconciliacao( )',
			'disabled'  => 'true'
		    )
		)
	     )
	     ->_defineDecorators();
    }
}