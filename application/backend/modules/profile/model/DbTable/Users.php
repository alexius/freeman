<?php

/**
 * 
 * users DbTable Class
 * @author user
 *
 */
class DbTable_Users extends Core_Model_DbTable_Base 
{
    /** Table name */
    protected $_name    = 'users';
    
	public function deleteById($id)
	{
		parent::delete($this->getAdapter()->quoteInto('user_id = ?', $id));
	}
}
