<?php

/**
 * Description of Modulo
 *
 * @version $Id: TipoLancamentoController.php 792 2012-07-31 11:55:27Z fred $
 */
class Financeiro_TipoLancamentoController extends App_Controller_Default
{
    /**
     *
     * @var type 
     */
    protected $_mapper;
    
    public function init()
    {
        $this->_mapper = new Model_Mapper_TipoLancamento();
    }
    
    public function listAction()
    {
        $data = $this->_mapper->fetchGrid();
        
        $this->_helper->json( $data );
    }

     /**
     *
     * @param string $action
     * @return Financeiro_Form_TipoLancamento
     */
    protected function _getForm( $action )
    {
        if ( null == $this->_form ) {

            $this->_form = new Financeiro_Form_TipoLancamento();
            $this->_form->setAction( $action );
        }

        return $this->_form;
    }

    /**
     *
     */
    public function editPostHook()
    {
        $this->view->form->getElement( 'fn_tipo_lanc_status' )->setAttrib(  'readOnly' , null );
        $this->view->form->getElement( 'fn_categoria_id' )->setAttrib(  'readOnly' , true )->setAttrib( 'disabled', null );
        $this->view->form->getElement( 'projeto_id' )->setAttrib(  'readOnly' , true );
	
	$data = $this->view->data;
	
	$dbCategoria = App_Model_DbTable_Factory::get( 'Categoria' );
	$rows = $dbCategoria->fetchAll( array ( 'projeto_id = ?' => $data['projeto_id'] ), 'fn_categoria_descricao' );
	
	$categoria = $this->view->form->getElement( 'fn_categoria_id' );
	foreach ( $rows as $row )
	    $categoria->addMultiOption( $row->fn_categoria_id, $row->fn_categoria_descricao );
    }
    
    /**
     * 
     */
    public function organizarAction()
    {
	$this->view->form = new Financeiro_Form_OrganizarTipoLancamento();
    }
    
    /**
     * 
     */
    public function treeAction()
    {
	$dojoData = new Zend_Dojo_Data( 'id', $this->_mapper->listTipoLancamentoTree( $this->_getParam( 'id' ) ), 'name' );
	
	$itens = $dojoData->toArray();
	
	$this->_helper->json( $itens );
    }
    
    /**
     * 
     */
    public function organizarTipoLancamentoAction()
    {
	$retorno = $this->_mapper->organizarTipoLancamento( $this->getRequest()->getPost() );
	
	$this->_helper->json( $retorno );
    }
    
    /**
     * 
     */
    public function categoriasPorProjetoAction()
    {
        $id = $this->_getParam( 'id', 0 );
        
	$dbCategoria = App_Model_DbTable_Factory::get( 'Categoria' );
	$rows = $dbCategoria->fetchAll( array ( 'projeto_id = ?' => $id ), 'fn_categoria_descricao' );
        
        $data = array();

        if ( $rows->count() ) {

            foreach ( $rows as $key => $row ) {

                $data[] = array(
                    'id'    => $row->fn_categoria_id,
                    'name'  => $row->fn_categoria_descricao
                );
            }
        }

        $this->_helper->json( 
				array(
				    'identifier' => 'id',
				    'label'	 => 'name',
				    'items' => $data 
				)
                            );

    }
}