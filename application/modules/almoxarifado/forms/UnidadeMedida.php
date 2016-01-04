<?php

class Almoxarifado_Form_UnidadeMedida extends App_Forms_Default
{
    /**
     * 
     */
    public function init()
    {
        $this->setName( 'form-almoxarifado-unidade-medida' );
	
	$elements = array();
        
        $elements[] = $this->createElement( 'hidden', 'unidade_medida_id' );

	$elements[] = $this->createElement( 'ValidationTextBox', 'unidade_medida_nome' )
			   ->setLabel( 'Nome' )
			   ->setDijitParam( 'placeHolder', 'Digite o nome da unidade de medida' )
			   ->setAttrib( 'maxlength', 45 )
			   ->setAttrib( 'class', 'input-form' )
			   ->addFilter( 'StringTrim' )
			   ->setRequired( true );
	
	$optStatus[1] = 'Ativo';
	$optStatus[0] = 'Inativo';
	
	$elements[] = $this->createElement( 'FilteringSelect', 'unidade_medida_status' )
                           ->setAttrib( 'readOnly', true )
			   ->setAttrib( 'class', 'input-form' )
			   ->setLabel( 'Status' )
			   ->addMultiOptions( $optStatus );

	$this->addElements( $elements );
    }
}