<?php

/**
 * Description of Budget
 *
 * @version $Id: BudgetController.php 499 2012-04-14 12:23:13Z fred $
 */
class Financeiro_BudgetController extends App_Controller_Default
{
    /**
     *
     * @var type 
     */
    protected $_mapper;
    
    /**
     * 
     */
    public function init()
    {
        $this->_mapper = new Model_Mapper_Budget();
    }
    
    /**
     * 
     */
    public function indexAction()
    {
	$this->view->form = new Financeiro_Form_Budget();
    }
    
    /**
     * 
     */
    public function verificaBudgetAction()
    {
	$retorno = $this->_mapper->verificaBudget( $this->_getParam( 'id' ) );
	
	$this->_helper->json( $retorno );
    }
    
    /**
     * 
     */
    public function categoriasAction()
    {
	
    }
    
    /**
     * 
     */
    public function treeCategoriasAction()
    {
	$mapperCategoria = new Model_Mapper_Categoria();
	
	$dojoData = new Zend_Dojo_Data( 'id', $mapperCategoria->listCategoriaTree( $this->_getParam( 'id' ) ), 'name' );
	
	$itens = $dojoData->toArray();
	
	$this->_helper->json( $itens );
    }
    
    /**
     * 
     */
    public function listaLancamentosAction()
    {
	$retorno = $this->_mapper->listaLancamentos( $this->getRequest()->getPost() );
	
	$this->_helper->json( $retorno );
    }
    
    /**
     * 
     */
    public function saveAction()
    {
	$retorno = $this->_mapper->setData( $this->getRequest()->getPost() )->save();
	
	$this->_helper->json( $retorno );
    }
    
    /**
     * 
     */
    public function replicaValoresAction()
    {
	$retorno = $this->_mapper->setData( $this->getRequest()->getPost() )->replicaValores();
	
	$this->_helper->json( $retorno );
    }
    
    /**
     * 
     */
    public function valorGeralAction()
    {
	$retorno = $this->_mapper->setData( $this->getRequest()->getPost() )->valorGeral();
	
	$this->_helper->json( $retorno );
    }
}