<?php

/**
 * Description of Modulo
 *
 * @version $Id: TerceiroController.php 252 2012-02-15 03:21:42Z fred $
 */
class Admin_TerceiroController extends App_Controller_Default
{
    /**
     *
     * @var type 
     */
    protected $_mapper;
    
    public function init()
    {
        $this->_mapper = new Model_Mapper_Terceiro();
    }
    
     /**
     *
     * @param string $action
     * @return Admin_Form_Cargo
     */
    protected function _getForm( $action )
    {
        if ( null == $this->_form ) {

            $this->_form = new Admin_Form_Terceiro();
            $this->_form->setAction( $action );
        }

        return $this->_form;
    }

    /**
     *
     */
    public function editPostHook()
    {
    }
}