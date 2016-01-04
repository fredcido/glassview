<?php

/**
 *
 * @version $Id: AcaoController.php 258 2012-02-15 17:24:28Z fred $
 */
class Master_AcaoController extends App_Controller_Default
{
    /**
     *
     * @var Model_Mapper_Acao
     */
    protected $_mapper;
    
    /**
     * (non-PHPdoc)
     * @see Zend_Controller_Action::init()
     */
    public function init()
    {
        $this->_mapper = new Model_Mapper_Acao();
    }
        
    /**
     *
     * @param string $action
     * @return Master_Form_Acao
     */
    protected function _getForm( $action )
    {
        if ( null == $this->_form ) {

            $this->_form = new Master_Form_Acao();
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
	
	$this->view->privilegios = $this->_mapper->listPrivilegios( $id );
	
	$this->view->form->getElement( 'acao_descricao' )->setAttrib( 'readOnly', true );
	$this->view->form->getElement( 'tela_id' )->setAttrib( 'readOnly', true );
    }
    
    /**
     * 
     */
    public function deletePrivilegioAction()
    {
	$result = $this->_mapper->deletePrivilegio( $this->_getAllParams() );
	$this->_helper->json( $result );
    }
}