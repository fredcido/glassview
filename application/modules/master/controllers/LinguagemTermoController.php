<?php

/**
 *
 * @version $Id: LinguagemTermoController.php 330 2012-02-23 12:42:19Z helion $
 */
class Master_LinguagemTermoController extends App_Controller_Default
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
        $this->_mapper = new Model_Mapper_LinguagemTermo();
    }
    
     /**
     *
     * @param string $action
     * @return Master_Form_Modulo
     */
    protected function _getForm( $action )
    {
        if ( null == $this->_form ) {

            $this->_form = new Master_Form_LinguagemTermo();
            $this->_form->setAction( $action );
        }

        return $this->_form;
    }
}