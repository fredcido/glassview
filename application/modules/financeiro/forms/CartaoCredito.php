<?php

class Financeiro_Form_CartaoCredito extends App_Forms_Default
{
    /**
     * 
     */
    public function init()
    {
        $this->setName( 'form-financeiro-cartao-credito' );
	
	$elements = array();
        
        $elements[] = $this->createElement( 'hidden', 'fn_cc_id' );

	$elements[] = $this->createElement( 'ValidationTextBox', 'fn_cc_descricao' )
			   ->setLabel( 'Descrição' )
			   ->setDijitParam( 'placeHolder', 'Digite descrição do cartão' )
			   ->setAttrib( 'maxlength', 120 )
			   ->setAttrib( 'class', 'input-form' )
			   ->addFilter( 'StringTrim' )
			   ->setRequired( true );

        $dbBanco = new Model_DbTable_Banco();
	$data = $dbBanco->fetchAll( array(), 'fn_banco_nome' );

	$optBanco = array( '' => '' );
	foreach ( $data as $row )
	    $optBanco[$row->fn_banco_id] = $row->fn_banco_codigo.' - '.$row->fn_banco_nome;

	$elements[] = $this->createElement( 'FilteringSelect', 'fn_banco_id' )
			   ->setLabel( 'Banco' )
                           ->setRegisterInArrayValidator(false)
                           ->setRequired( false )
			   ->setAttrib( 'class', 'input-form' )
			   ->addMultiOptions( $optBanco );

        $elements[] = $this->createElement( 'ValidationTextBox', 'fn_cc_numero' )
                           ->setLabel(  'Número' )
                           ->setDijitParam( 'placeHolder', 'Digite o número do cartão' )
                           ->setAttrib( 'maxlength', 16 )
                           ->setAttrib( 'regExp', '^\d{0,}$' )
                           ->setAttrib( 'class', 'input-form' )
                           ->addFilter( 'StringTrim' )
                           ->setRequired( true );
        
        $elements[] = $this->createElement( 'ValidationTextBox', 'fn_cc_titular' )
                           ->setLabel(  'Titular' )
                           ->setAttrib( 'maxlength', 120 )
                           ->setDijitParam( 'placeHolder', 'Digite o titular do cartão' )
                           ->setAttrib( 'class', 'input-form' )
                           ->addFilter( 'StringTrim' )
                           ->setRequired( true );
       
        
        $elements[] = $this->createElement( 'ValidationTextBox', 'fn_cc_bandeira' )
                           ->setLabel(  'Bandeira' )
                           ->setAttrib( 'maxlength', 60 )
                           ->setDijitParam( 'placeHolder', 'Digite a bandeira do cartão' )
                           ->setAttrib( 'class', 'input-form' )
                           ->addFilter( 'StringTrim' )
                           ->setRequired( false );
        
        $elements[] = $this->createElement( 'DateTextBox', 'fn_cc_validade' )
                            ->setLabel( 'Validade' )
                            ->setAttrib( 'style', 'width:100px;' )
                            ->setDatePattern( 'MM/yyyy' )
                            ->setSelector( 'date' )
                            ->setAttrib( 'class', 'input-form' )
                            ->addFilter( 'StringTrim' )
                            ->setRequired( false );
        
        $elements[] = $this->createElement( 'ValidationTextBox', 'fn_cc_cvv' )
                           ->setLabel(  'Verificador' )
                           ->setAttrib( 'maxlength', 4 )
                           ->setAttrib( 'regExp', '^\d{0,}$' )
                           ->setAttrib( 'style', 'width:50px;' )
                           ->addFilter( 'StringTrim' )
                           ->setRequired( false );


	$optStatus = array(
                            null => '',
                            'A'  => 'Ativo',
                            'B'  => 'Bloqueado',
                            'F'  => 'Bloqueado por fraude',
                            'C'  => 'Cancelado'
                          );

	
	$elements[] = $this->createElement( 'FilteringSelect', 'fn_cc_status' )
			   ->setAttrib( 'class', 'input-form' )
			   ->setLabel( 'Status' )
                           ->setRequired( true )
			   ->addMultiOptions( $optStatus );

	$this->addElements( $elements );
    }
}