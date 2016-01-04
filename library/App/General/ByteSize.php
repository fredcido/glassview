<?php

/**
 *  
 */
class App_General_ByteSize
{
	/**
	 * Unidade com seu respectivo expoente
	 * 
	 * @var array
	 */
	protected static $_unit = array(
		'B' => 0,
		'K' => 10,
		'M' => 20,
		'G' => 30,
		'T' => 40,
		'P' => 50
	);
		
	/**
	 * 
	 * @access 	public
	 * @param 	float $value
	 * @param 	string $of
	 * @param 	string $for
	 * @return 	float
	 */
	public static function convert ( $value, $of, $for )
	{
		try {
		
			switch ( true ) {
			
				case !is_numeric($value):
					throw new Exception('Not a number');
					break;
					
				case !in_array( $of, array_keys(self::$_unit) ):
					throw new Exception('Unit is not defined: ' . $of);
					break;
					
				case !in_array( $for, array_keys(self::$_unit) ):
					throw new Exception('Unit is not defined: ' . $for);
					break;
					
			}
	
			return $value * pow(2, self::$_unit[$of]) / pow(2, self::$_unit[$for]);
			
		} catch ( Exception $e ) {
			
			die( $e->getMessage() );
			return $value;
			
		}
	}
	
	/**
	 * 
	 * @access 	public
	 * @param 	int $bytes
	 * @return 	string
	 */
	public static function calculator ( $bytes )
	{
		for ( $i = 0; $bytes > 1024; $i++ )
			$bytes /= 1024;

		$keys = array_keys( self::$_unit );
		$unit = ('B' === $keys[$i]) ? 'bytes' : ($keys[$i] . 'B');
			
		return number_format( $bytes, 2, ',', '' ) . ' ' . $unit;
	}
}