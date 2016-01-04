<?php

class Financeiro_Form_Duplicata extends App_Forms_Default
{
    /**
     * 
     */
    public function init()
    {
        $this->setName( 'form-financeiro-duplicata' );
	
	$elements = array();
        
        $elements[] = $this->createElement( 'hidden', 'fn_duplicata_id' );
        $elements[] = $this->createElement( 'hidden', 'fn_doc_fiscal_id' );
        $elements[] = $this->createElement( 'hidden', 'fn_lancamento_id' );
        $elements[] = $this->createElement( 'hidden', 'fn_duplicata_quitada' )
                           ->setValue(0);
        
        $elements[] = $this->createElement( 'hidden', 'fn_lancamento_data' )
                           ->setIsArray( true );
        
        $elements[] = $this->createElement( 'hidden', 'fn_lancamento_valor' )
                           ->setIsArray( true );
        
        $elements[] = $this->createElement( 'hidden', 'fn_duplicata_data' )
                           ->setValue(Zend_Date::now()->toString('yyyy-MM-dd'));
        
        $elements[] = $this->createElement( 'hidden', 'id_parcela' )
                           ->setIsArray( true );
        
        $dbConta = new Model_DbTable_Conta();
        $rowsConta = $dbConta->fetchAll();

        $optConta = array('' => '');

        foreach ($rowsConta as $rowConta)
                $optConta[$rowConta->fn_conta_id] = $rowConta->fn_conta_descricao;
        
        $elements[] = $this->createElement('FilteringSelect', 'fn_conta_id')
                            ->setLabel('Conta')
                            ->setDijitParam( 'placeHolder', 'Selecione Conta' )
                            ->setAttrib('class', 'input-form')
                            ->addMultiOptions($optConta)
                            ->setRequired( true );
        
        $elements[] = $this->createElement('FilteringSelect', 'id_conta')
                            ->setLabel('Conta')
                            ->setDijitParam( 'placeHolder', 'Selecione Conta' )
                            ->setAttrib('class', 'input-form')
                            ->addMultiOptions($optConta)
                            ->setIsArray( true );
                
        $dbTerceiro = new Model_DbTable_Terceiro();
	$data = $dbTerceiro->fetchAll( array(), 'terceiro_nome' );

	$optTerceiro = array( '' => '' );
	foreach ( $data as $row )
	    $optTerceiro[$row->terceiro_id] = $row->terceiro_nome;

	$elements[] = $this->createElement( 'FilteringSelect', 'terceiro_id' )
			   ->setLabel( 'Terceiro' )
                           ->setRegisterInArrayValidator(false)
			   ->addMultiOptions( $optTerceiro )
                           ->setAttrib( 'class', 'input-form' )
                           ->setRequired( true );
        
        $elements[] = $this->createElement( 'CurrencyTextBox', 'fn_duplicata_total' )
			   ->setLabel( 'Valor Total' )
			   ->setAttrib( 'class', 'input-form' )
			   ->setDijitParam( 'currency', 'R$ ' )
                           ->setAttrib( 'onChange','financeiroDuplicata.geraPacelamento()' )
			   ->addFilter( 'StringTrim' )
                           ->setValue(0)
			   ->setRequired( true );
        
        $elements[] = $this->createElement( 'NumberSpinner', 'fn_duplicata_parcelas' )
                           ->setLabel(  'Parcelas' )
                           ->setDijitParam( 'placeHolder', 'Informe quantidade de parcelas' )
                           ->setDijitParam( 'constraints', array('min' => 1 ,'max'=> 72) )
                           ->setAttrib( 'onChange','financeiroDuplicata.geraPacelamento()' )
                           ->setAttrib( 'class', 'input-form' )
                           ->addFilter( 'StringTrim' )
                           ->setValue(0)
                           ->setRequired( true );

	$elements[] = $this->createElement( 'FilteringSelect', 'fn_duplicata_tipo' )
                           ->setRequired( true )
			   ->setAttrib( 'class', 'input-form' )
			   ->setLabel( 'Tipo' )
			   ->addMultiOptions( array( '' => '',
                                                     'E' => 'Entrada',
                                                     'S' => 'Saída')
                                   );
        
	$elements[] = $this->createElement( 'ValidationTextBox', 'fn_doc_fiscal_numero' )
			   ->setLabel( 'Documento Fiscal' )
			   ->setDijitParam( 'placeHolder', 'Selecione Documento Fiscal' )
			   ->setAttrib( 'readOnly', true )
			   ->setAttrib( 'class', 'input-form' )
			   ->setAttrib( 'style', 'width:172px !important;')
			   ->addFilter( 'StringTrim' )
			   ->setRequired( true );
        
	$elements[] = $this->createElement( 'Button', 'btn_doc_fiscal' )
                           ->setLabel(  '' )
                           ->setAttrib( 'onClick','financeiroDuplicata.buscaDocFiscal()' )
						   ->setAttrib( 'style', 'float:left; margin-top:-2px;' )
                           ->setDijitParam( 'iconClass','icon-toolbar-applicationformmagnify' )
						->setDecorators( array('DijitElement') )
						->setDijitParam( 'showLabel', 'false' );
        

        
	$this->addElements( $elements );

        $this->addDisplayGroup(
                array(
                        'terceiro_id',
                        'fn_conta_id',
                        'fn_doc_fiscal_id',
                        'fn_doc_fiscal_numero',
                        'btn_doc_fiscal'
                ),
                'left',
                array(
                        'decorators' => array('FormElements')
                )
        );

        $this->addDisplayGroup(
                array(
                        'fn_duplicata_total',
                        'fn_duplicata_parcelas',
                        'fn_duplicata_id',
                        'fn_lancamento_id',
                        'fn_duplicata_data',
                        'fn_duplicata_quitada',
                        'fn_duplicata_tipo'
                ),
                'right',
                array(
                        'decorators' => array('FormElements')
                )
        );

                $this->setCustomButtons(
                        array(
                            array(
                                'action'   => App_Plugins_Acl::getIdentifier( '/financeiro/duplicata/excluir', 'Excluir' ),
                                'id'       => 'buttonRemoveDuplicata',
                                'name'     => 'buttonRemoveDuplicata',
                                'label'    => 'Excluir',
                                'icon'     => 'dijitEditorIcon dijitEditorIconDelete',
                                'click'    => 'financeiroDuplicata.deletaDuplicata( "' . $this->getId() . '" )',
                                'disabled' => 'true'
                            )
                           )
                             )
                     ->setRenderDefaultButtons( true )
                     ->_defineDecorators();
                  //$this->_defineDecorators();
    }
}