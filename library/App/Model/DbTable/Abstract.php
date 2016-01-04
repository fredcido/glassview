<?php

/**
 *
 */
abstract class App_Model_DbTable_Abstract extends Zend_Db_Table_Abstract
{
    /**
     *
     * @var bool
     */
    protected $_auditing = true;

    /**
     * (non-PHPdoc)
     * @see Zend_Db_Table_Abstract::fetchAll()
     */
    public function fetchAll( $where = null, $order = null, $count = null, $offset = null )
    {
	$rows = parent::fetchAll( $where, $order, $count, $offset );

	return $rows;
    }

    /**
     * (non-PHPdoc)
     * @see Zend_Db_Table_Abstract::fetchRow()
     */
    public function fetchRow( $where = null, $order = null, $offset = null )
    {
	$row = parent::fetchRow( $where, $order, $offset );

	return $row;
    }
    
    /**
     * (non-PHPdoc)
     * @see Zend_Db_Table_Abstract::insert()
     */
    public function insert( array $data )
    {
	$return = parent::insert( $data );
	
	$this->_auditCode();
	
	return $return;
    }

    /**
     * (non-PHPdoc)
     * @see Zend_Db_Table_Abstract::update()
     */
    public function update( array $data, $where )
    {
	$return = parent::update( $data, $where );
	
	$this->_auditCode();
	
	return $return;
    }

    /**
     * (non-PHPdoc)
     * @see Zend_Db_Table_Abstract::delete()
     */
    public function delete( $where )
    {
	$return = parent::delete( $where );
	
	$this->_auditCode();
	
	return $return;
    }

    /**
     * 
     * @access public
     * @param array $data
     * @return array
     */
    public function cleanData( array $data )
    {
	$fields = parent::info( parent::COLS );

	foreach ( $data as $key => $value )
	    if ( !in_array( $key, $fields ) )
		unset( $data[$key] );

	return $data;
    }
    
    /**
     * 
     */
    protected function _auditCode()
    {
	if ( $this->_auditing ) {
	    
	    $query = $this->getAdapter()->getProfiler()->getLastQueryProfile();

	    $mapperAuditoria = new Model_Mapper_Auditoria();
	    $mapperAuditoria->save( $query );
	}
    }

    /**
     * 
     * @access 	public
     * @param 	array $where
     * @param 	int $id
     * @return 	boolean
     */
    public function isUnique( array $where, $id = null )
    {
	$rows = $this->fetchAll( $where );

	$result = false;

	switch ( $rows->count() ) {

	    case 0:
		$result = true;
		break;

	    case 1:
		if ( !empty( $id ) )
		    $result = ($id === $rows[0]->{$this->getPrimaryKey()} );
		break;
	}

	return $result;
    }

    /**
     * 
     * @access 	public
     * @return 	string
     */
    public function __toString()
    {
	return $this->_name;
    }

    /**
     *
     * @return string 
     */
    public function getPrimaryKey()
    {
	return array_shift( $this->info( App_Model_DbTable_Abstract::PRIMARY ) );
    }

}