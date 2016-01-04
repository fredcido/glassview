<?php

class Default_Form_Lembrete extends App_Forms_Default
{
    /**
     * 
     */
    public function init()
    {
        $this->setName( 'form-admin-usuario' );
	
	$elementosDados = array();
        
        $elementosDados[] = $this->createElement( 'hidden', 'lembrete_id' );
	
	$elementosDados[] = $this->createElement( 'ValidationTextBox', 'lembrete_titulo' )
			   ->setLabel( 'Título' )
			   ->setDijitParam( 'placeHolder', 'Digite o título do lembrete' )
			   ->setAttrib( 'maxlength', 60 )
			   ->setAttrib( 'class', 'input-form' )
			   ->addFilter( 'StringTrim' )
			   ->setRequired( true );
	
	$optNivel[1] = 'Normal';
	$optNivel[0] = 'Urgente';
	
	$elementosDados[] = $this->createElement( 'FilteringSelect', 'lembrete_nivel' )
			   ->setLabel( 'Nível' )
                           ->setRequired( true )
			   ->setAttrib( 'class', 'input-form' )
			   ->addMultiOptions( $optNivel );
	
	$elementosDados[] = $this->createElement( 'DateTextBox', 'lembrete_data_prevista' )
			   ->setLabel( 'Data Prevista' )
                           ->setAttrib( 'style', 'width: 100px;' );
	
	$elementosDados[] = $this->createElement( 'TimeTextBox', 'lembrete_hora_prevista' )
			   ->setLabel( 'Hora Prevista' )
			   ->addFilter( 'StringTrim' )
                           ->setAttrib( 'style', 'width:100px;' );
	
	$elementosDados[] = $this->createElement( 'SimpleTextarea', 'lembrete_msg' )
			   ->setLabel( 'Lembrete' )
			   ->setAttrib( 'maxlength', 500 )
			   ->setAttrib( 'cols', '28' )
			   ->setRequired( true )
			   ->setDijitParam( 'progressObserver', true )
			   ->addFilters( array( 'StringTrim', 'StripTags' ) );

	$this->addDisplayGroup( $elementosDados, 'dados', 
				array( 'decorators' => array( 'FormElements', 
							 array( 'ContentPane', array( 'title' => 'Dados' ) )
						       ) 
				    )
			       );
	
	
	$elementosDestino = array();
	
	// Lista usuarios para salvar os lembretes
	$dbUsuario = App_Model_DbTable_Factory::get( 'Usuario' );
	$usuarios = $dbUsuario->fetchAll( array( 'usuario_status = ?' => 1 ), array( 'usuario_nome' ) );
	
	$optUsuarios = array();
	foreach ( $usuarios as $usuario )
	    $optUsuarios[$usuario->usuario_id] = $usuario->usuario_nome;
	
	$elementosDestino[] = $this->createElement( 'Multiselect', 'usuarios' )
			   ->setLabel( 'Usuários' )
			   ->setAttrib( 'style', 'width: 250px;' )
			   ->setMultiOptions( $optUsuarios );
	
	// Lista perfis
	$dbPerfil = App_Model_DbTable_Factory::get( 'Perfil' );
	$perfis = $dbPerfil->fetchAll( array( 'perfil_status = ?' => 1 ), array( 'perfil_nome' ) );
	
	$optPerfis = array();
	foreach ( $perfis as $perfil )
	    $optPerfis[$perfil->perfil_id] = $perfil->perfil_nome;
	
	$elementosDestino[] = $this->createElement( 'Multiselect', 'perfis' )
			   ->setLabel( 'Perfis' )
			   ->setAttrib( 'style', 'width: 250px;' )
			   ->setMultiOptions( $optPerfis );
	
	// Lista perfis
	$dbPerfil = App_Model_DbTable_Factory::get( 'Perfil' );
	$perfis = $dbPerfil->fetchAll( array( 'perfil_status = ?' => 1 ), array( 'perfil_nome' ) );
	
	$optPerfis = array();
	foreach ( $perfis as $perfil )
	    $optPerfis[$perfil->perfil_id] = $perfil->perfil_nome;
	
	$elementosDestino[] = $this->createElement( 'Multiselect', 'perfis' )
			   ->setLabel( 'Perfis' )
			   ->setAttrib( 'style', 'width: 250px;' )
			   ->setMultiOptions( $optPerfis );
	
	$optNivelUser['A'] = 'Administrador';
	$optNivelUser['G'] = 'Gestor';
	$optNivelUser['N'] = 'Normal';
	
	$elementosDestino[] = $this->createElement( 'Multiselect', 'nivel' )
			   ->setLabel( 'Nível' )
			   ->setAttrib( 'style', 'width: 250px;' )
			   ->setMultiOptions( $optNivelUser );
	
	$this->addDisplayGroup( $elementosDados, 'elementos-dados', array( 'decorators' => array( 'FormElements' ) ) );
	$this->addDisplayGroup( $elementosDestino, 'elementos-destino', array( 'decorators' => array( 'FormElements' ) ) );
	
	$this->_defineDecorators()
	     ->setRenderDefaultDecorators( false )
	     ->setRenderDefaultToolbar( false );
    }
}