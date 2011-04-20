<?php
/**
 * Default base class for bootstraping requested modules
 *
 * @author     Fedor Petryk
 * @uses       Zend_Application_Bootstrap_Bootstrap
 * @copyright  Copyright (c) 2006-2010 S2B (http://www.s2b.com.ua)
 */
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	/**
	 * 
	 * Initialization of main configuration
	 * See configs/application.ini
	 */
	protected function _initConfiguration()
    {
    	$app = $this->getApplication();
    	$config = $app->getOptions();
    	Zend_Registry::set ( 'app_config', $config );
    	
    	date_default_timezone_set($config['timezone']);
		setlocale(LC_CTYPE, $config['locale']);
		
    	if (APPLICATION_ENV == 'development') {
	    	//error_reporting(E_ALL & E_STRICT);
	    	if (isset($config['phpsettings'])) {
		    	foreach ($config['phpsettings'] as $setting => $value) {
		    		ini_set($setting, $value);
		    	}  
	    	}
    	}
   	}
    
   	/**
   	 * 
   	 * Enabling autoloading
   	 */
	protected function _initAutoload()
    {
		$autoloader = Zend_Loader_Autoloader::getInstance();
		$autoloader->setFallbackAutoloader(true);
		
		return $autoloader;
    }

	protected function  _initView()
	{
		$view = new Zend_View();
		$view->setEncoding('UTF-8');
		$view->doctype('XHTML1_STRICT');
		$view->headMeta()->appendHttpEquiv('Content-Type', 'text/html;charset=utf-8');
		$view->addHelperPath ( 'Core/View/Helper/', 'Core_View_Helper' );
		
 		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper(
 			'ViewRenderer'
 		);
 		$viewRenderer->setView($view);
 		return $view;
	}



    /**
     * 
     * Initializing view caching
     * See cache/Cache.php
     */
	protected function  _initViewCaching() 
    {
		$classFileIncCache = APPLICATION_PATH . '/cache/Cache.php';
		if (file_exists($classFileIncCache)){
			include_once $classFileIncCache;
		}
		Zend_Loader_PluginLoader::setIncludeFileCache($classFileIncCache);
	}
    
	/**
	 * 
	 * Initializing Db
	 */
	protected function _initDatabase()
	{
		$options = Zend_Registry::get('app_config');
        $db = Zend_Db::factory($options['database']['adapter'], $options['database']['params']);
        $db->query("SET NAMES 'utf8'");
        Zend_Db_Table_Abstract::setDefaultAdapter($db);
        Zend_Registry::set('DB', $db);
        
        return $db;
	}


    /**
     *
     * Initializing front controller
     *
     */
    protected function _initController()
    {
    	$this->bootstrap('FrontController');
    	$controller = $this->getResource('FrontController');
		$modules = $controller->getControllerDirectory();
		$controller->setParam('prefixDefaultModule', true);

    	$controller->registerPlugin(
    		new Core_Modules_Loader($modules)
    	);

    	return $controller;
    }

    /**
     *
     * Initializing request
     */
    protected function _initRequest()
    {
        $this->bootstrap('FrontController');
        $front = $this->getResource('FrontController');
        $request = $front->getRequest();
    	if (null === $front->getRequest()) {
            $request = new Zend_Controller_Request_Http();
            $front->setRequest($request);
        }

    	return $request;
    }
	
	/**
	 * 
	 * Building acl
	 * Checking user identity permissions
	 * @throws Exception
	 */
	protected function _initAcl()
    {
    	$aclBuilder = new Core_Acl_AclBuilder();
    	$aclBuilder->init();
    	$aclBuilder->buildAcl();
		$rolesAcl = $aclBuilder->getRoleAcl(); 
        $auth = Zend_Auth::getInstance(); 
        
		if (!($auth->hasIdentity()))
        {
            $role = 'guest'; 
        }  
        else if ($auth->hasIdentity())
        {
        	$user = Core_Model_User::getInstance(); 
        	$user_data = $auth->getIdentity();
			        	
        	if (!is_array($user_data))
        	{
        		throw new Exception(Core_Model_Errors::getError(98));	
        	}
        	
        	$user->populate($auth->getIdentity());
        	$role = $user->role;
        	
	        if (!array_key_exists($role, $rolesAcl)) {
	        	$user->clear();
	        	$auth->clearIdentity(); 	
	        }
        }
        	
        $front = Zend_Controller_Front::getInstance();
        $front->registerPlugin(new Core_Controller_Plugin_Acl($aclBuilder,  $role));

       
        if (array_key_exists($role, $rolesAcl)) {
			Zend_Registry::set('acl', $rolesAcl[$role]);		
        	Zend_Registry::set('fullAcl', $rolesAcl);
			Zend_Registry::set('aclObject', $aclBuilder);
        } 
        
    } 

	protected function _initTranslation()
    {
		$user = Core_Model_User::getInstance();
		if (empty($user->language)){
			$options = Zend_Registry::get('app_config');
        	$defaultLang = $options['lang'];
		} else {
			$defaultLang = $user->language;
		}

		Zend_Registry::set('language', $defaultLang);

        $translation = new Core_Translation();
        $translation->init();
        $ui_translation = $translation->getTranslation($defaultLang);
        if($ui_translation){
            Zend_Registry::set('translation',$translation);
        }else{
            throw new Exception(Core_Model_Errors::getError(89));
        }
    }

    protected function _initSmpt()
    {
    	$app = $this->getApplication();
    	$config = $app->getOptions();
	   	$tr = new Zend_Mail_Transport_Smtp($config['mail']['server'],
    		$config['mail'] );
    	Zend_Mail::setDefaultTransport($tr);
    }

	public function _initAddtitionalConfigs()
	{
		$defaultLang = Zend_Registry::get('language');

    	$conf = new Zend_Config_Ini(APPLICATION_PATH
				. '/configs/statuses_' . $defaultLang . '.ini');
    	Zend_Registry::set ( 'statuses', $conf->toArray() );
	}

    protected function _initModules()
    {
		// Call to prevent ZF from loading all modules
    }
}