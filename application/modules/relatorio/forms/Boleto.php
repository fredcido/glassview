<?php

class Relatorio_Form_Boleto extends App_Forms_Default
{
    /**
     * 
     */
    public function init()
    {
        $this->setName( 'form-relatorio-boleto' );
	
	$elements = array();
	
        $dbTerceiro = new Model_DbTable_Terceiro();
	$data = $dbTerceiro->fetchAll( array(), 'terceiro_nome' );

	$optTerceiro = array( '' => '' );
	foreach ( $data as $row )
	    $optTerceiro[$row->terceiro_id] = $row->terceiro_nome;

	$elements[] = $this->createElement( 'FilteringSelect', 'terceiro_id' )
			   ->setLabel( 'Terceiro' )
                           ->setRegisterInArrayValidator(false)
			   ->addMultiOptions( $optTerceiro )
                           ->setAttrib( 'class', 'input-form' )
                           ->setRequired( false );

        $elements[] = $this->createElement( 'DateTextBox', 'rel_data_ini' )
                           ->setLabel(  'Para data inicial' )
			   ->setAttrib( 'onchange', 'relatorioBoleto.validaDatas( "rel_data_ini", "rel_data_fim" );')
                           ->setRequired( true );
        
	$elements[] = $this->createElement( 'DateTextBox', 'rel_data_fim' )
                           ->setLabel(  'Para data final' )
			   ->setAttrib( 'onchange', 'relatorioBoleto.validaDatas( "rel_data_ini", "rel_data_fim" );')
                           ->setRequired( true );
        
	$elements[] = $this->createElement( 'FilteringSelect', 'fn_lancamento_efetivado' )
                           ->setRequired( false )
			   ->setAttrib( 'class', 'input-form' )
			   ->setLabel( 'Pago' )
			   ->addMultiOptions( array( '' => '',
                                                     'S' => 'Sim',
                                                     'N' => 'Não')
                                   );
        
	$this->addElements( $elements );
	
	$this->setRenderDefaultButtons( false )
	     ->setCustomButtons(
		array(
		    array(
			'label'  => 'Visualizar',
			'icon'   => 'icon-toolbar-magnifier',
			'click'  => 'relatorioBoleto.visualizar( "' . $this->getId() . '" )'
		    ),
		    array(
			'label'  => 'Gerar PDF',
			'icon'   => 'icon-toolbar-pagewhiteacrobat',
			'click'  => 'relatorioBoleto.gerarPdf( "' . $this->getId() . '" )'
		    ),
		    array(
			'label'  => 'Gerar Excel',
			'icon'   => 'icon-toolbar-pageexcel',
			'click'  => 'relatorioBoleto.gerarExcell( "' . $this->getId() . '" )'
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