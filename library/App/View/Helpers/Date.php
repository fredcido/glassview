<?php

/**
 * 
 */
class App_View_Helper_Date extends Zend_View_Helper_Abstract
{
	/**
	 * 
	 * @var Zend_Date
	 */
	protected $_date;
	
	/**
	 * 
	 * @access 	public
	 * @param 	string $date
	 * @param 	string $format
	 * @return 	string
	 */
	public function date ( $date = null, $format = 'dd/MM/yyyy' )
	{
		if ( is_null($date) ) {
    		
			$this->_date = new Zend_Date();
    		
		} else {
			
			if ( is_null($this->_date) )
				$this->_date = new Zend_Date( $date );
			else
				$this->_date->set( $date );
				
		}
		
		return $this->_date->toString( $format );
    }
}