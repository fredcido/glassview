<?php

/**
 * 
 */
class App_Validate_PasswordConfirm extends Zend_Validate_Abstract
{
	/**
	 * 
	 * @var string
	 */
	const NOT_MATCH = 'notMatch';
	
	/** 
	 * 
	 * @var array
	 */
	protected $_messageTemplates = array(
		self::NOT_MATCH => 'Senhas não conferem.'
	);
	
	/**
	 * (non-PHPdoc)
	 * @see Zend_Validate_Interface::isValid()
	 */
	public function isValid ( $value, $context = null )
	{
		$value = (string) $value;
		
		$this->_setValue( $value );
		
		if ( is_array( $context ) ) {
			if ( isset($context['usuario_senha']) && ($value == $context['usuario_senha2']) ) {
				return true;
			}
		} elseif ( is_string($context) && ($value == $context) ) {
			return true;
		}
		
		$this->_error( self::NOT_MATCH );
		
		return false;
    }

}

