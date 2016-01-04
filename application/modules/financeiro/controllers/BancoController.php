<?php

/**
 * Description of Modulo
 *
 * @version $Id: BancoController.php 499 2012-04-14 12:23:13Z helion $
 */
class Financeiro_BancoController extends App_Controller_Default
{
    /**
     *
     * @var type 
     */
    protected $_mapper;
    
    public function init()
    {
        $this->_mapper = new Model_Mapper_Banco();
    }
    
    public function listAction()
    {
        $data = $this->_mapper->fetchGrid();
        
        $this->_helper->json( $data );
    }

     /**
     *
     * @param string $action
     * @return Financeiro_Form_Banco
     */
    protected function _getForm( $action )
    {
        if ( null == $this->_form ) {

            $this->_form = new Financeiro_Form_Banco();
            $this->_form->setAction( $action );
        }

        return $this->_form;
    }

}