<?php

class Almoxarifado_Form_TipoProduto extends App_Forms_Default
{
    /**
     * 
     */
    public function init()
    {
        $this->setName( 'form-almoxarifado-tipo-produto' );
	
	$elements = array();
        
        $elements[] = $this->createElement( 'hidden', 'tipo_produto_id' );

	$elements[] = $this->createElement( 'ValidationTextBox', 'tipo_produto_nome' )
			   ->setLabel( 'Nome' )
			   ->setDijitParam( 'placeHolder', 'Digite o nome do tipo do produto' )
			   ->setAttrib( 'maxlength', 100 )
			   ->setAttrib( 'class', 'input-form' )
			   ->addFilter( 'StringTrim' )
			   ->setRequired( true );

        $elements[] = $this->createElement( 'SimpleTextarea', 'tipo_produto_descricao' )
                   ->setLabel( 'Descrição' )
		   ->setAttrib( 'class', 'input-form' )
                   ->setAttrib( 'maxlength', 400 )
                   ->setDijitParam( 'progressObserver', true )
                   ->addFilters( array( 'StringTrim', 'StripTags' ) );

	$this->addElements( $elements );
    }
}