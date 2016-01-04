<?php

/**
 * 
 * @version $Id $
 */
class Financeiro_Form_FormaPagamento extends App_Forms_Default 
{
	/**
	 * 
	 */
	public function init() 
	{
		$this->setName( 'form-financeiro-formapagamento' );

		$elements = array();
		
		$elements[] = $this->createElement( 'hidden', 'fn_lancamento_id' );
		$elements[] = $this->createElement( 'hidden', 'fn_tipo_lancamento' );
		
		$elements[] = $this->createElement('FilteringSelect', 'fn_forma_pgto_tipo')
			->setLabel('Tipo Pagamento')
			->setRequired(true)
			->setAttrib('class', 'input-form')
			->addMultiOptions( 
				array(
					'D' => 'Dinheiro',
					'T' => 'TED/DOC',
					'B' => 'Cheque',
					'C' => 'Cartão de Crédito',
				        'L' => 'Boleto'
				)
			);
		
		$elements[] = $this->createElement( 'DateTextBox', 'fn_lancamento_dtefetivado' )
			   ->setRequired( true )
			   ->setAttrib( 'class', 'input-form' )
			   ->setDijitParam( 'placeHolder', 'Selecione a data de efetivação' )
			   ->setValue( Zend_Date::now()->toString( 'yyyy-MM-dd' ) )
			   ->setLabel( 'Data' );
			
		$elements[] = $this->createElement('CurrencyTextBox', 'fn_forma_pgto_valor')
			->setLabel('Valor')
			->setDijitParam('currency', 'R$ ')
			->setAttrib('class', 'input-form')
			->setRequired(true);

		$this->addElements($elements);
	}

}