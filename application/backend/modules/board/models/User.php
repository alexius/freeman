<?php

class User extends SuperModel 
{
    protected $_data = array(
    	'id' => null,
   		'login' => null,
    	'password' => null,
    	'username' => null,
    	'date_login' => null,
        'admin'    => null,
    );
    
	protected $_formName = 'LoginForm';
	
    public function authenticate()
    {
        $username = $this->login; 
        $password = $this->password; 

        $db = Zend_Registry::get('db');
         
        $authAdapter = new Zend_Auth_Adapter_DbTable($db); 
        $authAdapter->setTableName('managers'); 
        $authAdapter->setIdentityColumn('login'); 
        $authAdapter->setCredentialColumn('password'); 
               
                // Set the input credential values to authenticate against 
        $authAdapter->setIdentity($username); 
        $authAdapter->setCredential($password); 
                 
                // do the authentication  
        $auth = Zend_Auth::getInstance();              
        $result = $auth->authenticate($authAdapter); 
           
        if ($result->isValid()) 
        {
			$data = $authAdapter->getResultRowObject(null, 'password'); 
            $role = $this->getRole($data->id); 
            $data->role = $role;
            $auth->getStorage()->write($data);   

            $lang = new Zend_Session_Namespace('Default');
			if (!isset($lang->language)) {
           		$lang->language = 1;
            	Zend_Registry::set('lang', $lang->language);                          
            }
            return true;
        }
        else if (!$result->isValid()) { 
        	$this->_error = Errors::getError(200); 
        	return false;
        }
        else {
        	$this->_error = Errors::getError(201); 
        	return false;
        }
    }
    
        
    public function getRole($id)
    {
        $db = Zend_Registry::get('db'); 
        return $db->fetchOne('SELECT g.group_type FROM managers AS m
                LEFT JOIN groups AS g
                    on m.group_id = g.id
                WHERE m.id = "' . $id . '"');  
    }
}