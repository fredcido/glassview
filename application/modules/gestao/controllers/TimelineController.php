<?php

/**
 * 
 * @version $Id $
 */
class Gestao_TimelineController extends App_Controller_Default
{
    /**
     * 
     * @var Model_Mapper_Timeline
     */
    protected $_mapper;
    
    /**
     * 
     * @var Gestao_Form_Timeline
     */
    protected $_form;

    /**
     * 
     * @access public
     * @return void
     */
    public function init()
    {
        $this->_mapper = new Model_Mapper_Timeline();
    }
    
    /**
     * 
     * @access protected
     * @param string $action
     * @return Gestao_Form_Timeline
     */
    protected function _getForm( $action )
    {
        if ( null == $this->_form ) {

            $this->_form = new Gestao_Form_Timeline();
            $this->_form->setAction( $action );
            
        }

        return $this->_form;
    }
    
    /**
     * 
     * @access public
     * @return void
     */
    public function indexAction()
    {
    }
    
    /**
     * 
     * @access public
     * @return void
     */
    public function listAction()
    {
	$projeto = $this->_getParam( 'id' );
	$data = $this->_mapper->fetchGrid( $projeto );
       
	$this->_helper->json( $data ); 
    }
    
    /**
     * 
     * @access public
     * @return void
     */
    public function editPostHook()
    {
        $timelineInicio= explode( ' ', $this->view->data['timeline_inicio'] );
        $timelineFim = explode( ' ', $this->view->data['timeline_fim'] );

        $this->view->form->getElement( 'dt_inicio' )
                          ->setValue( $timelineInicio[0] );

        $this->view->form->getElement( 'hr_inicio' )
                          ->setValue( 'T'.$timelineInicio[1] );

        $this->view->form->getElement( 'dt_fim' )
                          ->setValue( $timelineFim[0] );

        $this->view->form->getElement( 'hr_fim' )
                          ->setValue( 'T'.$timelineFim[1] );
    }
    
    /**
     * 
     * @access public
     * @return void
     */
    public function changeEventSaveAction()
    {
        $post = $this->getRequest()->getPost();
        
        $this->_mapper->setData( $post );
        $result = $this->_mapper->save();
        
        $this->_helper->json( array('result' => $result) );
    }
    
    /**
     * 
     */
    public function cargaHorariaAction()
    {
        $post = $this->getRequest()->getPost();
        
        $mapper = new Model_Mapper_Funcionario();
        
        $mapper->setData( array('id' => $post['id']) );
        
        $row = $mapper->fetchRow();
        
        $this->_helper->json( $row->toArray() );
    }
    
    /**
     * 
     * @access public
     * @return void
     */
    public function lancamentoAction()
    {
        $action = $this->_helper->url( 'lancamento' );
        
        $form = new Gestao_Form_Lancamento();
        $form->setAction( $action );
        
        $this->view->form = $form;
    }
    /**
     * 
     */
    public function maxmimatividadesAction()
    {
        $post = $this->getRequest()->getPost();
        
        $this->_mapper->setData( $post );
        $result = $this->_mapper->buscaMaxMimAtividades();
        
        $this->_helper->json( $result );        
    }
    
    /**
     * 
     * @access public
     * @return void
     */
    public function listaLancamentosAction()
    {
        $post = $this->getRequest()->getPost();
        
        $this->_mapper->setData( $post );
        $result = $this->_mapper->fetchLancamentos();
        
        $this->_helper->json( $result );        
    }
    
    /**
     * 
     * @access public
     * @return void
     */
    public function custoAction()
    {
        $action = $this->_helper->url( 'calcular-custo' );
        
        $form = new Gestao_Form_Custo();
        $form->setAction( $action );
        
        $this->view->form = $form;
    }
    
    public function calcularCustoAction()
    {
        $post = $this->getRequest()->getPost();
        
        $this->_mapper->setData( $post );
        $result = $this->_mapper->calcularCusto();
        
        $this->_helper->json( $result );        
    }
    
    /**
     * Busca profissionais de acordo com projeto
     * 
     * @access public
     * @return void
     */
    public function funcionarioAction()
    {
        $projeto_id = $this->getRequest()->getParam('projeto_id', null);
        
        $data = array( 'projeto_id' => $projeto_id );
        
        $this->_mapper->setData( $data );
        
        $result = $this->_mapper->fetchFuncionarioByProjeto();
        
        $this->_helper->json( $result );
    }
    
    /**
     * Busca o periodo do funcionario no projeto
     * 
     * @access public
     * @return void
     */
    public function periodoAction()
    {
        $post = $this->getRequest()->getPost();
        
        $this->_mapper->setData( $post );
        $row = $this->_mapper->fetchPeriodo();
        
        $this->_helper->json( $row->toArray() );
    }
    
    /**
     * 
     */
    public function projetosAction()
    {
	$dbProjeto = App_Model_DbTable_Factory::get( 'Projeto' );
	$projetos = $dbProjeto->fetchAll();
	
	$data = array( array( 'id' => '', 'name' => '' ) );
	foreach ( $projetos as $projeto )
	    $data[] = array( 'id' => $projeto->projeto_id, 'name' => $projeto->projeto_nome );
	
	$dojoData = new Zend_Dojo_Data( 'id', $data, 'name' );
	
	$this->_helper->json( $dojoData->toArray() );
    }
}
