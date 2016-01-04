<?php

class Admin_Form_Permissao extends App_Forms_Default
{
    /**
     * 
     */
    public function init()
    {
        $this->setName( 'form-admin-permissao' );
	
	$elements = array();
        
        $elements[] = $this->createElement( 'hidden', 'acao_id' )
			   ->setIsArray( true );


	$dbPerfil = new Model_DbTable_Perfil();
	$data = $dbPerfil->fetchAll( array(), 'perfil_nome' );

	$optPerfil = array('' => '' );
	foreach ( $data as $row )
	    $optPerfil[$row->perfil_id] = $row->perfil_nome;

	$elements[] = $this->createElement( 'FilteringSelect', 'perfil_id' )
			   ->setLabel( 'Perfil' )
                           ->setRegisterInArrayValidator(false)
                           ->setAttrib( 'onChange', 'adminPermissao.carregaPermissoes();' )
                           ->setRequired( true )
			   ->setAttrib( 'class', 'input-form' )
			   ->addMultiOptions( $optPerfil );
	
	$this->addElements( $elements );
	
	$this->_defineDecorators();
    }
}