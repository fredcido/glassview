<?php

/**
 * Description of Modulo
 *
 * @version $Id: ReconciliacaoBancariaController.php 883 2013-04-27 22:13:38Z fred $
 */
class Relatorio_ReconciliacaoBancariaController extends App_Controller_Default
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
    const TITULO = 'Reconciliação Bancária';
    
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

            $this->_form = new Relatorio_Form_ReconciliacaoBancaria();
            $this->_form->setAction( $action );
        }

        return $this->_form;
    }

    /**
     * 
     */
    public function visualizarAction()
    {
	// Busca Reconciliacao Bancaria
	$data = $this->_mapper->setData( $this->_getAllParams() )->ReconciliacaoBancaria();
	
	// Agrupa por lancamentos
	$this->view->data = $this->_mapper->agrupaLancamentosReconciliacaoBancaria( $data );

	$this->_helper->viewRenderer->setRender( 'relatorio' );
    }
    
    /**
     * 
     */
    public function gerarPdfAction()
    {
	// Busca Reconciliacao Bancaria
	$data = $this->_mapper->setData( $this->_getAllParams() )->ReconciliacaoBancaria();
	
	$layoutPath = APPLICATION_PATH . '/modules/relatorio/views/scripts/';

	$html = new Zend_View();
	$html->setScriptPath( $layoutPath );
	$html->addHelperPath( 'App/View/Helpers/', 'App_View_Helper' );
	
	$html->path = APPLICATION_PATH . '/..';
        $html->data = $this->_mapper->agrupaLancamentosReconciliacaoBancaria( $data );
        $html->titulo = self::TITULO;
        
	$domPdf = new App_General_DomPDF( 'a4', 'landscape' );
	$domPdf->loadHtml( $html->render( 'reconciliacao-bancaria/body.phtml' ) );
	$domPdf->download( 'Relatorio_Reconciliacao_Bancaria_' . date('d_m_Y_H_i') . '.pdf' );
	
	$this->_helper->viewRenderer->setNoRender();
    }
    
    /**
     * 
     */
    public function gerarExcellAction()
    {
	// Busca Reconciliacao Bancaria
	$data = $this->_mapper->setData( $this->_getAllParams() )->ReconciliacaoBancaria();
	
	$layoutPath = APPLICATION_PATH . '/modules/relatorio/views/scripts/';

	$html = new Zend_View();
	$html->setScriptPath( $layoutPath );
	$html->addHelperPath( 'App/View/Helpers/', 'App_View_Helper' );
        
	$html->data = $this->_mapper->agrupaLancamentosReconciliacaoBancaria( $data );;
	$html->titulo = self::TITULO;
	
	App_General_Util::toExcell( 'Relatorio_Reconciliacao_Bancaria_' . date('d_m_Y_H_i') . '.xls', $html->render( 'reconciliacao-bancaria/body.phtml' ) );
	
	$this->_helper->viewRenderer->setNoRender();
    }
}