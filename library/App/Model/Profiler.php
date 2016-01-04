<?php

class App_Model_Profiler extends Zend_Db_Profiler
{
    /**
     *
     * @param boolean $enabled 
     */
    public function __construct( $enabled = false ) 
    {
	parent::__construct( $enabled );
	
	$this->setFilterQueryType( self::INSERT | self::UPDATE | self::DELETE );
    }
    
    /**
     *
     * @param int $queryId
     * @return void 
     */
    public function queryEnd( $queryId ) 
    {
	$retorno = parent::queryEnd( $queryId );
	
	if ( !$this->getEnabled() || $retorno == self::IGNORED ) {
            return;
        }
	
	$query = $this->getLastQueryProfile();

	$this->setEnabled( false );

	$mapperAuditoria = new Model_Mapper_Auditoria();
	$mapperAuditoria->save( $query );

	$this->setEnabled( true );
	
	return $retorno;
    }
}