<?php

class Relatorio_Form_ReconciliacaoBancaria extends App_Forms_Default
{
    /**
     * 
     */
    public function init()
    {
        $this->setName( 'form-relatorio-reconciliacao-bancaria' );
	
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

       

        $elements[] = $this->createElement( 'DateTextBox', 'rel_data_ini' )
                           ->setLabel(  'Data inicial' )
			   ->setAttrib( 'onchange', 'relatorioCheque.validaDatas( "rel_data_ini", "rel_data_fim" );')
                           ->setRequired( true );
        
	$elements[] = $this->createElement( 'DateTextBox', 'rel_data_fim' )
                           ->setLabel(  'Data final' )
			   ->setAttrib( 'onchange', 'relatorioCheque.validaDatas( "rel_data_ini", "rel_data_fim" );')
                           ->setRequired( true );
        
        $optSituacao = array(
            'T' => 'Efetivada + Não efetivada',
            'E' => 'Efetivada',
            'N' => 'Não Efetivada'
        );

	$elements[] = $this->createElement( 'FilteringSelect', 'fn_cheque_situacao' )
			   ->setLabel( 'Situação' )
			   ->setRequired( false )
			   ->setAttrib( 'class', 'input-form' )
			   ->addMultiOptions( $optSituacao );

	$this->addElements( $elements );
	
	$this->setRenderDefaultButtons( false )
	     ->setCustomButtons(
		array(
		    array(
			'label'  => 'Visualizar',
			'icon'   => 'icon-toolbar-magnifier',
			'click'  => 'relatorioReconciliacaoBancaria.visualizar( "' . $this->getId() . '" )'
		    ),
		    array(
			'label'  => 'Gerar PDF',
			'icon'   => 'icon-toolbar-pagewhiteacrobat',
			'click'  => 'relatorioReconciliacaoBancaria.gerarPdf( "' . $this->getId() . '" )'
		    ),
		    array(
			'label'  => 'Gerar Excel',
			'icon'   => 'icon-toolbar-pageexcel',
			'click'  => 'relatorioReconciliacaoBancaria.gerarExcell( "' . $this->getId() . '" )'
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