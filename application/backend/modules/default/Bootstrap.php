<?php

/**
 * Extended base class for bootstrap classes
 *
 *
 * @uses       Zend_Application_Module_Bootstrap
 * @package    Default
 * @subpackage Bootstrap
 * @copyright  Copyright (c) 2006-2010 S2B (http://www.s2b.com.ua)
 */

class Default_Bootstrap extends Zend_Application_Module_Bootstrap 
{
	protected $_moduleName = 'default';

	/**
	 * 
	 * Init module specific configuration
	 */
	protected function _initConfiguration()
    {
		$options = $this->getApplication()->getOptions();
    	Zend_Registry::set ( 'config', $options );    	
		    	
    	set_include_path(implode(PATH_SEPARATOR, array(
		    realpath(APPLICATION_PATH . '/modules/' . $this->_moduleName . '/model'),
		    realpath(APPLICATION_PATH . '/modules/' . $this->_moduleName . '/service'),
		    realpath(APPLICATION_PATH . '/modules/' . $this->_moduleName . '/mapper'),
		    get_include_path(),
		)));
    }

}