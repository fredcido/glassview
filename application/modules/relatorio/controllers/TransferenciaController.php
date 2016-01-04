<?php

/**
 * Description of Modulo
 *
 * @version $Id: TransferenciaController.php 786 2012-07-03 18:01:50Z ze $
 */
class Relatorio_TransferenciaController extends App_Controller_Default
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
    const TITULO = 'Transferência';
    
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

            $this->_form = new Relatorio_Form_Transferencia();
            $this->_form->setAction( $action );
        }

        return $this->_form;
    }

    /**
     * 
     */
    public function visualizarAction()
    {
	// Busca lancamentos de Transferencia
	$rows = $this->_mapper->setData( $this->_getAllParams() )->Transferencia();
        
        $data = array( 'total' => 0 ,'data' => array() );
        
        foreach ( $rows as $key => $row ) {

            $data['total'] += (float)$row->fn_lancamento_valor;
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
	// Busca lancamentos de Transferencia
	$rows = $this->_mapper->setData( $this->_getAllParams() )->Transferencia();

        $data = array( 'total' => 0 ,'data' => array() );

        foreach ( $rows as $key => $row ) {

            $data['total'] += (float)$row->fn_lancamento_valor;
            $data['data'][$key]['data'] = $row;
        }
		
	$layoutPath = APPLICATION_PATH . '/modules/relatorio/views/scripts/';

	$html = new Zend_View();
	$html->setScriptPath( $layoutPath );
	$html->addHelperPath( 'App/View/Helpers/', 'App_View_Helper' );
	
	$html->path = APPLICATION_PATH . '/..';
    $html->data = $data;
    $html->titulo = self::TITULO;
        
	$domPdf = new App_General_DomPDF( 'a4', 'landscape' );
	$domPdf->loadHtml( $html->render( 'transferencia/relatorio.phtml' ) );
	$domPdf->download( 'Relatorio_Transferencia_' . date('d_m_Y_H_i') . '.pdf' );
	
	$this->_helper->viewRenderer->setNoRender();
    }
    
    /**
     * 
     */
    public function gerarExcellAction()
    {
	// Busca lancamentos de Transferencia
	$rows = $this->_mapper->setData( $this->_getAllParams() )->Transferencia();

        $data = array( 'total' => 0 ,'data' => array() );

        foreach ( $rows as $key => $row ) {

            $data['total'] += (float)$row->fn_lancamento_valor;
            $data['data'][$key]['data'] = $row;
        }
		
	$layoutPath = APPLICATION_PATH . '/modules/relatorio/views/scripts/';

	$html = new Zend_View();
	$html->setScriptPath( $layoutPath );
	$html->addHelperPath( 'App/View/Helpers/', 'App_View_Helper' );
        
	$html->data = $data;
	$html->titulo = self::TITULO;
	
	App_General_Util::toExcell( 'Relatorio_Transferencia_' . date('d_m_Y_H_i') . '.xls', $html->render( 'transferencia/body.phtml' ) );
	
	$this->_helper->viewRenderer->setNoRender();
    }
}