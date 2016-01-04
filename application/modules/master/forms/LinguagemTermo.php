<?php

class Master_Form_LinguagemTermo extends App_Forms_Default
{
    /**
     * 
     */
    public function init()
    { 
        $this->setName( 'form-master-linguagem-termo' );
        
        $elements[] = $this->createElement( 'hidden', 'linguagem_termo_id' );

	$elements[] = $this->createElement( 'SimpleTextarea', 'linguagem_termo_desc' )
			   ->setLabel( 'Termo' )
			   ->setAttrib( 'maxlength', 200 )
			   ->setDijitParam( 'progressObserver', true )
			   ->addFilters( array( 'StringTrim', 'StripTags' ) );
        	
	$this->addElements( $elements );
    }
}