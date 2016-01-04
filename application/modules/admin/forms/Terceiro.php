<?php

class Admin_Form_Terceiro extends App_Forms_Default
{
    /**
     * 
     */
    public function init()
    {
        $this->setName( 'form-admin-terceiro' );
	
	$elements = array();
        
        $elements[] = $this->createElement( 'hidden', 'terceiro_id ' );

	$elements[] = $this->createElement( 'ValidationTextBox', 'terceiro_nome' )
			   ->setLabel( 'Nome' )
			   ->setDijitParam( 'placeHolder', 'Digite o nome do terceiro' )
			   ->setAttrib( 'maxlength', 150 )
			   ->setAttrib( 'class', 'input-form' )
			   ->addFilter( 'StringTrim' )
			   ->setRequired( true );

	$elements[] = $this->createElement( 'FilteringSelect', 'terceiro_tipo' )
			   ->setLabel( 'Tipo' )
                           ->setDijitParam( 'placeHolder', 'Selecione o tipo' )
			   ->setAttrib( 'class', 'input-form' )
			   ->setRequired( true )
			   ->addMultiOptions( array( '' => '', 
                                                    'C' =>  'Cliente' , 
                                                    'F' =>  'Fornecedor' , 
                                                    'M' =>  'Mantenedor'
                                                    ) 
                                            );
        
	$elements[] = $this->createElement( 'FilteringSelect', 'terceiro_pessoa ' )
			   ->setLabel( 'Pessoa' )
			   ->setAttrib( 'class', 'input-form' )
			   ->setRequired( true )
                           ->setDijitParam( 'placeHolder', 'Selecione o tipo de pessoa' )
			   ->addMultiOptions( array( '' => '', 
                                                    'F' =>  'Física' , 
                                                    'J' =>  'Jurídica'
                                                    ) 
                                            );
        
	$elements[] = $this->createElement( 'ValidationTextBox', 'terceiro_cpf_cnpj' )
			   ->setLabel( 'CPF / CNPJ' )
			   ->setDijitParam( 'placeHolder', 'Digite o CPF ou CNPJ' )
			   ->setAttrib( 'maxlength', 20 )
                           ->setAttrib('regExp', '^\d{0,}$')
			   ->setAttrib( 'class', 'input-form' )
			   ->addFilter( 'StringTrim' )
			   ->setRequired( false );


	$elements[] = $this->createElement( 'ValidationTextBox', 'terceiro_contato' )
			   ->setLabel( 'Contato' )
			   ->setDijitParam( 'placeHolder', 'Digite o nome do contato do terceiro' )
			   ->setAttrib( 'maxlength', 100 )
			   ->setAttrib( 'class', 'input-form' )
			   ->addFilter( 'StringTrim' )
			   ->setRequired( false );
        
	$elements[] = $this->createElement( 'ValidationTextBox', 'terceiro_telefone' )
			   ->setLabel( 'Telefone' )
			   ->setDijitParam( 'placeHolder', 'Digite o telefone do terceiro' )
			   ->setAttrib( 'maxlength', 40 )
			   ->setAttrib( 'class', 'input-form' )
			   ->addFilter( 'StringTrim' )
			   ->setRequired( false );

	$elements[] = $this->createElement( 'ValidationTextBox', 'terceiro_fax' )
			   ->setLabel( 'Fax' )
			   ->setDijitParam( 'placeHolder', 'Digite o fax do terceiro' )
			   ->setAttrib( 'maxlength', 40 )
			   ->setAttrib( 'class', 'input-form' )
			   ->addFilter( 'StringTrim' )
			   ->setRequired( false );
        
	$elements[] = $this->createElement( 'SimpleTextarea', 'terceiro_endereco ' )
			   ->setLabel( 'Endereço' )
			   ->setAttrib( 'maxlength', 255  )
			   ->setAttrib( 'class', 'input-form' )
			   ->setDijitParam( 'progressObserver', true )
			   ->addFilters( array( 'StringTrim', 'StripTags' ) );
	
	$this->addElements( $elements );
    }
}