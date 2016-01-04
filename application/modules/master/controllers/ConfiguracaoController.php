<?php

/**
 *
 * @version $Id: ConfiguracaoController.php 252 2012-02-15 03:21:42Z fred $
 */
class Master_ConfiguracaoController extends App_Controller_Default
{
    /**
     *
     * @var Model_Mapper_Configuracao
     */
    protected $_mapper;
    
    /**
     * (non-PHPdoc)
     * @see Zend_Controller_Action::init()
     */
    public function init()
    {
        $this->_mapper = new Model_Mapper_Configuracao();
    }
    
    /**
     * 
     */
    public function indexAction()
    {
        $this->view->form = $this->_getForm( $this->_helper->url( 'save' ) );
        $this->view->form->populate( $this->_mapper->fetchRow() );
    }
    
     /**
     *
     * @param string $action
     * @return Master_Form_Modulo
     */
    protected function _getForm( $action )
    {
        if ( null == $this->_form ) {

            $this->_form = new Master_Form_Configuracao();
            $this->_form->setAction( $action );
        }

        return $this->_form;
    }
}