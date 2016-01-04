<?php

/**
 * Description of Modulo
 *
 * @version $Id: UsuarioController.php 383 2012-03-02 12:42:19Z helion $
 */
class Admin_UsuarioController extends App_Controller_Default
{
    /**
     *
     * @var type 
     */
    protected $_mapper;
    
    public function init()
    {
        $this->_mapper = new Model_Mapper_Usuario();
    }


     /**
     *
     * @param string $action
     * @return Admin_Form_Usuario
     */
    protected function _getForm( $action )
    {
        if ( null == $this->_form ) {

            $this->_form = new Admin_Form_Usuario();
            $this->_form->setAction( $action );
        }

        return $this->_form;
    }

    /**
     *
     */
    public function editPostHook()
    {
        $this->view->form->getElement( 'usuario_login' )->setAttrib( 'readOnly', true );
        $this->view->form->getElement( 'usuario_status' )->setAttrib( 'readOnly', null );
        $this->view->form->getElement( 'usuario_senha' )->setRequired( false )->setValue( null );
        $this->view->form->getElement( 'usuario_senha2' )->setRequired( false )->setValue( null );
	
	if ( 'N' == $this->view->form->getElement( 'usuario_nivel' )->getValue() )
	    $this->view->form->getElement( 'perfil_id' )->setAttrib( 'disabled', null );
    }
}