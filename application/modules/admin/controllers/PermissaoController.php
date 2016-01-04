<?php

/**
 * Description of Modulo
 *
 * @version $Id: PermissaoController.php 218 2012-02-14 01:09:13Z helion $
 */
class Admin_PermissaoController extends App_Controller_Default
{
    /**
     *
     * @var type 
     */
    protected $_mapper;
    
    public function init()
    {
        $this->_mapper = new Model_Mapper_Permissao();
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
     * @param string $action
     * @return Admin_Form_Permissao
     */
    protected function _getForm( $action )
    {
        if ( null == $this->_form ) {

            $this->_form = new Admin_Form_Permissao();
            $this->_form->setAction( $action );
        }

        return $this->_form;
    }
    
    /**
     * 
     */
    public function itensAction()
    {
	$dojoData = new Zend_Dojo_Data( 'id', $this->_mapper->listTelasAcao( $this->_getParam( 'id' ) ), 'name' );
	
	$itens = $dojoData->toArray();
	
	$this->_helper->json( $itens );
    }
    
    /**
     * 
     */
    public function deleteAction()
    {
	$result = $this->_mapper->removerPermissao( $this->_getAllParams() );
		
	$this->_helper->json( $result );
    }
    
    /**
     * 
     */
    public function adicionarAction()
    {
	$this->_mapper->setData( $this->_getAllParams() );
	$result = $this->_mapper->adicionarPermissao();
		
	$this->_helper->json( $result );
    }
}