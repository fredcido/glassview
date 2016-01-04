<?php

class Almoxarifado_Form_TipoAtivo extends App_Forms_Default
{
    /**
     * 
     */
    public function init()
    {
        $this->setName( 'form-almoxarifado-tipo-ativo' );
	
	$elements = array();
        
        $elements[] = $this->createElement( 'hidden', 'tipo_ativo_id' );

	$elements[] = $this->createElement( 'ValidationTextBox', 'tipo_ativo_nome' )
			   ->setLabel( 'Nome' )
			   ->setDijitParam( 'placeHolder', 'Digite o nome do tipo de ativo' )
			   ->setAttrib( 'maxlength', 70 )
			   ->setAttrib( 'class', 'input-form' )
			   ->addFilter( 'StringTrim' )
			   ->setRequired( true );

        $elements[] = $this->createElement( 'SimpleTextarea', 'tipo_ativo_descricao' )
                   ->setLabel( 'Descrição' )
		   ->setAttrib( 'class', 'input-form' )
                   ->setAttrib( 'maxlength', 400 )
                   ->setDijitParam( 'progressObserver', true )
                   ->addFilters( array( 'StringTrim', 'StripTags' ) );
	
	$optStatus[1] = 'Ativo';
	$optStatus[0] = 'Inativo';
	
	$elements[] = $this->createElement( 'FilteringSelect', 'tipo_ativo_status' )
                           ->setAttrib( 'readOnly', true )
			   ->setAttrib( 'class', 'input-form' )
			   ->setLabel( 'Status' )
			   ->addMultiOptions( $optStatus );

	$this->addElements( $elements );
    }
}