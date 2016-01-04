<?php

/**
 * Description of Modulo
 *
 * @version $Id: AtivoController.php 953 2013-09-06 12:39:25Z helion $
 */
class Relatorio_AtivoController extends App_Controller_Default
{
    /**
     *
     * @var type 
     */
    protected $_mapper;
    
    const TITULO = 'Ativo';
    
    /**
     * 
     */
    public function init()
    {
        $this->_mapper = new Relatorio_Model_Mapper_Almoxarifado();
        
        $this->view->titulo = self::TITULO;
    }
    
    /**
     * 
     */
    public function indexAction()
    {
	$this->view->form = $this->_getForm( $this->_helper->url( 'visualizar' ) );
    }

     /**
     *
     * @param string $action
     * @return Relatorio_Form_Balancete
     */
    protected function _getForm( $action )
    {
        if ( null == $this->_form ) {

            $this->_form = new Relatorio_Form_Ativo();
            $this->_form->setAction( $action );
        }

        return $this->_form;
    }

  
    /**
     * 
     */
    public function visualizarAction()
    {
	// Busca ativos
	$this->view->data = $this->_mapper->setData( $this->_getAllParams() )->ativo();

	$this->_helper->viewRenderer->setRender( 'relatorio' );
    }
    
    /**
     * 
     */
    public function gerarPdfAction()
    {
	// Busca lancamentos
	$data = $this->_mapper->setData( $this->_getAllParams() )->ativo();
		
	$layoutPath = APPLICATION_PATH . '/modules/relatorio/views/scripts/';

	$html = new Zend_View();
	$html->setScriptPath( $layoutPath );
	$html->addHelperPath( 'App/View/Helpers/', 'App_View_Helper' );
	
	$html->path 	= APPLICATION_PATH . '/..';
	$html->data 	= $data;
	$html->titulo 	= self::TITULO;
	
	$domPdf = new App_General_DomPDF( 'a4', 'portrait' );
	$domPdf->loadHtml( $html->render( 'ativo/relatorio.phtml' ) );
	$domPdf->download( 'Relatorio_Ativo_' . date('d_m_Y_H_i') . '.pdf' );
	
	$this->_helper->viewRenderer->setNoRender();
    }
    
    /**
     * 
     */
    public function gerarExcellAction()
    {
	// Busca lancamentos
	$data = $this->_mapper->setData( $this->_getAllParams() )->ativo();
		
	$layoutPath = APPLICATION_PATH . '/modules/relatorio/views/scripts/';

	$html = new Zend_View();
	$html->setScriptPath( $layoutPath );
	$html->addHelperPath( 'App/View/Helpers/', 'App_View_Helper' );
	
	$html->data 	= $data;
	$html->titulo 	= self::TITULO;
	
	App_General_Util::toExcell( 'Relatorio_Ativo_' . date('d_m_Y_H_i'),  $html->render( 'ativo/body.phtml' ) );
	
	$this->_helper->viewRenderer->setNoRender();
    }
}