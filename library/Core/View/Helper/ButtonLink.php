<?php

/**
 * 
 * Messenger helper class class
 * @author Fedor Petryk
 * @copyright  Copyright (c) 2006-2010 S2B (http://www.s2b.com.ua)
 */
class Zend_View_Helper_ButtonLink
{
	
	protected static $_httpPath = null;
	protected static $_baseHttpPath = null;
	
	public function setView(Zend_View_Interface $view) {
		$this->view = $view;
	}
	
	/**
	 * 
	 * Return current module base link path
	 * @param int $link | if needed to return raw path
	 * @param String $name | link visual name
	 * @param String $id | link identifier 'id="a1"'
	 * @param Srting $classes | classes list
	 * @param bool $blank | open in new window 
	 */
	public function buttonLink($link, $name, $id = '', $classes = '', $blank = false)
	{
//		TODO add action checker
		
/*		$user = Core_Model_User::getInstance();
		$acl = Zend_Registry::get('aclObject');*/

		if ($blank == true){
			$blank = ' target=_blank ';
		}

		if (!empty($link)){
			$id = ' id="' . $id . '"';
		}
		if (!empty($link)){
			$link = ' href="' . $link . '"';
		}
		$template = '<p><a ' . $id . $blank
			 . $link . ' class="dialog_link ui-state-default ui-corner-all '
			. $classes . '">
			' . $name . '</a></p>';
		return $template;
	}
}
?>