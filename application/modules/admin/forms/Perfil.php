<?php

class Admin_Form_Perfil extends App_Forms_Default
{
    /**
     * 
     */
    public function init()
    {
        $this->setName( 'form-admin-perfil' );
	
	$elements = array();
        
        $elements[] = $this->createElement( 'hidden', 'perfil_id' );

	$elements[] = $this->createElement( 'ValidationTextBox', 'perfil_nome' )
			   ->setLabel( 'Nome' )
			   ->setDijitParam( 'placeHolder', 'Digite o nome do perfil' )
			   ->setAttrib( 'maxlength', 70 )
			   ->setAttrib( 'class', 'input-form' )
			   ->addFilter( 'StringTrim' )
			   ->setRequired( true );

        $elements[] = $this->createElement( 'SimpleTextarea', 'perfil_descricao' )
                   ->setLabel( 'Descrição' )
		   ->setAttrib( 'class', 'input-form' )
                   ->setAttrib( 'maxlength', 400 )
                   ->setDijitParam( 'progressObserver', true )
                   ->addFilters( array( 'StringTrim', 'StripTags' ) );
	
	$optStatus[1] = 'Ativo';
	$optStatus[0] = 'Inativo';
	
	$elements[] = $this->createElement( 'FilteringSelect', 'perfil_status' )
                           ->setAttrib( 'readOnly', true )
			   ->setAttrib( 'class', 'input-form' )
			   ->setLabel( 'Status' )
			   ->addMultiOptions( $optStatus );
	

	$this->addElements( $elements );
    }
}