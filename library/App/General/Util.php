<?php

abstract class App_General_Util
{
    /**
     *
     * @param string $fileName
     * @param string $content 
     */
    public static function toExcell( $fileName, $content )
    {
        header ( "Content-type: application/vnd.ms-excel" );
	header ( "Cache-Control: no-cache, must-revalidate" );
	header ( "Pragma: no-cache" );
	header ( "Content-Disposition: attachment; filename=" . $fileName . ".xls" );
	
	echo utf8_decode($content);
    }
}