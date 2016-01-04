<?php

abstract class App_Forms_Default extends Zend_Dojo_Form
{
    /**
     *
     * @var boolean
     */
    protected $_renderDefaultFormDecorators = true;
    
    /**
     *
     * @var boolean
     */
    protected $_renderDefaultDecorators = true;
    
    /**
     *
     * @var string
     */
    protected $_defaultDecorators = 'default';
    
    /**
     *
     * @var boolean
     */
    protected $_renderToolbar = true;
    
    /**
     *
     * @var boolean
     */
    protected $_renderDefaultButtons = true;
    
    /**
     *
     * @var array
     */
    protected $_customButtons = array();
    
    /**
     *
     * @var array 
     */
    protected $_buttonsToolbar = array();
    
    /**
     *
     * @var array
     */
    protected $_dojoElements = array(
	'Button',
	'CheckBox',
	'ComboBox',
	'CurrencyTextBox',
	'DateTextBox',
	'Editor',
	'FilteringSelect',
	'HorizontalSlider',
	'NumberSpinner',
	'NumberTextBox',
	'PasswordTextBox',
	'RadioButton',
	'SimpleTextarea',
	'Slider',
	'SubmitButton',
	'TextBox',
	'TimeTextBox',
	'ValidationTextBox',
	'VerticalSlider'
    );
    
    /**
     *
     * @var string
     */
    protected $_pregPrefix = '/^.*?_([A-Za-z0-9]+)$/i';
    
    /**
     *
     * @var array
     */
    protected $_defaultElementDecorator = array(
	'DijitElement',
	array(
	    array( 'element' => 'HtmlTag' ),
	    array( 'tag' => 'span', 'class' => 'input' )
	),
	array( 'Label', array( 'requiredSuffix' => '*' ) ),
	array(
	    array( 'wrapper' => 'HtmlTag' ),
	    array( 'tag' => 'div', 'class' => 'element' )
	)
    );

    
    /**
     *
     * @var array
     */
    protected $_defaultFormDecorator = array(
	'FormElements',
	array( 'wrapper' => 'HtmlTag', array( 'tag' => 'div', 'class' => 'elementosForm' ) ),
	array( 'DijitForm', array( 'class' => 'customForm' ) )
    );
    
    /**
     *
     * @var array
     */
    protected $_defaultFormDecoratorColumns = array(
	'FormElements',
	array( 'wrapper' => 'HtmlTag', array( 'tag' => 'div', 'class' => 'elementosForm elementosFormColumns' ) ),
	array( 'DijitForm', array( 'class' => 'customForm' ) )
    );
    
    
    /**
     * 
     */
    protected function _setDecoratorsDefault()
    {
	$elements = $this->getElements();
	
	$dojoElements = array_map( 'strtolower', $this->_dojoElements );
	
	foreach ( $elements as $element ) {
	    
	    $type = get_class( $element );
	    
	    if ( preg_match( $this->_pregPrefix, $type, $match ) ) {
		
		$type = strtolower( $match[1] );
		
		if ( 'hidden' == $type )
		    $element->setDecorators( array( 'ViewHelper' ) );
		else if ( in_array( $type, $dojoElements  ) )
		    $element->setDecorators( $this->_defaultElementDecorator );
		else {

		    $decorators = $this->_defaultElementDecorator;
		    array_shift( $decorators );
		    array_unshift( $decorators, 'ViewHelper' );

		    $element->setDecorators( $decorators );
		}
	    }
	}
	
	$this->_decoraForm();
    }
    
    /**
     * 
     */
    protected function _setDecoratorsColumns()
    {
	$elements = $this->getElements();
	
	$this->_defaultElementDecorator[] = array(
	    array( 'float' => 'HtmlTag' ),
	    array( 'tag' => 'div', 'class' => 'columns' )
	);
	
	$dojoElements = array_map( 'strtolower', $this->_dojoElements );
	
	foreach ( $elements as $element ) {
	    
	    $type = get_class( $element );
	    
	    if ( preg_match( $this->_pregPrefix, $type, $match ) ) {
		
		$type = strtolower( $match[1] );
		
		if ( 'hidden' == $type )
		    $element->setDecorators( array( 'ViewHelper' ) );
		else if ( in_array( $type, $dojoElements  ) )
		    $element->setDecorators( $this->_defaultElementDecorator );
		else {

		    $decorators = $this->_defaultElementDecorator;
		    array_shift( $decorators );
		    array_unshift( $decorators, 'ViewHelper' );

		    $element->setDecorators( $decorators );
		}
	    }
	}
	
	
	$this->_decoraForm( true );
    }
    
    /**
     * 
     */
    protected function _decoraForm( $column = false)
    {
	if ( $this->_renderDefaultFormDecorators ) {
	    
	    if ( $column )
		$this->setDecorators( $this->_defaultFormDecoratorColumns );
	    else
		$this->setDecorators( $this->_defaultFormDecorator );
	}
    }
    
    /**
     *
     * @param boolean $flag 
     */
    public function setRenderDefaultDecorators( $flag = false )
    {
	$this->_renderDefaultDecorators = (boolean)$flag;
	return $this;
    }
    
    /**
     *
     * @param boolean $flag 
     */
    public function setRenderDefaultFormDecorators( $flag = false )
    {
	$this->_renderDefaultFormDecorators = (boolean)$flag;
	return $this;
    }
    
    /**
     *
     * @param boolean $flag 
     */
    public function setRenderDefaultToolbar( $flag = false )
    {
	$this->_renderToolbar = (boolean)$flag;
	return $this;
    }
    
    /**
     *
     * @param boolean $flag 
     */
    public function setRenderDefaultButtons( $flag = false )
    {
        $this->_renderDefaultButtons = (boolean)$flag;
	return $this;
    }
    
    /**
     *
     * @param string $decorator 
     */
    public function setDefaultDecorator( $decorator )
    {
	if ( !in_array( $decorator, array( 'default', 'columns') ) )
	    throw new Exception( 'Tipo de decorator inválido.' );
	
	$this->_defaultDecorators = $decorator;
	return $this;
    }
    
    /**
     *
     * @param array $buttons 
     */
    public function setCustomButtons( array $buttons )
    {
        $this->_customButtons = $buttons;
	return $this;
    }
    
    /**
     *
     * @param array $buttons 
     */
    protected function _buildToolbar( array $buttons )
    {
	$display = App_Forms_Toolbar::build( $buttons ); 
	
	if ( !empty( $display ) )
	    $this->_addDisplayGroupObject ( $display );
    }
    
    /**
     * 
     */
    protected function _renderToolbar()
    {
        if ( $this->_renderDefaultButtons )
            $this->_buttonsToolbar = $this->_getDefaultButtons();
        
        $this->_buttonsToolbar = array_merge( $this->_buttonsToolbar, $this->_customButtons );
	
        $this->_buildToolbar( $this->_buttonsToolbar );
    }
    
    /**
     *
     * @return array 
     */
    protected function _getDefaultButtons()
    {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        
        $objJavascript = lcfirst( $this->_toCamelCase( $request->getModuleName() ) ) . 
			 $this->_toCamelCase( $request->getControllerName() );
        
        $path = '/' . $request->getModuleName() . '/' . $request->getControllerName() . '/';
        
        $identifier = App_Plugins_Acl::getIdentifier( $path , 'Salvar' );
        
        $defaultButtons = array(
            array(
                'action' => $identifier,
                'id'     => $identifier,
                'label'  => 'Salvar',
                'icon'   => 'dijitEditorIcon dijitEditorIconSave',
                'click'  => "objGeral.submit('" . $this->getId() . "', " . $objJavascript . " )"
            ),
            array(
                'label'  => 'Fechar',
                'icon'   => 'dijitEditorIcon dijitEditorIconCancel',
                'click'  => 'objGeral.closeGenericDialog();'
            )
        );
        
        return $defaultButtons;
    }
    
    /**
     * 
     */
    protected function _defineDecorators()
    {
	// Renderiza decorators padrao
	if ( $this->_renderDefaultDecorators ) {
	    
	    if ( 'default' == $this->_defaultDecorators )
		$this->_setDecoratorsDefault();
	    else
		$this->_setDecoratorsColumns();
	}
	
	// Renderiza toolbar padrao
	if ( $this->_renderToolbar )
	    $this->_renderToolbar();
	
	return $this;
    }
    
    /**
     *
     * @param Zend_View_Interface $view 
     */
    public function render( Zend_View_Interface $view = null )
    {
	$this->_defineDecorators();
	
	return parent::render( $view );
    }
    
    /**
     *
     * @param string $string
     * @return string 
     */
    protected function _toCamelCase( $string )
    {
	$pieces = explode( '-', $string );

	return implode( '', array_map( 'ucfirst', $pieces ) );
    }
}