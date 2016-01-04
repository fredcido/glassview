<?php

/**
 * Description of Modulo
 *
 * @version $Id: DuplicataController.php 1002 2013-10-01 19:07:00Z helion $
 */
class Financeiro_DuplicataController extends App_Controller_Default
{
    /**
     *
     * @var type 
     */
    protected $_mapper;
    
    public function init()
    {
        $this->_mapper = new Model_Mapper_Duplicata();
    }
    
    public function listAction()
    {
        $data = $this->_mapper->fetchGrid();
        
        $this->_helper->json( $data );
    }

     /**
     *
     * @param string $action
     * @return Financeiro_Form_Duplicata
     */
    protected function _getForm( $action )
    {
        if ( null == $this->_form ) {

            $this->_form = new Financeiro_Form_Duplicata();
            $this->_form->setAction( $action );
        }

        return $this->_form;
    }
    
    /**
     * 
     */
    public function editPostHook()
    {
	$id = $this->_getParam( 'id' );

	$this->view->parcelas = $this->_mapper->listaParcelasDuplicata( $id );
        
        
	$pacelasEfetivadas = $this->_mapper->listaParcelasDuplicata( $id , true );
        
        
        $this->view->qtdparcelaseftivadas = $pacelasEfetivadas;
        
        if(!empty($this->view->qtdparcelaseftivadas)){
            
            $this->view->form->getElement( 'terceiro_id' )
                             ->setAttrib( 'readOnly', 'readOnly' );

            $this->view->form->getElement( 'fn_duplicata_total' )
                             ->setAttrib( 'readOnly', 'readOnly' );

            $this->view->form->getElement( 'fn_duplicata_parcelas' )
                             ->setAttrib( 'readOnly', 'readOnly' );

            $this->view->form->getElement( 'fn_duplicata_tipo' )
                             ->setAttrib( 'readOnly', 'readOnly' );

            $this->view->form->getElement( 'btn_doc_fiscal' )
                             ->setAttrib( 'disabled', true );
            
            $this->view->form->getDisplayGroup('toolbar')
                              ->removeElement( 'buttonRemoveDuplicata' );
        }else{
            
            $this->_mapper->setData( array( 'fn_duplicata_id' => $id ) );
            
            if( $this->_mapper->isDeleteDuplicata() ){
                
                $this->view->form->getDisplayGroup('toolbar')
                                  ->getElement( 'buttonRemoveDuplicata' )
                                  ->setAttrib( 'disabled', null );
            }else{
                
                $this->view->form->getDisplayGroup('toolbar')
                              ->removeElement( 'buttonRemoveDuplicata' );
            }
        }
        $this->view->form->getElement( 'fn_conta_id' )
                         ->setAttrib( 'readOnly', 'readOnly' )
                         ->setRequired( false );
    }
    
    /**
     * @access public
     * @return void
     */
    public function deletAction()
    {
	$data = array();

	if ( $this->getRequest()->getPost() ) {

	    $post = $this->getRequest()->getPost();

	    $this->_mapper->setData( array( 'fn_duplicata_id' => $post['identify'] ) );

	    $data['status']     =  $this->_mapper->delete();
	    $data['description'] = $this->_mapper->getMessage()->toArray();
	} else {

	    $message = new App_Message();
	    $message->addMessage( $this->_config->messages->warning, App_Message::WARNING );

	    $data['status'] = false;
	    $data['description'] = $message->toArray();
	}

	$this->_helper->json( $data );
    }
}