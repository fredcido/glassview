<?php

/**
 * 
 * @version $Id: Configuracao.php 188 2012-02-12 20:31:54Z fred $
 */
class Model_Mapper_Configuracao extends App_Model_Mapper_Abstract
{
    /**
     *
     * @return array
     */
    public function fetchRow()
    {
        return $this->_config->toArray();
    }
    
    public function save()
    {
        try {
            
            $filename = APPLICATION_PATH . '/configs/config.ini';
            
            $configWriter = new Zend_Config_Writer_Ini();
            $configWriter->write( $filename , new Zend_Config( $this->_data ) );
            
            return true;
            
        } catch ( Exception $e ) {
            return false;
        }
    }
}