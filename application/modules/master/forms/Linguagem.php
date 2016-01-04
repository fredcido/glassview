<?php

class Master_Form_Linguagem extends App_Forms_Default
{
    /**
     * 
     */
    public function init()
    { 
        $this->setName( 'form-master-linguagem' );
        
        $elements[] = $this->createElement( 'hidden', 'linguagem_id' );

	$elements[] = $this->createElement( 'ValidationTextBox', 'linguagem_nome' )
			   ->setLabel( 'Nome' )
			   ->setAttrib( 'class', 'input-form' )
			   ->setDijitParam( 'placeHolder' , $this->getView()->translate()->_('Digite o nome da linguagem') )
			   ->setAttrib( 'maxlength', 100 )
			   ->addFilter( 'StringTrim' )
			   ->setRequired( true );
        
	$elements[] = $this->createElement( 'ValidationTextBox', 'linguagem_local' )
			   ->setLabel( 'Local' )
			   ->setAttrib( 'class', 'input-form' )
			   ->setDijitParam( 'placeHolder' , $this->getView()->translate()->_('Digite o local da linguagem') )
			   ->setAttrib( 'maxlength', 5 )
			   ->addFilter( 'StringTrim' )
			   ->setRequired( true );
	
	$optStatus[1] = $this->getView()->translate()->_('Ativo');
	$optStatus[0] = $this->getView()->translate()->_('Inativado');
	
	$elements[] = $this->createElement( 'FilteringSelect', 'linguagem_status' )
			   ->setLabel( 'Status' )
			   ->setAttrib( 'class' , 'input-form' )
                           ->setAttrib( 'readOnly', true )
			   ->addMultiOptions( $optStatus );
	
	$this->addElements( $elements );
    }
}