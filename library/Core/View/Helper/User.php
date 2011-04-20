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
		$html = '<div class="user"><div>';
		$html .= '<div class="greeting">'
			. Zend_Registry::get('translation')->get('greeting') . ' ' 
			. '</div>';
		$html .= '<div class="avatar">
				     <img src="' . $host . 'profile/settings/getavatar">'           
	              . '</div>';
		$html .= '<div class="user_data"><b>' . $user->getInitials() . '</b><br /><br />';
		//$html .= 'Вы вошли как: <b>' . $user->role_name . '</b><br /><br />';
		$html .= '<b><a href = "' . $host . 'profile/settings/index/">' .
                Zend_Registry::get('translation')->get('settings') . '</a></b><br /><br />';
		$html .= '<b><a href = "' . $host . 'default/index/logout/">' .
                Zend_Registry::get('translation')->get('exit') . '</a></b>';

		$html .= '</div>';
		$html .= '</div></div>';
		return $html;
	}
}
?>