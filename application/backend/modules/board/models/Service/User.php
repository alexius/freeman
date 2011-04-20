<?php
class Service_User extends Service_Super 
{
	//protected $_mapperName = 'ProductsMapper';
	protected $_formName = 'LoginForm';
	
	public function logout()
	{
		Zend_Auth::getInstance()->clearIdentity(); 
	}
	
	public function authenticate(array $data)
	{
		$user = new User();
		
		if ($this->_validator->isValid($data)){
			$user->populate($this->_validator->getValues());
			if ($user->authenticate() === true){
				return true;
			}	
			else {
				return $user->getError();
			}
		} 
		else {
			return false;
		}

	}

}