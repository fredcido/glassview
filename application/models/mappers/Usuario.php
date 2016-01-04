<?php

/**
 * 
 * @version $Id: Usuario.php 380 2012-03-02 00:00:06Z fred $
 */
class Model_Mapper_Usuario extends App_Model_Mapper_Abstract
{
    /**
     * 
     */
    public function fetchGrid()
    {
        $dbUsuario = App_Model_DbTable_Factory::get('Usuario');
        $dbPerfil  = App_Model_DbTable_Factory::get('Perfil');

        $select = $dbUsuario->select()
                ->setIntegrityCheck(false)
                ->from(
                        array('u' => $dbUsuario),
                        array('u.*')
                )
                ->joinLeft(
                        array('p' => $dbPerfil),
                        'p.perfil_id = u.perfil_id',
                        array('p.perfil_nome')
                );
        
        if ( Zend_Auth::getInstance()->getIdentity()->usuario_nivel != 'A' ) {
            
	    $select->where('u.usuario_nivel <> :usuario_nivel')
                            ->bind(
                                    array(
                                           ':usuario_nivel' => 'A'
                                    )
				);
	}

        $rows = $dbUsuario->fetchAll( $select );

        $data = array('rows' => array());

        if ( $rows->count() ) {

            foreach ( $rows as $key => $row ) {

                switch ( $row->usuario_nivel ) {
                    case 'A':
                        $nivel = 'Administrativo';
                        break;
                    case 'G':
                        $nivel = 'Gestor';
                        break;
		    default:
			$nivel = 'Normal';
                }

                $data['rows'][] = array(
                    'id'    => $row->usuario_id,
                    'data'  => array(
                        ++$key,
                        $row->usuario_nome,
                        $row->usuario_login,
                        ( $row->perfil_nome ? $row->perfil_nome : '-' ),
                        $row->usuario_email,
                        $nivel,
                        parent::_showStatus( $row->usuario_status )
                    )
                );

            }

        }

        return $data;
    }
    
    /**
     *
     * @return boolean 
     */
    public function save()
    {
        try {
	    
	    $dbTable = App_Model_DbTable_Factory::get( 'Usuario' );
		
	    $where = array( 'UPPER(usuario_login) = UPPER(?)' => $this->_data['usuario_login'] );
	    
	    if ( empty( $this->_data['usuario_senha'] ) )
		unset( $this->_data['usuario_senha'] );
	    else
		$this->_data['usuario_senha'] = sha1( $this->_data['usuario_senha'] );

	    if ( !$dbTable->isUnique( $where, $this->_data['usuario_id'] ) ) {

		$this->_message->addMessage( 'Usu&aacute;rio j&aacute; cadastrado com esse login.', App_Message::ERROR );
		return false;
	    }
	    
	    if ( 'A' == $this->_data['usuario_nivel'] )
		$this->_data['perfil_id'] = null;
	    
	    return parent::_simpleSave( $dbTable );
            
        } catch ( Exception $e ) {
	    
            return false;
            
        }
    }
    
    /**
     *
     * @return App_Model_DbTable_Row_Abstract
     */
    public function fetchRow()
    {
        $where = array( 'usuario_id = ?' => $this->_data['id'] );
        
        return parent::_simpleFetchRow( App_Model_DbTable_Factory::get( 'Usuario' ), $where );
    }
}