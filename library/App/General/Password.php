<?php

/**
 * 
 */
class App_General_Password
{
	/**
	 * 
	 * @access 	public
	 * @param 	int $length
	 * @param 	boolean $lower
	 * @param 	boolean $upper
	 * @param 	boolean $number
	 * @return 	string
	 */
	public static function getRand ( $length = 6, $lower = true, $upper = true, $number = true )
	{
		$password = null;
		
		while ( $length > 0 ) {
			
			$character = array();
			
			switch ( true ) {
				
				case $lower:
					array_push( $character, chr(rand(97, 122)) );
					
				case $upper:
					array_push( $character, chr(rand(65, 90)) );
					
				case $number:
					array_push( $character, chr(rand(48, 57)) );
				
			}
			
			$indice = rand( 0, (count($character) - 1) );

			$password .= $character[$indice];
			
			$length--;
			
		}
		
		return $password;
	}	
}