<?php

class Financeiro_Form_Recibo extends App_Forms_Default
{
    /**
     * 
     */
    public function init()
    {
        $this->setName( 'form-financeiro-recibo' );
	
	$elements = array();
        
        $elements[] = $this->createElement( 'hidden', 'fn_recibo_id' );
	
	$dbTerceiro = new Model_DbTable_Terceiro();
	$data = $dbTerceiro->fetchAll( array(), 'terceiro_nome' );

	$optReceptior[''] = '';
	foreach ( $data as $row )
	    $optReceptior[ 'T-' . $row->terceiro_id ] = $row->terceiro_nome;
	
	$mapperFuncionario = new Model_Mapper_Funcionario();
	$data = $mapperFuncionario->listAll();
	
	foreach ( $data as $row )
	    $optReceptior[ 'F-' . $row->funcionario_id ] = $row->funcionario_nome;

	$elements[] = $this->createElement( 'FilteringSelect', 'receptor' )
			   ->setLabel( 'Para/De' )
			   ->addMultiOptions( $optReceptior )
			   ->setAttrib( 'class', 'input-form' )
			   ->setAttrib( 'onChange', 'financeiroRecibo.buscaDadosReceptor' )
			   ->setDijitParam( 'placeHolder', 'Selecione o fornecedor' )
			   ->setRequired( true );
	
	$elements[] = $this->createElement( 'ValidationTextBox', 'terceiro_cpf_cnpj' )
			   ->setLabel( 'CPF / CNPJ' )
			   ->setDijitParam( 'placeHolder', 'CPF/CNPJ' )
			   ->setAttrib( 'class', 'input-form' )
			   ->setAttrib( 'readOnly', 'true' )
			   ->addFilter( 'StringTrim' );
	
	$optTipo['E'] = 'Entrada';
	$optTipo['S'] = 'Saída';
	
	$elements[] = $this->createElement( 'FilteringSelect', 'fn_recibo_tipo' )
			   ->setAttrib( 'class', 'input-form' )
			   ->setLabel( 'Tipo' )
			   ->setRequired( true )
			   ->addMultiOptions( $optTipo );
	
	$elements[] = $this->createElement( 'CurrencyTextBox', 'fn_recibo_valor' )
			    ->setLabel( 'Valor' )
			    ->setAttrib( 'class', 'input-form' )
			    ->setDijitParam( 'currency', 'R$ ' )
                            ->setValue(0)
			    ->setRequired( true );

        $elements[] = $this->createElement( 'ValidationTextBox', 'fn_recibo_descricao' )
                           ->setLabel(  'Referente' )
                           ->setAttrib( 'maxlength', 255 )
                           ->setDijitParam( 'placeHolder', 'Digite a descrição do recibo' )
                           ->setAttrib( 'class', 'input-form' )
                           ->addFilter( 'StringTrim' )
                           ->setRequired( true );
        
       $elements[] = $this->createElement( 'DateTextBox', 'fn_recibo_data' )
			   ->setRequired( true )
			   ->setAttrib( 'class', 'input-form' )
			   ->setDijitParam( 'placeHolder', 'Selecione a data do recibo' )
			   ->setLabel( 'Data' );
        
	$this->addElements( $elements );
    }
}