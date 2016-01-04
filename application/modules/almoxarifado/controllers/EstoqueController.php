<?php

/**
 * Description of Modulo
 *
 * @version $Id: EstoqueController.php 252 2012-02-15 03:21:42Z fred $
 */
class Almoxarifado_EstoqueController extends App_Controller_Default
{
    /**
     *
     * @var type 
     */
    protected $_mapper;
    
    public function init()
    {
        $this->_mapper = new Model_Mapper_Estoque();
    }

     /**
     *
     * @param string $action
     * @return Admin_Form_Estoque
     */
    protected function _getForm( $action )
    {
        if ( null == $this->_form ) {

            $this->_form = new Almoxarifado_Form_Estoque();
            $this->_form->setAction( $action );
        }

        return $this->_form;
    }
    
    /**
     * 
     */
    public function dadosProdutoAction()
    {
	$mapperProduto = new Model_Mapper_Produto();
	$produto = $mapperProduto->getDadosMovimentacao( $this->_getParam( 'id' ) );
	
	if ( empty( $produto ) )
	    $data = array( 'valid' => false );
	else {
	    
	    $produto = $produto->toArray();
	    
	    $produto['estoque_valor_atual'] = (float)$produto['estoque_valor_atual'];
	    
	    $data = array( 
		'valid' => true,
		'data'	=> $produto
	    );
	}
	    
	    
	$this->_helper->json( $data );
    }
    
    /**
     * 
     */
    public function ajusteAction()
    {
	$lancamento = $this->_mapper->buscaLancamento( $this->_getParam( 'id' ) );
	
	if ( empty( $lancamento ) )
	    throw new Exception( 'Lan&ccedil;amento inv&aacute;lido.' );
	
	$form = $this->_getForm( $this->_helper->url( 'save' ) );
	$form->populate( $lancamento->toArray() );
	
	$estoque_tipo = $lancamento->estoque_tipo == 'E' ? 'S' : 'E';
	    
	$form->getElement( 'estoque_tipo' )->setValue( $estoque_tipo );
	$form->getElement( 'produto_id' )->setAttrib( 'readOnly', true );
	$form->getElement( 'estoque_quantidade' )->setAttrib( 'readOnly', null )->setValue( 1 );
	$form->getElement( 'estoque_valor_atual' )->setAttrib( 'readOnly', null );
	$form->getElement( 'estoque_observacao' )->setAttrib( 'readOnly', null )->setRequired( true );
	
	$mapperProduto = new Model_Mapper_Produto();
	$produto = $mapperProduto->getDadosMovimentacao( $lancamento->produto_id );
	$form->getElement( 'estoque_qtde_anterior' )->setValue( $produto->estoque_qtde_anterior );
	$form->getElement( 'estoque_anterior' )->setValue( $this->_getParam( 'id' ) );
	
	if ( $estoque_tipo == 'E' )
	    $form->getElement( 'terceiro_id' )->setAttrib( 'readOnly', null );
	
	$form->addDecorator( 'Description', array( 'placement' => 'prepend' ) )
	     ->setDescription( $this->_messageAjuste( $lancamento ) );
	
	$this->view->form = $form;
	
	$this->_helper->viewRenderer->setRender( 'form' );
    }
    
    /**
     *
     * @param type $lancamento
     * @return type 
     */
    protected function _messageAjuste( $lancamento )
    {
	$movimento = $lancamento->estoque_tipo == 'S' ? 'Saída' : 'Entrada';
	$currency = new Zend_Currency();
	
	$message = 'Ajuste de lançamento de %s, com quantidade %s no valor de %s';
	
	return sprintf( $this->view->translate()->_( $message ), $this->view->translate()->_( $movimento ), 
		$lancamento->estoque_quantidade, $currency->setValue( $lancamento->estoque_valor_total )->toString() );
    }
    
    public function visualizarAction()
    {
	$lancamento = $this->_mapper->buscaLancamento( $this->_getParam( 'id' ) );
	
	if ( empty( $lancamento ) )
	    throw new Exception( 'Lan&ccedil;amento inv&aacute;lido.' );
	
	$form = $this->_getForm( $this->_helper->url( 'save' ) );
	$form->populate( $lancamento->toArray() );
	$form->getElement( 'produto_id' )->setAttrib( 'readOnly', true );
	$form->removeDisplayGroup( 'toolbar' );
	
	$this->view->form = $form;
	
	$this->_helper->viewRenderer->setRender( 'form' );
    }
}