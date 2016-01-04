<?php

/**
 * 
 * @version $Id $
 */
class Relatorio_ReconciliacaoCartaoCreditoController extends App_Controller_Default
{
	/**
	 *
	 * @var Relatorio_Model_Mapper_ReconciliacaoCartaoCredito
	 */
	protected $_mapper;
	
	/**
	 * 
	 * @var string
	 */
	const TITULO = 'Reconciliação de Cartão de Crédito';
	
	/**
	 * (non-PHPdoc)
	 * @see Zend_Controller_Action::init()
	 */
	public function init()
	{
		$this->_mapper = new Relatorio_Model_Mapper_ReconciliacaoCartaoCredito();
		
		$this->view->titulo = self::TITULO;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see App_Controller_Default::indexAction()
	 */
	public function indexAction()
	{
		$form = new Relatorio_Form_ReconciliacaoCartaoCredito();
		$form->setAction( $this->_helper->url('visualizar') );
	
		$this->view->form = $form;
	}
	
	/**
	 *
	 * @access public
	 * @return void
	 */
	public function visualizarAction()
	{
		$data = $this->_mapper->setData( $this->_getAllParams() )->reconciliacaoCartaoCredito();
	
		$this->view->data = $data;
	
		$this->_helper->viewRenderer->setRender( 'relatorio' );
	}
	
	/**
	 *
	 * @access public
	 * @return void
	 */
	public function gerarPdfAction()
	{
		$data = $this->_mapper->setData( $this->_getAllParams() )->reconciliacaoCartaoCredito();
	
		$layoutPath = APPLICATION_PATH . '/modules/relatorio/views/scripts/';
	
		$html = new Zend_View();
		$html->setScriptPath( $layoutPath );
		$html->addHelperPath( 'App/View/Helpers/', 'App_View_Helper' );
		
		$html->path 	= APPLICATION_PATH . '/..';
		$html->data 	= $data;
		$html->titulo 	= self::TITULO;
	
		$domPdf = new App_General_DomPDF( 'a4', 'landscape' );
		$domPdf->loadHtml( $html->render('reconciliacao-cartao-credito/relatorio.phtml') );
		$domPdf->download( 'Relatorio_Reconciliacao_Cartao_Credito_' . date('d_m_Y_H_i') . '.pdf' );
	
		$this->_helper->viewRenderer->setNoRender();
	}
	
	/**
	 *
	 */
	public function gerarExcellAction()
	{
		// Busca lancamentos
		$data = $this->_mapper->setData( $this->_getAllParams() )->reconciliacaoCartaoCredito();
	
		$layoutPath = APPLICATION_PATH . '/modules/relatorio/views/scripts/';
	
		$html = new Zend_View();
		$html->setScriptPath( $layoutPath );
		$html->addHelperPath( 'App/View/Helpers/', 'App_View_Helper' );
		
		$html->data 	= $data;
		$html->titulo 	= self::TITULO;
	
		App_General_Util::toExcell( 'Relatorio_Reconciliacao_Cartao_Credito_' . date('d_m_Y_H_i'), $html->render( 'reconciliacao-cartao-credito/body.phtml' ) );
	
		$this->_helper->viewRenderer->setNoRender();
	}	
}