<?php

class Financeiro_Form_OrganizarTipoLancamento extends App_Forms_Default
{
    /**
     * 
     */
    public function init()
    {
        $this->setName( 'form-financeiro-organizar-tipo-lancamento' );
	
	$elements = array();

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
                           ->setAttrib( 'onChange', 'financeiroTipoLancamento.carregarTiposLancamento();' )
                           ->setRequired( true )
			   ->setAttrib( 'class', 'input-form' )
			   ->setAttrib( 'disabled', true );
	
	$this->addElements( $elements );
	
	$this->setRenderDefaultButtons( false )
	     ->setCustomButtons( 
		array( 
		     array(
			'label'  => 'Cancelar',
			'icon'   => 'dijitEditorIcon dijitEditorIconCancel',
			'click'  => 'objGeral.closeGenericDialog();'
		    )
		)
	     )
	     ->_defineDecorators();
    }
}