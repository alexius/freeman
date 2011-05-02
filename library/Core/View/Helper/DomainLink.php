<?php

/**
 * 
 * Domain link class
 * @author Fedor Petryk
 * @copyright  Copyright (c) 2006-2010 S2B (http://www.s2b.com.ua)
 */
class Zend_View_Helper_DomainLink 
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
	public function domainLink($base = null) 
	{
        $config = Zend_Registry::get ( 'app_config' );
		if ($base == null)
		{
			$configModule = Zend_Registry::get ( 'config' );
			if (self::$_httpPath === null) {
				self::$_httpPath = $config['baseHttpPath'] 
					. $configModule['httpPath'];
			}
			
			return self::$_httpPath;
		}
		else if ($base == 1)
		{
			if (self::$_baseHttpPath === null) {
				self::$_baseHttpPath = $config['baseHttpPath'];
			}
			return self::$_baseHttpPath;
		}
        else if ($base == 2)
        {
            return $config['textEditorPrefix'];
        }
	}
}
?>