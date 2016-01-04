<?php

class Master_Form_Tela extends App_Forms_Default
{
    /**
     * 
     */
    public function init()
    {
        $this->setName( 'form-master-tela' );
	
	$elements = array();
        
        $elements[] = $this->createElement( 'hidden', 'tela_id' );

	$elements[] = $this->createElement( 'ValidationTextBox', 'tela_nome' )
			   ->setLabel( 'Nome' )
			   ->setDijitParam( 'placeHolder', 'Digite o nome da tela' )
			   ->setAttrib( 'maxlength', 100 )
			   ->setAttrib( 'class', 'input-form' )
			   ->addFilter( 'StringTrim' )
			   ->setRequired( true );
	
	$dbModulo = new Model_DbTable_Modulo();
	$data = $dbModulo->fetchAll( array(), 'modulo_nome' );
	
	$opt[''] = '';
	foreach ( $data as $row )
	    $opt[$row->modulo_id] = $row->modulo_nome;
	
	$elements[] = $this->createElement( 'FilteringSelect', 'modulo_id' )
			   ->setLabel( 'Módulo' )
			   ->addMultiOptions( $opt )
			   ->setAttrib( 'class', 'input-form' )
			   ->setDijitParam( 'placeHolder', 'Selecione o módulo' )
			   ->setRequired( true );
	
	$elements[] = $this->createElement( 'ValidationTextBox', 'tela_path' )
			   ->setLabel( 'Caminho' )
			   ->setDijitParam( 'placeHolder', 'Digite o caminho da tela' )
                           ->setDijitParam( 'regExp', '\/[\w-]+\/[\w-]+\/$' )
			   ->setAttrib( 'class', 'input-form' )
			   ->setAttrib( 'maxlength', 100 )
			   ->addFilter( 'StringTrim' )
			   ->setRequired( true );
	
	$optStatus[1] = 'Ativo';
	$optStatus[0] = 'Inativo';
	
	$elements[] = $this->createElement( 'FilteringSelect', 'tela_status' )
			   ->setLabel( 'Status' )
			   ->setAttrib( 'class', 'input-form' )
			   ->addMultiOptions( $optStatus );
	
	$elements[] = $this->createElement( 'SimpleTextarea', 'tela_descricao' )
			   ->setLabel( 'Descrição' )
			   ->setAttrib( 'maxlength', 400 )
			   ->setDijitParam( 'progressObserver', true )
			   ->addFilters( array( 'StringTrim', 'StripTags' ) );
	
	$this->addElements( $elements );
    }
}