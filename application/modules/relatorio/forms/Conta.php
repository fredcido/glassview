<?php

class Relatorio_Form_Conta extends App_Forms_Default
{
    /**
     * 
     */
    public function init()
    {
        $this->setName( 'form-relatorio-conta' );
	
	$elements = array();
	
	$dbConta = App_Model_DbTable_Factory::get( 'Conta' );

	$rowsConta = $dbConta->fetchAll( array('fn_conta_status' => 1), 'fn_conta_descricao' );

	$optConta = array( null => '' );

	if ( $rowsConta->count() ) 
		foreach ( $rowsConta as $rowConta )
			$optConta[$rowConta->fn_conta_id] = $rowConta->fn_conta_descricao;

	$elements[] = $this->createElement( 'FilteringSelect', 'fn_conta_id' )
			    ->setLabel( 'Conta' )
			    ->setRequired( false )
			    ->addMultiOptions( $optConta )
			    ->setAttrib( 'class', 'input-form' );
	
	$dbProjeto = new Model_DbTable_Projeto();
	$data = $dbProjeto->fetchAll( array(), 'projeto_nome' );

	$optProjeto = array('' => '' );
	foreach ( $data as $row )
	    $optProjeto[$row->projeto_id] = $row->projeto_nome;

	$elements[] = $this->createElement( 'FilteringSelect', 'projeto_id' )
			   ->setLabel( 'Projeto' )
			   ->setAttrib( 'class', 'input-form' )
			   ->setAttrib( 'onchange', 'relatorioConta.buscaTiposLancamento();')
			   ->addMultiOptions( $optProjeto );
	
	$elements[] = $this->createElement( 'FilteringSelect', 'fn_tipo_lanc_id' )
			   ->setLabel( 'Tipo de lançamento' )
			   ->setAttrib( 'disabled', 'true' )
			   ->setAttrib( 'class', 'input-form' );
        
        $elements[] = $this->createElement( 'DateTextBox', 'rel_data_ini' )
                           ->setLabel(  'Data inicial' )
                           ->setValue( Zend_Date::now()->toString('yyyy-MM').'-01' )
			   ->setAttrib( 'onchange', 'relatorioConta.validaDatas( "rel_data_ini", "rel_data_fim" );')
                           ->setRequired( true );
        
	$elements[] = $this->createElement( 'DateTextBox', 'rel_data_fim' )
                           ->setLabel(  'Data final' )
                           ->setValue( Zend_Date::now()->toString('yyyy-MM-dd') )
			   ->setAttrib( 'onchange', 'relatorioConta.validaDatas( "rel_data_ini", "rel_data_fim" );')
                           ->setRequired( true );
	
	$this->addElements( $elements );
	
	$this->setRenderDefaultButtons( false )
	     ->setCustomButtons(
		array(
		    array(
			'label'  => 'Visualizar',
			'icon'   => 'icon-toolbar-magnifier',
			'click'  => 'relatorioConta.visualizar( "' . $this->getId() . '" )'
		    ),
		    array(
			'label'  => 'Gerar PDF',
			'icon'   => 'icon-toolbar-pagewhiteacrobat',
			'click'  => 'relatorioConta.gerarPdf( "' . $this->getId() . '" )'
		    ),
		    array(
			'label'  => 'Gerar Excel',
			'icon'   => 'icon-toolbar-pageexcel',
			'click'  => 'relatorioConta.gerarExcell( "' . $this->getId() . '" )'
		    ),
		    array(
			'label'  => 'Fechar',
			'icon'   => 'dijitEditorIcon dijitEditorIconCancel',
			'click'  => 'objGeral.closeGenericDialog();'
		    )
		)
	     );
    }
}