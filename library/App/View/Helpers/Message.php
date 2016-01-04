<?php

/**
 * 
 */
class App_View_Helper_Message extends Zend_View_Helper_Abstract
{
	/**
	 * 
	 * @access public
	 * @return mixed
	 */
	public function message()
	{
		$helperFlashMessenger = Zend_Controller_Action_HelperBroker::getStaticHelper('flashMessenger');
        
		switch ( true ) {
			
			case $helperFlashMessenger->hasMessages():
				$message = array_shift( $helperFlashMessenger->getMessages() );
				$helperFlashMessenger->clearMessages();
				break;
				
			case $helperFlashMessenger->hasCurrentMessages():
				$message = array_shift( $helperFlashMessenger->getCurrentMessages() );
				$helperFlashMessenger->clearCurrentMessages();
				break;
				
			default:
				$message = null;
				
        }

		return $message;
    }
}