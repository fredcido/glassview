<?php

class Financeiro_Form_Cheque extends App_Forms_Default
{
    /**
     * 
     */
    public function init()
    {
        $this->setName( 'form-financeiro-cheque' );
	
	$elements = array();
        
        $elements[] = $this->createElement( 'hidden', 'fn_cheque_id' );


        $dbConta = new Model_DbTable_Conta();
	$data = $dbConta->fetchAll( array(), 'fn_conta_descricao' );

	$optConta = array( '' => '' );
	foreach ( $data as $row )
	    $optConta[$row->fn_conta_id] = $row->fn_conta_descricao;

	$elements[] = $this->createElement( 'FilteringSelect', 'fn_conta_id' )
			   ->setLabel( 'Conta' )
                           ->setRegisterInArrayValidator(false)
			   ->addMultiOptions( $optConta )
                           ->setAttrib( 'class', 'input-form' )
                           ->setRequired( true );
        
        
        $dbTerceiro = new Model_DbTable_Terceiro();
	$data = $dbTerceiro->fetchAll( array(), 'terceiro_nome' );

	$optTerceiro = array( '' => '' );
	foreach ( $data as $row )
	    $optTerceiro[$row->terceiro_id] = $row->terceiro_nome;

	$elements[] = $this->createElement( 'FilteringSelect', 'terceiro_id' )
			   ->setLabel( 'Emitido para' )
                           ->setRegisterInArrayValidator(false)
			   ->addMultiOptions( $optTerceiro )
                           ->setAttrib( 'class', 'input-form' )
                           ->setRequired( true );
        
        
        $elements[] = $this->createElement( 'ValidationTextBox', 'fn_cheque_numero' )
                           ->setLabel(  'Número' )
                           ->setAttrib( 'maxlength', 60 )
                           ->setDijitParam( 'placeHolder', 'Digite o número do cheque' )
                           ->setAttrib( 'class', 'input-form' )
                           ->addFilter( 'StringTrim' )
                           ->setRequired( true );

        $elements[] = $this->createElement( 'CurrencyTextBox', 'fn_cheque_valor' )
			   ->setLabel( 'Valor' )
			   ->setAttrib( 'class', 'input-form' )
			   ->setDijitParam( 'currency', 'R$ ' )
			   ->addFilter( 'StringTrim' )
                           ->setValue(0)
			   ->setRequired( true );
        
	$elements[] = $this->createElement( 'DateTextBox', 'fn_cheque_data' )
			   ->setLabel( 'Emissão' )
                           ->setValue( Zend_Date::now()->toString('yyyy-MM-dd') )
			   ->addFilter( 'StringTrim' )
			   ->setRequired( true )
                           ->setAttrib('style', 'width:100px;');
        
	$elements[] = $this->createElement( 'DateTextBox', 'fn_cheque_para' )
			   ->setLabel( 'Para' )
                           ->setValue( Zend_Date::now()->toString('yyyy-MM-dd') )
			   ->addFilter( 'StringTrim' )
			   ->setRequired( true )
                           ->setAttrib('style', 'width:100px;');
        
        $optSituacao = array(
            ''  => '',
            'A' => 'A Compensar',
            'D' => 'Depositado',
            'C' => 'Compensado',
            'V' => 'Devolvido',
            'P' => 'Pago',
            'R' => 'Repassado'
        );

	$elements[] = $this->createElement( 'FilteringSelect', 'fn_cheque_situacao' )
			   ->setLabel( 'Situação' )
			   ->setRequired( true )
			   ->setAttrib( 'class', 'input-form' )
			   ->addMultiOptions( $optSituacao );
        
        
	$this->addElements( $elements );
    }
}