<?php

/**
 * 
 * @version $Id: Acao.php 396 2012-03-06 12:27:40Z fred $
 */
class Model_Mapper_Acao extends App_Model_Mapper_Abstract
{

    /**
     * 
     * @access public
     * @return array
     */
    public function fetchGrid()
    {

	$dbAcao = App_Model_DbTable_Factory::get( 'Acao' );
	$dbTela = App_Model_DbTable_Factory::get( 'Tela' );

	$select = $dbAcao->select()
		->setIntegrityCheck( false )
		->from(
			array('a' => $dbAcao), array('a.*')
		)
		->join(
		array('t' => $dbTela), 'a.tela_id = t.tela_id', array('t.tela_nome')
	);

	$rows = $dbAcao->fetchAll( $select );

	$data = array('rows' => array());

	if ( $rows->count() ) {

	    foreach ( $rows as $key => $row ) {

		$data['rows'][] = array(
		    'id' => $row->acao_id,
		    'data' => array(
			++$key,
			$row->acao_descricao,
			$row->tela_nome,
			$row->acao_identificador
		    )
		);
	    }
	}

	return $data;
    }

    public function save()
    {
	$adapter = Zend_Db_Table::getDefaultAdapter();

	try {

	    $dbAcao = App_Model_DbTable_Factory::get( 'Acao' );
	    $dbPrivilegio = App_Model_DbTable_Factory::get( 'Privilegios' );

	    $adapter->beginTransaction();

	    $where = array(
		'UPPER(acao_descricao) = UPPER(?)' => $this->_data['acao_descricao'],
		'tela_id = ?' => $this->_data['tela_id']
	    );

	    if ( !$dbAcao->isUnique( $where, $this->_data['acao_id'] ) ) {

		$this->_message->addMessage( 'A&ccedil;&atilde;o j&aacute; cadastrada para essa tela.', App_Message::ERROR );
		return false;
	    }

	    if ( empty( $this->_data['acao_id'] ) ) {

		$mapperTela = new Model_Mapper_Tela();
		$tela = $mapperTela->setData( array('id' => $this->_data['tela_id']) )->fetchRow();

		$this->_data['acao_identificador'] = App_Plugins_Acl::getIdentifier( $tela->tela_path, $this->_data['acao_descricao'] );
	    }
	    
	    if ( false !== ($result = parent::_simpleSave( $dbAcao ) ) )
		parent::_cleanCacheTag( array('acl') );

	    // Insere novos privilegios
	    if ( !empty( $this->_data['privilegios'] ) ) {

		foreach ( $this->_data['privilegios'] as $privilegio )
		    $dbPrivilegio->insert( array('acao_id' => $result, 'privilegios_action' => $privilegio) );
	    }

	    // Altera privilegios ja cadastrados
	    if ( !empty( $this->_data['privilegios_base'] ) ) {

		foreach ( $this->_data['privilegios_base'] as $id => $privilegio ) {

		    $where = array('acao_id = ?' => $result, 'privilegios_id = ?' => $id);

		    $dbPrivilegio->update( array('privilegios_action' => $privilegio), $where );
		}
	    }

	    $adapter->commit();

	    return $result;
	} catch ( Exception $e ) {

	    $adapter->rollBack();
	    
	    return false;
	}
    }

    /**
     *
     * @return App_Model_DbTable_Row_Abstract
     */
    public function fetchRow()
    {
	$where = array('acao_id = ?' => $this->_data['id']);

	return parent::_simpleFetchRow( App_Model_DbTable_Factory::get( 'Acao' ), $where );
    }

    /**
     *
     * @param int $id
     * @return Zend_Db_Table_Rowset 
     */
    public function listPrivilegios( $id )
    {
	$dbPrivilegios = App_Model_DbTable_Factory::get( 'Privilegios' );

	return $dbPrivilegios->fetchAll( array('acao_id = ?' => $id) );
    }

    /**
     *
     * @param array $data
     * @return array 
     */
    public function deletePrivilegio( $data )
    {
	if ( empty( $data['id'] ) )
	    return array('status' => false, 'message' => $this->_config->messages->error);

	$dbPrivilegios = App_Model_DbTable_Factory::get( 'Privilegios' );
	$dbPrivilegios->delete( array('privilegios_id = ?' => $data['id']) );

	return array('status' => true);
    }

}