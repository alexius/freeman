<?php

class DbTable_News extends DbTable_Base 
{
    /** Table name */
    protected $_name    = 'news';
    protected $_primary = 'id';  
     
    public function deleteAllByKey($key, $value)
    {
        parent::delete($this->getAdapter()->quoteInto($key . ' = ?', $value));                                                                                                                                                                        
        $this->getAdapter()->delete('slugs', 
            $this->getAdapter()->quoteInto('item_id = ?', $value));   
    }
}
