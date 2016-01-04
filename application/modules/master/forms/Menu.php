<?php

class Master_Form_Menu extends App_Forms_Default
{
    /**
     * 
     */
    public function init()
    {
	$this->setName( 'form-master-menu' );
	
	$elements = array();
	
        $elements[] = $this->createElement( 'hidden', 'menu_id' );

	$elements[] = $this->createElement( 'ValidationTextBox', 'menu_label' )
			   ->setLabel( 'Label' )
			   ->setDijitParam( 'placeHolder', 'Digite a label do menu' )
			   ->setAttrib( 'maxlength', 100 )
			   ->setAttrib( 'class', 'input-form' )
			   ->addFilter( 'StringTrim' )
			   ->setRequired( true );
	
	$optTipo[''] = '';
	$optTipo['A'] = 'Aba';
	$optTipo['G'] = 'Agrupador';
	$optTipo['C'] = 'Customizado';
	$optTipo['D'] = 'Dialog';
	
	$elements[] = $this->createElement( 'FilteringSelect', 'menu_tipo' )
			   ->setLabel( 'Tipo' )
			   ->addMultiOptions( $optTipo )
			   ->setAttrib( 'class', 'input-form' )
			   ->setDijitParam( 'placeHolder', 'Selecione o tipo do menu' )
			   ->setRequired( true );
	
	$dbTela = new Model_DbTable_Tela();
	$data = $dbTela->fetchAll( array(), 'tela_nome' );
	
	$opt[''] = '';
	foreach ( $data as $row )
	    $opt[$row->tela_id] = $row->tela_nome;
	
	$elements[] = $this->createElement( 'FilteringSelect', 'tela_id' )
			   ->setLabel( 'Tela' )
			   ->addMultiOptions( $opt )
			   ->setAttrib( 'class', 'input-form' )
			   ->setDijitParam( 'placeHolder', 'Selecione a tela' );
	
	$baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
	
	$elements[] = $this->createElement( 'FilteringSelect', 'menu_icon' )
			   ->setLabel( 'Classe do ícone' )
			   ->setDijitParam( 'placeHolder', 'Selecione a classe para o ícone' )
			   ->setStoreType( 'dojo.data.ItemFileReadStore' )
			   ->setStoreId( 'iconsStore' )
			   ->setStoreParams( array( 'url' => $baseUrl . '/master/menu/icons/' ) )
			   ->setDijitParam( 'labelType', 'html' )
			   ->setDijitParam( 'labelAttr', 'label' )
			   ->setAttrib( 'class', 'input-form' )
			   ->addFilter( 'StringTrim' );
	
	$elements[] = $this->createElement( 'ValidationTextBox', 'menu_exec' )
			   ->setLabel( 'Callback' )
			   ->setDijitParam( 'placeHolder', 'Digite a função de execução' )
			   ->setAttrib( 'maxlength', 100 )
			   ->setAttrib( 'class', 'input-form' )
			   ->addFilter( 'StringTrim' );

	$this->addElements( $elements );
    }
}