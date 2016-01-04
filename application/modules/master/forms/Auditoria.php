<?php

class Master_Form_Auditoria extends App_Forms_Default
{
    /**
     * 
     */
    public function init()
    {
	$elements[] = $this->createElement( 'ValidationTextBox', 'auditoria_data' )
			   ->setLabel( 'Data' )
			   ->setAttrib( 'class', 'input-form' )
			   ->setAttrib( 'readOnly', 'true' );
	
	$elements[] = $this->createElement( 'ValidationTextBox', 'auditoria_path' )
			   ->setLabel( 'Caminho' )
			   ->setAttrib( 'class', 'input-form' )
			   ->setAttrib( 'readOnly', 'true' );
	
	$elements[] = $this->createElement( 'ValidationTextBox', 'usuario' )
			   ->setLabel( 'Usuário' )
			   ->setAttrib( 'class', 'input-form' )
			   ->setAttrib( 'readOnly', 'true' );
	
	$elements[] = $this->createElement( 'ValidationTextBox', 'auditoria_ip' )
			   ->setLabel( 'Endereço IP' )
			   ->setAttrib( 'class', 'input-form' )
			   ->setAttrib( 'readOnly', 'true' );
	
	$elements[] = $this->createElement( 'SimpleTextarea', 'auditoria_sql' )
			   ->setLabel( 'Query' )
			   ->setAttrib( 'style', 'width: 280px; height: 100px' )
			   ->setAttrib( 'readOnly', 'true' );
	
	$elements[] = $this->createElement( 'SimpleTextarea', 'auditoria_params' )
			   ->setLabel( 'Params' )
			   ->setAttrib( 'style', 'width: 280px; height: 100px' )
			   ->setAttrib( 'readOnly', 'true' );
	
	$this->addElements( $elements );
    }
}