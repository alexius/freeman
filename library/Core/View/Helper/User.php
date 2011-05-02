<?php

/**
 * user menu builder
 * build header menu according to permited usrs rights 
 * @author Fedor Petryk
 * @copyright  Copyright (c) 2006-2010 S2B (http://www.s2b.com.ua)
 */
class Zend_View_Helper_User {
		
	public function setView(Zend_View_Interface $view) 
	{
		$this->view = $view;
	}
	
	/**
	 * build header
	 */
	public function user() 
	{
		if (!Zend_Auth::getInstance()->hasIdentity())
		{
			return; 
		}
		
		$host = $this->view->domainLink(1);
		$user = Core_Model_User::getInstance();
        $conf = Zend_Registry::get('app_config');
        $site =  $conf['baseSiteHttpPath'];

        $template = '<div id="profile-links">' .
				 $this->view->translation('greeting') .
                    ', <a href="' . $host . 'profile/settings/index/"
                     title="' .
                        $this->view->translation('settings') . '">' .
                    $user->name . ' ' . $user->patronymic .
                    '</a>
				 <br /><br />
				<a href="' . $site . '" target="_blank">' .
                    $this->view->translation('view_site') . '</a> |
				<a href="' . $host . 'default/index/logout/">' .
                     $this->view->translation('exit') .
                '</a></div>';

		return $template;
	}
}
?>