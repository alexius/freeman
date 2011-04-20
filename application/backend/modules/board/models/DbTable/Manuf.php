<?php

class DbTable_Manuf extends DbTable_Base 
{
    /** 
     * Table name
        */    
    protected $_name    = 'manufacturers';
    protected $_primary = 'id';
    
    public function deleteAllByKey($key, $value)
    {
        parent::delete($this->getAdapter()->quoteInto($key . ' = ?', $value));                                                                                                                                                                        
    }
}
