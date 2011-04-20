<?php

class DbTable_SysMsg extends DbTable_Base 
{
    /** Table name */
    protected $_name    = 'system_messages';
   
	
	public function deleteById($id)
	{
		parent::delete($this->getAdapter()->quoteInto('id = ?', $id));
	}
}
