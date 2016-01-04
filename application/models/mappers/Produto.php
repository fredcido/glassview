<?php

/**
 * 
 * @version $Id: Produto.php 489 2012-04-04 17:53:29Z fred $
 */
class Model_Mapper_Produto extends App_Model_Mapper_Abstract
{
    /**
     * 
     */
    public function fetchGrid()
    {
        $dbProduto       = App_Model_DbTable_Factory::get('Produto');
        $dbTipoProduto   = App_Model_DbTable_Factory::get('TipoProduto');
        $dbUnidadeMedida = App_Model_DbTable_Factory::get('UnidadeMedida');
        $dbEstoque = App_Model_DbTable_Factory::get('Estoque');

        $subSelect = $dbEstoque->select()
			       ->from( array( 'e' => $dbEstoque ), 'estoque_qtde_atual' )
			       ->where( 'e.produto_id = p.produto_id' )
			       ->order( 'e.estoque_id DESC' )
			       ->limit( 1 );
	
        $select = $dbProduto->select()
                ->setIntegrityCheck(false)
                ->from(
                        array('p' => $dbProduto),
                        array( 'p.*', 'estoque' => new Zend_Db_Expr( '(' . $subSelect . ')' ) )
                )
                ->join(
                        array('tp' => $dbTipoProduto),
                        'tp.tipo_produto_id = p.tipo_produto_id',
                        array('tp.tipo_produto_nome')
                )
                ->join(
                        array('um' => $dbUnidadeMedida),
                        'um.unidade_medida_id = p.unidade_medida_id',
                        array('um.unidade_medida_nome')
                )
		->order( 'p.produto_descricao' );
        

        $rows = $dbProduto->fetchAll( $select );

        $data = array('rows' => array());

        if ( $rows->count() ) {
	    
	    $currency = new Zend_Currency();

            foreach ( $rows as $key => $row ) {
                
                $data['rows'][] = array(
                    'id'    => $row->produto_id ,
                    'data'  => array(
                        ++$key,
                        $row->produto_descricao,
                        $row->tipo_produto_nome,
                        $row->unidade_medida_nome,
                        $currency->setValue( $row->produto_valor_unitario )->toString(),
			$row->estoque,
                        $row->produto_estoque_min,
                        $row->produto_estoque_max,
                        parent::_showStatus( $row->produto_status )
                    )
                );

            }

        }

        return $data;
    }
    
    /**
     *
     * @return boolean
     */
    public function save()
    {
        try {

            $dbTable = App_Model_DbTable_Factory::get('Produto');

            $where = array( 'UPPER(produto_descricao) = UPPER(?)' => $this->_data['produto_descricao'] );

            if ( !$dbTable->isUnique( $where, $this->_data['produto_id'] ) ) {

                $this->_message->addMessage( 'Produto j&aacute; cadastrado com essa descrição .', App_Message::ERROR );
                return false;
            }
            return parent::_simpleSave( $dbTable );

        } catch ( Exception $e ) {
            return false;
        }
    }
    
    /**
     *
     * @return App_Model_DbTable_Row_Abstract
     */
    public function fetchRow()
    {
        $where = array( 'produto_id = ?' => $this->_data['id'] );
        
        return parent::_simpleFetchRow( App_Model_DbTable_Factory::get( 'Produto' ), $where );
    }
    
    /**
     *
     * @param int $id
     * @return App_Model_DbTable_Row_Abstract
     */
    public function getDadosMovimentacao( $id )
    {
	$dbProduto = App_Model_DbTable_Factory::get( 'Produto' );
	$dbEstoque = App_Model_DbTable_Factory::get( 'Estoque' );
	
	$subSelect = $dbEstoque->select()
			       ->from( array( 'e' => $dbEstoque ), 'estoque_qtde_atual' )
			       ->where( 'e.produto_id = p.produto_id' )
			       ->order( 'e.estoque_id DESC' )
			       ->limit( 1 );
	
	$select = $dbProduto->select()
			    ->setIntegrityCheck( false )
			    ->from( 
				array( 'p' => $dbProduto ),
				array(
				    'produto_descricao',
				    'produto_aviso',
				    'produto_estoque_min',
				    'produto_estoque_max',
				    'estoque_valor_atual' => 'produto_valor_unitario',
				    'estoque_qtde_anterior' => new Zend_Db_Expr( 'IFNULL( (' . $subSelect . '), 0)' ),
				)
			    )
			    ->where( 'p.produto_id = ?', $id );
	
	return $dbProduto->fetchRow( $select );
    }
}