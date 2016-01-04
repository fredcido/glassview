<?php

/**
 *
 * @version $Id $
 */
class Model_Mapper_Traducao extends App_Model_Mapper_Abstract
{
    
   /**
     * 
     */
    public function fetchGrid()
    {
        $dbTraducao       = App_Model_DbTable_Factory::get('Traducao');
        $dbLinguagem      = App_Model_DbTable_Factory::get('Linguagem');
        $dbLinguagemTermo = App_Model_DbTable_Factory::get('LinguagemTermo');
        $dbMenu           = App_Model_DbTable_Factory::get('Menu');


        $select = $dbTraducao->select()
                ->setIntegrityCheck(false)
                ->from(
                        array('t' => $dbTraducao),
                        array('t.*')
                )
                ->join(
                        array('l' => $dbLinguagem),
                        'l.linguagem_id = t.linguagem_id',
                        array('l.*')
                )
                ->joinLeft(
                        array('lt' => $dbLinguagemTermo),
                        'lt.linguagem_termo_id = t.linguagem_termo_id',
                        array('lt.*')
                )
                ->joinLeft(
                        array('m' => $dbMenu),
                        'm.menu_id = t.menu_id',
                        array('m.menu_label')
                );
        

        $rows = $dbTraducao->fetchAll( $select );

        $data = array('rows' => array());

        if ( $rows->count() ) {

            foreach ( $rows as $key => $row ) {


                $data['rows'][] = array(
                    'id'    => $row->traducao_id,
                    'data'  => array(
                        ++$key,
                        $row->linguagem_nome,
                        $this->_getTipoTrducao( $row->traducao_tipo ),
                        ( $row->traducao_tipo == 'M' ? $row->menu_label : $row->linguagem_termo_desc),
                        $row->traducao_desc

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

            if( $this->_data['traducao_tipo'] == 'M' ){

                $this->_data['menu_id'] = $this->_data['menu_termo_id'];

                $this->_data['linguagem_termo_id'] = NULL;

                $where = array( 'linguagem_id = ?' => $this->_data['linguagem_id'],
                                'menu_id      = ?' => $this->_data['menu_id'] );
            }else{
                
                $this->_data['linguagem_termo_id'] = $this->_data['menu_termo_id'];

                $this->_data['menu_id'] = NULL;

                $where = array( 'linguagem_id       = ?' => $this->_data['linguagem_id'],
                                'linguagem_termo_id = ?' => $this->_data['linguagem_termo_id'] );
            }
            unset($this->_data['menu_termo_id']);

            $dbTable = App_Model_DbTable_Factory::get('Traducao');

            if ( !$dbTable->isUnique( $where, $this->_data['traducao_id'] ) ) {

                $this->_message->addMessage( 'Tradução j&aacute; cadastrada.', App_Message::ERROR );
                return false;
            }
            parent::_cleanCacheTag( array( 'translate' ) );
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
        $where = array( 'traducao_id = ?' => $this->_data['id'] );
        
        return parent::_simpleFetchRow( App_Model_DbTable_Factory::get( 'Traducao' ), $where );
    }
    
    /**
     *
     * @param string $locale
     * @return array
     */
    public function getTranslate( $locale )
    {
        $dbTraducao       = App_Model_DbTable_Factory::get('Traducao');
        $dbLinguagem      = App_Model_DbTable_Factory::get('Linguagem');
        $dbLinguagemTermo = App_Model_DbTable_Factory::get('LinguagemTermo');
        $dbMenu           = App_Model_DbTable_Factory::get('Menu');

        $select = $dbTraducao->select()
            ->setIntegrityCheck(false)
            ->from(
                array('t' => $dbTraducao),
                array('t.traducao_desc','t.traducao_tipo')
            )
            ->join(
                array('l' => $dbLinguagem),
                't.linguagem_id  = l.linguagem_id',
                array()
            )
            ->joinLeft(
                array('lt' => $dbLinguagemTermo),
                'lt.linguagem_termo_id  = t.linguagem_termo_id',
                array('lt.linguagem_termo_desc')
            )
            ->joinLeft(
                    array('m' => $dbMenu),
                    'm.menu_id = t.menu_id',
                    array('m.menu_label')
            )
            ->where('l.linguagem_local = :linguagem_local')
            ->bind(
                array(
                    ':linguagem_local' => $locale
                )
            );

        $rows = $dbTraducao->fetchAll( $select );

        $data = array();
        
        foreach ( $rows as $row ){
            
            $idxTrans = ( $row->traducao_tipo == 'M' ? $row->menu_label : $row->linguagem_termo_desc);
            $data[$idxTrans] = $row->traducao_desc;
        }
        
        return $data;
    }
    
    public function listaTermos( )
    {

        $dbLinguagemTermo   = App_Model_DbTable_Factory::get('LinguagemTermo');
        $dbTraducao         = App_Model_DbTable_Factory::get('Traducao');

        $subSelect = $dbTraducao->select()
            ->from(
                array('t' => $dbTraducao),
                array('linguagem_termo_id')
            )
            ->where('t.linguagem_id = :linguagem_id')
            ->where('t.linguagem_termo_id IS NOT NULL');
            

        $select = $dbLinguagemTermo->select()
                    ->from(
                        array('lt' => $dbLinguagemTermo),
                        array('linguagem_termo_id','linguagem_termo_desc')
                    )
                    ->where('lt.linguagem_termo_id NOT IN(?)', $subSelect)
                    ->bind(
                        array(
                            ':linguagem_id' => $this->_data['lang']
                        )
                    );


        $rows = $dbLinguagemTermo->fetchAll( $select );

        $data = array();

        if ( $rows->count() ) {

            foreach ( $rows as $key => $row ) {

                $data[] = array(
                    'id'    => $row->linguagem_termo_id,
                    'name'  => $row->linguagem_termo_desc
                );

            }

        }

        return $data;

    }

    public function listaMenu( )
    {

        $dbMenu     = App_Model_DbTable_Factory::get('Menu');
        $dbTraducao = App_Model_DbTable_Factory::get('Traducao');

        $subSelect = $dbTraducao->select()
            ->from(
                array('t' => $dbTraducao),
                array('menu_id')
            )
            ->where('t.linguagem_id = :linguagem_id')
            ->where('t.menu_id IS NOT NULL');


        $select = $dbMenu->select()
                    ->from(
                        array('m' => $dbMenu),
                        array('menu_id','menu_label')
                    )
                    ->where('m.menu_id NOT IN(?)', $subSelect)
                    ->bind(
                        array(
                            ':linguagem_id' => $this->_data['lang']
                        )
                    );

        $rows = $dbMenu->fetchAll( $select );

        $data = array();

        if ( $rows->count() ) {

            foreach ( $rows as $key => $row ) {

                $data[] = array(
                    'id'    => $row->menu_id,
                    'name'  => $row->menu_label
                );

            }

        }

        return $data;

    }

    /**
     *
     * @param string $type
     * @return string
     */
    protected function _getTipoTrducao( $type )
    {
	$optTipo['M'] = 'Menu';
	$optTipo['T'] = 'Termo';

	return empty( $optTipo[ $type ] ) ? 'Desconhecido' : $optTipo[ $type ];
    }
}