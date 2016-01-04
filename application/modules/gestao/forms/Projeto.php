<?php

class Gestao_Form_Projeto extends App_Forms_Default
{
    /**
     * 
     */
    public function init()
    {
        $this->setName( 'form-gestao-projeto' );
	
	$elements = array();
        
        $elements[] = $this->createElement( 'hidden', 'projeto_id' );

	$elements[] = $this->createElement( 'ValidationTextBox', 'projeto_nome' )
			   ->setLabel( 'Nome' )
			   ->setDijitParam( 'placeHolder', 'Digite o nome do projeto' )
			   ->setAttrib( 'maxlength', 150 )
			   ->setAttrib( 'class', 'input-form' )
			   ->addFilter( 'StringTrim' )
			   ->setRequired( true );
	
	$elements[] = $this->createElement( 'CurrencyTextBox', 'projeto_orcamento' )
			   ->setLabel( 'Orçamento' )
			   ->setDijitParam( 'currency', 'R$ ' )
                           ->setValue(0)
			   ->setDijitParam( 'placeHolder', 'Informe o orçamento do projeto' )
			   ->setRequired( true )
                           ->setAttrib( 'class', 'input-form' );

	$elements[] = $this->createElement( 'DateTextBox', 'projeto_inicio' )
			   ->setLabel( 'Data Início' )
                           ->setValue(Zend_Date::now()->toString('yyyy-MM-dd'))
			   ->addFilter( 'StringTrim' )
			   ->setRequired( true )
                           ->setAttrib('style', 'width:100px;');
        
	$elements[] = $this->createElement( 'DateTextBox', 'projeto_final' )
			   ->setLabel( 'Data Final' )
                           //->setValue(Zend_Date::now()->toString('yyyy-MM-dd'))
			   ->addFilter( 'StringTrim' )
			   ->setRequired( true )
                           ->setAttrib('style', 'width:100px;');

		
	$elements[] = $this->createElement( 'SimpleTextarea', 'projeto_descricao' )
			   ->setLabel( 'Descrição' )
			   ->setAttrib( 'maxlength', 400 )
			   ->setAttrib( 'class', 'input-form' )
			   ->setDijitParam( 'progressObserver', true )
			   ->addFilters( array( 'StringTrim', 'StripTags' ) );

        $optStatus[''] = '';
	$optStatus['P'] = 'Pendente';
	$optStatus['I'] = 'Iniciado';
	$optStatus['F'] = 'Finalizado';
	$optStatus['C'] = 'Cancelado';

	$elements[] = $this->createElement( 'FilteringSelect', 'projeto_status' )
			   ->setLabel( 'Status' )
			   ->setRequired( true )
			   ->setAttrib( 'class', 'input-form' )
			   ->addMultiOptions( $optStatus );
	
	$this->addElements( $elements );
    }
}