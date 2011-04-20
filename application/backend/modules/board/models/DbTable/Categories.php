<?php

class DbTable_Categories extends DbTable_Base 
{
    /** Table name */
    protected $_name    = 'categories';
    protected $_primary = 'id';   
	
	public function deleteAllWhere($ids)
	{
		parent::delete($ids);
	}
    
    public function deleteAllByKey($key, $value)
    {
        parent::delete($this->getAdapter()->quoteInto($key . ' = ?', $value));                                                                                                                                                                        
        $this->getAdapter()->delete('slugs', 
            $this->getAdapter()->quoteInto('item_id = ?', $value));   
    }
}
