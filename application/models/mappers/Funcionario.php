<?php

/**
 * 
 * @version $Id: Funcionario.php 276 2012-02-17 13:53:20Z fred $
 */
class Model_Mapper_Funcionario extends App_Model_Mapper_Abstract
{
    /**
     *
     * @return array 
     */
    public function fetchGrid()
    {
        $rows = $this->listAll();

        $data = array( 'rows' => array() );

        if ( $rows->count() ) {

            foreach ( $rows as $key => $row ) {

                $data['rows'][] = array(
                    'id'    => $row->funcionario_id,
                    'data'  => array(
                        ++$key,
                        $row->funcionario_nome,
                        $row->funcionario_email,
                        $row->cargo,
                        $row->cargo
                    )
                );

            }
        }

        return $data;
    }
    
    /**
     *
     * @return Zend_Db_Table_Rowset
     */
    public function listAll()
    {
	$dbFuncionario = App_Model_DbTable_Factory::get( 'Funcionario' );
	$dbDadosFuncionario = App_Model_DbTable_Factory::get( 'DadosFuncionario' );
	$dbCargo = App_Model_DbTable_Factory::get( 'Cargo' );
	$dbUsuario = App_Model_DbTable_Factory::get( 'Usuario' );
	
	$select = $dbFuncionario->select()
				->from( array( 'f' => $dbFuncionario ) )
				->setIntegrityCheck( false )
				->join(
				    array( 'c' => $dbCargo ),
				    'c.cargo_id = f.cargo_id',
				    array( 'cargo' => 'cargo_nome' )
				)
				->joinLeft(
				    array( 'u' => $dbUsuario ),
				    'u.usuario_id = f.usuario_id',
				    array()
				)
				->joinLeft(
				    array( 'df' => $dbDadosFuncionario ),
				    'df.funcionario_id = f.funcionario_id',
				    array(
					'funcionario_nome' => new Zend_Db_Expr( 'IFNULL( u.usuario_nome, df.dados_func_nome )' ),
					'funcionario_email' => new Zend_Db_Expr( 'IFNULL( u.usuario_email, df.dados_func_email )' ),
				    )
				)
				->order( 'funcionario_nome' );
	
	return $dbFuncionario->fetchAll( $select );
    }
    
    /**
     *
     * @return mixed
     */
    public function save()
    {
	$dbFuncionario = App_Model_DbTable_Factory::get( 'Funcionario' );
	$dbDadosFuncionario = App_Model_DbTable_Factory::get( 'DadosFuncionario' );
	
	$dbAdapter = $dbFuncionario->getAdapter();
	
	$dbAdapter->beginTransaction();
	try {
	    
	    $dataForm = $this->_data;
	    
	    // Se for usuario, insere ou atualiza o msm
	    if ( 'S' == $this->_data['usuario'] ) {

		$dataForm['usuario_nome'] = $dataForm['funcionario_nome'];
		$dataForm['usuario_email'] = $dataForm['funcionario_email'];
		
		if ( empty( $this->_data['usuario_id'] ) )
		    $dataForm['usuario_status'] = 1;
		
		$mapperUsuario = new Model_Mapper_Usuario();
		$usuario_id = $mapperUsuario->setData( $dataForm )->save();
				
		if ( empty( $usuario_id ) ) {
		    
		    $messages = $mapperUsuario->getMessage()->toArray();
		    
		    if ( empty( $messages ) )
			$this->_message->addMessage( $messages[0]['message'] , App_Message::ERROR );
		    else
			$this->_message->addMessage( $this->_config->messages->error , App_Message::ERROR );
		    
		    return false;
		}
		
	    } else $usuario_id = null;
	    
	    $this->_data['usuario_id'] = $usuario_id;
	    
	    $funcionario_id = parent::_simpleSave( $dbFuncionario );
	    
	    // Se nao for usuario, insere dados do funcionario
	    if ( 'S' != $dataForm['usuario'] ) {
		
		$this->_data = array(
		    'dados_func_id'	=> $dataForm['dados_func_id'],
		    'funcionario_id'	=> $funcionario_id,
		    'dados_func_nome'	=> $dataForm['funcionario_nome'],
		    'dados_func_email'	=> $dataForm['funcionario_email'],
		);
		
		parent::_simpleSave( $dbDadosFuncionario, false );
	    }
	    
	    $dbAdapter->commit();
	    
	    return $funcionario_id;
	    
	} catch ( Exception $e ) {
	    
	    $dbAdapter->rollBack();
	    
	    return false;
	}
    }
    
    /**
     *
     * @return App_Model_DbTable_Row_Abstract
     */
    public function fetchRow()
    {
	$dbFuncionario = App_Model_DbTable_Factory::get( 'Funcionario' );
	$dbDadosFuncionario = App_Model_DbTable_Factory::get( 'DadosFuncionario' );
	$dbUsuario = App_Model_DbTable_Factory::get( 'Usuario' );
	
	$select = $dbFuncionario->select()
				->from( array( 'f' => $dbFuncionario ) )
				->setIntegrityCheck( false )
				->joinLeft(
				    array( 'u' => $dbUsuario ),
				    'u.usuario_id = f.usuario_id',
				    array( 'usuario_login', 'perfil_id', 'usuario_nivel' )
				)
				->joinLeft(
				    array( 'df' => $dbDadosFuncionario ),
				    'df.funcionario_id = f.funcionario_id',
				    array(
					'funcionario_nome' => new Zend_Db_Expr( 'IFNULL( u.usuario_nome, df.dados_func_nome )' ),
					'funcionario_email' => new Zend_Db_Expr( 'IFNULL( u.usuario_email, df.dados_func_email )' ),
					'dados_func_id'
				    )
				)
				->where( 'f.funcionario_id = ?', $this->_data['id'] );
	
	return $dbFuncionario->fetchRow( $select );
    }
}