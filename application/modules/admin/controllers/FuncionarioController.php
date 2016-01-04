<?php

/**
 * Description of Modulo
 *
 * @version $Id: FuncionarioController.php 276 2012-02-17 13:53:20Z fred $
 */
class Admin_FuncionarioController extends App_Controller_Default
{
    /**
     *
     * @var type 
     */
    protected $_mapper;
    
    public function init()
    {
        $this->_mapper = new Model_Mapper_Funcionario();
    }
    
     /**
     *
     * @param string $action
     * @return Admin_Form_Usuario
     */
    protected function _getForm( $action )
    {
        if ( null == $this->_form ) {

            $this->_form = new Admin_Form_Funcionario();
            $this->_form->setAction( $action );
        }

        return $this->_form;
    }

    /**
     *
     */
    public function editPostHook()
    {
	$form = $this->view->form;
	$data = $this->view->data;
	
	if ( !empty( $data['usuario_id'] ) ) {
	    
	    $form->getElement( 'usuario_senha' )->setAttrib( 'readOnly', null );
	    $form->getElement( 'usuario_senha2' )->setAttrib( 'readOnly', null );
	    $form->getElement( 'perfil_id' )->setAttrib( 'readOnly', null )->setRequired( true );
	    $form->getElement( 'usuario' )->setAttrib( 'readOnly', true )->setValue( 'S' );
	    $form->getElement( 'usuario_nivel' )->setAttrib( 'readOnly', null )->setRequired( true );
	}
    }
}