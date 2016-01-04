<?php

class Admin_Form_Funcionario extends App_Forms_Default
{
    /**
     * 
     */
    public function init()
    {
        $this->setName( 'form-admin-funcionario' );
	
	$elementsFunc = array();
	$elementsUser = array();
        
        $elementsFunc[] = $this->createElement( 'hidden', 'funcionario_id' );
        $elementsFunc[] = $this->createElement( 'hidden', 'dados_func_id' );
        $elementsFunc[] = $this->createElement( 'hidden', 'usuario_id' );

	$elementsFunc[] = $this->createElement( 'ValidationTextBox', 'funcionario_nome' )
			   ->setLabel( 'Nome' )
			   ->setDijitParam( 'placeHolder', 'Digite o nome do funcionário' )
			   ->setAttrib( 'maxlength', 150 )
			   ->setAttrib( 'class', 'input-form' )
			   ->addFilter( 'StringTrim' )
			   ->setRequired( true );
	
	$dbCargo = new Model_DbTable_Cargo();
	$data = $dbCargo->fetchAll( array(), 'cargo_nome' );

	$optCargo = array('' => '' );
	foreach ( $data as $row )
	    $optCargo[$row->cargo_id] = $row->cargo_nome;

	$elementsFunc[] = $this->createElement( 'FilteringSelect', 'cargo_id' )
			   ->setLabel( 'Cargo' )
                           ->setDijitParam( 'placeHolder', 'Selecione o cargo do funcionário' )
                           ->setRequired( true )
			   ->setAttrib( 'class', 'input-form' )
			   ->addMultiOptions( $optCargo );

	$elementsFunc[] = $this->createElement( 'ValidationTextBox', 'funcionario_email' )
			   ->setLabel( 'E-mail' )
			   ->setDijitParam( 'placeHolder', 'Digite o e-mail do funcionário' )
			   ->setAttrib( 'maxlength', 100 )
                           ->setAttrib('regExp', '^[a-zA-Z0-9_\.-]{2,}@([A-Za-z0-9_-]{2,}\.)+[A-Za-z]{2,4}$')
			   ->setAttrib( 'class', 'input-form' )
			   ->addFilter( 'StringTrim' )
			   ->addValidator( 'EmailAddress' )
			   ->setRequired( false );
	
	$dbFilial = new Model_DbTable_Filial();
	$data = $dbFilial->fetchAll( array(), 'filial_nome' );

	$optFilial = array('' => '' );
	foreach ( $data as $row )
	    $optFilial[$row->filial_id] = $row->filial_nome;

	$elementsFunc[] = $this->createElement( 'FilteringSelect', 'filial_id' )
			   ->setLabel( 'Filial' )
                           ->setDijitParam( 'placeHolder', 'Selecione a filial do funcionário' )
                           ->setRegisterInArrayValidator( false )
                           ->setRequired( true )
			   ->setAttrib( 'class', 'input-form' )
			   ->addMultiOptions( $optFilial );
	
	$optSimNao['N'] = 'Não';
	$optSimNao['S'] = 'Sim';
	
	$elementsUser[] = $this->createElement( 'FilteringSelect', 'usuario' )
			   ->setLabel( 'Usuário' )
			   ->setAttrib( 'class', 'input-form' )
			   ->setAttrib( 'onChange', 'adminFuncionario.liberaUsuario()' )
			   ->addMultiOptions( $optSimNao );
	
	$optUsuarioNivel[''] = '';
	$optUsuarioNivel['G'] = 'Gestor';
        $optUsuarioNivel['N'] = 'Normal';

	$elementsUser[] = $this->createElement( 'FilteringSelect', 'usuario_nivel' )
			   ->setLabel( 'Nível' )
			   ->setAttrib( 'class', 'input-form' )
			   ->setAttrib( 'readOnly', true )
			   ->setDijitParam( 'placeHolder', 'Selecione o nível do usuário' )
			   ->setAttrib( 'onChange', 'adminFuncionario.liberaPerfil()' )
			   ->addMultiOptions( $optUsuarioNivel );
	
	$elementsUser[] = $this->createElement( 'ValidationTextBox', 'usuario_login' )
			   ->setLabel( 'Login' )
			   ->setAttrib( 'maxlength', 100 )
			   ->setDijitParam( 'placeHolder', 'Digite o login do usuário' )
                           ->setAttrib('regExp', '^[a-zA-Z0-9]{4,}$')
			   ->setAttrib( 'readOnly', true )
			   ->setAttrib( 'class', 'input-form' )
			   ->addFilters( array( 'StringTrim', 'StringToLower' ) );
	
	$passwordConfirmation = new App_Validate_PasswordConfirm();
	
	$elementsUser[] = $this->createElement( 'PasswordTextBox', 'usuario_senha' )
			   ->setLabel( 'Senha' )
			   ->setDijitParam( 'placeHolder', 'Digite senha do usuário' )
			   ->setAttrib( 'maxlength', 30 )
			   ->setAttrib( 'readOnly', true )
                           ->setAttrib( 'regExp', '^[a-zA-Z0-9_\.-]{5,}$' )
			   ->setAttrib( 'class', 'input-form' )
			   ->addFilter( 'StringTrim' )
			   ->addValidator( $passwordConfirmation );
	
	$dbPerfil = new Model_DbTable_Perfil();
        $select = $dbPerfil->select('perfil_id','perfil_nome')
                            ->where( 'perfil_status = :perfil_status' )
                            ->bind( array( ':perfil_status' => 1 ) );

	$data = $dbPerfil->fetchAll( $select );
	
	$optPerfil = array( '' => '' );
	foreach ( $data as $row )
	    $optPerfil[$row->perfil_id] = $row->perfil_nome;

	$elementsUser[] = $this->createElement( 'FilteringSelect', 'perfil_id' )
			   ->setLabel( 'Perfil' )
                           ->setDijitParam( 'placeHolder', 'Selecione o perfil do usuário' )
                           ->setRequired( false )
			   ->setAttrib( 'readOnly', true )
			   ->setAttrib( 'class', 'input-form' )
			   ->addMultiOptions( $optPerfil );
        
	$elementsUser[] = $this->createElement( 'PasswordTextBox', 'usuario_senha2' )
			   ->setLabel( 'Confirme Senha' )
			   ->setDijitParam( 'placeHolder', 'Confirme senha do usuário' )
			   ->setAttrib( 'maxlength', 30 )
			   ->setAttrib( 'readOnly', true )
                           ->setAttrib( 'regExp', '^[a-zA-Z0-9_\.-]{5,}$' )
			   ->setAttrib( 'class', 'input-form' )
			   ->addFilter( 'StringTrim' );
	
	
	$elementsFunc[] = $this->createElement( 'CurrencyTextBox', 'funcionario_salario' )
			   ->setLabel( 'Salário' )
			   ->setDijitParam( 'placeHolder', 'Digite o salário do funcionário' )
			   ->setDijitParam( 'currency', 'R$ ' )
			   ->setAttrib( 'class', 'input-form' )
			   ->setRequired( true );
	
	$elementsFunc[] = $this->createElement( 'ValidationTextBox', 'funcionario_carga_diaria' )
			   ->setLabel( 'Carga Diária' )
			   ->setDijitParam( 'placeHolder', 'Digite a carga horária diária.' )
			   ->setAttrib( 'class', 'input-form' )
			   ->setAttrib('regExp', '[0-9]+')
			   ->setAttrib( 'maxlength', 11 )
			   ->setValue( 8 )
			   ->setRequired( true );
	
	$elementsFunc[] = $this->createElement( 'ValidationTextBox', 'funcionario_carga_mensal' )
			   ->setLabel( 'Carga Mensal' )
			   ->setDijitParam( 'placeHolder', 'Digite a carga horária mensal.' )
			   ->setAttrib( 'class', 'input-form' )
			   ->setAttrib( 'regExp', '[0-9]+' )
			   ->setAttrib( 'maxlength', 11 )
			   ->setValue( 220 )
			   ->setRequired( true );
	
	$elementsFunc[] = $this->createElement( 'ValidationTextBox', 'funcionario_cpf_cnpj' )
			   ->setLabel( 'CPF / CNPJ' )
			   ->setDijitParam( 'placeHolder', 'Digite o CPF ou CNPJ' )
			   ->setAttrib( 'maxlength', 20 )
                           ->setAttrib('regExp', '^\d{0,}$')
			   ->setAttrib( 'class', 'input-form' )
			   ->addFilter( 'StringTrim' )
			   ->setRequired( false );
	
	$elementsFunc[] = $this->createElement( 'ValidationTextBox', 'funcionario_telefone' )
			   ->setLabel( 'Telefone' )
			   ->setDijitParam( 'placeHolder', 'Digite o telefone do funcionário.' )
			   ->setAttrib( 'class', 'input-form' )
			   ->setAttrib( 'maxlength', 50 )
			   ->setRequired( false );
	
	$elementsFunc[] = $this->createElement( 'SimpleTextarea', 'funcionario_endereco' )
			   ->setLabel( 'Endereço' )
			   ->setAttrib( 'maxlength', 400 )
			   ->setAttrib( 'style', 'height: 50px; width: 540px;' )
			   ->setDijitParam( 'progressObserver', true )
			   ->addFilters( array( 'StringTrim', 'StripTags' ) );
	
	$this->addDisplayGroup( $elementsFunc, 'elementos-func', array( 'decorators' => array( 'FormElements' ) ) );
	$this->addDisplayGroup( $elementsUser, 'elementos-user', array( 'decorators' => array( 'FormElements' ) ) );
	
	$this->setDefaultDecorator( 'columns' )
	     ->_defineDecorators()
	     ->setRenderDefaultDecorators( false )
	     ->setRenderDefaultToolbar( false );
	
	$this->getElement( 'funcionario_endereco' )->removeDecorator( 'float' );
    }
    
    /**
     *
     * @param array $data
     * @return bool 
     */
    public function isValid( $data )
    {
        if ( 'S' == $data['usuario'] && empty( $data['usuario_id'] ) ) {

            $this->getElement( 'usuario_login' )->setRequired( true );
            $this->getElement( 'usuario_senha' )->setRequired( true );
            $this->getElement( 'usuario_senha2' )->setRequired( true );
	    
	    
	    if ( 'N' == $data['usuario_nivel'] )
		$this->getElement( 'perfil_id' )->setRequired( true );
        }
	
        return parent::isValid( $data );
    }
}