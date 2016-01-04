<?php

/**
 *
 * @version $Id: ModuloController.php 331 2012-02-23 13:52:30Z helion $
 */
class Master_ModuloController extends App_Controller_Default
{
    /**
     *
     * @var Model_Mapper_Modulo 
     */
    protected $_mapper;
    
    /**
     * (non-PHPdoc)
     * @see Zend_Controller_Action::init()
     */
    public function init()
    {
        $this->_mapper = new Model_Mapper_Modulo();
    }
        
    /**
     *
     * @param string $action
     * @return Master_Form_Modulo
     */
    protected function _getForm( $action )
    {
        if ( null == $this->_form ) {

            $this->_form = new Master_Form_Modulo();
            $this->_form->setAction( $action );
        }

        return $this->_form;
    }
}