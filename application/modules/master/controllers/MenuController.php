<?php

/**
 *
 * @version $Id: MenuController.php 279 2012-02-17 18:30:45Z fred $
 */
class Master_MenuController extends App_Controller_Default
{
    /**
     *
     * @var Model_Mapper_Menu
     */
    protected $_mapper;
    
    /**
     * (non-PHPdoc)
     * @see Zend_Controller_Action::init()
     */
    public function init()
    {
        $this->_mapper = new Model_Mapper_Menu();
    }
    
    /**
     * 
     */
    public function organizarAction()
    {
	
    }
    
    /**
     * 
     */
    public function treeAction()
    {
	$dojoData = new Zend_Dojo_Data( 'id', $this->_mapper->listMenuTree(), 'name' );
	
	$itens = $dojoData->toArray();
	
	$this->_helper->json( $itens );
    }
    
    /**
     *
     * @param string $action
     * @return Master_Form_Modulo
     */
    protected function _getForm( $action )
    {
        if ( null == $this->_form ) {

            $this->_form = new Master_Form_Menu();
            $this->_form->setAction( $action );
        }

        return $this->_form;
    }
    
    /**
     * 
     */
    public function iconsAction()
    {
	$dojoData = new Zend_Dojo_Data( 'id', $this->_mapper->getIconsMenu(), 'label' );
	
	$itens = $dojoData->toArray();
	
	$this->_helper->json( $itens );
    }
    
    /**
     * 
     */
    public function organizarMenuAction()
    {
	$retorno = $this->_mapper->organizarMenu( $this->getRequest()->getPost() );
	
	$this->_helper->json( $retorno );
    }
}