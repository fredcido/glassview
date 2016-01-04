<?php

class Master_Form_Configuracao extends App_Forms_Default
{
    /**
     * 
     */
    public function init()
    {
        $this->setName( 'form-master-configuracao' );
        
        $config = Zend_Registry::get( 'config' );
        
        $configs = $config->toArray();
        
        $elements = array();
        
        foreach ( $configs as $key => $conf ) {
	    
	    $subElements = array();
	    
            foreach ( $conf as $chave => $item ) {
                
                $element = $this->createElement( 'ValidationTextBox', $chave )
                                    ->setLabel( ucfirst( $chave ) )
                                    ->setBelongsTo( $key )
                                    ->setAttrib( 'maxlength', 200 )
                                    ->setAttrib( 'class', 'input-form' )
                                    ->addFilter( 'StringTrim' )
                                    ->setRequired( true );
		
		$subElements[] = $element;
            }
	    
	    $this->addDisplayGroup( $subElements, $key, array(
		'decorators' => array(
		    'FormElements',
		    'Fieldset'
		),
		'legend' => ucfirst( $key )
	    ) );
        }
	
	//$this->addElements( $elements );
        
        $this->setDefaultDecorator( 'columns' );
    }
}