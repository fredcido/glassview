<?php

class Financeiro_Form_OrganizarCategoria extends App_Forms_Default
{
    /**
     * 
     */
    public function init()
    {
        $this->setName( 'form-financeiro-organizar-categoria' );
	
	$elements = array();

	$dbProjeto = new Model_DbTable_Projeto();
	$data = $dbProjeto->fetchAll( array(), 'projeto_nome' );

	$optProjeto = array('' => '' );
	foreach ( $data as $row )
	    $optProjeto[$row->projeto_id] = $row->projeto_nome;

	$elements[] = $this->createElement( 'FilteringSelect', 'projeto_id' )
			   ->setLabel( 'Projeto' )
                           ->setAttrib( 'onChange', 'financeiroCategoria.carregarCategoriasProjeto();' )
                           ->setRequired( true )
			   ->setAttrib( 'class', 'input-form' )
			   ->addMultiOptions( $optProjeto );
	
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