<?php

/**
 * 
 * @version $Id: Recovery.php 263 2012-02-15 20:48:40Z helion $
 */
class Default_Form_Recovery extends Zend_Form
{
	/**
	 * (non-PHPdoc)
	 * @see Zend_Form::init()
	 */
	public function init()
	{
		$elements = array();
		
		$decorators = array('ViewHelper');
		
		$elements[] = $this->createElement('text', 'email')
			->addFilter('StripTags')
			->addValidator('EmailAddress')
			->setDecorators($decorators)
			->setAttrib('class', 'text')
			->setAttrib('placeholder', 'Email')
			->setRequired(true);
			
		$this->addElements($elements);
	}
}