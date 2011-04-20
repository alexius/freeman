<?php

class AuthController extends Zend_Controller_Action
{
	protected $_service;
    
	function init() 
    {    
        $this->_helper->layout()->disableLayout();     
        $this->_service =	new Service_User();  
    } 
     
	function indexAction() 
    { 
        $this->_forward('login');         
    }
     
    function loginAction() 
    {
    	if ($this->_request->isPost())
		{
	   		$data = $this->_request->getPost();
            $resp = $this->_service->authenticate($data);  

   			if ($resp === true)
            {                 
	   			$this->_redirect('/'); 		
	   		}
	   		else {
    			$this->view->form = $this->_service->getForm();
    			$this->view->message = $resp;
    		}		
    	}
        $this->view->form = $this->_service->getForm();
    }    
    
    function logoutAction() 
    { 
    	$this->_service->logout();
        $this->_appUser->currentRole = 'guest'; 
        $this->_redirect('/'); 
    } 
}
