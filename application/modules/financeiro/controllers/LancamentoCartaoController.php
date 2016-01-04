<?php

/**
 * Description of Lancamento cartao
 *
 * @version $Id: LancamentoCartaoController.php 502 2012-04-15 19:53:11Z fred $
 */
class Financeiro_LancamentoCartaoController extends App_Controller_Default
{
    /**
     *
     * @var type 
     */
    protected $_mapper;
    
    /**
     * 
     */
    public function init()
    {
        $this->_mapper = new Model_Mapper_LancamentoCartao();
    }
    
     /**
     *
     * @param string $action
     * @return Financeiro_Form_LancamentoCartao
     */
    protected function _getForm( $action )
    {
        if ( null == $this->_form ) {

            $this->_form = new Financeiro_Form_LancamentoCartao();
            $this->_form->setAction( $action );
        }

        return $this->_form;
    }

    /**
     * 
     */
    public function editPostHook()
    {
	$id = $this->_getParam( 'id' );

	$this->view->tiposlancamentos = $this->_mapper->listTiposLancamentos( $id );
        
        $dbProjeto = new Model_DbTable_Projeto();
	$this->view->projetos = $dbProjeto->fetchAll( array( 'projeto_status = ?' => 'I' ), 'projeto_nome' );
        
        
        if(!empty($this->view->data['fn_doc_fiscal_id'])){
        
            $dbDocumentoFiscal = new Model_DbTable_DocumentoFiscal();
            $dataDocumentoFiscal= $dbDocumentoFiscal->fetchRow( 
                                                        array( 'fn_doc_fiscal_id = ?' => 
                                                        $this->view->data['fn_doc_fiscal_id'] ) 
                                                );
            
            $this->view->form->getElement( 'fn_doc_fiscal_numero' )
                            ->setValue($dataDocumentoFiscal->fn_doc_fiscal_numero);
        
        }
        
        if( $this->view->data['fn_lanc_cartao_status'] == 'F' ){
            
            $lancFat = true;
        }else{
            
            $lancFat = $this->_mapper->verificaLancamentoFatura( $id );
        }
        
        $this->view->lancfat = $lancFat;
        if( $lancFat ){
            
            $this->view->form->getElement( 'fn_cc_id' )
                            ->setAttrib(  'readOnly' , true );
            $this->view->form->getElement( 'fn_lanc_cartao_data' )
                            ->setAttrib(  'readOnly' , true );
            $this->view->form->getElement( 'fn_lanc_cartao_desc' )
                            ->setAttrib(  'readOnly' , true );
            $this->view->form->getElement( 'fn_tipo_lanc_id' )
                            ->setAttrib(  'readOnly' , true );
        }

    }
    
    /**
     * 
     */
    public function tipolancamentolistAction()
    {
        $id = $this->_getParam( 'id', 0 );

        if(empty ($id) ){

            $data = array();
        }else{
            
            $mapper = new Model_Mapper_TipoLancamento();

            $mapper->setData( array( 'projeto_id' => $id) );

            $rows = $mapper->fetchTipoLancamentoPorProjeto();
            $data = $this->view->treeTipoLancamento( $rows );
        }
        $this->_helper->json( $data );
    }

    /**
     * 
     */
    public function projetolistAction()
    {
	$dbProjeto = new Model_DbTable_Projeto();
	$data = $dbProjeto->fetchAll( array( 'projeto_status = ?' => 'I' ), 'projeto_nome' );

	$opt[] = array( 'id' => null, 'name' => '','label' => '');
	foreach ( $data as $row )
            $opt[] = array(
                        'id' => $row->projeto_id,
                        'name' => $row->projeto_nome,
                        'label' => $row->projeto_nome
                );

        $this->_helper->json( array('identifier' => 'id',
                                         'label' => 'label',
                                         'items' => $opt )
                            );
    }
    
    /**
     * 
     */
    public function validaTipoLancamentoAction()
    {
	$data = $this->_getAllParams() ;
        
        $arrayA = array();
        foreach ($data['fn_tipo_lanc_id'] as $value) {
            
            $arrayA[] = $value;
        }
        
        $arrayB = array_unique($arrayA);
        
        if( count($arrayA) == count($arrayB) ){
            
            $this->_helper->json( array('validacao' => true) );
        }else{
            
            $this->_helper->json( array('validacao' => false) );
        }
    }

    /**
     * 
     */
    public function somaLancamentoAction()
    {
	$data = $this->_getAllParams() ;

        $soma = (float)0;
        if(!empty($data['fn_lanc_cc_tipo_valor']))
            foreach ($data['fn_lanc_cc_tipo_valor'] as $value) {

                $soma = $soma + $value;
            }

        $this->_helper->json( array('total' => $soma ) );

    }
    
    /**
     * 
     */
    public function reconciliarAction()
    {
	$formReconciliacao = new Financeiro_Form_ReconciliacaoCartaoCredito();
	$this->view->form = $formReconciliacao;
    }
    
    /**
     * 
     */
    public function buscaLancamentosAction()
    {
	$retorno = $this->_mapper->buscaLancamentosCartao( $this->_getAllParams() );
	
	$this->_helper->json( $retorno );
    }
    
    /**
     * 
     */
    public function salvarReconciliacaoAction()
    {
	$retorno = $this->_mapper->setData( $this->getRequest()->getPost() )->salvarFatura();
	
	$this->_helper->json( $retorno );
    }
    
    /**
     * 
     */
    public function verificaPermissaoEfetivarAction()
    {
	$this->_helper->json( $this->_mapper->verificaPermissaoEfetivar() );
    }
    
    /**
     * 
     */
    public function preparaFaturaLancamentoAction()
    {
	$retorno = $this->_mapper->setData( $this->getRequest()->getPost() )->preparaFaturaLancamento();
	
	$this->_helper->json( $retorno );
    }
    
     /**
     * 
     */
    public function efetivarReconciliacaoAction()
    {
	$retorno = $this->_mapper->setData( $this->getRequest()->getPost() )->efetivarFatura();
	
	$this->_helper->json( $retorno );
    }
    
    public function lancamentotipolancamentoAction()
    {
	$this->view->form = new Financeiro_Form_LancamentoTipoLancamento();
    }
    
    public function treetipolancamentoAction()
    {
        $mapperTipoLancamento = new Model_Mapper_TipoLancamento();
        
	$dojoData = new Zend_Dojo_Data( 'id', $mapperTipoLancamento->listTipoLancamentoTreeProjeto( $this->_getParam( 'id' ) ), 'name' );
	
	$itens = $dojoData->toArray();
	
	$this->_helper->json( $itens );
    }
    
    public function buscaTipoLancamentoCodigoAction()
    {
        $mapperTipoLancamento = new Model_Mapper_TipoLancamento();

        $mapperTipoLancamento->setData( $this->getRequest()->getPost() );
        
	$this->_helper->json( $mapperTipoLancamento->buscaTipoLancamentoCodigo( ) );
    }
}