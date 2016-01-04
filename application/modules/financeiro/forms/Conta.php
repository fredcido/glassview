<?php

class Financeiro_Form_Conta extends App_Forms_Default
{
    /**
     * 
     */
    public function init()
    {
        $this->setName( 'form-financeiro-conta' );
	
	$elements = array();
        
        $elements[] = $this->createElement( 'hidden', 'fn_conta_id' );
        $elements[] = $this->createElement( 'hidden', 'lancamentos' )
                           ->setValue(0);

	$elements[] = $this->createElement( 'ValidationTextBox', 'fn_conta_descricao' )
			   ->setLabel( 'Descrição' )
			   ->setDijitParam( 'placeHolder', 'Digite descrição da conta' )
			   ->setAttrib( 'maxlength', 50 )
			   ->setAttrib( 'class', 'input-form' )
			   ->addFilter( 'StringTrim' )
			   ->setRequired( true );

	$optTipo[''] = '';
	$optTipo['B'] = 'Banco';
	$optTipo['C'] = 'Caixa';
	$optTipo['O'] = 'Outros';
	
	$elements[] = $this->createElement( 'FilteringSelect', 'fn_conta_tipo' )
			   ->setAttrib( 'class', 'input-form' )
			   ->setLabel( 'Tipo' )
                           ->setRequired( true )
                           ->setAttrib( 'onChange','financeiroConta.setRequired( )' )
			   ->addMultiOptions( $optTipo );


	$dbBanco = new Model_DbTable_Banco();
	$data = $dbBanco->fetchAll( array(), 'fn_banco_nome' );

	$optBanco = array( '' => '' );
	foreach ( $data as $row )
	    $optBanco[$row->fn_banco_id] = $row->fn_banco_codigo.' - '.$row->fn_banco_nome;

	$elements[] = $this->createElement( 'FilteringSelect', 'fn_banco_id' )
			   ->setLabel( 'Banco' )
                           ->setRegisterInArrayValidator(false)
                           ->addMultiOptions( $optBanco )
			   ->setAttrib( 'class', 'input-form' )
                           ->setAttrib( 'readOnly', true )
                           ->setRequired( false );
			   
        
        $elements[] = $this->createElement( 'ValidationTextBox', 'fn_conta_agencia' )
                           ->setLabel(  'Agência' )
                           ->setAttrib( 'maxlength', 120 )
                           ->setDijitParam( 'placeHolder', 'Digite o número da agência' )
                           ->setAttrib( 'class', 'input-form' )
                           ->addFilter( 'StringTrim' )
                           ->setAttrib( 'readOnly', true )
                           ->setRequired( false );
        
        $elements[] = $this->createElement( 'ValidationTextBox', 'fn_conta_numero' )
                           ->setLabel(  'Número' )
                           ->setAttrib( 'maxlength', 50 )
                           ->setDijitParam( 'placeHolder', 'Digite o número da conta' )
                           ->setAttrib( 'class', 'input-form' )
                           ->addFilter( 'StringTrim' )
                           ->setAttrib( 'readOnly', true )
                           ->setRequired( false );

        $elements[] = $this->createElement( 'CurrencyTextBox', 'fn_conta_saldo_inicial' )
			   ->setLabel( 'Saldo inicial' )
			   ->setDijitParam( 'placeHolder', 'Informe o saldo da conta' )
			   ->setAttrib( 'class', 'input-form' )
                           ->setAttrib( 'onChange',"dijit.byId('fn_conta_saldo').set( 'value', this.value );" )
			   ->setDijitParam( 'currency', 'R$ ' )
			   ->addFilter( 'StringTrim' )
                           ->setValue(0)
                           ->setAttrib( 'readOnly', true )
			   ->setRequired( true );

        $elements[] = $this->createElement( 'CurrencyTextBox', 'fn_conta_saldo' )
			   ->setLabel( 'Saldo atual' )
			   ->setDijitParam( 'placeHolder', 'Informe o saldo da conta' )
			   ->setAttrib( 'class', 'input-form' )
			   ->setDijitParam( 'currency', 'R$ ' )
			   ->addFilter( 'StringTrim' )
                           ->setValue(0)
                           ->setAttrib( 'readOnly', true )
			   ->setRequired( true );

  	$elements[] = $this->createElement( 'DateTextBox', 'fn_conta_data_cad' )
			   ->setLabel( 'Data' )
                           ->setValue( Zend_Date::now()->toString('yyyy-MM-dd') )
			   ->addFilter( 'StringTrim' )
			   ->setRequired( true )
                           ->setAttrib( 'readOnly', true )
                           ->setAttrib('style', 'width:100px;');
        
	$optStatus[1] = 'Ativo';
	$optStatus[0] = 'Inativo';
	
	$elements[] = $this->createElement( 'FilteringSelect', 'fn_conta_status' )
                           ->setAttrib( 'readOnly', true )
			   ->setAttrib( 'class', 'input-form' )
			   ->setLabel( 'Status' )
			   ->addMultiOptions( $optStatus );

	$this->addElements( $elements );
    }
}