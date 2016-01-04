<?php

/**
 * Description of Modulo
 *
 * @version $Id: ChequeController.php 509 2012-04-17 14:48:11Z helion $
 */
class Financeiro_ChequeController extends App_Controller_Default
{
    /**
     *
     * @var type 
     */
    protected $_mapper;
    
    public function init()
    {
        $this->_mapper = new Model_Mapper_Cheque();
    }
    
    public function listAction()
    {
        $data = $this->_mapper->fetchGrid();
        
        $this->_helper->json( $data );
    }

     /**
     *
     * @param string $action
     * @return Financeiro_Form_Cheque
     */
    protected function _getForm( $action )
    {
        if ( null == $this->_form ) {

            $this->_form = new Financeiro_Form_Cheque();
            $this->_form->setAction( $action );
        }

        return $this->_form;
    }

}