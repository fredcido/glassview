<?php

class Admin_Form_Usuario extends App_Forms_Default
{
    /**
     * 
     */
    public function init()
    {
        $this->setName( 'form-admin-usuario' );
	
	$elements = array();
        
        $elements[] = $this->createElement( 'hidden', 'usuario_id' );

	$elements[] = $this->createElement( 'ValidationTextBox', 'usuario_nome' )
			   ->setLabel( 'Nome' )
			   ->setDijitParam( 'placeHolder', 'Digite o nome do usuário' )
			   ->setAttrib( 'maxlength', 150 )
			   ->setAttrib( 'class', 'input-form' )
			   ->addFilter( 'StringTrim' )
			   ->setRequired( true );
        
	$elements[] = $this->createElement( 'ValidationTextBox', 'usuario_login' )
			   ->setLabel( 'Login' )
			   ->setDijitParam( 'placeHolder', 'Digite o login do usuário' )
			   ->setAttrib( 'maxlength', 100 )
                           ->setAttrib('regExp', '^[a-zA-Z0-9]{4,}$')
			   ->setAttrib( 'class', 'input-form' )
			   ->addFilters( array( 'StringTrim', 'StringToLower' ) )
			   ->setRequired( true );
	
	$passwordConfirmation = new App_Validate_PasswordConfirm();
        
	$elements[] = $this->createElement( 'PasswordTextBox', 'usuario_senha' )
			   ->setLabel( 'Senha' )
			   ->setDijitParam( 'placeHolder', 'Digite senha do usuário' )
			   ->setAttrib( 'maxlength', 30 )
                           ->setAttrib( 'regExp', '^[a-zA-Z0-9_\.-]{5,}$' )
			   ->setAttrib( 'class', 'input-form' )
			   ->addFilter( 'StringTrim' )
			   ->addValidator( $passwordConfirmation )
			   ->setRequired( true );
        
	$elements[] = $this->createElement( 'PasswordTextBox', 'usuario_senha2' )
			   ->setLabel( 'Confirme Senha' )
			   ->setDijitParam( 'placeHolder', 'Confirme senha do usuário' )
			   ->setAttrib( 'maxlength', 30 )
                           ->setAttrib( 'regExp', '^[a-zA-Z0-9_\.-]{5,}$' )
			   ->setAttrib( 'class', 'input-form' )
			   ->addFilter( 'StringTrim' )
			   ->setRequired( true );
        
        
	$elements[] = $this->createElement( 'ValidationTextBox', 'usuario_email' )
			   ->setLabel( 'E-mail' )
			   ->setDijitParam( 'placeHolder', 'Digite o e-mail do usuário' )
			   ->setAttrib( 'maxlength', 100 )
                           ->setAttrib('regExp', '^[a-zA-Z0-9_\.-]{2,}@([A-Za-z0-9_-]{2,}\.)+[A-Za-z]{2,4}$')
			   ->setAttrib( 'class', 'input-form' )
			   ->addFilter( 'StringTrim' )
			   ->addValidator( 'EmailAddress' )
			   ->setRequired( false );
	
        $optUsuarioNivel = array('' => '');
	
        if( Zend_Auth::getInstance()->getIdentity()->usuario_nivel == 'A' )
            $optUsuarioNivel['A'] = 'Administrativo';
        
        $optUsuarioNivel['G'] = 'Gestor';
        $optUsuarioNivel['N'] = 'Normal';

	$elements[] = $this->createElement( 'FilteringSelect', 'usuario_nivel' )
			   ->setLabel( 'Nível' )
                           ->setRequired( true )
			   ->setAttrib( 'class', 'input-form' )
			   ->setAttrib( 'onChange', 'adminUsuario.liberaPerfil()' )
			   ->addMultiOptions( $optUsuarioNivel );
        
	$dbPerfil = new Model_DbTable_Perfil();
        $select = $dbPerfil->select('perfil_id','perfil_nome')
                            ->where( 'perfil_status = :perfil_status' )
                            ->bind( array( ':perfil_status' => 1 ) );

        $data = $dbPerfil->fetchAll( $select );

	$optPerfil = array('' => '' );
	foreach ( $data as $row )
	    $optPerfil[$row->perfil_id] = $row->perfil_nome;

	$elements[] = $this->createElement( 'FilteringSelect', 'perfil_id' )
			   ->setLabel( 'Perfil' )
                           ->setDijitParam( 'placeHolder', 'Selecione o perfil do usuário' )
                           ->setRegisterInArrayValidator(false)
                           ->setRequired( false )
			   ->setAttrib( 'disabled', true )
			   ->setAttrib( 'class', 'input-form' )
			   ->addMultiOptions( $optPerfil );
        
	$dbLinguagem = new Model_DbTable_Linguagem();
	$data        = $dbLinguagem->fetchAll( array( 'linguagem_status = ? ' => 1 ), 'linguagem_nome' );

	$optLinguagem = array('' => '' );
	foreach ( $data as $row )
	    $optLinguagem[$row->linguagem_id] = $row->linguagem_nome;
        
	$elements[] = $this->createElement( 'FilteringSelect', 'linguagem_id' )
			   ->setLabel( 'Linguagem' )
			   ->setAttrib( 'class', 'input-form' )
			   ->addMultiOptions( $optLinguagem )
                           ->setRequired( true );
        
	$elements[] = $this->createElement( 'FilteringSelect', 'usuario_status' )
                           ->setAttrib( 'readOnly', true )
			   ->setLabel( 'Status' )
			   ->setAttrib( 'class', 'input-form' )
			   ->addMultiOptions( array( 1 => 'Ativo', 0 => 'Inativo') );
	
	$this->addElements( $elements );
	
	$this->setDefaultDecorator( 'columns' );
    }
    
    /**
     *
     * @param array $data
     * @return bool 
     */
    public function isValid( $data )
    {
        if ( !empty( $data['usuario_id'] ) ) {

            $this->getElement( 'usuario_senha' )->setRequired( false );
            $this->getElement( 'usuario_senha2' )->setRequired( false );
        }
	
	if ( 'N' == $data['usuario_nivel'] )
	    $this->getElement( 'perfil_id' )->setRequired( true );

        return parent::isValid( $data );
    }
}