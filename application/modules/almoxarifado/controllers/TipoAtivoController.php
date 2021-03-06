<?php

/**
 * Description of Modulo
 *
 * @version $Id: TipoAtivoController.php 252 2012-02-15 03:21:42Z fred $
 */
class Almoxarifado_TipoAtivoController extends App_Controller_Default
{
    /**
     *
     * @var type 
     */
    protected $_mapper;
    
    public function init()
    {
        $this->_mapper = new Model_Mapper_TipoAtivo();
    }

     /**
     *
     * @param string $action
     * @return Admin_Form_TipoAtivo
     */
    protected function _getForm( $action )
    {
        if ( null == $this->_form ) {

            $this->_form = new Almoxarifado_Form_TipoAtivo();
            $this->_form->setAction( $action );
        }

        return $this->_form;
    }
    
    /**
     *
     */
    public function editPostHook()
    {
        $this->view->form->getElement( 'tipo_ativo_status' )->setAttrib( 'readOnly', null );
    }
}