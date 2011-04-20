<?php

class DbTable_Users extends DbTable_Base 
{
    /** Table name */
    protected $_name    = 'users';
    
	public function deleteById($id)
	{
		parent::delete($this->getAdapter()->quoteInto('user_id = ?', $id));
	}
}
