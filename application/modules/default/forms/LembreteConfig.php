<?php

class Default_Form_LembreteConfig extends App_Forms_Default
{
    /**
     * 
     */
    public function init()
    {
        $this->setName( 'form-default-lembrete-config' );
	
	$elements = array();
	
	$optTipo[''] = '';
	$optTipo['E'] = 'Estoque';
	$optTipo['L'] = 'Lançamento';
	
	$elements[] = $this->createElement( 'FilteringSelect', 'lembrete_config_tipo' )
			   ->setLabel( 'Tipo' )
			   ->setAttrib( 'class', 'input-form' )
			   ->setAttrib( 'onChange', 'defaultLembreteConfig.buscaConfigLembretes' )
			   ->setRequired( true )
			   ->addMultiOptions( $optTipo );
	
	// Lista perfis
	$dbPerfil = App_Model_DbTable_Factory::get( 'Perfil' );
	$perfis = $dbPerfil->fetchAll( array( 'perfil_status = ?' => 1 ), array( 'perfil_nome' ) );
	
	$optPerfis = array();
	foreach ( $perfis as $perfil )
	    $optPerfis[$perfil->perfil_id] = $perfil->perfil_nome;
	
	$elements[] = $this->createElement( 'Multiselect', 'perfis' )
			   ->setLabel( 'Perfis' )
			   ->setRequired( true )
			   ->setAttrib( 'dojoType', 'dijit.form.MultiSelect' )
			   ->setAttrib( 'style', 'width: 250px;height: 150px' )
			   ->setMultiOptions( $optPerfis );
	
	$this->addElements( $elements );
    }
}