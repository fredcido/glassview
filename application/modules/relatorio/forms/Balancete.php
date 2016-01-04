<?php

class Relatorio_Form_Balancete extends App_Forms_Default
{
    /**
     * 
     */
    public function init()
    {
        $this->setName( 'form-relatorio-balancete' );
	
	$elements = array();
	        
        $elements[] = $this->createElement( 'DateTextBox', 'rel_data_ini' )
                           ->setLabel(  'Data inicial' )
			   ->setAttrib( 'onchange', 'relatorioBalancete.validaDatas( "rel_data_ini", "rel_data_fim" );')
                           ->setRequired( true );
        
	$elements[] = $this->createElement( 'DateTextBox', 'rel_data_fim' )
                           ->setLabel(  'Data final' )
			   ->setAttrib( 'onchange', 'relatorioBalancete.validaDatas( "rel_data_ini", "rel_data_fim" );')
                           ->setRequired( true );
	
	$this->addElements( $elements );
	
	$this->setRenderDefaultButtons( false )
	     ->setCustomButtons(
		array(
		    array(
			'label'  => 'Visualizar',
			'icon'   => 'icon-toolbar-magnifier',
			'click'  => 'relatorioBalancete.visualizar( "' . $this->getId() . '" )'
		    ),
		    array(
			'label'  => 'Gerar PDF',
			'icon'   => 'icon-toolbar-pagewhiteacrobat',
			'click'  => 'relatorioBalancete.gerarPdf( "' . $this->getId() . '" )'
		    ),
		    array(
			'label'  => 'Gerar Excel',
			'icon'   => 'icon-toolbar-pageexcel',
			'click'  => 'relatorioBalancete.gerarExcell( "' . $this->getId() . '" )'
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