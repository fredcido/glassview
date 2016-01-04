<?php

class Financeiro_Form_Budget extends App_Forms_Default
{
    /**
     * 
     */
    public function init()
    {
        $this->setName( 'formFinanceiroBudget' );
	
	$elements = array();

        $elements[] = $this->createElement( 'hidden', 'fn_budget_id' );
        
	$elements[] = $this->createElement( 'hidden', 'fn_categoria_id' );

	$dbProjeto = new Model_DbTable_Projeto();
	$data = $dbProjeto->fetchAll( array(), 'projeto_nome' );

	$optProjeto = array( null => '');
	foreach ( $data as $row )
	    $optProjeto[$row->projeto_id] = $row->projeto_nome;

	$elements[] = $this->createElement( 'FilteringSelect', 'projeto_id' )
			   ->addMultiOptions( $optProjeto )
			   ->setLabel( 'Projeto' )
			   ->setAttrib( 'class', 'input-form' )
			   ->setAttrib( 'onChange', 'financeiroBudget.changeProjeto()')
			   ->setDijitParam( 'placeHolder', 'Selecione o Projeto' )
			   ->setRequired( true );
	
	$elements[] = $this->createElement( 'ValidationTextBox', 'fn_categoria_descricao' )
			   ->setLabel( 'Categoria' )
			   ->setAttrib( 'readOnly', 'true' )
			   ->setAttrib( 'style', 'width: 220px' )
			   ->setAttrib( 'onChange', 'financeiroBudget.changeCategoria()')
			   ->setDijitParam( 'placeHolder', 'Selecione a Categoria' )
			   ->setRequired( true );
	
	$elements[] = $this->createElement( 'CurrencyTextBox', 'projeto_orcamento' )
			    ->setLabel( 'Orçamento' )
			    ->setAttrib( 'readOnly', 'true' )
			    ->setAttrib( 'class', 'input-form' )
			    ->setDijitParam( 'currency', 'R$ ' )
                            ->setValue(0)
			    ->setRequired( true );
	
	$elements[] = $this->createElement( 'ValidationTextBox', 'budget_ano' )
			   ->setLabel( 'Ano' )
			   ->setAttrib( 'readOnly', 'true' )
			   ->setAttrib( 'style', 'width: 100px' )
			   ->setRequired( true );
	
	$elements[] = $this->createElement( 'CurrencyTextBox', 'fn_budget_total' )
			    ->setLabel( 'Total Budget' )
			    ->setAttrib( 'readOnly', 'true' )
			    ->setAttrib( 'class', 'input-form' )
			    ->setDijitParam( 'currency', 'R$ ' )
                            ->setValue(0)
			    ->setRequired( true );
	
	$elements[] = $this->createElement( 'CurrencyTextBox', 'fn_budget_total_categoria' )
			    ->setLabel( 'Total Categoria' )
			    ->setAttrib( 'readOnly', 'true' )
			    ->setAttrib( 'class', 'input-form' )
			    ->setDijitParam( 'currency', 'R$ ' )
                            ->setValue(0)
			    ->setRequired( true );

	$this->addElements( $elements );
	
	$this->addDisplayGroup( 
				$elements, 
				'elementos_form', 
				array( 'decorators' => array( 'FormElements' ) )
			      );
	
	$this->setRenderDefaultButtons( false )
	     ->setCustomButtons(
		array(
		    array(
			'label'  => 'Fechar',
			'icon'   => 'dijitEditorIcon dijitEditorIconCancel',
			'click'  => 'objGeral.closeGenericDialog();'
		    )
		)
	     )
	     ->_defineDecorators();
    }
}