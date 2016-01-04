<?php

class Gestao_Form_Lancamento extends App_Forms_Default
{
    /**
     * 
     */
    public function init()
    {
        $this->setName( 'form-gestao-timeline-lancamento' );
        $this->setAttrib( 'id', 'form-gestao-timeline-lancamento' );
        
        $elements = array();

        $dbDadosFuncionario = new Model_Mapper_DadosFuncionario();
        $optFuncionario     = $dbDadosFuncionario->getFuncionarioTimeline();
       
        //Funcionario
        $elements[] = $this->createElement( 'FilteringSelect', 'funcionario_id' )
            ->setLabel( 'Funcionário' )
            ->setAttrib( 'class', 'input-form' )
            ->setAttrib( 'onChange', 'gestaoTimeline.setConstraintDataIniFimLanc()' )
            ->addMultiOptions( $optFuncionario )
            ->setRequired( true );
        
        
        $elements[] = $this->createElement( 'DateTextBox', 'lanc_dt_inicio' )
            ->setLabel( 'Data Início' )
            ->setRequired( true )
            ->setAttrib( 'style', 'width:100px;' )
            ->setAttrib( 'class', 'input-form' )
            ->setAttrib( 'readOnly', true )
            ->setAttrib( 'onChange', 'gestaoTimeline.setConstraintMinLanc()' )
            ->addFilter( 'StringTrim' );
           
        $elements[] = $this->createElement( 'DateTextBox', 'lanc_dt_fim' )
            ->setLabel( 'Data Final' )
            ->setRequired( true )
            ->setAttrib( 'style', 'width:100px;' )
            ->setAttrib( 'class', 'input-form' )
            ->setAttrib( 'readOnly', true )
            ->setAttrib( 'onChange', 'gestaoTimeline.setConstraintMaxLanc()' )
            ->addFilter( 'StringTrim' );
        
        $this->setRenderDefaultButtons( false )
            ->setCustomButtons(
                array(
                    array(
                        'id'     => 'button-calcular-custo',
                        'label'  => 'Buscar',
                        'icon'   => 'icon-toolbar-applicationformmagnify',
                        'click'  => 'gestaoTimeline.listaLancamentos();'
                    )
                )
            );
	
        $this->addElements( $elements );
    }
}