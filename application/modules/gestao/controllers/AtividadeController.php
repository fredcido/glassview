<?php

/**
 * Description of Modulo
 *
 * @version $Id: AtividadeController.php 384 2012-03-02 16:41:10Z helion $
 */
class Gestao_AtividadeController extends App_Controller_Default
{
    /**
     *
     * @var type 
     */
    protected $_mapper;
    
    public function init()
    {
        $this->_mapper = new Model_Mapper_Atividade();
    }
    
    public function listAction()
    {
        $data = $this->_mapper->fetchGrid();
        
        $this->_helper->json( $data );
    }

     /**
     *
     * @param string $action
     * @return Admin_Form_Cargo
     */
    protected function _getForm( $action )
    {
        if ( null == $this->_form ) {

            $this->_form = new Gestao_Form_Atividade();
            $this->_form->setAction( $action );
        }

        return $this->_form;
    }

    public function editPostHook()
    {
    }
}