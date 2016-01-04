<?php

class Master_Form_Acao extends App_Forms_Default
{
    /**
     * 
     */
    public function init()
    {
    	$this->setName( 'form-master-acao' );
    	
        $elements[] = $this->createElement( 'hidden', 'acao_id' );
        
        $elements[] = $this->createElement( 'hidden', 'privilegios' )
			   ->addFilters( array( 'StringTrim' ) )
			   ->setIsArray( true );
	
       $elements[] = $this->createElement( 'hidden', 'privilegios_base' )
		       ->addFilters( array( 'StringTrim' ) )
		       ->setIsArray( true );
        	
        $elements[] = $this->createElement( 'ValidationTextBox', 'acao_descricao' )
        	->setLabel( 'Descrição' )
		->setAttrib( 'class', 'input-form' )
        	->setDijitParam( 'placeHolder', 'Digite a descrição da ação' )
        	->setAttrib( 'maxlength', 50 );
        	
        $dbTela = new Model_DbTable_Tela();
        $data = $dbTela->fetchAll( array(), 'tela_nome' );
        
        $opt[''] = '';
        
        foreach ( $data as $row )
        	$opt[$row->tela_id] = $row->tela_nome;
        	
        $elements[] = $this->createElement( 'FilteringSelect', 'tela_id' )
			->setLabel( 'Tela' )
			->addMultiOptions( $opt )
			->setAttrib( 'class', 'input-form' )
			->setDijitParam( 'placeHolder', 'Selecione a tela' )
			->setRequired( true );
			
	$this->addElements( $elements );
	
	$this->addDisplayGroup( 
				array( 'acao_id', 'acao_descricao', 'tela_id' ), 
				'elementos_form', 
				array( 'decorators' => array( 'FormElements' ) )
			      );
	
	$this->_defineDecorators();
    }
}