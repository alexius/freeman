<?php

/**
 * User authentication controller
 *
 * @author      Fedor Petryk
 * @copyright  Copyright (c) 2006-2010 S2B (http://www.s2b.com.ua)
 */
class Default_IndexController extends Core_Controller_Start
{
	protected $_defaultServiceName = 'UserService';
	
	/**
	 * 
	 * 
	 * @var UserService
	 */
	protected $_service = null;
	
	/**
	 * Authentication of users
	 * Post params login, password  
	 **/
    public function indexAction()
    {
    	// TODO Authentication with Active Directory
    	$auth = Zend_Auth::getInstance(); 
    	if (!$auth->hasIdentity())
    	{
			if ($this->_request->isPost())
			{
				$data = $this->_request->getPost(); 
				if ($this->_service->authenticate($data) === true)
				{
					$user = Core_Model_User::getInstance(); 
					if (empty($user->default_module))
					{
						throw new Exception(Errors::getError(203));
					}
    				$this->_redirect($user->default_module);
				}
			} 
			$this->view->service = $this->_service;
    	}
    	else
    	{
    		$this->_helper->layout->disableLayout();  
    		$this->_helper->viewRenderer->setNoRender();
    		$user = Core_Model_User::getInstance();
    		$this->_redirect($user->default_module);	
    	}
    }
    
  	/**
  	 * Clearing user session
	 **/  
    public function logoutAction()
    {
    	$this->_helper->layout->disableLayout();  
    	$this->_helper->viewRenderer->setNoRender();
   		UserService::logout(); 	
   		$this->_redirect('/');	
    }
}
