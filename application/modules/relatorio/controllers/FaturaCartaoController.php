<?php

/**
 * Description of Modulo
 *
 * @version $Id: FaturaCartaoController.php 837 2012-11-30 11:07:25Z fred $
 */
class Relatorio_FaturaCartaoController extends App_Controller_Default
{
    /**
     *
     * @var type 
     */
    protected $_mapper;
    
    /**
     *
     * @var string
     */
    const TITULO = 'Fatura de Cartão';
    
    /**
     * 
     */
    public function init()
    {
        $this->_mapper = new Relatorio_Model_Mapper_Financeiro();
        
        $this->view->titulo = self::TITULO;
    }
    
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

            $this->_form = new Relatorio_Form_FaturaCartao();
            $this->_form->setAction( $action );
        }

        return $this->_form;
    }

    
    /**
     * 
     */
    public function visualizarAction()
    {
	// Busca Fatura
	$data = $this->_mapper->setData( $this->_getAllParams() )->FaturaCartao();
	
	// Agrupa por lancamentos
	$this->view->data = $this->_mapper->agrupaLancamentosFaturaCartao( $data );

	$this->_helper->viewRenderer->setRender( 'relatorio' );
    }
    
    /**
     * 
     */
    public function gerarPdfAction()
    {
	// Busca Fatura
	$data = $this->_mapper->setData( $this->_getAllParams() )->FaturaCartao();
		
	$layoutPath = APPLICATION_PATH . '/modules/relatorio/views/scripts/';

	$html = new Zend_View();
	$html->setScriptPath( $layoutPath );
	$html->addHelperPath( 'App/View/Helpers/', 'App_View_Helper' );
	
	$html->path = APPLICATION_PATH . '/..';
	$html->data = $this->_mapper->agrupaLancamentosFaturaCartao( $data );
	$html->titulo = self::TITULO;
	
	$domPdf = new App_General_DomPDF( 'a4', 'landscape' );
	$domPdf->loadHtml( $html->render( 'fatura-cartao/relatorio.phtml' ) );
	$domPdf->download( 'Relatorio_Fatura_Cartao_' . date('d_m_Y_H_i') . '.pdf' );
	
	$this->_helper->viewRenderer->setNoRender();
    }
    
    /**
     * 
     */
    public function gerarExcellAction()
    {
	// Busca Fatura
	$data = $this->_mapper->setData( $this->_getAllParams() )->FaturaCartao();
		
	$layoutPath = APPLICATION_PATH . '/modules/relatorio/views/scripts/';

	$html = new Zend_View();
	$html->setScriptPath( $layoutPath );
	$html->addHelperPath( 'App/View/Helpers/', 'App_View_Helper' );
	
	$html->data = $this->_mapper->agrupaLancamentosFaturaCartao( $data );
	$html->titulo = self::TITULO;
	
	App_General_Util::toExcell( 'Relatorio_Fatura_Cartao_' . date('d_m_Y_H_i'), $html->render( 'fatura-cartao/body.phtml' ) );
	
	$this->_helper->viewRenderer->setNoRender();
    }
}