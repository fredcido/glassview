<?php

class Financeiro_Form_Categoria extends App_Forms_Default
{
    /**
     * 
     */
    public function init()
    {
        $this->setName( 'form-financeiro-categoria' );
	
	$elements = array();
        
        $elements[] = $this->createElement( 'hidden', 'fn_categoria_id' );

	$elements[] = $this->createElement( 'ValidationTextBox', 'fn_categoria_descricao' )
			   ->setLabel( 'Descrição' )
			   ->setDijitParam( 'placeHolder', 'Digite descrição da categoria' )
			   ->setAttrib( 'maxlength', 50 )
			   ->setAttrib( 'class', 'input-form' )
			   ->addFilter( 'StringTrim' )
			   ->setRequired( true );
        
	$dbProjeto = new Model_DbTable_Projeto();
	$data = $dbProjeto->fetchAll( array(), 'projeto_nome' );

	$optProjeto = array( '' => '' );
	foreach ( $data as $row )
	    $optProjeto[$row->projeto_id] = $row->projeto_nome;

	$elements[] = $this->createElement( 'FilteringSelect', 'projeto_id' )
			   ->setLabel( 'Projeto' )
			   ->setAttrib( 'class', 'input-form' )
			   ->addMultiOptions( $optProjeto );
	
	$optAgrupador[0] = 'Não';
	$optAgrupador[1] = 'Sim';
	
	$elements[] = $this->createElement( 'FilteringSelect', 'fn_categoria_agrupador' )
			   ->setAttrib( 'class', 'input-form' )
			   ->setLabel( 'Agrupador' )
			   ->addMultiOptions( $optAgrupador );

	$optStatus[1] = 'Ativo';
	$optStatus[0] = 'Inativo';
	
	$elements[] = $this->createElement( 'FilteringSelect', 'fn_categoria_status' )
                           ->setAttrib( 'readOnly', true )
			   ->setAttrib( 'class', 'input-form' )
			   ->setLabel( 'Status' )
			   ->addMultiOptions( $optStatus );

	$this->addElements( $elements );
    }
}