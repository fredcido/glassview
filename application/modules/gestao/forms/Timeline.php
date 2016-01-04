<?php

class Gestao_Form_Timeline extends App_Forms_Default
{
    /**
     * 
     */
    public function init()
    {
        $this->setName( 'form-gestao-timeline' );
        
        $elements = array();
        
        $elements[] = $this->createElement( 'hidden', 'timeline_id' );
        
        //Funcionario
        $optFuncionario = array();
        
        $mapperFuncionario = new Model_Mapper_Funcionario();
        $rowsFuncionario = $mapperFuncionario->listAll();
        
        $optFuncionario[''] = null;
        
        foreach ( $rowsFuncionario as $row )
            $optFuncionario[$row->funcionario_id] = $row->funcionario_nome;
        
        $elements[] = $this->createElement( 'FilteringSelect', 'funcionario_id' )
            ->setLabel( 'Funcionário' )
            ->setAttrib( 'class', 'input-form' )
            ->setAttrib( 'onChange', 'gestaoTimeline.getCargaHoraria(this.value)')
            ->setRequired( true )
            ->addMultiOptions( $optFuncionario );
        
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
            ->setRequired( true )
            ->addMultiOptions( $optProjeto );
        
        //Atividade
        $optAtividade = array();
        
        $dbAtividade = App_Model_DbTable_Factory::get( 'Atividade' );
        $rowsAtividade = $dbAtividade->fetchAll();
        
        $optAtividade[''] = null;
        
        foreach ( $rowsAtividade as $row )
            $optAtividade[$row->atividade_id] = $row->atividade_nome; 

        $elements[] = $this->createElement( 'FilteringSelect', 'atividade_id' )
            ->setLabel( 'Atividade' )
            ->setAttrib( 'class', 'input-form' )
            ->setRequired( true )
            ->addMultiOptions( $optAtividade );
        
        $elements[] = $this->createElement( 'DateTextBox', 'dt_inicio' )
            ->setLabel( 'Data Início' )
            ->setRequired( true )
            ->setAttrib( 'style', 'width:100px;' )
            ->setAttrib( 'class', 'input-form' )
            ->addFilter( 'StringTrim' );
            
        $elements[] = $this->createElement( 'TimeTextBox', 'hr_inicio' )
            ->setLabel( 'Hora Início' )
            ->setRequired( true )
            ->setAttrib( 'style', 'width:100px;' )
            ->setAttrib( 'class', 'input-form' )
            ->addFilter( 'StringTrim' );
            
        $elements[] = $this->createElement( 'DateTextBox', 'dt_fim' )
            ->setLabel( 'Data Final' )
            ->setRequired( true )
            ->setAttrib( 'style', 'width:100px;' )
            ->setAttrib( 'class', 'input-form' )
            ->addFilter( 'StringTrim' );
            
        $elements[] = $this->createElement( 'TimeTextBox', 'hr_fim' )
            ->setLabel( 'Hora Final' )
            ->setRequired( true )
            ->setAttrib( 'style', 'width:100px;' )
            ->setAttrib( 'class', 'input-form' )
            ->addFilter( 'StringTrim' );
            
        $elements[] = $this->createElement( 'NumberTextBox', 'timeline_carga_horaria' )
            ->setLabel( 'Carga Horária' )
            ->setRequired( true )
            //->setAttrib( 'readOnly', true )
            ->setAttrib( 'style', 'width:100px;' )
            ->addFilter( 'StringTrim' );
    
        $this->addElements( $elements );
    }
}
