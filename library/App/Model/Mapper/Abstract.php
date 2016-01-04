<?php

/**
 *
 */
abstract class App_Model_Mapper_Abstract
{
    /**
     *
     * @var array
     */
    protected $_data;

    /**
     *
     * @var array
     */
    protected $_methodsValidators = array();

    /**
     *
     * @var bool
     */
    protected $_breakOnFailure = true;

    /**
     *
     * @var App_Message
     */
    protected $_message;

    /**
     *
     * @var Zend_Config
     */
    protected $_config;

    /**
     *
     * @var Zend_Session_Namespace
     */
    protected $_session;

    /**
     * 
     * @access public
     * @return void
     */
    public function __construct ()
    {	
		$this->_message = new App_Message();
		$this->_config 	= Zend_Registry::get( 'config' );
		$this->_session = new Zend_Session_Namespace( $this->_config->geral->appid );
    }

    /**
     * 
     * @access 	public
     * @param 	array $data
     * @return 	void
     */
    public function setData ( array $data )
    {
		$this->_data = $data;
		return $this;
    }

    /**
     * 
     * @access 	public
     * @return 	mixed
     */
    public function getData ()
    {
		return $this->_data;
    }

    /**
     * 
     * @access 	public
     * @param 	array $validators
     * @param 	boolean $breakOnFailure
     * @return 	void
     */
    public function setValidators ( array $validators, $breakOnFailure = true )
    {
		$this->_methodsValidators = $validators;
	
		$this->setBreakOnFailure( $breakOnFailure );
    }

    /**
     * 
     * @access 	public
     * @param 	boolean $breakOnFailure
     * @return 	boolean
     */
    public function setBreakOnFailure ( $breakOnFailure )
    {
		$this->_breakOnFailure = (bool) $breakOnFailure;
    }

    /**
     * 
     * @access 	public
     * @return 	boolean
     * @throws 	Exception
     */
    public function isValid ()
    {
		$check = true;
	
		foreach ( $this->_methodsValidators as $method ) {
	
		    if ( method_exists( $this, $method ) ) {
	
				if ( !call_user_func( array( $this, $method ) ) ) {
				    if ( $this->_breakOnFailure )
						return false;
				    else
						$check = false;
				}
			
		    } else {
		    	
		    	throw new Exception( 'Method ' . $method . ' is not valid in the context of validation.' );
		    	
		    }
		}
	
		return $check;
    }

    /**
     * 
     * @access 	public
     * @return 	App_Message
     */
    public function getMessage ()
    {
		return $this->_message;
    }

    /**
     * 
     * @access 	protected
     * @param 	App_Model_DbTable_Abstract $dbTable
     * @return 	int
     */
    protected function _simpleSave ( App_Model_DbTable_Abstract $dbTable, $message = true )
    {    		
        $primary = $dbTable->getPrimaryKey();
	
	$this->_data = $this->_emptyToNull( $this->_data );
        
    	if ( empty( $this->_data[$primary] ) ) {
    		
    		$row = $dbTable->createRow();
    		$row->setFromArray( $this->_data );
    		
    		$result = $row->save();
    		
    	} else {
    		
    		$where = array( $primary . ' = ?' => $this->_data[$primary] );
    		
    		$data = $dbTable->cleanData( $this->_data );
    		
    		$result = (false !== $dbTable->update($data, $where)) ? $this->_data[$primary] : false;
    		
    	}
    	
	if ( $message ) {
	
	    if ( $result )
		    $this->_message->addMessage( $this->_config->messages->success, App_Message::SUCCESS );
	    else
		    $this->_message->addMessage( $this->_config->messages->error, App_Message::ERROR );
	
	}
    		
    	return $result;
    }
    
    /**
     *
     * @param array $data
     * @return array 
     */
    protected function _emptyToNull( $data )
    {
	foreach ( $data as $key => $value )
	    if (  $value === '' )
		$data[$key] = null;
	    
	return $data;
    }

    /**
     * 
     * @access 	protected
     * @param 	App_Model_DbTable_Abstract $dbTable
     * @param 	mixed $where
     * @return 	Zend_Db_Table_Rowset
     */
    protected function _simpleFetchAll ( App_Model_DbTable_Abstract $dbTable, $where = null )
    {
    	if ( is_null($where) && !empty($this->_data) ) {
    		
    		$where = array();
    		
    		foreach ( $this->_data as $key => $value ) 
    			$where[$key . ' = ?'] = $value;
    			    		
    	}
    	
		return $dbTable->fetchAll( $where );
    }

    /**
     * 
     * @access 	protected
     * @param 	App_Model_DbTable_Abstract $dbTable
     * @param 	array $where
     * @return 	Zend_Db_Table_Row
     */
    protected function _simpleFetchRow ( App_Model_DbTable_Abstract $dbTable, array $where = null )
    {
    	if ( is_null($where) ) {
    	
    		$where = array();
    	
	    	if ( !empty($this->_data) )
		    	foreach ( $this->_data as $key => $value )
	    			$where[$key.' = ?'] = $value;
	    			
    	}
    	
    	return $dbTable->fetchRow( $where );
    }
    
	/**
	 * 
	 * @access 	public
	 * @param 	App_Model_DbTable_Abstract $dbTable
	 * @param 	mixed $param
	 * @return 	boolean
	 */
	protected function _updateStatus ( App_Model_DbTable_Abstract $dbTable, $param )
	{
		try {
	
		    $data = array( 'liberado' => $param['action'] );
		    
	    	$where = $dbTable->getAdapter()->quoteInto( 'id IN(?)', $param['value'] );
		    	
			$dbTable->update( $data, $where );
	
		    return true;
		    
		} catch ( Exception $e ) {
	
		    return false;
		    
		}
	}

	/**
	 * 
	 * @access protected
	 * @param array $tags
	 */
    protected function _cleanCacheTag( array $tags )
    {
    	$frontendOptions = array('automatic_serialization' => true);
		
		$backendOptions = array('cache_dir' => APPLICATION_PATH . '/cache');
		
		$cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
		
	    $cache->clean( Zend_Cache::CLEANING_MODE_MATCHING_TAG, $tags );
    }
	
	/**
     * 
     * @access protected
     * @param int $status
     * @return string
     */
    protected function _showStatus( $status ) 
    {
        return $status ? 'Ativo' : 'Inativado';
    }
    
    /**
     *
     * @return string 
     */
    public function randomId()
    {
	return md5( uniqid( time() ) );
    }
}