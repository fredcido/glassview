<?php

class Admin_Form_Filial extends App_Forms_Default
{
    /**
     * 
     */
    public function init()
    {
        $this->setName( 'form-admin-filial' );
        
        $elements[] = $this->createElement( 'hidden', 'filial_id' );

	$elements[] = $this->createElement( 'ValidationTextBox', 'filial_nome' )
			   ->setLabel( 'Nome' )
			   ->setDijitParam( 'placeHolder', 'Digite o nome da filial' )
			   ->setAttrib( 'maxlength', 70 )
			   ->setAttrib( 'class', 'input-form' )
			   ->addFilter( 'StringTrim' )
			   ->setRequired( true );

	$elements[] = $this->createElement( 'ValidationTextBox', 'filial_fax' )
			   ->setLabel( 'Telefone' )
			   ->setDijitParam( 'placeHolder', 'Digite o telefone da filial' )
			   ->setAttrib( 'maxlength', 40 )
			   ->setAttrib( 'class', 'input-form' )
			   ->addFilter( 'StringTrim' )
			   ->setRequired( false );

	$elements[] = $this->createElement( 'ValidationTextBox', 'filial_telefone' )
			   ->setLabel( 'Fax' )
			   ->setDijitParam( 'placeHolder', 'Digite o fax da filial' )
			   ->setAttrib( 'maxlength', 40 )
			   ->setAttrib( 'class', 'input-form' )
			   ->addFilter( 'StringTrim' )
			   ->setRequired( false );

	$dbPais = new Model_DbTable_Pais();
	$data = $dbPais->fetchAll( array(), 'pais_nome' );

	$optPais = array('' => '' );
	foreach ( $data as $row )
	    $optPais[$row->pais_id] = $row->pais_nome;

	$elements[] = $this->createElement( 'FilteringSelect', 'pais_id' )
			   ->setLabel( 'País' )
                           ->setRegisterInArrayValidator(false)
                           ->setAttrib( 'onChange', 'adminFilial.changeEstado(this);' )
                           ->setRequired( true )
			   ->setAttrib( 'class', 'input-form' )
			   ->addMultiOptions( $optPais );

        $optEstado = array('' => '' );
	$elements[] = $this->createElement( 'FilteringSelect', 'estado_id' )
                           ->setAttrib( 'disabled', 'disabled' )
                           ->setRegisterInArrayValidator(false)
			   ->setLabel( 'Estado' )
			   ->setAttrib( 'class', 'input-form' )
                           ->setAttrib( 'onChange', 'adminFilial.changeCidade(this);' )
                           ->setRequired( true )
			   ->addMultiOptions( $optEstado );

	$baseUrl = $this->getView()->baseUrl();
		
	$elements[] = $this->createElement( 'FilteringSelect', 'cidade_id' )
			   ->setLabel( 'Cidade' )
                           ->setAttrib( 'disabled', 'disabled' )
			   ->setAttrib( 'class', 'input-form' )
//			   ->addFilter( 'StringTrim' )
//			   ->setStoreType( 'dojo.data.ItemFileReadStore' )
//			   ->setDijitParam( 'hasDownArrow', 'false' )
//			   ->setStoreId( 'cityStore' )
//			   ->setStoreParams( array( 'url' => $baseUrl . '/admin/filial/cities/' ) )
//			   ->setAttrib( 'maxlength', 150 )
			   ->setRegisterInArrayValidator( false )
                           ->setRequired( true );
	
	$elements[] = $this->createElement( 'FilteringSelect', 'filial_status' )
                           ->setAttrib( 'readOnly', true )
			   ->setLabel( 'Status' )
			   ->setAttrib( 'class', 'input-form' )
			   ->addMultiOptions( array( 1 => 'Ativo' , 0 => 'Inativo' ) );
	
	$elements[] = $this->createElement( 'SimpleTextarea', 'filial_endereco' )
			   ->setLabel( 'Endereço' )
			   ->setAttrib( 'maxlength', 255 )
			   ->setDijitParam( 'progressObserver', true )
			   ->addFilters( array( 'StringTrim', 'StripTags' ) );
	
	$this->addElements( $elements );
    }
}