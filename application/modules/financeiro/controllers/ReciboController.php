<?php

/**
 * Description of Recibo
 *
 * @version $Id: ReciboController.php 816 2012-09-30 14:42:20Z fred $
 */
class Financeiro_ReciboController extends App_Controller_Default
{
    /**
     *
     * @var type 
     */
    protected $_mapper;
    
    public function init()
    {
        $this->_mapper = new Model_Mapper_Recibo();
    }

     /**
     *
     * @param string $action
     * @return Financeiro_Form_Recibo
     */
    protected function _getForm( $action )
    {
        if ( null == $this->_form ) {

            $this->_form = new Financeiro_Form_Recibo();
            $this->_form->setAction( $action );
        }

        return $this->_form;
    }
    
    public function editPostHook()
    {
	$data = $this->view->data["fn_recibo_data"];
	
	if ( !empty( $this->view->data['terceiro_id'] ) )
	    $receptor = 'T-' . $this->view->data['terceiro_id'];
	else
	    $receptor = 'F-' . $this->view->data['funcionario_id'];
	
	$date = new Zend_Date( $data );
	$this->view->form->getElement( 'fn_recibo_data' )->setValue( $date->toString( 'yyyy-MM-dd' ) );
	$this->view->form->getElement( 'receptor' )->setValue( $receptor );
    }
    
    /**
     * 
     */
    public function dadosReceptorAction()
    {
	$param = $this->_getParam( 'id' );
	$id = preg_replace( '/[^0-9]/i', '', $param );
	$tipo = preg_replace( '/[0-9-]/i', '', $param );
	
	if ( 'T' == $tipo ) {
	
	    $dbTerceiro = App_Model_DbTable_Factory::get( 'Terceiro' );
	    $terceiro = $dbTerceiro->fetchRow( array( 'terceiro_id = ?' => $id ) );
	    $cpf_cnpj = $terceiro->terceiro_cpf_cnpj;
	    
	} else {
	    
	    $mapperFuncionario = new Model_Mapper_Funcionario();
	    $funcionario = $mapperFuncionario->setData( array( 'id' => $id ) )->fetchRow();
	    $cpf_cnpj = $funcionario->funcionario_cpf_cnpj;
	}
	
	$this->_helper->json( array( 'cpf_cnpj' => $cpf_cnpj ) );
    }
    
    /**
     * 
     */
    public function imprimirAction()
    {
	$layoutPath = APPLICATION_PATH . '/modules/financeiro/views/scripts/recibo/';

	$html = new Zend_View();
	$html->setScriptPath( $layoutPath );
	$html->addHelperPath( 'App/View/Helpers/', 'App_View_Helper' );
	$html->recibos = $this->_mapper->listAll( explode( ',', $this->_getParam( 'recibos' ) ) );;
	
	$domPdf = new App_General_DomPDF( 'a4', 'portrait' );
	$domPdf->loadHtml( $html->render( 'recibos.phtml' ) );
	$domPdf->download( 'Recibo_' . date('d_m_Y_H_i') . '.pdf' );
	
	$this->_helper->viewRenderer->setNoRender();
    }
    

}