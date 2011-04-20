<?php

/**
 * 
 * Messenger helper class class
 * @author Fedor Petryk
 * @copyright  Copyright (c) 2006-2010 S2B (http://www.s2b.com.ua)
 */
class Zend_View_Helper_Icon
{
	
	protected static $_httpPath = null;
	protected static $_baseHttpPath = null;
	
	public function setView(Zend_View_Interface $view) {
		$this->view = $view;
	}
	
	/**
	 * 
	 * Return current module base link path
	 * @param int $base | if needed to return raw path
	 */
	public function icon($icon) 
	{
		$template = '<span class="ui-icon-green ' . $icon . '"></span>';
		return $template;
	}
}
?>