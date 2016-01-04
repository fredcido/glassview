<?php

/**
 * Description of Modulo
 *
 * @version $Id: ContaController.php 611 2012-05-16 21:29:28Z helion $
 */
class Financeiro_ContaController extends App_Controller_Default
{
    /**
     *
     * @var type 
     */
    protected $_mapper;
    
    public function init()
    {
        $this->_mapper = new Model_Mapper_Conta();
    }
    
    public function listAction()
    {
        $data = $this->_mapper->fetchGrid();
        
        $this->_helper->json( $data );
    }

     /**
     *
     * @param string $action
     * @return Financeiro_Form_Conta
     */
    protected function _getForm( $action )
    {
        if ( null == $this->_form ) {

            $this->_form = new Financeiro_Form_Conta();
            $this->_form->setAction( $action );
        }

        return $this->_form;
    }

    /**
     *
     */
    public function editPostHook()
    {
        $this->view->form->getElement( 'fn_conta_status' )
                         ->setAttrib( 'readOnly', null );

        $lancamento   = $this->_mapper->varificaLancamentosConta( $this->view->data["fn_conta_id"] );
        
        $this->view->form->getElement( 'lancamentos' )
                         ->setValue( $lancamento );
    }
}