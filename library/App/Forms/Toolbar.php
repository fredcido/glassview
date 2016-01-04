<?php

abstract class App_Forms_Toolbar
{
    /**
     *
     * @var array
     */
    protected static $_buttons = array();
    
    /**
     *
     * @var array
     */
    protected static $_elements = array();
    
    /**
     *
     * @var Zend_Form_DisplayGroup
     */
    protected static $_displayGroup;
    
    /**
     *
     * @var Zend_Dojo_Form
     */
    protected static $_form;
    
    /**
     *
     * @var array
     */
    protected static $_decoratorDisplay = array(
	'FormElements',
	array( 'wrapper' => 'HtmlTag', array( 'tag' => 'div', 'class' => 'toolbar' ) )
    );

    /**
     *
     * @param array $buttons
     * @return null|Zend_Form_DisplayGroup
     */
    public static function build( array $buttons = null )
    {
	self::$_buttons = $buttons;
	
	self::_createButtons();
	
	self::_createDisplayGroup();
	
	return self::$_displayGroup;
    }
    
    /**
     * 
     */
    protected static function _createButtons()
    {
	self::$_form = new Zend_Dojo_Form();
	
	foreach ( self::$_buttons as $button )
	    self::_createButton( $button );
    }
    
    /**
     *
     * @param array $button
     * @return boolean 
     */
    protected static function _createButton( array $button )
    {        
	if ( !empty( $button['action'] ) && !self::_checkAccess( $button['action'] ) )
	    return false;
	
	if ( empty( $button['id'] ) )
	    $button['id'] = App_General_Password::getRand();
	
	if ( empty( $button['label'] ) )
	    $button['label'] = 'Unknown';
	
	if ( empty( $button['icon'] ) )
	    $button['icon'] = '';
	
	if ( empty( $button['click'] ) )
	    $button['click'] = '';
	
	if ( empty( $button['disabled'] ) )
	    $button['disabled'] = '';
	
	self::$_elements[] = self::$_form->createElement( 'Button', $button['id'] )
					 ->setLabel( $button['label'] )
					 ->setDijitParam( 'iconClass', 'customIcon ' . $button['icon'] )
					 ->setIgnore( true )
					 ->setDecorators( array( 'DijitElement' ) )
					 ->setAttrib( 'onClick', $button['click'] )
					 ->setAttrib( 'disabled', $button['disabled'] );
	
	return true;
    }
    
    /**
     * 
     * @access protected
     * @param int $action
     * @return boolean
     */
    protected static function _checkAccess( $action )
    {
	$session = new Zend_Session_Namespace( Zend_Registry::get('config')->geral->appid );

	return $session->acl->isAllowedToolbar( $action );
    }
    
    /**
     * 
     */
    protected static function _createDisplayGroup()
    {
	if ( !empty ( self::$_elements ) ) {
            
            // instancia form e cria o Display Group
            self::$_form->addDisplayGroup( self::$_elements, 'toolbar' );

            self::$_displayGroup = self::$_form->getDisplayGroup( 'toolbar' );

            // Define decorators padrao
            self::$_displayGroup->setDecorators( self::$_decoratorDisplay );
	}
    }
}
