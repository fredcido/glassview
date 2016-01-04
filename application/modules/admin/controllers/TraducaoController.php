<?php

/**
 * Description of Modulo
 *
 * @version $Id: TraducaoController.php 340 2012-02-26 13:13:07Z helion $
 */
class Admin_TraducaoController extends App_Controller_Default
{
    /**
     *
     * @var type 
     */
    protected $_mapper;
    
    public function init()
    {
        $this->_mapper = new Model_Mapper_Traducao();
    }
 

     /**
     *
     * @param string $action
     * @return Admin_Form_Cargo
     */
    protected function _getForm( $action )
    {
        if ( null == $this->_form ) {

            $this->_form = new Admin_Form_Traducao();
            $this->_form->setAction( $action );
        }

        return $this->_form;
    }
    
    /**
     *
     */
    public function editPostHook()
    {
        
         $this->view->form->getElement( 'linguagem_id' )
                          ->setAttrib( 'readOnly', true );

         $this->view->form->getElement( 'traducao_tipo' )
                          ->setAttrib( 'readOnly', true );


        $optMenuTermo = array( '' => '' );

        if( $this->view->data['traducao_tipo'] == 'M' ){

            $menuTermoId = $this->view->data['menu_id'];

            $where = array( 'menu_id = ? ' => $menuTermoId  );

            $dbMenu = new Model_DbTable_Menu();
            $data   = $dbMenu->fetchAll( $where , 'menu_label' );

            foreach ( $data as $row )
                $optMenuTermo[$row->menu_id] = $row->menu_label;
        }else{

            $menuTermoId =  $this->view->data['linguagem_termo_id'];
            
            $where = array( 'linguagem_termo_id = ? ' => $menuTermoId );

            $dbLinguagemTermo = new Model_DbTable_LinguagemTermo();
            $data             = $dbLinguagemTermo->fetchAll( $where , 'linguagem_termo_desc' );

            foreach ( $data as $row )
                $optMenuTermo[$row->linguagem_termo_id] = $row->linguagem_termo_desc;
        }
        
        $this->view->form->getElement( 'menu_termo_id' )
                          ->setAttrib( 'disabled', null )
                          ->setAttrib( 'readOnly', true )
                          ->addMultiOptions( $optMenuTermo )
                          ->setValue( $menuTermoId );
        
    }
    
    public function listatermosAction()
    {
        $lang = $this->_getParam( 'lang', 0 );
        $tipo = $this->_getParam( 'tipo', 'T' );
        
	$this->_mapper->setData( array( 'lang' => $lang ) );

        if( $tipo == 'M' ){

            $data = $this->_mapper->listaMenu();
        }else{

            $data = $this->_mapper->listaTermos();
        }
        $this->_helper->json( array('identifier' => 'id',
                                         'label' => 'name',
                                         'items' => $data )
                            );
    }
}