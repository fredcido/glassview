<?php

class Relatorio_Form_Transferencia extends App_Forms_Default
{
    /**
     * 
     */
    public function init()
    {
        $this->setName( 'form-relatorio-transferencia' );
	
	$elements = array();
	
	$dbConta = App_Model_DbTable_Factory::get( 'Conta' );

	$rowsConta = $dbConta->fetchAll( array('fn_conta_status' => 1), 'fn_conta_descricao' );

	$optConta = array( null => '' );

	if ( $rowsConta->count() ) 
		foreach ( $rowsConta as $rowConta )
			$optConta[$rowConta->fn_conta_id] = $rowConta->fn_conta_descricao;

	$elements[] = $this->createElement( 'FilteringSelect', 'fn_conta_id_debito' )
			    ->setLabel( 'Conta Débito' )
			    ->setRequired( false )
			    ->addMultiOptions( $optConta )
			    ->setAttrib( 'class', 'input-form' );
	
	$elements[] = $this->createElement( 'FilteringSelect', 'fn_conta_id_credito' )
			    ->setLabel( 'Conta Crédito' )
			    ->setRequired( false )
			    ->addMultiOptions( $optConta )
			    ->setAttrib( 'class', 'input-form' );
        
        $elements[] = $this->createElement( 'DateTextBox', 'rel_data_ini' )
                           ->setLabel(  'Data inicial' )
                           ->setValue( Zend_Date::now()->toString('yyyy-MM').'-01' )
			   ->setAttrib( 'onchange', 'relatorioTransferencia.validaDatas( "rel_data_ini", "rel_data_fim" );')
                           ->setRequired( true );
        
	$elements[] = $this->createElement( 'DateTextBox', 'rel_data_fim' )
                           ->setLabel(  'Data final' )
                           ->setValue( Zend_Date::now()->toString('yyyy-MM-dd') )
			   ->setAttrib( 'onchange', 'relatorioTransferencia.validaDatas( "rel_data_ini", "rel_data_fim" );')
                           ->setRequired( true );
	
	$this->addElements( $elements );
	
	$this->setRenderDefaultButtons( false )
	     ->setCustomButtons(
		array(
		    array(
			'label'  => 'Visualizar',
			'icon'   => 'icon-toolbar-magnifier',
			'click'  => 'relatorioTransferencia.visualizar( "' . $this->getId() . '" )'
		    ),
		    array(
			'label'  => 'Gerar PDF',
			'icon'   => 'icon-toolbar-pagewhiteacrobat',
			'click'  => 'relatorioTransferencia.gerarPdf( "' . $this->getId() . '" )'
		    ),
		    array(
			'label'  => 'Gerar Excel',
			'icon'   => 'icon-toolbar-pageexcel',
			'click'  => 'relatorioTransferencia.gerarExcell( "' . $this->getId() . '" )'
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