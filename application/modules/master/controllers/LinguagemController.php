<?php

/**
 *
 * @version $Id: LinguagemController.php 329 2012-02-23 11:26:32Z helion $
 */
class Master_LinguagemController extends App_Controller_Default
{
    /**
     *
     * @var type 
     */
    protected $_mapper;
    
    /**
     * (non-PHPdoc)
     * @see Zend_Controller_Action::init()
     */
    public function init()
    {
        $this->view->t = Zend_Registry::get('Zend_Translate');
        $this->_mapper = new Model_Mapper_Linguagem();
    }
    
     /**
     *
     * @param string $action
     * @return Master_Form_Modulo
     */
    protected function _getForm( $action )
    {
        if ( null == $this->_form ) {

            $this->_form = new Master_Form_Linguagem();
            $this->_form->setAction( $action );
        }

        return $this->_form;
    }
    
    /**
     * 
     */
    public function editPostHook()
    {
        $this->view->form->getElement( 'linguagem_status' )->setAttrib( 'readOnly', null );
    }
}