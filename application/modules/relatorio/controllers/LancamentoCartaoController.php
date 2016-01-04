<?php

/**
 * Description of Modulo
 *
 * @version $Id: LancamentoCartaoController.php 837 2012-11-30 11:07:25Z fred $
 */
class Relatorio_LancamentoCartaoController extends App_Controller_Default
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
    const TITULO = 'Lançamento de Cartão';
    
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

            $this->_form = new Relatorio_Form_LancamentoCartao();
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
	$rows = $this->_mapper->setData( $this->_getAllParams() )->LancamentoCartao();
	
        $data = array( 'total' => 0 ,'data' => array() );
        
        foreach ( $rows as $key => $row ) {

            $data['total'] += (float)$row->fn_lanc_cartao_valor;
            $data['data'][$key]['data'] = $row;
        }
	
        $this->view->data = $data;
        
	$this->_helper->viewRenderer->setRender( 'relatorio' );
    }
    
    /**
     * 
     */
    public function gerarPdfAction()
    {
	// Busca Fatura
	$rows = $this->_mapper->setData( $this->_getAllParams() )->LancamentoCartao();
		
	$layoutPath = APPLICATION_PATH . '/modules/relatorio/views/scripts/';

	$html = new Zend_View();
	$html->setScriptPath( $layoutPath );
	$html->addHelperPath( 'App/View/Helpers/', 'App_View_Helper' );
	
	$html->path = APPLICATION_PATH . '/..';
	$html->titulo = self::TITULO;
	
	$data = array( 'total' => 0 ,'data' => array() );
        
        foreach ( $rows as $key => $row ) {

            $data['total'] += (float)$row->fn_lanc_cartao_valor;
            $data['data'][$key]['data'] = $row;
        }
	
        $html->data = $data;
	
	$domPdf = new App_General_DomPDF( 'a4', 'landscape' );
	$domPdf->loadHtml( $html->render( 'lancamento-cartao/relatorio.phtml' ) );
	$domPdf->download( 'Relatorio_Lancamento_Cartao_' . date('d_m_Y_H_i') . '.pdf' );
	
	$this->_helper->viewRenderer->setNoRender();
    }
    
    /**
     * 
     */
    public function gerarExcellAction()
    {
	// Busca Fatura
	$rows = $this->_mapper->setData( $this->_getAllParams() )->LancamentoCartao();
		
	$layoutPath = APPLICATION_PATH . '/modules/relatorio/views/scripts/';

	$html = new Zend_View();
	$html->setScriptPath( $layoutPath );
	$html->addHelperPath( 'App/View/Helpers/', 'App_View_Helper' );
	
	$html->titulo = self::TITULO;
	
	$data = array( 'total' => 0 ,'data' => array() );
        
        foreach ( $rows as $key => $row ) {

            $data['total'] += (float)$row->fn_lanc_cartao_valor;
            $data['data'][$key]['data'] = $row;
        }
	
        $html->data = $data;
	
	App_General_Util::toExcell( 'Relatorio_Lancamento_Cartao_' . date('d_m_Y_H_i'), $html->render( 'lancamento-cartao/body.phtml' ) );
	
	$this->_helper->viewRenderer->setNoRender();
    }
}