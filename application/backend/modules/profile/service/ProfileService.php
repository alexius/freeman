<?php

/**
 * 
 * User's Profile Service class
 * @author Aleksey Kagarlykskiy
 * @copyright  Copyright (c) 2006-2010 S2B (http://www.s2b.com.ua)
 */
class ProfileService extends Core_Service_Super 
{
	/**
	 * @var Form_LoadavatarForm
	 */
	protected $_avatarForm = null;

	/**
	 * @var Form_Settings
	 */
	protected $_settingsForm = null;
	
	/**
	 * Profile mapper class
	 * @var String
	 */
	protected $_mapperName = 'ProfileMapper';

	/**
	 *  @var ProfileMapper
	 */
	protected $_mapper;
	
	public function loadAvatar(array $data) 
	{
		if (!$this->getAvatarForm()->isValid($data)) {
			return false;
		}
		
		$adapter = $this->_avatarForm->document->getTransferAdapter();
		$file = $adapter->getFileInfo();
		$tmpName = $file['document']['tmp_name'];
		
		$r = new ResizerService();
		$content = $r->resizeAvatar($tmpName);
		
		$user = Core_Model_User::getInstance();
		$id = $user->user_id;
		
		$this->_mapper->setAvatar($id, $content);
		return true;
		
	}

	public function loadSettings(array $data) 
	{
		if (!$this->getSettingsForm()->isValid($data)) {
			return false;
		}
		
		$user = Core_Model_User::getInstance();
		$id = $user->user_id;
		
		$filteredData = $this->getSettingsForm()->getValues();
		$res = $this->_mapper->setSettings(
			$id, 
			$filteredData
		);
		
		if ($res){
			$user->populate($filteredData);
		}
		
		$user->createUserStorage();
		//Zend_Auth::getInstance()->getStorage()->write($filteredData);

		return true;
		
	}
		
	/**
	 * Get loadavatar form
	 * @return Form_LoadavatarForm;
	 */
	public function getAvatarForm()
	{
		if ($this->_avatarForm == null){
			$this->_avatarForm = new Form_LoadavatarForm();
		}
		return $this->_avatarForm;
	}
	
	public function getAvatar()
	{
		$user = Core_Model_User::getInstance();
		$id = $user->user_id;
		$avatar = $this->_mapper->getAvatar($id);
        if(!$avatar)
        {
            $avatar = file_get_contents('images/nofoto.jpg');
        }

		return $avatar;
	}
	
	/**
	 * Get settings form
	 * @return Form_Settings;
	 */
	public function getSettingsForm()
	{
		if ($this->_settingsForm == null){
			$this->_settingsForm = new Form_Settings();
			$user = Core_Model_User::getInstance();
			
			$this->_settingsForm->populate($user->toArray());
		}
		return $this->_settingsForm;
	}
	
}

?>