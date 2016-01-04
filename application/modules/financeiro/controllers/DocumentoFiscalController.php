<?php

/**
 * Description of Modulo
 *
 * @version $Id: DocumentoFiscalController.php 944 2013-08-20 17:12:41Z helion $
 */
class Financeiro_DocumentoFiscalController extends App_Controller_Default
{
    /**
     *
     * @var type 
     */
    protected $_mapper;
    
    public function init()
    {
        $this->_mapper = new Model_Mapper_DocumentoFiscal();
    }
    
    public function listAction()
    {
        $data = $this->_mapper->fetchGrid();
        
        $this->_helper->json( $data );
    }

     /**
     *
     * @param string $action
     * @return Financeiro_Form_DocumentoFiscal
     */
    protected function _getForm( $action )
    {
        if ( null == $this->_form ) {

            $this->_form = new Financeiro_Form_DocumentoFiscal();
            $this->_form->setAction( $action );
        }

        return $this->_form;
    }

    public function editPostHook()
    {
	$id = $this->_getParam( 'id' );

	$this->view->itensDocFiscal = $this->_mapper->listItensDocFiscal( $id );
	$this->view->lancamentosEmDocFiscal = $this->_mapper->lancamentosLigadosEmDocumentoFiscal( $id, true );
        
        if(!empty($this->view->lancamentosEmDocFiscal)){
            
            $this->view->editDocFiscal  = false;
            
            $this->view->form->getElement( 'terceiro_id_remetente' )
                              ->setAttrib( 'readOnly', 'readOnly' );

            $this->view->form->getElement( 'terceiro_id_destinatario' )
                              ->setAttrib( 'readOnly', 'readOnly' );

            $this->view->form->getElement( 'fn_doc_fiscal_numero' )
                              ->setAttrib( 'readOnly', 'readOnly' );
            
            $this->view->form->getElement( 'fn_doc_fiscal_data' )
                              ->setAttrib( 'readOnly', 'readOnly' );
            
            $this->view->form->getElement( 'fn_doc_fiscal_chave' )
                              ->setAttrib( 'readOnly', 'readOnly' );
        }else{
            
            $this->view->editDocFiscal = true;
            $this->view->lancamentosEmDocFiscal = $this->_mapper->lancamentosLigadosEmDocumentoFiscal( $id  );
        }
    }

    /**
     *
     */
    public function listaDocumentosFiscaisAction()
    {
	$retorno = $this->_mapper->listaDocumentosFiscais( $this->_getAllParams() );

	$this->_helper->json( $retorno );
    }
    
    public function somanotaAction()
    {
        $params = $this->_getAllParams();
        
        $retorno = array('total' => 0);
        
        foreach ($params['fn_doc_fiscal_item_total'] as $value) {
            
            $retorno['total'] = $retorno['total'] + $value;
        }
        
        $this->_helper->json( $retorno );
    }
}