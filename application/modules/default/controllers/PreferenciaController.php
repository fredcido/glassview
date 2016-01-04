<?php

/**
 * 
 * @version $Id $
 */
class Default_PreferenciaController extends App_Controller_Default
{
    /**
     *
     * @var type 
     */
    protected $_mapper;
    
    public function init()
    {
        $this->_mapper = new Model_Mapper_Preferencia();
    }

     /**
     *
     * @param string $action
     * @return Admin_Form_Preferencia
     */
    protected function _getForm( $action )
    {
        if ( null == $this->_form ) {

            $this->_form = new Default_Form_Preferencia();
            $this->_form->setAction( $action );
        }

        return $this->_form;
    }
    
    /**
     * 
     */
    public function indexAction()
    {
	$id = Zend_Auth::getInstance()->getIdentity()->usuario_id;
	$this->_setParam( 'id', $id );
	
	
	$this->_forward( 'edit' );
    }
}
