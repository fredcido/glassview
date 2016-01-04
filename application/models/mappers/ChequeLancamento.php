<?php

/**
 * @version $Id $
 */
class Model_Mapper_ChequeLancamento extends App_Model_Mapper_Abstract
{
	/**
	 * @access public
	 * @param int $fn_lancamento_id
	 * @return Zend_Db_Table_Rowset
	 */
	public function fetchLancamento ( $fn_lancamento_id )
	{
		$dbChequeLancamento = App_Model_DbTable_Factory::get( 'ChequeLancamento' );
		
		$select = $dbChequeLancamento->select()
			->from(
				$dbChequeLancamento,
				array( 'fn_cheque_id' )
			)
			->where( 'fn_lancamento_id = ?', $fn_lancamento_id );
			
		$rows = $dbChequeLancamento->fetchAll( $select );
		
		return $rows;
	}
}
