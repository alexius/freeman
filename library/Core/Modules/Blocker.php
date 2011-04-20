<?php

/**
 * 
 * The modue blocked checker
 * @author Fedor Petryk
 * @copyright  Copyright (c) 2006-2010 S2B (http://www.s2b.com.ua)
 */
class Core_Modules_Blocker extends Zend_Controller_Plugin_Abstract
{

	/**
	 * 
	 * Checks whethere module was blocked by system
	 * @param $moduleName
	 */
	public static function isBlocked($moduleCode)
	{
		$db = Zend_Registry::get('DB');
		$select = $db->select()->from('system_modules')
			->where('module_code = "' . $moduleCode . '"');
		$module = $db->fetchRow($select);
		
		if (empty($module)|| $module['blocked'] == 1)
		{
			return true;
		}
		return false;
	}
}