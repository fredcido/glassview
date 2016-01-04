<?php

/**
 * Description of Modulo
 *
 * @version $Id: TipoProdutoController.php 298 2012-02-21 01:43:06Z helion $
 */
class Almoxarifado_TipoProdutoController extends App_Controller_Default
{
    /**
     *
     * @var type 
     */
    protected $_mapper;
    
    public function init()
    {
        $this->_mapper = new Model_Mapper_TipoProduto();
    }

     /**
     *
     * @param string $action
     * @return Admin_Form_TipoAtivo
     */
    protected function _getForm( $action )
    {
        if ( null == $this->_form ) {

            $this->_form = new Almoxarifado_Form_TipoProduto();
            $this->_form->setAction( $action );
        }

        return $this->_form;
    }
    
}