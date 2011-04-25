<?php
/**
 * Default base class for bootstraping requested module
 *
 * @author     Fedor Petryk
 * @uses       Zend_Application_Bootstrap_Bootstrap
 * @copyright  Copyright (c) 2006-2010 S2B (http://www.s2b.com.ua)
 */
class Profile_Bootstrap extends Zend_Application_Module_Bootstrap 
{
	protected $_moduleName = 'profile';
	
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

		defined('APPLICATION_PUB')
    		|| define('APPLICATION_PUB', BASE_PATH . '/../../application/backend' . '/modules/' . $this->_moduleName);
    }
    
	/**
     *
     * Inits view
     */
	protected function _initView()
	{
		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper(
 			'ViewRenderer'
 		);
		$view = $viewRenderer->view;
		$view->addHelperPath('views/helpers/', 'Profile_Helper');
 		$viewRenderer->setView($view);
 		return $view;
	}
}