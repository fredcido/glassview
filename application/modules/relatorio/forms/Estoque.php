<?php

class Relatorio_Form_Estoque extends App_Forms_Default
{
    /**
     * 
     */
    public function init()
    {
        $this->setName( 'form-relatorio-estoque' );
	
	$elements = array();
	
	$dbProduto = new Model_DbTable_Produto();
	$data = $dbProduto->fetchAll( array( 'produto_status = ?' => 1 ), 'produto_descricao' );

	$optProduto = array('' => '');
	foreach ( $data as $row )
	    $optProduto[$row->produto_id] = $row->produto_descricao;

	$elements[] = $this->createElement( 'FilteringSelect', 'produto_id' )
			   ->setLabel( 'Produto' )
			   ->addMultiOptions( $optProduto )
			   ->setAttrib( 'class', 'input-form' )
			   ->setDijitParam( 'placeHolder', 'Selecione o produto' )
			   ->setRequired( false );
        
        $elements[] = $this->createElement( 'DateTextBox', 'rel_data_ini' )
                           ->setLabel(  'Data inicial' )
                            ->setDijitParam( 'placeHolder', 'Data inicial' )
			   ->setAttrib( 'onchange', 'relatorioEstoque.validaDatas( "rel_data_ini", "rel_data_fim" );')
                           ->setRequired( true );
        
	$elements[] = $this->createElement( 'DateTextBox', 'rel_data_fim' )
                           ->setLabel(  'Data final' )
                           ->setDijitParam( 'placeHolder', 'Data final' )
			   ->setAttrib( 'onchange', 'relatorioEstoque.validaDatas( "rel_data_ini", "rel_data_fim" );')
                           ->setRequired( true );
        	
	$elements[] = $this->createElement( 'FilteringSelect', 'estoque_tipo' )
			   ->setLabel( 'Tipo' )
			   ->addMultiOptions( array( '' => '' ,'E' => 'Entrada' , 'S' => 'Saída') )
			   ->setDijitParam( 'placeHolder', 'Tipo da movimentação' )
			   ->setAttrib( 'class', 'input-form' )
			   ->setRequired( false );
        
        $elements[] = $this->createElement( 'CheckBox', 'estoque_fluxo' )
                ->setLabel( 'Ajuste' );
        
        
	$this->addElements( $elements );
	
	$this->setRenderDefaultButtons( false )
	     ->setCustomButtons(
		array(
		    array(
			'label'  => 'Visualizar',
			'icon'   => 'icon-toolbar-magnifier',
			'click'  => 'relatorioEstoque.visualizar( "' . $this->getId() . '" )'
		    ),
		    array(
			'label'  => 'Gerar PDF',
			'icon'   => 'icon-toolbar-pagewhiteacrobat',
			'click'  => 'relatorioEstoque.gerarPdf( "' . $this->getId() . '" )'
		    ),
		    array(
			'label'  => 'Gerar Excel',
			'icon'   => 'icon-toolbar-pageexcel',
			'click'  => 'relatorioEstoque.gerarExcell( "' . $this->getId() . '" )'
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