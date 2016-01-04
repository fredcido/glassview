<?php

class Relatorio_Form_Cheque extends App_Forms_Default
{
    /**
     * 
     */
    public function init()
    {
        $this->setName( 'form-relatorio-cheque' );
	
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

        $dbTerceiro = new Model_DbTable_Terceiro();
	$data = $dbTerceiro->fetchAll( array(), 'terceiro_nome' );

	$optTerceiro = array( '' => '' );
	foreach ( $data as $row )
	    $optTerceiro[$row->terceiro_id] = $row->terceiro_nome;

	$elements[] = $this->createElement( 'FilteringSelect', 'terceiro_id' )
			   ->setLabel( 'Emitido para' )
                           ->setRegisterInArrayValidator(false)
			   ->addMultiOptions( $optTerceiro )
                           ->setAttrib( 'class', 'input-form' )
                           ->setRequired( false );

        $elements[] = $this->createElement( 'DateTextBox', 'rel_data_ini' )
                           ->setLabel(  'Para data inicial' )
			   ->setAttrib( 'onchange', 'relatorioCheque.validaDatas( "rel_data_ini", "rel_data_fim" );')
                           ->setRequired( true );
        
	$elements[] = $this->createElement( 'DateTextBox', 'rel_data_fim' )
                           ->setLabel(  'Para data final' )
			   ->setAttrib( 'onchange', 'relatorioCheque.validaDatas( "rel_data_ini", "rel_data_fim" );')
                           ->setRequired( true );
        
        $optSituacao = array(
            ''  => '',
            'A' => 'A Compensar',
            'D' => 'Depositado',
            'C' => 'Compensado',
            'V' => 'Devolvido',
            'P' => 'Pago',
            'R' => 'Repassado'
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
			'click'  => 'relatorioCheque.visualizar( "' . $this->getId() . '" )'
		    ),
		    array(
			'label'  => 'Gerar PDF',
			'icon'   => 'icon-toolbar-pagewhiteacrobat',
			'click'  => 'relatorioCheque.gerarPdf( "' . $this->getId() . '" )'
		    ),
		    array(
			'label'  => 'Gerar Excel',
			'icon'   => 'icon-toolbar-pageexcel',
			'click'  => 'relatorioCheque.gerarExcell( "' . $this->getId() . '" )'
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