<?php

/**
 * Loading roles and resourses, verifying users rights 
 *
 * @author     Petryk Fedor
 * @uses       Zend_Acl
 * @package    Core_Acl
 * @copyright  Copyright (c) 2010-2011 S2B (http://www.105.in.ua)
 */
class Core_Acl_AclBuilder extends Zend_Acl
{
	
    /**
     * Db gateway
     *
     * @var Zend_Db_Adapter_Abstract
     */
	protected $_db = NULL;
	
	/**
     * ACL Tree  registry
     * @var Array
     */
	protected $_rolesAcl = null;
	
	/**
     * Initializes DB from registry
     */
	public function init()
	{
		$this->_db = Zend_Registry::get('DB');	
	}
	
	/**
     * Returns the ACL
     *
     * @return Array (Tree)
     */
	public function getRoleAcl()
	{
		return $this->_rolesAcl;	
	}
	
	/**
     * Builds ACL Tree accroding to that in DB
     *
     * @uses   Zend_Db_Adapter_Abstract::get()
     */
	public function buildAcl()
	{
		$res_permissions = $this->getAllRoles();
		$groups = array();
           
        foreach ($res_permissions as $key => $group) {
            $groups[$group['role_code']][$key] = $group;
        }
        
		$this->_rolesAcl = $groups;   
         
        $select = $this->_db->select()->from('resourses');   
        $resourses = $this->_db->fetchAssoc($select);
                 
        foreach ($resourses as $res) {
            $this->addResource($res['resourse_code']);
        }
	
    // TODO remove guest from code 
        
        foreach ($groups as $role => $g)
        {
        	if ($role != 'guest'){
        		$this->addRole(new Zend_Acl_Role($role), 'guest');
            }
        	else {
        		$this->addRole(new Zend_Acl_Role($role));
            }
        }

        foreach ($groups as $key => $g)
        {
            foreach ($g as $user)
            {
                $this->allow($user['role_code'], $user['resourse_code'], 
                        $user['action']);
                
            }
        } 
	}
	
	/**
	 * Returns all roles in the system
	 * @return array | users roles list
	 */
	protected function getAllRoles()
	{
		$select = $this->_db->select()
            ->from(array('r' => 'roles'), 
            	array('role_code', 'role_name', 'role_id', 
            	'parent_role_id', 'editable')
            	)
            ->joinInner(array('rr' => 'roles_rights'), 
            	'rr.role_id = r.role_id',
            	 array())
            ->joinLeft(array('ri' => 'rights'), 'ri.right_id = rr.right_id',
            	array('action', 'right_name', 'menu'))
            ->joinLeft(array('res' => 'resourses'), 
            	'res.resourse_id = rr.resourse_id',
            	array('resourse_code', 'resourse_name'))
            ->joinLeft(array('m' => 'system_modules'), 'm.id = res.module_id',
            	array('module_name', 'module_code AS default_module', 'show'))
            ->order('r.role_id ASC')
            ->order('r.parent_role_id ASC')
			->order('m.id ASC')
			->order('ri.right_name ASC');

        return $this->_db->fetchAll($select);
	}
}