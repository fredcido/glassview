<?php

/**
 *
 * @version $Id: AuditoriaController.php 252 2012-02-15 03:21:42Z fred $
 */
class Master_AuditoriaController extends App_Controller_Default
{
    /**
     *
     * @var Model_Mapper_Auditoria
     */
    protected $_mapper;
    
    /**
     * (non-PHPdoc)
     * @see Zend_Controller_Action::init()
     */
    public function init()
    {
        $this->_mapper = new Model_Mapper_Auditoria();
    }

    /**
     *
     * @param string $action
     * @return Master_Form_Acao
     */
    protected function _getForm( $action )
    {
        if ( null == $this->_form ) {

            $this->_form = new Master_Form_Auditoria();
            $this->_form->setAction( $action );
        }

        return $this->_form;
    }
}