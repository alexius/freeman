<?php
/**
 * User's settings controller
 * @author      Aleksey Kagarlykskiy
 * @copyright  Copyright (c) 2006-2010 S2B (http://www.s2b.com.ua)
 */

class Profile_SettingsController extends Core_Controller_Start
{
	protected $_defaultServiceName = 'ProfileService';
	
	/**
	 * @var ProfileService
	 */
	protected $_service = null;
	
  	/**
  	 * Load user's avatar
	 **/  
	public function loadavatarAction()
	{
    	if ($this->_request->isPost())
		{
			$this->view->post = $post = $this->_request->getPost();
			$add = $this->_service->loadAvatar($post);
			if($add){
				$this->_redirect('/profile/settings/index/');
			}
		} 
	}
	
  	/**
  	 * Load user's avatar
	 **/  
	public function editsettingsAction()
	{
    	if ($this->_request->isPost())
		{
			$this->view->post = $post = $this->_request->getPost();
			$add = $this->_service->loadSettings($post);
			if($add){
				$this->_redirect('/profile/settings/index/');
			}
		} 
	}
	
	public function getavatarAction(){
		$this->_helper->layout->disableLayout();
	}
	
	public function indexAction()
	{
	}
}