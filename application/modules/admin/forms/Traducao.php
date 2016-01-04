<?php

class Admin_Form_Traducao extends App_Forms_Default
{
    /**
     * 
     */
    public function init()
    {
        $this->setName( 'form-admin-traducao' );
	
	$elements = array();

        $t = $this->getView()->translate();

        $elements[] = $this->createElement( 'hidden', 'traducao_id' );

        $optTipo =  array( '' => '' , 'M' => $t->_('Menu'), 'T' => $t->_('Termo'));

	$elements[] = $this->createElement( 'FilteringSelect', 'traducao_tipo' )
			   ->setLabel( 'Tipo' )
			   ->setAttrib( 'class', 'input-form' )
                           ->setDijitParam( 'placeHolder' , $t->_('Selecione o tipo de tradução') )
                           ->setAttrib( 'onChange', 'adminTraducao.carregaTermo();' )
			   ->addMultiOptions( $optTipo )
                           ->setRequired( true );
        
	$dbLinguagem = new Model_DbTable_Linguagem();
	$data        = $dbLinguagem->fetchAll( array( 'linguagem_status = ? ' => 1 ), 'linguagem_nome' );

	$optLinguagem = array('' => '' );
	foreach ( $data as $row )
	    $optLinguagem[$row->linguagem_id] = $row->linguagem_nome;
        
	$elements[] = $this->createElement( 'FilteringSelect', 'linguagem_id' )
			   ->setLabel( 'Linguagem' )
			   ->setAttrib( 'class', 'input-form' )
                           ->setAttrib( 'onChange', 'adminTraducao.carregaTermo();' )
			   ->addMultiOptions( $optLinguagem )
                           ->setRequired( true );
	   
	$elements[] = $this->createElement( 'FilteringSelect', 'menu_termo_id' )
			   ->setLabel( 'Menu/Termo' )
                           ->setRegisterInArrayValidator(false)
			   ->setAttrib( 'class', 'input-form' )
			   ->addMultiOptions(  array( '' => '' ) )
                           ->setAttrib( 'disabled', 'disabled' )
                           ->setRequired( true );
        
	$elements[] = $this->createElement( 'SimpleTextarea', 'traducao_desc' )
			   ->setLabel( 'Tradução' )
			   ->setAttrib( 'maxlength', 200 )
			   ->setAttrib( 'class', 'input-form' )
			   ->setDijitParam( 'progressObserver', true )
			   ->addFilters( array( 'StringTrim', 'StripTags' ) )
                           ->setRequired( true );
	
	$this->addElements( $elements );
    }
}