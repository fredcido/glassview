<?php

class Almoxarifado_Form_Estoque extends App_Forms_Default
{
    /**
     * 
     */
    public function init()
    {
        $this->setName( 'formAlmoxarifadoEstoque' );
	
	$elementsProd = array();
	$elementsMov = array();
	
	$dbProduto = new Model_DbTable_Produto();
	$data = $dbProduto->fetchAll( array( 'produto_status = ?' => 1 ), 'produto_descricao' );

	$optProduto[''] = '';
	foreach ( $data as $row )
	    $optProduto[$row->produto_id] = $row->produto_descricao;
	
	$elementsProd[] = $this->createElement( 'hidden', 'estoque_anterior' );

	$elementsProd[] = $this->createElement( 'FilteringSelect', 'produto_id' )
			   ->setLabel( 'Produto' )
			   ->addMultiOptions( $optProduto )
			   ->setAttrib( 'class', 'input-form' )
			   ->setAttrib( 'onChange', 'almoxarifadoEstoque.buscaProduto();' )
			   ->setDijitParam( 'placeHolder', 'Selecione o produto' )
			   ->setRequired( true );
	
	$elementsProd[] = $this->createElement( 'ValidationTextBox', 'estoque_qtde_anterior' )
			   ->setLabel( 'Estoque atual' )
			   ->setAttrib( 'readOnly', 'true' )
			   ->setAttrib( 'class', 'input-form' );
	
	$elementsProd[] = $this->createElement( 'ValidationTextBox', 'produto_estoque_min' )
			   ->setLabel( 'Estoque mínimo' )
			   ->setAttrib( 'readOnly', 'true' )
			   ->setAttrib( 'class', 'input-form' );
	
	$elementsProd[] = $this->createElement( 'ValidationTextBox', 'produto_estoque_max' )
			   ->setLabel( 'Estoque máximo' )
			   ->setAttrib( 'readOnly', 'true' )
			   ->setAttrib( 'class', 'input-form' );
	
	$optTipo[''] = '';
	$optTipo['E'] = 'Entrada';
	$optTipo['S'] = 'Saída';
	
	$elementsMov[] = $this->createElement( 'FilteringSelect', 'estoque_tipo' )
			   ->setLabel( 'Tipo' )
			   ->addMultiOptions( $optTipo )
			   ->setDijitParam( 'placeHolder', 'Tipo da movimentação' )
			   ->setAttrib( 'class', 'input-form' )
			   ->setAttrib( 'onChange', 'almoxarifadoEstoque.tipoMovimentacao()' )
			   ->setRequired( true );
	
	$dbTerceiro = new Model_DbTable_Terceiro();
	$data = $dbTerceiro->fetchAll( array( 'terceiro_tipo = ?' => 'F' ), 'terceiro_nome' );

	$optTerceiro[''] = '';
	foreach ( $data as $row )
	    $optTerceiro[$row->terceiro_id] = $row->terceiro_nome;

	$elementsMov[] = $this->createElement( 'FilteringSelect', 'terceiro_id' )
			   ->setLabel( 'Fornecedor' )
			   ->addMultiOptions( $optTerceiro )
			   ->setAttrib( 'class', 'input-form' )
			   ->setDijitParam( 'placeHolder', 'Selecione o fornecedor' )
			   ->setRequired( false );
	
	$elementsMov[] = $this->createElement( 'NumberSpinner', 'estoque_quantidade' )
			   ->setLabel( 'Quantidade' )
			   ->setDijitParam( 'placeHolder', 'Informe a quantidade da movimentação' )
			   ->setIntermediateChanges( true )
			   ->setMin( 1 )
			   ->setAttrib( 'class', 'input-form' )
			   ->addFilter( 'StringTrim' )
			   ->setAttrib( 'onChange', 'almoxarifadoEstoque.calculaTotal()' )
                           ->setValue( 1 )
			   ->setAttrib( 'readOnly', 'true' )
			   ->setRequired( true );
	
	$elementsMov[] = $this->createElement( 'CurrencyTextBox', 'estoque_valor_atual' )
			   ->setLabel( 'Valor atual' )
			   ->setDijitParam( 'placeHolder', 'Informe o valor do produto' )
			   ->setAttrib( 'class', 'input-form' )
			   ->setAttrib( 'onChange', 'almoxarifadoEstoque.calculaTotal()' )
                           ->setValue(0)
			   ->setRequired( true );
	
	$elementsMov[] = $this->createElement( 'ValidationTextBox', 'estoque_numero_nota' )
			   ->setLabel( 'Número nota' )
			   ->setDijitParam( 'placeHolder', 'Digite o número da nota' )
			   ->setAttrib( 'maxlength', 50 )
			   ->setAttrib( 'class', 'input-form' )
			   ->addFilter( 'StringTrim' );
	
	$elementsMov[] = $this->createElement( 'CurrencyTextBox', 'estoque_valor_total' )
			   ->setLabel( 'Valor total' )
			   ->setAttrib( 'class', 'input-form' )
			   ->setAttrib( 'readOnly', 'true' )
                           ->setValue(0)
			   ->setRequired( true );
	
	$elementsMov[] = $this->createElement( 'SimpleTextarea', 'estoque_observacao' )
                   ->setLabel( 'Observação' )
		   ->setAttrib( 'style', 'height: 50px; width: 540px;' )
                   ->setAttrib( 'maxlength', 400 )
                   ->setDijitParam( 'progressObserver', true )
                   ->addFilters( array( 'StringTrim', 'StripTags' ) );
	
	$this->addDisplayGroup( $elementsProd, 'elementos-prod', 
		array( 'decorators' => array( 
					    'FormElements',
					    'Fieldset'
					),
			'legend' => 'Dados produto'		
		    ) );
	
	$this->addDisplayGroup( $elementsMov, 'elementos-mov', 
		array( 'decorators' => array( 
					    'FormElements',
					    'Fieldset'
					),
			'legend' => 'Movimentação'			
		) );
	
	foreach( $elementsMov as $element )
	    $element->setAttrib( 'readOnly', true );
	
	$this->setDefaultDecorator( 'columns' )
	     ->_defineDecorators()
	     ->setRenderDefaultDecorators( false )
	     ->setRenderDefaultToolbar( false );
	
	$this->setDefaultDecorator( 'columns' )
	     ->getElement( 'estoque_observacao' )
	     ->removeDecorator( 'float' );
    }
    
    /**
     *
     * @param array $data
     * @return bool 
     */
    public function isValid( $data )
    {
        if ( 'S' != $data['estoque_tipo'] )
            $this->getElement( 'terceiro_id' )->setRequired( true );
	
        return parent::isValid( $data );
    }
}