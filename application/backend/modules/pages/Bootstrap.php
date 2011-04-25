<?php
/**
 * Default base class for bootstraping module
 *
 * @author     Fedor Petryk
 * @uses       Zend_Application_Bootstrap_Bootstrap
 * @copyright  Copyright (c) 2006-2010 S2B (http://www.s2b.com.ua)
 */
class Pages_Bootstrap extends Zend_Application_Module_Bootstrap
{
	protected $_moduleName = 'pages';
	
	/**
	 * 
	 * Init module specific configuration
	 */
	protected function _initConfiguration()
    {
    	$front = Zend_Controller_Front::getInstance();
    	$front->registerPlugin(
    		new Core_Controller_Plugin_Authentication()
    	);
    	
    	$options = $this->getApplication()->getOptions();
    	Zend_Registry::set ( 'config', $options );
    	
    	set_include_path(implode(PATH_SEPARATOR, array(
		    realpath(APPLICATION_PATH . '/modules/' . $this->_moduleName . '/model'),
		    realpath(APPLICATION_PATH . '/modules/' . $this->_moduleName . '/service'),
		    realpath(APPLICATION_PATH . '/modules/' . $this->_moduleName . '/mapper'),
		    get_include_path(),
		)));
		
        defined('APPLICATION_PUB')
                || define('APPLICATION_PUB', BASE_PATH . '/../../application/backend' . '/modules/' . $this->_moduleName);
 	}
}