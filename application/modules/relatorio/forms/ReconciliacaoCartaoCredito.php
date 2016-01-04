<?php

/**
 * 
 * @version $Id $
 */
class Relatorio_Form_ReconciliacaoCartaoCredito extends App_Forms_Default 
{
	/**
	 * (non-PHPdoc)
	 * @see Zend_Form::init()
	 */
	public function init()
	{
		$this->setName( 'form-relatorio-reconciliacao-cartao-credito' );
		
		$elements = array();
		
		//Cartao de Credito
		$dbCartaoCredito = App_Model_DbTable_Factory::get( 'CartaoCredito' );
		
		$rows = $dbCartaoCredito->fetchAll();
		
		$optProjeto = array( '' => '' );
		
		foreach ( $rows as $row )
			$optProjeto[$row->fn_cc_id] = $row->fn_cc_descricao;
		
		$elements[] = $this->createElement( 'FilteringSelect', 'fn_cc_id' )
			->setLabel( 'Cartão de Crédito' )
			->setAttrib( 'class', 'input-form' )
			->addMultiOptions( $optProjeto );
		
		//Periodo
		$elements[] = $this->createElement( 'DateTextBox', 'dt_start' )
			->setLabel(  'Data inicial' )
			->setAttrib( 'onChange', 'relatorioReconciliacaoCartaoCredito.setConstraint(this);' )
			->setRequired( true );
		
		$elements[] = $this->createElement( 'DateTextBox', 'dt_end' )
			->setLabel(  'Data final' )
			->setAttrib( 'onChange', 'relatorioReconciliacaoCartaoCredito.setConstraint(this);' )
			->setRequired( true );

		//Situacao
		$elements[] = $this->createElement( 'FilteringSelect', 'faturado' )
			->setLabel( 'Faturado' )
			->setAttrib( 'class', 'input-form' )
			->addMultiOptions( 
				array(
					'' 	=> '',
					'1' => 'Sim', 
					'0' => 'Não'
				) 
			);
		
		$this->addElements( $elements );
		
		$this->setRenderDefaultButtons( false )
			->setCustomButtons(
				array(
					array(
						'label'  => 'Visualizar',
						'icon'   => 'icon-toolbar-magnifier',
						'click'  => 'relatorioReconciliacaoCartaoCredito.visualizar( "' . $this->getId() . '" )'
					),
					array(
						'label'  => 'Gerar PDF',
						'icon'   => 'icon-toolbar-pagewhiteacrobat',
						'click'  => 'relatorioReconciliacaoCartaoCredito.gerarPdf( "' . $this->getId() . '" )'
					),
					array(
						'label'  => 'Gerar Excel',
						'icon'   => 'icon-toolbar-pageexcel',
						'click'  => 'relatorioReconciliacaoCartaoCredito.gerarExcell( "' . $this->getId() . '" )'
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