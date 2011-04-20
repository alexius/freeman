<?php

/**
 * 
 * Role Service class
 * @author Fedor Petryk
 * @copyright  Copyright (c) 2006-2010 S2B (http://www.s2b.com.ua)
 */
class RoleService extends Core_Service_Super 
{
	
	/**
	 * 
	 * Current service domain object
	 * @var Object
	 */
	protected $_rowObject = null;
	
	/**
	 * 
	 * Role mapper class
	 * @var RolesMapper
	 */
	protected $_mapperName = 'RolesMapper';
	
	/**
	 * 
	 * THe validator - role form class name
	 * @var String
	 */
	protected $_validatorName = 'Form_Role';
	
		/**
	 * 
	 * THe validator - role form class object
	 * @var Form_Role
	 */
	protected $_validator;
	
	/**
	 * Change user department
	 * @param array | user_id, department_id
	 * @return boolean
	 */
	public function saveUserDepartment(array $data)
	{
		$from = new Form_UserDepartment();
		if ($from->isValid($data))
		{
			$this->_mapper->saveUserDepartment($data);
			return true;
		}
		$this->setError(Errors::getError(303));
		return false;		
	}
	
	/**
	 * 
	 * Get role object
	 * @param int $id | role id
	 */
	public function getRole($id)
	{
		$this->_rowObject = $this->_mapper->getRole($id);
		$this->_validator->populate($this->_rowObject->toArray());
	}
	
	public function getObject()
	{
		return $this->_rowObject;
	}
	
	/**
	 * 
	 * Changing user role
	 * @param array $data
	 */
	public function changeUserRole(array $data)
	{
		$from = new Form_UserRole();
		if ($from->isValid($data))
		{
			$this->_mapper->saveUserRole($data);
			return true;
		}
	
		$this->setError(Errors::getError(303));
	}
	
	/**
	 * 
	 * Get full acl list by modules
	 * @return mixed | ACL or boolean
	 * 
	 */
	public function getFullAcl()
	{
		$acl = $this->_mapper->getFullAcl();
		$aclByModule = array();
		if (!empty($acl))
		{
			foreach ($acl as $a)
			{
				$aclByModule[$a['id']]['module_name'] = $a['module_name'];	
				
				$aclByModule[$a['id']]['resourses']
					[$a['resourse_id']]['resourse_name'] = $a['resourse_name'];
					
				if (!empty($a['right_id']))
				{
					$aclByModule[$a['id']]['resourses'][$a['resourse_id']]['actions']
						[$a['right_id']]['right_name'] = $a['right_name'];
				}
			}
			return $aclByModule;
		}
		return false;
	}
	
	/**
	 * 
	 * Creates new role with assigned resourses and rights
	 * @param array | Massive of role data 
	 * @return mixed | boolean or Role
	 */
	public function saveRole(array $data)
	{
		
		if (!$this->_validator->isValid($data))
		{
			$this->_error = Errors::getError(300);
			return;
		}
		if (!$this->_validateRules($data))	
		{
			$this->_error = Errors::getError(301);
			return false;
		}
		
		$class = $this->_mapper->getRowClass();
		$model = new $class;
		$model->populate($data);
		
		if ($model->role_id != null)
		{
			$this->_mapper->deleteAllRules($model->role_id);
		}
		
		$this->_mapper->objectSave($model);
		if ($model->getError())
		{
			$this->_error = $model->getError();
			return false;				
		}
		
		$ans = $this->_mapper->addRules($data['actions'],$model);
		if ($ans === true)
		{
			return $model;
		}
		else if ($data['role_id'] == null)
		{
			$this->_mapper->getDbTable()->deleteById($model->role_id);
			$this->_error = Errors::getError(300);
			return false;
		}

	}
	
	/**
	 * 
	 * Validating rules and building queries
	 * @param $data | rules array
	 * @return boolean
	 */
	private function _validateRules(array $data)
	{
		if (empty($data['resourses']) || empty($data['actions']))
		{
			return false;
		}
		return  true;
	}
	
	
	
}