<?php 

/**
 * Default ErrorController
 * 
 * @author      Fedor Petryk
 * @copyright  Copyright (c) 2006-2010 S2B (http://www.s2b.com.ua)
 */ 
class Core_Controller_ErrorController extends Core_Controller_Start
{ 
    /**
     * errorAction() is the action that will be called by the "ErrorHandler" 
     * plugin.  When an error/exception has been encountered
     * in a ZF MVC application (assuming the ErrorHandler has not been disabled
     * in your bootstrap) - the Errorhandler will set the next dispatchable 
     * action to come here.  This is the "default" module, "error" controller, 
     * specifically, the "error" action.  These options are configurable, see 
     * {@link http://framework.zend.com/manual/en/zend.controller.plugins.html#zend.controller.plugins.standard.errorhandler
	 * the docs on the ErrorHandler Plugin}
     *
     * @return void
     */

	public function postDispatch ()
	{
    	if ($this->getRequest()->isXmlHttpRequest())
        {
            $this->_helper->viewRenderer->setNoRender();
            $this->_helper->layout->disableLayout();
			$errors = $this->_getParam('error_handler');

			if ($this->_request->getActionName() != 'denied')
			{
				$this->ajaxResponse(
					$errors->exception->getMessage(),
					'application/json'
				);
			}
			else
			{
				$this->ajaxResponse(
					Zend_Json::encode(array(
						'error_message' => Core_Model_Errors::getError('no_rights'),
						'error' => 'true'
					)),
					'application/json'
				);
			}
        }
	}
	
	public function init()
	{
		//$this->_helper->layout->setLayout('clear');
	}
	
    public function errorAction() 
    { 

        // Ensure the default view suffix is used so we always return good 
        // content
        $this->_helper->viewRenderer->setViewSuffix('phtml');

        // Grab the error object from the request
        $errors = $this->_getParam('error_handler'); 

        // $errors will be an object set as a parameter of the request object, 
        // type is a property
       
        switch ($errors->type) { 
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER: 
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION: 

                // 404 error -- controller or action not found 
             
                $this->getResponse()->setHttpResponseCode(404); 
                $this->view->title = Zend_Registry::get('translation')->get('404'); 
                $this->view->message = Core_Model_Errors::getServerError(404);
                Core_Log_Logger::logErrorEvent($errors);
                
                break; 
            default: 
                // application error 
                $this->getResponse()->setHttpResponseCode(500); 
                $this->view->title = Zend_Registry::get('translation')->get('500'); 
                $this->view->message = Core_Model_Errors::getServerError(500);
				Core_Log_Logger::logErrorEvent($errors);
				
                break; 
        } 
        
        $this->view->main_title = Zend_Registry::get('translation')->get('error');
        $this->view->main_keywords = Zend_Registry::get('translation')->get('error');
        $this->view->main_description = Zend_Registry::get('translation')->get('error'); 

        // pass the environment to the view script so we can conditionally 
        // display more/less information
        $this->view->env       = $this->getInvokeArg('env'); 
        
        // pass the actual exception object to the view
        $this->view->exception = $errors->exception; 
        
        // pass the request to the view
        $this->view->request   = $errors->request; 
        
        $conf = Zend_Registry::get('app_config');
        $this->view->showErrors = $conf['showViewError'];
    } 
    
    public function indexAction()
    {
          $this->getResponse()->setHttpResponseCode(404); 
          $this->view->title = '404 Страница не найдена'; 
          $this->view->message = Errors::getServerError(404);       
    }

    
    public function deniedAction()
    {
      
    }

	public function notallowedAction()
    {

    }
}
