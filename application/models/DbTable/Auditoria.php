<?php

/**
 * 
 * @version $Id: Auditoria.php 807 2012-08-14 19:35:07Z fred $
 */
class Model_DbTable_Auditoria extends App_Model_DbTable_Abstract
{
     /**
     *
     * @var bool
     */
    protected $_auditing = false;
    
    /** 
     * O nome da tabela
     * 
     * @var string
     */
    protected $_name = 'auditoria';
}