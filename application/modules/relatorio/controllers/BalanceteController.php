<?php

/**
 * Description of Modulo
 *
 * @version $Id: BalanceteController.php 839 2012-12-12 17:33:14Z fred $
 */
class Relatorio_BalanceteController extends App_Controller_Default
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
    const TITULO = 'Balancete';
    
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

            $this->_form = new Relatorio_Form_Balancete();
            $this->_form->setAction( $action );
        }

        return $this->_form;
    }

    /**
     * @access public
     * @return void
     */
    public function tipoLancamentoAction()
    {
	$id = $this->_getParam( 'id', 0 );

	$mapper = new Model_Mapper_TipoLancamento();
	$mapper->setData( array( 'projeto_id' => $id ) );

	$rows = $mapper->fetchTipoLancamentoPorProjeto();

	$data = array();

	if ( $rows->count() ) 
	    $data = $this->view->treeTipoLancamento( $rows );

	$this->_helper->json( $data );
    }
    
    /**
     * 
     */
    public function visualizarAction()
    {
	// Busca lancamentos
	$data = $this->_mapper->setData( $this->_getAllParams() )->conta();
	
	// Agrupa por lancamentos
	$this->view->data = $this->_mapper->agrupaLancamentos( $data );

	$this->_helper->viewRenderer->setRender( 'relatorio' );
    }
    
    /**
     * 
     */
    public function gerarPdfAction()
    {
	// Busca lancamentos
	$data = $this->_mapper->setData( $this->_getAllParams() )->conta();
		
	$layoutPath = APPLICATION_PATH . '/modules/relatorio/views/scripts/';

	$html = new Zend_View();
	$html->setScriptPath( $layoutPath );
	$html->addHelperPath( 'App/View/Helpers/', 'App_View_Helper' );
	
	$html->path 	= APPLICATION_PATH . '/..';
	$html->titulo 	= self::TITULO;
	$html->data 	= $this->_mapper->agrupaLancamentos( $data );
	
	$domPdf = new App_General_DomPDF( 'a4', 'landscape' );
	$domPdf->loadHtml( $html->render( 'balancete/relatorio.phtml' ) );
	$domPdf->download( 'Relatorio_Balancete_' . date('d_m_Y_H_i') . '.pdf' );
	
	$this->_helper->viewRenderer->setNoRender();
    }
    
    /**
     * 
     */
    public function gerarExcellAction()
    {
	// Busca lancamentos
	$data = $this->_mapper->setData( $this->_getAllParams() )->conta();
		
	$layoutPath = APPLICATION_PATH . '/modules/relatorio/views/scripts/';

	$html = new Zend_View();
	$html->setScriptPath( $layoutPath );
	$html->addHelperPath( 'App/View/Helpers/', 'App_View_Helper' );
	
	$html->data = $this->_mapper->agrupaLancamentos( $data );
	$html->titulo = self::TITULO;
	
	App_General_Util::toExcell( 'Relatorio_Balancete_' . date('d_m_Y_H_i'), $html->render( 'balancete/body.phtml' ) );
	
	$this->_helper->viewRenderer->setNoRender();
    }
}