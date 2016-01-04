<?php

/**
 * Description of Modulo
 *
 * @version $Id: UnidadeMedidaController.php 297 2012-02-21 01:14:12Z helion $
 */
class Almoxarifado_UnidadeMedidaController extends App_Controller_Default
{
    /**
     *
     * @var type 
     */
    protected $_mapper;
    
    public function init()
    {
        $this->_mapper = new Model_Mapper_UnidadeMedida();
    }

     /**
     *
     * @param string $action
     * @return Admin_Form_Ativo
     */
    protected function _getForm( $action )
    {
        if ( null == $this->_form ) {

            $this->_form = new Almoxarifado_Form_UnidadeMedida();
            $this->_form->setAction( $action );
        }

        return $this->_form;
    }
    
    /**
     *
     */
    public function editPostHook()
    {
        $this->view->form->getElement( 'unidade_medida_status' )->setAttrib( 'readOnly', null );
    }
}