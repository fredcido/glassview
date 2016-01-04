<?php

/**
 * 
 */
class App_View_Helper_Menu extends Zend_View_Helper_Abstract
{
    /**
     *
     * @var Zend_Session_Namespace
     */
    protected $_session;
    
    /**
     *
     * @var Zend_Config
     */
    protected $_config;
    
    /**
     *
     * @var DOMDocument
     */
    protected $_dom;
    
    /**
     *
     * @var string
     */
    protected $_dojoArg = 'dojoType';
    
    /**
     *
     * @var Zend_Acl
     */
    protected $_acl;
    
    /**
     *
     * @var Model_Mapper_Menu
     */
    protected $_mapper;
    
    /**
     *
     * @var Model_Mapper_Menu
     */
    protected $_translate;
    
    /**
     * 
     */
    public function __construct()
    {
	$this->_config = Zend_Registry::get( 'config' );
	
	$this->_session = new Zend_Session_Namespace( $this->_config->geral->appid );
	
	$this->_acl = Zend_Registry::get( 'acl' );
        
        $this->_translate = Zend_Registry::get('Zend_Translate');
	
	$this->_mapper = new Model_Mapper_Menu();
    }
    
    /**
     *
     * @return App_View_Helper_Menu
     */
    public function menu()
    {
	return $this;
    }
    
    /**
     *
     * @return string
     */
    public function __toString()
    {
	return $this->render();
    }
    
    /**
     *
     * @return string
     */
    public function render()
    {
	return $this->_createMenu();
    }
    
    /**
     *
     * @return DOMDocument
     */
    protected function _createMenu()
    {
	// Cria Html dos menus
	return $this->_createHtml( $this->_mapper->getMenusTela() );
    }
    
    /**
     *
     * @param array $itens
     * @return string
     */
    protected function _createHtml( $itens )
    {
	$this->_dom = new DOMDocument();
	
	$mainContainer = $this->_dom->createElement( 'div' );
	
	$mainContainer->setAttribute( $this->_dojoArg, 'dijit.MenuBar' );
	$mainContainer->setAttribute( 'id', 'mainMenu' );
	
	
	// Insere itens
	foreach ( $itens as $key => $item ) {
	    $this->_insertItem( $item, $mainContainer );
	    //if ( $this->_insertItem( $item, $mainContainer ) )
		//$mainContainer->appendChild( $this->_dom->createTextNode( '|' ) );
	}
	
	$mainContainer->removeChild( $mainContainer->lastChild );
	
	$this->_dom->appendChild( $mainContainer );
	
	return $this->_dom->saveHTML();
    }
    
    /**
     *
     * @param DOMElement $item
     * @param DOMElement $parent 
     */
    protected function _insertItem( $item, $parent )
    {
	$itemMenu = $this->_dom->createElement( 'div' );
	
	// Configura o item
	$this->_configItem( $item, $itemMenu, $parent );
		
	// Se item for vinculado a tela, verifica se o msm tem permissao
	if ( !empty( $item['tela_id'] ) ) {
	    
	    // Verifica se menu tem permissao
	    if ( !$this->_hasResource( $item['tela_path'] ) )
		return false;
		
	}
	
	// Se item tiver filhos, tenta inseri-los
	if ( $item['menu_tipo'] == 'G' || array_key_exists( 'children', $item ) && $item['children']->count() > 0 ) {
	    
	    // Cria container para filhos
	    $menuContainer = $this->_dom->createElement( 'div' );
	    $menuContainer->setAttribute( $this->_dojoArg, 'dijit.Menu' );
	    
	    // Se nenhum filho foi inserido, nao insere item
	    if ( empty( $item['children'] ) || !$this->_insertChildren( $item['children'], $menuContainer ) )
		return false;
	
	    $itemMenu->appendChild( $menuContainer );
	}
	
	$parent->appendChild( $itemMenu );
		
	return true;
    }
 
    
    /**
     *
     * @param string $resource
     * @return boolean
     */
    protected function _hasResource( $resource )
    {
	$resourceName = strtolower( $resource );
	
	return $this->_acl->has( $resourceName );
    }
    
    /**
     *
     * @param DOMElement $item 
     */
    protected function _configItem( $item, $itemMenu, $parent )
    {
	$type = $this->_getClassMenu( $item, $parent );
	
	$itemMenu->setAttribute( $this->_dojoArg, $type );
	$itemMenu->setAttribute( 'id', 'menu-item-' . $item['menu_id'] );
	$itemMenu->setAttribute( 'iconClass', $item['menu_icon'] );
	
	// Cria acao do onclick do botao
	$this->_createAction( $itemMenu, $item );
	
	$spanLabel = $this->_dom->createElement( 'span' );
	
	if ( 'mainMenu' == $parent->getAttribute('id') ) {
	    
	    $divIcon = $this->_dom->createElement( 'div' );
	    $divIcon->setAttribute( 'class', 'icon-menu-topo ' . $item['menu_icon'] ); 
	    $spanLabel->appendChild( $divIcon );
	}
	
	$spanLabel->appendChild( $this->_dom->createTextNode( $this->_translate->_($item['menu_label']) ) );
	
	$itemMenu->appendChild( $spanLabel );
    }
    
    /**
     *
     * @param array $item
     * @param DOMElement $parent
     * @return string 
     */
    protected function _getClassMenu( $item, $parent )
    {
	if ( array_key_exists( 'children', $item ) && $item['children']->count() > 0 )
	    return 'mainMenu' == $parent->getAttribute('id') ? 'dijit.PopupMenuBarItem' : 'dijit.PopupMenuItem';
	else if ( 'mainMenu' == $parent->getAttribute('id') )
	    return 'dijit.MenuBarItem';
	else
	    return 'dijit.MenuItem';
    }
    
    /**
     *
     * @param array $itens
     * @param DOMElement $parent
     * @return boolean 
     */
    protected function _insertChildren( $itens, $parent ) 
    {
	$hasChildren = false;
	
	foreach ( $itens as $item ) {
	
	    if( $this->_insertItem( $item, $parent ) )
		$hasChildren = true;
	}
	
	return $hasChildren;
    }
    
    /**
     *
     * @param DOMElement $element
     * @param array $item
     * @return boolean 
     */
    protected function _createAction( $element, $item )
    {
	
	if ( $item['menu_tipo'] == 'G' )
	    return true;
	else if ( $item['menu_tipo'] == 'C' ) {
	    
	    $click = $item['menu_exec'];
	    
	} else {
	    
	    switch ( $item['menu_tipo'] ) {
		case 'A':
		    $action = "objGeral.openTab('%s', objGeral.translate('%s'), %s, '%s' );";
		    break;
		case 'D':
		    $action = "objGeral.createDialog('%s',objGeral.translate('%s'), %s );";		
		    break;
		case 'C':
		    break;
	    }

	    $path = $item['tela_path'];
	    $callback = empty( $item['menu_exec'] ) ? 'null' : $item['menu_exec'];
	    
	    $click = sprintf( $action, $path, $item['tela_nome'], $callback, $item['menu_icon'] );
	}
	
	$element->setAttribute( 'onClick', $click );
	
	return true;
    }
}