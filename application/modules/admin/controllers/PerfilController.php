<?php

/**
 * Description of Modulo
 *
 * @version $Id: PerfilController.php 252 2012-02-15 03:21:42Z fred $
 */
class Admin_PerfilController extends App_Controller_Default
{
    /**
     *
     * @var type 
     */
    protected $_mapper;
    
    public function init()
    {
        $this->_mapper = new Model_Mapper_Perfil();
    }

     /**
     *
     * @param string $action
     * @return Admin_Form_Perfil
     */
    protected function _getForm( $action )
    {
        if ( null == $this->_form ) {

            $this->_form = new Admin_Form_Perfil();
            $this->_form->setAction( $action );
        }

        return $this->_form;
    }

    /**
     *
     */
    public function editPostHook()
    {
        $this->view->form->getElement( 'perfil_status' )->setAttrib( 'readOnly', null );
    }
}