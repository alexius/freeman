<?php
/** Zend_Acl */
require_once 'Zend/Acl.php';

/** Zend_Controller_Plugin_Abstract */
require_once 'Zend/Controller/Plugin/Abstract.php';

/**
 * Front Controller Plugin
 *
 * @uses       Zend_Controller_Plugin_Abstract
 * @category   Zion
 * @package    Zion_Controller
 * @subpackage Plugins
 */
class Core_Controller_Plugin_Acl extends Zend_Controller_Plugin_Abstract
{
    /**
     * @var Zend_Acl
     **/
    protected $_acl;

    /**
     * @var string
     **/
    protected $_roleName;

    /**
     * @var array
     **/
    protected $_errorPage;

    /**
     * Constructor
     *
     * @param mixed $aclData
     * @param $roleName
     * @return void
     **/
    public function __construct(Core_Acl_AclBuilder $aclData, $roleName = 'defaultRole')
    {
        $this->_errorPage = array('module' => 'default', 
                                  'controller' => 'error', 
                                  'action' => 'denied');

        $this->_roleName = $roleName;

        if (null !== $aclData) {
            $this->setAcl($aclData);
        }
    }

    /**
     * Sets the ACL object
     *
     * @param mixed $aclData
     * @return void
     **/
    public function setAcl(Core_Acl_AclBuilder $aclData)
    {
        $this->_acl = $aclData;
    }

    /**
     * Returns the ACL object
     *
     * @return Zend_Acl
     **/
    public function getAcl()
    {
        return $this->_acl;
    }

    /**
     * Sets the ACL role to use
     *
     * @param string $roleName
     * @return void
     **/
    public function setRoleName($roleName)
    {
        $this->_roleName = $roleName;
    }

    /**
     * Returns the ACL role used
     *
     * @return string
     * @author 
     **/
    public function getRoleName()
    {
        return $this->_roleName;
    }

    /**
     * Sets the error page
     *
     * @param string $action
     * @param string $controller
     * @param string $module
     * @return void
     **/
    public function setErrorPage($action, $controller = 'error', $module = 'default')
    {
        $this->_errorPage = array('module' => $module, 
                                  'controller' => $controller,
                                  'action' => $action);
    }

    /**
     * Returns the error page
     *
     * @return array
     **/
    public function getErrorPage()
    {
        return $this->_errorPage;
    }

    /**
     * Predispatch
     * Checks if the current user identified by roleName has rights to the requested url (module/controller/action)
     * If not, it will call denyAccess to be redirected to errorPage
     *
     * @return void
     **/
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $resourceName = '';
        $resourceName .= $request->getModuleName() . ':';
        $resourceName .= $request->getControllerName();

		if ($request->isPost()) {
			$type = 2;	
		}
		else {
			$type = 0;
		}
		
    	if (!$this->getAcl()->has($resourceName))
		{
			Core_Log_Logger::logAccessEvent($request, 4);
			$this->denyAccess();
			return false;
		}

        /** Check if the controller/action can be accessed by the current user */
        if (!$this->getAcl()->isAllowed($this->_roleName, $resourceName, $request->getActionName())) 
        {
        	Core_Log_Logger::logAccessEvent($request, 1);
        	
            /** Redirect to access denied page */
            $this->denyAccess();
        }
        else
        {
        	Core_Log_Logger::logAccessEvent($request, $type);
        }
    }

    /**
     * Deny Access Function
     * Redirects to errorPage, this can be called from an action using the action helper
     *
     * @return void
     **/
    public function denyAccess()
    {
        $this->_request->setModuleName($this->_errorPage['module']);
        $this->_request->setControllerName($this->_errorPage['controller']);
        $this->_request->setActionName($this->_errorPage['action']);
    }

	/**
	 * Send request to the login page
	 * @return void 
	 **/

	public function loginPage()
	{
		$this->_request->setModuleName('default');
        $this->_request->setControllerName('index');
        $this->_request->setActionName('index');
	}

}