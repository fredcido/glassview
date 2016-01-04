<?php

/**
 *
 */
class App_Model_DbTable_Factory
{
	/**
	 * 
	 * @var array
	 */
	protected static $_dbTable = array();

	/**
	 * 
	 * @access private
	 * @return void
	 */
	private function __construct ()
	{
	}
	
	/**
	 * 
	 * @access 	public
	 * @param 	string $class
	 * @param 	string $module
	 * @return 	App_Model_DbTable_Abstract 
	 * @throws 	Exception
	 */
	public static function get ( $class, $module = null )
	{
		$className = null;
		
		if ( !is_null($module) )
			$className .= ucfirst($module) . '_';
		
		//$className .= 'Model_DbTable_' . ucfirst( $class );
		$className .= 'Model_DbTable_' . implode('', array_map('ucfirst', explode('_', $class)));
		
		if ( !class_exists( $className ) ) 
			throw new Exception( 'class not found: ' . $class );
						
		if ( !in_array( $className, array_keys( self::$_dbTable ) ) )
			self::$_dbTable[$className] = new $className();
			
		return self::$_dbTable[$className];
	} 
}