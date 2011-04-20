<?php

class DbTable_Settings extends DbTable_Base 
{
    /** Table name */
    protected $_name    = 'settings';
    protected $_primary = 'id';
	
	public function deleteById($id)
	{
		parent::delete($this->getAdapter()->quoteInto($_primary . ' = ?', $id));
	}
	
	public function fetchGroup($group, $vision = 'backend')
	{
        if (empty($vision))
        {
            $vision = 'backend';
        }
        
		$select = parent::select()->from($this->_name)
			->where('params_group = "' . $group . '"');
		return parent::fetchAll($select);
	}
}
