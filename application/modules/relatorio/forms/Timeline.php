<?php

/**
 * 
 * @version $Id $
 */
class Relatorio_Form_Timeline extends App_Forms_Default
{
	public function init()
	{
		$this->setName( 'form-gestao-timeline-lancamento' );
		$this->setAttrib( 'id', 'form-gestao-timeline-lancamento' );
		
		$elements = array();
		
		$dbDadosFuncionario = new Model_Mapper_DadosFuncionario();
		$optFuncionario     = $dbDadosFuncionario->getFuncionarioTimeline();
		 
		//Funcionario
		$elements[] = $this->createElement( 'FilteringSelect', 'funcionario_id' )
			->setLabel( 'Funcionário' )
			->setAttrib( 'class', 'input-form' )
			->addMultiOptions( $optFuncionario )
			->setRequired( true );
		
		$elements[] = $this->createElement( 'DateTextBox', 'dt_inicio' )
			->setLabel( 'Data Início' )
			->setRequired( true )
			->setAttrib( 'style', 'width:100px;' )
			->setAttrib( 'class', 'input-form' )
                        ->setValue( Zend_Date::now()->toString('yyyy-MM').'-01' )
			->setAttrib( 'onChange', 'relatorioTimeline.setConstraint(this)' )
			->addFilter( 'StringTrim' );
		 
		$elements[] = $this->createElement( 'DateTextBox', 'dt_fim' )
			->setLabel( 'Data Final' )
			->setRequired( true )
			->setAttrib( 'style', 'width:100px;' )
			->setAttrib( 'class', 'input-form' )
                        ->setValue( Zend_Date::now()->toString('yyyy-MM-dd') )
			->setAttrib( 'onChange', 'relatorioTimeline.setConstraint(this)' )
			->addFilter( 'StringTrim' );
		
		$this->setRenderDefaultButtons( false )
			->setCustomButtons(
				array(
					array(
						'label'  => 'Visualizar',
						'icon'   => 'icon-toolbar-magnifier',
						'click'  => 'relatorioTimeline.visualizar( "' . $this->getId() . '" )'
					),
					array(
						'label'  => 'Gerar PDF',
						'icon'   => 'icon-toolbar-pagewhiteacrobat',
						'click'  => 'relatorioTimeline.gerarPdf( "' . $this->getId() . '" )'
					),
					array(
						'label'  => 'Gerar Excel',
						'icon'   => 'icon-toolbar-pageexcel',
						'click'  => 'relatorioTimeline.gerarExcell( "' . $this->getId() . '" )'
					),
					array(
						'label'  => 'Fechar',
						'icon'   => 'dijitEditorIcon dijitEditorIconCancel',
						'click'  => 'objGeral.closeGenericDialog();'
					)
				)
		);
		
		$this->addElements( $elements );
	}
}