<?php

class Zend_View_Helper_Nomenclatura extends Zend_View_Helper_Abstract
{
    /**
     *
     * @return Zend_View_Helper_Nomenclatura 
     */
    public function nomenclatura()
    {
	return $this;
    }
    
    /**
     *
     * @param string $tipo
     * @return string 
     */
    public function tipoLancamento ( $lancamento )
    {
	
	if ( $lancamento->fn_lancamento_trans == 1 )
	    $descricao = 'Transferência';
	else {
	    
	    if ( $lancamento->fn_lancamento_estorno == 1 )
		$descricao = 'Estorno';
	    else
		$descricao = 'Normal';
	}
	
	return $this->view->translate()->_( $descricao );
    }
    
    /**
     *
     * @param string $tipo
     * @return string 
     */
    public function tipoMovimentacaoEstoque ( $estoque )
    {
	$descricao = ( $estoque->estoque_tipo == 'E' ? 'Entrada' : 'Saída' );
        return $this->view->translate()->_( $descricao );
    }

    /**
     *
     * @param string $tipo
     * @return string
     */
    public function situacaoCheque ( $cheque )
    {
        $optSituacao = array(
            'A' => 'A Compensar',
            'D' => 'Depositado',
            'C' => 'Compensado',
            'V' => 'Devolvido',
            'P' => 'Pago',
            'R' => 'Repassado'
        );

        $type      = $cheque->fn_cheque_situacao;
	$descricao = ( empty( $optSituacao[$type] ) ? '-' : $optSituacao[$type] );
        return $this->view->translate()->_( $descricao );
    }
    /**
     *
     * @param string $tipo
     * @return string
     */
    public function tipoDuplicata ( $duplicata )
    {
        $optDuplicataTipo = array(
            'E' => 'Entrada',
            'S' => 'Saída'
        );

        $type      = $duplicata->fn_duplicata_tipo;
	$descricao = ( empty( $optDuplicataTipo[$type] ) ? '-' : $optDuplicataTipo[$type] );
        return $this->view->translate()->_( $descricao );
    }
    
    /**
     *
     * @param string $tipo
     * @return string
     */
    public function pgtoLancamento ( $lancamento )
    {
        $optPgtolanc = array(
            '1' => 'Pago',
            '0' => 'Não Pago'
        );

        $type      = $lancamento->fn_lancamento_efetivado;
	$descricao = ( empty( $optPgtolanc[$type] ) ? '-' : $optPgtolanc[$type] );
        return $this->view->translate()->_( $descricao );
    }
    
    /**
     *
     * @param string $tipo
     * @return string
     */
    public function situacaoReconciliacaoBancaria ( $reconciliacaoBancaria )
    {
        $optPgtolanc = array(
            '1' => 'Efetivada',
            '0' => 'Não Efetivada'
        );

        $type      = $reconciliacaoBancaria->fn_recon_efetivada;
	$descricao = ( empty( $optPgtolanc[$type] ) ? '-' : $optPgtolanc[$type] );
        return $this->view->translate()->_( $descricao );
    }
    
    /**
     *
     * @param string $tipo
     * @return string
     */
    public function tipoLancamentoBancario ( $lancamento )
    {
        $optTipolanc = array(
            'C' => 'Crédito',
            'D' => 'Débito'
        );

        $type      = $lancamento->fn_lancamento_tipo;
	$descricao = ( empty( $optTipolanc[$type] ) ? '-' : $optTipolanc[$type] );
        return $this->view->translate()->_( $descricao );
    }
}