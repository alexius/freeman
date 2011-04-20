<?php

/**
 * 
 * Messenger helper class class
 * @author Fedor Petryk
 * @copyright  Copyright (c) 2006-2010 S2B (http://www.s2b.com.ua)
 */
class Zend_View_Helper_IconButton
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
	public function iconButton($link, $icon, $tip = '', $id = '', $classes = '',
        $liClasses = '')
	{
		if (!empty($tip)) {
			$classes .= ' tooltip';
		}
		
		$template = '<li class="icon-link ui-state-default ui-corner-all '
            . $liClasses . '">
			<a id="' . $id . '" title = "' . $tip . '" 
			' . (!empty($link) ? 'href="' . $link . '"' : '' ) .
			    (!empty($classes) ? 'class="' . $classes . '"' : '') . '>
			<span class="ui-icon ui-icon-green ' . $icon . '"></span>' .
			'</a></li>';
		return $template;
	}
}
?>