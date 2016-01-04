<?php

class Almoxarifado_Form_Ativo extends App_Forms_Default
{
    /**
     * 
     */
    public function init()
    {
        $this->setName( 'form-admin-ativo' );
	
	$elements = array();
        
        $elements[] = $this->createElement( 'hidden', 'ativo_id' );

	$elements[] = $this->createElement( 'ValidationTextBox', 'ativo_nome' )
			   ->setLabel( 'Nome' )
			   ->setDijitParam( 'placeHolder', 'Digite o nome do ativo' )
			   ->setAttrib( 'maxlength', 70 )
			   ->setAttrib( 'class', 'input-form' )
			   ->addFilter( 'StringTrim' )
			   ->setRequired( true );
	
	$dbTipoAtivo = new Model_DbTable_TipoAtivo();
	$data = $dbTipoAtivo->fetchAll( array(), 'tipo_ativo_nome' );

	$optTipoAtivo = array('' => '' );
	foreach ( $data as $row )
	    $optTipoAtivo[$row->tipo_ativo_id] = $row->tipo_ativo_nome;

	$elements[] = $this->createElement( 'FilteringSelect', 'tipo_ativo_id' )
			   ->setLabel( 'Tipo de ativo' )
                           ->setDijitParam( 'placeHolder', 'Selecione o tipo de ativo' )
                           ->setRequired( true )
			   ->setAttrib( 'class', 'input-form' )
			   ->addMultiOptions( $optTipoAtivo );
	
	$dbSituacaoAtivo = new Model_DbTable_SituacaoAtivo();
	$data = $dbSituacaoAtivo->fetchAll( array(), 'situacao_ativo_nome' );

	$optSituacaoAtivo = array('' => '' );
	foreach ( $data as $row )
	    $optSituacaoAtivo[$row->situacao_ativo_id] = $row->situacao_ativo_nome;

	$elements[] = $this->createElement( 'FilteringSelect', 'situacao_ativo_id' )
			   ->setLabel( 'Situação de ativo' )
                           ->setDijitParam( 'placeHolder', 'Selecione a situação do ativo' )
                           ->setRequired( true )
			   ->setAttrib( 'class', 'input-form' )
			   ->addMultiOptions( $optSituacaoAtivo );
	
	$dbFilial = new Model_DbTable_Filial();
	$data = $dbFilial->fetchAll( array(), 'filial_nome' );

	$optFilial = array('' => '' );
	foreach ( $data as $row )
	    $optFilial[$row->filial_id] = $row->filial_nome;

	$elements[] = $this->createElement( 'FilteringSelect', 'filial_id' )
			   ->setLabel( 'Localização' )
                           ->setDijitParam( 'placeHolder', 'Selecione a filial do funcionário' )
                           ->setRegisterInArrayValidator( false )
                           ->setRequired( true )
			   ->setAttrib( 'class', 'input-form' )
			   ->addMultiOptions( $optFilial );
	
	$elements[] = $this->createElement( 'CurrencyTextBox', 'ativo_valor' )
			   ->setLabel( 'Valor' )
			   ->setDijitParam( 'placeHolder', 'Digite o valor do ativo' )
			   ->setDijitParam( 'currency', 'R$ ' )
			   ->setAttrib( 'class', 'input-form' );
	
	$elements[] = $this->createElement( 'ValidationTextBox', 'ativo_patrimonio' )
			   ->setLabel( 'Patrimônio' )
			   ->setDijitParam( 'placeHolder', 'Digite o número do patrimônio' )
			   ->setAttrib( 'maxlength', 50 )
			   ->setAttrib( 'class', 'input-form' )
			   ->addFilter( 'StringTrim' );
	
	$elements[] = $this->createElement( 'DateTextBox', 'ativo_aquisicao' )
			   ->setLabel( 'Data de Aquisição' )
			   ->setAttrib( 'class', 'input-form' );

	$optStatus[1] = 'Ativo';
	$optStatus[0] = 'Inativo';
	
	$elements[] = $this->createElement( 'FilteringSelect', 'ativo_status' )
                           ->setAttrib( 'readOnly', true )
			   ->setAttrib( 'class', 'input-form' )
			   ->setLabel( 'Status' )
			   ->addMultiOptions( $optStatus );
	
	$elements[] = $this->createElement( 'SimpleTextarea', 'ativo_descricao' )
                   ->setLabel( 'Descrição' )
                   ->setAttrib( 'maxlength', 400 )
                   ->setDijitParam( 'progressObserver', true )
		   ->setAttrib( 'style', 'height: 50px; width: 540px;' )
                   ->addFilters( array( 'StringTrim', 'StripTags' ) );
	
	$this->addElements( $elements );
	
	$this->setDefaultDecorator( 'columns' )
	     ->_defineDecorators()
	     ->setRenderDefaultDecorators( false )
	     ->setRenderDefaultToolbar( false );
	
	$this->getElement( 'ativo_descricao' )->removeDecorator( 'float' );
    }
}