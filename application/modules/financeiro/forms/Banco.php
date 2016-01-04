<?php

class Financeiro_Form_Banco extends App_Forms_Default
{
    /**
     * 
     */
    public function init()
    {
        $this->setName( 'form-financeiro-banco' );
	
	$elements = array();
        
        $elements[] = $this->createElement( 'hidden', 'fn_banco_id' );


        $elements[] = $this->createElement( 'ValidationTextBox', 'fn_banco_codigo' )
                           ->setLabel(  'Código' )
                           ->setAttrib( 'maxlength', 3 )
                           ->setDijitParam( 'placeHolder', 'Digite o Código do banco' )
                           ->setAttrib( 'class', 'input-form' )
                           ->addFilter( 'StringTrim' )
                           ->setRequired( true );

        
        $elements[] = $this->createElement( 'ValidationTextBox', 'fn_banco_nome' )
                           ->setLabel(  'Nome' )
                           ->setDijitParam( 'placeHolder', 'Digite o nome do banco' )
                           ->setAttrib( 'maxlength', 100 )
                           ->setAttrib( 'class', 'input-form' )
                           ->addFilter( 'StringTrim' )
                           ->setRequired( true );
        

	$this->addElements( $elements );
    }
}