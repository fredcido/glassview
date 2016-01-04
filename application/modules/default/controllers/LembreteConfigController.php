<?php

/**
 * 
 * @version $Id $
 */
class Default_LembreteConfigController extends App_Controller_Default
{
    /**
     *
     * @var type 
     */
    protected $_mapper;
    
    /**
     * 
     */
    public function init()
    {
        $this->_mapper = new Model_Mapper_LembreteConfig();
    }

     /**
     *
     * @param string $action
     * @return Admin_Form_LembreteConfig
     */
    protected function _getForm( $action )
    {
        if ( null == $this->_form ) {

            $this->_form = new Default_Form_LembreteConfig();
            $this->_form->setAction( $action );
        }

        return $this->_form;
    }
    
    /**
     * 
     */
    public function indexAction()
    {
	$this->_forward( 'form' );
    }
    
    /**
     * 
     */
    public function listPerfisAction()
    {
	$perfis = $this->_mapper->listPerfis( $this->_getParam( 'tipo' ) );
	$this->_helper->json( $perfis );
    }
}
