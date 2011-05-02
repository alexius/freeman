<?php

/**
 * 
 * Role Service class
 * @author Fedor Petryk
 * @copyright  Copyright (c) 2006-2010 S2B (http://www.s2b.com.ua)
 */
class TranslationService extends Core_Service_Super
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
	protected $_mapperName = 'TranslationMapper';
	
	/**
	 * 
	 * THe validator - role form class name
	 * @var String
	 */
	protected $_validatorName = 'Form_Translation';
	
		/**
	 * 
	 * THe validator - role form class object
	 * @var Form_Role
	 */
	protected $_validator;

    public function setSystem($system)
    {
        $this->_mapper->setSystem($system);

    }
    
	public function add($data)
	{
		if (!$this->_validator->isValid($data))
		{
			$this->setError(Errors::getError(300));
			$this->setFormMessages($this->_validator->getMessages());
			return false;
		}
		$conf = Zend_Registry::get('app_config');
		$res = $this->_mapper->add(
			$this->_validator->getValues(),
			$conf['languages']
		);
		if ($res){
			$this->setMessage(Core_Model_Messages::getMessage(2));
			return true;
		}
		return false;
	}

	public function save($data)
	{
		if (empty($data['trans'])){
			return false;
		}

		$res = $this->_mapper->saveTranslation($data['trans']);

		if ($res !== true){
			$this->setError($res);
			return false;
		}

		$this->setMessage(Core_Model_Messages::getMessage(1));
		return true;
	}


	/**
	 * @param  $data | array of filter params (module, resourse, controller
	 * @return bool
	 */
	public function translations($data)
	{
		$pattern = '/[a-z\_]*/i';
		$valid = new Zend_Validate_Regex($pattern);
		
		if (!$valid->isValid($data['fmodule']) ||
				!$valid->isValid($data['fresourse']) ||
				!$valid->isValid($data['faction']))
		{
			return false;
		}

		if (!$valid->isValid($data['search'])){
			return false;
		}

		$trans = $this->_mapper->getTranslation(
			$data['fmodule'],
			$data['fresourse'],
			$data['faction'],
			$data['search']
		);

		if ($trans->count() > 0){
			$this->_jsonData['data'] = $trans->generateInputs();
            $this->setError(Core_Model_Messages::getMessage('mdata_get'));
		}
        $this->setError(Core_Model_Errors::getError('no_data'));
	}

	public function filters()
	{
		return $this->_mapper->getFilters();
	}

}