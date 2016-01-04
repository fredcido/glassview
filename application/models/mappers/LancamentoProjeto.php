<?php

/**
 * 
 * @version $Id $ 
 */
class Model_Mapper_LancamentoProjeto extends App_Model_Mapper_Abstract
{
	/**
	 * @access public
	 * @return bool
	 */
	public function delete()
	{
		try {
	
			$dbLancamentoProjeto = App_Model_DbTable_Factory::get( 'LancamentoProjeto' );
			
			$where = array(
				'projeto_id = ?' 		=> $this->_data['projeto_id'],
				'fn_tipo_lanc_id = ?' 	=> $this->_data['fn_tipo_lanc_id'],
				'fn_lancamento_id = ?' 	=> $this->_data['fn_lancamento_id']
			);
			
			$dbLancamentoProjeto->delete( $where );
			
			$this->_message->addMessage( $this->_config->messages->success, App_Message::SUCCESS );
			
			return true;
			
		} catch ( Exception $e ) {
			
			$this->_message->addMessage( $this->_config->messages->error, App_Message::ERROR );
			
			return false;
			
		} 
		
	} 
}
