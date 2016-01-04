<?php

class Relatorio_Form_LancamentoCartao extends App_Forms_Default
{
    /**
     * 
     */
    public function init()
    {
        $this->setName( 'form-relatorio-lancamento-cartao' );
	
	$elements = array();
	
	$dbCartaoCredito = new Model_DbTable_CartaoCredito();
	$data = $dbCartaoCredito->fetchAll( array( 'fn_cc_status = ?' => 'A' ), 'fn_cc_descricao' );

	$optCartaoCredito = array( null => '');
	foreach ( $data as $row )
	    $optCartaoCredito[$row->fn_cc_id] = $row->fn_cc_descricao;

	$elements[] = $this->createElement( 'FilteringSelect', 'fn_cc_id' )
			   ->setLabel( 'Cartão' )
			   ->addMultiOptions( $optCartaoCredito )
			   ->setAttrib( 'class', 'input-form' )
			   ->setDijitParam( 'placeHolder', 'Selecione o Cartão de Credito' )
			   ->setRequired( false );
	
	$optSituacao = array(
            '' => '',
            'P' => 'Pendente',
            'F' => 'Faturado'
        );

	$elements[] = $this->createElement( 'FilteringSelect', 'fn_lanc_cartao_status' )
			   ->setLabel( 'Situação' )
			   ->setRequired( false )
			   ->setAttrib( 'class', 'input-form' )
			   ->addMultiOptions( $optSituacao );

        $elements[] = $this->createElement( 'DateTextBox', 'rel_data_ini' )
                           ->setLabel(  'Data inicial' )
			   ->setAttrib( 'onchange', 'relatorioLancamentoCartao.validaDatas( "rel_data_ini", "rel_data_fim" );')
                           ->setRequired( true );
        
	$elements[] = $this->createElement( 'DateTextBox', 'rel_data_fim' )
                           ->setLabel(  'Data final' )
			   ->setAttrib( 'onchange', 'relatorioLancamentoCartao.validaDatas( "rel_data_ini", "rel_data_fim" );')
                           ->setRequired( true );
	
	$this->addElements( $elements );
	
	$this->setRenderDefaultButtons( false )
	     ->setCustomButtons(
		array(
		    array(
			'label'  => 'Visualizar',
			'icon'   => 'icon-toolbar-magnifier',
			'click'  => 'relatorioLancamentoCartao.visualizar( "' . $this->getId() . '" )'
		    ),
		    array(
			'label'  => 'Gerar PDF',
			'icon'   => 'icon-toolbar-pagewhiteacrobat',
			'click'  => 'relatorioLancamentoCartao.gerarPdf( "' . $this->getId() . '" )'
		    ),
		    array(
			'label'  => 'Gerar Excel',
			'icon'   => 'icon-toolbar-pageexcel',
			'click'  => 'relatorioLancamentoCartao.gerarExcell( "' . $this->getId() . '" )'
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