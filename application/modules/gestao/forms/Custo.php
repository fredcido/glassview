<?php

class Gestao_Form_Custo extends App_Forms_Default
{
    /**
     * 
     */
    public function init()
    {
        $this->setName( 'form-gestao-timeline-custo' );
        $this->setAttrib( 'id', 'form-gestao-timeline-custo' );
        
        $elements = array();
        
        //Projeto
        $optProjeto = array();
        
        $dbProjeto = App_Model_DbTable_Factory::get( 'Projeto' );
        $rowsProjeto = $dbProjeto->fetchAll();
        
        $optProjeto[''] = null;
        
        foreach ( $rowsProjeto as $row )
            $optProjeto[$row->projeto_id] = $row->projeto_nome;
        
        $elements[] = $this->createElement( 'FilteringSelect', 'projeto_id' )
            ->setLabel( 'Projeto' )
            ->setAttrib( 'class', 'input-form' )
            ->setAttrib( 'onChange', 'gestaoTimeline.loadFuncionario(this.value)' )
            ->setRequired( true )
            ->addMultiOptions( $optProjeto );
        
        //Funcionario
        $elements[] = $this->createElement( 'FilteringSelect', 'funcionario_id' )
            ->setLabel( 'Funcionário' )
            ->setAttrib( 'class', 'input-form' )
            ->setAttrib( 'disabled', true )
            ->setAttrib( 'onChange', 'gestaoTimeline.changeFuncionario(this.id)' )
            ->setRequired( true );
        
        $elements[] = $this->createElement( 'DateTextBox', 'dt_inicio' )
            ->setLabel( 'Data Início' )
            ->setRequired( true )
            ->setAttrib( 'style', 'width:100px;' )
            ->setAttrib( 'class', 'input-form' )
            ->setAttrib( 'readOnly', true )
            ->addFilter( 'StringTrim' );
           
        $elements[] = $this->createElement( 'DateTextBox', 'dt_fim' )
            ->setLabel( 'Data Final' )
            ->setRequired( true )
            ->setAttrib( 'style', 'width:100px;' )
            ->setAttrib( 'class', 'input-form' )
            ->setAttrib( 'readOnly', true )
            ->addFilter( 'StringTrim' );
            
        $elements[] = $this->createElement( 'CurrencyTextBox', 'custo' )
            ->setLabel( 'Custo' )
            ->setDijitParam( 'currency', 'R$ ' )
            ->setAttrib( 'readOnly', true );

        $elements[] = $this->createElement( 'ValidationTextBox', 'carga_horaria' )
            ->setLabel( 'Carga Horária' )
            ->setAttrib( 'readOnly', true );
        
        $this->setRenderDefaultButtons( false )
            ->setCustomButtons(
                array(
                    array(
                        'id'     => 'button-calcular-custo',
                        'label'  => 'Calcular',
                        'icon'   => 'icon-toolbar-calculator',
                        'click'  => 'gestaoTimeline.calcularCusto();'
                    )
                )
            );
	
        $this->addElements( $elements );
    }
}