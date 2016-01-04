<?php

class Relatorio_Form_Duplicata extends App_Forms_Default
{
    /**
     * 
     */
    public function init()
    {
        $this->setName( 'form-relatorio-duplicata' );
	
	$elements = array();

        $dbConta = new Model_DbTable_Conta();
        $rowsConta = $dbConta->fetchAll();

        $optConta = array('' => '');
        foreach ($rowsConta as $rowConta)
                $optConta[$rowConta->fn_conta_id] = $rowConta->fn_conta_descricao;

        $elements[] = $this->createElement('FilteringSelect', 'fn_conta_id')
                            ->setLabel('Conta')
                            ->setDijitParam( 'placeHolder', 'Selecione Conta' )
                            ->setRequired( false )
                            ->setAttrib('class', 'input-form')
                            ->addMultiOptions($optConta);


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
                           ->setLabel(  'Data inicial' )
			   ->setAttrib( 'onchange', 'relatorioDuplicata.validaDatas( "rel_data_ini", "rel_data_fim" );')
                           ->setRequired( true );
        
	$elements[] = $this->createElement( 'DateTextBox', 'rel_data_fim' )
                           ->setLabel(  'Data final' )
			   ->setAttrib( 'onchange', 'relatorioDuplicata.validaDatas( "rel_data_ini", "rel_data_fim" );')
                           ->setRequired( true );
        
	$elements[] = $this->createElement( 'FilteringSelect', 'fn_duplicata_tipo' )
                           ->setRequired( false )
			   ->setAttrib( 'class', 'input-form' )
			   ->setLabel( 'Tipo' )
			   ->addMultiOptions( array( '' => '',
                                                     'E' => 'Entrada',
                                                     'S' => 'Saída')
                                   );

	$this->addElements( $elements );
	
	$this->setRenderDefaultButtons( false )
	     ->setCustomButtons(
		array(
		    array(
			'label'  => 'Visualizar',
			'icon'   => 'icon-toolbar-magnifier',
			'click'  => 'relatorioDuplicata.visualizar( "' . $this->getId() . '" )'
		    ),
		    array(
			'label'  => 'Gerar PDF',
			'icon'   => 'icon-toolbar-pagewhiteacrobat',
			'click'  => 'relatorioDuplicata.gerarPdf( "' . $this->getId() . '" )'
		    ),
		    array(
			'label'  => 'Gerar Excel',
			'icon'   => 'icon-toolbar-pageexcel',
			'click'  => 'relatorioDuplicata.gerarExcell( "' . $this->getId() . '" )'
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