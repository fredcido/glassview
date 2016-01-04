<?php

/**
 * Description of Modulo
 *
 * @version $Id: ReconciliacaoController.php 1031 2013-10-22 15:30:34Z helion $
 */
class Financeiro_ReconciliacaoController extends App_Controller_Default
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
	$this->_mapper = new Model_Mapper_Reconciliacao();
    }

    /**
     * 
     */
    public function listAction()
    {
	$data = $this->_mapper->fetchGrid();

	$this->_helper->json( $data );
    }

    /**
     *
     * @param string $action
     * @return Financeiro_Form_Reconciliacao
     */
    protected function _getForm( $action )
    {
	if ( null == $this->_form ) {

	    $this->_form = new Financeiro_Form_Reconciliacao();
	    $this->_form->setAction( $action );
	}

	return $this->_form;
    }

    /**
     *
     */
    public function editPostHook()
    {
	$this->view->form->getElement( 'fn_conta_id' )->setAttrib( 'readOnly', true );
    }

    /**
     * 
     */
    public function buscaLancamentosAction()
    {
	$params = $this->_getAllParams();

	if ( empty( $params['recon'] ) ) {

	    $dadosStartReconc     = $this->_mapper->buscaDadosStartReconcilicao( $params );
	    $lancamentoEfetivados = array( 'rows' => array() );
	    
	    if ( !empty( $params['dataini'] ) )
		$dadosStartReconc['data_ini'] = $params['dataini'];
             
	} else {

            if($this->_mapper->isEfetivado($params['recon'])){
                
                $dadosStartReconc = array( 'data_ini' => $params['dataini'], 'valor_ini' => null );
            }else{
                
                $dadosStartReconc = $this->_mapper->buscaDadosStartReconcilicao( $params );
            }
	    if ( !empty( $params['dataini'] ) &&  !empty( $params['setdtini'] ) )
               $dadosStartReconc['data_ini'] = $params['dataini'];
	    $lancamentoEfetivados = $this->_mapper->buscaLancamentosEmConciliacao( $params, $dadosStartReconc['data_ini'] );
	}
        
        if ( !empty( $params['dataini'] ) &&  !empty( $params['setdtini'] ) )
               $dadosStartReconc['data_ini'] = $params['dataini'];

	$lancamentoPedentes = $this->_mapper->buscaLancamentosForaDeConciliacao( $params, $dadosStartReconc['data_ini'] );

	$this->_helper->json(
		array(
		    'lancamentospedentes'   => $lancamentoPedentes,
		    'lancamentosefetivados' => $lancamentoEfetivados,
		    'reconciliacao'	    => $dadosStartReconc,
		)
	);
    }

    /**
     * 
     */
    public function calculaSaldoFinalAction()
    {
	$params = $this->_getAllParams();

	$saldo = ( (float)0 ) + $params["fn_recon_ini_valor"];

	if ( !empty( $params["lancamentos"] ) ) {

	    foreach ( $params["lancamentos"] as $value ) {

		if ( !empty( $value ) ) {

		    $lancDados = $this->_mapper->buscaDadosLancamento( $value );

		    if ( $lancDados->fn_lancamento_tipo == 'C' ) {

			$saldo = $saldo + $lancDados->fn_lancamento_valor;
		    } else {

			$saldo = $saldo - $lancDados->fn_lancamento_valor;
		    }
		}
	    }
	}

	$this->_helper->json( array( 'saldofinal' => $saldo ) );
    }

}