<?php

class Gestao_Form_Atividade extends App_Forms_Default
{
    /**
     * 
     */
    public function init()
    {
        $this->setName( 'form-gestao-atividade' );
        
        $elements[] = $this->createElement( 'hidden', 'atividade_id' );

	$elements[] = $this->createElement( 'ValidationTextBox', 'atividade_nome' )
			   ->setLabel( 'Nome' )
			   ->setDijitParam( 'placeHolder', 'Digite o nome da atividade' )
			   ->setAttrib( 'maxlength', 100 )
			   ->setAttrib( 'class', 'input-form' )
			   ->addFilter( 'StringTrim' )
			   ->setRequired( true );
		
	$elements[] = $this->createElement( 'SimpleTextarea', 'atividade_descricao' )
			   ->setLabel( 'Descrição' )
			   ->setAttrib( 'maxlength', 400 )
			   ->setAttrib( 'class', 'input-form' )
			   ->setDijitParam( 'progressObserver', true )
			   ->addFilters( array( 'StringTrim', 'StripTags' ) );

	
	$this->addElements( $elements );
    }
}