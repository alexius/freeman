<?php

/**
 * 
 * The users service class
 * @author Fedor Petryk
 * @copyright  Copyright (c) 2006-2010 S2B (http://www.s2b.com.ua)
 */
class UserService extends Core_Service_Super 
{
	
	/**
	 * 
	 * Users mapper object
	 * @var UsersMapper
	 */
	protected $_mapperName = 'UsersMapper';
	
	/**
	 *
	 * Login form name
	 * @var string
	 */
	protected $_formName = 'LoginForm';
	
	/**
	 * 
	 * the login form class
	 * @var Form_LoginForm::
	 */
	protected $_loginForm = null;
	
	/**
	 * 
	 * Clearing session
	 */
	public static function logout()
	{
		Zend_Auth::getInstance()->clearIdentity(); 
	}
	
	/**
	 * 
	 * Authentication of user
	 * @uses Core_Model_User
	 * @param array $data
	 * @return boolean
	 */
	public function authenticate(array $data)
	{
		$user = Core_Model_User::getInstance();
		$this->initLoginForm();
		if ($this->_loginForm->isValid($data))
		{
			$user->populate($this->_loginForm->getValues());
			$auth = $user->authenticate();		
			if ($auth)
			{
				$this->_mapper->getUserRole($user);
				if (empty($user->role))
				{			
					$this->_loginForm->password->addError(Errors::getError(202, 
						array( 0 => $user->login)));
					$user->clear();
					Zend_Auth::getInstance()->clearIdentity();
					return false;	
				}
				$user->createUserStorage();
				return true;
			}	
			else 
			{
				$this->_loginForm->password->addError($user->getError());
				return false;
			}
		} 
		else 
		{
			return false;
		}
	}
	
	/**
	 * 
	 * creates login form instance
	 */
	public function initLoginForm()
	{
		$this->_loginForm = new Form_LoginForm(); 		
	}
	
	/**
	 * 
	 * Returns login form
	 * @return Form_LoginForm
	 */
	public function getLoginForm()
	{
		if (null == $this->_loginForm)
		{
			$this->initLoginForm();
		}
		return $this->_loginForm;
	}

}