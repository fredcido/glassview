<?php

/**
 * 
 * @version $Id: Preferencia.php 386 2012-03-02 18:44:25Z helion $
 */
class Default_Form_Preferencia extends App_Forms_Default
{
    /**
     * 
     */
    public function init()
    {
        $this->setName( 'form-default-preferencia' );
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
                           ->setAttrib( 'readOnly', true )
                           ->setAttrib('regExp', '^[a-zA-Z0-9]{4,}$')
			   ->setAttrib( 'class', 'input-form' )
			   ->addFilter( 'StringTrim' )
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
			   ->setRequired( false );
        
	$elements[] = $this->createElement( 'PasswordTextBox', 'usuario_senha2' )
			   ->setLabel( 'Confirme Senha' )
			   ->setDijitParam( 'placeHolder', 'Confirme senha do usuário' )
			   ->setAttrib( 'maxlength', 30 )
                           ->setAttrib( 'regExp', '^[a-zA-Z0-9_\.-]{5,}$' )
			   ->setAttrib( 'class', 'input-form' )
			   ->addFilter( 'StringTrim' )
			   ->setRequired( false );
        
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
        
	$this->addElements( $elements );
    }
}