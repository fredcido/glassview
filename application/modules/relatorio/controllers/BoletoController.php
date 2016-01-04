<?php

/**
 * Description of Modulo
 *
 * @version $Id: BoletoController.php 786 2012-07-03 18:01:50Z ze $
 */
class Relatorio_BoletoController extends App_Controller_Default
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
    const TITULO = 'Boleto';
    
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

            $this->_form = new Relatorio_Form_Boleto();
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
	// Busca Boletos
	$rows = $this->_mapper->setData( $this->_getAllParams() )->Boleto();

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
	// Busca Boletos
	$rows = $this->_mapper->setData( $this->_getAllParams() )->Boleto();

        $data = array( 'total' => 0 ,'data' => array() );

        foreach ( $rows as $key => $row ) {

            $data['total'] += (float)$row->fn_lancamento_valor;
            $data['data'][$key]['data'] = $row;
        }
		
	$layoutPath = APPLICATION_PATH . '/modules/relatorio/views/scripts/';

	$html = new Zend_View();
	$html->setScriptPath( $layoutPath );
	$html->addHelperPath( 'App/View/Helpers/', 'App_View_Helper' );
	
	$html->path 	= APPLICATION_PATH . '/..';
	$html->data 	= $data;
	$html->titulo 	= self::TITULO;
	
	$domPdf = new App_General_DomPDF( 'a4', 'landscape' );
	$domPdf->loadHtml( $html->render( 'boleto/relatorio.phtml' ) );
	$domPdf->download( 'Relatorio_Boleto_' . date('d_m_Y_H_i') . '.pdf' );
	
	$this->_helper->viewRenderer->setNoRender();
    }
    
    /**
     * 
     */
    public function gerarExcellAction()
    {
	// Busca Boletos
	$rows = $this->_mapper->setData( $this->_getAllParams() )->Boleto();

        $data = array( 'total' => 0 ,'data' => array() );

        foreach ( $rows as $key => $row ) {

            $data['total'] += (float)$row->fn_lancamento_valor;
            $data['data'][$key]['data'] = $row;
        }
		
	$layoutPath = APPLICATION_PATH . '/modules/relatorio/views/scripts/';

	$html = new Zend_View();
	$html->setScriptPath( $layoutPath );
	$html->addHelperPath( 'App/View/Helpers/', 'App_View_Helper' );
	
	$html->data 	= $data;
	$html->titulo 	= self::TITULO;
	
	App_General_Util::toExcell( 'Relatorio_Boleto_' . date('d_m_Y_H_i'), $html->render( 'boleto/body.phtml' ) );
	
	$this->_helper->viewRenderer->setNoRender();
    }
}