<?php

/**
 * 
 * @version $Id $
 */
class Default_LembreteController extends App_Controller_Default
{
    /**
     *
     * @var type 
     */
    protected $_mapper;
    
    /**
     * 
     */
    public function init()
    {
        $this->_mapper = new Model_Mapper_Lembrete();
    }

     /**
     *
     * @param string $action
     * @return Admin_Form_Lembrete
     */
    protected function _getForm( $action )
    {
        if ( null == $this->_form ) {

            $this->_form = new Default_Form_Lembrete();
            $this->_form->setAction( $action );
        }

        return $this->_form;
    }
    
    /**
     * 
     */
    public function editPostHook()
    {
	$form = $this->view->form;
	
	if ( !empty( $this->view->data['lembrete_data_prevista'] ) ) {
	    
	    $dataPrevista = explode( ' ', $this->view->data['lembrete_data_prevista'] );

	    $form->getElement( 'lembrete_data_prevista' )->setValue( $dataPrevista[0] );
	    $form->getElement( 'lembrete_hora_prevista' )->setValue( 'T'.$dataPrevista[1] );
	}
    }
    
    /**
     * 
     */
    public function buscaLembretesAction()
    {
	$usr_id = Zend_Auth::getInstance()->getIdentity()->usuario_id;
	
	$lembretes = $this->_mapper->buscaLembretesUser( $usr_id );
	
	$this->_helper->json( $lembretes->toArray() );
    }
    
    /**
     * 
     */
    public function detalhaAction()
    {
	$lembrete = $this->_mapper->detalhaLembrete( $this->_getParam( 'id', 0 ) );
	
	$this->view->lembrete = $lembrete;
    }
    
    /**
     * 
     */
    public function mudarStatusAction()
    {
	$retorno = $this->_mapper->mudarStatus( $this->_getParam( 'id', 0 ) );
	
	$this->_helper->json( $retorno );
    }
}
