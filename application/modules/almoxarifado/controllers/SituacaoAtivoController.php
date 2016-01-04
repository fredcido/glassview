<?php

/**
 * Description of Modulo
 *
 * @version $Id: SituacaoAtivoController.php 252 2012-02-15 03:21:42Z fred $
 */
class Almoxarifado_SituacaoAtivoController extends App_Controller_Default
{
    /**
     *
     * @var type 
     */
    protected $_mapper;
    
    public function init()
    {
        $this->_mapper = new Model_Mapper_SituacaoAtivo();
    }

     /**
     *
     * @param string $action
     * @return Admin_Form_SituacaoAtivo
     */
    protected function _getForm( $action )
    {
        if ( null == $this->_form ) {

            $this->_form = new Almoxarifado_Form_SituacaoAtivo();
            $this->_form->setAction( $action );
        }

        return $this->_form;
    }
}