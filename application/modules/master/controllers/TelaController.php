<?php

/**
 *
 * @version $Id: TelaController.php 252 2012-02-15 03:21:42Z fred $
 */
class Master_TelaController extends App_Controller_Default
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
        $this->_mapper = new Model_Mapper_Tela();
    }
    
     /**
     *
     * @param string $action
     * @return Master_Form_Modulo
     */
    protected function _getForm( $action )
    {
        if ( null == $this->_form ) {

            $this->_form = new Master_Form_Tela();
            $this->_form->setAction( $action );
        }

        return $this->_form;
    }
    
    /**
     * 
     */
    public function editPostHook()
    {
        $this->view->form->getElement( 'tela_path' )->setAttrib( 'readOnly', true );
        $this->view->form->getElement( 'modulo_id' )->setAttrib( 'readOnly', true );
    }
}