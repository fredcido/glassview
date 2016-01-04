<?php

/**
 *
 * @author Helion Mendanha
 */
class App_View_Helper_Translate extends Zend_View_Helper_Abstract
{
    /**
     *
     * @return Zend_Translate
     */
    public function translate()
    {
        $translate = Zend_Registry::get('Zend_Translate');
        
        return $translate;
    }
}