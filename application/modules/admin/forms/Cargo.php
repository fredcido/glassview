<?php

class Admin_Form_Cargo extends App_Forms_Default
{
    /**
     * 
     */
    public function init()
    {
        $this->setName( 'form-admin-cargo' );
	
	$elements = array();
        
        $elements[] = $this->createElement( 'hidden', 'cargo_id' );

	$elements[] = $this->createElement( 'ValidationTextBox', 'cargo_nome' )
			   ->setLabel( 'Nome' )
			   ->setDijitParam( 'placeHolder', 'Digite o nome do cargo' )
			   ->setAttrib( 'maxlength', 70 )
			   ->setAttrib( 'class', 'input-form' )
			   ->addFilter( 'StringTrim' )
			   ->setRequired( true );
	
	$optStatus[1] = 'Ativo';
	$optStatus[0] = 'Inativo';
	
	$elements[] = $this->createElement( 'FilteringSelect', 'cargo_status' )
                           ->setAttrib( 'readOnly', true )
			   ->setLabel( 'Status' )
			   ->setAttrib( 'class', 'input-form' )
			   ->addMultiOptions( $optStatus );
	
	$elements[] = $this->createElement( 'SimpleTextarea', 'cargo_descricao' )
			   ->setLabel( 'Descrição' )
			   ->setAttrib( 'maxlength', 400 )
			   ->setAttrib( 'class', 'input-form' )
			   ->setDijitParam( 'progressObserver', true )
			   ->addFilters( array( 'StringTrim', 'StripTags' ) );
	
	$this->addElements( $elements );
    }
}