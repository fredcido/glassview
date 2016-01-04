<?php

/**
 * 
 * @version $Id $
 */
class Relatorio_Form_Projeto extends App_Forms_Default
{
	/**
	 * (non-PHPdoc)
	 * @see Zend_Form::init()
	 */
	public function init()
	{
		$this->setName( 'form-relatorio-projeto' );
		
		$elements = array();
		
		//Projeto
		$dbProjeto = App_Model_DbTable_Factory::get( 'Projeto' );
		
		$rows = $dbProjeto->fetchAll( array(), 'projeto_nome' );

		$optProjeto = array( '' => '' );

		foreach ( $rows as $row ) 
			$optProjeto[$row->projeto_id] = $row->projeto_nome;

		$elements[] = $this->createElement( 'FilteringSelect', 'projeto_id' )
			->setLabel( 'Projeto' )
			->setAttrib( 'class', 'input-form' )
			->addMultiOptions( $optProjeto );
		
		//Funcionario
		$mapper = new Model_Mapper_Funcionario();
        $rows = $mapper->listAll();
		
		$optFuncionario = array( '' => '' );
		
		foreach ( $rows as $row )
			$optFuncionario[$row->funcionario_id] = $row->funcionario_nome;
		
		$elements[] = $this->createElement( 'FilteringSelect', 'funcionario_id' )
			->setLabel( 'Funcionario')
			->setAttrib( 'class', 'input-form' )
			->addMultiOptions( $optFuncionario );
		
		//Atividade
		$dbAtividade = App_Model_DbTable_Factory::get( 'Atividade' );
		
		$rows = $dbAtividade->fetchAll( array(), 'atividade_nome' );
		
		$optAtividade = array( '' => '' );
		
		foreach ( $rows as $row )
			$optAtividade[$row->atividade_id] = $row->atividade_nome;
		
		$elements[] = $this->createElement( 'FilteringSelect', 'atividade_id')
			->setLabel( 'Atividade' )
			->setAttrib( 'class', 'input-form' )
			->addMultiOptions( $optAtividade );
		
		//Periodo
		$elements[] = $this->createElement( 'DateTextBox', 'dt_start' )
			->setLabel(  'Data inicial' )
                        ->setValue( Zend_Date::now()->toString('yyyy-MM').'-01' )
			->setAttrib( 'onChange', 'relatorioProjeto.setConstraint(this);' )
			->setRequired( true );
		
		$elements[] = $this->createElement( 'DateTextBox', 'dt_end' )
			->setLabel(  'Data final' )
                        ->setValue( Zend_Date::now()->toString('yyyy-MM-dd') )
			->setAttrib( 'onChange', 'relatorioProjeto.setConstraint(this);' )
			->setRequired( true );
		
		$this->addElements( $elements );
		
		$this->setRenderDefaultButtons( false )
			->setCustomButtons(
				array(
					array(
						'label'  => 'Visualizar',
						'icon'   => 'icon-toolbar-magnifier',
						'click'  => 'relatorioProjeto.visualizar( "' . $this->getId() . '" )'
					),
					array(
						'label'  => 'Gerar PDF',
						'icon'   => 'icon-toolbar-pagewhiteacrobat',
						'click'  => 'relatorioProjeto.gerarPdf( "' . $this->getId() . '" )'
					),
					array(
						'label'  => 'Gerar Excel',
						'icon'   => 'icon-toolbar-pageexcel',
						'click'  => 'relatorioProjeto.gerarExcell( "' . $this->getId() . '" )'
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