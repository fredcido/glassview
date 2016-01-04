<?php

class Almoxarifado_Form_Produto extends App_Forms_Default
{
    /**
     * 
     */
    public function init()
    {
        $this->setName( 'form-almoxarifado-produto' );
	
	$elements = array();
        
        $elements[] = $this->createElement( 'hidden', 'produto_id' );

	$elements[] = $this->createElement( 'ValidationTextBox', 'produto_descricao' )
			   ->setLabel( 'Descrição' )
			   ->setDijitParam( 'placeHolder', 'Digite descrição do produto' )
			   ->setAttrib( 'maxlength', 150 )
			   ->setAttrib( 'class', 'input-form' )
			   ->addFilter( 'StringTrim' )
			   ->setRequired( true );

	$dbTipoProduto = new Model_DbTable_TipoProduto();
	$data = $dbTipoProduto->fetchAll( array(), 'tipo_produto_nome' );

	$optTipoProduto[''] = '';
	foreach ( $data as $row )
	    $optTipoProduto[$row->tipo_produto_id] = $row->tipo_produto_nome;

	$elements[] = $this->createElement( 'FilteringSelect', 'tipo_produto_id' )
			   ->setLabel( 'Tipo' )
			   ->addMultiOptions( $optTipoProduto )
			   ->setAttrib( 'class', 'input-form' )
			   ->setDijitParam( 'placeHolder', 'Selecione o tipo do produto' )
			   ->setRequired( true );
        
	$dbUnidadeMedida = new Model_DbTable_UnidadeMedida();
        $data = $dbUnidadeMedida->fetchAll( array( 'unidade_medida_status' => 1 ),
                                            'unidade_medida_nome' );

	$optUnidadeMedida[''] = '';
	foreach ( $data as $row )
	    $optUnidadeMedida[$row->unidade_medida_id] = $row->unidade_medida_nome;

	$elements[] = $this->createElement( 'FilteringSelect', 'unidade_medida_id' )
			   ->setLabel( 'Unidade' )
			   ->addMultiOptions( $optUnidadeMedida )
			   ->setAttrib( 'class', 'input-form' )
			   ->setDijitParam( 'placeHolder', 'Selecione a unidade de medida' )
			   ->setRequired( true );

        $elements[] = $this->createElement( 'CurrencyTextBox', 'produto_valor_unitario' )
			   ->setLabel( 'Valor unitário' )
			   ->setDijitParam( 'placeHolder', 'Informe o valor unitário' )
			   ->setAttrib( 'class', 'input-form' )
			   ->setDijitParam( 'currency', 'R$ ' )
			   ->addFilter( 'StringTrim' )
                           ->setValue(0)
			   ->setRequired( false );
       
	$elements[] = $this->createElement( 'NumberSpinner', 'produto_estoque_max' )
			   ->setLabel( 'Estoque máximo' )
			   ->setDijitParam( 'placeHolder', 'Informe estoque máximo' )
                           ->setAttrib( 'onChange', 'almoxarifadoProduto.setMinMaxEstoque();' )
			   ->setAttrib( 'regExp', '^\d{0,}$' )
			   ->setAttrib( 'class', 'input-form' )
			   ->addFilter( 'StringTrim' )
                           ->setValue(0)
			   ->setRequired( false );

	$elements[] = $this->createElement( 'NumberSpinner', 'produto_estoque_min' )
			   ->setLabel( 'Estoque mínimo' )
			   ->setDijitParam( 'placeHolder', 'Informe estoque mínimo' )
                           ->setAttrib( 'onChange', 'almoxarifadoProduto.setMinMaxEstoque( );' )
                           ->setAttrib( 'regExp', '^\d{0,}$' )
			   ->setAttrib( 'class', 'input-form' )
			   ->addFilter( 'StringTrim' )
                           ->setValue(0)
			   ->setRequired( false );

	$elements[] = $this->createElement( 'CheckBox', 'produto_aviso' )
                           ->setChecked(true)
			   ->setLabel( 'Aviso estoque' );

	$optStatus[1] = 'Ativo';
	$optStatus[0] = 'Inativo';

	$elements[] = $this->createElement( 'FilteringSelect', 'produto_status' )
                           ->setAttrib( 'readOnly', true )
			   ->setAttrib( 'class', 'input-form' )
			   ->setLabel( 'Status' )
			   ->addMultiOptions( $optStatus );
	
	$this->setDefaultDecorator( 'columns' );
        
	$this->addElements( $elements );
    }
}