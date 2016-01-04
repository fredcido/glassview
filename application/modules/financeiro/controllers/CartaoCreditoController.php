<?php

/**
 * Description of Modulo
 *
 * @version $Id: CartaoCreditoController.php 518 2012-04-27 17:38:19Z helion $
 */
class Financeiro_CartaoCreditoController extends App_Controller_Default
{
    /**
     *
     * @var type 
     */
    protected $_mapper;
    
    public function init()
    {
        $this->_mapper = new Model_Mapper_CartaoCredito();
    }
    
    public function listAction()
    {
        $data = $this->_mapper->fetchGrid();
        
        $this->_helper->json( $data );
    }

     /**
     *
     * @param string $action
     * @return Financeiro_Form_CartaoCredito
     */
    protected function _getForm( $action )
    {
        if ( null == $this->_form ) {

            $this->_form = new Financeiro_Form_CartaoCredito();
            $this->_form->setAction( $action );
        }

        return $this->_form;
    }

}