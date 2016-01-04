<?php

/**
 * 
 * @version $Id $
 */
class Financeiro_LancamentoController extends App_Controller_Default
{

    /**
     * @var Model_Mapper_Lancamento
     */
    protected $_mapper;

    /**
     * @var Financeiro_Form_Lancamento
     */
    protected $_form;

    /**
     * @access public
     * @return void
     */
    public function init()
    {
	$this->_mapper = new Model_Mapper_LancamentoBancario();
    }

    /**
     * @access protected
     * @param string $action
     * @return Financeiro_Form_Lancamento
     */
    protected function _getForm( $action )
    {
	if ( is_null( $this->_form ) ) {

	    $this->_form = new Financeiro_Form_Lancamento();
	    $this->_form->setAction( $action );
	}

	return $this->_form;
    }

    /**
     * Exibe json para popular a grid
     * 
     * @access public
     * @return void
     */
    public function listEntradaAction()
    {
	$data = $this->_mapper->fetchGrid( 'C' );

	$this->_helper->json( $data );
    }

    /**
     * Exibe json para popular a grid
     * 
     * @access public
     * @return void
     */
    public function listSaidaAction()
    {
	$data = $this->_mapper->fetchGrid( 'D' );

	$this->_helper->json( $data );
    }
    
    /**
     * Exibe json para popular a grid
     * 
     * @access public
     * @return void
     */
    public function listEfetivadosAction()
    {
	$data = $this->_mapper->fetchEfetivados( 'D' );

	$this->_helper->json( $data );
    }

    /**
     * @access public
     * @return void
     */
    public function projetoAction()
    {
	$mapper = new Model_Mapper_Projeto();

	$rows = $mapper->fetchAll();

	$items = array();

	if ( $rows->count() ) {

	    $items[] = array('id' => null, 'name' => '');

	    foreach ( $rows as $row ) {
		$items[] = array(
		    'id' => $row->projeto_id,
		    'name' => $row->projeto_nome
		);
	    }
	}

	$data = array(
	    'identifier' => 'id',
	    'label' => 'name',
	    'items' => $items
	);

	$this->_helper->json( $data );
    }

        /**
     * @access public
     * @return void
     */
    public function formPostHook()
    {
        $this->view->form->removeElement('duplicata_desc');
        $this->view->form->removeElement('duplicata_valor');
        $this->view->form->removeElement('lancamento_duplicata_id');
    }
    /**
     * @access public
     * @return void
     */
    public function editAction()
    {
	$this->_helper->viewRenderer->setRender( 'form' );

	$form = $this->_getForm( $this->_helper->url( 'save' ) );        
        $this->view->form = $form;
        
	$id = $this->getRequest()->getParam( 'id' );
        
	$mapperDuplicata = new Model_Mapper_Duplicata();
	$rowDuplicata = $mapperDuplicata->verificaLancamentoDuplicata( $id );
        
        if(!empty($rowDuplicata)){
           
            $id = $rowDuplicata['lancamento_duplicata_id']; 
        }
        
	$this->_mapper->setData( array( 'id' => $id ) );

	$row = $this->_mapper->fetchRow();
        
        if ( empty( $row ) ){
            
	    $this->_helper->redirector->goToSimple( 'index' );
        }else{
            
            $data = $row->toArray();
            $form->populate( $data );
        }
        
        if(!empty($rowDuplicata)){
            
            $this->view->form->getElement( 'lancamento_duplicata_id' )
                             ->setValue( $rowDuplicata['fn_lancamento_id'] );
           
            $this->view->form->getElement( 'duplicata_valor' )
                             ->setValue( $rowDuplicata['fn_parcela_valor'] );
            
            $this->view->form->getElement( 'duplicata_desc' )
                             ->setValue( 'Parcela '.$rowDuplicata['fn_parcela_ref'].' de '.$rowDuplicata['fn_duplicata_parcelas'] );
            
            $this->view->form->getElement( 'fn_conta_id' )
                             ->setValue( $rowDuplicata['fn_conta_id'] );
            
            $this->view->form->getElement( 'fn_lancamento_status' )
                             ->setAttrib( 'readOnly', 'readOnly' );
            
            $this->view->form->getElement( 'btn_doc_fiscal' )
                             ->setAttrib( 'disabled',true);
            
            $this->view->form->getElement( 'terceiro_id' )
                             ->setAttrib( 'readOnly', 'readOnly' );
            
            $this->view->form->getElement( 'fn_lancamento_tipo' )
                             ->setAttrib( 'readOnly', 'readOnly' );
            
            $this->view->form->getDisplayGroup('toolbar')
                              ->removeElement( 'buttonRemoveLancamento' );
            
        }else{
            
            $this->view->form->removeElement('duplicata_desc');
            $this->view->form->removeElement('duplicata_valor');
            $this->view->form->removeElement('lancamento_duplicata_id');
            
            $this->_mapper->setData( array( 'fn_lancamento_id' => $id ) );
            
            if( $this->_mapper->isDeleteLancamento() ){
             
                $this->view->form->getDisplayGroup('toolbar')
                                  ->getElement( 'buttonRemoveLancamento' )
                                  ->setAttrib( 'disabled', null );
             
            }else{
                
                $this->view->form->getDisplayGroup('toolbar')
                                  ->removeElement( 'buttonRemoveLancamento' );
            }
        }
 
	//Cheques do lancamento
	$mapperChequeLancamento = new Model_Mapper_ChequeLancamento();
	$rowsCheque = $mapperChequeLancamento->fetchLancamento( $id );

	$this->view->rowsCheque = $rowsCheque;

	//Lancamentos por projeto
	$this->_mapper->setData( array('fn_lancamento_id' => $id) );
	$rows = $this->_mapper->fetchLancamentoProjeto();

	$this->view->rows = $rows;

	//Store de projeto
	$mapperProjeto = new Model_Mapper_Projeto();
	$store = $mapperProjeto->fetchAll();
	
	$efetivado = $this->_getParam( 'efetivado' );
	if ( !empty( $efetivado ) ) {
	    
	    $form = $this->view->form;
	    foreach ( $form->getElements() as $element )
		$element->setAttrib( 'readOnly', true );
	}

	$this->view->efetivado = $efetivado;
	$this->view->store = $store;
    }

    /**
     * @access public
     * @return void
     */
    public function tipoLancamentoAction()
    {
	$id = $this->_getParam( 'id', 0 );

	$mapper = new Model_Mapper_TipoLancamento();
	$mapper->setData( array('projeto_id' => (int)$id) );

	$rows = $mapper->fetchTipoLancamentoPorProjeto();
	
	$data = array();

	if ( $rows->count() )
	    $data = $this->view->treeTipoLancamento( $rows );

	$this->_helper->json( $data );
    }

    /**
     * Exibe tela para realizar transferencia entre contas
     * 
     * @access public
     * @return void
     */
    public function transferenciaAction()
    {
	$form = new Financeiro_Form_Transferencia();
	$form->setAction( $this->_helper->url( 'transferir' ) );

	$this->view->form = $form;
    }

    /**
     * @access public
     * @return void
     */
    public function contaDestinoAction()
    {
	$id = $this->_getParam( 'id', 0 );

	$mapper = new Model_Mapper_Conta();
	$mapper->setData( array('fn_conta_id' => $id) );

	$rows = $mapper->fetchContaDestino();

	$items = array();

	if ( $rows->count() ) {

	    foreach ( $rows as $row ) {
		$items[] = array(
		    'id' => $row->fn_conta_id,
		    'name' => $row->fn_conta_descricao
		);
	    }
	}

	$data = array(
	    'identifier' => 'id',
	    'label' => 'name',
	    'items' => $items
	);

	$this->_helper->json( $data );
    }

    /**
     * @access public
     * @return void
     */
    public function buscaSaldoAction()
    {
	$post = $this->getRequest()->getPost();

	$mapper = new Model_Mapper_Conta();
	$mapper->setData( $post );

	$row = $mapper->fetchSaldo();

	$data = array('saldo' => $row->fn_conta_saldo);

	$this->_helper->json( $data );
    }

    /**
     * @access public
     * @return void
     */
    public function transferirAction()
    {
	$data = array();

	if ( $this->getRequest()->isPost() ) {

	    $post = $this->getRequest()->getPost();

	    $mapper = new Model_Mapper_Conta();
	    $mapper->setData( $post );

	    $data['status'] = $mapper->transferir();
	    $data['description'] = $mapper->getMessage()->toArray();
	} else {

	    $message = new App_Message();
	    $message->addMessage( $this->_config->messages->warning, App_Message::WARNING );

	    $data['status'] = false;
	    $data['description'] = $message->toArray();
	}
	
	$data['refresh'] = true;

	$this->_helper->json( $data );
    }

    /**
     * @access public
     * @return void
     */
    public function removeLancamentoProjetoAction()
    {
	$data = array();

	if ( $this->getRequest()->getPost() ) {

	    $post = $this->getRequest()->getPost();

	    $mapper = new Model_Mapper_LancamentoProjeto();
	    $mapper->setData( $post );

	    $data['status'] = $mapper->delete();
	    $data['description'] = $mapper->getMessage()->toArray();
	} else {

	    $message = new App_Message();
	    $message->addMessage( $this->_config->messages->warning, App_Message::WARNING );

	    $data['status'] = false;
	    $data['description'] = $message->toArray();
	}

	$this->_helper->json( $data );
    }

    /**
     * @access public
     * @return void
     */
    public function pagamentoAction()
    {
	$id = $this->_getParam( 'id', 0 );

	$this->_mapper->setData( array('id' => $id) );
	$row = $this->_mapper->fetchRow();
        
	//Lancamentos por projeto
	$this->_mapper->setData( array('fn_lancamento_id' => $id) );
	$rows = $this->_mapper->fetchLancamentoProjeto();

	$this->view->rows = $rows;
        
	$form = new Financeiro_Form_Pagamento();
	$form->setAction( $this->_helper->url( 'pagar' ) );

	$form->getElement( 'fn_lancamento_id' )->setValue( $row->fn_lancamento_id );
	$form->getElement( 'fn_forma_pgto_valor' )->setValue( $row->fn_lancamento_valor );
	$form->getElement( 'fn_conta_id' )->setValue( $row->fn_conta_id );
	$form->getElement( 'fn_lancamento_tipo' )->setValue( $row->fn_lancamento_tipo );
	
	$this->view->edit = $this->_getParam( 'edit' );
	if ( !empty( $this->view->edit ) ) {
	    
	    $form->getElement( 'fn_forma_pgto_valor' )->setAttrib( 'readOnly', true );
	    $form->getElement( 'fn_lancamento_dtefetivado' )->setValue( $this->view->date( $row->fn_lancamento_dtefetivado, 'yyyy-MM-dd' ) );
	}else{
            
            $form->removeElement( 'projeto_id' );
            $form->removeElement( 'text_lancamento' );
            $form->removeElement( 'fn_tipo_lanc_id' );
            $form->removeElement( 'fn_lanc_projeto_valor' );
        }

	$this->view->form = $form;
    }

    /**
     * @access public
     * @return void
     */
    public function pagarAction()
    {
	$data = array();

	if ( $this->getRequest()->isPost() ) {

	    $post = $this->getRequest()->getPost();

	    $mapper = new Model_Mapper_FormaPagamento();
	    $mapper->setData( $post );

	    $data['status'] = $mapper->save();
	    $data['description'] = $mapper->getMessage()->toArray();
	    $data['refresh'] = true;
	} else {

	    $message = new App_Message();
	    $message->addMessage( $this->_config->messages->warning, App_Message::WARNING );

	    $data['status'] = false;
	    $data['description'] = $message->toArray();
	}

	$this->_helper->json( $data );
    }

    /**
     * @access public
     * @return void
     */
    public function listaChequeAction()
    {
	$id = $this->_getParam( 'id', null );

	$mapper = new Model_Mapper_Cheque();
	$mapper->setData( array( 'fn_lancamento_id' => $id ) );

	$rows = $mapper->fetchLancamento();

	$data = array('rows' => array());

	if ( $rows->count() ) {

	    foreach ( $rows as $row ) {

		$data['rows'][] = array(
		    'id' => $row->fn_cheque_id,
		    'data' => array(
			$row->selected,
			$row->fn_banco_nome,
			$row->fn_conta_descricao,
			$row->fn_cheque_numero,
			$row->fn_cheque_valor
		    )
		);
	    }
	}

	$this->_helper->json( $data );
    }
    
    /**
     * 
     */
    public function listTransferenciaAction()
    {
	$mapper = new Relatorio_Model_Mapper_Financeiro();
	$rows = $mapper->Transferencia();

	$data = array( 'rows' => array() );

	if ( $rows->count() ) {

	    foreach ( $rows as $key => $row ) {

		$data['rows'][] = array(
		    'id' => $row->fn_lancamento_id,
		    'data' => array(
			++$key,
			$this->view->date( $row->fn_lancamento_data ),
			$this->view->date( $row->fn_lancamento_dtefetivado ),
			$row->conta_debito,
			$row->conta_credito,
			$this->view->currency( $row->fn_lancamento_valor )
		    )
		);
	    }
	}

	$this->_helper->json( $data );
    }

    /**
     * Exibe tela para realizar transferencia entre contas
     * 
     * @access public
     * @return void
     */
    public function editTransferenciaAction()
    {
	$form = new Financeiro_Form_Transferencia();
	$form->setAction( $this->_helper->url( 'transferir' ) );
	
	// Busca dados da transferencia
	$mapper = new Model_Mapper_Conta();
	$transferencia = $mapper->fetchTransferencia( $this->_getParam( 'id' ) );
	
	$data = $transferencia->toArray();
	$data['fn_lancamento_dtefetivado'] = $this->view->date( $data['fn_lancamento_dtefetivado'], 'yyyy-MM-dd' );
	
	$mapper->setData( array('fn_conta_id' => $data['conta_origem'] ) );
	$rows = $mapper->fetchContaDestino();
	
	$optConta = array( '' => '' );
	foreach ( $rows as $row )
	    $optConta[$row->fn_conta_id] = $row->fn_conta_descricao;
	
	$form->getElement( 'conta_destino' )->addMultiOptions( $optConta );
	$form->removeElement( 'saldo_conta_origem' );
	$form->removeElement( 'saldo_conta_destino' );
	
	$form->populate( $data );
	
	foreach ( $form->getElements() as $element )
	    $element->getName() != 'fn_lancamento_data' ? $element->setAttrib( 'readOnly', true ) : null;

	$this->view->form = $form;
	
	$this->_helper->viewRenderer->setRender( 'transferencia' );
    }

    public function lancamentotipolancamentoAction()
    {
	$this->view->form = new Financeiro_Form_LancamentoTipoLancamento();
    }
    
    public function treetipolancamentoAction()
    {
        $mapperTipoLancamento = new Model_Mapper_TipoLancamento();
        
        $dados    = $mapperTipoLancamento->listTipoLancamentoTreeProjeto( $this->_getParam( 'id' ) );
                
	$dojoData = new Zend_Dojo_Data( 'id', $dados , 'name' );
	
	$itens    = $dojoData->toArray();
	
	$this->_helper->json( $itens );
    }
    
    public function buscaTipoLancamentoCodigoAction()
    {
        $mapperTipoLancamento = new Model_Mapper_TipoLancamento();

        $mapperTipoLancamento->setData( $this->getRequest()->getPost() );
        
	$this->_helper->json( $mapperTipoLancamento->buscaTipoLancamentoCodigo( ) );
    }
    /**
     * @access public
     * @return void
     */
    public function deletAction()
    {
	$data = array();

	if ( $this->getRequest()->getPost() ) {

	    $post = $this->getRequest()->getPost();

	    $mapper = new Model_Mapper_LancamentoBancario();
	    $mapper->setData( array( 'fn_lancamento_id' => $post['identify'] ) );
            
            $data['status']     =  $mapper->delete();
            $data['description'] = $mapper->getMessage()->toArray();
  
	} else {

	    $message = new App_Message();
	    $message->addMessage( $this->_config->messages->warning, App_Message::WARNING );

	    $data['status'] = false;
	    $data['description'] = $message->toArray();
	}

	$this->_helper->json( $data );
    }
}
