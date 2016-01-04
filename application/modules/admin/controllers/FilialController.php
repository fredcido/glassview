<?php

/**
 * Description of Modulo
 *
 * @version $Id: FilialController.php 445 2012-03-12 17:54:21Z fred $
 */
class Admin_FilialController extends App_Controller_Default
{
    /**
     *
     * @var type 
     */
    protected $_mapper;
    
    public function init()
    {
        $this->_mapper = new Model_Mapper_Filial();
    }
    
     /**
     *
     * @param string $action
     * @return Admin_Form_Filial
     */
    protected function _getForm( $action )
    {
        if ( null == $this->_form ) {

            $this->_form = new Admin_Form_Filial();
            $this->_form->setAction( $action );
        }

        return $this->_form;
    }

    /**
     *
     */
    public function editPostHook()
    {
        $this->view->form->getElement( 'filial_status' )
                          ->setAttrib( 'readOnly', null );

        $this->_mapper->setData( array( 'pais' => $this->view->data['pais_id'] ) );
	$rows = $this->_mapper->buscaEstados();
        
        $optEstado = array('' => '');

        if ( $rows->count() )

            foreach ( $rows as $key => $row )

                $optEstado[$row->estado_id] = $row->estado_nome;

        $this->view->form->getElement( 'estado_id' )
                         ->addMultiOptions( $optEstado )
                         ->setValue( $this->view->data['estado_id'] )
                         ->setAttrib( 'disabled', '' );

 	$this->_mapper->setData( array( 'estado' => $this->view->data['estado_id'] ) );
	$rows = $this->_mapper->buscaCidade();

        $optCidade = array('' => '');

        if ( $rows->count() )

            foreach ( $rows as $key => $row )
                
                $optCidade[$row->cidade_id] = $row->cidade_nome;


        $this->view->form->getElement( 'cidade_id' )
                         ->addMultiOptions( $optCidade )
                         ->setValue( $this->view->data['cidade_id'] )
                         ->setAttrib( 'disabled', '' );
    }
    
    /**
     * 
     */
    public function buscaestadoAction()
    {
        $pais = $this->_getParam( 'pais', 0 );
        
	$this->_mapper->setData( array( 'pais' => $pais ) );

	$rows = $this->_mapper->buscaEstados();

        $data = array();

        if ( $rows->count() ) {

            foreach ( $rows as $key => $row ) {

                $data[] = array(
                    'id'    => $row->estado_id,
                    'name'  => $row->estado_sigla . ' - ' . $row->estado_nome
                );

            }

        }
        
        $this->_helper->json( array('identifier' => 'id',
                                         'label' => 'name',
                                         'items' => $data )
                            );
    }
    
    public function buscacidadeAction()
    {
        $estado = $this->_getParam( 'estado', 0 );
        
	$this->_mapper->setData( array( 'estado' => $estado ) );

	$rows = $this->_mapper->buscaCidade();
        
        $data = array();

        if ( $rows->count() ) {

            foreach ( $rows as $key => $row ) {

                $data[] = array(
                    'id'    => $row->cidade_id,
                    'name'  => $row->cidade_nome
                );

            }

        }

        $this->_helper->json( array('identifier' => 'id',
                                         'label' => 'name',
                                         'items' => $data )
                            );

    }
    
}