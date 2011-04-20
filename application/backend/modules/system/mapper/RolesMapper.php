<?php

/**
 * 
 * RolesMapper class
 * @author Fedor Petryk
 * @copyright  Copyright (c) 2006-2010 S2B (http://www.s2b.com.ua)
 */
class RolesMapper extends Core_Mapper_Super
{
	
	/**
	 * 
	 * DbTable Class
	 * @var DbTable_Roles
	 */
	protected $_tableName = 'DbTable_Roles';
	
	/**
	 * 
	 * Rols row class
	 * @var Role
	 */
	protected $_rowClass = 'Role';
	
	
	/**
	 * 
	 * Saves user department
	 * @param array $data | user id, department id
	 * @return boolean
	 */
	public function saveUserDepartment(array $data)
	{
		$this->setDbTable('DbTable_UsersDepartments');
		$db = $this->getDbTable();
		
		if ($data['department_id'] == 0)
		{
			$db->delete('user_id = "' . $data['user_id'] . '"');
			return true;	
		}
		
		
		$select = $db->getAdapter()->select()
			->from(array('departments'))
				->where('department_id = "' . $data['department_id'] . '"');
		$dep = $db->getAdapter()->fetchOne($select);	
		if (empty($dep))
		{
			return false;
		}
		
				
		$select = $db->select()
			->from(array('users_departments'))
			->where('user_id = "' . $data['user_id'] . '"');
			
		$row = $db->fetchRow($select);	
		
		if (empty($row))
		{
			$data = $this->getDbTable()->cleanArray($data);
			$this->getDbTable()->insert($data);
		}
		else
		{
			$row->department_id = $data['department_id'];
			$row->chief = $data['chief'];
			$row->save();	
		}
		return true;
	}

	
	/**
	 * 
	 * Checks if rules are assignable by module (gets this from db)
	 * 
	 * @param array $rules | the array of resourses and rules
	 * @param Role $model | The role model
	 * @return boolean
	 */
	public  function  addRules($rules, $model)
	{
		$db = $this->getDbTable()->getAdapter();
		$select = $db->select()->from(array('r' => 'rights'), array('right_id')	)
			->joinLeft(array('rr' => 'resourses_rights'), 'r.right_id = rr.right_id',
				array())
			->joinLeft(array('res' => 'resourses'), 'res.resourse_id = rr.resourse_id',
				array('resourse_id'))
			->joinLeft(array('m' => 'system_modules'), 'res.module_id = m.id',
				array('assignable'));
				
		foreach ($rules as $res => $actions)
		{
			foreach ($actions as $action => $on)
			{
				$select->orWhere('r.right_id = "' . $action . '"');
			}
		}	
		
		$rules = $db->fetchAll($select);			
		
		$query = 'INSERT INTO roles_rights (role_id, right_id, resourse_id) VALUES ';
		$i = 1; 
		foreach ($rules as $res => $actions)
		{
			$query .= ' (' . $model->role_id . ', ' . $actions['right_id'] . ',' . $actions['resourse_id'] . ')';	
			if ( count($rules) > $i )
			{
				$query .= ',';	
			}	
			$i++;
		}
	
		return $this->runQuery($query);		
	}
	
	/**
	 * 
	 * Saves new user role
	 * @param array $data | user id, role id
	 * @return boolean
	 */
	public function saveUserRole(array $data)
	{
		$this->setDbTable('DbTable_UsersRoles');
		$db = $this->getDbTable();
		
		if ($data['role_id'] == 0)
		{
			$db->delete('user_id = "' . $data['user_id'] . '"');
			return true;	
		}
		
		
		$select = $db->getAdapter()->select()
			->from(array('roles'))
				->where('editable = 1 AND role_id = "' . $data['role_id'] . '"');
		$role = $db->getAdapter()->fetchOne($select);	
		$role = new Role($role);
		if (empty($role))
		{
			return false;
		}
		else if (!$role->editable)
		{
			return false;
		}
				
		$select = $db->select()
			->from(array('users_roles'))
			->where('user_id = "' . $data['user_id'] . '"');
			
		$row = $db->fetchRow($select);	
		
		if (empty($row))
		{
			$data = $this->getDbTable()->cleanArray($data);
			$this->getDbTable()->insert($data);
		}
		else
		{
			$row->role_id = $data['role_id'];
			$row->save();	
		}
		return true;
	}
	
	/**
	 * 
	 * Clears all role rights (rules)
	 * @param int $id role_id
	 * @return boolean
	 */
	public function deleteAllRules($id)
	{
		return $this->getDbTable()->getAdapter()
			->query('DELETE FROM roles_rights WHERE role_id = ' . $id);
	}
	
	/**
	 * 
	 * Get full role with rules by id
	 * @param int $id | role id
	 * @return Role
	 */
	public function getRole($id) 
	{
		$db = $this->getDbTable()->getAdapter();
		$select = $db->select()
			->from(array('r' => 'roles'))
			->joinLeft(array('rr' => 'roles_rights'), 'r.role_id = rr.role_id')
			->where('r.editable = 1 AND r.role_id = ' . $id);
		$data = $db->fetchAll($select);		

		$model = new $this->_rowClass;
		if (!empty($data))
		{
			$model->populate($data[0]);
			
			$resourses = array();
			foreach ($data as $reses)
			{
				$resourses['actions'][$reses['resourse_id']]
					[$reses['right_id']] = $reses['right_id'];		
					
				$resourses['resourses'][$reses['resourse_id']] 
					= $reses['resourse_id'];
					
				$resourses['default_module'] = $reses['default_module'];
			}
			$model->setRules($resourses);
		}
		else
		{
			$model->setError(Errors::getError(302));	
		}
		return $model;	
	}
	
	/**
	 * 
	 * Get ACL list from db
	 * @return array | access control list by modules
	 */
	public function getFullAcl()
	{
		$db = $this->getDbTable()->getAdapter();
		$select = $db->select()
			->from(array('m' => 'system_modules'), array('module_name', 'id'))
			->joinLeft(array('r' => 'resourses'), 'r.module_id = m.id',
				array('resourse_id', 'resourse_name'))
			->joinLeft(array('rr' => 'resourses_rights'), 
				'rr.resourse_id = r.resourse_id', array())
			->joinLeft(array('ri' => 'rights'), 'ri.right_id = rr.right_id')
			->where('m.assignable = 1');
		return  $db->fetchAll($select);	
	}
	
	/**
	 * 
	 * Returns all modules pairs id => module_name
	 * @return array | module id - module name
	 */
	public function getModulesPairs()
	{
		$db = $this->getDbTable()->getAdapter();
		$select = $db->select()
			->from(array('m' => 'system_modules'), array('module_name', 'id'));
		return $db->fetchPairs($select);
	}
	
	
	/**
	 * 
	 * Pairs of users and roles
	 * @return array | departmetns list
	 */
	public function getUsersDepartments()
	{
		$db = $this->getDbTable()->getAdapter();
		$select = $db->select()
			->from(array('u' => 'users'), array('user_id', 'name', 'surname', 'patronymic'))
			->joinLeft(array('ud' => 'users_departments'),
				'ud.user_id = u.user_id', array('department_id', 'chief'));
		return $db->fetchAll($select);	
	}

	
	/**
	 * 
	 * Pairs of users and roles
	 * @return array | list of possible users roles
	 */
	public function getUsersRoles()
	{
		$db = $this->getDbTable()->getAdapter();
		$select = $db->select()
			->from(array('u' => 'users'), array('user_id', 'name', 'surname', 'patronymic'))
			->joinLeft(array('ur' => 'users_roles'),
				'ur.user_id = u.user_id', array('role_id'));
		return $db->fetchAll($select);	
	}
	
	/**
	 * 
	 * Returns all roles pairs irole_id => role_name 
	 * @return array | role spairs role id - role name
	 */
	public function getRolesPairs()
	{
		$db = $this->getDbTable()->getAdapter();
		$select = $db->select()
			->from(array('r' => 'roles'), array('role_id', 'role_name'))
			->where('r.editable = 1');
		return $db->fetchPairs($select);
	}
	
	/**
	 * 
	 * Returns all users pairs user_id => user_name
	 * @return array | users pairs user id - user name
	 */
	public function getUsersPairs()
	{
		$db = $this->getDbTable()->getAdapter();
		$select = $db->select()
			->from(array('u' => 'users'), array('user_id', 'name', 'surname', 'patronymic'));
		return $db->fetchPairs($select);
	}
	
}