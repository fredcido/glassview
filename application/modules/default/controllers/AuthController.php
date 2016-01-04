<?php

/**
 * 
 * @version $Id: AuthController.php 17 2012-01-23 10:52:03Z ze $
 */
class Default_AuthController extends App_Controller_Default
{

    /**
     * 
     * @var Default_Form_Auth
     */
    protected $_formAuth;

    /**
     * 
     * @var Default_Form_Recovery
     */
    protected $_formRecovery;

    /**
     * (non-PHPdoc)
     * @see Zend_Controller_Action::init()
     */
    public function init()
    {
	$this->_helper->layout()->setLayout( 'auth' );
    }

    /**
     * 
     * @access protected
     * @return Default_Form_Auth
     */
    protected function _getFormAuth()
    {
	if ( is_null( $this->_formAuth ) ) {

	    $this->_formAuth = new Default_Form_Auth(
			    array(
				'action' => $this->_helper->url( 'login' ),
				'method' => Zend_Form::METHOD_POST,
				'class' => 'form',
				'id' => 'form-auth',
				'onSubmit' => 'return loginSistema();'
			    )
	    );
	}

	return $this->_formAuth;
    }

    /**
     * 
     * @access protected
     * @return Default_Form_Recovery
     */
    protected function _getFormRecovery()
    {
	$form = new Default_Form_Recovery(
			array(
			    'action' => $this->_helper->url( 'recovery' ),
			    'method' => Zend_Form::METHOD_POST,
			    'class' => 'form',
			    'id' => 'form-recovery',
			    'onSubmit' => 'return recoverySistema();'
			)
	);

	return $form;
    }

    /**
     * 
     * @access public
     * @return void
     */
    public function indexAction()
    {
	$this->view->formAuth = $this->_getFormAuth();
	$this->view->formRecovery = $this->_getFormRecovery();
    }

    /**
     * 
     * @access public
     * @return void
     */
    public function loginAction()
    {
	if ( $this->getRequest()->isPost() ) {

	    $form = $this->_getFormAuth();

	    if ( $form->isValid( $this->getRequest()->getPost() ) ) {

		$mapper = new Default_Model_Mapper_Auth();
		$mapper->setData( $form->getValues() );

		$result = array(
		    'valid' => $mapper->login(),
		    'message' => $mapper->getMessage()->toArray()
		);
	    } else {

		$message = new App_Message();
		$message->addMessage( $this->_config->messages->warning, App_Message::WARNING );

		$result = array(
		    'valid' => false,
		    'message' => $message->toArray(),
		    'error' => $form->getMessages()
		);
	    }

	    $this->_helper->json( $result );
	}
    }

    /**
     * 
     * @access public
     * @return void
     */
    public function logoutAction()
    {
	$auth = Zend_Auth::getInstance();

	$auth->getStorage()->clear();
	$auth->clearIdentity();

	Zend_Session::destroy();

	$name = 'auth_' . Zend_Registry::get( 'config' )->geral->appid;

	setcookie( $name, false, time(), '/' );

	$this->_helper->redirector->gotoSimple( 'index' );
    }

    /**
     * 
     * @access public
     * @return void
     */
    public function recoveryAction()
    {
	$result = array('valid' => false);

	if ( $this->getRequest()->isPost() ) {

	    $form = $this->_getFormRecovery();

	    if ( $form->isValid( $this->getRequest()->getPost() ) ) {

		$mapper = new Default_Model_Mapper_Auth();

		$mapper->setData( $this->getRequest()->getPost() );

		$result['valid'] = $mapper->recovery();
	    }
	}

	$this->_helper->json( $result );
    }

    /**
     * 
     * @access public
     * @return void
     */
    public function passwordAction()
    {
	$hash = $this->_getParam( 'hash', null );

	if ( empty( $hash ) )
	    $this->_helper->redirector->gotoSimple( 'index' );

	$mapper = new Default_Model_Mapper_Auth();

	$mapper->setData( array('hash' => $hash) );

	$this->view->result = $mapper->password();

	$this->_helper->viewRenderer->setRender( 'password' );
    }

}