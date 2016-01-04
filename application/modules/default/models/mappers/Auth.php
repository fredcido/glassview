<?php

/**
 * 
 * @version $Id: Auth.php 453 2012-03-12 20:39:21Z ze $
 */
class Default_Model_Mapper_Auth extends App_Model_Mapper_Abstract
{

    /**
     * Verifica autenticacao do usuario
     * 
     * @access public
     * @return boolean
     */
    public function login()
    {
	$valid = false;

	try {

	    $auth = Zend_Auth::getInstance();

	    $adapter = new Zend_Auth_Adapter_DbTable(
				Zend_Db_Table_Abstract::getDefaultAdapter(),
				'usuario',
				'usuario_login',
				'usuario_senha',
				'SHA1(?)'
			   );

	    $adapter->setIdentity( $this->_data['usuario'] );
	    $adapter->setCredential( $this->_data['senha'] );

	    $select = $adapter->getDbSelect()
		    ->joinLeft(
			    array('perfil' => App_Model_DbTable_Factory::get( 'Perfil' )), 'perfil.perfil_id = usuario.perfil_id', array('perfil_nome')
		    )
		    ->joinLeft(
		    array('linguagem' => App_Model_DbTable_Factory::get( 'Linguagem' )), 'linguagem.linguagem_id = usuario.linguagem_id', array('linguagem.linguagem_local')
	    );

	    $result = $auth->authenticate( $adapter );

	    if ( $result->isValid() ) {

		$row = $adapter->getResultRowObject( null, 'usuario_senha' );

		if ( '1' === $row->usuario_status ) {

		    $auth->getStorage()->write( $row );

		    //Lembrar senha
		    $this->_createCookie();

		    $valid = true;
		} else
		    throw new Exception( 'Login inactive' );
	    } else
		throw new Exception( 'Login is not valid' );
	} catch ( Exception $e ) {

	    $this->_message->addMessage( 'Usu&aacute;rio invalido', App_Message::WARNING );

	    $auth->getStorage()->clear();
	    $auth->clearIdentity();
	}

	return $valid;
    }

    /**
     * Cria cookie para manter usuario logado no periodo de 30 dias
     * 
     * @access protected
     * @return void
     */
    protected function _createCookie()
    {
	//Nome do cookie
	$name = 'auth_' . $this->_config->geral->appid;

	if ( !empty( $this->_data['remember'] ) ) {

	    $dbUsuario = App_Model_DbTable_Factory::get( 'Usuario' );

	    $data = array('usuario_keeplogged' => md5( uniqid( time() ) ));
	    $where = array('usuario_id = ?' => Zend_Auth::getInstance()->getIdentity()->usuario_id);

	    $dbUsuario->update( $data, $where );

	    //Expira em 30 dias
	    setcookie( $name, $data['usuario_keeplogged'], (time() + 60 * 60 * 24 * 30 ), '/' );
	} else {

	    setcookie( $name, false, time(), '/' );
	}
    }

    /**
     * 
     * @access public
     * @param string $cookie
     * @return boolean
     */
    public function loginCookie( $cookie )
    {
	$dbUsuario = App_Model_DbTable_Factory::get( 'Usuario' );
	$dbPerfil = App_Model_DbTable_Factory::get( 'Perfil' );
	$dbLinguagem = App_Model_DbTable_Factory::get( 'Linguagem' );

	$select = $dbUsuario->select()
		->setIntegrityCheck( false )
		->from(
		    array( 'u' => $dbUsuario ), array(
			'usuario_id',
			'perfil_id',
			'usuario_nome',
			'usuario_email',
			'usuario_login',
			'usuario_hash',
			'usuario_nivel'
		    )
		)
		->joinLeft(
			array('p' => $dbPerfil), 'p.perfil_id = u.perfil_id', array('perfil_nome')
		)
		->joinLeft(
			array('l' => $dbLinguagem), 'l.linguagem_id = u.linguagem_id', array('l.linguagem_local')
		)
		->where( 'u.usuario_status = :status' )
		->where( 'u.usuario_keeplogged = :cookie' )
		->bind(
		array(
		    ':status' => 1,
		    ':cookie' => $cookie
		)
	);

	$row = $dbUsuario->fetchRow( $select );

	if ( !empty( $row ) ) {

	    $auth = Zend_Auth::getInstance();
	    $auth->getStorage()->write( $row );

	    return true;
	} else
	    return false;
    }

    /**
     * Ativa opcao para recuperar senha e solicita confirmacao do usuario
     * 
     * @access public
     * @return boolean
     */
    public function recovery()
    {
	try {

	    $dbUsuario = App_Model_DbTable_Factory::get( 'Usuario' );

	    $where = array(
		'usuario_email = ?' => $this->_data['email'],
		'usuario_status = ?' => 1
	    );

	    $row = $dbUsuario->fetchRow( $where );

	    if ( !empty( $row ) ) {

		$data = array('usuario_hash' => md5( uniqid( time() ) ));
		$dbUsuario->update( $data, $where );

		//Gera mensagem 
		$path = APPLICATION_PATH . '/modules/default/views/scripts/auth/';

		$baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();

		$url = 'http://' . $_SERVER['SERVER_NAME'] . $baseUrl . '/auth/password/hash/' . $data['usuario_hash'];

		$view = new Zend_View();
		$view->setScriptPath( $path );
		$view->assign( 'row', $row );
		$view->assign( 'url', $url );

		//Email
		$mail = new Zend_Mail( 'utf-8' );

		$mail->addTo( $row->usuario_email, $row->usuario_nome );
		$mail->setSubject( 'Recuperar Senha - ' . $this->_config->geral->title );
		$mail->setBodyHtml( $view->render( 'mail-recovery.phtml' ) );
		$mail->setFrom( $this->_config->email->address, $this->_config->email->name );

		$mail->send();

		return true;
	    }

	    return false;
	} catch ( Exception $e ) {
	    return false;
	}
    }

    /**
     * Envia nova senha para usuario 
     * 
     * @access public
     * @return boolean
     */
    public function password()
    {
	try {

	    $dbUsuario = App_Model_DbTable_Factory::get( 'Usuario' );

	    $where = array(
		'usuario_hash = ?' => $this->_data['hash'],
		'usuario_status = ?' => 1
	    );

	    $row = $dbUsuario->fetchRow( $where );

	    if ( !empty( $row ) ) {

		// Gera nova senha
		$password = App_General_Password::getRand();

		$data = array(
		    'usuario_hash' => md5( uniqid( time() ) ),
		    'usuario_senha' => sha1( $password )
		);

		$dbUsuario->update( $data, $where );

		//Gera Mensagem
		$path = APPLICATION_PATH . '/modules/default/views/scripts/auth/';

		$view = new Zend_View();
		$view->setScriptPath( $path );
		$view->assign( 'row', $row );
		$view->assign( 'password', $password );

		//Email
		$mail = new Zend_Mail( 'utf-8' );

		$mail->addTo( $row->usuario_email, $row->usuario_nome );
		$mail->setSubject( 'Nova Senha - ' . $this->_config->geral->title );
		$mail->setBodyHtml( $view->render( 'mail-password.phtml' ) );
		$mail->setFrom( $this->_config->email->address, $this->_config->email->name );

		$mail->send();

		return true;
	    }

	    return false;
	} catch ( Exception $e ) {

	    return false;
	}
    }

}