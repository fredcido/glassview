<?php

class Relatorio_Form_Ativo extends App_Forms_Default
{
    /**
     * 
     */
    public function init()
    {
        $this->setName( 'form-relatorio-ativo' );
	
	$elements = array();
	
	$dbFilial = App_Model_DbTable_Factory::get( 'Filial' );
	$rowFiliais = $dbFilial->fetchAll( array( 'filial_status' => 1 ), 'filial_nome' );
	$optFilial = array( null => '' );

	if ( $rowFiliais->count() ) 
	    foreach ( $rowFiliais as $rowFilial )
		$optFilial[$rowFilial->filial_id] = $rowFilial->filial_nome;

	$elements[] = $this->createElement( 'FilteringSelect', 'filial_id' )
			    ->setLabel( 'Filial' )
			    ->addMultiOptions( $optFilial )
			    ->setAttrib( 'class', 'input-form' );
	
	$dbSituacaoAtivo = App_Model_DbTable_Factory::get( 'SituacaoAtivo' );
	$rowSituacaoAtivo = $dbSituacaoAtivo->fetchAll( array(), 'situacao_ativo_nome' );
	$optSituacaoAtivo = array( null => '' );

	if ( $rowSituacaoAtivo->count() ) 
	    foreach ( $rowSituacaoAtivo as $row )
		$optSituacaoAtivo[$row->situacao_ativo_id] = $row->situacao_ativo_nome;

	$elements[] = $this->createElement( 'FilteringSelect', 'situacao_ativo_id' )
			    ->setLabel( 'Situação' )
			    ->addMultiOptions( $optSituacaoAtivo )
			    ->setAttrib( 'class', 'input-form' );
	
	$dbTipoAtivo = App_Model_DbTable_Factory::get( 'TipoAtivo' );
	$rowTipoAtivo = $dbTipoAtivo->fetchAll( array(), 'tipo_ativo_nome' );
	$optTipoAtivo = array( null => '' );

	if ( $rowTipoAtivo->count() ) 
	    foreach ( $rowTipoAtivo as $row )
		$optTipoAtivo[$row->tipo_ativo_id] = $row->tipo_ativo_nome;

	$elements[] = $this->createElement( 'FilteringSelect', 'tipo_ativo_id' )
			    ->setLabel( 'Tipo de ativo' )
			    ->addMultiOptions( $optTipoAtivo )
			    ->setAttrib( 'class', 'input-form' );
	
	$optStatus[''] = '';
	$optStatus['0'] = 'Inativo';
	$optStatus['1'] = 'Ativo';
	
	$elements[] = $this->createElement( 'FilteringSelect', 'ativo_status' )
			    ->setLabel( 'Status' )
			    ->addMultiOptions( $optStatus )
			    ->setAttrib( 'class', 'input-form' );
	
        $elements[] = $this->createElement( 'DateTextBox', 'rel_data_ini' )
                           ->setLabel(  'Data inicial' )
			   ->setAttrib( 'onchange', 'relatorioAtivo.validaDatas( "rel_data_ini", "rel_data_fim" );')
                           ->setRequired( true );
        
	$elements[] = $this->createElement( 'DateTextBox', 'rel_data_fim' )
                           ->setLabel(  'Data final' )
			   ->setAttrib( 'onchange', 'relatorioAtivo.validaDatas( "rel_data_ini", "rel_data_fim" );')
                           ->setRequired( true );
	
	$this->addElements( $elements );
	
	$this->setRenderDefaultButtons( false )
	     ->setCustomButtons(
		array(
		    array(
			'label'  => 'Visualizar',
			'icon'   => 'icon-toolbar-magnifier',
			'click'  => 'relatorioAtivo.visualizar( "' . $this->getId() . '" )'
		    ),
		    array(
			'label'  => 'Gerar PDF',
			'icon'   => 'icon-toolbar-pagewhiteacrobat',
			'click'  => 'relatorioAtivo.gerarPdf( "' . $this->getId() . '" )'
		    ),
		    array(
			'label'  => 'Gerar Excel',
			'icon'   => 'icon-toolbar-pageexcel',
			'click'  => 'relatorioAtivo.gerarExcell( "' . $this->getId() . '" )'
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