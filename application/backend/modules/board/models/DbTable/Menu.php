<?php

class DbTable_Menu extends DbTable_Base 
{
    /** 
     * Table name
        */    
    protected $_name    = 'menu';
    protected $_primary = 'id';
    
    public function deleteAllByKey($key, $value)
    {
        parent::delete($this->getAdapter()->quoteInto($key . ' = ?', $value));                                                                                                                                                                        
        $this->getAdapter()->delete('slugs', 
            $this->getAdapter()->quoteInto('item_id = ?', $value));   
    }
}
