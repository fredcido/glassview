<?php

/**
 * 
 * @version $Id: Auth.php 517 2012-04-27 14:16:15Z helion $
 */
class Default_Form_Auth extends Zend_Form
{
	/**
	 * (non-PHPdoc)
	 * @see Zend_Form::init()
	 */
    public function init()
    {
    	$elements = array();
        
        $t = $this->getView()->translate();
    	
    	$decorators = array('ViewHelper');
    	
    	$elements[] = $this->createElement('text', 'usuario')
    		->addFilter('StripTags')
    		->setDecorators($decorators)
    		->setAttrib('class', 'text')
    		->setAttrib('placeholder', $t->_('Usuário') )
    		->setRequired(true);
    		
    	$elements[] = $this->createElement('password', 'senha')
    		->addFilter('StripTags')
    		->setDecorators($decorators)
    		->setAttrib('class', 'text')
    		->setAttrib('placeholder', $t->_('Senha') )
    		->setRequired(true);
    		
    	$elements[] = $this->createElement('checkbox', 'remember')
    		        ->setDecorators($decorators)
			//->setAttrib('class', 'remember')
			->setValue(0);
			
		$this->addElements($elements);
    }
}