<?php

/**
 * Description of Modulo
 *
 * @version $Id: CategoriaController.php 502 2012-04-15 19:53:11Z fred $
 */
class Financeiro_CategoriaController extends App_Controller_Default
{
    /**
     *
     * @var type 
     */
    protected $_mapper;
    
    public function init()
    {
        $this->_mapper = new Model_Mapper_Categoria();
    }
    
    public function listAction()
    {
        $data = $this->_mapper->fetchGrid();
        
        $this->_helper->json( $data );
    }

     /**
     *
     * @param string $action
     * @return Financeiro_Form_Categoria
     */
    protected function _getForm( $action )
    {
        if ( null == $this->_form ) {

            $this->_form = new Financeiro_Form_Categoria();
            $this->_form->setAction( $action );
        }

        return $this->_form;
    }

    /**
     *
     */
    public function editPostHook()
    {
        $this->view->form->getElement( 'fn_categoria_status' )->setAttrib( 'readOnly', null );
    }
    
    /**
     * 
     */
    public function organizarAction()
    {
	$this->view->form = new Financeiro_Form_OrganizarCategoria();
    }
    
    /**
     * 
     */
    public function treeAction()
    {
	$dojoData = new Zend_Dojo_Data( 'id', $this->_mapper->listCategoriaTree( $this->_getParam( 'id' ) ), 'name' );
	
	$itens = $dojoData->toArray();
	
	$this->_helper->json( $itens );
    }
    
    /**
     * 
     */
    public function organizarCategoriaAction()
    {
	$retorno = $this->_mapper->organizarCategoria( $this->getRequest()->getPost() );
	
	$this->_helper->json( $retorno );
    }
}