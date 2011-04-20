<?php

/**
 * User administration controller
 *
 * @author     Petryk Fedor  
 * @copyright  Copyright (c) 2006-2010 S2B (http://www.s2b.com.ua)
 */
class System_IndexController extends Core_Controller_Start
{
	
	/**
     * Default service class name for current controller
     *
     * @var String
     */
	protected $_defaultServiceName = 'RoleService';
	
	/**
     * The service layer object
     *
     * @var RoleService
     */
	protected $_service;

	/**
	 * Shows and changes users departments
	 * 
	 */
	public function departmentAction()
	{
    	if ($this->_request->isPost())
		{
			$this->view->post = $post = $this->_request->getPost();
			$add = $this->_service->saveUserDepartment($post);
		} 			
	}
	
	/**
     * View current roles
     * see view script
     */
    public function indexAction()
    {
    }

    /**
     *
     * Assign role to a user
     * 
     */
    public function adduserroleAction()
    {
    	if ($this->_request->isPost())
		{
			$this->view->post = $post = $this->_request->getPost();
			$add = $this->_service->changeUserRole($post);
		}  	
    }   
     
   /**
    * Create new role
    */
	public function addAction()
    {
		$acl = $this->_service->getFullAcl();
		$this->view->acl = $acl;
		
		if ($this->_request->isPost())
		{
			$this->view->post = $post = $this->_request->getPost();
			$add = $this->_service->saveRole($post);
			
			if ($add){
				$this->_redirect('admin/index/edit/id/' . $add->role_id);
			}
		}
    }
    
    /**
     * Edit existence Role
     */
    public function editAction()
    {
    	$id = (int) $this->_request->getParam('id');
    	if ($id == 0)
    	{
    		$this->_redirect('admin');	
    	}
    	
    	$acl = $this->_service->getFullAcl();
		$this->view->acl = $acl;
		$this->_service->getRole($id);
		
		if ($this->_request->isPost())
		{
			$this->view->post = $post = $this->_request->getPost();
			$add = $this->_service->saveRole($post);
		}   	
    }
    
}
