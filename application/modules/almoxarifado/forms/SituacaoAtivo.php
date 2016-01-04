<?php

class Almoxarifado_Form_SituacaoAtivo extends App_Forms_Default
{
    /**
     * 
     */
    public function init()
    {
        $this->setName( 'form-admin-situacao-ativo' );
	
	$elements = array();
        
        $elements[] = $this->createElement( 'hidden', 'situacao_ativo_id' );

	$elements[] = $this->createElement( 'ValidationTextBox', 'situacao_ativo_nome' )
			   ->setLabel( 'Nome' )
			   ->setDijitParam( 'placeHolder', 'Digite o nome do situação' )
			   ->setAttrib( 'maxlength', 70 )
			   ->setAttrib( 'class', 'input-form' )
			   ->addFilter( 'StringTrim' )
			   ->setRequired( true );

        $elements[] = $this->createElement( 'SimpleTextarea', 'situacao_ativo_descricao' )
                   ->setLabel( 'Descrição' )
		   ->setAttrib( 'class', 'input-form' )
                   ->setAttrib( 'maxlength', 400 )
                   ->setDijitParam( 'progressObserver', true )
                   ->addFilters( array( 'StringTrim', 'StripTags' ) );

	$this->addElements( $elements );
    }
}