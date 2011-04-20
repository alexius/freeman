<?php

/**
 * 
 * The modue loader class
 * @author Fedor Petryk
 * @copyright  Copyright (c) 2006-2010 S2B (http://www.s2b.com.ua)
 */
class Core_Modules_Loader extends Zend_Controller_Plugin_Abstract
{
	
	/**
	 * 
	 * The list of the modules
	 * @var String
	 */
	protected $_modules;
	
	/**
	 * 
	 * Assigns current module list
	 * @param array $modulesList
	 */
	public function __construct(array $modulesList) 
	{
		$this->_modules = $modulesList;
	}
	
	/**
	 * Initializes requested module bootstrap
	 * @see library/Zend/Controller/Plugin/Zend_Controller_Plugin_Abstract::dispatchLoopStartup()
	 * @param Zend_Controller_Request_Abstract | $request
	 * 
	 * NOTE: changed from dispatchLoopStartup to routeShutdown because need to checksystem availabilty 
	 * before the initializaion module controllers
	 */
	public function routeShutdown(Zend_Controller_Request_Abstract $request)
	{
		$module = $request->getModuleName();

		if (!isset($this->_modules[$module])) {
			throw new Exception("Module does not exist!");
		}
		
	    // checks if module is not blocked by security system   			
    	$isBlocked = Core_Modules_Blocker::isBlocked($module);
    	if ($isBlocked)
    	{
    		$params = array ('0' =>  $module);
    		throw new Exception(Core_Model_Errors::getError(97, $params));
    	}
    	
		
		$bootstrapPath = $this->_modules[$module];
		
		$bootstrapFile = dirname($bootstrapPath) . '/Bootstrap.php';
        $class         = ucfirst($module) . '_Bootstrap';
        $application   = new Zend_Application(
        	APPLICATION_ENV,
    		APPLICATION_PATH . '/modules/' . $module . '/configs/module.ini'
		);  
		
        if (Zend_Loader::loadFile('Bootstrap.php', dirname($bootstrapPath)) 
        	&& class_exists($class)) {
            $bootstrap = new $class($application);
            $bootstrap->bootstrap();
        }
	}
}