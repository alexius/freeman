<?php

/**
 * 
 * Users mapper class
 * @author Fedor Petryk
 * @copyright  Copyright (c) 2006-2010 S2B (http://www.s2b.com.ua)
 */
class UsersMapper extends Core_Mapper_Super
{
	
	/**
	 * 
	 * Users DbTable class
	 * @var DbTable_Users
	 */
	protected $_tableName = 'DbTable_Users';
	
	/**
	 * 
	 * Users model
	 * @var Core_Mapper_Super
	 */
	protected $_rowClass = 'Core_Model_User';
	
	/**
	 * 
	 * Returns current user role
	 * @param Core_Model_User $user
	 * @return boolean
	 */
	public function getUserRole(Core_Model_User $user)
	{
		$db = $this->getDbTable()->getAdapter();
		$select = $db->select()->from(array('ur' => 'users_roles'), array())
			->joinLeft(array('r' => 'roles'), 'ur.role_id = r.role_id',
				array('role_code', 'role_name'))
			->joinLeft(array('sm' => 'system_modules'), 
				'sm.id = r.default_module', 
				array('sm.module_code as default_module'))
			->where('ur.user_id = ' . $user->user_id)
			->where('r.active = 1');	

		$result = $db->fetchRow($select); 
		$user->role = $result['role_code'];
		$user->default_module = $result['default_module'];
		$user->role_name = $result['role_name'];
		
		return true;
	}

		/**
	 *
	 * Returns current user role
	 * @param Core_Model_User $user
	 * @return boolean
	 */
	public function getUserDepartment(Core_Model_User $user)
	{
		$db = $this->getDbTable()->getAdapter();
		$select = $db->select()
			->from(array( 'ud' => 'users_departments'),
				array('department_id'))
			->where('ud.user_id = ' . $user->user_id);

		$result = $db->fetchOne($select);
		$user->department_id = $result;
		return true;
	}
	
	/**
	 * 
	 * TEMPORARY
	 * Fetches users list
	 */
	public function getUsersList()
	{
		$db = $this->getDbTable()->getAdapter();
		$select = $db->select()
			->from(array('u' => 'users'), 
				array('login', 'password', 'name', 'surname', 'patronymic'))
			->joinLeft(array('ur' => 'users_roles'), 
				'u.user_id = ur.user_id',
				array())
			->joinLeft(array('r' => 'roles'), 
				'ur.role_id = r.role_id',
				array('role_name', 'active'))
			->joinLeft(array('sm' => 'system_modules'), 
				'sm.id = r.default_module', 
				array('module_name'))
			->joinLeft(array('ud' => 'users_departments'), 
				'u.user_id = ud.user_id', 
				array())
			->joinLeft(array('d' => 'departments'), 
				'd.department_id = ud.department_id', 
				array('department_name'))
			->order('d.department_id ASC');	
		return $db->fetchAll($select);		
	}

}