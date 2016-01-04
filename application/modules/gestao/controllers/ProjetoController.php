<?php

/**
 * Description of Modulo
 *
 * @version $Id: ProjetoController.php 569 2012-05-10 15:13:49Z ze $
 */
class Gestao_ProjetoController extends App_Controller_Default
{
    /**
     *
     * @var type 
     */
    protected $_mapper;
    
    public function init()
    {
        $this->_mapper = new Model_Mapper_Projeto();
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

            $this->_form = new Gestao_Form_Projeto();
            $this->_form->setAction( $action );
        }

        return $this->_form;
    }
}