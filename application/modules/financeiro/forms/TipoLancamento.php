<?php

class Financeiro_Form_TipoLancamento extends App_Forms_Default
{
    /**
     * 
     */
    public function init()
    {
        $this->setName( 'form-financeiro-tipo-lancamento' );
	
	$elements = array();
        
        $elements[] = $this->createElement( 'hidden', 'fn_tipo_lanc_id' );
	
	$dbProjeto = new Model_DbTable_Projeto();
	$data = $dbProjeto->fetchAll( array(), 'projeto_nome' );

	$optProjeto = array('' => '' );
	foreach ( $data as $row )
	    $optProjeto[$row->projeto_id] = $row->projeto_nome;

	$elements[] = $this->createElement( 'FilteringSelect', 'projeto_id' )
			   ->setLabel( 'Projeto' )
                           ->setAttrib( 'onChange', 'financeiroTipoLancamento.buscaCategoriasProjeto();' )
                           ->setRequired( true )
			   ->setAttrib( 'class', 'input-form' )
			   ->addMultiOptions( $optProjeto );

	$elements[] = $this->createElement( 'FilteringSelect', 'fn_categoria_id' )
			   ->setLabel( 'Categoria' )
                           ->setRegisterInArrayValidator(false)
                           ->setRequired( true )
			   ->setAttrib( 'class', 'input-form' )
			   ->setAttrib( 'disabled', true );
	
	$elements[] = $this->createElement( 'ValidationTextBox', 'fn_tipo_lanc_desc' )
			   ->setLabel( 'Descrição' )
			   ->setDijitParam( 'placeHolder', 'Digite descrição do tipo de lançamento' )
			   ->setAttrib( 'maxlength', 70 )
			   ->setAttrib( 'class', 'input-form' )
			   ->addFilter( 'StringTrim' )
			   ->setRequired( true );
        
	$elements[] = $this->createElement( 'FilteringSelect', 'fn_tipo_lanc_agrupador' )
			   ->setAttrib( 'class', 'input-form' )
			   ->setLabel( 'Agrupador' )
                           ->setAttrib( 'onChange','financeiroTipoLancamento.setReadOnly()' )
			   ->addMultiOptions( array( '' => '' , 0 => 'Não' , 1 => 'Sim') )
                           ->setRequired( true );
        
	$elements[] = $this->createElement( 'ValidationTextBox', 'fn_tipo_lanc_cod' )
			   ->setLabel( 'Código' )
			   ->setDijitParam( 'placeHolder', 'Digite código do tipo de lançamento' )
			   ->setAttrib( 'maxlength', 30 )
			   ->setAttrib( 'class', 'input-form' )
			   ->addFilter( 'StringTrim' )
                           ->setAttrib( 'readOnly', true );
	
	$elements[] = $this->createElement( 'FilteringSelect', 'fn_tipo_lanc_status' )
                           ->setAttrib( 'readOnly', true )
			   ->setAttrib( 'class', 'input-form' )
			   ->setLabel( 'Status' )
			   ->addMultiOptions( array( 1 => 'Ativo', 0 => 'Inativo') );

	$this->addElements( $elements );
    }
}