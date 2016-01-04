<?php

class Master_Form_Modulo extends App_Forms_Default
{
    /**
     * 
     */
    public function init()
    { 
        $this->setName( 'form-master-modulo' );
        
        $elements[] = $this->createElement( 'hidden', 'modulo_id' );

	$elements[] = $this->createElement( 'ValidationTextBox', 'modulo_nome' )
			   ->setLabel( 'Nome' )
			   ->setAttrib( 'class', 'input-form' )
			   ->setDijitParam( 'placeHolder' , $this->getView()->translate()->_('Digite o nome do módulo') )
			   ->setAttrib( 'maxlength', 100 )
			   ->addFilter( 'StringTrim' )
			   ->setRequired( true );
	
	$optStatus[1] = 'Liberado';
	$optStatus[0] = 'Bloqueado';
	
	$elements[] = $this->createElement( 'FilteringSelect', 'modulo_status' )
			   ->setLabel( 'Status' )
			   ->setAttrib( 'class' , 'input-form' )
			   ->addMultiOptions( $optStatus );
	
	$this->addElements( $elements );
    }
}