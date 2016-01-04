<?php

/**
 * 
 */
abstract class App_Controller_Default extends Zend_Controller_Action
{

    /**
     * 
     * @var mixed
     */
    protected $_form;

    /**
     * 
     * @var Zend_Session_Namespace
     */
    protected $_session;

    /**
     * 
     * @var Zend_Config_Ini
     */
    protected $_config;

    /**
     * (non-PHPdoc)
     * @see Zend_Controller_Action::preDispatch()
     */
    public function preDispatch()
    {
	$this->_config = Zend_Registry::get( 'config' );
	$this->_session = new Zend_Session_Namespace( $this->_config->geral->appid );

	$this->_hookAction( 'pre' );
    }

    /**
     * (non-PHPdoc)
     * @see Zend_Controller_Action::postDispatch()
     */
    public function postDispatch()
    {
	$this->_hookAction( 'post' );
    }

    /**
     * 
     * @access 	protected
     * @param 	string $moment
     * @return 	void
     */
    protected function _hookAction( $moment )
    {
	$action = $this->getRequest()->getActionName();
	$method = $this->_parseActionName( $action ) . ucfirst( $moment ) . 'Hook';

	// Ve se existe o gancho para action
	if ( method_exists( $this, $method ) )
	    call_user_func( array( $this, $method ) );
    }

    /**
     * 
     * @access 	protected
     * @param 	string $action
     * @return 	string
     */
    protected function _parseActionName( $action )
    {
	$pieces = explode( '-', $action );

	$init = array_shift( $pieces );

	return $init . implode( '', array_map( 'ucfirst', $pieces ) );
    }

    /**
     * 
     * @access public
     * @return void
     */
    public function indexAction()
    {
	
    }
    
    /**
     * 
     */
    public function listAction()
    {
        $data = $this->_mapper->fetchGrid();
        
        $this->_helper->json( $data );
    }

    /**
     * 
     * @access 	public
     * @return 	void
     */
    public function formAction()
    {    	
	$this->_helper->layout()->disableLayout();

	$form = $this->_getForm( $this->_helper->url( 'save' ) );

        $translate = Zend_Registry::get('Zend_Translate');

        $form->setTranslator($translate);
	
        $this->view->form = $form;
    }

    /**
     * 
     * @access public
     * @return void
     */
    public function saveAction()
    {
	$this->_helper->viewRenderer->setRender( 'form' );

	if ( $this->getRequest()->isXmlHttpRequest() )
	    $this->_saveAjax();
	else
	    $this->_save();
    }

    /**
     * 
     * @access protected
     * @return void
     */
    protected function _save()
    {
	$form = $this->_getForm( $this->_helper->url( 'save' ) );

	if ( $this->getRequest()->isPost() ) {

	    if ( $form->isValid( $this->getRequest()->getPost() ) ) {

		$this->_mapper->setData( $form->getValues() );

		$result = $this->_mapper->save();

		$this->_helper->FlashMessenger( $this->_mapper->getMessage() );

		if ( $result )
		    $this->_helper->redirector->goToSimple( 'index' );
	    } else {

		$message = new App_Message();
		$message->addMessage( $this->_config->messages->warning, App_Message::WARNING );

		$this->_helper->FlashMessenger( $message );
	    }
	}

	$this->view->form = $form;
    }

    /**
     * 
     * @access protected
     * @return void
     */
    protected function _saveAjax()
    {
	$form = $this->_getForm( $this->_helper->url( 'save' ) );

	if ( $this->getRequest()->isPost() ) {

	    if ( $form->isValid( $this->getRequest()->getPost() ) ) {

		$this->_mapper->setData( $form->getValues() );

		$result = $this->_mapper->save();

		$message = $this->_mapper->getMessage()->toArray();

		$data = array(
		    'status'	  => (boolean)$result,
		    'id'	  => $result,
		    'description' => $message,
		    'data' => $form->getValues()
		);

		$this->_helper->json( $data );
	    } else {

		$message = new App_Message();
		$message->addMessage( $this->_config->messages->warning, App_Message::WARNING );

		$data = array(
		    'status' => false,
		    'description' => $message->toArray(),
		    'errors' => $form->getMessages()
		);

		$this->_helper->json( $data );
	    }
	}
    }

    /**
     * 
     * @access public
     * @return void
     */
    public function saveUploadAction()
    {
	$this->_helper->layout->disableLayout();

	$form = $this->_getForm( $this->_helper->url( 'save-upload' ) );

	if ( $this->getRequest()->isPost() ) {

	    if ( $form->isValid( $this->getRequest()->getPost() ) ) {

		$this->_mapper->setData( $form->getValues() );

		$result = $this->_mapper->save();

		$message = $this->_mapper->getMessage()->toArray();

		$data = array(
		    'status' => (bool) $result,
		    'id' => $result,
		    'description' => $message,
		    'data' => $form->getValues()
		);
	    } else {

		$message = new App_Message();

		$message->addMessage( $this->_config->messages->warning, App_Message::WARNING );

		$data = array(
		    'status' => false,
		    'description' => $message->toArray(),
		    'errors' => $form->getMessages()
		);
	    }
	}

	$this->view->result = json_encode( $data );
    }

    /**
     * 
     * @access public
     * @return void
     */
    public function editAction()
    {
	$this->_helper->viewRenderer->setRender( 'form' );

	$form = $this->_getForm( $this->_helper->url( 'save' ) );

	$id = $this->_getParam( 'id', 0 );

	$this->_mapper->setData( array( 'id' => $id ) );

	$row = $this->_mapper->fetchRow();

	if ( empty( $row ) )
	    $this->_helper->redirector->goToSimple( 'index' );

	$data = $row->toArray();

	$form->populate( $data );

	$this->view->data = $data;
	$this->view->form = $form;
    }

}