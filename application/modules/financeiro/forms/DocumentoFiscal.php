<?php

class Financeiro_Form_DocumentoFiscal extends App_Forms_Default
{
    /**
     * 
     */
    public function init()
    {
        $this->setName( 'form-financeiro-documento-fiscal' );
	
	$elements = array();
        
        $elements[] = $this->createElement( 'hidden', 'fn_doc_fiscal_id' );

        $dbTerceiro = new Model_DbTable_Terceiro();
	$data = $dbTerceiro->fetchAll( array(), 'terceiro_nome' );

	$optTerceiro = array( '' => '' );
	foreach ( $data as $row )
	    $optTerceiro[$row->terceiro_id] = $row->terceiro_nome;

	$elements[] = $this->createElement( 'FilteringSelect', 'terceiro_id_remetente' )
			   ->setLabel( 'Remetente' )
                           ->setRegisterInArrayValidator(false)
			   ->addMultiOptions( $optTerceiro )
                           ->setAttrib( 'class', 'input-form' )
                           ->setRequired( true );

	$elements[] = $this->createElement( 'FilteringSelect', 'terceiro_id_destinatario' )
			   ->setLabel( 'Destinatário' )
                           ->setRegisterInArrayValidator(false)
			   ->addMultiOptions( $optTerceiro )
                           ->setAttrib( 'class', 'input-form' )
                           ->setRequired( true );

        $elements[] = $this->createElement( 'ValidationTextBox', 'fn_doc_fiscal_numero' )
                           ->setLabel(  'Número' )
                           ->setAttrib( 'maxlength', 100 )
                           ->setDijitParam( 'placeHolder', 'Digite o Número do Documento Fiscal' )
                           ->setAttrib( 'class', 'input-form' )
                           ->addFilter( 'StringTrim' )
                           ->setRequired( true );

	$elements[] = $this->createElement( 'DateTextBox', 'fn_doc_fiscal_data' )
			   ->setLabel( 'Data' )
                           ->setValue( Zend_Date::now()->toString('yyyy-MM-dd') )
			   ->addFilter( 'StringTrim' )
			   ->setRequired( true )
                           ->setAttrib('style', 'width:100px;');
        
        $elements[] = $this->createElement( 'CurrencyTextBox', 'fn_doc_fiscal_valor_total_da_nota' )
			   ->setLabel( 'Valor Total' )
			   ->setAttrib( 'class', 'input-form' )
			   ->setDijitParam( 'currency', 'R$ ' )
			   ->addFilter( 'StringTrim' )
                           ->setValue(0)
                           ->setAttrib( 'readOnly', true );
        
        $elements[] = $this->createElement( 'ValidationTextBox', 'fn_doc_fiscal_chave' )
                           ->setLabel(  'Chave' )
                           ->setDijitParam( 'placeHolder', 'Digite a Chave do Documento Fiscal' )
                           ->setAttrib( 'maxlength', 120 )
                           ->setAttrib( 'class', 'input-form' )
                           ->addFilter( 'StringTrim' )
                           ->setRequired( false );

	$elements[] = $this->createElement( 'hidden', 'fn_doc_fiscal_item_descricao' )
                           ->setIsArray( true );

	$elements[] = $this->createElement( 'hidden', 'fn_doc_fiscal_item_qtde' )
                           ->setIsArray( true );
        
	$elements[] = $this->createElement( 'hidden', 'fn_doc_fiscal_item_valor' )
                           ->setIsArray( true );

	$elements[] = $this->createElement( 'hidden', 'fn_doc_fiscal_item_total' )
                           ->setIsArray( true );
        
	$this->addElements( $elements );

        $this->addDisplayGroup(
                array(
                        'fn_doc_fiscal_id',
                        'terceiro_id_remetente',
                        'fn_doc_fiscal_numero',
                        'fn_doc_fiscal_chave'
                ),
                'left',
                array(
                        'decorators' => array('FormElements')
                )
        );

        $this->addDisplayGroup(
                array(
                        'terceiro_id_destinatario',
                        'fn_doc_fiscal_data',
                        'fn_doc_fiscal_valor_total_da_nota'
                ),
                'right',
                array(
                        'decorators' => array('FormElements')
                )
        );

	$this->_defineDecorators();
    }
}