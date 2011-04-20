<?php

/**
 * Base user domain class
 * Singleton class
 *
 * @author     Fedor Petryk
 * @package    Core_Model
 * @copyright  Copyright (c) 2006-2010 S2B (http://www.s2b.com.ua)
 */
class Core_Model_User extends Core_Model_Super 
	implements Zend_Auth_Adapter_Interface
{
	/**
	 * 
	 * @see Core_Model_Super
	 */
	protected $_data = array (
		'user_id' => null,
		'login' => null,
		'password' => null,
		'active_directory' => null,
		'name'=> null,
		'surname' => null,
		'patronymic' => null,
		'email' => null,
		'role' => null,
		'default_module' => null,
		'role_name' => null,
		'department_id' => null,
		'language' => 'ua'
	);

	/**
	 * 
	 * The instance of class
	 * @var Core_Model_User
	 */
	protected static $_instance = null;
	
	/**
	 * 
	 * Singleton cant have constructor
	 */
	public function __construct()
	{
		
	}
	
	/**
	 * 
	 * Singleton cant be cloned
	 */
	public  function __clone()
	{
		
	}	
	
	/**
	 * 
	 * Returns an inctance of this class
	 * @return Core_Model_User
	 */
	public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
	
    /**
     * 
     * The authentication of the user
     * @uses Zend_Auth_Adapter_DbTable
     * @see library/Zend/Auth/Adapter/Zend_Auth_Adapter_Interface::authenticate()
     */
  	public function authenticate()
    {
        $username = $this->login; 
        $password = $this->password; 

        $db = Zend_Registry::get('DB');
         
        $authAdapter = new Zend_Auth_Adapter_DbTable($db); 
        $authAdapter->setTableName('users'); 
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
        	$this->populate($data);  
            return $this;
           
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
    
    /**
     * 
     * Creates persistent user storage
     * @uses Zend_Auth
     */
    public function createUserStorage()
    {
    	Zend_Auth::getInstance()->getStorage()->write($this->toArray());   
    }
    
	/**
	 * 
	 * Clears  all user data
	 */
	public  function clear()
	{
		foreach ($this->_data as $key => $value){
           	$this->$key = null;
       	}		
	}

	public function getInitials()
	{
		return $this->surname . ' '
				. mb_substr($this->name,0,1, 'UTF-8') . '. '
				. mb_substr($this->patronymic,0,1, 'UTF-8') . '.';
	}

}