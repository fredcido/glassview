<?php

/**
 * 
 * @version $Id $
 */
class Relatorio_ProjetoController extends App_Controller_Default 
{
	/**
	 * 
	 * @var Relatorio_Model_Mapper_Projeto
	 */
	protected $_mapper;
	
	/**
	 *
	 * @var string
	 */
	const TITULO = 'Projeto';
	
	/**
	 * (non-PHPdoc)
	 * @see Zend_Controller_Action::init()
	 */
	public function init()
	{
		$this->_mapper = new Relatorio_Model_Mapper_Projeto();
		
		$this->view->titulo = self::TITULO;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see App_Controller_Default::indexAction()
	 */
	public function indexAction()
	{
		$form = new Relatorio_Form_Projeto();
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
		$data = $this->_mapper->setData( $this->_getAllParams() )->projeto();
		
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
		$data = $this->_mapper->setData( $this->_getAllParams() )->projeto();
	
		$layoutPath = APPLICATION_PATH . '/modules/relatorio/views/scripts/';
	
		$html = new Zend_View();
		$html->setScriptPath( $layoutPath );
		$html->addHelperPath( 'App/View/Helpers/', 'App_View_Helper' );
		
		$html->path = APPLICATION_PATH . '/..';
		$html->data = $data;
		$html->titulo = self::TITULO;
	
		$domPdf = new App_General_DomPDF( 'a4', 'landscape' );
		$domPdf->loadHtml( $html->render('projeto/relatorio.phtml') );
		$domPdf->download( 'Relatorio_Projeto_' . date('d_m_Y_H_i') . '.pdf' );
	
		$this->_helper->viewRenderer->setNoRender();
	}
	
	/**
	 *
	 */
	public function gerarExcellAction()
	{
		// Busca lancamentos
		$data = $this->_mapper->setData( $this->_getAllParams() )->projeto();
	
		$layoutPath = APPLICATION_PATH . '/modules/relatorio/views/scripts/';
	
		$html = new Zend_View();
		$html->setScriptPath( $layoutPath );
		$html->addHelperPath( 'App/View/Helpers/', 'App_View_Helper' );
		
		$html->data = $data;
		$html->titulo = self::TITULO;
	
		App_General_Util::toExcell( 'Relatorio_Projeto_' . date('d_m_Y_H_i'), $html->render( 'projeto/body.phtml' ) );
	
		$this->_helper->viewRenderer->setNoRender();
	}
}