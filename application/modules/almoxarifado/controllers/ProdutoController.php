<?php

/**
 * Description of Modulo
 *
 * @version $Id: ProdutoController.php 299 2012-02-21 02:16:02Z helion $
 */
class Almoxarifado_ProdutoController extends App_Controller_Default
{
    /**
     *
     * @var type 
     */
    protected $_mapper;
    
    public function init()
    {
        $this->_mapper = new Model_Mapper_Produto();
    }

     /**
     *
     * @param string $action
     * @return Admin_Form_TipoAtivo
     */
    protected function _getForm( $action )
    {
        if ( null == $this->_form ) {

            $this->_form = new Almoxarifado_Form_Produto();
            $this->_form->setAction( $action );
        }

        return $this->_form;
    }

    /**
     *
     */
    public function editPostHook()
    {
        $this->view->form->getElement( 'produto_status' )->setAttrib( 'readOnly', null );
    }
}